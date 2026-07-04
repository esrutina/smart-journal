<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Journal — Your Safe Space to Write, Reflect & Grow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="landing.css">
</head>
<body class="landing-page">
    <!-- Navigation -->
    <nav class="landing-nav">
        <div class="nav-brand">
            <span class="brand-icon">📓</span>
            <span class="brand-text">Smart Journal</span>
        </div>
       <div class="nav-links">
    <a href="#about">About</a>
    <a href="#features">Features</a>
    <a href="#stories">Stories</a>
    <a href="#contact">Contact</a>
    <button class="theme-toggle-btn" id="themeToggle" onclick="toggleTheme()" title="Toggle Dark/Light Mode">🌙</button>
    <?php if (isLoggedIn()): ?>
        <a href="dashboard.php" class="btn btn-primary btn-sm">Dashboard</a>
    <?php else: ?>
        <button class="btn btn-primary btn-sm" onclick="app.openModal('loginModal')">Get Started</button>
    <?php endif; ?>
</div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge animate-fadeInDown">
                <span>✨ Trusted by 10,000+ writers</span>
            </div>
            <h1 class="hero-title animate-fadeInUp stagger-1">
                Your Thoughts<br>
                Deserve<br>
                <span class="text-gradient-secondary">a Safe Home</span>
            </h1>
            <p class="hero-desc animate-fadeInUp stagger-2">
                A private, beautiful space to capture your daily moments, track your mood, 
                and watch your personal growth unfold — one entry at a time.
            </p>
            <div class="hero-buttons animate-fadeInUp stagger-3">
                <button class="btn btn-primary btn-lg" onclick="app.openModal('loginModal')">
                    Start Writing Free →
                </button>
                <button class="btn btn-secondary btn-lg" onclick="document.getElementById('features').scrollIntoView({behavior: 'smooth'})">
                    See How It Works
                </button>
            </div>
            <div class="hero-rating animate-fadeInUp stagger-4">
                <span class="stars">⭐⭐⭐⭐⭐</span>
                <span>"The most calming journaling app I've ever used" — Sarah M.</span>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-card animate-float">
                <div class="hero-card-header">
                    <div class="hero-card-dots">
                        <span></span><span></span><span></span>
                    </div>
                    <div class="hero-card-mood">
                        <span>😌</span> Calm
                        <small>Mood tracked</small>
                    </div>
                </div>
                <div class="hero-card-content">
                    <div class="hero-card-tag">😊 Feeling grateful today...</div>
                    <div class="hero-card-lines">
                        <div></div><div></div><div></div><div style="width:60%"></div>
                    </div>
                    <div class="hero-card-photo">
                        <span>📷</span> Memory captured
                    </div>
                </div>
            </div>
            <div class="hero-streak animate-float" style="animation-delay: 0.5s;">
                <span class="streak-fire">🔥</span>
                <div>
                    <strong>12 Days</strong>
                    <small>Current Streak</small>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="section-header">
            <span class="badge badge-primary">Features</span>
            <h2>Everything You Need to<br><span class="text-gradient-secondary">Reflect & Grow</span></h2>
            <p>Designed with care for your mental wellness and personal journey</p>
        </div>
        <div class="features-grid">
            <div class="feature-card card animate-on-scroll">
                <div class="feature-icon" style="background: rgba(16, 185, 129, 0.15);">🔒</div>
                <h3>100% Private</h3>
                <p>Your entries are yours alone. End-to-end privacy with no data selling, ever.</p>
            </div>
            <div class="feature-card card animate-on-scroll">
                <div class="feature-icon" style="background: rgba(251, 191, 36, 0.15);">😊</div>
                <h3>Mood Tracking</h3>
                <p>Log your emotions with 8 mood types and intensity ratings. Watch patterns emerge.</p>
            </div>
            <div class="feature-card card animate-on-scroll">
                <div class="feature-icon" style="background: rgba(59, 130, 246, 0.15);">📷</div>
                <h3>Photo Memories</h3>
                <p>Attach photos to entries. Capture the moment, not just the words.</p>
            </div>
            <div class="feature-card card animate-on-scroll">
                <div class="feature-icon" style="background: rgba(139, 92, 246, 0.15);">📊</div>
                <h3>Insights & Analytics</h3>
                <p>Beautiful charts showing your mood trends, writing habits, and growth over time.</p>
            </div>
            <div class="feature-card card animate-on-scroll">
                <div class="feature-icon" style="background: rgba(245, 158, 11, 0.15);">🔥</div>
                <h3>Writing Streaks</h3>
                <p>Build healthy habits with daily streak tracking and gentle reminders.</p>
            </div>
            <div class="feature-card card animate-on-scroll">
                <div class="feature-icon" style="background: rgba(236, 72, 153, 0.15);">💾</div>
                <h3>Export Your Data</h3>
                <p>Download your entries anytime as JSON. Your data, your control.</p>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="about-content">
            <span class="badge badge-primary">Our Mission</span>
            <h2>We Believe Writing<br><span class="text-gradient-secondary">Heals & Transforms</span></h2>
            <p>Smart Journal was born from a simple belief: everyone deserves a safe, judgment-free space to process their thoughts and emotions.</p>
            <p>In a world of constant noise, we created a quiet corner where you can be completely yourself. No likes, no comments, no pressure — just you and your words.</p>
            <div class="stats-row">
                <div class="stat-item">
                    <span class="stat-number gradient-text">50K+</span>
                    <span class="stat-label">Entries Written</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number gradient-text">10K+</span>
                    <span class="stat-label">Happy Writers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number gradient-text">4.9★</span>
                    <span class="stat-label">Average Rating</span>
                </div>
            </div>
        </div>
        <div class="about-quote card">
            <blockquote>
                "This app helped me through my toughest year. Being able to see my mood improve over time gave me hope."
            </blockquote>
            <div class="quote-author">
                <div class="quote-avatar">JM</div>
                <div>
                    <strong>Jessica M.</strong>
                    <span>Writing since 2024</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Stories Section -->
    <section id="stories" class="stories">
        <div class="section-header">
            <span class="badge badge-primary">Stories</span>
            <h2>Lives Changed by<br><span class="text-gradient-secondary">Daily Reflection</span></h2>
        </div>
        <div class="stories-grid">
            <div class="story-card card">
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <p>"I've tried dozens of journal apps. This is the only one that feels like a warm hug. The mood tracking helped me realize I was actually getting happier."</p>
                <div class="story-author">
                    <div class="story-avatar" style="background: #3b82f6;">AK</div>
                    <div>
                        <strong>Alex K.</strong>
                        <span>Student, 234 day streak</span>
                    </div>
                </div>
            </div>
            <div class="story-card card">
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <p>"As someone with anxiety, having a private space to vent without judgment is priceless. The photo feature lets me capture little joys too."</p>
                <div class="story-author">
                    <div class="story-avatar" style="background: #ec4899;">MR</div>
                    <div>
                        <strong>Maria R.</strong>
                        <span>Designer, 156 day streak</span>
                    </div>
                </div>
            </div>
            <div class="story-card card">
                <div class="stars">⭐⭐⭐⭐⭐</div>
                <p>"The analytics are eye-opening. I discovered I write most when I'm calm, and that awareness changed how I handle stress."</p>
                <div class="story-author">
                    <div class="story-avatar" style="background: #10b981;">DT</div>
                    <div>
                        <strong>David T.</strong>
                        <span>Engineer, 89 day streak</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="contact-content">
            <span class="badge badge-primary">Get in Touch</span>
            <h2>We'd Love to<br><span class="text-gradient-secondary">Hear From You</span></h2>
            <p>Questions, feedback, or just want to say hi? We're here for you.</p>
            <div class="contact-info">
                <div class="contact-item">
                    <span>📧</span>
                    <div>
                        <strong>Email</strong>
                        <span>hello@smartjournal.app</span>
                    </div>
                </div>
                <div class="contact-item">
                    <span>💬</span>
                    <div>
                        <strong>Response Time</strong>
                        <span>Within 24 hours</span>
                    </div>
                </div>
                <div class="contact-item">
                    <span>👥</span>
                    <div>
                        <strong>Community</strong>
                        <span>Join 10,000+ writers</span>
                    </div>
                </div>
            </div>
        </div>
        <form class="contact-form card">
            <div class="form-group">
                <label class="form-label">Your Name</label>
                <input type="text" class="form-input" placeholder="John Doe" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" class="form-input" placeholder="john@example.com" required>
            </div>
            <div class="form-group">
                <label class="form-label">Message</label>
                <textarea class="form-textarea" placeholder="Tell us what's on your mind..." rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary w-full">Send Message</button>
        </form>
    </section>

    <!-- Footer -->
    <footer class="landing-footer">
        <div class="footer-brand">
            <span class="brand-icon">📓</span>
            <span class="brand-text">Smart Journal</span>
            <p>Your safe space to write, reflect, and grow.</p>
        </div>
        <div class="footer-links">
            <div>
                <h4>Product</h4>
                <a href="#features">Features</a>
                <a href="#about">About</a>
                <a href="#stories">Stories</a>
            </div>
            <div>
                <h4>Support</h4>
                <a href="#contact">Contact</a>
                <a href="#">Privacy</a>
                <a href="#">Terms</a>
            </div>
            <div>
                <h4>Connect</h4>
                <a href="#">Twitter</a>
                <a href="#">Instagram</a>
                <a href="#">Discord</a>
            </div>
        </div>
        <div class="footer-bottom">
            <span>Made with 💜 for mindful writers everywhere</span>
            <span>© 2026 Smart Journal</span>
        </div>
    </footer>

    <!-- Login Modal -->
    <div class="modal-overlay" id="loginModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Welcome Back</h2>
                <button class="modal-close" onclick="app.closeModal('loginModal')">×</button>
            </div>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Sign in to your Smart Journal</p>
            <form action="login.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Username or Email</label>
                    <input type="text" name="username" class="form-input" placeholder="Enter username or email" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Enter password" required>
                </div>
                <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
                    <label class="custom-checkbox">
                        <input type="checkbox" name="remember">
                        <span class="checkmark"></span>
                        <span style="color: var(--text-secondary); font-size: 0.9rem;">Remember me</span>
                    </label>
                    <a href="#" onclick="app.openModal('forgotModal'); app.closeModal('loginModal'); return false;" style="color: var(--primary-light); font-size: 0.9rem;">Forgot password?</a>
                </div>
                <button type="submit" class="btn btn-primary w-full" style="margin-bottom: 1rem;">Sign In</button>
                <p style="text-align: center; color: var(--text-secondary); font-size: 0.9rem;">
                    Don't have an account? 
                    <a href="#" onclick="app.openModal('registerModal'); app.closeModal('loginModal'); return false;" style="color: var(--primary-light);">Create one</a>
                </p>
            </form>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal-overlay" id="registerModal">
        <div class="modal">
            <div class="modal-header">
                <h2 class="modal-title">Create Account</h2>
                <button class="modal-close" onclick="app.closeModal('registerModal')">×</button>
            </div>
            <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Start your journaling journey</p>
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-input" placeholder="Choose a username" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" placeholder="your@email.com" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="full_name" class="form-input" placeholder="Your full name (optional)">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-input" placeholder="Create a password" required minlength="6">
                </div>
                <button type="submit" class="btn btn-primary w-full" style="margin-bottom: 1rem;">Create Account</button>
                <p style="text-align: center; color: var(--text-secondary); font-size: 0.9rem;">
                    Already have an account? 
                    <a href="#" onclick="app.openModal('loginModal'); app.closeModal('registerModal'); return false;" style="color: var(--primary-light);">Sign in</a>
                </p>
            </form>
        </div>
    </div>

    <script src="app.js"></script>
    <!-- Forgot Password Modal -->
<div class="modal-overlay" id="forgotModal">
    <div class="modal">
        <div class="modal-header">
            <h2 class="modal-title">Reset Password</h2>
            <button class="modal-close" onclick="app.closeModal('forgotModal')">×</button>
        </div>
        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Enter your email and we'll send you a reset link</p>
        <form action="forgot_password.php" method="POST">
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" placeholder="your@email.com" required>
            </div>
            <button type="submit" class="btn btn-primary w-full" style="margin-bottom: 1rem;">Send Reset Link</button>
            <p style="text-align: center; color: var(--text-secondary); font-size: 0.9rem;">
                Remember your password? 
                <a href="#" onclick="app.openModal('loginModal'); app.closeModal('forgotModal'); return false;" style="color: var(--primary-light);">Sign in</a>
            </p>
        </form>
    </div>
</div>
</body>
</html>