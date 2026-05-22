<!-- Register Page - Multi-Step Alumni Registration (MINSU-Style) -->
<div class="page-wrapper">
  <div class="auth-card" style="max-width: 600px">
    <!-- Header Section with Gradient -->
    <div class="auth-header">
      <div class="auth-logo">
        <img
          src="/assets/images/logo.svg"
          alt="Alumni Logo"
          onerror="
            this.style.display = 'none';
            this.nextElementSibling.style.display = 'flex';
          "
        />
        <span class="auth-logo-text" style="display: none">AMS</span>
      </div>
      <h1 class="auth-title">Alumni Registration</h1>
      <p class="auth-subtitle">Join our alumni network today</p>
    </div>

    <div class="auth-body">
      <!-- Progress Steps -->
      <div class="step-indicator" id="progressSteps">
        <div class="step active" data-step="1">
          <span class="step-circle">1</span>
          <span class="step-label">Account</span>
        </div>
        <div class="step-line"></div>
        <div class="step" data-step="2">
          <span class="step-circle">2</span>
          <span class="step-label">Academic</span>
        </div>
        <div class="step-line"></div>
        <div class="step" data-step="3">
          <span class="step-circle">3</span>
          <span class="step-label">Personal</span>
        </div>
        <div class="step-line"></div>
        <div class="step" data-step="4">
          <span class="step-circle">4</span>
          <span class="step-label">Career</span>
        </div>
      </div>

      <!-- Error Alert -->
      <div
        id="registerError"
        class="alert alert-error mb-md"
        style="display: none"
      >
        <span id="registerErrorText"></span>
      </div>

      <!-- Register Form -->
      <form id="registerForm">
        <!-- Step 1: Account Information -->
        <div class="step-content active" id="step1">
          <h3 class="text-lg font-bold mb-md" style="color: var(--primary-900)">
            Account Information
          </h3>

          <div class="form-group">
            <label class="form-label" for="name">Full Name</label>
            <input
              type="text"
              id="name"
              name="name"
              class="auth-input"
              placeholder="Enter your full name"
              required
            />
          </div>

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
              <button
                type="button"
                class="password-toggle"
                data-target="password"
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
                data-target="password_confirmation"
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
        </div>

        <!-- Step 2: Academic Information -->
        <div class="step-content" id="step2">
          <h3 class="text-lg font-bold mb-md" style="color: var(--primary-900)">
            Academic Information
          </h3>

          <div class="form-group">
            <label class="form-label required" for="college_id">College</label>
            <select
              id="college_id"
              name="college_id"
              class="form-input"
              required
            >
              <option value="">Select College</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label required" for="program_id">Program</label>
            <select
              id="program_id"
              name="program_id"
              class="form-input"
              required
              disabled
            >
              <option value="">Select Program</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="section_id">Section</label>
            <select
              id="section_id"
              name="section_id"
              class="form-input"
              disabled
            >
              <option value="">Select Section (Optional)</option>
            </select>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label required" for="batch_year"
                >Batch Year</label
              >
              <input
                type="number"
                id="batch_year"
                name="batch_year"
                class="form-input"
                placeholder="e.g., 2020"
                min="1950"
                max="2100"
                required
              />
            </div>
            <div class="form-group">
              <label class="form-label" for="graduation_year"
                >Graduation Year</label
              >
              <input
                type="number"
                id="graduation_year"
                name="graduation_year"
                class="form-input"
                placeholder="e.g., 2024"
                min="1950"
                max="2100"
              />
            </div>
          </div>
        </div>

        <!-- Step 3: Personal Information -->
        <div class="step-content" id="step3">
          <h3 class="mb-md">Personal Information</h3>

          <div class="form-group">
            <label class="form-label" for="phone">Phone Number</label>
            <input
              type="tel"
              id="phone"
              name="phone"
              class="form-input"
              placeholder="+63 9XX XXX XXXX"
            />
          </div>

          <div class="form-group">
            <label class="form-label" for="address">Address</label>
            <input
              type="text"
              id="address"
              name="address"
              class="form-input"
              placeholder="Street Address"
            />
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="city">City</label>
              <input
                type="text"
                id="city"
                name="city"
                class="form-input"
                placeholder="City"
              />
            </div>
            <div class="form-group">
              <label class="form-label" for="state">State/Province</label>
              <input
                type="text"
                id="state"
                name="state"
                class="form-input"
                placeholder="State/Province"
              />
            </div>
          </div>

          <div class="form-row">
            <div class="form-group">
              <label class="form-label" for="country">Country</label>
              <input
                type="text"
                id="country"
                name="country"
                class="form-input"
                placeholder="Country"
                value="Philippines"
              />
            </div>
            <div class="form-group">
              <label class="form-label" for="postal_code">Postal Code</label>
              <input
                type="text"
                id="postal_code"
                name="postal_code"
                class="form-input"
                placeholder="Postal Code"
              />
            </div>
          </div>
        </div>

        <!-- Step 4: Career Information -->
        <div class="step-content" id="step4">
          <h3 class="mb-md">Career Information (Optional)</h3>

          <div class="form-group">
            <label class="form-label" for="company">Company/Organization</label>
            <input
              type="text"
              id="company"
              name="company"
              class="form-input"
              placeholder="Current employer"
            />
          </div>

          <div class="form-group">
            <label class="form-label" for="job_title">Job Title</label>
            <input
              type="text"
              id="job_title"
              name="job_title"
              class="form-input"
              placeholder="Your position"
            />
          </div>

          <div class="form-group">
            <label class="form-label" for="industry">Industry</label>
            <select id="industry" name="industry" class="form-input">
              <option value="">Select Industry</option>
              <option value="technology">Technology</option>
              <option value="healthcare">Healthcare</option>
              <option value="education">Education</option>
              <option value="finance">Finance/Banking</option>
              <option value="manufacturing">Manufacturing</option>
              <option value="retail">Retail</option>
              <option value="government">Government</option>
              <option value="nonprofit">Non-Profit</option>
              <option value="consulting">Consulting</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="bio">Short Bio</label>
            <textarea
              id="bio"
              name="bio"
              class="form-input"
              rows="3"
              placeholder="Tell us about yourself..."
            ></textarea>
          </div>

          <div class="form-group">
            <label class="form-check">
              <input
                type="checkbox"
                class="form-check-input"
                name="agree"
                required
              />
              <span class="form-check-label">
                I agree to the
                <a href="#/terms" target="_blank">Terms of Service</a> and
                <a href="#/privacy" target="_blank">Privacy Policy</a>
              </span>
            </label>
          </div>
        </div>

        <!-- Dynamic Form Fields -->
        <div id="dynamicFields"></div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between mt-lg gap-md">
          <button
            type="button"
            class="btn btn-secondary"
            id="prevBtn"
            style="display: none"
          >
            &larr; Previous
          </button>
          <button type="button" class="btn-auth flex-1" id="nextBtn">
            Next &rarr;
          </button>
          <button
            type="submit"
            class="btn-auth flex-1"
            id="submitBtn"
            style="display: none"
          >
            Create Account
          </button>
        </div>
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
          >Sign in</span
        >
      </p>
    </div>
  </div>
</div>

<style>
  .step-content {
    display: none;
  }
  .step-content.active {
    display: block;
  }
  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
  }
  @media (max-width: 480px) {
    .form-row {
      grid-template-columns: 1fr;
    }
  }
</style>

<script>
  // Registration Form Handler v2.0 - Updated for dev mode support
  (function () {
    const registerForm = Utils.$("#registerForm");
    const googleBtn = Utils.$("#googleRegisterBtn");
    const prevBtn = Utils.$("#prevBtn");
    const nextBtn = Utils.$("#nextBtn");
    const submitBtn = Utils.$("#submitBtn");

    let currentStep = 1;
    const totalSteps = 4;

    // Load colleges on init
    async function loadColleges() {
      try {
        const response = await API.organization.getColleges();
        if (response.success && response.data) {
          const select = Utils.$("#college_id");
          const options = response.data.map((c) => "<option value='" + c.id + "'>" + c.name + "</option>").join("");
          select.innerHTML = "<option value=''>Select College</option>" + options;
        }
      } catch (e) {
        console.error("Error loading colleges:", e);
      }
    }

    // Load programs when college changes
    Utils.$("#college_id").addEventListener("change", async function () {
      const collegeId = this.value;
      const programSelect = Utils.$("#program_id");
      const sectionSelect = Utils.$("#section_id");

      programSelect.innerHTML = "<option value=''>Select Program</option>";
      sectionSelect.innerHTML = "<option value=''>Select Section</option>";
      programSelect.disabled = !collegeId;
      sectionSelect.disabled = true;

      if (collegeId) {
        try {
          const response = await API.organization.getPrograms(collegeId);
          if (response.success && response.data) {
            const options = response.data.map((p) => "<option value='" + p.id + "'>" + p.name + "</option>").join("");
            programSelect.innerHTML = "<option value=''>Select Program</option>" + options;
            programSelect.disabled = false;
          }
        } catch (e) {
          console.error("Error loading programs:", e);
        }
      }
    });

    // Load sections when program changes
    Utils.$("#program_id").addEventListener("change", async function () {
      const programId = this.value;
      const sectionSelect = Utils.$("#section_id");

      sectionSelect.innerHTML = "<option value=''>Select Section (Optional)</option>";
      sectionSelect.disabled = !programId;

      if (programId) {
        try {
          const response = await API.organization.getSections(programId);
          if (response.success && response.data) {
            const options = response.data.map((s) => "<option value='" + s.id + "'>" + s.name + " (" + s.batch_year + ")</option>").join("");
            sectionSelect.innerHTML = "<option value=''>Select Section (Optional)</option>" + options;
            sectionSelect.disabled = false;
          }
        } catch (e) {
          console.error("Error loading sections:", e);
        }
      }
    });

    // Load dynamic form fields
    async function loadDynamicFields() {
      try {
        const response = await API.formFields.getAll();
        if (response.success && response.data && response.data.length) {
          const container = Utils.$("#dynamicFields");
          const fieldsHTML = response.data.map((field) => {
            let input = "";
            const required = field.is_required ? "required" : "";
            const requiredLabel = field.is_required ? "required" : "";
            const fieldId = "custom_" + field.id;
            const fieldName = "custom_fields[" + field.field_key + "]";

            switch (field.field_type) {
              case "text":
                input = "<input type='text' id='" + fieldId + "' name='" + fieldName + "' class='form-input' " + required + " />";
                break;
              case "email":
                input = "<input type='email' id='" + fieldId + "' name='" + fieldName + "' class='form-input' " + required + " />";
                break;
              case "number":
                input = "<input type='number' id='" + fieldId + "' name='" + fieldName + "' class='form-input' " + required + " />";
                break;
              case "textarea":
                input = "<textarea id='" + fieldId + "' name='" + fieldName + "' class='form-input' rows='3' " + required + "></textarea>";
                break;
              case "select":
                const options = field.options ? JSON.parse(field.options) : [];
                const optionsHTML = options.map((o) => "<option value='" + o + "'>" + o + "</option>").join("");
                input = "<select id='" + fieldId + "' name='" + fieldName + "' class='form-input' " + required + "><option value=''>Select...</option>" + optionsHTML + "</select>";
                break;
              case "date":
                input = "<input type='date' id='" + fieldId + "' name='" + fieldName + "' class='form-input' " + required + " />";
                break;
              default:
                input = "<input type='text' id='" + fieldId + "' name='" + fieldName + "' class='form-input' " + required + " />";
            }

            const hintHTML = field.placeholder ? "<div class='form-hint'>" + field.placeholder + "</div>" : "";
            return "<div class='form-group'><label class='form-label " + requiredLabel + "' for='" + fieldId + "'>" + field.label + "</label>" + input + hintHTML + "</div>";
          }).join("");
          
          container.innerHTML = "<h3 class='mb-md mt-lg'>Additional Information</h3>" + fieldsHTML;
        }
      } catch (e) {
        console.log("No dynamic fields or error:", e);
      }
    }

    function updateSteps() {
      // Update step indicators
      document.querySelectorAll(".step").forEach((step, index) => {
        const stepNum = index + 1;
        step.classList.remove("active", "completed");
        if (stepNum === currentStep) {
          step.classList.add("active");
        } else if (stepNum < currentStep) {
          step.classList.add("completed");
        }
      });

      // Show/hide step content
      document.querySelectorAll(".step-content").forEach((content, index) => {
        content.classList.toggle("active", index + 1 === currentStep);
      });

      // Show/hide buttons
      prevBtn.style.display = currentStep > 1 ? "block" : "none";
      nextBtn.style.display = currentStep < totalSteps ? "block" : "none";
      submitBtn.style.display = currentStep === totalSteps ? "block" : "none";
    }

    function validateStep(step) {
      const stepContent = document.querySelector("#step" + step);
      const inputs = stepContent.querySelectorAll(
        "input[required], select[required]",
      );
      let valid = true;

      inputs.forEach((input) => {
        if (!input.value.trim()) {
          input.classList.add("error");
          valid = false;
        } else {
          input.classList.remove("error");
        }
      });

      // Step 1: validate passwords match
      if (step === 1) {
        const password = Utils.$("#password").value;
        const confirm = Utils.$("#password_confirmation").value;
        if (password !== confirm) {
          Utils.error("Passwords do not match");
          valid = false;
        }
      }

      return valid;
    }

    nextBtn.addEventListener("click", () => {
      if (validateStep(currentStep)) {
        currentStep++;
        updateSteps();
      } else {
        Utils.error("Please fill in all required fields");
      }
    });

    prevBtn.addEventListener("click", () => {
      currentStep--;
      updateSteps();
    });

    // Handle form submission
    registerForm.addEventListener("submit", async (e) => {
      e.preventDefault();

      if (!validateStep(currentStep)) {
        Utils.error("Please fill in all required fields");
        return;
      }

      const formData = Utils.serializeForm(registerForm);

      Utils.setButtonLoading("#submitBtn", true);
      Utils.clearFormErrors(registerForm);

      try {
        const response = await Auth.register(formData);

        // Check if email verification is required
        if (response && response.data && response.data.requiresVerification) {
          Utils.success(
            "Registration successful! Please check your email for verification code.",
          );

          // Store email for verification page
          sessionStorage.setItem("verify_email", formData.email);

          // Redirect to verification
          Router.navigate("/verify-email");
        } else if (response && response.data && response.data.requiresProfileCompletion) {
          // Registration completed without email verification (dev mode)
          Utils.success("Registration successful! Please complete your profile.");
          
          // Redirect to complete profile or dashboard
          Router.navigate("/complete-profile");
        } else {
          // Registration completed
          Utils.success("Registration successful!");
          Router.navigate("/dashboard");
        }
      } catch (error) {
        if (error && error.errors) {
          Utils.showFormErrors(registerForm, error.errors);
        }
        Utils.error((error && error.message) || "Registration failed");
      } finally {
        Utils.setButtonLoading("#submitBtn", false);
      }
    });

    // Handle Google Sign Up
    googleBtn.addEventListener("click", async () => {
      const firebaseReady =
        typeof firebase !== "undefined" &&
        firebase.apps &&
        firebase.apps.length > 0 &&
        typeof firebase.auth === "function";

      if (!firebaseReady) {
        Utils.error(
          "Google Sign-Up is not configured. Please ask the administrator to complete Firebase settings.",
        );
        return;
      }

      Utils.setButtonLoading("#googleRegisterBtn", true);

      try {
        const provider = new firebase.auth.GoogleAuthProvider();
        provider.setCustomParameters({ prompt: "select_account" });
        const result = await firebase.auth().signInWithPopup(provider);
        const idToken = await result.user.getIdToken();

        const response = await Auth.googleLogin(idToken);

        Utils.success("Account created successfully!");
        
        // Check if profile needs to be completed
        const user = response.data.user;
        if (!user.profile_completed) {
          Router.navigate("/complete-profile");
        } else {
          Router.navigate("/dashboard");
        }
      } catch (error) {
        console.error("Google signup error:", error);
        Utils.error(error.message || "Google sign-up failed");
      } finally {
        Utils.setButtonLoading("#googleRegisterBtn", false);
      }
    });

    // Initialize
    loadColleges();
    loadDynamicFields();
    updateSteps();
  })();
</script>
