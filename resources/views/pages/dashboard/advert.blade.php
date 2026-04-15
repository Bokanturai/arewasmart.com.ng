@if(isset($adverts) && $adverts->count() > 0)
    <div id="serviceAdvertCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000" data-bs-pause="hover">
        <div class="carousel-indicators custom-indicators">
            @foreach($adverts as $index => $advert)
                <button type="button" data-bs-target="#serviceAdvertCarousel" data-bs-slide-to="{{ $index }}" class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
        
        <button class="carousel-control-prev custom-nav d-none d-md-flex" type="button" data-bs-target="#serviceAdvertCarousel" data-bs-slide="prev">
            <i class="ti ti-chevron-left"></i>
        </button>
        <button class="carousel-control-next custom-nav d-none d-md-flex" type="button" data-bs-target="#serviceAdvertCarousel" data-bs-slide="next">
            <i class="ti ti-chevron-right"></i>
        </button>
        
        <div class="carousel-inner premium-carousel-inner shadow-lg border-0" style="border-radius: 30px !important;">
            @foreach($adverts as $index => $advert)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                    @if($advert->link)
                        <a href="{{ $advert->link }}" class="text-decoration-none h-100 w-100">
                    @endif
                    <div class="advert-card position-relative overflow-hidden" 
                         style="background: {{ $advert->image ? 'url(' . asset($advert->image) . ')' : 'linear-gradient(135deg, #0f172a 0%, #334155 100%)' }}; background-size: cover; background-position: center;">
                        
                        @if($advert->image)
                            <img src="{{ asset($advert->image) }}" class="d-none" 
                                 onerror="this.parentElement.style.backgroundImage = 'linear-gradient(135deg, #0f172a 0%, #334155 100%)';">
                        @endif

                        <!-- Ambient Background Objects -->
                        <div class="glass-ambient-overlay">
                            <div class="ambient-circle ambient-1"></div>
                            <div class="ambient-circle ambient-2"></div>
                        </div>

                        <!-- Main Content Overlay -->
                        <div class="advert-content-wrapper d-flex flex-column justify-content-center h-100 px-4 px-md-5">
                            <div class="content-glass-panel p-3 p-md-4">
                                <!-- Service Badge -->
                                @if($advert->service_name)
                                    <div class="service-tag-wrapper mb-2 animate__animated animate__fadeInDown">
                                        <span class="glass-badge-service">
                                            <i class="ti ti-sparkles me-1 text-warning"></i> {{ $advert->service_name }}
                                        </span>
                                    </div>
                                @endif
                                
                                <!-- Message -->
                                <h3 class="advert-title text-white fw-bold mb-2 animate__animated animate__fadeInLeft">
                                    {{ $advert->message }}
                                </h3>
                                
                                <!-- CTA Area -->
                                <div class="d-flex align-items-center gap-3 mt-auto">
                                    @if($advert->discount)
                                        <div class="glass-discount-pill">
                                            <span class="discount-value">{{ $advert->discount }}</span>
                                            <span class="discount-text ms-1">OFF</span>
                                        </div>
                                    @endif
                                    
                                    <div class="cta-indicator">
                                        <span class="small text-white-50 fw-medium">Tap to explore</span>
                                        <i class="ti ti-arrow-right ms-1 text-white animate-arrow-side"></i>
                                    </div>
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
    :root {
        --advert-height-desktop: 240px;
        --advert-height-mobile: 180px;
        --glass-border: rgba(255, 255, 255, 0.15);
    }

    /* Aggressive Spacing Fixes */
    #serviceAdvertCarousel {
        border-radius: 30px;
        overflow: hidden;
        margin-bottom: -15px !important;
        height: var(--advert-height-desktop);
    }

    /* Mobile Height Correction */
    @media (max-width: 768px) {
        #serviceAdvertCarousel {
            height: var(--advert-height-mobile);
        }
    }

    .premium-carousel-inner {
        border-radius: 30px !important;
        height: 100%;
        margin: 0 !important;
        padding: 0 !important;
    }

    .advert-card {
        height: 100%;
        width: 100%;
        display: flex;
        border-radius: 30px !important;
        background-color: #0f172a;
        z-index: 1;
        margin: 0 !important;
    }

    /* Glass Effect Panel */
    .content-glass-panel {
        background: rgba(0, 0, 0, 0.45);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        max-width: 85%;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        position: relative;
        z-index: 5;
    }

    /* Discount Pill */
    .glass-discount-pill {
        background: linear-gradient(135deg, #f26522, #ea580c);
        padding: 4px 12px;
        border-radius: 100px;
        color: #fff;
        font-weight: 800;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 15px rgba(242, 101, 34, 0.4);
        border: 1px solid rgba(255,255,255,0.2);
    }

    .discount-value { font-size: 1rem; }
    .discount-text { font-size: 0.65rem; opacity: 0.9; }

    /* Indicators - Inside and Absolute */
    .custom-indicators {
        position: absolute !important;
        bottom: 10px !important; 
        left: 50% !important;
        transform: translateX(-50%) !important;
        margin: 0 !important;
        padding: 0 !important;
        gap: 6px;
        z-index: 15;
        width: fit-content !important;
    }

    .custom-indicators [data-bs-target] {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.4);
        border: none;
        margin: 0 !important;
    }

    .custom-indicators .active {
        width: 16px;
        border-radius: 8px;
        background-color: #fff;
    }

    /* Ambient Background Objects */
    .glass-ambient-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        z-index: 2;
    }

    .ambient-circle {
        position: absolute;
        border-radius: 50%;
        filter: blur(50px);
        opacity: 0.12;
        animation: float-ambient 12s infinite ease-in-out;
    }

    .ambient-1 {
        width: 200px; height: 200px;
        background: #f26522; top: -50px; right: -20px;
    }

    .ambient-2 {
        width: 180px; height: 180px;
        background: #0ea5e9; bottom: -40px; left: 5%;
        animation-delay: -6s;
    }

    /* Text Style */
    .advert-title {
        font-size: 1.5rem;
        line-height: 1.2;
        text-shadow: 0 2px 10px rgba(0,0,0,0.3);
        margin-bottom: 0.5rem;
    }

    .glass-badge-service {
        background: rgba(255, 255, 255, 0.1);
        padding: 3px 10px;
        border-radius: 100px;
        font-size: 0.7rem;
        font-weight: 700;
        color: #fff;
        text-transform: uppercase;
        border: 1px solid rgba(255,255,255,0.1);
    }

    /* Navigation */
    .custom-nav {
        width: 36px;
        height: 36px;
        background: rgba(255, 255, 255, 0.1) !important;
        backdrop-filter: blur(8px);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        margin: 0 10px;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        opacity: 0.4;
        transition: all 0.3s ease;
        color: white;
        z-index: 10;
    }

    .custom-nav:hover { opacity: 1; background: #f26522 !important; }

    @media (max-width: 768px) {
        .content-glass-panel {
            max-width: 90%;
            padding: 0.85rem !important;
        }
        .advert-title {
            font-size: 1rem;
            margin-bottom: 0.3rem;
        }
    }

    @keyframes float-ambient {
        0%, 100% { transform: translate(0, 0); }
        50% { transform: translate(10px, -10px); }
    }
    
    .animate-swipe {
        animation: swipe-hint 2s infinite ease-in-out;
    }
    
    @keyframes swipe-hint {
        0%, 100% { transform: translateX(0); opacity: 0.5; }
        50% { transform: translateX(-8px); opacity: 1; }
    }
</style>
@endif