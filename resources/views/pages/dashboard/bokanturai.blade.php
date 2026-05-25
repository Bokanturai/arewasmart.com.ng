@push('styles')
<style>
/* ──────────────────────────────────────────
   Advert Carousel  (bokanturai.blade.php)
   ────────────────────────────────────────── */
.adv-wrap { position: relative; }

.adv-carousel {
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 6px 24px rgba(0,0,0,0.12);
}

.adv-slide-link {
    display: block;
    text-decoration: none;
}

/* ── Slide Card ── */
.adv-card {
    border-radius: 22px;
    min-height: 115px;
    display: flex;
    align-items: center;
    padding: 0;
    overflow: hidden;
    position: relative;
}

/* ── Background image ── */
.adv-bg-img {
    position: absolute;
    inset: 0;
    background-size: cover;
    background-position: center;
    transition: transform 0.6s ease;
}
.carousel-item.active .adv-bg-img {
    transform: scale(1.04);
}
.adv-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(120deg, rgba(10,10,30,0.72) 0%, rgba(10,10,30,0.32) 100%);
}

/* ── Gradient Fallbacks ── */
.adv-gradient-bg {
    position: absolute;
    inset: 0;
}
.adv-grad-0 { background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); }
.adv-grad-1 { background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%); }
.adv-grad-2 { background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%); }
.adv-grad-3 { background: linear-gradient(135deg, #10b981 0%, #0ea5e9 100%); }
.adv-grad-4 { background: linear-gradient(135deg, #ec4899 0%, #f97316 100%); }

/* ── Body content ── */
.adv-body {
    position: relative;
    z-index: 2;
    padding: 20px 20px;
    width: 100%;
}

/* ── Text ── */
.adv-tag {
    display: inline-block;
    background: rgba(255,255,255,0.22);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    border-radius: 20px;
    padding: 2px 10px;
    margin-bottom: 6px;
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,0.25);
}
.adv-message {
    color: #fff;
    font-size: 13.5px;
    font-weight: 600;
    line-height: 1.4;
    text-shadow: 0 1px 4px rgba(0,0,0,0.3);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.adv-discount-badge {
    background: rgba(255,255,255,0.95);
    color: #7c3aed;
    font-size: 11px;
    font-weight: 700;
    border-radius: 20px;
    padding: 2px 10px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

/* ── CTA Arrow ── */
.adv-arrow {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.22);
    backdrop-filter: blur(8px);
    border: 1.5px solid rgba(255,255,255,0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.1rem;
    transition: all 0.25s ease;
    flex-shrink: 0;
}
.adv-slide-link:hover .adv-arrow,
.adv-slide-link:active .adv-arrow {
    background: rgba(255,255,255,0.4);
    transform: translateX(4px);
}

/* ── Decorative circles ── */
.adv-deco {
    position: absolute;
    border-radius: 50%;
    opacity: 0.10;
    pointer-events: none;
    z-index: 1;
    background: #fff;
}
.adv-deco-1 { width: 130px; height: 130px; bottom: -45px; right: -25px; }
.adv-deco-2 { width: 75px;  height: 75px;  top: -22px;   right: 65px;  }

/* ── Dot Indicators ── */
.adv-dots {
    display: flex;
    justify-content: center;
    gap: 5px;
    padding: 10px 0 2px;
}
.adv-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #d1d5db;
    border: none;
    padding: 0;
    cursor: pointer;
    transition: all 0.3s ease;
}
.adv-dot.active {
    width: 22px;
    border-radius: 3px;
    background: #6366f1;
}

/* ── Carousel transition ── */
#advertCarousel .carousel-item {
    transition: transform 0.55s ease-in-out;
}
</style>
@endpush

{{-- ── Advert Carousel ── --}}
@php
    /* Use real DB adverts if available, else show demo slides */
    $slides = (isset($adverts) && $adverts->count())
        ? $adverts->map(fn($a) => [
            'tag'      => $a->service_name,
            'message'  => $a->message,
            'discount' => $a->discount,
            'image'    => $a->image,
            'link'     => $a->link ?? '#',
            'external' => str_starts_with($a->link ?? '', 'http'),
          ])
        : collect([
            [
                'tag'      => 'Arewa Smart',
                'message'  => 'Top up airtime & data instantly — anytime, anywhere.',
                'discount' => null,
                'image'    => null,
                'link'     => route('airtime'),
                'external' => false,
                'grad'     => 0,
            ],
            [
                'tag'      => 'BVN & NIN',
                'message'  => 'Verify, modify and manage your identity records with ease.',
                'discount' => null,
                'image'    => null,
                'link'     => route('bvn-crm'),
                'external' => false,
                'grad'     => 1,
            ],
            [
                'tag'      => 'Wallet',
                'message'  => 'Fund your wallet and enjoy seamless bill payments all day.',
                'discount' => null,
                'image'    => null,
                'link'     => route('wallet'),
                'external' => false,
                'grad'     => 3,
            ],

            [
                'tag'      => 'Transfer',
                'message'  => 'Send money instantly to any Arewa Smart user at zero hassle.',
                'discount' => null,
                'image'    => null,
                'link'     => route('withdraw.index'),
                'external' => false,
                'grad'     => 4,
            ],

            [
                'tag'      => 'Gift Card',
                'message'  => 'Buy & redeem gift cards for yourself or loved ones today.',
                'discount' => null,
                'image'    => null,
                'link'     => route('gift-card.index'),
                'external' => false,
                'grad'     => 2,
            ],
        ]);
@endphp

<div class="adv-wrap mb-1">
    <div id="advertCarousel" class="carousel slide adv-carousel"
         data-bs-ride="carousel" data-bs-interval="4500">

        {{-- Slides --}}
        <div class="carousel-inner">
            @foreach($slides as $i => $slide)
                <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                    <a href="{{ $slide['link'] }}"
                       class="adv-slide-link"
                       target="{{ ($slide['external'] ?? false) ? '_blank' : '_self' }}"
                       rel="noopener">

                        <div class="adv-card">

                            {{-- Background --}}
                            @if(!empty($slide['image']))
                                <div class="adv-bg-img" style="background-image: url('{{ $slide['image'] }}');"></div>
                                <div class="adv-overlay"></div>
                            @else
                                <div class="adv-gradient-bg adv-grad-{{ $slide['grad'] ?? ($i % 5) }}"></div>
                            @endif

                            {{-- Content --}}
                            <div class="adv-body d-flex align-items-center justify-content-between gap-3">
                                <div class="adv-text">
                                    @if(!empty($slide['tag']))
                                        <span class="adv-tag">{{ $slide['tag'] }}</span>
                                    @endif
                                    <p class="adv-message mb-0">{{ $slide['message'] }}</p>
                                    @if(!empty($slide['discount']))
                                        <span class="adv-discount-badge mt-2 d-inline-block">
                                            🎉 {{ $slide['discount'] }} OFF
                                        </span>
                                    @endif
                                </div>
                                <div class="adv-cta flex-shrink-0">
                                    <div class="adv-arrow">
                                        <i class="ti ti-arrow-right"></i>
                                    </div>
                                </div>
                            </div>

                            {{-- Decorative bubbles --}}
                            <div class="adv-deco adv-deco-1"></div>
                            <div class="adv-deco adv-deco-2"></div>
                        </div>

                    </a>
                </div>
            @endforeach
        </div>

        {{-- Dot Indicators --}}
        @if($slides->count() > 1)
            <div class="adv-dots">
                @foreach($slides as $i => $slide)
                    <button type="button"
                        data-bs-target="#advertCarousel"
                        data-bs-slide-to="{{ $i }}"
                        class="adv-dot {{ $i === 0 ? 'active' : '' }}"
                        aria-label="Slide {{ $i + 1 }}">
                    </button>
                @endforeach
            </div>
        @endif

    </div>
</div>
