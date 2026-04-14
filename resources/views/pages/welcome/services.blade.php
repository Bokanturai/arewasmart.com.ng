<section class="services-section" id="services">
    <div class="services-container">
        <div class="services-header">
            <span class="sub-heading">Smart Solutions</span>
            <h2>Our AI-Enhanced Services</h2>
            <p>Experience ultra-fast, automated digital solutions powered by advanced AI for seamless daily operations.</p>
        </div>
        
        <div class="services-slider">
            <div class="services-track" id="servicesTrack">
                
                <!-- Service Slide 1 -->
                <div class="service-slide active">
                    <div class="service-info">
                        <div class="service-icon"><i class="fa-solid fa-microchip"></i></div>
                        <h3>Smart Identity Management</h3>
                        <p>Perform precise BVN validations, comprehensive searches, and verify details instantly with our AI-driven verification engine. Maintain complete control of your identity securely.</p>
                        <ul class="service-features">
                            <li><i class="fa-solid fa-check text-primary"></i> AI-Powered BVN Search</li>
                            <li><i class="fa-solid fa-check text-primary"></i> Instant NIN Verification</li>
                            <li><i class="fa-solid fa-check text-primary"></i> Real-time Identity Logs</li>
                            <li><i class="fa-solid fa-check text-primary"></i> Automated Validation</li>
                        </ul>
                    </div>
                    <div class="service-image">
                        <img src="{{ asset('assets/img/landing/004.png') }}" alt="Identity Management">
                        <div class="image-backdrop"></div>
                    </div>
                </div>

                <!-- Service Slide 2 -->
                <div class="service-slide">
                    <div class="service-info">
                        <div class="service-icon"><i class="fa-solid fa-bolt-lightning"></i></div>
                        <h3>Automated Utility Payments</h3>
                        <p>Pay your electricity bills safely and securely with zero downtime. We support all major distribution companies with instant, automated token generation.</p>
                        <ul class="service-features">
                            <li><i class="fa-solid fa-check text-primary"></i> Instant Token Delivery</li>
                            <li><i class="fa-solid fa-check text-primary"></i> Zero Transaction Charges</li>
                            <li><i class="fa-solid fa-check text-primary"></i> 24/7 Automated Processing</li>
                        </ul>
                    </div>
                    <div class="service-image">
                        <img src="{{ asset('assets/img/landing/001.png') }}" alt="Utility Bill Payments">
                        <div class="image-backdrop"></div>
                    </div>
                </div>

                <!-- Service Slide 3 -->
                <div class="service-slide">
                    <div class="service-info">
                        <div class="service-icon"><i class="fa-solid fa-robot"></i></div>
                        <h3>AI Data & Airtime Top-up</h3>
                        <p>Stay connected with our smart data bundles and airtime top-ups. Our system optimizes network selection for the fastest delivery (MTN, Airtel, Glo, 9mobile).</p>
                        <ul class="service-features">
                            <li><i class="fa-solid fa-check text-primary"></i> Multi-Network Support</li>
                            <li><i class="fa-solid fa-check text-primary"></i> Ultra-Fast API Delivery</li>
                            <li><i class="fa-solid fa-check text-primary"></i> Automated Low-Balance Alerts</li>
                        </ul>
                    </div>
                    <div class="service-image">
                        <img src="{{ asset('assets/img/landing/001.png') }}" alt="Data and Airtime">
                        <div class="image-backdrop"></div>
                    </div>
                </div>
                
                <!-- Service Slide 4 -->
                <div class="service-slide">
                    <div class="service-info">
                        <div class="service-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
                        <h3>Spry Gift Cards & AI Bonuses</h3>
                        <p>Bring the ultimate vibe to your African weddings and occasions! 🎉 Our smart Spry Gift Card lets you spray money effortlessly and with unmatched class.</p>
                        <ul class="service-features">
                            <li><i class="fa-solid fa-check text-primary"></i> Automated Gift Generation</li>
                            <li><i class="fa-solid fa-check text-primary"></i> Smart Referral Rewards</li>
                            <li><i class="fa-solid fa-check text-primary"></i> Instant Cashback Bonuses</li>
                        </ul>
                    </div>
                    <div class="service-image">
                        <img src="{{ asset('assets/img/landing/002.png') }}" alt="Spry Gift Cards">
                        <div class="image-backdrop"></div>
                    </div>
                </div>

                    <!-- Service Slide 5 -->
                <div class="service-slide">
                    <div class="service-info">
                        <div class="service-icon"><i class="fa-solid fa-user-gear"></i></div>
                        <h3>Automated Modifications</h3>
                        <p>Update your identity records seamlessly. Whether you need a name correction or DOB update, our automated system processes your modifications efficiently and securely.</p>
                        <ul class="service-features">
                            <li><i class="fa-solid fa-check text-primary"></i> Smart Name Correction</li>
                            <li><i class="fa-solid fa-check text-primary"></i> Automated DOB Updates</li>
                            <li><i class="fa-solid fa-check text-primary"></i> Secure Identity Syncing</li>
                        </ul>
                    </div>
                    <div class="service-image">
                        <img src="{{ asset('assets/img/landing/003.png') }}" alt="BVN and NIN Modifications">
                        <div class="image-backdrop"></div>
                    </div>
                </div>

                    <!-- Service Slide 6 -->
                <div class="service-slide">
                    <div class="service-info">
                        <div class="service-icon"><i class="fa-solid fa-brain"></i></div>
                        <h3>Smart Educational Pins</h3>
                        <p>Unlock your academic future with instant access to educational scratch cards and e-pins. Get WAEC, NECO, JAMB, and NABTEB pins delivered by our AI engine.</p>
                        <ul class="service-features">
                            <li><i class="fa-solid fa-check text-primary"></i> Instant Pin Generation</li>
                            <li><i class="fa-solid fa-check text-primary"></i> Direct Result Access</li>
                            <li><i class="fa-solid fa-check text-primary"></i> Automated Receipting</li>
                        </ul>
                    </div>
                    <div class="service-image">
                        <img src="{{ asset('assets/img/landing/005.png') }}" alt="Educational Pins">
                        <div class="image-backdrop"></div>
                    </div>
                </div>

            </div>
            
            <!-- Controls -->
            <div class="slider-controls">
                <button class="slider-btn prev-btn" id="prevService"><i class="fa-solid fa-chevron-left"></i></button>
                <div class="slider-dots" id="serviceDots">
                    <!-- Dots will be generated by JS -->
                </div>
                <button class="slider-btn next-btn" id="nextService"><i class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>
    </div>
</section>
