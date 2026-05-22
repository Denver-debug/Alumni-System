<!-- Email Verification Page -->
<div class="page-wrapper">
  <div class="auth-card">
    <!-- Header Section with Gradient -->
    <div class="auth-header">
      <div class="auth-logo">
        <img src="/assets/images/logo.svg" alt="MINSU Alumni" 
          onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';" />
        <span class="auth-logo-text" style="display: none">MINSU</span>
      </div>
      <h1 class="auth-title">Verify Email</h1>
      <p class="auth-subtitle">Confirm your email to continue registration</p>
    </div>

    <div class="auth-body">
      <!-- Icon -->
      <div style="text-align: center; margin-bottom: var(--spacing-lg);">
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; background: var(--primary-100); border-radius: 50%;">
          <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="var(--primary-600)" stroke-width="2">
            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
            <polyline points="22,6 12,13 2,6"/>
          </svg>
        </div>
      </div>

      <p class="text-secondary mb-lg text-center" id="emailText">
        We've sent a verification code to your email address.
      </p>

      <div
        id="approvalNotice"
        class="alert alert-success mb-md"
        style="display: none"
      >
        <div id="approvalText" class="mb-sm"></div>
        <button type="button" class="btn btn-primary btn-sm" id="approvalLoginBtn">
          Go to login
        </button>
      </div>

      <!-- Verification Form -->
      <form id="verifyForm">
        <input type="hidden" id="email" name="email" />
        
        <div class="form-group">
          <label class="form-label" for="code">Verification Code</label>
          <input
            type="text"
            id="code"
            name="code"
            class="auth-input text-center"
            placeholder="Enter 6-digit code"
            maxlength="6"
            style="font-size: 1.5rem; letter-spacing: 0.5rem; text-align: center;"
            required
          />
        </div>

        <button type="submit" class="btn-auth" id="verifyBtn">
          Verify Email
        </button>
      </form>

      <!-- Resend -->
      <div class="mt-lg text-center" id="resendSection">
        <p class="text-sm text-secondary mb-sm">Didn't receive the code?</p>
        <button type="button" class="btn btn-ghost" id="resendBtn">
          Resend Code
        </button>
        <p class="text-xs text-muted mt-sm" id="timerText" style="display: none">
          You can resend in <span id="countdown">60</span> seconds
        </p>
      </div>

      <!-- Back to login -->
      <p class="auth-toggle">
        <a href="#/login" class="auth-link">← Back to login</a>
      </p>
    </div>
  </div>
</div>

<script>
  (function () {
    const verifyForm = Utils.$("#verifyForm");
    const resendBtn = Utils.$("#resendBtn");
    const emailInput = Utils.$("#email");
    const emailText = Utils.$("#emailText");
    const approvalNotice = Utils.$("#approvalNotice");
    const approvalText = Utils.$("#approvalText");
    const approvalLoginBtn = Utils.$("#approvalLoginBtn");
    const resendSection = Utils.$("#resendSection");
    const timerText = Utils.$("#timerText");
    const countdownSpan = Utils.$("#countdown");

    // Get email from session storage or URL params
    const email =
      sessionStorage.getItem("verify_email") ||
      new URLSearchParams(window.location.hash.split("?")[1]).get("email");

    if (!email) {
      Utils.error("No email address provided");
      Router.navigate("/register");
      return;
    }

    emailInput.value = email;
    emailText.innerHTML = `We've sent a verification code to <strong>${Utils.escapeHtml(email)}</strong>`;

    approvalLoginBtn.addEventListener("click", () => {
      Router.navigate("/login");
    });

    let resendTimer = null;
    let countdown = 0;

    function startResendTimer(seconds = 60) {
      countdown = seconds;
      resendBtn.disabled = true;
      timerText.style.display = "block";
      countdownSpan.textContent = countdown;

      resendTimer = setInterval(() => {
        countdown--;
        countdownSpan.textContent = countdown;

        if (countdown <= 0) {
          clearInterval(resendTimer);
          resendBtn.disabled = false;
          timerText.style.display = "none";
        }
      }, 1000);
    }

    // Start timer on page load
    startResendTimer();

    // Handle verification
    verifyForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const code = Utils.$("#code").value.trim();

      if (code.length !== 6) {
        Utils.error("Please enter a valid 6-digit code");
        return;
      }

      Utils.setButtonLoading("#verifyBtn", true);

      try {
        const response = await Auth.verifyEmail(email, code);

        const requiresProfileCompletion = !!response?.data?.requiresProfileCompletion;
        const requiresApproval = !!response?.data?.requiresApproval;
        const approvalMessage =
          "Email verified successfully. Your account is pending admin approval. You can log in once it is approved.";

        // Clear stored email
        sessionStorage.removeItem("verify_email");

        if (requiresProfileCompletion) {
          Utils.success("Email verified. Please complete your profile.");
          Router.navigate("/complete-profile");
          return;
        }

        if (requiresApproval) {
          Utils.success("Email verified. Your account is pending admin approval.");
          verifyForm.style.display = "none";
          if (resendSection) {
            resendSection.style.display = "none";
          }
          approvalText.textContent = approvalMessage;
          approvalNotice.style.display = "block";
          setTimeout(() => Router.navigate("/login"), 4000);
          return;
        }

        Utils.success("Email verified successfully. You can now log in.");
        Router.navigate("/login");
      } catch (error) {
        Utils.error(error.message || "Invalid verification code");
      } finally {
        Utils.setButtonLoading("#verifyBtn", false);
      }
    });

    // Handle resend
    resendBtn.addEventListener("click", async () => {
      Utils.setButtonLoading("#resendBtn", true);

      try {
        await Auth.resendVerification(email);
        Utils.success("Verification code sent!");
        startResendTimer();
      } catch (error) {
        Utils.error(error.message || "Failed to resend code");
      } finally {
        Utils.setButtonLoading("#resendBtn", false);
      }
    });

    // Auto-focus and auto-submit
    const codeInput = Utils.$("#code");
    codeInput.focus();

    codeInput.addEventListener("input", (e) => {
      e.target.value = e.target.value.replace(/\D/g, "");

      if (e.target.value.length === 6) {
        verifyForm.dispatchEvent(new Event("submit"));
      }
    });
  })();
</script>
