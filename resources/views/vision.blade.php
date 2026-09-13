@extends('layouts.app')

@section('title', 'Our Vision - CholoSave')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<div class="welcome-guest-container" style="width:100vw;max-width:100vw;margin:0;padding:0;">
    <!-- Hero Section -->
    <section class="carousel-hero" style="width:100vw;max-width:100vw;margin:0 auto;padding:0;">
        <div class="carousel-particles"></div>
        <div class="carousel-container" style="width:100vw;max-width:100vw;margin:0;padding:0;">
            <div class="carousel-slide slide-1" style="min-width:100%;height:100%;display:flex;align-items:center;justify-content:center;position:relative;padding:0 2rem;background:linear-gradient(135deg,#0f766e 0%,#38bdf8 100%);">
                <div class="slide-content" style="max-width:1200px;width:100%;display:grid;grid-template-columns:1fr 1fr;gap:4rem;align-items:center;">
                    <div class="slide-text">
                        <h1 class="slide-title">Our Vision</h1>
                        <p class="slide-subtitle">At CholoSave, our vision is to empower people to achieve financial independence through collaboration and smart investments. We believe in creating a platform that supports financial growth for everyone, regardless of background or financial knowledge.</p>
                        <ul class="slide-features">
                            <li>🌍 Empowering financial independence</li>
                            <li>🤝 Collaboration & community</li>
                            <li>💡 Smart, accessible tools for all</li>
                        </ul>
                        <div class="slide-cta">
                            <a href="{{ route('register') }}" class="btn-primary">Join CholoSave</a>
                            <a href="#vision-features" class="btn-secondary">Learn More</a>
                        </div>
                    </div>
                    <div class="slide-visual">
                        <div class="floating-element element-1">💰 Save Smart</div>
                        <div class="floating-element element-2">📊 Track Progress</div>
                        <div class="floating-element element-3">🎯 Reach Goals</div>
                        <img src="/images/vision/hero.jpg" alt="Financial Vision" class="slide-image">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features/Impact Section -->
    <section class="features" id="vision-features">
        <div class="section-header">
            <h2>How We Make an Impact</h2>
            <p>Discover the ways CholoSave transforms your financial journey</p>
        </div>
        <div class="features-grid">
            <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon-wrapper">
                    <div class="feature-icon">🤝</div>
                    <div class="icon-bg"></div>
                </div>
                <h3>Collaboration</h3>
                <p>By pooling resources and working together, we unlock greater investment opportunities and savings potential for everyone involved.</p>
                <div class="feature-arrow">→</div>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon-wrapper">
                    <div class="feature-icon">📈</div>
                    <div class="icon-bg"></div>
                </div>
                <h3>Smart Investment</h3>
                <p>We provide intelligent tools and guidance to ensure that your investments grow steadily, maximizing returns with minimal risk.</p>
                <div class="feature-arrow">→</div>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon-wrapper">
                    <div class="feature-icon">🔓</div>
                    <div class="icon-bg"></div>
                </div>
                <h3>Financial Freedom</h3>
                <p>Our goal is to help you gain financial freedom through consistent savings, smart investments, and the support of like-minded individuals.</p>
                <div class="feature-arrow">→</div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works">
        <div class="section-header">
            <h2>Our Goals</h2>
            <p>Start your journey to financial independence in three simple steps</p>
        </div>
        <div class="steps-container">
            <div class="step" data-aos="zoom-in" data-aos-delay="100">
                <div class="step-number">01</div>
                <div class="step-content">
                    <div class="step-icon">🛠️</div>
                    <h4>Accessible Tools</h4>
                    <p>Provide accessible financial tools for everyone.</p>
                </div>
                <div class="step-connector"></div>
            </div>
            <div class="step" data-aos="zoom-in" data-aos-delay="200">
                <div class="step-number">02</div>
                <div class="step-content">
                    <div class="step-icon">🐖</div>
                    <h4>Culture of Saving</h4>
                    <p>Encourage a culture of saving and smart investing.</p>
                </div>
                <div class="step-connector"></div>
            </div>
            <div class="step" data-aos="zoom-in" data-aos-delay="300">
                <div class="step-number">03</div>
                <div class="step-content">
                    <div class="step-icon">🚀</div>
                    <h4>Financial Independence</h4>
                    <p>Help members achieve long-term financial independence.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="features" style="background:linear-gradient(135deg,#1E40AF 0%,#3B82F6 100%);color:#fff;">
        <div class="section-header">
            <h2 style="color:#fff;">Our Growing Community</h2>
            <p style="color:#e0e7ef;">See how our community is making an impact</p>
        </div>
        <div class="features-grid" style="max-width:700px;">
            <div class="feature-card" style="background:rgba(255,255,255,0.12);color:#fff;">
                <div class="feature-icon-wrapper">
                    <div class="feature-icon">👥</div>
                    <div class="icon-bg"></div>
                </div>
                <h3>Total Users</h3>
                <div class="stat-number" data-count="{{ $userCount ?? 0 }}">0</div>
                <p>Active members in our community</p>
            </div>
            <div class="feature-card" style="background:rgba(255,255,255,0.12);color:#fff;">
                <div class="feature-icon-wrapper">
                    <div class="feature-icon">👥</div>
                    <div class="icon-bg"></div>
                </div>
                <h3>Active Groups</h3>
                <div class="stat-number" data-count="{{ $groupCount ?? 0 }}">0</div>
                <p>Collaborative saving groups</p>
            </div>
        </div>
        <div class="vision-cta" style="margin-top:2.5rem;">
            <a href="{{ route('register') }}" class="btn-primary">Join Our Community</a>
        </div>
    </section>

    <footer style="background:#fff;color:#000;padding:1rem 0;text-align:center;width:100%;border-top:1px solid #e5e7eb;position:relative;bottom:0;left:0;">
        &copy; {{ date('Y') }} CholoSave. All rights reserved.
    </footer>
</div>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
/* Copy all relevant CSS from welcome.blade.php here for pixel-perfect match */
{{ file_get_contents(resource_path('views/welcome.blade.php')) }}
</style>

<script>
// Copy all relevant JS for animation and counters from welcome.blade.php
// Carousel, AOS, and counter animation
// (You may want to extract the relevant JS from welcome.blade.php for a pixel-perfect match)
// Counter animation for stats
const counters = document.querySelectorAll('.stat-number');
const speed = 200;
const animateCounters = () => {
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-count'));
        const count = parseInt(counter.innerText);
        const inc = target / speed;
        if (count < target) {
            counter.innerText = Math.ceil(count + inc);
            setTimeout(animateCounters, 10);
        } else {
            counter.innerText = target;
        }
    });
};
const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('aos-animate');
            if (entry.target.classList.contains('stat-number')) {
                animateCounters();
            }
        }
    });
}, observerOptions);
document.querySelectorAll('.stat-number').forEach(el => observer.observe(el));
document.querySelectorAll('[data-aos]').forEach(el => observer.observe(el));
</script>
@endsection