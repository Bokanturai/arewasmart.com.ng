@if(isset($adverts) && $adverts->count() > 0)
<div class="mt-3 mb-4">
    <div id="serviceAdvertCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-indicators">
            @foreach($adverts as $index => $advert)
                <button type="button" data-bs-target="#serviceAdvertCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
        
        {{-- Optional: Add prev/next buttons for better UX --}}
        <button class="carousel-control-prev d-none d-md-flex" type="button" data-bs-target="#serviceAdvertCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next d-none d-md-flex" type="button" data-bs-target="#serviceAdvertCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
        
        <div class="carousel-inner rounded-4 shadow-lg">
            @foreach($adverts as $index => $advert)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    @if($advert->link)
                        <a href="{{ $advert->link }}" class="text-decoration-none">
                    @endif
                    <div class="advert-card position-relative overflow-hidden rounded-4" 
                         id="advert-card-{{ $index }}"
                         style="height: 240px; background: {{ $advert->image ? 'url(' . asset($advert->image) . ')' : 'linear-gradient(135deg, #111827 0%, #F26522 100%)' }}; background-size: cover; background-position: center;">
                        
                        {{-- Image Fallback Logic --}}
                        @if($advert->image)
                            <img src="{{ asset($advert->image) }}" class="d-none" 
                                 onerror="this.parentElement.style.backgroundImage = 'linear-gradient(135deg, #111827 0%, #F26522 100%)';">
                        @endif

                        <!-- Premium Overlay with Gradient Animation -->
                        <div class="advert-overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center px-4 px-md-5" 
                             style="background: linear-gradient(105deg, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.1) 100%);">
                            
                            <!-- Floating Elements for Depth -->
                            <div class="floating-elements">
                                <div class="circle-blur position-absolute rounded-circle bg-white" style="width: 200px; height: 200px; top: -50px; left: -50px; opacity: 0.03;"></div>
                                <div class="circle-blur position-absolute rounded-circle bg-primary" style="width: 250px; height: 250px; bottom: -80px; right: -50px; opacity: 0.08;"></div>
                            </div>
                            
                            <!-- Service Badge with Icon -->
                            @if($advert->service_name)
                                <div class="service-badge mb-3 animate__animated animate__fadeInDown">
                                    <span class="badge bg-warning bg-gradient text-dark px-3 py-2 rounded-pill fw-bold text-uppercase shadow-sm" style="font-size: 0.7rem; letter-spacing: 1.5px; background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);">
                                        <i class="ti ti-crown me-1"></i> {{ $advert->service_name }}
                                    </span>
                                </div>
                            @endif
                            
                            <!-- Main Message with Text Shadow -->
                            <h3 class="text-white fw-bold mb-2 animate__animated animate__fadeInLeft" style="font-size: 1.5rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); max-width: 80%;">
                                {{ $advert->message }}
                            </h3>
                            
                            <!-- Discount Banner with Enhanced Design -->
                            @if($advert->discount)
                                <div class="discount-banner mb-3 animate__animated animate__fadeInLeft animate__delay-1s">
                                    <div class="d-inline-flex align-items-center bg-white bg-opacity-15 border border-white border-opacity-25 rounded-pill px-3 py-1.5 backdrop-blur">
                                        <span class="badge bg-danger me-2 px-2 py-1 rounded-pill" style="font-size: 0.7rem;">
                                            <i class="ti ti-discount-2 me-1"></i> {{ $advert->discount }}% OFF
                                        </span>
                                        <span class="text-white small fw-semibold">Limited Time Offer</span>
                                    </div>
                                    <p class="text-white-50 small mt-1 mb-0" style="font-size: 0.75rem;">
                                        <i class="ti ti-clock me-1"></i> Hurry! Offer ends soon
                                    </p>
                                </div>
                            @endif
                            </div>
                        </div>
                    </div>
                    @if($advert->link)
                        </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <style>
   

    /* Backdrop Blur */
    .backdrop-blur {
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    
    .bg-opacity-15 {
        --bs-bg-opacity: 0.15;
        background-color: rgba(255, 255, 255, var(--bs-bg-opacity)) !important;
    }
    
    .py-1\.5 {
        padding-top: 0.375rem !important;
        padding-bottom: 0.375rem !important;
    }

    /* Carousel Indicators */
    #serviceAdvertCarousel .carousel-indicators {
        bottom: 15px;
        justify-content: flex-start;
        margin-left: 2rem;
        margin-right: 0;
        gap: 4px;
    }
    
    #serviceAdvertCarousel .carousel-indicators [data-bs-target] {
        width: 12px;
        height: 4px;
        border-radius: 4px;
        margin: 0;
        background-color: rgba(255, 255, 255, 0.5);
        border: none;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    #serviceAdvertCarousel .carousel-indicators [data-bs-target]:hover {
        background-color: rgba(255, 255, 255, 0.8);
        transform: scaleY(1.2);
    }
    
    #serviceAdvertCarousel .carousel-indicators .active {
        background: var(--primary-gradient);
        width: 35px;
        opacity: 1;
    }

    /* Card Styles */
    .advert-card {
        transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    
   
    /* Hot Badge Animation */
    .hot-badge {
        background: linear-gradient(135deg, #dc3545 0%, #ff6b81 100%) !important;
        position: relative;
        overflow: hidden;
    }
    
    .hot-badge::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: rgba(255,255,255,0.1);
        transform: rotate(45deg);
        animation: shimmer 3s infinite;
    }
    
    .pulse-dot {
        width: 8px;
        height: 8px;
        background-color: #fff;
        border-radius: 50%;
        margin-right: 6px;
        animation: pulse-dot 2s infinite;
    }
    
    @keyframes pulse-dot {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.5); opacity: 0.5; }
        100% { transform: scale(1); opacity: 1; }
    }
    
    @keyframes shimmer {
        0% { transform: rotate(45deg) translateX(-100%); }
        100% { transform: rotate(45deg) translateX(100%); }
    }

    /* CTA Button Hover Effect */
    .btn-cta {
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .btn-cta:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(242, 101, 34, 0.3) !important;
    }
    
    .btn-cta:hover i {
        animation: arrow-move 0.6s ease infinite;
    }
    
    @keyframes arrow-move {
        0% { transform: translateX(0); }
        50% { transform: translateX(5px); }
        100% { transform: translateX(0); }
    }

    /* Carousel Transition Enhancement */
    #serviceAdvertCarousel .carousel-item {
        transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Floating Circles Animation */
    .circle-blur {
        animation: float 8s infinite ease-in-out;
    }
    
    .circle-blur:nth-child(2) {
        animation-delay: -4s;
    }
    
    @keyframes float {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(10px, -10px); }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .advert-card {
            height: 200px !important;
        }
        
        #serviceAdvertCarousel .carousel-indicators {
            justify-content: center;
            margin-left: 0;
        }
        
        #serviceAdvertCarousel .carousel-indicators [data-bs-target] {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }
        
        #serviceAdvertCarousel .carousel-indicators .active {
            width: 24px;
            border-radius: 12px;
        }
        
        h3 {
            font-size: 1.1rem !important;
            max-width: 100% !important;
        }
    }

    /* Loading Animation (optional) */
    .carousel-item {
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
    }
    
    .carousel-item.active {
        opacity: 1;
    }

    /* Optional: Add these animation classes if you include Animate.css or create your own */
    .animate__animated {
        animation-duration: 0.8s;
        animation-fill-mode: both;
    }
    
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes fadeInLeft {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate__fadeInDown { animation-name: fadeInDown; }
    .animate__fadeInLeft { animation-name: fadeInLeft; }
    .animate__fadeInUp { animation-name: fadeInUp; }
    .animate__delay-1s { animation-delay: 0.2s; }
    .animate__delay-2s { animation-delay: 0.4s; }
</style>
@endif