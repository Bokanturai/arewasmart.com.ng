<script>
    /**
     * WebAuthn / Passkey Helpers
     */
    const bufferToBase64url = (buffer) => {
        return btoa(String.fromCharCode(...new Uint8Array(buffer)))
            .replace(/\+/g, '-')
            .replace(/\//g, '_')
            .replace(/=/g, '');
    };

    const decodeBase64url = (s) => {
        // Add padding if missing (required by atob)
        const padded = s.padEnd(s.length + (4 - s.length % 4) % 4, '=');
        return Uint8Array.from(atob(padded.replace(/-/g, '+').replace(/_/g, '/')), c => c.charCodeAt(0));
    };

    /**
     * WebAuthn / Passkey Registration Logic
     */
    document.addEventListener('DOMContentLoaded', function() {
        const enableBtn = document.getElementById('enable-biometrics');
        
        if (enableBtn) {
            enableBtn.addEventListener('click', async function() {
                const originalContent = enableBtn.innerHTML;
                enableBtn.disabled = true;
                enableBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

                try {
                    const optionsResponse = await fetch('{{ route("webauthn.register.options") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    if (!optionsResponse.ok) throw new Error('Failed to fetch biometric options.');

                    const options = await optionsResponse.json();
                    const publicKeyOptions = options.publicKey || options;
                    
                    // Decode necessary Base64URL strings to Uint8Array
                    publicKeyOptions.challenge = decodeBase64url(publicKeyOptions.challenge);
                    publicKeyOptions.user.id = decodeBase64url(publicKeyOptions.user.id);
                    if (publicKeyOptions.excludeCredentials) {
                        publicKeyOptions.excludeCredentials.forEach(cred => {
                            cred.id = decodeBase64url(cred.id);
                        });
                    }

                    const credential = await navigator.credentials.create({
                        publicKey: publicKeyOptions
                    });

                    if (!credential) throw new Error('Credential creation failed.');

                    const attestationResponse = {
                        id: credential.id,
                        rawId: bufferToBase64url(credential.rawId),
                        type: credential.type,
                        response: {
                            attestationObject: bufferToBase64url(credential.response.attestationObject),
                            clientDataJSON: bufferToBase64url(credential.response.clientDataJSON),
                        },
                    };

                    const registerResponse = await fetch('{{ route("webauthn.register") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(attestationResponse)
                    });

                    if (registerResponse.ok) {
                        localStorage.setItem('arewa_smart_biometrics_enabled', 'true');
                        Swal.fire({
                            icon: 'success',
                            title: 'Biometrics Enabled!',
                            text: 'You can now use your biometrics to sign in faster.',
                            confirmButtonColor: '#F26522'
                        }).then(() => location.reload());
                    } else {
                        const errorData = await registerResponse.json();
                        throw new Error(errorData.message || 'Verification failed.');
                    }

                } catch (error) {
                    console.error('WebAuthn Registration Error:', error);
                    if (error.name !== 'NotAllowedError' && error.name !== 'AbortError') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed',
                            text: error.message || 'Your device might not support biometrics or the session timed out.',
                            confirmButtonColor: '#d33'
                        });
                    }
                } finally {
                    enableBtn.disabled = false;
                    enableBtn.innerHTML = originalContent;
                }
            });
        }
    });

    /**
     * WebAuthn Login Logic
     */
    let webAuthnAbortController = null;

    async function webAuthnLogin(email = null, mediation = 'optional') {
        if (webAuthnAbortController) {
            webAuthnAbortController.abort('New request started');
        }
        webAuthnAbortController = new AbortController();

        try {
            const payload = { 
                email: (email && email.trim() !== '') ? email : null 
            };

            const optionsResponse = await fetch('{{ route("webauthn.login.options") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload),
                signal: webAuthnAbortController.signal
            });

            if (!optionsResponse.ok) throw new Error('Failed to initialize biometric login');

            const options = await optionsResponse.json();
            const publicKeyOptions = options.publicKey || options;
            
            publicKeyOptions.challenge = decodeBase64url(publicKeyOptions.challenge);
            if (publicKeyOptions.allowCredentials) {
                publicKeyOptions.allowCredentials.forEach(cred => {
                    cred.id = decodeBase64url(cred.id);
                });
            }

            const assertion = await navigator.credentials.get({
                publicKey: publicKeyOptions,
                mediation: mediation,
                signal: webAuthnAbortController.signal
            });

            if (!assertion) return;

            const assertionResponse = {
                id: assertion.id,
                rawId: bufferToBase64url(assertion.rawId),
                type: assertion.type,
                response: {
                    authenticatorData: bufferToBase64url(assertion.response.authenticatorData),
                    clientDataJSON: bufferToBase64url(assertion.response.clientDataJSON),
                    signature: bufferToBase64url(assertion.response.signature),
                    userHandle: assertion.response.userHandle ? bufferToBase64url(assertion.response.userHandle) : null,
                },
            };

            const loginResponse = await fetch('{{ route("webauthn.login") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(assertionResponse),
                signal: webAuthnAbortController.signal
            });

            if (loginResponse.ok) {
                localStorage.setItem('arewa_smart_biometrics_enabled', 'true');
                window.location.href = '{{ route("dashboard") }}';
            } else {
                const errorData = await loginResponse.json();
                throw new Error(errorData.message || 'Biometric verification failed');
            }
        } catch (error) {
            if (mediation === 'conditional' || 
                error.name === 'NotAllowedError' || 
                error.name === 'AbortError' || 
                error === 'New request started') {
                console.log('Biometric signal handled:', error.name || error);
                return;
            }

            console.error('WebAuthn Login Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: error.message || 'Could not verify your biometrics. Please use your password.',
                confirmButtonColor: '#d33'
            });
        }
    }

    /**
     * Initialize Passkey Conditional UI (Autofill)
     */
    document.addEventListener('DOMContentLoaded', async () => {
        if (window.PublicKeyCredential && 
            PublicKeyCredential.isConditionalMediationAvailable && 
            await PublicKeyCredential.isConditionalMediationAvailable()) {
            webAuthnLogin(null, 'conditional');
        }
    });
</script>
