<!-- Forgot Password Page -->
<div class="min-h-screen flex items-center justify-center bg-gray-50 p-lg">
  <div class="card" style="width: 100%; max-width: 420px">
    <div class="card-body p-xl text-center">
      <!-- Icon -->
      <div
        class="avatar avatar-2xl bg-primary-light mb-lg"
        style="margin: 0 auto"
      >
        <svg
          width="32"
          height="32"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          style="color: var(--primary-700)"
        >
          <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
          <path d="M7 11V7a5 5 0 0 1 10 0v4" />
        </svg>
      </div>

      <h1 class="text-2xl font-bold text-gray-900 mb-sm">Forgot Password?</h1>
      <p class="text-secondary mb-lg">
        Enter your email address and we'll send you a code to reset your
        password.
      </p>

      <!-- Forgot Password Form -->
      <form id="forgotForm">
        <div class="form-group">
          <label class="form-label text-left" for="email">Email Address</label>
          <input
            type="email"
            id="email"
            name="email"
            class="form-input"
            placeholder="Enter your email"
            required
          />
        </div>

        <button
          type="submit"
          class="btn btn-primary btn-block btn-lg"
          id="submitBtn"
        >
          Send Reset Code
        </button>
      </form>

      <!-- Back to login -->
      <p class="mt-lg text-sm">
        <a href="#/login">← Back to login</a>
      </p>
    </div>
  </div>
</div>

<script>
  (function () {
    const forgotForm = Utils.$("#forgotForm");

    forgotForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const email = Utils.$("#email").value.trim();

      if (!email) {
        Utils.error("Please enter your email address");
        return;
      }

      Utils.setButtonLoading("#submitBtn", true);

      try {
        await Auth.forgotPassword(email);

        Utils.success(
          "If an account exists with this email, you will receive a reset code.",
        );

        // Store email for reset page
        sessionStorage.setItem("reset_email", email);

        // Redirect to reset password
        Router.navigate("/reset-password");
      } catch (error) {
        Utils.error(error.message || "Failed to send reset code");
      } finally {
        Utils.setButtonLoading("#submitBtn", false);
      }
    });
  })();
</script>
