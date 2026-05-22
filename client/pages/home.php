<!-- Home Page - MINSU Alumni Landing Page -->
<div class="landing-page">
  <!-- Navigation -->
  <nav class="landing-nav">
    <div class="container">
      <div class="nav-brand">
        <img
          src="assets/images/logo.svg"
          alt="MINSU"
          class="nav-logo"
          data-theme-logo="true"
          onerror="
            this.style.display = 'none';
            this.nextElementSibling.style.display = 'block';
          "
        />
        <span class="nav-logo-text" data-branding="short" style="display: none">MINSU</span>
        <div class="nav-brand-copy">
          <span class="nav-title" data-branding="site-name">Alumni Network</span>
          <span class="nav-subtitle" data-branding="institution">Mindoro State University</span>
        </div>
      </div>
      <div class="nav-links" aria-label="Main sections">
        <a href="#/features" class="nav-link">Features</a>
        <a href="#/about" class="nav-link">About</a>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="hero-section">
    <div class="hero-bg"></div>
    <div class="container">
      <div class="hero-content">
        <div class="hero-badge">🎓 MINSU Official Alumni Platform</div>
        <h1 class="hero-title">
          Stay Connected.<br />
          <span class="text-gradient">Grow Together.</span>
        </h1>
        <p class="hero-description">
          Join thousands of MINSU alumni. Network with fellow graduates, attend
          exclusive events, and be part of a thriving community that supports
          your personal and professional growth.
        </p>
        <div class="hero-cta">
          <a href="#/register" class="btn btn-primary btn-xl">
            <span>Join Now</span>
            <svg
              width="20"
              height="20"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path d="M5 12h14M12 5l7 7-7 7" />
            </svg>
          </a>
          <a href="#/login" class="btn btn-ghost btn-xl">Sign In</a>
        </div>
        <div class="hero-stats">
          <div class="stat">
            <span class="stat-number" id="heroStatOneValue">5,000+</span>
            <span class="stat-label" id="heroStatOneLabel">Alumni Members</span>
          </div>
          <div class="stat-divider"></div>
          <div class="stat">
            <span class="stat-number" id="heroStatTwoValue">50+</span>
            <span class="stat-label" id="heroStatTwoLabel"
              >Events Per Year</span
            >
          </div>
          <div class="stat-divider"></div>
          <div class="stat">
            <span class="stat-number" id="heroStatThreeValue">12</span>
            <span class="stat-label" id="heroStatThreeLabel">Colleges</span>
          </div>
        </div>
      </div>
      <div class="hero-visual">
        <div class="hero-card card-1">
          <div class="card-icon">👋</div>
          <div class="card-text">
            <strong id="heroCardUserTitle">Welcome Back!</strong>
            <span id="heroCardUserValue">Juan dela Cruz</span>
          </div>
        </div>
        <div class="hero-card card-2">
          <div class="card-icon">🏆</div>
          <div class="card-text">
            <strong id="heroCardPointsValue">+50 Points</strong>
            <span id="heroCardPointsLabel">Profile Completed</span>
          </div>
        </div>
        <div class="hero-card card-3">
          <div class="card-icon">📅</div>
          <div class="card-text">
            <strong id="heroCardEventTitle">Alumni Reunion 2026</strong>
            <span id="heroCardEventLabel">Coming Soon</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features-section" id="features">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">Features</span>
        <h2 class="section-title">Everything You Need to Stay Connected</h2>
        <p class="section-description">
          Our platform offers powerful tools to help you network, engage, and
          grow with your fellow alumni.
        </p>
        <div class="features-highlights" aria-label="Platform highlights">
          <div class="highlight-pill">
            <strong>Official</strong>
            <span>University-backed platform</span>
          </div>
          <div class="highlight-pill">
            <strong>Secure</strong>
            <span>Private and verified alumni network</span>
          </div>
          <div class="highlight-pill">
            <strong>Rewarding</strong>
            <span>Earn points as you participate</span>
          </div>
        </div>
      </div>

      <div class="features-grid">
        <div class="feature-item">
          <div class="feature-icon-wrapper">
            <span class="feature-icon">🎓</span>
          </div>
          <span class="feature-chip">Identity</span>
          <h3>Digital Alumni ID</h3>
          <p>
            Get your official digital alumni ID upon registration. Use it for
            event check-ins and alumni verification.
          </p>
        </div>

        <div class="feature-item">
          <div class="feature-icon-wrapper">
            <span class="feature-icon">📅</span>
          </div>
          <span class="feature-chip">Events</span>
          <h3>Exclusive Events</h3>
          <p>
            Access alumni-only events, reunions, career fairs, and networking
            opportunities. RSVP and earn points!
          </p>
        </div>

        <div class="feature-item">
          <div class="feature-icon-wrapper">
            <span class="feature-icon">💬</span>
          </div>
          <span class="feature-chip">Communication</span>
          <h3>Group Messaging</h3>
          <p>
            Connect with alumni from your section, program, or college. Send
            personal or group messages easily.
          </p>
        </div>

        <div class="feature-item">
          <div class="feature-icon-wrapper">
            <span class="feature-icon">🏆</span>
          </div>
          <span class="feature-chip">Engagement</span>
          <h3>Gamified Rewards</h3>
          <p>
            Earn points for attending events, completing your profile, and
            engaging with the community. Redeem for rewards!
          </p>
        </div>

        <div class="feature-item">
          <div class="feature-icon-wrapper">
            <span class="feature-icon">📊</span>
          </div>
          <span class="feature-chip">Profile</span>
          <h3>Profile & Portfolio</h3>
          <p>
            Showcase your career achievements, skills, and experiences. Let
            fellow alumni know what you're up to!
          </p>
        </div>

        <div class="feature-item">
          <div class="feature-icon-wrapper">
            <span class="feature-icon">📣</span>
          </div>
          <span class="feature-chip">Updates</span>
          <h3>Announcements</h3>
          <p>
            Stay updated with the latest news, job opportunities, and
            announcements from your alma mater.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- How It Works -->
  <section class="how-it-works-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">How It Works</span>
        <h2 class="section-title">Get Started in 3 Easy Steps</h2>
      </div>

      <div class="steps-grid">
        <div class="step-item">
          <div class="step-number">1</div>
          <h3>Create Account</h3>
          <p>
            Sign up with your email in seconds. We'll verify your alumni status
            automatically.
          </p>
        </div>
        <div class="step-connector"></div>
        <div class="step-item">
          <div class="step-number">2</div>
          <h3>Complete Profile</h3>
          <p>
            Fill in your academic and professional details. Earn 50 points for
            completing your profile!
          </p>
        </div>
        <div class="step-connector"></div>
        <div class="step-item">
          <div class="step-number">3</div>
          <h3>Start Connecting</h3>
          <p>
            Explore events, message fellow alumni, and engage with your
            community.
          </p>
        </div>
      </div>
    </div>
  </section>

  <!-- Announcements Section -->
  <section class="announcements-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">Stay Informed</span>
        <h2 class="section-title">Latest Announcements</h2>
        <p class="section-description">
          Stay up-to-date with the latest news, opportunities, and updates from your alma mater.
        </p>
      </div>

      <div class="announcements-grid" id="announcementsGrid">
        <!-- Announcements will be loaded here -->
        <div class="announcement-skeleton"></div>
        <div class="announcement-skeleton"></div>
        <div class="announcement-skeleton"></div>
      </div>

      <div class="text-center mt-lg">
        <a href="#/announcements" class="btn btn-primary">
          View All Announcements
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12h14M12 5l7 7-7 7" />
          </svg>
        </a>
      </div>
    </div>
  </section>

  <!-- Events Section -->
  <section class="events-section">
    <div class="container">
      <div class="section-header">
        <span class="section-badge">Get Involved</span>
        <h2 class="section-title">Upcoming Events</h2>
        <p class="section-description">
          Join us for exciting events, reunions, and networking opportunities throughout the year.
        </p>
      </div>

      <div class="events-grid" id="eventsGrid">
        <!-- Events will be loaded here -->
        <div class="event-skeleton"></div>
        <div class="event-skeleton"></div>
        <div class="event-skeleton"></div>
      </div>

      <div class="text-center mt-lg">
        <a href="#/events" class="btn btn-primary">
          View All Events
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M5 12h14M12 5l7 7-7 7" />
          </svg>
        </a>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section class="about-section" id="about">
    <div class="container">
      <div class="about-grid">
        <div class="about-content">
          <span class="section-badge">About</span>
          <h2 class="section-title">Building Bridges Between Generations</h2>
          <p>
            The MINSU Alumni Network is the official platform for graduates of
            Mindoro State University. Our mission is to foster lasting
            connections between alumni, provide valuable resources for
            professional development, and support the continued growth of our
            alma mater.
          </p>
          <p>
            Whether you graduated last year or decades ago, you're part of a
            community that spans generations and industries. Join us in building
            a stronger, more connected alumni network.
          </p>
          <div class="about-features">
            <div class="about-feature">
              <svg
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
              </svg>
              <span>Official University Platform</span>
            </div>
            <div class="about-feature">
              <svg
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
              </svg>
              <span>Secure & Private</span>
            </div>
            <div class="about-feature">
              <svg
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                <polyline points="22 4 12 14.01 9 11.01" />
              </svg>
              <span>Free for All Alumni</span>
            </div>
          </div>
        </div>
        <div class="about-image">
          <div class="image-placeholder">
            <span>🎓</span>
            <p>MINSU Alumni Community</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta-section">
    <div class="container">
      <div class="cta-content">
        <h2>Ready to Reconnect?</h2>
        <p>
          Join thousands of MINSU alumni already on the platform. It only takes
          a minute to get started.
        </p>
        <div class="cta-links" role="navigation" aria-label="Primary actions">
          <a href="#/register" class="cta-link">Join Now</a>
          <span class="cta-divider" aria-hidden="true">•</span>
          <a href="#/login" class="cta-link">Sign In</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="landing-footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-brand">
          <img
            src="/assets/images/logo.svg"
            alt="MINSU"
            class="footer-logo"
            onerror="
              this.style.display = 'none';
              this.nextElementSibling.style.display = 'block';
            "
          />
          <span class="footer-logo-text" style="display: none">MINSU</span>
          <p data-branding="footer"></p>
        </div>
        <div class="footer-links">
          <h4>Quick Links</h4>
          <a href="#/login">Sign In</a>
          <a href="#/register">Join Now</a>
          <a href="#/features">Features</a>
          <a href="#/about">About</a>
        </div>
        <div class="footer-links">
          <h4>Support</h4>
          <a href="#/contact">Contact Us</a>
          <a href="#/faq">FAQ</a>
          <a href="#/privacy">Privacy Policy</a>
          <a href="#/terms">Terms of Service</a>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2026 Mindoro State University. All rights reserved.</p>
      </div>
    </div>
  </footer>
</div>

<style>
  /* Landing Page Styles */
  .landing-page {
    --color-accent: var(--color-primary);
    --color-accent-soft: var(--primary-50);
    --color-accent-muted: var(--primary-100);
    --color-accent-strong: var(--primary-700);
    --color-accent-ink: var(--primary-900);
    --color-accent-glow: var(--auth-page-gradient-start);
    --landing-section-bg: var(--color-surface);
    --landing-section-muted: var(--color-surface-soft);
    --landing-panel-bg: var(--color-panel-bg);
    --landing-panel-border: var(--color-panel-border);
    --landing-hero-overlay-opacity: 0.9;
    --landing-hero-glow-opacity: 0;
    --landing-hero-text: #ffffff;
    --landing-hero-text-muted: rgb(255 255 255 / 0.9);
    --landing-hero-divider: rgb(255 255 255 / 0.34);
    --landing-copy-primary: rgb(248 250 252 / 0.98);
    --landing-copy-secondary: rgb(226 232 240 / 0.9);
    --landing-card-bg: rgb(2 6 23 / 0.58);
    --landing-card-bg-soft: rgb(15 23 42 / 0.42);
    --landing-feature-card-bg: var(--color-panel-bg);
    --landing-feature-card-bg-soft: var(--color-surface-soft);
    --landing-feature-border: var(--color-panel-border);
    --landing-nav-pill-bg: linear-gradient(
      120deg,
      rgb(255 255 255 / 0.74),
      rgb(255 255 255 / 0.54)
    );
    --landing-nav-pill-border: var(--primary-200);
    --landing-feature-aura-soft: var(--primary-50);
    --landing-feature-aura-strong: var(--primary-100);
    position: relative;
    min-height: 100vh;
    overflow: hidden;
    background-color: #021510;
    background-image: var(--landing-background-image);
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
  }

  .landing-page::before {
    content: "";
    display: none;
    position: absolute;
    inset: 0 0 auto 0;
    height: min(580px, 72vh);
    background:
      radial-gradient(
        circle at 12% -8%,
        rgb(16 185 129 / 0.22),
        transparent 42%
      ),
      radial-gradient(
        circle at 88% 10%,
        rgb(167 243 208 / 0.24),
        transparent 46%
      );
    opacity: 0.55;
    pointer-events: none;
    z-index: 0;
  }

  .landing-page::after {
    content: "";
    display: none;
    position: absolute;
    inset: 0 0 auto 0;
    height: min(580px, 72vh);
    background:
      linear-gradient(180deg, rgb(255 255 255 / 0.06), transparent 72%),
      repeating-linear-gradient(
        120deg,
        rgb(148 163 184 / 0.06) 0,
        rgb(148 163 184 / 0.06) 1px,
        transparent 1px,
        transparent 26px
      );
    opacity: 0.08;
    pointer-events: none;
    z-index: 0;
  }

  .landing-page > * {
    position: relative;
    z-index: 1;
  }

  /* Navigation */
  .landing-nav {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 100;
    overflow: hidden;
    padding: 0.62rem 0;
    background:
      linear-gradient(180deg, rgb(2 6 23 / 0.66) 0%, rgb(2 6 23 / 0.38) 100%),
      linear-gradient(120deg, rgb(16 185 129 / 0.2), transparent 52%);
    backdrop-filter: blur(16px) saturate(150%);
    border-bottom: 1px solid rgb(255 255 255 / 0.2);
    box-shadow:
      0 20px 34px -30px rgb(2 6 23 / 0.92),
      0 14px 24px -24px rgb(16 185 129 / 0.44);
  }

  .landing-nav::before {
    content: "";
    position: absolute;
    inset: 0;
    background: radial-gradient(
      circle at 12% -26%,
      rgb(167 243 208 / 0.36),
      transparent 44%
    );
    opacity: 0.42;
    pointer-events: none;
  }

  .landing-nav::after {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    height: 1.5px;
    background: linear-gradient(
      90deg,
      transparent 0%,
      var(--primary-300) 26%,
      var(--color-accent) 50%,
      var(--primary-300) 74%,
      transparent 100%
    );
    opacity: 0.9;
  }

  .landing-nav .container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: clamp(0.5rem, 1.6vw, var(--spacing-md));
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 clamp(0.9rem, 1.6vw, 1.3rem) 0 clamp(0.35rem, 1vw, 0.7rem);
    position: relative;
    z-index: 1;
  }

  .nav-brand {
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 0;
    max-width: min(74%, 860px);
    gap: 0.5rem;
    padding: 0;
  }

  .nav-brand-copy {
    display: flex;
    flex-direction: column;
    min-width: 0;
    gap: 0.06rem;
  }

  .nav-logo {
    width: 36px;
    height: 36px;
    object-fit: contain;
    border-radius: 50%;
    border: 1px solid rgb(167 243 208 / 0.45);
    background: rgb(2 6 23 / 0.24);
    box-shadow: 0 10px 18px -18px rgb(16 185 129 / 0.62);
  }

  .nav-logo-text {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--color-accent);
  }

  .nav-title {
    font-family: var(--font-family-heading);
    font-size: clamp(0.84rem, 1.45vw, 1.7rem);
    font-weight: 700;
    letter-spacing: 0.01em;
    line-height: 1.05;
    color: rgb(248 250 252 / 0.98);
    text-shadow: 0 8px 16px rgb(2 6 23 / 0.45);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .nav-subtitle {
    font-size: clamp(0.58rem, 0.95vw, 0.72rem);
    font-weight: 600;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    color: rgb(226 232 240 / 0.88);
    opacity: 0.96;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .nav-links {
    display: flex;
    align-items: center;
    flex-shrink: 0;
    gap: 0.35rem;
    padding: 0;
  }

  .nav-link {
    position: relative;
    color: rgb(248 250 252 / 0.92);
    font-weight: 600;
    opacity: 0.96;
    padding: 0.38rem 0.6rem;
    border-radius: var(--radius-sm);
    transition:
      color 0.2s,
      opacity 0.2s,
      background 0.2s,
      transform 0.2s;
  }

  .nav-link::after {
    content: "";
    position: absolute;
    left: 0.65rem;
    right: 0.65rem;
    bottom: 0.2rem;
    height: 2px;
    border-radius: var(--radius-full);
    background: linear-gradient(90deg, var(--primary-200), var(--primary-50));
    opacity: 0;
    transition: opacity 0.2s;
  }

  .nav-link:hover {
    color: rgb(248 250 252 / 1);
    background: rgb(16 185 129 / 0.16);
    transform: translateY(-1px);
  }

  .nav-link:hover::after {
    opacity: 1;
  }

  .nav-link:focus-visible {
    outline: 2px solid var(--color-focus-ring);
    outline-offset: 1px;
    background: rgb(16 185 129 / 0.2);
  }

  .nav-link:focus-visible::after {
    opacity: 1;
  }

  /* Hero Section */
  .hero-section {
    position: relative;
    isolation: isolate;
    min-height: calc(100vh - 24px);
    display: flex;
    align-items: center;
    padding: calc(72px + clamp(var(--spacing-xl), 5vw, var(--spacing-3xl))) 0
      clamp(var(--spacing-xl), 4vw, var(--spacing-3xl));
    overflow: visible;
  }

  .hero-bg {
    position: fixed;
    inset: 0;
    background: none;
    filter: none;
    z-index: -2;
    pointer-events: none;
  }

  .hero-bg::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(
      110deg,
      var(--auth-page-gradient-start) 0%,
      var(--auth-page-gradient-end) 100%
    );
    opacity: var(--landing-hero-overlay-opacity);
    pointer-events: none;
  }

  .hero-bg::after {
    display: none;
  }

  .hero-section::before {
    content: "";
    display: none;
    position: absolute;
    inset: 0;
    background:
      radial-gradient(
        circle at 18% 24%,
        rgb(110 231 183 / 0.14),
        transparent 44%
      ),
      linear-gradient(180deg, rgb(255 255 255 / 0), rgb(236 253 245 / 0.06));
    pointer-events: none;
    z-index: -1;
  }

  .hero-section .container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: clamp(var(--spacing-lg), 4vw, var(--spacing-2xl));
    align-items: center;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
  }

  .hero-content {
    max-width: min(640px, 100%);
  }

  .hero-badge {
    display: inline-block;
    padding: 8px 16px;
    background: rgb(255 255 255 / 0.88);
    color: var(--color-accent-strong);
    border: 1px solid rgb(255 255 255 / 0.68);
    border-radius: 999px;
    font-size: 0.74rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    margin-bottom: var(--spacing-md);
  }

  .hero-title {
    font-family: var(--font-family-heading);
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 800;
    line-height: 1.02;
    letter-spacing: -0.03em;
    color: var(--landing-hero-text);
    text-shadow: 0 12px 28px rgb(2 6 23 / 0.35);
    margin-bottom: var(--spacing-md);
  }

  .text-gradient {
    background: linear-gradient(
      135deg,
      var(--color-accent-strong),
      var(--color-accent)
    );
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .hero-title .text-gradient {
    color: #ffffff;
    background: linear-gradient(
      120deg,
      #ffffff 0%,
      var(--primary-50) 54%,
      var(--primary-100) 100%
    );
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 10px 24px rgb(2 6 23 / 0.3);
  }

  .hero-description {
    font-size: clamp(0.98rem, 2vw, 1.2rem);
    color: var(--landing-hero-text-muted);
    line-height: 1.76;
    margin-bottom: var(--spacing-lg);
    max-width: 58ch;
  }

  .hero-cta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: var(--spacing-md);
    margin-bottom: var(--spacing-xl);
  }

  .btn-xl {
    padding: 16px 32px;
    font-size: 1.1rem;
  }

  .hero-cta .btn-ghost {
    background: rgb(255 255 255 / 0.14);
    color: var(--landing-hero-text);
    border: 1px solid rgb(255 255 255 / 0.44);
    backdrop-filter: blur(6px);
    text-shadow: 0 8px 16px rgb(2 6 23 / 0.4);
  }

  .hero-cta .btn-ghost:hover:not(:disabled),
  .hero-cta .btn-ghost:focus-visible {
    background: rgb(2 6 23 / 0.56);
    color: rgb(248 250 252 / 0.98);
    border-color: rgb(167 243 208 / 0.56);
    outline: none;
  }

  .hero-stats {
    display: flex;
    align-items: center;
    gap: var(--spacing-lg);
    width: fit-content;
    padding: 0.7rem 1rem;
    background: rgb(15 23 42 / 0.26);
    border: 1px solid rgb(255 255 255 / 0.22);
    border-radius: var(--radius-xl);
    backdrop-filter: blur(5px);
  }

  .stat {
    display: flex;
    flex-direction: column;
  }

  .hero-stats .stat {
    min-width: 138px;
    padding: 0.95rem 1rem;
    background: linear-gradient(180deg, rgb(2 6 23 / 0.58), rgb(15 23 42 / 0.42));
    border: 1px solid rgb(255 255 255 / 0.18);
    box-shadow:
      0 18px 30px -26px rgb(2 6 23 / 0.72),
      inset 0 1px 0 rgb(255 255 255 / 0.08);
    backdrop-filter: blur(10px);
  }

  .stat-number {
    font-size: 2rem;
    font-weight: 800;
    color: rgb(236 253 245 / 0.98);
    text-shadow: 0 10px 22px rgb(2 6 23 / 0.62);
  }

  .stat-label {
    font-size: var(--font-size-sm);
    color: var(--landing-hero-text-muted);
  }

  .hero-stats .stat-number {
    color: rgb(248 250 252 / 0.99);
  }

  .hero-stats .stat-label {
    color: rgb(226 232 240 / 0.95);
  }

  .stat-divider {
    width: 1px;
    height: 40px;
    background: var(--landing-hero-divider);
  }

  .hero-visual {
    position: relative;
    height: 410px;
  }

  .hero-card {
    position: absolute;
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    padding: 0.78rem 0.9rem;
    background: linear-gradient(
      180deg,
      var(--landing-card-bg),
      var(--landing-card-bg-soft)
    );
    border: 1px solid rgb(255 255 255 / 0.24);
    border-radius: 0.95rem;
    box-shadow:
      0 26px 44px -34px rgb(2 6 23 / 0.82),
      0 10px 20px -20px var(--color-accent-glow);
    backdrop-filter: blur(8px);
    animation:
      heroCardIn 700ms cubic-bezier(0.21, 1.05, 0.4, 1) both,
      float 4.5s ease-in-out infinite;
    transform-origin: center;
  }

  .hero-card .card-icon {
    font-size: 2rem;
  }

  .hero-card .card-text {
    display: flex;
    flex-direction: column;
  }

  .hero-card .card-text strong {
    color: var(--landing-copy-primary);
    text-shadow: 0 6px 12px rgb(2 6 23 / 0.38);
  }

  .hero-card .card-text span {
    font-size: var(--font-size-sm);
    color: var(--landing-copy-secondary);
  }

  .card-1 {
    top: 10%;
    left: 10%;
    animation-delay: 80ms, 0s;
  }

  .card-2 {
    top: 40%;
    right: 10%;
    animation-delay: 220ms, 500ms;
  }

  .card-3 {
    bottom: 15%;
    left: 20%;
    animation-delay: 360ms, 900ms;
  }

  @keyframes heroCardIn {
    from {
      opacity: 0;
      transform: translateY(18px) scale(0.97);
    }
    to {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
  }

  @keyframes float {
    0%,
    100% {
      transform: translateY(0);
    }
    50% {
      transform: translateY(-10px);
    }
  }

  /* Features Section */
  .features-section {
    --landing-feature-card-bg: rgb(2 6 23 / 0.34);
    --landing-feature-card-bg-soft: rgb(15 23 42 / 0.24);
    --landing-feature-border: rgb(255 255 255 / 0.22);
    --landing-feature-aura-soft: rgb(167 243 208 / 0.2);
    --landing-feature-aura-strong: rgb(110 231 183 / 0.14);
    position: relative;
    isolation: isolate;
    padding: clamp(var(--spacing-2xl), 8vw, var(--spacing-3xl)) 0;
    background: transparent;
  }

  .features-section::before {
    display: none;
  }

  .features-section .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
  }

  .section-header {
    text-align: center;
    margin-bottom: clamp(2rem, 4vw, 3rem);
  }

  .section-badge {
    display: inline-block;
    padding: 6px 16px;
    background: linear-gradient(
      135deg,
      var(--color-accent-soft),
      var(--landing-section-bg)
    );
    color: var(--color-accent-strong);
    border: 1px solid var(--color-accent-muted);
    border-radius: 20px;
    font-size: var(--font-size-sm);
    font-weight: 700;
    letter-spacing: 0.02em;
    margin-bottom: var(--spacing-md);
  }

  .section-title {
    font-size: clamp(1.9rem, 4.2vw, 2.7rem);
    font-weight: 800;
    line-height: 1.1;
    letter-spacing: -0.01em;
    color: var(--landing-copy-primary);
    text-shadow: 0 10px 24px rgb(2 6 23 / 0.36);
    margin-bottom: var(--spacing-sm);
  }

  .section-description {
    font-size: clamp(1rem, 1.5vw, 1.14rem);
    color: var(--landing-copy-secondary);
    max-width: 640px;
    line-height: 1.72;
    margin: 0 auto;
  }

  .features-section .section-badge {
    background: rgb(2 6 23 / 0.32);
    color: rgb(236 253 245 / 0.96);
    border-color: rgb(167 243 208 / 0.4);
  }

  .features-section .section-title {
    color: var(--landing-hero-text);
    text-shadow: 0 10px 24px rgb(2 6 23 / 0.3);
  }

  .features-section .section-description {
    color: rgb(236 253 245 / 0.84);
  }

  .features-highlights {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.72rem;
    margin-top: 1.25rem;
  }

  .highlight-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.42rem 0.72rem;
    border-radius: var(--radius-full);
    background: linear-gradient(
      135deg,
      rgb(2 6 23 / 0.44),
      rgb(15 23 42 / 0.28)
    );
    border: 1px solid rgb(255 255 255 / 0.2);
    color: var(--landing-hero-text);
    box-shadow: 0 14px 20px -22px rgb(2 6 23 / 0.78);
  }

  .highlight-pill:nth-child(1) {
    border-color: rgb(110 231 183 / 0.42);
    background: linear-gradient(
      135deg,
      rgb(5 120 87 / 0.34),
      rgb(2 6 23 / 0.38)
    );
  }

  .highlight-pill:nth-child(2) {
    border-color: rgb(167 243 208 / 0.34);
    background: linear-gradient(
      135deg,
      rgb(4 120 87 / 0.3),
      rgb(15 23 42 / 0.36)
    );
  }

  .highlight-pill:nth-child(3) {
    border-color: rgb(110 231 183 / 0.36);
    background: linear-gradient(
      135deg,
      rgb(6 78 59 / 0.32),
      rgb(2 6 23 / 0.38)
    );
  }

  .highlight-pill strong {
    font-size: 0.74rem;
    font-weight: 800;
    color: var(--primary-50);
    letter-spacing: 0.03em;
    text-transform: uppercase;
  }

  .highlight-pill span {
    font-size: 0.79rem;
    color: rgb(236 253 245 / 0.84);
  }

  .features-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: clamp(1rem, 2vw, 1.2rem);
  }

  .feature-item {
    --feature-accent: var(--primary-100);
    --feature-accent-soft: rgb(110 231 183 / 0.34);
    --feature-surface-start: var(--landing-feature-card-bg);
    --feature-surface-end: var(--landing-feature-card-bg-soft);
    position: relative;
    isolation: isolate;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    padding: clamp(1.1rem, 2.5vw, 1.45rem);
    background: linear-gradient(
      160deg,
      var(--feature-surface-start) 0%,
      var(--feature-surface-end) 100%
    );
    border: 1px solid rgb(255 255 255 / 0.26);
    border-radius: 1.05rem;
    min-height: 220px;
    box-shadow:
      0 22px 34px -30px rgb(2 6 23 / 0.72),
      0 8px 14px -18px rgb(110 231 183 / 0.24);
    backdrop-filter: blur(7px) saturate(112%);
    transition: all 0.3s ease;
  }

  .feature-item > * {
    position: relative;
    z-index: 2;
  }

  .feature-item::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(
      90deg,
      var(--feature-accent),
      var(--feature-accent-soft)
    );
    opacity: 0.55;
    transition: opacity 0.3s ease;
    z-index: 3;
  }

  .feature-item::after {
    content: "";
    position: absolute;
    inset: 0;
    background:
      linear-gradient(
        180deg,
        rgb(2 6 23 / 0.1) 0%,
        rgb(2 6 23 / 0.56) 56%,
        rgb(2 6 23 / 0.82) 100%
      ),
      radial-gradient(
        circle at 78% 8%,
        rgb(255 255 255 / 0.08),
        transparent 46%
      );
    opacity: 1;
    pointer-events: none;
    transition:
      opacity 0.3s ease,
      background 0.3s ease;
    z-index: 1;
  }

  .feature-item:hover {
    transform: translateY(-6px);
    box-shadow:
      0 30px 38px -30px rgb(2 6 23 / 0.82),
      0 14px 22px -18px rgb(110 231 183 / 0.28);
    border-color: var(--feature-accent-soft);
  }

  .feature-item:hover::before {
    opacity: 1;
  }

  .feature-item:hover::after {
    background:
      linear-gradient(
        180deg,
        rgb(2 6 23 / 0.06) 0%,
        rgb(2 6 23 / 0.5) 54%,
        rgb(2 6 23 / 0.78) 100%
      ),
      radial-gradient(
        circle at 78% 8%,
        rgb(255 255 255 / 0.12),
        transparent 46%
      );
    opacity: 1;
  }

  .feature-icon-wrapper {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(
      145deg,
      var(--feature-accent-soft),
      rgb(15 23 42 / 0.52)
    );
    border: 1px solid rgb(255 255 255 / 0.36);
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-md);
    box-shadow: 0 12px 20px -20px var(--color-accent-glow);
  }

  .feature-icon {
    font-size: 2.05rem;
    filter: saturate(1.05);
  }

  .feature-chip {
    display: inline-flex;
    align-items: center;
    width: fit-content;
    margin-bottom: 0.5rem;
    padding: 0.24rem 0.56rem;
    border-radius: var(--radius-full);
    background: rgb(2 6 23 / 0.56);
    border: 1px solid var(--feature-accent-soft);
    color: rgb(240 253 250 / 0.98);
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
  }

  .feature-item:nth-child(1),
  .feature-item:nth-child(4) {
    --feature-accent: var(--primary-50);
    --feature-accent-soft: rgb(167 243 208 / 0.4);
    --feature-surface-end: rgb(5 120 87 / 0.2);
  }

  .feature-item:nth-child(2),
  .feature-item:nth-child(5) {
    --feature-accent: var(--primary-100);
    --feature-accent-soft: rgb(110 231 183 / 0.34);
    --feature-surface-end: rgb(6 95 70 / 0.2);
  }

  .feature-item:nth-child(3),
  .feature-item:nth-child(6) {
    --feature-accent: var(--primary-200);
    --feature-accent-soft: rgb(52 211 153 / 0.32);
    --feature-surface-end: rgb(4 120 87 / 0.18);
  }

  .feature-item h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: rgb(248 250 252 / 0.99);
    text-shadow: 0 8px 18px rgb(2 6 23 / 0.56);
    margin-bottom: var(--spacing-sm);
  }

  .feature-item p {
    color: rgb(241 245 249 / 0.94);
    text-shadow: 0 6px 14px rgb(2 6 23 / 0.5);
    line-height: 1.62;
  }

  /* How It Works */
  .how-it-works-section {
    padding: clamp(var(--spacing-2xl), 7vw, var(--spacing-3xl)) 0;
    background: transparent;
  }

  .how-it-works-section .container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
  }

  .steps-grid {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: var(--spacing-md);
  }

  .step-item {
    flex: 1;
    text-align: center;
    padding: var(--spacing-lg);
    background: linear-gradient(
      180deg,
      rgb(2 6 23 / 0.56),
      rgb(15 23 42 / 0.38)
    );
    border: 1px solid rgb(255 255 255 / 0.24);
    border-radius: var(--radius-xl);
    box-shadow:
      0 24px 34px -34px rgb(2 6 23 / 0.84),
      0 12px 20px -22px rgb(16 185 129 / 0.24);
    backdrop-filter: blur(5px);
  }

  .step-number {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(
      135deg,
      var(--primary-50),
      var(--primary-100) 55%,
      var(--primary-300)
    );
    color: var(--primary-900);
    font-size: 1.5rem;
    font-weight: 800;
    border-radius: 50%;
    margin: 0 auto var(--spacing-md);
    box-shadow: 0 14px 24px -20px rgb(16 185 129 / 0.8);
  }

  .step-item h3 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--landing-copy-primary);
    margin-bottom: var(--spacing-sm);
  }

  .step-item p {
    color: var(--landing-copy-secondary);
  }

  .step-connector {
    flex: 0 0 60px;
    height: 2px;
    background: linear-gradient(
      90deg,
      rgb(167 243 208 / 0.48),
      rgb(110 231 183 / 0.9)
    );
    margin-top: 58px;
  }

  /* Announcements Section */
  .announcements-section {
    padding: clamp(var(--spacing-2xl), 7vw, var(--spacing-3xl)) 0;
    background: transparent;
  }

  .announcements-section .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
  }

  .announcements-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: clamp(1rem, 2vw, 1.5rem);
  }

  .announcement-card {
    position: relative;
    isolation: isolate;
    display: flex;
    flex-direction: column;
    padding: clamp(1.1rem, 2.5vw, 1.45rem);
    background: linear-gradient(
      160deg,
      rgb(2 6 23 / 0.58),
      rgb(15 23 42 / 0.38)
    );
    border: 1px solid rgb(255 255 255 / 0.26);
    border-radius: 1.05rem;
    min-height: 220px;
    box-shadow:
      0 22px 34px -30px rgb(2 6 23 / 0.72),
      0 8px 14px -18px rgb(110 231 183 / 0.24);
    backdrop-filter: blur(7px) saturate(112%);
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
  }

  .announcement-card:hover {
    transform: translateY(-6px);
    box-shadow:
      0 30px 38px -30px rgb(2 6 23 / 0.82),
      0 14px 22px -18px rgb(110 231 183 / 0.28);
    border-color: rgb(167 243 208 / 0.4);
  }

  .announcement-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(
      90deg,
      var(--primary-50),
      rgb(167 243 208 / 0.4)
    );
    opacity: 0.55;
    transition: opacity 0.3s ease;
    z-index: 3;
  }

  .announcement-card:hover::before {
    opacity: 1;
  }

  .announcement-date {
    display: inline-flex;
    align-items: center;
    width: fit-content;
    margin-bottom: 0.75rem;
    padding: 0.24rem 0.56rem;
    border-radius: var(--radius-full);
    background: rgb(2 6 23 / 0.56);
    border: 1px solid rgb(167 243 208 / 0.4);
    color: rgb(240 253 250 / 0.98);
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
  }

  .announcement-card h3 {
    font-size: 1.15rem;
    font-weight: 700;
    color: rgb(248 250 252 / 0.99);
    text-shadow: 0 8px 18px rgb(2 6 23 / 0.56);
    margin-bottom: var(--spacing-sm);
    line-height: 1.3;
  }

  .announcement-card p {
    color: rgb(241 245 249 / 0.94);
    text-shadow: 0 6px 14px rgb(2 6 23 / 0.5);
    line-height: 1.62;
    flex: 1;
  }

  .announcement-skeleton,
  .event-skeleton {
    position: relative;
    isolation: isolate;
    padding: clamp(1.1rem, 2.5vw, 1.45rem);
    background: linear-gradient(
      160deg,
      rgb(2 6 23 / 0.58),
      rgb(15 23 42 / 0.38)
    );
    border: 1px solid rgb(255 255 255 / 0.26);
    border-radius: 1.05rem;
    min-height: 220px;
    box-shadow:
      0 22px 34px -30px rgb(2 6 23 / 0.72),
      0 8px 14px -18px rgb(110 231 183 / 0.24);
    backdrop-filter: blur(7px) saturate(112%);
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
  }

  @keyframes pulse {
    0%, 100% {
      opacity: 1;
    }
    50% {
      opacity: 0.5;
    }
  }

  /* Events Section */
  .events-section {
    padding: clamp(var(--spacing-2xl), 7vw, var(--spacing-3xl)) 0;
    background: transparent;
  }

  .events-section .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
  }

  .events-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: clamp(1rem, 2vw, 1.5rem);
  }

  .event-card {
    position: relative;
    isolation: isolate;
    display: flex;
    flex-direction: column;
    padding: 0;
    background: linear-gradient(
      160deg,
      rgb(2 6 23 / 0.58),
      rgb(15 23 42 / 0.38)
    );
    border: 1px solid rgb(255 255 255 / 0.26);
    border-radius: 1.05rem;
    overflow: hidden;
    box-shadow:
      0 22px 34px -30px rgb(2 6 23 / 0.72),
      0 8px 14px -18px rgb(110 231 183 / 0.24);
    backdrop-filter: blur(7px) saturate(112%);
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
  }

  .event-card:hover {
    transform: translateY(-6px);
    box-shadow:
      0 30px 38px -30px rgb(2 6 23 / 0.82),
      0 14px 22px -18px rgb(110 231 183 / 0.28);
    border-color: rgb(167 243 208 / 0.4);
  }

  .event-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(
      90deg,
      var(--primary-50),
      rgb(167 243 208 / 0.4)
    );
    opacity: 0.55;
    transition: opacity 0.3s ease;
    z-index: 3;
  }

  .event-card:hover::before {
    opacity: 1;
  }

  .event-date-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    background: linear-gradient(
      135deg,
      var(--primary-50),
      var(--primary-100) 55%,
      var(--primary-300)
    );
    border-radius: 8px;
    box-shadow: 0 14px 24px -20px rgb(16 185 129 / 0.8);
    z-index: 2;
  }

  .event-date-badge .day {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--primary-900);
    line-height: 1;
  }

  .event-date-badge .month {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--primary-700);
    text-transform: uppercase;
    letter-spacing: 0.05em;
  }

  .event-card-content {
    padding: clamp(1.1rem, 2.5vw, 1.45rem);
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    flex: 1;
  }

  .event-card h3 {
    font-size: 1.15rem;
    font-weight: 700;
    color: rgb(248 250 252 / 0.99);
    text-shadow: 0 8px 18px rgb(2 6 23 / 0.56);
    margin-bottom: 0;
    line-height: 1.3;
  }

  .event-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .event-meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: rgb(241 245 249 / 0.94);
    font-size: 0.85rem;
  }

  .event-meta-item svg {
    flex-shrink: 0;
    color: var(--primary-100);
  }

  .event-card p {
    color: rgb(241 245 249 / 0.94);
    text-shadow: 0 6px 14px rgb(2 6 23 / 0.5);
    line-height: 1.62;
    flex: 1;
    font-size: 0.9rem;
  }

  @media (max-width: 1024px) {
    .announcements-grid,
    .events-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (max-width: 768px) {
    .announcements-grid,
    .events-grid {
      grid-template-columns: 1fr;
    }
  }

  /* About Section */
  .about-section {
    position: relative;
    padding: clamp(var(--spacing-xl), 5vw, var(--spacing-2xl)) 0
      clamp(var(--spacing-md), 3vw, var(--spacing-lg));
    background: transparent;
  }

  .about-section .container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
  }

  .about-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: clamp(1.4rem, 3vw, 2.4rem);
    align-items: stretch;
  }

  .about-content {
    padding: clamp(1rem, 2.6vw, 1.5rem);
    border-radius: calc(var(--radius-xl) + 0.25rem);
    border: 1px solid rgb(255 255 255 / 0.24);
    background: linear-gradient(
      160deg,
      rgb(2 6 23 / 0.58),
      rgb(15 23 42 / 0.38)
    );
    box-shadow:
      0 28px 42px -36px rgb(2 6 23 / 0.86),
      0 14px 22px -24px rgb(16 185 129 / 0.24);
    backdrop-filter: blur(6px);
  }

  .about-content .section-badge {
    background: rgb(2 6 23 / 0.42);
    color: rgb(240 253 250 / 0.98);
    border-color: rgb(167 243 208 / 0.44);
  }

  .about-content .section-title {
    color: var(--landing-hero-text);
    text-shadow: 0 10px 24px rgb(2 6 23 / 0.4);
  }

  .about-content p {
    color: var(--landing-copy-secondary);
    line-height: 1.7;
    text-shadow: 0 6px 14px rgb(2 6 23 / 0.34);
    margin-bottom: var(--spacing-md);
  }

  .about-features {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-sm);
    margin-top: var(--spacing-md);
  }

  .about-feature {
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    width: 100%;
    padding: 0.34rem 0.62rem;
    border-radius: var(--radius-full);
    border: 1px solid rgb(255 255 255 / 0.24);
    background: linear-gradient(
      120deg,
      rgb(2 6 23 / 0.34),
      rgb(5 120 87 / 0.22)
    );
    box-shadow: 0 12px 20px -22px rgb(2 6 23 / 0.72);
    color: var(--landing-copy-primary);
  }

  .about-feature svg {
    color: var(--primary-100);
  }

  .about-feature span {
    font-weight: 600;
    letter-spacing: 0.01em;
  }

  .about-image {
    display: flex;
    justify-content: center;
  }

  .image-placeholder {
    width: 100%;
    max-width: 400px;
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: linear-gradient(
      135deg,
      rgb(2 6 23 / 0.56),
      rgb(5 120 87 / 0.3)
    );
    border: 1px solid rgb(167 243 208 / 0.4);
    border-radius: var(--radius-xl);
    color: var(--landing-hero-text);
    box-shadow:
      0 28px 42px -36px rgb(2 6 23 / 0.84),
      0 14px 24px -24px rgb(16 185 129 / 0.28);
    backdrop-filter: blur(4px);
  }

  .image-placeholder span {
    font-size: 5rem;
    color: var(--primary-50);
    text-shadow: 0 12px 24px rgb(2 6 23 / 0.4);
    margin-bottom: var(--spacing-md);
  }

  .image-placeholder p {
    color: rgb(236 253 245 / 0.92);
    font-weight: 600;
    text-align: center;
    text-shadow: 0 8px 18px rgb(2 6 23 / 0.42);
  }

  /* CTA Section */
  .cta-section {
    position: relative;
    overflow: visible;
    padding: clamp(var(--spacing-md), 3vw, var(--spacing-lg)) 0
      clamp(var(--spacing-xl), 5vw, var(--spacing-2xl));
    background: transparent;
  }

  .cta-section::before {
    display: none;
  }

  .cta-section .container {
    max-width: 920px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
  }

  .cta-content {
    text-align: center;
    color: white;
    position: relative;
    z-index: 1;
    max-width: 760px;
    margin: 0 auto;
    padding: clamp(1rem, 2.2vw, 1.5rem);
    border-radius: calc(var(--radius-xl) + 0.35rem);
    border: 1px solid rgb(255 255 255 / 0.28);
    background: linear-gradient(
      165deg,
      rgb(2 6 23 / 0.64) 0%,
      rgb(5 120 87 / 0.26) 52%,
      rgb(2 6 23 / 0.56) 100%
    );
    box-shadow:
      0 34px 48px -40px rgb(2 6 23 / 0.9),
      0 16px 24px -24px rgb(16 185 129 / 0.34);
    backdrop-filter: blur(8px);
  }

  .cta-content::before {
    content: "";
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background: radial-gradient(
      circle at 18% 6%,
      rgb(167 243 208 / 0.24),
      transparent 42%
    );
    pointer-events: none;
  }

  .cta-content h2 {
    font-size: clamp(1.9rem, 4vw, 2.7rem);
    font-weight: 800;
    color: rgb(248 250 252 / 0.99);
    text-shadow: 0 12px 24px rgb(2 6 23 / 0.44);
    margin-bottom: var(--spacing-md);
  }

  .cta-content p {
    font-size: 1.1rem;
    color: rgb(226 232 240 / 0.94);
    text-shadow: 0 6px 14px rgb(2 6 23 / 0.36);
    margin-bottom: var(--spacing-xl);
  }

  .cta-links {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.6rem;
    padding: 0;
    border: 0;
    background: transparent;
  }

  .cta-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 138px;
    padding: 0.68rem 1.05rem;
    border-radius: var(--radius-full);
    border: 1px solid rgb(255 255 255 / 0.36);
    background: rgb(2 6 23 / 0.42);
    color: rgb(248 250 252 / 0.98);
    font-weight: 700;
    letter-spacing: 0.01em;
    text-decoration: none;
    transition:
      transform 0.2s ease,
      box-shadow 0.2s ease,
      background 0.2s ease,
      color 0.2s ease;
  }

  .cta-link:first-of-type {
    border-color: rgb(167 243 208 / 0.72);
    background: linear-gradient(
      135deg,
      var(--primary-50),
      var(--primary-200) 68%,
      var(--primary-300)
    );
    color: var(--primary-900);
    box-shadow: 0 14px 24px -20px rgb(16 185 129 / 0.86);
  }

  .cta-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 24px -22px rgb(2 6 23 / 0.86);
  }

  .cta-link:not(:first-of-type):hover {
    background: rgb(2 6 23 / 0.64);
    border-color: rgb(167 243 208 / 0.52);
    color: rgb(248 250 252 / 0.99);
  }

  .cta-link:first-of-type:hover {
    color: var(--primary-900);
    background: linear-gradient(
      135deg,
      var(--primary-50),
      var(--primary-200) 68%,
      var(--primary-300)
    );
  }

  .cta-divider {
    display: none;
  }

  /* Footer */
  .landing-footer {
    position: relative;
    background: var(--auth-page-gradient-end);
    color: white;
    padding: 60px 0 30px;
    border-top: 1px solid rgb(255 255 255 / 0.14);
  }

  .landing-footer .container {
    position: relative;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 var(--spacing-lg);
  }

  .footer-content {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 60px;
    margin-bottom: 40px;
    padding: 0;
    border: 0;
    border-radius: 0;
    background: none;
    box-shadow: none;
    backdrop-filter: none;
  }

  .footer-logo {
    width: 60px;
    height: 60px;
    margin-bottom: var(--spacing-md);
  }

  .footer-logo-text {
    font-size: 2rem;
    font-weight: 800;
    color: var(--color-accent);
  }

  .footer-brand p {
    color: rgb(241 245 249 / 0.9);
    line-height: 1.65;
    text-shadow: none;
    max-width: 34ch;
  }

  .footer-links h4 {
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 0.01em;
    margin-bottom: var(--spacing-md);
    color: rgb(248 250 252 / 0.98);
    text-shadow: none;
  }

  .footer-links a {
    display: block;
    width: fit-content;
    color: rgb(226 232 240 / 0.92);
    padding: 0.28rem 0.42rem;
    border-radius: var(--radius-md);
    transition:
      color 0.2s,
      background 0.2s,
      transform 0.2s;
  }

  .footer-links a:hover {
    color: var(--primary-50);
    background: rgb(255 255 255 / 0.08);
    transform: translateX(2px);
  }

  .footer-bottom {
    margin-top: 0.4rem;
    padding-top: 30px;
    border-top: 1px solid rgb(255 255 255 / 0.18);
    text-align: center;
  }

  .footer-bottom p {
    color: rgb(226 232 240 / 0.88);
    text-shadow: none;
    font-size: var(--font-size-sm);
  }

  /* Responsive */
  @media (max-width: 1024px) {
    .hero-section .container {
      grid-template-columns: 1fr;
      text-align: center;
    }

    .hero-content {
      margin: 0 auto;
    }

    .hero-visual {
      display: none;
    }

    .hero-cta {
      justify-content: center;
    }

    .hero-stats {
      justify-content: center;
    }

    .features-grid {
      grid-template-columns: repeat(2, 1fr);
    }

    .about-grid {
      grid-template-columns: 1fr;
    }

    .about-image {
      order: -1;
    }

    .cta-content {
      max-width: 700px;
    }
  }

  @media (max-width: 768px) {
    .nav-title {
      font-size: 1rem;
    }

    .nav-logo {
      width: 28px;
      height: 28px;
    }

    .nav-brand {
      max-width: calc(100% - 130px);
      gap: 0.38rem;
    }

    .nav-links {
      display: flex;
      gap: 0.22rem;
    }

    .nav-link {
      display: inline-flex;
      font-size: var(--font-size-xs);
      padding: 0.32rem 0.62rem;
    }

    .nav-subtitle {
      display: block;
    }

    .hero-description {
      padding: 0 var(--spacing-sm);
    }

    .hero-cta {
      width: min(100%, 340px);
      margin-left: auto;
      margin-right: auto;
      flex-direction: column;
      align-items: stretch;
    }

    .hero-cta .btn {
      width: 100%;
    }

    .hero-stats {
      flex-wrap: wrap;
      gap: var(--spacing-md);
      justify-content: center;
      width: 100%;
    }

    .stat-divider {
      display: none;
    }

    .features-grid {
      grid-template-columns: 1fr;
    }

    .highlight-pill {
      width: min(100%, 360px);
      justify-content: center;
    }

    .steps-grid {
      flex-direction: column;
      align-items: center;
      width: 100%;
    }

    .step-item {
      width: min(100%, 460px);
    }

    .about-content {
      padding: 0.95rem;
    }

    .about-feature {
      padding: 0.38rem 0.6rem;
    }

    .step-connector {
      width: 2px;
      height: 30px;
      margin: 0;
      background: linear-gradient(
        180deg,
        var(--color-accent-muted),
        var(--color-accent)
      );
    }

    .footer-content {
      grid-template-columns: 1fr;
      gap: 24px;
      padding: 1rem;
    }

    .cta-links {
      width: 100%;
      margin: 0 auto;
      flex-direction: column;
      align-items: stretch;
    }

    .cta-link {
      width: 100%;
    }
  }
</style>

<script>
  (function () {
    if (typeof API === "undefined") {
      return;
    }

    const byId = (id) => document.getElementById(id);

    const setText = (id, value) => {
      const element = byId(id);
      if (!element || value === null || value === undefined) {
        return;
      }

      element.textContent = String(value);
    };

    const toArray = (value) => (Array.isArray(value) ? value : []);

    const toNumber = (value) => {
      const parsed = Number(value);
      return Number.isFinite(parsed) ? parsed : 0;
    };

    const formatCount = (value) => {
      const safeValue = toNumber(value);
      if (Math.abs(safeValue) >= 10000) {
        return new Intl.NumberFormat("en-US", {
          notation: "compact",
          maximumFractionDigits: 1,
        }).format(safeValue);
      }

      return new Intl.NumberFormat("en-US").format(safeValue);
    };

    const truncate = (value, maxLength = 34) => {
      const safeValue = String(value || "").trim();
      if (!safeValue) {
        return "";
      }

      if (safeValue.length <= maxLength) {
        return safeValue;
      }

      return `${safeValue.slice(0, maxLength - 1)}...`;
    };

    const getFirstName = (name) => {
      const safeName = String(name || "").trim();
      if (!safeName) {
        return "Alumni";
      }

      return safeName.split(/\s+/)[0] || safeName;
    };

    const getBrandingName = () => {
      if (
        typeof App !== "undefined" &&
        App &&
        typeof App.getBrandingSnapshot === "function"
      ) {
        const snapshot = App.getBrandingSnapshot();
        if (snapshot && snapshot.institutionName) {
          return snapshot.institutionName;
        }
      }

      return "MINSU Alumni";
    };

    const formatEventDate = (dateValue, timeValue) => {
      const safeDate = String(dateValue || "").trim();
      if (!safeDate) {
        return "Upcoming soon";
      }

      const date = new Date(safeDate);
      if (Number.isNaN(date.getTime())) {
        return "Upcoming soon";
      }

      let label = date.toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
      });

      const safeTime = String(timeValue || "").trim();
      if (safeTime) {
        const match = safeTime.match(/^(\d{1,2}):(\d{2})/);
        if (match) {
          const temp = new Date();
          temp.setHours(Number(match[1]), Number(match[2]), 0, 0);
          label += ` · ${temp.toLocaleTimeString("en-US", {
            hour: "numeric",
            minute: "2-digit",
          })}`;
        }
      }

      return label;
    };

    const applyGuestStats = (
      upcomingCount,
      announcementCount,
      collegeCount,
      hasUpcomingData,
      hasAnnouncementsData,
      hasCollegeData,
    ) => {
      setText(
        "heroStatOneValue",
        hasUpcomingData ? formatCount(upcomingCount) : "5,000+",
      );
      setText("heroStatOneLabel", "Upcoming Events");

      setText(
        "heroStatTwoValue",
        hasAnnouncementsData ? formatCount(announcementCount) : "50+",
      );
      setText("heroStatTwoLabel", "Announcements");

      setText(
        "heroStatThreeValue",
        hasCollegeData ? formatCount(collegeCount) : "12",
      );
      setText("heroStatThreeLabel", "Colleges");
    };

    const applyUserStats = (pointsData, eventStatsData) => {
      const totalPoints = toNumber(pointsData.total_points);
      const attended = toNumber(eventStatsData.attended);
      const upcoming = toNumber(eventStatsData.upcoming);

      setText("heroStatOneValue", formatCount(totalPoints));
      setText("heroStatOneLabel", "Your Points");

      setText("heroStatTwoValue", formatCount(attended));
      setText("heroStatTwoLabel", "Events Attended");

      setText("heroStatThreeValue", formatCount(upcoming));
      setText("heroStatThreeLabel", "Upcoming Events");
    };

    const applyEventCard = (eventItem, fallbackAnnouncement) => {
      if (eventItem) {
        setText(
          "heroCardEventTitle",
          truncate(eventItem.title || "Upcoming Event"),
        );
        setText(
          "heroCardEventLabel",
          formatEventDate(eventItem.event_date, eventItem.event_time),
        );
        return;
      }

      if (fallbackAnnouncement) {
        setText(
          "heroCardEventTitle",
          truncate(fallbackAnnouncement.title || "Latest Announcement"),
        );
        setText("heroCardEventLabel", "Latest published update");
        return;
      }

      setText("heroCardEventTitle", "No Upcoming Event Yet");
      setText("heroCardEventLabel", "Watch this space for updates");
    };

    const applyUserCard = (user) => {
      if (user && user.name) {
        setText("heroCardUserTitle", "Welcome Back!");
        setText("heroCardUserValue", getFirstName(user.name));
        return;
      }

      setText("heroCardUserTitle", "Welcome to");
      setText("heroCardUserValue", truncate(getBrandingName(), 28));
    };

    const applyActivityCard = (
      hasUserMetrics,
      pointsData,
      eventStatsData,
      upcomingCount,
      hasUpcomingData,
    ) => {
      if (hasUserMetrics) {
        const totalPoints = toNumber(pointsData.total_points);
        const attended = toNumber(eventStatsData.attended);

        setText("heroCardPointsValue", `+${formatCount(totalPoints)} Points`);
        setText(
          "heroCardPointsLabel",
          attended > 0
            ? `${formatCount(attended)} events attended`
            : "Complete profile and join events",
        );
        return;
      }

      setText(
        "heroCardPointsValue",
        hasUpcomingData
          ? `${formatCount(upcomingCount)} Open Events`
          : "Community Updates",
      );
      setText("heroCardPointsLabel", "Ready for alumni participation");
    };

    const hydrateLandingData = async () => {
      const user = typeof API.getUser === "function" ? API.getUser() : null;
      const isAuthenticated =
        typeof API.getToken === "function" && Boolean(API.getToken());

      applyUserCard(user);

      const requests = [
        API.events
          .list({ status: "upcoming", page: 1, limit: 1 })
          .catch(() => null),
        API.announcements.list({ limit: 20, offset: 0 }).catch(() => null),
        API.organization.getColleges().catch(() => null),
      ];

      if (isAuthenticated) {
        requests.push(API.events.getMyStats().catch(() => null));
        requests.push(API.gamification.getPoints().catch(() => null));
      } else {
        requests.push(Promise.resolve(null));
        requests.push(Promise.resolve(null));
      }

      const [
        eventsResponse,
        announcementsResponse,
        collegesResponse,
        myStatsResponse,
        pointsResponse,
      ] = await Promise.all(requests);

      const upcomingEvents = toArray(eventsResponse?.data?.events);
      const upcomingEvent = upcomingEvents[0] || null;
      const upcomingCount = toNumber(
        eventsResponse?.data?.total || upcomingEvents.length,
      );
      const hasUpcomingData = Boolean(eventsResponse && eventsResponse.success);

      const announcements = toArray(announcementsResponse?.data?.announcements);
      const announcementCount = announcements.length;
      const hasAnnouncementsData = Boolean(
        announcementsResponse && announcementsResponse.success,
      );

      const colleges = toArray(collegesResponse?.data);
      const collegeCount = colleges.length;
      const hasCollegeData = Boolean(
        collegesResponse && collegesResponse.success,
      );

      const eventStatsData = myStatsResponse?.data || {};
      const pointsData = pointsResponse?.data || {};
      const hasUserMetrics = Boolean(
        (myStatsResponse && myStatsResponse.success) ||
        (pointsResponse && pointsResponse.success),
      );

      applyActivityCard(
        hasUserMetrics,
        pointsData,
        eventStatsData,
        upcomingCount,
        hasUpcomingData,
      );
      applyEventCard(upcomingEvent, announcements[0] || null);

      if (hasUserMetrics) {
        applyUserStats(pointsData, eventStatsData);
      } else {
        applyGuestStats(
          upcomingCount,
          announcementCount,
          collegeCount,
          hasUpcomingData,
          hasAnnouncementsData,
          hasCollegeData,
        );
      }
    };

    hydrateLandingData().catch(() => {
      // Keep static fallback content if dynamic landing data is unavailable.
    });

    // Load announcements
    const loadAnnouncements = async () => {
      const grid = document.getElementById('announcementsGrid');
      if (!grid) return;

      try {
        const response = await API.announcements.list({ limit: 3, status: 'published' });
        const announcements = toArray(response?.data?.announcements);

        if (!announcements.length) {
          grid.innerHTML = '<div class="col-span-3 text-center text-secondary">No announcements available at this time.</div>';
          return;
        }

        grid.innerHTML = announcements.map(announcement => {
          const title = truncate(announcement.title || 'Announcement', 60);
          const content = truncate(announcement.content || '', 120);
          const date = announcement.published_at || announcement.created_at;
          const formattedDate = date ? new Date(date).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
          }) : 'Recent';

          return `
            <a href="#/announcements/${announcement.id}" class="announcement-card">
              <span class="announcement-date">${Utils.escapeHtml(formattedDate)}</span>
              <h3>${Utils.escapeHtml(title)}</h3>
              <p>${Utils.escapeHtml(content)}</p>
            </a>
          `;
        }).join('');
      } catch (error) {
        console.error('Failed to load announcements:', error);
        grid.innerHTML = '<div class="col-span-3 text-center text-secondary">Unable to load announcements.</div>';
      }
    };

    // Load events
    const loadEvents = async () => {
      const grid = document.getElementById('eventsGrid');
      if (!grid) return;

      try {
        const response = await API.events.list({ status: 'upcoming', limit: 3 });
        const events = toArray(response?.data?.events);

        if (!events.length) {
          grid.innerHTML = '<div class="col-span-3 text-center text-secondary">No upcoming events at this time.</div>';
          return;
        }

        grid.innerHTML = events.map(event => {
          const title = truncate(event.title || 'Event', 60);
          const description = truncate(event.description || '', 100);
          const eventDate = event.event_date ? new Date(event.event_date) : null;
          const day = eventDate ? eventDate.getDate() : '?';
          const month = eventDate ? eventDate.toLocaleDateString('en-US', { month: 'short' }) : '?';
          const location = event.location || 'TBA';
          const time = event.event_time || 'TBA';

          return `
            <a href="#/events/${event.id}" class="event-card">
              <div class="event-date-badge">
                <span class="day">${day}</span>
                <span class="month">${month}</span>
              </div>
              <div class="event-card-content">
                <h3>${Utils.escapeHtml(title)}</h3>
                <div class="event-meta">
                  <div class="event-meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <circle cx="12" cy="12" r="10"></circle>
                      <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    <span>${Utils.escapeHtml(time)}</span>
                  </div>
                  <div class="event-meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                      <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    <span>${Utils.escapeHtml(location)}</span>
                  </div>
                </div>
                <p>${Utils.escapeHtml(description)}</p>
              </div>
            </a>
          `;
        }).join('');
      } catch (error) {
        console.error('Failed to load events:', error);
        grid.innerHTML = '<div class="col-span-3 text-center text-secondary">Unable to load events.</div>';
      }
    };

    // Load announcements and events
    loadAnnouncements();
    loadEvents();

    const sectionRouteTargets = {
      "/features": "features",
      "/about": "about",
    };

    const currentRoutePath = String(window.__pageContext?.path || "");
    const targetSectionId = sectionRouteTargets[currentRoutePath] || null;
    if (targetSectionId) {
      requestAnimationFrame(() => {
        const targetSection = document.getElementById(targetSectionId);
        if (targetSection) {
          targetSection.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      });
    }
  })();
</script>
