<!-- Register Page - Simple Email/Password Registration -->
<div class="page-wrapper">
  <div class="auth-card">
    <!-- Header Section with Gradient -->
    <div class="auth-header">
      <div class="auth-logo">
        <img
          src="/assets/images/logo.svg"
          alt="MINSU Alumni"
          onerror="
            this.style.display = 'none';
            this.nextElementSibling.style.display = 'flex';
          "
        />
        <span class="auth-logo-text" style="display: none">MINSU</span>
      </div>
      <h1 class="auth-title">Join Alumni Network</h1>
      <p class="auth-subtitle">Create your account in seconds</p>
    </div>

    <div class="auth-body">
      <!-- Error Alert -->
      <div
        id="registerError"
        class="alert alert-error mb-md"
        style="display: none"
      >
        <span id="registerErrorText"></span>
      </div>

      <!-- Success Alert -->
      <div
        id="registerSuccess"
        class="alert alert-success mb-md"
        style="display: none"
      >
        <span id="registerSuccessText"></span>
      </div>

      <!-- Register Form -->
      <form id="registerForm">
        <div class="form-group">
          <label class="form-label" for="email">Email Address</label>
          <input
            type="email"
            id="email"
            name="email"
            class="auth-input"
            placeholder="Enter your email"
            required
          />
          <div class="form-hint">
            We'll send a verification code to this email
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Password</label>
          <div class="input-password-wrapper">
            <input
              type="password"
              id="password"
              name="password"
              class="auth-input"
              placeholder="Create a password"
              required
            />
            <button type="button" class="password-toggle" id="togglePassword">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                <circle cx="12" cy="12" r="3" />
              </svg>
            </button>
          </div>
          <div class="form-hint">
            Min 8 chars: upper, lower, number, and symbol.
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="password_confirmation"
            >Confirm Password</label
          >
          <div class="input-password-wrapper">
            <input
              type="password"
              id="password_confirmation"
              name="password_confirmation"
              class="auth-input"
              placeholder="Confirm your password"
              required
            />
            <button
              type="button"
              class="password-toggle"
              id="toggleConfirmPassword"
            >
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                <circle cx="12" cy="12" r="3" />
              </svg>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-auth mt-md" id="registerBtn">
          Create Account
        </button>
      </form>

      <!-- Divider -->
      <div class="auth-divider">
        <span>or continue with</span>
      </div>

      <!-- Google Sign Up -->
      <button type="button" class="btn-google" id="googleRegisterBtn">
        <svg width="18" height="18" viewBox="0 0 24 24">
          <path
            fill="#4285F4"
            d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
          />
          <path
            fill="#34A853"
            d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
          />
          <path
            fill="#FBBC05"
            d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
          />
          <path
            fill="#EA4335"
            d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
          />
        </svg>
        Sign up with Google
      </button>

      <!-- Login Link -->
      <p class="auth-toggle">
        Already have an account?
        <span class="auth-toggle-link" onclick="Router.navigate('/login')"
          >Sign In</span
        >
      </p>
    </div>
  </div>
</div>

<script>
  // Registration Form Handler - Simplified version
  (function () {
    console.log("Register page script loaded");
    
    const form = Utils.$("#registerForm");
    const errorDiv = Utils.$("#registerError");
    const errorText = Utils.$("#registerErrorText");
    const successDiv = Utils.$("#registerSuccess");
    const successText = Utils.$("#registerSuccessText");
    const googleBtn = Utils.$("#googleRegisterBtn");

    console.log("Form element:", form);

    // Password toggles
    const togglePassword = Utils.$("#togglePassword");
    const toggleConfirmPassword = Utils.$("#toggleConfirmPassword");
    const passwordInput = Utils.$("#password");
    const confirmPasswordInput = Utils.$("#password_confirmation");

    function togglePasswordVisibility(input, button) {
      const type = input.type === "password" ? "text" : "password";
      input.type = type;
      button.innerHTML =
        type === "password"
          ? '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>'
          : '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
    }

    togglePassword.addEventListener("click", () =>
      togglePasswordVisibility(passwordInput, togglePassword),
    );
    toggleConfirmPassword.addEventListener("click", () =>
      togglePasswordVisibility(confirmPasswordInput, toggleConfirmPassword),
    );

    function showError(message) {
      errorText.textContent = message;
      errorDiv.style.display = "flex";
      successDiv.style.display = "none";
    }

    function showSuccess(message) {
      successText.textContent = message;
      successDiv.style.display = "flex";
      errorDiv.style.display = "none";
    }

    function hideAlerts() {
      errorDiv.style.display = "none";
      successDiv.style.display = "none";
    }

    function isFirebaseReady() {
      return (
        typeof firebase !== "undefined" &&
        firebase.apps &&
        firebase.apps.length > 0 &&
        typeof firebase.auth === "function"
      );
    }

    function deriveNameFromEmail(email) {
      const rawLocalPart = String(email || "").split("@")[0] || "";
      const cleaned = rawLocalPart
        .replace(/[._-]+/g, " ")
        .replace(/[^a-zA-Z0-9 ]+/g, " ")
        .replace(/\s+/g, " ")
        .trim();

      if (!cleaned || cleaned.length < 2) {
        return "Alumni User";
      }

      return cleaned
        .split(" ")
        .filter(Boolean)
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join(" ")
        .slice(0, 255);
    }

    // Form submission
    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      console.log("Form submitted!");
      hideAlerts();

      const registerBtn = Utils.$("#registerBtn");
      const email = Utils.$("#email").value.trim();
      const password = Utils.$("#password").value;
      const passwordConfirmation = Utils.$("#password_confirmation").value;

      console.log("Form data:", { email, passwordLength: password.length });

      // Basic validation
      if (!email || !password || !passwordConfirmation) {
        showError("Please fill in all fields");
        return;
      }

      if (password !== passwordConfirmation) {
        showError("Passwords do not match");
        return;
      }

      if (password.length < 8) {
        showError("Password must be at least 8 characters");
        return;
      }

      const name = deriveNameFromEmail(email);
      
      console.log("Starting registration for:", email);
      Utils.setButtonLoading(registerBtn, true);

      try {
        const response = await Auth.register({
          name: name,
          email: email,
          password: password,
          password_confirmation: passwordConfirmation,
        });

        console.log("Registration response:", response);

        // Check if email verification is required
        if (response && response.data && response.data.requiresVerification) {
          showSuccess(
            "Registration successful! Please check your email for verification code.",
          );

          // Store email for verification page
          sessionStorage.setItem("verify_email", email);

          // Redirect to verification after delay
          setTimeout(() => {
            Router.navigate("/verify-email");
          }, 2000);
        } else if (response && response.data && response.data.requiresProfileCompletion) {
          // Registration completed without email verification (dev mode)
          showSuccess("Registration successful! Redirecting to complete your profile...");
          
          // Redirect to complete profile
          setTimeout(() => {
            Router.navigate("/complete-profile");
          }, 1500);
        } else {
          // Registration completed
          showSuccess("Registration successful! Redirecting...");
          setTimeout(() => {
            Router.navigate("/dashboard");
          }, 1500);
        }
      } catch (error) {
        console.error("Registration error:", error);
        console.error("Error details:", JSON.stringify(error, null, 2));
        
        // Show specific validation errors if available
        if (error.errors) {
          const errorMessages = Object.values(error.errors).flat().join(", ");
          showError(errorMessages || error.message || "Registration failed. Please try again.");
        } else {
          showError(error.message || "Registration failed. Please try again.");
        }
      } finally {
        Utils.setButtonLoading(registerBtn, false);
      }
    });

    // Google Sign Up
    googleBtn.addEventListener("click", async () => {
      if (!isFirebaseReady()) {
        showError(
          "Google Sign-In is not configured. Please ask the administrator to complete Firebase settings.",
        );
        return;
      }

      Utils.setButtonLoading("#googleRegisterBtn", true);
      hideAlerts();

      try {
        const provider = new firebase.auth.GoogleAuthProvider();
        provider.setCustomParameters({ prompt: "select_account" });
        const result = await firebase.auth().signInWithPopup(provider);
        const idToken = await result.user.getIdToken();

        const response = await Auth.googleLogin(idToken);

        Utils.success(
          response.data.isNewUser ? "Account created!" : "Welcome back!",
        );

        const user = response.data.user;

        // Check if profile needs to be completed
        if (!user.profile_completed) {
          Router.navigate("/complete-profile");
        } else if (["admin", "system_admin", "campus_admin", "staff"].includes(user.role)) {
          Router.navigate("/admin/dashboard");
        } else {
          Router.navigate("/dashboard");
        }
      } catch (error) {
        console.error("Google signup error:", error);
        showError(error.message || "Google sign-up failed");
      } finally {
        Utils.setButtonLoading("#googleRegisterBtn", false);
      }
    });
  })();
</script>
