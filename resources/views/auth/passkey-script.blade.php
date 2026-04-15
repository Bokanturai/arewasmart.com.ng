<script>
    /**
     * WebAuthn / Passkey Registration Logic
     */
    document.addEventListener('DOMContentLoaded', function() {
        const enableBtn = document.getElementById('enable-biometrics');
        
        if (enableBtn) {
            enableBtn.addEventListener('click', async function() {
                // 1. Disable button and show loading state
                const originalContent = enableBtn.innerHTML;
                enableBtn.disabled = true;
                enableBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';

                try {
                    // 2. Fetch Registration Options (Challenge)
                    const optionsResponse = await fetch('{{ route("webauthn.register.options") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });

                    if (!optionsResponse.ok) {
                        throw new Error('Failed to fetch biometric options.');
                    }

                    const options = await optionsResponse.json();

                    // 3. Helper to convert Base64URL to ArrayBuffer (required by WebAuthn API)
                    const recursiveBase64ToUint8Array = (obj) => {
                        for (let key in obj) {
                            if (typeof obj[key] === 'string' && /^[0-9a-zA-Z-_]+$/.test(obj[key]) && (key === 'challenge' || key === 'id' || key === 'userHandle')) {
                                obj[key] = Uint8Array.from(atob(obj[key].replace(/-/g, '+').replace(/_/g, '/')), c => c.charCodeAt(0));
                            } else if (typeof obj[key] === 'object') {
                                recursiveBase64ToUint8Array(obj[key]);
                            }
                        }
                    };

                    // Laragear usually handles encoding, but we ensure Uint8Array for the browser
                    const decodeBase64 = (s) => Uint8Array.from(atob(s.replace(/-/g, '+').replace(/_/g, '/')), c => c.charCodeAt(0));
                    
                    const publicKeyOptions = options.publicKey || options;
                    
                    publicKeyOptions.challenge = decodeBase64(publicKeyOptions.challenge);
                    publicKeyOptions.user.id = decodeBase64(publicKeyOptions.user.id);
                    if (publicKeyOptions.excludeCredentials) {
                        publicKeyOptions.excludeCredentials.forEach(cred => {
                            cred.id = decodeBase64(cred.id);
                        });
                    }

                    // 4. Create Credential via WebAuthn API
                    const credential = await navigator.credentials.create({
                        publicKey: publicKeyOptions
                    });

                    // 5. Helper to convert ArrayBuffer to Base64URL for transport
                    const bufferToBase64url = (buffer) => {
                        return btoa(String.fromCharCode(...new Uint8Array(buffer)))
                            .replace(/\+/g, '-')
                            .replace(/\//g, '_')
                            .replace(/=/g, '');
                    };

                    // 6. Prepare Attestation Response
                    const attestationResponse = {
                        id: credential.id,
                        rawId: bufferToBase64url(credential.rawId),
                        type: credential.type,
                        response: {
                            attestationObject: bufferToBase64url(credential.response.attestationObject),
                            clientDataJSON: bufferToBase64url(credential.response.clientDataJSON),
                        },
                    };

                    // 7. Send Attestation to Server
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
                        // Success! - Save that biometrics are enabled for this browser
                        localStorage.setItem('arewa_smart_biometrics_enabled', 'true');

                        Swal.fire({
                            icon: 'success',
                            title: 'Biometrics Enabled!',
                            text: 'You can now use your biometrics to sign in faster.',
                            confirmButtonColor: '#F26522'
                        }).then(() => {
                            // Close modal if using bootstrap
                            const modalEl = document.getElementById('biometricModal');
                            if (modalEl) {
                                const modal = bootstrap.Modal.getInstance(modalEl);
                                if (modal) modal.hide();
                            }
                            location.reload(); // Refresh to update UI state
                        });
                    } else {
                        const errorData = await registerResponse.json();
                        throw new Error(errorData.message || 'Verification failed.');
                    }

                } catch (error) {
                    console.error('WebAuthn Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: error.message || 'Your device might not support biometrics or the session timed out.',
                        confirmButtonColor: '#d33'
                    });
                } finally {
                    enableBtn.disabled = false;
                    enableBtn.innerHTML = originalContent;
                }
            });
        }
    });

    /**
     * WebAuthn Login Logic (Optional helper)
     */
    let webAuthnAbortController = null;

    async function webAuthnLogin(email = null, mediation = 'optional') {
        // Abort any existing pending WebAuthn request to prevent conflicts
        if (webAuthnAbortController) {
            webAuthnAbortController.abort('New request started');
        }
        webAuthnAbortController = new AbortController();

        try {
            // Ensure empty email strings are treated as null for the server
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
            const decodeBase64 = (s) => Uint8Array.from(atob(s.replace(/-/g, '+').replace(/_/g, '/')), c => c.charCodeAt(0));
            
            const publicKeyOptions = options.publicKey || options;
            
            publicKeyOptions.challenge = decodeBase64(publicKeyOptions.challenge);
            if (publicKeyOptions.allowCredentials) {
                publicKeyOptions.allowCredentials.forEach(cred => {
                    cred.id = decodeBase64(cred.id);
                });
            }

            const assertion = await navigator.credentials.get({
                publicKey: publicKeyOptions,
                mediation: mediation,
                signal: webAuthnAbortController.signal
            });

            // If assertion is null (e.g. user cancelled), just return
            if (!assertion) return;

            const bufferToBase64url = (buffer) => {
                return btoa(String.fromCharCode(...new Uint8Array(buffer)))
                    .replace(/\+/g, '-')
                    .replace(/\//g, '_')
                    .replace(/=/g, '');
            };

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
                // Save that biometrics are enabled for this browser
                localStorage.setItem('arewa_smart_biometrics_enabled', 'true');
                window.location.href = '{{ route("dashboard") }}';
            } else {
                throw new Error('Biometric verification failed');
            }
        } catch (error) {
            // Don't alert if it's a silent conditional check, an abort, or user cancellation
            if (mediation === 'conditional' || 
                error.name === 'NotAllowedError' || 
                error.name === 'AbortError' || 
                error === 'New request started') {
                console.log('Biometric login cancelled, aborted, or background check complete.');
                return;
            }

            console.error('WebAuthn Login Error:', error);
            
            // Show user feedback on login failure
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: error.message || 'Could not verify your biometrics. Please try again or use your password.',
                confirmButtonColor: '#d33'
            });

            throw error;
        } finally {
            // We only clear the controller if it's the one we created
            // if (webAuthnAbortController?.signal === signal) webAuthnAbortController = null;
        }
    }

    /**
     * Initialize Passkey Conditional UI (Autofill)
     */
    document.addEventListener('DOMContentLoaded', async () => {
        if (window.PublicKeyCredential && 
            PublicKeyCredential.isConditionalMediationAvailable && 
            await PublicKeyCredential.isConditionalMediationAvailable()) {
            
            // Trigger conditional login in the background.
            // This will show the biometric prompt when the user clicks the email field.
            webAuthnLogin(null, 'conditional');
        }
    });
</script>
