<!-- Reset Password Page -->
<div class="min-h-screen flex items-center justify-center bg-gray-50 p-lg">
  <div class="card" style="width: 100%; max-width: 420px">
    <div class="card-body p-xl">
      <!-- Header -->
      <div class="text-center mb-xl">
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
            <path
              d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 1 1-7.778 7.778 5.5 5.5 0 0 1 7.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"
            />
          </svg>
        </div>
        <h1 class="text-2xl font-bold text-gray-900 mb-sm">Reset Password</h1>
        <p class="text-secondary" id="emailText">
          Enter the code we sent to your email and create a new password.
        </p>
      </div>

      <!-- Reset Form -->
      <form id="resetForm">
        <input type="hidden" id="email" name="email" />

        <div class="form-group">
          <label class="form-label" for="code">Reset Code</label>
          <input
            type="text"
            id="code"
            name="code"
            class="form-input text-center"
            placeholder="Enter 6-digit code"
            maxlength="6"
            required
          />
        </div>

        <div class="form-group">
          <label class="form-label" for="password">New Password</label>
          <input
            type="password"
            id="password"
            name="password"
            class="form-input"
            placeholder="Enter new password"
            required
          />
          <div class="form-hint">
            At least 8 characters with uppercase, lowercase, number, and special
            character
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="password_confirmation"
            >Confirm Password</label
          >
          <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            class="form-input"
            placeholder="Confirm new password"
            required
          />
        </div>

        <button
          type="submit"
          class="btn btn-primary btn-block btn-lg"
          id="resetBtn"
        >
          Reset Password
        </button>
      </form>

      <!-- Back to login -->
      <p class="text-center mt-lg text-sm">
        <a href="#/login">← Back to login</a>
      </p>
    </div>
  </div>
</div>

<script>
  (function () {
    const resetForm = Utils.$("#resetForm");
    const emailInput = Utils.$("#email");
    const emailText = Utils.$("#emailText");

    // Get email from session storage
    const email = sessionStorage.getItem("reset_email");

    if (!email) {
      Utils.warning("Please request a password reset first");
      Router.navigate("/forgot-password");
      return;
    }

    emailInput.value = email;
    emailText.innerHTML = `Enter the code we sent to <strong>${Utils.escapeHtml(email)}</strong>`;

    resetForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      const formData = Utils.serializeForm(resetForm);

      // Validate
      if (formData.code.length !== 6) {
        Utils.error("Please enter a valid 6-digit code");
        return;
      }

      if (formData.password !== formData.password_confirmation) {
        Utils.showFormErrors(resetForm, {
          password_confirmation: "Passwords do not match",
        });
        return;
      }

      Utils.setButtonLoading("#resetBtn", true);
      Utils.clearFormErrors(resetForm);

      try {
        await Auth.resetPassword(
          email,
          formData.code,
          formData.password,
          formData.password_confirmation,
        );

        Utils.success(
          "Password reset successfully! Please login with your new password.",
        );

        // Clear stored email
        sessionStorage.removeItem("reset_email");

        // Redirect to login
        Router.navigate("/login");
      } catch (error) {
        if (error.errors) {
          Utils.showFormErrors(resetForm, error.errors);
        }
        Utils.error(error.message || "Failed to reset password");
      } finally {
        Utils.setButtonLoading("#resetBtn", false);
      }
    });
  })();
</script>
