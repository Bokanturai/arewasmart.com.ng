<x-app-layout>
    <title>Arewa Smart - Generate Gift Card</title>

    <!-- External Assets -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <!-- Premium Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Outfit:wght@400;600;700;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --gc-primary: #4f46e5;
            --gc-secondary: #3b82f6;
            --gc-surface: #ffffff;
            --gc-border-radius: 24px;
        }

        /* ── Common Card Styles ─────────────────────────────────────────── */
        /* Using Bootstrap card utility classes */

        /* ── Amount Quick Select ────────────────────────────────────────── */
        .amount-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .amount-chip {
            padding: 8px 16px;
            border-radius: 12px;
            border: 1.5px solid #eef2ff;
            background: #f8faff;
            color: #4f46e5;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
        }

        .amount-chip:hover, .amount-chip.active {
            background: #4f46e5;
            border-color: #4f46e5;
            color: #ffffff;
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.2);
        }

        /* ── Theme Tabs & Swatches ──────────────────────────────────────── */
        .theme-tabs {
            display: flex;
            overflow-x: auto;
            gap: 8px;
            padding-bottom: 10px;
            margin-bottom: 15px;
            scrollbar-width: thin;
        }

        .theme-tabs::-webkit-scrollbar { height: 4px; }
        .theme-tabs::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        .theme-tab {
            white-space: nowrap;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            background: #f1f5f9;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .theme-tab.active {
            background: #4f46e5;
            color: #ffffff;
        }

        .theme-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 10px;
            max-height: 250px;
            overflow-y: auto;
            padding: 5px;
        }

        .theme-swatch {
            aspect-ratio: 16/10;
            border-radius: 12px;
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            color: #ffffff;
            cursor: pointer;
            border: 3px solid transparent;
            transition: transform 0.2s, box-shadow 0.2s;
            text-align: center;
            text-transform: uppercase;
        }

        .theme-swatch:hover { transform: scale(1.03); }
        .theme-swatch.selected { border-color: #4f46e5; transform: scale(1.05); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .theme-swatch .sw-icon { font-size: 1.2rem; margin-bottom: 4px; }

        /* Theme Gradients */
        .sw-birthday { background: linear-gradient(135deg, #f43f5e, #fb923c); }
        .sw-celebration { background: linear-gradient(135deg, #6366f1, #a855f7); }
        .sw-wedding { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%); }
        .sw-anniversary { background: linear-gradient(135deg, #ee9ca7 0%, #ffdde1 100%); }
        .sw-graduation { background: linear-gradient(135deg, #0ea5e9, #2563eb); }
        .sw-party { background: linear-gradient(135deg, #fbbf24, #f97316); }
        .sw-thanks { background: linear-gradient(135deg, #a8ff78, #78ffd6); }

        .sw-romantic { background: linear-gradient(135deg, #e11d48, #be123c); }
        .sw-love { background: linear-gradient(135deg, #ff0000, #ff6666); }
        .sw-valentine { background: linear-gradient(to top, #ff0844 0%, #ffb199 100%); }
        .sw-passion { background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%); }
        .sw-care { background: linear-gradient(120deg, #f093fb 0%, #f5576c 100%); }
        .sw-friendship { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }

        .sw-business { background: linear-gradient(135deg, #0f172a, #334155); }
        .sw-corporate { background: linear-gradient(135deg, #243949 0%, #517fa4 100%); }
        .sw-modern-biz { background: linear-gradient(135deg, #09203f 0%, #537895 100%); }
        .sw-reward { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); }
        .sw-promotion { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .sw-salary { background: linear-gradient(135deg, #11998e, #38ef7d); }

        .sw-general { background: linear-gradient(135deg, #4f46e5, #3b82f6); }
        .sw-gaming { background: linear-gradient(135deg, #1e293b, #475569); }
        .sw-shopping { background: linear-gradient(120deg, #84fab0 0%, #8fd3f4 100%); }
        .sw-food { background: linear-gradient(135deg, #ff7e5f, #feb47b); }
        .sw-travel { background: linear-gradient(to top, #209cff 0%, #68e0cf 100%); }
        .sw-surprise { background: linear-gradient(135deg, #89f7fe 0%, #66a6ff 100%); }

        /* ── Live Card Component ─────────────────────────────────────────── */
        .live-card-container {
            perspective: 1000px;
            margin: 0 auto;
            max-width: 450px;
        }

        .gift-card-main {
            width: 100%;
            aspect-ratio: 1.7 / 1;
            min-height: 220px;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            color: #fff;
            font-family: 'Outfit', sans-serif;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
            display: flex;
            flex-direction: column;
            transition: all 0.5s ease;
        }

        .card-inner-overlay {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 10% 20%, rgba(255,255,255,0.1) 0%, transparent 60%);
            pointer-events: none;
        }

        .card-header-section {
            padding: 20px 25px 0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .card-brand-logo { font-weight: 800; font-size: 0.9rem; display: flex; align-items: center; gap: 8px; }
        .card-brand-logo i { background: rgba(255,255,255,0.2); width: 32px; height: 32px; border-radius: 8px; display: grid; place-items: center; }

        .card-occation-title {
            font-family: 'Dancing Script', cursive;
            font-size: 1.8rem;
            margin: 15px 0 5px;
            text-align: center;
            width: 100%;
        }

        .card-amount-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0 25px;
        }

        .card-value { font-size: 3.5rem; font-weight: 900; letter-spacing: -2px; margin-bottom: 0; }
        .card-message-text { font-size: 0.75rem; font-weight: 500; opacity: 0.85; text-align: center; max-width: 90%; }

        .card-footer-section {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(5px);
            padding: 12px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .code-placeholder { font-size: 0.7rem; font-weight: 700; letter-spacing: 1px; opacity: 0.8; }
        .qr-placeholder { width: 45px; height: 45px; background: #fff; border-radius: 6px; display: grid; place-items: center; color: #333; font-size: 9px; font-weight: bold; }

        /* ── Specialized Decorations ────────────────────────────────────── */
        .decor-balloon { position: absolute; font-size: 2.5rem; opacity: 0.4; pointer-events: none; animation: float 6s infinite ease-in-out; }
        @keyframes float { 0%, 100% { transform: translateY(0) rotate(5deg); } 50% { transform: translateY(-20px) rotate(-5deg); } }

        /* ── Responsive adjustments ─────────────────────────────────────── */
        @media (max-width: 1199px) {
            .card-value { font-size: 3rem; }
            .card-occation-title { font-size: 1.6rem; }
        }

        @media (max-width: 991px) {
            .preview-sticky { 
                position: relative !important; 
                top: 0 !important;
                margin-bottom: 2.5rem; 
            }
            .gift-card-main {
                max-width: 400px;
                margin: 0 auto;
                aspect-ratio: 1.5 / 1; 
                height: auto !important; /* Allow height to expand if content is long */
                min-height: 250px;
            }
        }

        @media (max-width: 575px) {
            .premium-card { padding: 1.25rem !important; }
            .card-header-section { padding: 12px 15px 0; }
            .card-footer-section { padding: 10px 15px; min-height: 60px; }
            .card-value { font-size: 2.2rem; }
            .card-occation-title { font-size: 1.2rem; margin: 8px 0 2px; }
            .card-message-text { font-size: 0.65rem; line-height: 1.2; padding: 0 10px; }
            .qr-placeholder { width: 40px; height: 40px; font-size: 7px; flex-shrink: 0; }
            .code-placeholder { font-size: 0.6rem; word-break: break-all; flex: 1; }
            .amount-chip { padding: 6px 10px; font-size: 0.75rem; }
            .theme-swatch { font-size: 0.6rem; padding: 6px; }
            .gift-card-main { min-height: 230px; aspect-ratio: auto; } /* Remove aspect ratio on very small screens for safety */
        }
    </style>

    <div class="container-fluid px-0 px-md-3 py-4">
        <div class="row g-0 g-md-4">
            
            <div class="col-12 col-xl-5 mb-4">
                <div class="card shadow-lg border-0 p-4 p-xl-5" style="border-radius: 20px;">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="avatar-sm bg-primary-soft rounded-3 p-2">
                            <i class="ti ti-gift fs-1 text-primary"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1">Create Gift Card</h4>
                            <p class="text-muted small mb-0">Design a personalized voucher for someone special.</p>
                        </div>
                    </div>

                    <form action="{{ route('gift-card.store') }}" method="POST" id="generateForm">
                        @csrf
                        <input type="hidden" name="pin_confirmation" id="pinConfirmation" value="">
                        <input type="hidden" name="style" id="style_input" value="general">

                        <!-- Amount Section -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase letter-spacing-1">Select Amount</label>
                            <div class="amount-chips">
                                <button type="button" class="amount-chip" data-amount="1000">₦1,000</button>
                                <button type="button" class="amount-chip" data-amount="2000">₦2,000</button>
                                <button type="button" class="amount-chip active" data-amount="5000">₦5,000</button>
                                <button type="button" class="amount-chip" data-amount="10000">₦10,000</button>
                                <button type="button" class="amount-chip" data-amount="20000">₦20,000</button>
                                <button type="button" class="amount-chip" data-amount="50000">₦50,000</button>
                            </div>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-end-0">₦</span>
                                <input type="number" name="amount" id="amount_input" class="form-control border-start-0 ps-0 fw-bold" 
                                    min="100" placeholder="Custom Amount" value="5000">
                            </div>
                        </div>

                        <!-- Occasion / Title -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase letter-spacing-1">Occasion / Title</label>
                            <div class="input-group overflow-hidden rounded-3 shadow-sm border">
                                <span class="input-group-text bg-white border-0"><i class="ti ti-heading"></i></span>
                                <input type="text" name="title" id="title_input" class="form-control border-0 ps-0" 
                                    maxlength="25" placeholder="e.g. Happy Birthday!" value="Happy Birthday!">
                                <div class="bg-white border-0 p-1 d-flex align-items-center" style="width: 42px;">
                                    <input type="color" name="title_color" id="title_color_input" class="form-control-color border-0 p-0" 
                                        value="#a83535" title="Pick Title Color" style="width: 100%; height: 26px; cursor: pointer; border-radius: 4px;">
                                </div>
                            </div>
                            <div class="form-text small opacity-75 mt-1 d-flex justify-content-between">
                                <span>Max 25 characters</span>
                                <span class="text-primary fw-bold">Title Color Picker <i class="ti ti-arrow-up"></i></span>
                            </div>
                        </div>

                        <!-- Message -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase letter-spacing-1">Personal Message</label>
                            <textarea name="message" id="message_input" class="form-control rounded-3" rows="3" 
                                placeholder="Enter a sweet message here...">Wishing you a wonderful day filled with joy!</textarea>
                            
                            <div class="mt-3 p-3 bg-light-soft rounded-4 border border-dashed d-flex align-items-center justify-content-between shadow-sm">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-white p-2 rounded-circle shadow-sm" style="width: 40px; height: 40px; display: grid; place-items: center;">
                                        <i class="ti ti-palette text-primary fs-20"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark small">Body Text Color</div>
                                        <div class="text-muted" style="font-size: 0.65rem;">Customize message visibility</div>
                                    </div>
                                </div>
                                <div class="position-relative">
                                    <input type="color" name="text_color" id="text_color_input" class="form-control-color border-0 p-0 bg-transparent rounded-circle shadow-sm" 
                                        value="#fdaeaeff" style="width: 45px; height: 45px; cursor: pointer; z-index: 2;">
                                    <div class="position-absolute top-100 start-50 translate-middle-x mt-1">
                                        <span class="badge bg-primary rounded-pill px-2 py-1" style="font-size: 0.5rem;">PICKER</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Theme Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-uppercase letter-spacing-1">Choose Theme</label>
                            <div class="theme-tabs">
                                <button type="button" class="theme-tab" data-cat="all">Show All</button>
                                <button type="button" class="theme-tab active" data-cat="celebration">Celebration</button>
                                <button type="button" class="theme-tab" data-cat="love">Love</button>
                                <button type="button" class="theme-tab" data-cat="business">Business</button>
                                <button type="button" class="theme-tab" data-cat="general">Others</button>
                            </div>

                            <div class="theme-grid">
                                {{-- Celebration Category --}}
                                <div class="theme-swatch sw-birthday selected" data-theme="birthday" data-cat="celebration">
                                    <span class="sw-icon">🎂</span> Birthday
                                </div>
                                <div class="theme-swatch sw-celebration" data-theme="celebration" data-cat="celebration">
                                    <span class="sw-icon">🎉</span> Congrats
                                </div>
                                <div class="theme-swatch sw-wedding" data-theme="wedding" data-cat="celebration">
                                    <span class="sw-icon">💍</span> Wedding
                                </div>
                                <div class="theme-swatch sw-anniversary" data-theme="anniversary" data-cat="celebration">
                                    <span class="sw-icon">🎊</span> Anniversary
                                </div>
                                <div class="theme-swatch sw-graduation" data-theme="graduation" data-cat="celebration">
                                    <span class="sw-icon">🎓</span> Graduate
                                </div>
                                <div class="theme-swatch sw-party" data-theme="party" data-cat="celebration">
                                    <span class="sw-icon">🕺</span> Party
                                </div>
                                <div class="theme-swatch sw-thanks" data-theme="thankyou" data-cat="celebration">
                                    <span class="sw-icon">🙏</span> Thanks
                                </div>

                                {{-- Love Category --}}
                                <div class="theme-swatch sw-romantic" data-theme="romantic" data-cat="love">
                                    <span class="sw-icon">🌹</span> Romantic
                                </div>
                                <div class="theme-swatch sw-love" data-theme="love" data-cat="love">
                                    <span class="sw-icon">❤️</span> Love
                                </div>
                                <div class="theme-swatch sw-valentine" data-theme="valentine" data-cat="love">
                                    <span class="sw-icon">💘</span> Valentine
                                </div>
                                <div class="theme-swatch sw-passion" data-theme="passion" data-cat="love">
                                    <span class="sw-icon">🔥</span> Passion
                                </div>
                                <div class="theme-swatch sw-care" data-theme="care" data-cat="love">
                                    <span class="sw-icon">🤗</span> Care
                                </div>
                                <div class="theme-swatch sw-friendship" data-theme="friendship" data-cat="love">
                                    <span class="sw-icon">🤝</span> Friendly
                                </div>

                                {{-- Business Category --}}
                                <div class="theme-swatch sw-business" data-theme="reward" data-cat="business">
                                    <span class="sw-icon">💼</span> Business
                                </div>
                                <div class="theme-swatch sw-corporate" data-theme="corporate" data-cat="business">
                                    <span class="sw-icon">🏢</span> Corporate
                                </div>
                                <div class="theme-swatch sw-modern-biz" data-theme="modern_biz" data-cat="business">
                                    <span class="sw-icon">🚀</span> Startup
                                </div>
                                <div class="theme-swatch sw-reward" data-theme="reward" data-cat="business">
                                    <span class="sw-icon">🏆</span> Gold Reward
                                </div>
                                <div class="theme-swatch sw-promotion" data-theme="promotion" data-cat="business">
                                    <span class="sw-icon">📈</span> Growth
                                </div>
                                <div class="theme-swatch sw-salary" data-theme="salary" data-cat="business">
                                    <span class="sw-icon">💰</span> Salary
                                </div>

                                {{-- General/Others Category --}}
                                <div class="theme-swatch sw-general" data-theme="classic" data-cat="general">
                                    <span class="sw-icon">💳</span> Classic
                                </div>
                                <div class="theme-swatch sw-gaming" data-theme="gaming" data-cat="general">
                                    <span class="sw-icon">🎮</span> Gaming
                                </div>
                                <div class="theme-swatch sw-shopping" data-theme="shopping" data-cat="general">
                                    <span class="sw-icon">🛍️</span> Shopping
                                </div>
                                <div class="theme-swatch sw-food" data-theme="food" data-cat="general">
                                    <span class="sw-icon">🍕</span> Foodie
                                </div>
                                <div class="theme-swatch sw-travel" data-theme="travel" data-cat="general">
                                    <span class="sw-icon">✈️</span> Travel
                                    </div>
                                <div class="theme-swatch sw-surprise" data-theme="surprise" data-cat="general">
                                    <span class="sw-icon">🎁</span> Surprise
                                </div>
                            </div>
                        </div>

                        <div class="alert bg-primary-soft border-0 d-flex justify-content-between align-items-center p-3 mb-4">
                            <div class="small fw-semibold">Creation Fee: <span class="text-primary">₦{{ number_format($creationFee, 2) }}</span></div>
                            <div class="fw-black text-dark fs-20" id="total_deduction">₦0.00</div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-pill py-3 fw-bold shadow">
                            <i class="ti ti-lock me-2"></i> Generate
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Live Preview -->
            <div class="col-12 col-xl-7 mt-2 mt-md-0 order-first order-lg-last">
                <div class="preview-sticky position-sticky" style="top: 2rem;">
                    <div class="text-center mb-4">
                        <span class="badge bg-light text-primary py-2 px-3 fw-bold rounded-pill border">LIVE PREVIEW</span>
                    </div>

                    <div class="live-card-container">
                        <div class="gift-card-main" id="gift_card_preview">
                            <div class="card-inner-overlay"></div>
                            
                            <!-- Header -->
                            <div class="card-header-section">
                                <div class="card-brand-logo">
                                    <i class="ti ti-smart-home"></i> Arewa Smart
                                </div>
                                <div class="small fw-bold opacity-75">GIFT VOUCHER</div>
                            </div>

                            <!-- Ocassion -->
                            <div class="card-occation-title" id="preview_title">Happy Birthday!</div>

                            <!-- Body -->
                            <div class="card-amount-section">
                                <h1 class="card-value">₦<span id="preview_amount">5,000</span></h1>
                                <p class="card-message-text" id="preview_message">Wishing you a wonderful day filled with joy!</p>
                            </div>

                            <!-- Footer -->
                            <div class="card-footer-section">
                                <div class="code-placeholder">CODE: XXXXX-XXXXX-XXXXX</div>
                                <div class="qr-placeholder">QR CODE</div>
                            </div>

                            <!-- Decorative Balloons (for birthday) -->
                            <div class="decor-balloon theme-birthday-only" style="top: 20px; right: 10px;">🎈</div>
                            <div class="decor-balloon theme-birthday-only" style="bottom: 40px; left: 10px; animation-delay: -3s;">🎈</div>
                        </div>
                    </div>

                    <div class="text-center mt-5">
                        <p class="text-muted small mx-auto" style="max-width: 320px;">
                            <i class="ti ti-info-circle fs-4 mb-2 d-block"></i>
                            This is a visualization of your gift card. The functional QR code and secure link will be generated after payment.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PIN Modal -->
    <div class="modal fade" id="pinModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
            <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
                <div class="modal-body p-4 p-md-5 text-center">
                    <div class="bg-primary-soft rounded-circle d-inline-flex p-3 mb-4">
                        <i class="ti ti-shield-lock text-primary fs-1"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Authorize Security</h5>
                    <p class="text-muted small mb-4">Enter your 5-digit wallet PIN to authorize this ₦<span id="pin_modal_amount">0.00</span> generation.</p>
                    
                    <input type="password" id="pin_entry" maxlength="5" class="form-control text-center py-3 fs-1 fw-black border-2 border-primary rounded-4 mb-3" 
                        inputmode="numeric" placeholder="•••••" style="letter-spacing: 15px;">
                    
                    <button type="button" id="submit_form_btn" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">
                        Confirm & Pay Securely
                    </button>
                    <button type="button" class="btn btn-link text-muted small mt-2" data-bs-dismiss="modal">Cancel Transaction</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Redesigned Success Modal Code -->
    @if(session('generated_code'))
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 overflow-hidden shadow-2xl" style="border-radius: 20px;">
                <div class="row g-0">
                    <div class="col-lg-5 bg-gradient-success text-white p-5 d-flex flex-column justify-content-center" style="background: linear-gradient(135deg, #10b981, #059669);">
                        <div class="mb-4 text-center text-lg-start">
                            <div class="bg-white rounded-circle d-inline-flex p-3 mb-4 shadow">
                                <i class="ti ti-circle-check text-success fs-1"></i>
                            </div>
                            <h2 class="fw-black mb-1">Gift Card Ready!</h2>
                            <p class="opacity-90">Your voucher has been successfully generated and is ready to share.</p>
                        </div>
                        
                        <div class="d-grid gap-3">
                            <button class="btn btn-light btn-lg rounded-pill fw-bold py-3" onclick="copyCode()">
                                <i class="ti ti-copy me-2"></i> Copy Code
                            </button>
                            <button class="btn btn-outline-light btn-lg rounded-pill fw-bold py-3" onclick="downloadCard()">
                                <i class="ti ti-download me-2"></i> Download Card
                            </button>
                            <a href="{{ route('gift-card.index') }}" class="btn btn-link text-white text-decoration-none small text-center">
                                <i class="ti ti-layout-dashboard me-1"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-7 p-4 p-md-5 d-flex align-items-center justify-content-center bg-light">
                        <div id="capture_area" style="padding: 30px; background: #f8fafc; border-radius: 20px; width: 100%; max-width: 550px;">
                            <!-- The card will be re-rendered here for the success state -->
                            <div class="gift-card-main shadow-lg" id="success_card_final" style="height: auto !important; min-height: 280px; aspect-ratio: 1.6/1;">
                                <div class="card-inner-overlay"></div>
                                <div class="card-header-section">
                                    <div class="card-brand-logo"><i class="ti ti-smart-home"></i> Arewa Smart</div>
                                    <div class="small fw-bold opacity-75">GIFT VOUCHER</div>
                                </div>
                                <div class="card-occation-title" style="color: {{ session('gift_card')->title_color ?? '#ffffff' }};">{{ session('gift_card')->title }}</div>
                                <div class="card-amount-section">
                                    <h1 class="card-value">₦{{ number_format(session('gift_card')->amount, 0) }}</h1>
                                    <p class="card-message-text" style="color: {{ session('gift_card')->text_color ?? '#ffffff' }};">{{ session('gift_card')->message }}</p>
                                </div>
                                <div class="card-footer-section">
                                    <div class="code-placeholder" style="opacity: 1; letter-spacing: 1px; font-weight: 800;">CODE: {{ session('generated_code') }}</div>
                                    <div id="success_qr" style="background:#ffffff; padding: 5px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bsModal = new bootstrap.Modal(document.getElementById('successModal'));
            bsModal.show();
            new QRCode(document.getElementById("success_qr"), {
                text: "{{ session('generated_code') }}",
                width: 50,
                height: 50,
                colorDark: "#000000",
                colorLight: "#ffffff",
                correctLevel: QRCode.CorrectLevel.H
            });
        });
        function copyCode() {
            navigator.clipboard.writeText("{{ session('generated_code') }}");
            Swal.fire('Copied!', 'Gift card code copied to clipboard', 'success');
        }
        function downloadCard() {
            const btn = event.currentTarget;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="ti ti-loader-2 spin me-2"></i> Generating...';

            html2canvas(document.querySelector("#capture_area"), {
                scale: 2, // Higher quality
                useCORS: true,
                backgroundColor: null,
                logging: false,
                width: document.querySelector("#capture_area").offsetWidth,
                height: document.querySelector("#capture_area").offsetHeight
            }).then(canvas => {
                const link = document.createElement('a');
                link.download = 'ArewaSmart-GiftCard-{{ session('generated_code') }}.png';
                link.href = canvas.toDataURL("image/png");
                link.click();
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }
    </script>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const amountInput = document.getElementById('amount_input');
            const titleInput = document.getElementById('title_input');
            const messageInput = document.getElementById('message_input');
            const amountChips = document.querySelectorAll('.amount-chip');
            const themeSwatches = document.querySelectorAll('.theme-swatch');
            const themeTabs = document.querySelectorAll('.theme-tab');
            const titleColorInput = document.getElementById('title_color_input');
            const textColorInput = document.getElementById('text_color_input');
            const styleInput = document.getElementById('style_input');
            
            const pAmount = document.getElementById('preview_amount');
            const pTitle = document.getElementById('preview_title');
            const pMessage = document.getElementById('preview_message');
            const pCard = document.getElementById('gift_card_preview');
            const totalText = document.getElementById('total_deduction');
            const fee = parseFloat("{{ $creationFee }}");

            function updatePreview() {
                const amount = parseFloat(amountInput.value) || 0;
                pAmount.textContent = amount.toLocaleString();
                pTitle.textContent = titleInput.value || 'Happy Birthday!';
                pMessage.textContent = messageInput.value || 'Your message here...';
                pTitle.style.color = titleColorInput.value;
                pMessage.style.color = textColorInput.value;
                
                totalText.textContent = '₦' + (amount + fee).toLocaleString(undefined, {minimumFractionDigits: 2});
                
                // Birthday balloon logic
                const isBirthday = styleInput.value === 'birthday';
                document.querySelectorAll('.theme-birthday-only').forEach(el => {
                    el.style.display = isBirthday ? 'block' : 'none';
                });
            }

            // Amount Selection
            amountChips.forEach(chip => {
                chip.addEventListener('click', () => {
                    amountChips.forEach(c => c.classList.remove('active'));
                    chip.classList.add('active');
                    amountInput.value = chip.dataset.amount;
                    updatePreview();
                });
            });

            amountInput.addEventListener('input', () => {
                amountChips.forEach(c => c.classList.remove('active'));
                updatePreview();
            });

            // Theme Category Filtering
            themeTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    themeTabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    const cat = tab.dataset.cat;
                    themeSwatches.forEach(sw => {
                        sw.style.display = (cat === 'all' || sw.dataset.cat === cat) ? 'flex' : 'none';
                    });
                });
            });

            // Theme Selection
            themeSwatches.forEach(swatch => {
                swatch.addEventListener('click', () => {
                    themeSwatches.forEach(s => s.classList.remove('selected'));
                    swatch.classList.add('selected');
                    styleInput.value = swatch.dataset.theme;
                    
                    // Update preview card gradient
                    const gradient = getComputedStyle(swatch).backgroundImage;
                    pCard.style.background = gradient;
                    updatePreview();
                });
            });

            titleInput.addEventListener('input', updatePreview);
            messageInput.addEventListener('input', updatePreview);
            titleColorInput.addEventListener('input', updatePreview);
            textColorInput.addEventListener('input', updatePreview);

            // Submission with PIN Modal
            const generateForm = document.getElementById('generateForm');
            generateForm.addEventListener('submit', (e) => {
                e.preventDefault();
                document.getElementById('pin_modal_amount').textContent = (parseFloat(amountInput.value) + fee).toLocaleString();
                const pinModal = new bootstrap.Modal(document.getElementById('pinModal'));
                pinModal.show();
            });

            document.getElementById('submit_form_btn').addEventListener('click', function() {
                const pin = document.getElementById('pin_entry').value;
                if(pin.length < 4) return Swal.fire('Error', 'Please enter a valid PIN', 'error');
                
                const btn = this;
                const originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing...';
                
                document.getElementById('pinConfirmation').value = pin;
                generateForm.submit();
            });

            // Initial Category Filter (show active category by default)
            const activeCat = document.querySelector('.theme-tab.active').dataset.cat;
            themeSwatches.forEach(sw => {
                sw.style.display = (activeCat === 'all' || sw.dataset.cat === activeCat) ? 'flex' : 'none';
            });

            updatePreview();
        });
    </script>
</x-app-layout>