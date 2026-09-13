@extends('layouts.app')

@section('title', 'Welcome to CholoSave')

@section('content')
@auth
<div style="
    position:relative;
    background: linear-gradient(rgba(30,41,59,0.85), rgba(56,189,248,0.85)), url('https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=1200&h=800&fit=crop&crop=center') center/cover no-repeat;
    min-height: 100vh;
    padding-top: 5rem;
    overflow:hidden;
">
    <div style="position:absolute;top:10%;left:5%;background:rgba(255,255,255,0.12);backdrop-filter:blur(8px);border-radius:12px;padding:0.8rem 1.5rem;color:white;font-weight:600;z-index:1;">🎯 Goals</div>
    <div style="position:absolute;top:30%;right:10%;background:rgba(255,255,255,0.12);backdrop-filter:blur(8px);border-radius:12px;padding:0.8rem 1.5rem;color:white;font-weight:600;z-index:1;">💡 Smart Tips</div>
    <div style="position:absolute;bottom:20%;left:20%;background:rgba(255,255,255,0.12);backdrop-filter:blur(8px);border-radius:12px;padding:0.8rem 1.5rem;color:white;font-weight:600;z-index:1;">📈 Growth</div>
    <div class="welcome-container" style="position:relative;z-index:2;">
        <div class="welcome-content">
            <div class="welcome-text" style="text-align:center;margin-bottom:3rem;margin-top:7rem;">
                <h1 style="font-size:3rem;color:#fff;margin-bottom:1rem;text-shadow:0 4px 24px rgba(0,0,0,0.5);">
                    <span id="typewriter-welcome"></span>
                </h1>
                <p class="subtitle" style="font-size:1.5rem;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,0.4);">
                    Your financial journey continues here
                </p>
            </div>
            @php
                $campaigns = \App\Models\Campaign::active()->orderBy('created_at', 'desc')->get();
                $blueGradient = 'linear-gradient(135deg, #38bdf8 0%, #0ea5e9 100%)';
            @endphp
            @if($campaigns->count())
            <div class="campaigns-list" style="max-width:1200px;margin:10rem auto 0;">
                <h2 style="font-size:1.3rem;font-weight:700;color:#e11d48;margin-bottom:1.2rem;text-align:center;">Active Campaigns</h2>
                @foreach($campaigns as $campaign)
                <div class="campaign-highlight" style="
                    background: {{ $blueGradient }};
                    box-shadow: 0 4px 24px rgba(56,189,248,0.10);
                    border-radius: 18px;
                    padding: 1.5rem;
                    margin-bottom: 1rem;
                    display: flex;
                    align-items: center;
                    gap: 1.5rem;
                    flex-wrap: nowrap;
                    color: #fff;
                    border: 1px solid rgba(56,189,248,0.18);
                ">
                    <div style="min-width:200px;">
                        <h3 style="font-size:1.1rem;font-weight:700;color:#fff;margin-bottom:0.25rem;text-shadow:0 2px 8px rgba(0,0,0,0.18);">{{ $campaign->title }}</h3>
                        <p style="color:#e0e7ef;font-size:0.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;">{{ $campaign->description }}</p>
                    </div>
                    <div style="flex:1;min-width:200px;">
                        <div style="background:rgba(255,255,255,0.18);border-radius:8px;overflow:hidden;height:12px;width:100%;">
                            @php
                                $progress = $campaign->goal_amount > 0 ? min(100, round(($campaign->current_amount / $campaign->goal_amount) * 100)) : 0;
                            @endphp
                            <div style="width:{{ $progress }}%;background:#fff;height:100%;transition:width 0.6s;"></div>
                        </div>
                        <div style="display:flex;justify-content:space-between;font-size:0.85rem;color:#f3f4f6;margin-top:0.2rem;">
                            <span>৳{{ number_format($campaign->current_amount,2) }}</span>
                            <span>{{ $progress }}%</span>
                            <span>Goal: ৳{{ number_format($campaign->goal_amount,2) }}</span>
                        </div>
                    </div>
                    <div style="font-size:0.9rem;color:#e0e7ef;white-space:nowrap;">
                        Deadline: {{ \Carbon\Carbon::parse($campaign->deadline)->format('M d, Y') }}
                    </div>
                    <div style="display:flex;gap:0.75rem;white-space:nowrap;">
                        <button class="btn btn-primary view-campaign-btn" data-campaign-id="{{ $campaign->id }}" style="background:rgba(255,255,255,0.92);color:#0ea5e9;padding:0.5rem 1rem;border-radius:8px;font-weight:600;cursor:pointer;border:none;font-size:0.9rem;">View</button>
                        <button class="btn btn-success contribute-campaign-btn" data-campaign-id="{{ $campaign->id }}" data-campaign-title="{{ $campaign->title }}" data-bkash="{{ $campaign->bKash }}" data-rocket="{{ $campaign->Rocket }}" data-nagad="{{ $campaign->Nagad }}" style="background:rgba(255,255,255,0.92);color:#059669;padding:0.5rem 1rem;border-radius:8px;font-weight:600;cursor:pointer;border:none;font-size:0.9rem;">Contribute</button>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <!-- Campaign Details Modal -->
    <div id="campaignModal" style="display:none;position:fixed;z-index:9999;top:0;left:0;width:100vw;height:100vh;background:rgba(30,41,59,0.45);align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:16px;max-width:500px;width:95vw;padding:2rem;position:relative;box-shadow:0 8px 32px rgba(30,41,59,0.18);">
            <button id="closeCampaignModal" style="position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.5rem;color:#64748b;cursor:pointer;">&times;</button>
            <h2 id="modalCampaignTitle" style="font-size:1.3rem;font-weight:700;color:#1E40AF;margin-bottom:0.5rem;"></h2>
            <p id="modalCampaignDesc" style="color:#64748b;margin-bottom:1.2rem;"></p>
            <h4 style="font-size:1.1rem;font-weight:600;margin-bottom:0.5rem;">Contributions</h4>
            <div id="modalContributionsTableWrapper">
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f1f5f9;">
                            <th style="padding:0.5rem 0.3rem;text-align:left;font-size:0.95rem;">Name</th>
                            <th style="padding:0.5rem 0.3rem;text-align:right;font-size:0.95rem;">Amount</th>
                            <th style="padding:0.5rem 0.3rem;text-align:right;font-size:0.95rem;">Date</th>
                        </tr>
                    </thead>
                    <tbody id="modalContributionsTable">
                        <tr><td colspan="3" style="text-align:center;color:#64748b;">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Campaign Contribution Modal (Bootstrap) -->
    <div class="modal fade" id="contributeModal" tabindex="-1" aria-labelledby="contributeModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="contributeModalLabel">Contribute to <span id="contributeCampaignTitle"></span></h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="contributeForm">
              <input type="hidden" id="contribute_campaign_id" name="campaign_id">
              <input type="hidden" id="contribute_payment_method" name="payment_method">
              <div class="mb-3">
                <label class="form-label">Contribution Amount</label>
                <input type="number" id="contribute_amount" name="amount" class="form-control" min="1" step="0.01" required>
                <div class="form-text">Enter the amount you want to contribute.</div>
              </div>
              <div class="mb-3">
                <label class="form-label">Select Payment Method</label>
                <div class="d-flex gap-3" id="contributePaymentMethods">
                  <!-- Payment method buttons will be injected here -->
                </div>
              </div>
              <div class="mb-3">
                <div class="alert alert-info" role="alert">
                  <strong>Contribution Amount:</strong> ৳<span id="contributeSummaryAmount">0.00</span>
                </div>
              </div>
              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-success">Contribute Now</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var welcomeText = 'Welcome back, {{ addslashes($user->name) }}!';
    var i = 0;
    var speed = 70; // typing speed in ms
    var target = document.getElementById('typewriter-welcome');
    function typeWriter() {
        if (i < welcomeText.length) {
            target.textContent += welcomeText.charAt(i);
            i++;
            setTimeout(typeWriter, speed);
        }
    }
    if(target) typeWriter();

    // Modal logic
    const modal = document.getElementById('campaignModal');
    const closeModalBtn = document.getElementById('closeCampaignModal');
    const modalTitle = document.getElementById('modalCampaignTitle');
    const modalDesc = document.getElementById('modalCampaignDesc');
    const modalTable = document.getElementById('modalContributionsTable');
    document.querySelectorAll('.view-campaign-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const campaignId = this.getAttribute('data-campaign-id');
            modal.style.display = 'flex';
            // Clear previous data
            modalTitle.textContent = '';
            modalDesc.textContent = '';
            modalTable.innerHTML = '<tr><td colspan="3" style="text-align:center;color:#64748b;">Loading...</td></tr>';
            // Fetch campaign details and contributions
            fetch(`/campaign/${campaignId}/contributions`)
                .then(res => res.json())
                .then(data => {
                    modalTitle.textContent = data.title;
                    modalDesc.textContent = data.description;
                    if(data.contributions.length > 0) {
                        modalTable.innerHTML = data.contributions.map(c =>
                            `<tr>
                                <td style='padding:0.4rem 0.3rem;'>${c.user_name}</td>
                                <td style='padding:0.4rem 0.3rem;text-align:right;'>৳${parseFloat(c.amount).toLocaleString(undefined, {minimumFractionDigits:2})}</td>
                                <td style='padding:0.4rem 0.3rem;text-align:right;'>${c.date}</td>
                            </tr>`
                        ).join('');
                    } else {
                        modalTable.innerHTML = `<tr><td colspan='3' style='text-align:center;color:#64748b;'>No contributions yet.</td></tr>`;
                    }
                })
                .catch(() => {
                    modalTable.innerHTML = `<tr><td colspan='3' style='text-align:center;color:#e11d48;'>Failed to load data.</td></tr>`;
                });
        });
    });
    closeModalBtn.addEventListener('click', function() {
        modal.style.display = 'none';
    });
    window.addEventListener('click', function(e) {
        if(e.target === modal) modal.style.display = 'none';
    });

    // Contribute modal logic (Bootstrap)
    let contributeModalInstance = new bootstrap.Modal(document.getElementById('contributeModal'));
    document.querySelectorAll('.contribute-campaign-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const campaignId = this.getAttribute('data-campaign-id');
            const campaignTitle = this.getAttribute('data-campaign-title');
            document.getElementById('contribute_campaign_id').value = campaignId;
            document.getElementById('contributeCampaignTitle').textContent = campaignTitle;
            document.getElementById('contribute_amount').value = '';
            document.getElementById('contributeSummaryAmount').textContent = '0.00';
            document.getElementById('contribute_payment_method').value = '';
            // Payment methods
            const paymentMethodsDiv = document.getElementById('contributePaymentMethods');
            paymentMethodsDiv.innerHTML = '';
            const bkash = this.getAttribute('data-bkash');
            const rocket = this.getAttribute('data-rocket');
            const nagad = this.getAttribute('data-nagad');
            if(bkash) paymentMethodsDiv.innerHTML += `<button type="button" data-method="bkash" class="btn btn-outline-secondary contribute-method-btn d-flex flex-column align-items-center"><img src='{{ asset('images/payment/bkash.png') }}' alt='bKash' style='height:32px;' class='mb-1'><span class='small'>bKash</span></button>`;
            if(rocket) paymentMethodsDiv.innerHTML += `<button type="button" data-method="Rocket" class="btn btn-outline-secondary contribute-method-btn d-flex flex-column align-items-center"><img src='{{ asset('images/payment/rocket.png') }}' alt='Rocket' style='height:32px;' class='mb-1'><span class='small'>Rocket</span></button>`;
            if(nagad) paymentMethodsDiv.innerHTML += `<button type="button" data-method="Nagad" class="btn btn-outline-secondary contribute-method-btn d-flex flex-column align-items-center"><img src='{{ asset('images/payment/nagad.png') }}' alt='Nagad' style='height:32px;' class='mb-1'><span class='small'>Nagad</span></button>`;
            document.querySelectorAll('.contribute-method-btn').forEach(btn => btn.classList.remove('active'));
            contributeModalInstance.show();
        });
    });
    // Payment method selection
    document.addEventListener('click', function(e) {
        if(e.target.closest('.contribute-method-btn')) {
            document.querySelectorAll('.contribute-method-btn').forEach(b => b.classList.remove('active', 'btn-success'));
            const btn = e.target.closest('.contribute-method-btn');
            btn.classList.add('active', 'btn-success');
            document.getElementById('contribute_payment_method').value = btn.getAttribute('data-method');
        }
    });
    // Update summary amount on input
    document.getElementById('contribute_amount').addEventListener('input', function() {
        document.getElementById('contributeSummaryAmount').textContent = parseFloat(this.value || 0).toLocaleString('en-BD', {minimumFractionDigits: 2});
    });
    // Handle form submit
    document.getElementById('contributeForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const campaignId = document.getElementById('contribute_campaign_id').value;
        const paymentMethod = document.getElementById('contribute_payment_method').value;
        const amount = document.getElementById('contribute_amount').value;
        if (!paymentMethod) {
            Swal.fire({ icon: 'warning', title: 'Select Payment Method', text: 'Please select a payment method before proceeding.' });
            return;
        }
        if (!amount || parseFloat(amount) < 1) {
            Swal.fire({ icon: 'warning', title: 'Invalid Amount', text: 'Please enter a valid contribution amount.' });
            return;
        }
        fetch(`/campaign/${campaignId}/contribute`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                amount: amount,
                payment_method: paymentMethod
            })
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Thank you!',
                    html: 'Your contribution was successful.<br><a href="/contribution/' + data.contribution_id + '/receipt" class="btn btn-success mt-3" target="_blank" download>Download Receipt</a>',
                    showConfirmButton: true,
                    confirmButtonText: 'Close',
                    allowOutsideClick: false
                }).then(() => { window.location.reload(); });
            } else {
                Swal.fire({ icon: 'error', title: 'Contribution Failed', text: data.message || 'Unknown error occurred.' });
            }
        })
        .catch(err => {
            Swal.fire({ icon: 'error', title: 'Contribution Failed', text: err.message });
        });
    });
});
</script>
@else
<div class="welcome-guest-container" style="width:100vw;max-width:100vw;margin:0;padding:0;">
    <!-- Carousel Hero Section -->
    <section class="carousel-hero" style="width:100vw;max-width:100vw;margin:0 auto;padding:0;">
        <div class="carousel-particles"></div>
        <div class="carousel-container" id="carouselContainer" style="width:100vw;max-width:100vw;margin:0;padding:0;">
            <!-- Slide 1: Welcome & Overview -->
            <div class="carousel-slide slide-1">
                <div class="slide-content">
                    <div class="slide-text">
                        <h1 class="slide-title">Welcome to CholoSave</h1>
                        <p class="slide-subtitle">Your journey to financial freedom starts here. Transform your relationship with money and build the future you deserve.</p>
                        <ul class="slide-features">
                            <li>🚀 Start your financial journey today</li>
                            <li>💰 Smart savings with AI-driven insights</li>
                            <li>📈 Investment opportunities for everyone</li>
                        </ul>
                        <div class="slide-cta">
                            <a href="{{ route('register') }}" class="btn-primary">Get Started Free</a>
                            <a href="{{ route('login') }}" class="btn-secondary">Sign In</a>
                        </div>
                    </div>
                    <div class="slide-visual">
                        <div class="floating-element element-1">💰 Save Smart</div>
                        <div class="floating-element element-2">📊 Track Progress</div>
                        <div class="floating-element element-3">🎯 Reach Goals</div>
                        <img src="https://images.unsplash.com/photo-1579621970563-ebec7560ff3e?w=500&h=300&fit=crop&crop=center" alt="Financial Success" class="slide-image">
                    </div>
                </div>
            </div>
            <!-- Slide 2: Smart Savings -->
            <div class="carousel-slide slide-2">
                <div class="slide-content">
                    <div class="slide-text">
                        <h1 class="slide-title">Smart Savings</h1>
                        <p class="slide-subtitle">Automate your savings with intelligent algorithms that adapt to your lifestyle and help you save more without thinking about it.</p>
                        <ul class="slide-features">
                            <li>🤖 AI-powered saving recommendations</li>
                            <li>🔄 Automatic round-up savings</li>
                            <li>📅 Goal-based saving plans</li>
                        </ul>
                        <div class="slide-cta">
                            <a href="{{ route('register') }}" class="btn-primary">Start Saving</a>
                            <a href="#learn-more" class="btn-secondary">Learn More</a>
                        </div>
                    </div>
                    <div class="slide-visual">
                        <div class="floating-element element-1">🎯 Goals</div>
                        <div class="floating-element element-2">💡 Smart Tips</div>
                        <div class="floating-element element-3">📈 Growth</div>
                        <img src="https://images.unsplash.com/photo-1554224155-6726b3ff858f?w=500&h=300&fit=crop&crop=center" alt="Smart Savings" class="slide-image">
                    </div>
                </div>
            </div>
            <!-- Slide 3: Investment Tools -->
            <div class="carousel-slide slide-3">
                <div class="slide-content">
                    <div class="slide-text">
                        <h1 class="slide-title">Investment Made Easy</h1>
                        <p class="slide-subtitle">Grow your wealth with beginner-friendly investment tools and expert guidance. Start investing with as little as $1.</p>
                        <ul class="slide-features">
                            <li>📊 Diversified portfolio options</li>
                            <li>🎓 Educational investment resources</li>
                            <li>💹 Real-time market insights</li>
                        </ul>
                        <div class="slide-cta">
                            <a href="{{ route('register') }}" class="btn-primary">Start Investing</a>
                            <a href="#portfolio" class="btn-secondary">View Portfolios</a>
                        </div>
                    </div>
                    <div class="slide-visual">
                        <div class="floating-element element-1">📈 Invest</div>
                        <div class="floating-element element-2">🔍 Research</div>
                        <div class="floating-element element-3">💰 Profit</div>
                        <img src="https://images.unsplash.com/photo-1611974789855-9c2a0a7236a3?w=500&h=300&fit=crop&crop=center" alt="Investment Dashboard" class="slide-image">
                    </div>
                </div>
            </div>
            <!-- Slide 4: Community & Support -->
            <div class="carousel-slide slide-4">
                <div class="slide-content">
                    <div class="slide-text">
                        <h1 class="slide-title">Join Our Community</h1>
                        <p class="slide-subtitle">Connect with like-minded individuals on their financial journey. Share experiences, get support, and celebrate milestones together.</p>
                        <ul class="slide-features">
                            <li>🤝 Supportive community network</li>
                            <li>🎓 Financial education workshops</li>
                            <li>🏆 Achievement celebrations</li>
                        </ul>
                        <div class="slide-cta">
                            <a href="{{ route('register') }}" class="btn-primary">Join Community</a>
                            <a href="#success-stories" class="btn-secondary">Success Stories</a>
                        </div>
                    </div>
                    <div class="slide-visual">
                        <div class="floating-element element-1">🤝 Connect</div>
                        <div class="floating-element element-2">🎯 Achieve</div>
                        <div class="floating-element element-3">🏆 Celebrate</div>
                        <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=500&h=300&fit=crop&crop=center" alt="Community Success" class="slide-image">
                    </div>
                </div>
            </div>
        </div>
        <!-- Navigation -->
        <div class="carousel-nav" id="carouselNav">
            <div class="nav-dot active" data-slide="0"></div>
            <div class="nav-dot" data-slide="1"></div>
            <div class="nav-dot" data-slide="2"></div>
            <div class="nav-dot" data-slide="3"></div>
        </div>
        <!-- Arrow Navigation -->
        <div class="carousel-arrow arrow-left" id="prevSlide">‹</div>
        <div class="carousel-arrow arrow-right" id="nextSlide">›</div>
        <!-- Progress Bar -->
        <div class="carousel-progress">
            <div class="progress-fill" id="progressFill"></div>
        </div>
    </section>
    <style>
    /* Carousel CSS from user HTML */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Inter', 'Segoe UI', Arial, sans-serif; background: #ffffff; color: #1e293b; overflow-x: hidden; }
    .carousel-hero { position: relative; height: 100vh; overflow: hidden; background: linear-gradient(135deg, #1e293b 0%, #3b82f6 100%); }
    .carousel-container { position: relative; width: 100%; height: 100%; display: flex; transition: transform 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94); }
    .carousel-slide { min-width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; position: relative; padding: 0 2rem; }
    .slide-1 { background: linear-gradient(135deg, #1e293b 0%, #3b82f6 100%); }
    .slide-2 { background: linear-gradient(135deg, #0f766e 0%, #38bdf8 100%); }
    .slide-3 { background: linear-gradient(135deg, #fbbf24 0%, #fde68a 100%); }
    .slide-4 { background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%); }
    .slide-content { max-width: 1200px; width: 100%; display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; opacity: 0; transform: translateY(50px); animation: slideIn 1s ease-out 0.5s forwards; }
    .slide-text { color: white; z-index: 2; }
    .slide-title { font-size: 4rem; font-weight: 800; line-height: 1.1; margin-bottom: 1.5rem; text-shadow: 0 4px 24px rgba(0, 0, 0, 0.2); }
    .slide-subtitle { font-size: 1.3rem; margin-bottom: 2.5rem; opacity: 0.95; line-height: 1.6; }
    .slide-features { list-style: none; margin-bottom: 2rem; }
    .slide-features li { display: flex; align-items: center; margin-bottom: 1rem; font-size: 1.1rem; opacity: 0; animation: fadeInUp 0.6s ease-out forwards; }
    .slide-features li:nth-child(1) { animation-delay: 0.8s; }
    .slide-features li:nth-child(2) { animation-delay: 1s; }
    .slide-features li:nth-child(3) { animation-delay: 1.2s; }
    .slide-features li::before { content: '✓'; background: rgba(255, 255, 255, 0.2); border-radius: 50%; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-weight: bold; }
    .slide-visual { position: relative; display: flex; align-items: center; justify-content: center; }
    .slide-image { width: 100%; max-width: 400px; height: 300px; object-fit: cover; border-radius: 20px; box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3); transform: scale(0.8); animation: scaleIn 1s ease-out 0.7s forwards; }
    .floating-element { position: absolute; background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); padding: 0.8rem 1.2rem; border-radius: 12px; color: white; font-weight: 600; font-size: 0.9rem; animation: float 4s ease-in-out infinite; z-index: 3; }
    .element-1 { top: 20%; left: 10%; animation-delay: 0s; }
    .element-2 { top: 30%; right: 15%; animation-delay: 1.5s; }
    .element-3 { bottom: 25%; left: 20%; animation-delay: 3s; }
    .carousel-nav { position: absolute; bottom: 30px; left: 50%; transform: translateX(-50%); display: flex; gap: 1rem; z-index: 10; }
    .nav-dot { width: 12px; height: 12px; border-radius: 50%; background: rgba(255, 255, 255, 0.4); cursor: pointer; transition: all 0.3s ease; position: relative; }
    .nav-dot.active { background: white; transform: scale(1.2); }
    .nav-dot::after { content: ''; position: absolute; top: -8px; left: -8px; right: -8px; bottom: -8px; border: 2px solid transparent; border-radius: 50%; transition: border-color 0.3s ease; }
    .nav-dot.active::after { border-color: rgba(255, 255, 255, 0.6); }
    .carousel-arrow { position: absolute; top: 50%; transform: translateY(-50%); background: rgba(255, 255, 255, 0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; cursor: pointer; transition: all 0.3s ease; z-index: 10; font-size: 1.2rem; }
    .carousel-arrow:hover { background: rgba(255, 255, 255, 0.2); transform: translateY(-50%) scale(1.1); }
    .arrow-left { left: 30px; }
    .arrow-right { right: 30px; }
    .slide-cta { display: flex; gap: 1.5rem; opacity: 0; animation: fadeInUp 0.8s ease-out 1.4s forwards; }
    .btn-primary, .btn-secondary { padding: 1rem 2rem; border-radius: 50px; font-weight: 600; text-decoration: none; transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); font-size: 1.1rem; position: relative; overflow: hidden; }
    .btn-primary { background: rgba(255, 255, 255, 0.95); color: #1e293b; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); }
    .btn-primary:hover { transform: translateY(-3px) scale(1.05); box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2); }
    .btn-secondary { background: transparent; color: white; border: 2px solid rgba(255, 255, 255, 0.3); }
    .btn-secondary:hover { background: rgba(255, 255, 255, 0.1); border-color: white; transform: translateY(-3px); }
    .carousel-progress { position: absolute; bottom: 0; left: 0; height: 4px; background: rgba(255, 255, 255, 0.3); width: 100%; z-index: 10; }
    .progress-fill { height: 100%; background: white; width: 0%; transition: width 0.1s linear; }
    .carousel-particles { position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 1px, transparent 1px), radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.05) 1px, transparent 1px), radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.08) 1px, transparent 1px); background-size: 100px 100px, 150px 150px, 200px 200px; animation: particleFloat 20s infinite linear; z-index: 1; }
    @keyframes slideIn { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes scaleIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    @keyframes float { 0%, 100% { transform: translateY(0px) rotate(0deg); } 25% { transform: translateY(-10px) rotate(2deg); } 50% { transform: translateY(-20px) rotate(0deg); } 75% { transform: translateY(-10px) rotate(-2deg); } }
    @keyframes particleFloat { 0% { transform: translateY(0px) rotate(0deg); } 100% { transform: translateY(-20px) rotate(360deg); } }
    @media (max-width: 1024px) { .slide-content { grid-template-columns: 1fr; gap: 3rem; text-align: center; } .slide-title { font-size: 3rem; } }
    @media (max-width: 768px) { .carousel-hero { height: 80vh; } .slide-title { font-size: 2.5rem; } .slide-subtitle { font-size: 1.1rem; } .slide-cta { flex-direction: column; align-items: center; gap: 1rem; } .btn-primary, .btn-secondary { width: 200px; text-align: center; } .carousel-arrow { width: 40px; height: 40px; font-size: 1rem; } .arrow-left { left: 15px; } .arrow-right { right: 15px; } .floating-element { display: none; } }
    @media (max-width: 480px) { .slide-title { font-size: 2rem; } .slide-content { padding: 0 1rem; } }
    </style>
    <script>
        class CarouselManager {
            constructor() {
                this.currentSlide = 0;
                this.totalSlides = 4;
                this.autoPlayInterval = 6000; // 6 seconds
                this.autoPlayTimer = null;
                this.isTransitioning = false;
                this.container = document.getElementById('carouselContainer');
                this.navDots = document.querySelectorAll('.nav-dot');
                this.prevBtn = document.getElementById('prevSlide');
                this.nextBtn = document.getElementById('nextSlide');
                this.progressFill = document.getElementById('progressFill');
                this.init();
            }
            init() {
                this.bindEvents();
                this.startAutoPlay();
                this.updateProgress();
            }
            bindEvents() {
                // Navigation dots
                this.navDots.forEach((dot, index) => {
                    dot.addEventListener('click', () => {
                        if (!this.isTransitioning) {
                            this.goToSlide(index);
                        }
                    });
                });
                // Arrow navigation
                this.prevBtn.addEventListener('click', () => {
                    if (!this.isTransitioning) {
                        this.prevSlide();
                    }
                });
                this.nextBtn.addEventListener('click', () => {
                    if (!this.isTransitioning) {
                        this.nextSlide();
                    }
                });
                // Keyboard navigation
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'ArrowLeft' && !this.isTransitioning) {
                        this.prevSlide();
                    } else if (e.key === 'ArrowRight' && !this.isTransitioning) {
                        this.nextSlide();
                    }
                });
                // Pause autoplay on hover
                this.container.addEventListener('mouseenter', () => {
                    this.stopAutoPlay();
                });
                this.container.addEventListener('mouseleave', () => {
                    this.startAutoPlay();
                });
                // Touch/swipe support
                let startX = 0;
                let endX = 0;
                this.container.addEventListener('touchstart', (e) => {
                    startX = e.touches[0].clientX;
                    this.stopAutoPlay();
                });
                this.container.addEventListener('touchend', (e) => {
                    endX = e.changedTouches[0].clientX;
                    const diff = startX - endX;
                    if (Math.abs(diff) > 50 && !this.isTransitioning) {
                        if (diff > 0) {
                            this.nextSlide();
                        } else {
                            this.prevSlide();
                        }
                    }
                    this.startAutoPlay();
                });
            }
            goToSlide(index) {
                if (index === this.currentSlide) return;
                this.isTransitioning = true;
                this.currentSlide = index;
                // Update container transform
                const translateX = -index * 100;
                this.container.style.transform = `translateX(${translateX}%)`;
                // Update navigation dots
                this.updateNavigation();
                // Reset transition flag after animation
                setTimeout(() => {
                    this.isTransitioning = false;
                }, 800);
                // Restart autoplay
                this.restartAutoPlay();
                this.updateProgress();
            }
            nextSlide() {
                const nextIndex = (this.currentSlide + 1) % this.totalSlides;
                this.goToSlide(nextIndex);
            }
            prevSlide() {
                const prevIndex = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
                this.goToSlide(prevIndex);
            }
            updateNavigation() {
                this.navDots.forEach((dot, index) => {
                    dot.classList.toggle('active', index === this.currentSlide);
                });
            }
            startAutoPlay() {
                this.stopAutoPlay();
                this.autoPlayTimer = setInterval(() => {
                    if (!this.isTransitioning) {
                        this.nextSlide();
                    }
                }, this.autoPlayInterval);
            }
            stopAutoPlay() {
                if (this.autoPlayTimer) {
                    clearInterval(this.autoPlayTimer);
                    this.autoPlayTimer = null;
                }
            }
            restartAutoPlay() {
                this.stopAutoPlay();
                this.startAutoPlay();
            }
            updateProgress() {
                let progress = 0;
                const progressInterval = setInterval(() => {
                    progress += (100 / (this.autoPlayInterval / 100));
                    this.progressFill.style.width = `${progress}%`;
                    if (progress >= 100) {
                        clearInterval(progressInterval);
                        this.progressFill.style.width = '0%';
                    }
                }, 100);
            }
        }
        // Initialize carousel when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new CarouselManager();
            // Add smooth scroll behavior for CTA links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });
        });
        // Add entrance animation when page loads
        window.addEventListener('load', () => {
            document.querySelector('.carousel-hero').style.opacity = '1';
        });
    </script>
    <!-- End Carousel Hero Section -->

    <!-- Features Section -->
    <section class="features">
        <div class="section-header">
            <h2>Why Choose CholoSave?</h2>
            <p>Discover the tools that will transform your financial future</p>
        </div>
        <div class="features-grid">
            <div class="feature-card" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-icon-wrapper">
                    <div class="feature-icon">💰</div>
                    <div class="icon-bg"></div>
                </div>
                <h3>Smart Savings</h3>
                <p>Automate your savings and reach your goals faster with AI-powered tailored plans.</p>
                <div class="feature-arrow">→</div>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-icon-wrapper">
                    <div class="feature-icon">📈</div>
                    <div class="icon-bg"></div>
                </div>
                <h3>Investment Tools</h3>
                <p>Grow your wealth with easy-to-use investment options and expert financial advice.</p>
                <div class="feature-arrow">→</div>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-icon-wrapper">
                    <div class="feature-icon">🎓</div>
                    <div class="icon-bg"></div>
                </div>
                <h3>Financial Education</h3>
                <p>Access premium resources and tips to make informed financial decisions.</p>
                <div class="feature-arrow">→</div>
            </div>
            <div class="feature-card" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-icon-wrapper">
                    <div class="feature-icon">🤝</div>
                    <div class="icon-bg"></div>
                </div>
                <h3>Community Support</h3>
                <p>Join a supportive community and achieve your financial dreams together.</p>
                <div class="feature-arrow">→</div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="how-it-works">
        <div class="section-header">
            <h2>How It Works</h2>
            <p>Start your financial journey in three simple steps</p>
        </div>
        <div class="steps-container">
            <div class="step" data-aos="zoom-in" data-aos-delay="100">
                <div class="step-number">01</div>
                <div class="step-content">
                    <div class="step-icon">🚀</div>
                    <h4>Sign Up</h4>
                    <p>Create your free account in seconds with just your email.</p>
                </div>
                <div class="step-connector"></div>
            </div>
            <div class="step" data-aos="zoom-in" data-aos-delay="200">
                <div class="step-number">02</div>
                <div class="step-content">
                    <div class="step-icon">🎯</div>
                    <h4>Set Your Goals</h4>
                    <p>Choose your savings or investment goals with our smart planner.</p>
                </div>
                <div class="step-connector"></div>
            </div>
            <div class="step" data-aos="zoom-in" data-aos-delay="300">
                <div class="step-number">03</div>
                <div class="step-content">
                    <div class="step-icon">📊</div>
                    <h4>Grow Together</h4>
                    <p>Track your progress and celebrate achievements with our community!</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
 

    <!-- Testimonials Section -->
  

    <!-- CTA Section -->
  

    <!-- Footer -->
    <footer style="background:#fff;color:#000;padding:1rem 0;text-align:center;width:100%;border-top:1px solid #e5e7eb;position:relative;bottom:0;left:0;">
        &copy; {{ date('Y') }} CholoSave. All rights reserved.
    </footer>
</div>

<style>
/* Reset and Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.welcome-guest-container {
    font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    background: #ffffff;
    color: #1e293b;
    overflow-x: hidden;
}

/* Hero Section */
.hero {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    overflow: hidden;
    opacity: 0;
    animation: heroFadeIn 1.2s ease-out 0.2s forwards;
}

@keyframes heroFadeIn {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}

.hero-bg-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.9) 0%, rgba(139, 92, 246, 0.9) 100%);
    z-index: 1;
}

.hero-particles {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: 
        radial-gradient(circle at 20% 80%, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
        radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 1px, transparent 1px),
        radial-gradient(circle at 40% 40%, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
    background-size: 100px 100px, 150px 150px, 200px 200px;
    animation: float 20s infinite linear;
    z-index: 1;
}

@keyframes float {
    0% { transform: translateY(0px) rotate(0deg); }
    100% { transform: translateY(-20px) rotate(360deg); }
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 2rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 4rem;
    align-items: center;
}

.hero-text {
    color: white;
}

.hero-title {
    font-size: 4rem;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 2rem;
    text-shadow: 0 4px 24px rgba(30, 41, 59, 0.18), 0 1.5px 0 #fff;
}

.title-line {
    display: block;
    animation: slideInLeft 1s ease-out;
}

.brand-name {
    display: block;
    background: linear-gradient(45deg, #fbbf24, #f59e0b);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
    animation: slideInRight 1s ease-out 0.3s both;
}

.hero-subtitle {
    font-size: 1.3rem;
    margin-bottom: 3rem;
    opacity: 0.95;
    line-height: 1.6;
    animation: fadeInUp 1s ease-out 0.6s both;
}

.cta-buttons {
    display: flex;
    gap: 1.5rem;
    animation: fadeInUp 1s ease-out 0.9s both;
}

.btn-primary, .btn-secondary {
    position: relative;
    padding: 1rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s;
    font-size: 1.1rem;
    overflow: hidden;
    border: none;
    cursor: pointer;
    box-shadow: 0 2px 16px rgba(251, 191, 36, 0.08);
}

.btn-primary {
    background: linear-gradient(45deg, #fbbf24, #f59e0b);
    color: #1e293b;
}

.btn-primary:hover {
    transform: translateY(-3px) scale(1.07);
    box-shadow: 0 8px 32px 0 rgba(251, 191, 36, 0.25), 0 2px 8px rgba(30, 64, 175, 0.10);
    filter: brightness(1.08);
}

.btn-glow {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s;
}

.btn-primary:hover .btn-glow {
    left: 100%;
}

.btn-secondary {
    background: transparent;
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: white;
    transform: translateY(-3px);
}

.hero-visual {
    position: relative;
    display: flex;
    justify-content: center;
    align-items: center;
}

.floating-cards {
    position: absolute;
    z-index: 1;
}

.card {
    position: absolute;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 1rem 1.5rem;
    border-radius: 15px;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
    animation: floatCards 6s ease-in-out infinite;
}

.card-1 {
    top: -20px;
    left: -40px;
    animation-delay: 0s;
}

.card-2 {
    top: 50px;
    right: -30px;
    animation-delay: 2s;
}

.card-3 {
    bottom: -10px;
    left: 20px;
    animation-delay: 4s;
}

@keyframes floatCards {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
}

.hero-image {
    width: 100%;
    max-width: 400px;
    border-radius: 20px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
    animation: heroImageFloat 1.2s ease-out;
    position: relative;
    z-index: 2;
}

.scroll-indicator {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 2;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.3rem;
}

.scroll-arrow {
    width: 30px;
    height: 30px;
    border: 2px solid white;
    border-top: none;
    border-left: none;
    transform: rotate(45deg);
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0) rotate(45deg); }
    40% { transform: translateY(-10px) rotate(45deg); }
    60% { transform: translateY(-5px) rotate(45deg); }
}

.scroll-text {
    color: #fff;
    font-size: 0.95rem;
    opacity: 0.7;
    letter-spacing: 1px;
    margin-top: 0.2rem;
    font-weight: 500;
}

/* Section Headers */
.section-header {
    text-align: center;
    margin-bottom: 4rem;
}

.section-header h2 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1rem;
}

.section-header p {
    font-size: 1.2rem;
    color: #64748b;
    max-width: 600px;
    margin: 0 auto;
}

/* Features Section */
.features {
    padding: 6rem 2rem;
    background: #f8fafc;
}

.features-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2.5rem;
    max-width: 1200px;
    margin: 0 auto;
}

.feature-card {
    background: white;
    border-radius: 20px;
    padding: 2.5rem 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(59, 130, 246, 0.1);
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(59, 130, 246, 0.15);
    border-color: rgba(59, 130, 246, 0.3);
}

.feature-icon-wrapper {
    position: relative;
    display: inline-block;
    margin-bottom: 1.5rem;
}

.feature-icon {
    font-size: 3rem;
    position: relative;
    z-index: 2;
}

.icon-bg {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 80px;
    height: 80px;
    background: linear-gradient(45deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1));
    border-radius: 50%;
    transition: all 0.3s ease;
}

.feature-card:hover .icon-bg {
    transform: translate(-50%, -50%) scale(1.2);
    background: linear-gradient(45deg, rgba(59, 130, 246, 0.2), rgba(139, 92, 246, 0.2));
}

.feature-card h3 {
    font-size: 1.4rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 1rem;
}

.feature-card p {
    color: #64748b;
    line-height: 1.6;
    margin-bottom: 1.5rem;
}

.feature-arrow {
    font-size: 1.5rem;
    color: #3b82f6;
    opacity: 0;
    transform: translateX(-10px);
    transition: all 0.3s ease;
}

.feature-card:hover .feature-arrow {
    opacity: 1;
    transform: translateX(0);
}

/* How It Works Section */
.how-it-works {
    padding: 6rem 2rem;
    background: white;
}

.steps-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 2rem;
    max-width: 1000px;
    margin: 0 auto;
    flex-wrap: wrap;
}

.step {
    position: relative;
    text-align: center;
    flex: 1;
    min-width: 250px;
}

.step-number {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 40px;
    height: 40px;
    background: linear-gradient(45deg, #3b82f6, #8b5cf6);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1.1rem;
    z-index: 2;
}

.step-content {
    background: white;
    border-radius: 20px;
    padding: 2.5rem 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    border: 2px solid #f1f5f9;
    transition: all 0.3s ease;
    position: relative;
}

.step:hover .step-content {
    border-color: #3b82f6;
    transform: translateY(-5px);
}

.step-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.step-content h4 {
    font-size: 1.3rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 1rem;
}

.step-content p {
    color: #64748b;
    line-height: 1.6;
}

.step-connector {
    position: absolute;
    top: 50%;
    right: -2rem;
    width: 4rem;
    height: 2px;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    transform: translateY(-50%);
}

.step:last-child .step-connector {
    display: none;
}

/* Stats Section */


/* Footer */
.landing-footer, .footer-content, .footer-brand, .footer-links, .footer-bottom {
    display: none !important;
}

/* Animation Keyframes */
@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes heroImageFloat {
    from {
        opacity: 0;
        transform: translateY(30px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Scroll Animations */
[data-aos] {
    opacity: 0;
    transition: all 0.8s ease;
}

[data-aos="fade-up"] {
    transform: translateY(30px);
}

[data-aos="fade-left"] {
    transform: translateX(30px);
}

[data-aos="zoom-in"] {
    transform: scale(0.9);
}

[data-aos].aos-animate {
    opacity: 1;
    transform: translateY(0) translateX(0) scale(1);
}

/* Responsive Design */
@media (max-width: 1024px) {
    .hero-content {
        grid-template-columns: 1fr;
        gap: 3rem;
        text-align: center;
    }
    
    .hero-title {
        font-size: 3rem;
    }
    
    .features-grid {
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
    }
    
    .steps-container {
        flex-direction: column;
        gap: 1rem;
    }
    
    .step-connector {
        display: none;
    }
}

@media (max-width: 768px) {
    .hero {
        min-height: 70vh;
        padding: 1.5rem 0;
    }
    
    .hero-title {
        font-size: 2.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.1rem;
    }
    
    .cta-buttons {
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }
    
    .btn-primary, .btn-secondary {
        width: 200px;
        text-align: center;
    }
    
    .section-header h2 {
        font-size: 2rem;
    }
    
    .features, .how-it-works, .testimonials {
        padding: 3rem 0.5rem;
    }
    
    .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 2rem;
    }
    
    .footer-links {
        justify-content: center;
    }
    
    .floating-cards {
        display: none;
    }
}

@media (max-width: 480px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .cta-content h2 {
        font-size: 2rem;
    }
    
    .feature-card, .testimonial-card {
        padding: 2rem 1.5rem;
    }
    
    .step-content {
        padding: 2rem 1.5rem;
    }
}

/* JavaScript Animation Trigger Styles */
.animate-counter {
    transition: all 2s ease-out;
}

.reveal {
    opacity: 1 !important;
    transform: translateY(0) !important;
}

/* Additional Interactive Elements */
.hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(ellipse at top, rgba(59, 130, 246, 0.1) 0%, transparent 70%),
        radial-gradient(ellipse at bottom, rgba(139, 92, 246, 0.1) 0%, transparent 70%);
    z-index: 0;
    animation: gradientShift 8s ease-in-out infinite alternate;
}

@keyframes gradientShift {
    0% {
        transform: scale(1) rotate(0deg);
    }
    100% {
        transform: scale(1.1) rotate(5deg);
    }
}

/* Loading Animation for Images */
.hero-image {
    position: relative;
    overflow: hidden;
}

.hero-image::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    animation: shimmer 2s infinite;
    z-index: 1;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Hover Effects for Better Interactivity */
.feature-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(59, 130, 246, 0.05), rgba(139, 92, 246, 0.05));
    opacity: 0;
    transition: opacity 0.3s ease;
    border-radius: 20px;
}

.feature-card:hover::before {
    opacity: 1;
}

.testimonial-card::after {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    bottom: -2px;
    background: linear-gradient(45deg, #3b82f6, #8b5cf6, #3b82f6);
    border-radius: 22px;
    z-index: -1;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.testimonial-card:hover::after {
    opacity: 1;
}
</style>

<script>
// Smooth scrolling and animations
document.addEventListener('DOMContentLoaded', function() {
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

    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('aos-animate');
                
                // Trigger counter animation when stats section is visible
                if (entry.target.classList.contains('stats')) {
                    animateCounters();
                }
            }
        });
    }, observerOptions);

    // Observe all elements with data-aos attribute
    document.querySelectorAll('[data-aos]').forEach(el => {
        observer.observe(el);
    });

    // Observe stats section
    const statsSection = document.querySelector('.stats');
    if (statsSection) {
        observer.observe(statsSection);
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Parallax effect for hero section
    let ticking = false;
    
    function updateParallax() {
        const scrolled = window.pageYOffset;
        const parallaxElements = document.querySelectorAll('.hero-particles, .hero-bg-overlay');
        
        parallaxElements.forEach(element => {
            const speed = element.classList.contains('hero-particles') ? 0.5 : 0.3;
            element.style.transform = `translateY(${scrolled * speed}px)`;
        });
        
        ticking = false;
    }

    function requestTick() {
        if (!ticking) {
            requestAnimationFrame(updateParallax);
            ticking = true;
        }
    }

    window.addEventListener('scroll', requestTick);

    // Add loading animation complete class
    window.addEventListener('load', function() {
        document.body.classList.add('loaded');
    });

    // Testimonial slider auto-rotation (optional)
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    let currentTestimonial = 0;

    function rotateTestimonials() {
        testimonialCards.forEach((card, index) => {
            card.classList.remove('active');
            if (index === currentTestimonial) {
                card.classList.add('active');
            }
        });
        
        currentTestimonial = (currentTestimonial + 1) % testimonialCards.length;
    }

    // Auto-rotate testimonials every 5 seconds
    setInterval(rotateTestimonials, 5000);
});
</script>
@endauth
@endsection