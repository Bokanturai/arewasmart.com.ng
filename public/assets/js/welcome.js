document.addEventListener('DOMContentLoaded', () => {
    // --- Mobile Menu Logic ---
    const mobileMenu = document.getElementById('mobile-menu');
    const navLinks = document.getElementById('nav-links');
    const overlay = document.getElementById('mobile-overlay');

    const toggleMenu = () => {
        const isActive = navLinks.classList.contains('active');

        if (isActive) {
            navLinks.classList.remove('active');
            overlay.classList.remove('active');
            mobileMenu.innerHTML = '<i class="fa-solid fa-bars"></i>';
            document.body.style.overflow = '';
        } else {
            navLinks.classList.add('active');
            overlay.classList.add('active');
            mobileMenu.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            document.body.style.overflow = 'hidden';
        }
    };

    if (mobileMenu && overlay) {
        mobileMenu.addEventListener('click', toggleMenu);
        overlay.addEventListener('click', toggleMenu);
    }

    // Close menu when clicking a navigation link
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            if (navLinks.classList.contains('active')) {
                toggleMenu();
            }
        });
    });


    // --- AI Chatbot Logic ---
    const chatBody = document.getElementById('chatBody');
    const chatInput = document.getElementById('chatInput');
    const sendBtn = document.getElementById('sendBtn');
    const servicePills = document.querySelectorAll('.service-pill');

    const addMessage = (text, sender) => {
        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-message ${sender}-message`;
        msgDiv.innerHTML = text;
        chatBody.appendChild(msgDiv);
        scrollToBottom();
    };

    const scrollToBottom = () => {
        chatBody.scrollTo({
            top: chatBody.scrollHeight,
            behavior: 'smooth'
        });
    };

    const showTyping = () => {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-message bot-message typing';
        typingDiv.id = 'typingIndicator';
        typingDiv.innerHTML = `
            <div class="typing-indicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        `;
        chatBody.appendChild(typingDiv);
        scrollToBottom();
    };

    const removeTyping = () => {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    };

    const botResponses = {
        'bvn': 'We offer comprehensive BVN services. You can validate a BVN, perform full BVN searches, and verify details instantly. Would you like to go to the BVN dashboard?',
        'nin': 'Our NIN services cover modifications (like DOB, Name change), validation, and standard verification. Everything is fully automated. Need help starting a NIN modification?',
        'utility': 'You can safely and securely pay for electricity (IKEDC, EKEDC, KEDCO, etc) and cable subscriptions like DSTV and GOTV. Are you paying a bill today?',
        'data': 'We provide cheap data top-ups for MTN, Airtel, Glo, and 9mobile! Transactions are processed in less than 5 seconds.',
        'electricity': 'Electricity bill payment is easy! We support all major distribution companies. You just need your meter number.',
        'spry': 'Bring the ultimate vibe to your African weddings and occasions! 🎉 Our Spry Gift Card lets you spray money effortlessly and with unmatched class. Want to get a Spry Gift Card for your next big event?',
        'bonus': 'We love rewarding our family! 🎁 As a new user, you get a special Welcome Bonus when you sign up. Plus, you can earn even more cash with our Referral Code system. Ready to claim your bonus?',
        'default': 'Thank you for reaching out! Please register or log in to get started. You can also ask me specific questions about our BVN, NIN, Data, or Utility services.'
    };

    const handleUserMessage = async (text) => {
        if (!text.trim()) return;

        addMessage(text, 'user');
        chatInput.value = '';

        showTyping();

        try {
            const response = await fetch('/chatbot/message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: text })
            });

            const data = await response.json();
            removeTyping();

            if (response.ok) {
                addMessage(data.response, 'bot');
            } else {
                // Fallback to local logic if API is not configured or fails
                console.warn('Chatbot API failed, using fallback logic.');
                handleFallbackResponse(text);
            }
        } catch (error) {
            console.error('Chatbot error:', error);
            removeTyping();
            handleFallbackResponse(text);
        }
    };

    const handleFallbackResponse = (text) => {
        let response = botResponses['default'];
        const lowerText = text.toLowerCase();

        if (lowerText.includes('bvn')) response = botResponses['bvn'];
        else if (lowerText.includes('nin')) response = botResponses['nin'];
        else if (lowerText.includes('bill') || lowerText.includes('utility')) response = botResponses['utility'];
        else if (lowerText.includes('electricity')) response = botResponses['electricity'];
        else if (lowerText.includes('data') || lowerText.includes('airtime')) response = botResponses['data'];
        else if (lowerText.includes('spry') || lowerText.includes('gift')) response = botResponses['spry'];
        else if (lowerText.includes('bonus') || lowerText.includes('referral')) response = botResponses['bonus'];

        addMessage(response, 'bot');
    };

    if (sendBtn && chatInput) {
        sendBtn.addEventListener('click', () => {
            handleUserMessage(chatInput.value);
        });

        chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                handleUserMessage(chatInput.value);
            }
        });
    }

    servicePills.forEach(pill => {
        pill.addEventListener('click', () => {
            const text = pill.innerText;
            handleUserMessage(`Tell me more about ${text}`);
        });
    });

    // Initial bot start animation for existing messages
    const existingMessages = document.querySelectorAll('.chat-message');
    existingMessages.forEach((el, index) => {
        el.style.animationDelay = `${index * 0.4}s`;
    });

    // --- Services Slider Logic ---
    const servicesTrack = document.getElementById('servicesTrack');
    const slides = document.querySelectorAll('.service-slide');
    const prevBtn = document.getElementById('prevService');
    const nextBtn = document.getElementById('nextService');
    const dotsContainer = document.getElementById('serviceDots');
    
    if (servicesTrack && slides.length > 0) {
        let currentSlide = 0;
        const totalSlides = slides.length;
        let slideInterval;

        // Create dots
        slides.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => goToSlide(index));
            dotsContainer.appendChild(dot);
        });

        const dots = document.querySelectorAll('.dot');

        const updateSlider = () => {
            servicesTrack.style.transform = `translateX(-${currentSlide * 100}%)`;
            
            slides.forEach(slide => slide.classList.remove('active'));
            slides[currentSlide].classList.add('active');
            
            dots.forEach(dot => dot.classList.remove('active'));
            dots[currentSlide].classList.add('active');
        };

        const nextSlide = () => {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateSlider();
            resetInterval();
        };

        const prevSlide = () => {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateSlider();
            resetInterval();
        };

        const goToSlide = (index) => {
            currentSlide = index;
            updateSlider();
            resetInterval();
        };

        if (nextBtn) nextBtn.addEventListener('click', nextSlide);
        if (prevBtn) prevBtn.addEventListener('click', prevSlide);

        // Auto slide every 30 seconds
        const startInterval = () => {
            slideInterval = setInterval(nextSlide, 30000);
        };

        const resetInterval = () => {
            clearInterval(slideInterval);
            startInterval();
        };

        startInterval();
        
        // Touch events for mobile swipe
        let touchStartX = 0;
        let touchEndX = 0;
        
        servicesTrack.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        }, {passive: true});
        
        servicesTrack.addEventListener('touchend', e => {
            touchEndX = e.changedTouches[0].screenX;
            handleSwipe();
        }, {passive: true});
        
        const handleSwipe = () => {
            if (touchEndX < touchStartX - 50) nextSlide();
            if (touchEndX > touchStartX + 50) prevSlide();
        }
    }

    // --- Testimonials Slider Logic ---
    const testiTrack = document.getElementById('testi-track');
    const testiContainer = document.getElementById('testi-track-container');
    const testiPrevBtn = document.getElementById('testi-prev');
    const testiNextBtn = document.getElementById('testi-next');
    const testiDotsContainer = document.getElementById('testi-dots');

    if (testiTrack && testiContainer) {
        let testiCurrentIndex = 0;
        let testiCards = document.querySelectorAll('.testimonial-card');
        let testiTotalCards = testiCards.length;
        let testiCardsPerView = getTestiCardsPerView();
        let testiMaxIndex = Math.max(0, testiTotalCards - testiCardsPerView);
        let testiAutoPlayInterval;

        function getTestiCardsPerView() {
            if (window.innerWidth <= 768) return 1;
            if (window.innerWidth <= 992) return 2;
            return 3;
        }

        function createTestiDots() {
            if(!testiDotsContainer) return;
            testiDotsContainer.innerHTML = '';
            for (let i = 0; i <= testiMaxIndex; i++) {
                const dot = document.createElement('div');
                dot.classList.add('dot');
                if (i === testiCurrentIndex) dot.classList.add('active');
                dot.addEventListener('click', () => goTestiToSlide(i));
                testiDotsContainer.appendChild(dot);
            }
        }

        function updateTestiDots() {
            if(!testiDotsContainer) return;
            const dots = testiDotsContainer.querySelectorAll('.dot');
            dots.forEach((dot, index) => {
                if (index === testiCurrentIndex) dot.classList.add('active');
                else dot.classList.remove('active');
            });
        }

        function updateTestiSliderPosition() {
            // Gap is 2rem = 32px based on CSS
            const gapAndCard = testiCards[0].offsetWidth + 32; 
            const moveX = gapAndCard * testiCurrentIndex;
            testiTrack.style.transform = `translateX(-${moveX}px)`;
            updateTestiDots();
        }

        function goTestiToSlide(index) {
            testiCurrentIndex = index;
            if (testiCurrentIndex < 0) testiCurrentIndex = testiMaxIndex;
            if (testiCurrentIndex > testiMaxIndex) testiCurrentIndex = 0;
            updateTestiSliderPosition();
            resetTestiAutoPlay();
        }

        if (testiPrevBtn) {
            testiPrevBtn.addEventListener('click', () => goTestiToSlide(testiCurrentIndex - 1));
        }

        if (testiNextBtn) {
            testiNextBtn.addEventListener('click', () => goTestiToSlide(testiCurrentIndex + 1));
        }

        function startTestiAutoPlay() {
            testiAutoPlayInterval = setInterval(() => {
                goTestiToSlide(testiCurrentIndex + 1);
            }, 6000); 
        }

        function resetTestiAutoPlay() {
            clearInterval(testiAutoPlayInterval);
            startTestiAutoPlay();
        }

        // Touch support
        let testiTouchStartX = 0;
        let testiTouchEndX = 0;

        testiContainer.addEventListener('touchstart', e => {
            testiTouchStartX = e.changedTouches[0].screenX;
            clearInterval(testiAutoPlayInterval);
        }, {passive: true});

        testiContainer.addEventListener('touchend', e => {
            testiTouchEndX = e.changedTouches[0].screenX;
            if (testiTouchEndX < testiTouchStartX - 50) goTestiToSlide(testiCurrentIndex + 1); 
            if (testiTouchEndX > testiTouchStartX + 50) goTestiToSlide(testiCurrentIndex - 1); 
            startTestiAutoPlay();
        }, {passive: true});

        window.addEventListener('resize', () => {
            let newPerView = getTestiCardsPerView();
            if(newPerView !== testiCardsPerView) {
                testiCardsPerView = newPerView;
                testiMaxIndex = Math.max(0, testiTotalCards - testiCardsPerView);
                if(testiCurrentIndex > testiMaxIndex) testiCurrentIndex = testiMaxIndex;
                createTestiDots();
                updateTestiSliderPosition();
            }
        });

        // Init
        createTestiDots();
        startTestiAutoPlay();
    }

    // --- PWA Service Worker Registration ---
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => {
                    console.log('Service Worker registered', reg);
                    reg.addEventListener('updatefound', () => {
                        const newWorker = reg.installing;
                        if (newWorker) {
                            newWorker.addEventListener('statechange', () => {
                                if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                    console.log('New service worker installed. Reloading...');
                                    window.location.reload();
                                }
                            });
                        }
                    });
                })
                .catch(err => console.log('Service Worker registration failed', err));
        });

        let refreshing = false;
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            if (!refreshing) {
                refreshing = true;
                window.location.reload();
            }
        });
    }

    // --- Graceful Connection drop monitoring: Avoid immediate redirects ---
    let connectionCheckInterval = null;
    let offlineTimeout = null;

    function handleDeviceOffline() {
        if (window.location.pathname.includes('/ofline/')) return;

        // 1. Create the banner dynamically if not present
        let banner = document.getElementById('premium-offline-toast');
        if (!banner) {
            banner = document.createElement('div');
            banner.id = 'premium-offline-toast';
            banner.className = 'premium-offline-banner';
            banner.innerHTML = `
                <div class="premium-offline-content">
                    <i class="premium-offline-icon fa-solid fa-wifi-slash"></i>
					<span>Connection lost. Attempting to reconnect...</span>
                </div>
                <div class="premium-offline-spinner"></div>
            `;
            document.body.appendChild(banner);
        }

        // Force reflow and display banner
        setTimeout(() => banner.classList.add('show'), 100);

        // 2. Start dynamic countdown (5 seconds before ultimate redirect)
        if (offlineTimeout) clearTimeout(offlineTimeout);
        offlineTimeout = setTimeout(() => {
            sessionStorage.setItem('offline_fallback_url', window.location.href);
            window.location.href = '/ofline/index.html';
        }, 5000);

        // 3. Start active background reconnect checking (pings every 1.5 seconds)
        if (connectionCheckInterval) clearInterval(connectionCheckInterval);
        connectionCheckInterval = setInterval(async () => {
            try {
                const response = await fetch('/manifest.json?t=' + Date.now(), {
                    method: 'HEAD',
                    cache: 'no-store'
                });
                if (response.ok) {
                    // Connection restored! Clear everything and hide banner
                    handleDeviceOnline();
                }
            } catch (e) {
                // Still offline
            }
        }, 1500);
    }

    function handleDeviceOnline() {
        // Clear intervals and timeouts
        if (offlineTimeout) {
            clearTimeout(offlineTimeout);
            offlineTimeout = null;
        }
        if (connectionCheckInterval) {
            clearInterval(connectionCheckInterval);
            connectionCheckInterval = null;
        }

        // Hide the warning banner
        const banner = document.getElementById('premium-offline-toast');
        if (banner) {
            banner.classList.remove('show');
        }
    }

    window.addEventListener('offline', handleDeviceOffline);
    window.addEventListener('online', handleDeviceOnline);

    // Prevent form submissions when offline to avoid browser network error screens
    document.addEventListener('submit', function(event) {
        if (!navigator.onLine) {
            event.preventDefault();
            handleDeviceOffline(); // Instantly show offline toast
        }
    }, true);
});
