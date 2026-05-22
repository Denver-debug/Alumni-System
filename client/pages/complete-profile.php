<!-- Complete Profile Page - Multi-Step Alumni Profile Wizard -->
<div class="page-wrapper" style="padding: var(--spacing-lg)">
  <div class="profile-wizard" role="dialog" aria-modal="true" aria-labelledby="profileWizardTitle">
    <!-- Header -->
    <div class="wizard-header">
      <div
        class="auth-logo"
        style="width: 80px; height: 80px; margin: 0 auto var(--spacing-md)"
      >
        <img
          src="/assets/images/logo.svg"
          alt="MINSU Alumni"
          onerror="
            this.style.display = 'none';
            this.nextElementSibling.style.display = 'flex';
          "
        />
        <span class="auth-logo-text" style="display: none; font-size: 1.5rem"
          >MINSU</span
        >
      </div>
      <h1 id="profileWizardTitle" style="color: var(--primary-800); margin-bottom: var(--spacing-xs)">
        Complete Your Profile
      </h1>
        <p style="color: var(--gray-600)">
        Complete every required detail so your alumni profile can be verified
      </p>

      <!-- Points Badge -->
      <div
        class="points-badge"
        style="
          display: inline-flex;
          align-items: center;
          gap: 8px;
          background: linear-gradient(
            135deg,
            var(--primary-100),
            var(--primary-200)
          );
          padding: 8px 16px;
          border-radius: 20px;
          margin-top: var(--spacing-md);
        "
      >
        <span style="font-size: 1.2rem">🏆</span>
        <span style="color: var(--primary-800); font-weight: 600"
          >Earn 50 points for completing your profile!</span
        >
      </div>
    </div>

    <!-- Progress Bar -->
    <div class="wizard-progress">
      <div class="progress-bar">
        <div class="progress-fill" id="progressFill" style="width: 25%"></div>
      </div>
      <div class="progress-steps">
        <div class="progress-step active" data-step="1">
          <div class="step-dot"></div>
          <span>Academic</span>
        </div>
        <div class="progress-step" data-step="2">
          <div class="step-dot"></div>
          <span>Personal</span>
        </div>
        <div class="progress-step" data-step="3">
          <div class="step-dot"></div>
          <span>Employment</span>
        </div>
        <div class="progress-step" data-step="4">
          <div class="step-dot"></div>
          <span>Social</span>
        </div>
      </div>
    </div>

    <!-- Alert -->
    <div id="wizardAlert" class="alert mb-md" style="display: none"></div>

    <!-- Form -->
    <form id="profileWizardForm" novalidate>
      <!-- Step 1: Academic Information -->
      <div class="wizard-step active" id="wizardStep1">
        <h2 class="step-title">🎓 Academic Information</h2>
        <p class="step-description">
          Tell us about your academic background at MINSU
        </p>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label required">Campus</label>
            <select
              id="campus_id"
              name="campus_id"
              class="form-input"
              required
            >
              <option value="">Select Campus</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label required">College</label>
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
            <label class="form-label required">Program</label>
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
            <label class="form-label required">Section</label>
            <select
              id="section_id"
              name="section_id"
              class="form-input"
              required
              disabled
            >
              <option value="">Select Section</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label required">Student ID</label>
            <input
              type="text"
              id="student_id"
              name="student_id"
              class="form-input"
              placeholder="e.g., 2020-12345"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label required">Batch Year</label>
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
            <label class="form-label required">Graduation Year</label>
            <input
              type="number"
              id="graduation_year"
              name="graduation_year"
              class="form-input"
              placeholder="e.g., 2024"
              min="1950"
              max="2100"
              required
            />
          </div>
        </div>

        <div class="custom-fields-block" id="customFieldsStep1" style="display: none">
          <h3 class="custom-fields-title">Additional Academic Details</h3>
          <div class="form-grid" id="customFieldsStep1Grid"></div>
        </div>
      </div>

      <!-- Step 2: Personal Information -->
      <div class="wizard-step" id="wizardStep2">
        <h2 class="step-title">👤 Personal Information</h2>
        <p class="step-description">Help us know you better</p>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label required">First Name</label>
            <input
              type="text"
              id="first_name"
              name="first_name"
              class="form-input"
              placeholder="First Name"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label required">Middle Name</label>
            <input
              type="text"
              id="middle_name"
              name="middle_name"
              class="form-input"
              placeholder="Middle Name"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label required">Last Name</label>
            <input
              type="text"
              id="last_name"
              name="last_name"
              class="form-input"
              placeholder="Last Name"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label">Suffix</label>
            <input
              type="text"
              id="suffix"
              name="suffix"
              class="form-input"
              placeholder="Jr., Sr., III, etc."
            />
          </div>

          <div class="form-group">
            <label class="form-label required">Gender</label>
            <select id="gender" name="gender" class="form-input" required>
              <option value="">Select Gender</option>
              <option value="male">Male</option>
              <option value="female">Female</option>
              <option value="other">Other</option>
              <option value="prefer_not_to_say">Prefer not to say</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label required">Birthdate</label>
            <input
              type="date"
              id="birthdate"
              name="birthdate"
              class="form-input"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label required">Civil Status</label>
            <select id="civil_status" name="civil_status" class="form-input" required>
              <option value="">Select Status</option>
              <option value="single">Single</option>
              <option value="married">Married</option>
              <option value="widowed">Widowed</option>
              <option value="separated">Separated</option>
              <option value="divorced">Divorced</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label required">Phone Number</label>
            <input
              type="tel"
              id="mobile"
              name="mobile"
              class="form-input"
              placeholder="+63 9XX XXX XXXX"
              required
            />
          </div>

          <div class="form-group full-width">
            <label class="form-label required">Address</label>
            <input
              type="text"
              id="address_street"
              name="address_street"
              class="form-input"
              placeholder="Street Address"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label required">City</label>
            <input
              type="text"
              id="address_city"
              name="address_city"
              class="form-input"
              placeholder="City"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label required">Province</label>
            <input
              type="text"
              id="address_province"
              name="address_province"
              class="form-input"
              placeholder="Province"
              required
            />
          </div>
        </div>

        <div class="custom-fields-block" id="customFieldsStep2" style="display: none">
          <h3 class="custom-fields-title">Additional Personal Details</h3>
          <div class="form-grid" id="customFieldsStep2Grid"></div>
        </div>
      </div>

      <!-- Step 3: Employment Information -->
      <div class="wizard-step" id="wizardStep3">
        <h2 class="step-title">💼 Employment Information</h2>
        <p class="step-description">Tell us about your career</p>

        <div class="form-grid">
          <div class="form-group">
            <label class="form-label required">Employment Status</label>
            <select
              id="employment_status"
              name="employment_status"
              class="form-input"
              required
            >
              <option value="">Select Status</option>
              <option value="employed">Employed</option>
              <option value="self_employed">Self-Employed</option>
              <option value="unemployed">Unemployed</option>
              <option value="student">Student</option>
              <option value="retired">Retired</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label required">Industry</label>
            <select id="industry" name="industry" class="form-input" required>
              <option value="">Select Industry</option>
              <option value="technology">Technology</option>
              <option value="education">Education</option>
              <option value="healthcare">Healthcare</option>
              <option value="finance">Finance</option>
              <option value="government">Government</option>
              <option value="manufacturing">Manufacturing</option>
              <option value="retail">Retail</option>
              <option value="construction">Construction</option>
              <option value="agriculture">Agriculture</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label required">Current Employer</label>
            <input
              type="text"
              id="current_employer"
              name="current_employer"
              class="form-input"
              placeholder="Company Name"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label required">Job Title</label>
            <input
              type="text"
              id="job_title"
              name="job_title"
              class="form-input"
              placeholder="Your Position"
              required
            />
          </div>

          <div class="form-group full-width">
            <label class="form-label required">Company Address</label>
            <input
              type="text"
              id="company_address"
              name="company_address"
              class="form-input"
              placeholder="Company Address"
              required
            />
          </div>

          <div class="form-group">
            <label class="form-label required">Monthly Salary Range</label>
            <select
              id="monthly_salary_range"
              name="monthly_salary_range"
              class="form-input"
              required
            >
              <option value="">Select Salary Range</option>
              <option value="prefer_not_to_say">Prefer not to say</option>
              <option value="below_15000">Below ₱15,000</option>
              <option value="15000_25000">₱15,000 - ₱25,000</option>
              <option value="25000_40000">₱25,000 - ₱40,000</option>
              <option value="40000_60000">₱40,000 - ₱60,000</option>
              <option value="60000_100000">₱60,000 - ₱100,000</option>
              <option value="above_100000">Above ₱100,000</option>
            </select>
          </div>
        </div>

        <div class="custom-fields-block" id="customFieldsStep3" style="display: none">
          <h3 class="custom-fields-title">Additional Employment Details</h3>
          <div class="form-grid" id="customFieldsStep3Grid"></div>
        </div>
      </div>

      <!-- Step 4: Social Links -->
      <div class="wizard-step" id="wizardStep4">
        <h2 class="step-title">🌐 Social Links</h2>
        <p class="step-description">
          Connect with fellow alumni on social media (optional)
        </p>

        <div class="form-grid">
          <div class="form-group full-width">
            <label class="form-label">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="#0077B5"
                style="vertical-align: middle; margin-right: 8px"
              >
                <path
                  d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"
                />
              </svg>
              LinkedIn Profile
            </label>
            <input
              type="url"
              id="linkedin_url"
              name="linkedin_url"
              class="form-input"
              placeholder="https://linkedin.com/in/yourprofile"
            />
          </div>

          <div class="form-group full-width">
            <label class="form-label">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="#1877F2"
                style="vertical-align: middle; margin-right: 8px"
              >
                <path
                  d="M12 2.04C6.5 2.04 2 6.53 2 12.06C2 17.06 5.66 21.21 10.44 21.96V14.96H7.9V12.06H10.44V9.85C10.44 7.34 11.93 5.96 14.22 5.96C15.31 5.96 16.45 6.15 16.45 6.15V8.62H15.19C13.95 8.62 13.56 9.39 13.56 10.18V12.06H16.34L15.89 14.96H13.56V21.96A10 10 0 0 0 22 12.06C22 6.53 17.5 2.04 12 2.04Z"
                />
              </svg>
              Facebook Profile
            </label>
            <input
              type="url"
              id="facebook_url"
              name="facebook_url"
              class="form-input"
              placeholder="https://facebook.com/yourprofile"
            />
          </div>

          <div class="form-group full-width">
            <label class="form-label">
              <svg
                width="20"
                height="20"
                viewBox="0 0 24 24"
                fill="#E4405F"
                style="vertical-align: middle; margin-right: 8px"
              >
                <path
                  d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8A1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.25-1.25M12 7a5 5 0 0 1 5 5a5 5 0 0 1-5 5a5 5 0 0 1-5-5a5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3a3 3 0 0 0 3 3a3 3 0 0 0 3-3a3 3 0 0 0-3-3z"
                />
              </svg>
              Instagram Profile
            </label>
            <input
              type="url"
              id="instagram_url"
              name="instagram_url"
              class="form-input"
              placeholder="https://instagram.com/yourprofile"
            />
          </div>
        </div>

        <div class="custom-fields-block" id="customFieldsStep4" style="display: none">
          <h3 class="custom-fields-title">Additional Social Details</h3>
          <div class="form-grid" id="customFieldsStep4Grid"></div>
        </div>

        <!-- Summary -->
        <div
          class="wizard-summary"
          style="
            margin-top: var(--spacing-xl);
            padding: var(--spacing-lg);
            background: var(--gray-50);
            border-radius: var(--radius-lg);
          "
        >
          <h3
            style="color: var(--primary-800); margin-bottom: var(--spacing-sm)"
          >
            🎉 Almost Done!
          </h3>
          <p style="color: var(--gray-600)">
            By completing your profile, you'll:
          </p>
          <ul
            style="
              color: var(--gray-600);
              margin: var(--spacing-sm) 0;
              padding-left: var(--spacing-lg);
            "
          >
            <li>Earn <strong>50 points</strong> towards your badge</li>
            <li>Connect with alumni from your program</li>
            <li>Receive relevant event notifications</li>
            <li>Get your official Alumni ID</li>
          </ul>
        </div>
      </div>

      <!-- Navigation Buttons -->
      <div class="wizard-nav">
        <button
          type="button"
          class="btn btn-secondary"
          id="prevBtn"
          style="display: none"
        >
          ← Previous
        </button>
        <div style="flex: 1"></div>
        <button type="button" class="btn btn-primary" id="nextBtn">
          Next →
        </button>
        <button
          type="submit"
          class="btn btn-primary"
          id="submitBtn"
          style="display: none"
        >
          Complete Profile ✓
        </button>
      </div>
    </form>
  </div>
</div>

<style>
  .page-wrapper {
    padding: var(--spacing-md) !important;
    min-height: 100vh;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    overflow-y: auto;
  }

  .profile-wizard {
    max-width: 700px;
    width: 100%;
    margin: var(--spacing-lg) auto;
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    overflow: visible;
    display: flex;
    flex-direction: column;
  }

  .wizard-header {
    text-align: center;
    padding: var(--spacing-lg) var(--spacing-lg);
    background: linear-gradient(135deg, var(--primary-50), var(--primary-100));
    border-bottom: 1px solid var(--primary-200);
    flex-shrink: 0;
  }

  .wizard-header .auth-logo {
    width: 60px !important;
    height: 60px !important;
    margin: 0 auto var(--spacing-sm) !important;
  }

  .wizard-header h1 {
    font-size: 1.5rem !important;
    margin-bottom: var(--spacing-xs) !important;
  }

  .wizard-header p {
    font-size: 0.875rem !important;
    margin-bottom: 0 !important;
  }

  .points-badge {
    display: inline-flex !important;
    align-items: center !important;
    gap: 8px !important;
    background: linear-gradient(135deg, var(--primary-100), var(--primary-200)) !important;
    padding: 8px 16px !important;
    border-radius: 20px !important;
    margin-top: var(--spacing-sm) !important;
    font-size: 0.875rem !important;
  }

  .points-badge span:first-child {
    font-size: 1.1rem !important;
  }

  .wizard-progress {
    padding: var(--spacing-md) var(--spacing-lg);
    background: white;
    flex-shrink: 0;
  }

  .progress-bar {
    height: 4px;
    background: var(--gray-200);
    border-radius: 2px;
    overflow: hidden;
    margin-bottom: var(--spacing-sm);
  }

  .progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-500), var(--primary-600));
    transition: width 0.3s ease;
  }

  .progress-steps {
    display: flex;
    justify-content: space-between;
  }

  .progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 3px;
    color: var(--gray-400);
    font-size: 0.75rem;
  }

  .progress-step.active {
    color: var(--primary-600);
  }

  .progress-step.completed {
    color: var(--primary-700);
  }

  .step-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--gray-300);
    transition: all 0.3s ease;
  }

  .progress-step.active .step-dot {
    background: var(--primary-500);
    box-shadow: 0 0 0 3px var(--primary-100);
  }

  .progress-step.completed .step-dot {
    background: var(--primary-600);
  }

  .wizard-step {
    display: none;
    padding: var(--spacing-lg) var(--spacing-lg);
    animation: fadeIn 0.3s ease;
  }

  .wizard-step.active {
    display: block;
  }

  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateY(10px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .step-title {
    color: var(--primary-800);
    font-size: 1.25rem;
    margin-bottom: var(--spacing-xs);
  }

  .step-description {
    color: var(--gray-600);
    font-size: 0.875rem;
    margin-bottom: var(--spacing-lg);
  }

  .form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: var(--spacing-md);
  }

  .custom-fields-block {
    margin-top: var(--spacing-lg);
    padding-top: var(--spacing-md);
    border-top: 1px dashed var(--gray-200);
  }

  .custom-fields-title {
    font-size: 1rem;
    color: var(--primary-700);
    margin-bottom: var(--spacing-sm);
  }

  .form-group.full-width {
    grid-column: 1 / -1;
  }

  .form-group {
    margin-bottom: 0;
  }

  .form-group .form-label {
    font-size: 0.875rem;
    margin-bottom: var(--spacing-xs);
  }

  .form-group .form-input {
    padding: 10px 12px;
    font-size: 0.875rem;
  }

  .form-group .form-input.error {
    border-color: var(--error-500, #ef4444);
    background-color: var(--error-50, #fef2f2);
  }

  .form-group .form-input.error:focus {
    outline-color: var(--error-500, #ef4444);
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
  }

  .form-group.full-width {
    grid-column: 1 / -1;
  }

  .wizard-summary {
    margin-top: var(--spacing-lg) !important;
    padding: var(--spacing-lg) !important;
    background: var(--gray-50) !important;
    border-radius: var(--radius-lg) !important;
  }

  .wizard-summary h3 {
    font-size: 1rem !important;
    margin-bottom: var(--spacing-sm) !important;
  }

  .wizard-summary p,
  .wizard-summary ul {
    font-size: 0.875rem !important;
    margin: var(--spacing-sm) 0 !important;
  }

  .wizard-summary li {
    margin-bottom: var(--spacing-xs);
  }

  .wizard-nav {
    display: flex;
    gap: var(--spacing-sm);
    padding: var(--spacing-lg) var(--spacing-lg);
    background: var(--gray-50);
    border-top: 1px solid var(--gray-200);
    flex-shrink: 0;
  }

  .wizard-nav .btn {
    padding: 12px 20px;
    font-size: 0.875rem;
  }

  #wizardAlert {
    margin: 0 var(--spacing-lg) var(--spacing-md) var(--spacing-lg);
    font-size: 0.875rem;
    padding: 12px 16px;
  }

  @media (max-width: 768px) {
    .page-wrapper {
      padding: var(--spacing-sm) !important;
    }

    .profile-wizard {
      margin: var(--spacing-sm) auto;
      border-radius: var(--radius-md);
    }

    .form-grid {
      grid-template-columns: 1fr;
    }

    .wizard-nav {
      flex-wrap: wrap;
    }

    .wizard-nav button {
      flex: 1;
      min-width: 120px;
    }

    .progress-step span {
      font-size: 0.7rem;
    }

    .wizard-header .auth-logo {
      width: 50px !important;
      height: 50px !important;
    }

    .wizard-header h1 {
      font-size: 1.25rem !important;
    }
  }
</style>

<script>
  (async function () {
    let currentStep = 1;
    const totalSteps = 4;

    const form = Utils.$("#profileWizardForm");
    const alertDiv = Utils.$("#wizardAlert");
    const prevBtn = Utils.$("#prevBtn");
    const nextBtn = Utils.$("#nextBtn");
    const submitBtn = Utils.$("#submitBtn");
    const progressFill = Utils.$("#progressFill");
    let customFields = [];

    // Check if user is logged in
    if (!Auth.isAuthenticated()) {
      Router.navigate("/login");
      return;
    }

    // Check if profile is already completed by fetching fresh data
    try {
      const profileResponse = await API.auth.getProfile();
      const userData = profileResponse.data.user;
      
      // Check if profile_completed is true (1 or true)
      if (userData && (userData.profile_completed === true || userData.profile_completed === 1 || userData.profile_completed === '1')) {
        // Profile already completed, redirect to dashboard
        console.log('Profile already completed, redirecting to dashboard');
        Utils.info("Your profile is already complete!");
        setTimeout(() => {
          Router.navigate("/dashboard");
        }, 1000);
        return;
      }
    } catch (error) {
      console.error("Failed to check profile status:", error);
      // Continue to form if check fails
    }

    const user = Auth.user;

    // Load campus and college selectors on init - wait for them to complete
    try {
      await loadCampuses();
      await loadColleges();
    } catch (error) {
      console.error("Failed to load dropdowns:", error);
    }

    // Pre-fill name if available
    if (user && user.name) {
      const nameParts = user.name.split(" ");
      Utils.$("#first_name").value = nameParts[0] || "";
      Utils.$("#last_name").value = nameParts.slice(1).join(" ") || "";
    }

    // Load existing profile data and determine current step
    await loadExistingProfile();
    await loadCustomFields();

    async function loadExistingProfile() {
      try {
        const response = await API.auth.getProfile();
        const user = response.data.user;

        if (user) {
          // Populate form fields with existing data
          const fieldMapping = {
            // Step 1 - Academic
            'campus_id': user.campus_id,
            'college_id': user.college_id,
            'program_id': user.program_id,
            'section_id': user.section_id,
            'student_id': user.student_id,
            'batch_year': user.batch_year,
            'graduation_year': user.graduation_year,
            // Step 2 - Personal
            'first_name': user.first_name,
            'middle_name': user.middle_name,
            'last_name': user.last_name,
            'suffix': user.suffix,
            'gender': user.gender,
            'birthdate': user.birthdate,
            'civil_status': user.civil_status,
            'mobile': user.mobile,
            'address_street': user.address_street,
            'address_city': user.address_city,
            'address_province': user.address_province,
            // Step 3 - Employment
            'employment_status': user.employment_status,
            'industry': user.industry,
            'current_employer': user.current_employer,
            'job_title': user.job_title,
            'company_address': user.company_address,
            'monthly_salary_range': user.monthly_salary_range,
            // Step 4 - Social
            'linkedin_url': user.linkedin_url,
            'facebook_url': user.facebook_url,
            'instagram_url': user.instagram_url
          };

          Object.keys(fieldMapping).forEach(key => {
            const field = Utils.$(`#${key}`);
            if (field && fieldMapping[key]) {
              field.value = fieldMapping[key];
            }
          });

          // Determine which step to start from based on completed data
          if (user.section_id && user.batch_year) {
            // Step 1 complete
            if (user.first_name && user.address_city) {
              // Step 2 complete
              if (user.employment_status && user.current_employer) {
                // Step 3 complete
                currentStep = 4; // Go to step 4 (social links)
              } else {
                currentStep = 3; // Go to step 3 (employment)
              }
            } else {
              currentStep = 2; // Go to step 2 (personal)
            }
          } else {
            currentStep = 1; // Start from step 1 (academic)
          }

          // Trigger cascading loads for dropdowns if data exists
          if (user.campus_id && user.college_id) {
            // Wait for programs to load
            await loadProgramsForCampusAndCollege();
            
            if (user.program_id) {
              // Wait for sections to load
              const programId = user.program_id;
              const campusId = user.campus_id;
              const sectionSelect = Utils.$("#section_id");

              sectionSelect.innerHTML = '<option value="">Select Section</option>';
              sectionSelect.disabled = !programId || !campusId;

              if (programId && campusId) {
                try {
                  const response = await API.get("/sections", {
                    program_id: programId,
                    campus_id: campusId,
                  });
                  if (response.data && response.data.length > 0) {
                    response.data.forEach((section) => {
                      sectionSelect.innerHTML += `<option value="${section.id}">${Utils.escapeHtml(section.name)} (Batch ${Utils.escapeHtml(section.batch_year)})</option>`;
                    });
                    
                    // Set the selected section
                    if (user.section_id) {
                      sectionSelect.value = String(user.section_id);
                    }
                  }
                } catch (error) {
                  console.error("Failed to load sections:", error);
                }
              }
            }
          }

          updateProgress();
        }
      } catch (error) {
        console.error("Failed to load existing profile:", error);
        // Continue with empty form
        currentStep = 1;
        updateProgress();
      }
    }

    async function loadCustomFields() {
      try {
        const response = await API.formBuilder.getFields({ is_active: true });
        const fields = Array.isArray(response?.data) ? response.data : [];
        customFields = fields.filter((field) => !Number(field.is_builtin));
      } catch (error) {
        console.error("Failed to load custom fields:", error);
        customFields = [];
      }

      renderCustomFields();
    }

    function getCustomFieldStep(field) {
      const section = String(field.form_section_key || field.form_section || "general").toLowerCase();

      if (section === "education") return 1;
      if (section === "employment") return 3;
      if (section === "social") return 4;
      return 2;
    }

    function getColumnClass(field) {
      const width = String(field.column_width || "").toLowerCase();
      return width === "100%" || width === "full" ? "full-width" : "";
    }

    function renderCustomField(field) {
      const fieldId = `custom_${field.id}`;
      const label = Utils.escapeHtml(field.field_label || field.field_name || "");
      const requiredAttr = field.is_required ? "required" : "";
      const requiredClass = field.is_required ? "required" : "";
      const columnClass = getColumnClass(field);
      const options = Array.isArray(field.field_options) ? field.field_options : [];
      let inputHtml = "";

      switch (field.field_type) {
        case "email":
        case "number":
        case "url":
          inputHtml = `<input type="${field.field_type}" id="${fieldId}" name="${field.field_name}" class="form-input" ${requiredAttr} />`;
          break;
        case "phone":
          inputHtml = `<input type="tel" id="${fieldId}" name="${field.field_name}" class="form-input" ${requiredAttr} />`;
          break;
        case "date":
          inputHtml = `<input type="date" id="${fieldId}" name="${field.field_name}" class="form-input" ${requiredAttr} />`;
          break;
        case "textarea":
          inputHtml = `<textarea id="${fieldId}" name="${field.field_name}" class="form-input" rows="3" ${requiredAttr}></textarea>`;
          break;
        case "select":
          inputHtml = `<select id="${fieldId}" name="${field.field_name}" class="form-input" ${requiredAttr}><option value="">Select...</option>${options.map((option) => `<option value="${Utils.escapeHtml(String(option))}">${Utils.escapeHtml(String(option))}</option>`).join("")}</select>`;
          break;
        case "radio":
          inputHtml = options
            .map(
              (option, index) => `
                <label class="form-check" style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                  <input type="radio" name="${field.field_name}" value="${Utils.escapeHtml(String(option))}" ${index === 0 && field.is_required ? "required" : ""} />
                  <span>${Utils.escapeHtml(String(option))}</span>
                </label>
              `,
            )
            .join("");
          break;
        case "checkbox":
          if (options.length) {
            inputHtml = options
              .map(
                (option) => `
                  <label class="form-check" style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
                    <input type="checkbox" name="${field.field_name}" value="${Utils.escapeHtml(String(option))}" />
                    <span>${Utils.escapeHtml(String(option))}</span>
                  </label>
                `,
              )
              .join("");
          } else {
            inputHtml = `<label class="form-check" style="display:flex;align-items:center;gap:8px;"><input type="checkbox" id="${fieldId}" name="${field.field_name}" value="true" ${requiredAttr} /><span>${label}</span></label>`;
          }
          break;
        default:
          inputHtml = `<input type="text" id="${fieldId}" name="${field.field_name}" class="form-input" ${requiredAttr} />`;
      }

      return `
        <div class="form-group ${columnClass}">
          <label class="form-label ${requiredClass}" for="${fieldId}">${label}</label>
          ${inputHtml}
        </div>
      `;
    }

    function renderCustomFields() {
      const containers = {
        1: { wrapper: Utils.$("#customFieldsStep1"), grid: Utils.$("#customFieldsStep1Grid") },
        2: { wrapper: Utils.$("#customFieldsStep2"), grid: Utils.$("#customFieldsStep2Grid") },
        3: { wrapper: Utils.$("#customFieldsStep3"), grid: Utils.$("#customFieldsStep3Grid") },
        4: { wrapper: Utils.$("#customFieldsStep4"), grid: Utils.$("#customFieldsStep4Grid") },
      };

      Object.values(containers).forEach(({ wrapper, grid }) => {
        if (wrapper && grid) {
          wrapper.style.display = "none";
          grid.innerHTML = "";
        }
      });

      if (!customFields.length) {
        return;
      }

      const grouped = { 1: [], 2: [], 3: [], 4: [] };
      customFields.forEach((field) => {
        const step = getCustomFieldStep(field);
        grouped[step].push(field);
      });

      Object.keys(grouped).forEach((stepKey) => {
        const fields = grouped[stepKey];
        const section = containers[stepKey];
        if (!fields.length || !section?.wrapper || !section?.grid) {
          return;
        }

        section.grid.innerHTML = fields.map((field) => renderCustomField(field)).join("");
        section.wrapper.style.display = "block";
      });
    }

    async function loadCampuses() {
      try {
        const response = await API.get("/campuses/list");
        const select = Utils.$("#campus_id");
        select.innerHTML = '<option value="">Select Campus</option>';

        if (response.data && response.data.length > 0) {
          response.data.forEach((campus) => {
            select.innerHTML += `<option value="${campus.id}">${Utils.escapeHtml(campus.name)} (${Utils.escapeHtml(campus.code)})</option>`;
          });
        }
      } catch (error) {
        console.error("Failed to load campuses:", error);
      }
    }

    async function loadColleges() {
      try {
        const response = await API.organization.getColleges();
        const select = Utils.$("#college_id");
        select.innerHTML = '<option value="">Select College</option>';

        if (response.data && response.data.length > 0) {
          response.data.forEach((college) => {
            select.innerHTML += `<option value="${college.id}">${college.name} (${college.code})</option>`;
          });
        }
      } catch (error) {
        console.error("Failed to load colleges:", error);
      }
    }

    async function loadProgramsForCampusAndCollege() {
      const collegeId = Utils.$("#college_id").value;
      const campusId = Utils.$("#campus_id").value;
      const programSelect = Utils.$("#program_id");
      const sectionSelect = Utils.$("#section_id");

      programSelect.innerHTML = '<option value="">Select Program</option>';
      sectionSelect.innerHTML = '<option value="">Select Section</option>';
      programSelect.disabled = !collegeId || !campusId;
      sectionSelect.disabled = true;

      if (collegeId && campusId) {
        try {
          const response = await API.get("/programs", {
            college_id: collegeId,
            campus_id: campusId,
          });
          if (response.data && response.data.length > 0) {
            response.data.forEach((program) => {
              programSelect.innerHTML += `<option value="${program.id}">${Utils.escapeHtml(program.name)} (${Utils.escapeHtml(program.code)})</option>`;
            });
          }
        } catch (error) {
          console.error("Failed to load programs:", error);
        }
      }
    }

    Utils.$("#campus_id").addEventListener("change", loadProgramsForCampusAndCollege);
    Utils.$("#college_id").addEventListener("change", loadProgramsForCampusAndCollege);

    // Program change - load sections
    Utils.$("#program_id").addEventListener("change", async (e) => {
      const programId = e.target.value;
      const campusId = Utils.$("#campus_id").value;
      const sectionSelect = Utils.$("#section_id");

      sectionSelect.innerHTML = '<option value="">Select Section</option>';
      sectionSelect.disabled = !programId || !campusId;

      if (programId && campusId) {
        try {
          const response = await API.get("/sections", {
            program_id: programId,
            campus_id: campusId,
          });
          if (response.data && response.data.length > 0) {
            response.data.forEach((section) => {
              sectionSelect.innerHTML += `<option value="${section.id}">${Utils.escapeHtml(section.name)} (Batch ${Utils.escapeHtml(section.batch_year)})</option>`;
            });
          }
        } catch (error) {
          console.error("Failed to load sections:", error);
        }
      }
    });

    function updateProgress() {
      const progress = (currentStep / totalSteps) * 100;
      progressFill.style.width = `${progress}%`;

      document.querySelectorAll(".progress-step").forEach((step, index) => {
        step.classList.remove("active", "completed");
        if (index + 1 === currentStep) {
          step.classList.add("active");
        } else if (index + 1 < currentStep) {
          step.classList.add("completed");
        }
      });

      document.querySelectorAll(".wizard-step").forEach((step, index) => {
        step.classList.remove("active");
        if (index + 1 === currentStep) {
          step.classList.add("active");
        }
      });

      prevBtn.style.display = currentStep === 1 ? "none" : "inline-flex";
      nextBtn.style.display =
        currentStep === totalSteps ? "none" : "inline-flex";
      submitBtn.style.display =
        currentStep === totalSteps ? "inline-flex" : "none";
    }

    function validateStep(step) {
      const stepDiv = Utils.$(`#wizardStep${step}`);
      const requiredFields = stepDiv.querySelectorAll("[required]");
      let valid = true;
      let firstInvalidField = null;

      requiredFields.forEach((field) => {
        // Only validate visible fields
        if (field.offsetParent !== null) {
          if (!field.value || !field.value.trim()) {
            field.classList.add("error");
            if (!firstInvalidField) {
              firstInvalidField = field;
            }
            valid = false;
          } else {
            field.classList.remove("error");
          }
        }
      });

      // Focus on first invalid field
      if (firstInvalidField) {
        firstInvalidField.focus();
        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }

      return valid;
    }

    function showAlert(message, type = "error") {
      alertDiv.textContent = message;
      alertDiv.className = `alert alert-${type} mb-md`;
      alertDiv.style.display = "flex";
      alertDiv.scrollIntoView({ behavior: "smooth", block: "center" });
    }

    function hideAlert() {
      alertDiv.style.display = "none";
    }

    prevBtn.addEventListener("click", () => {
      if (currentStep > 1) {
        currentStep--;
        updateProgress();
        hideAlert();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    });

    nextBtn.addEventListener("click", async () => {
      hideAlert();

      if (!validateStep(currentStep)) {
        showAlert("Please fill in all required fields");
        return;
      }

      // Save current step data before moving to next
      Utils.setButtonLoading("#nextBtn", true);
      
      try {
        await saveCurrentStepData();
        
        if (currentStep < totalSteps) {
          currentStep++;
          updateProgress();
          window.scrollTo({ top: 0, behavior: 'smooth' });
        }
      } catch (error) {
        showAlert(error.message || "Failed to save progress. Please try again.");
      } finally {
        Utils.setButtonLoading("#nextBtn", false);
      }
    });

    async function saveCurrentStepData() {
      const formData = new FormData(form);
      const data = {};

      formData.forEach((value, key) => {
        if (value.trim()) {
          data[key] = value.trim();
        }
      });

      // Don't mark as complete yet
      data.complete_profile = false;

      const response = await API.put("/alumni/profile", data);
      return response;
    }

    form.addEventListener("submit", async (e) => {
      e.preventDefault();
      hideAlert();

      // Validate only the current (final) step
      if (!validateStep(currentStep)) {
        showAlert("Please fill in all required fields");
        return;
      }

      // Gather form data
      const formData = new FormData(form);
      const data = {};

      formData.forEach((value, key) => {
        if (value.trim()) {
          data[key] = value.trim();
        }
      });
      data.complete_profile = true;

      Utils.setButtonLoading("#submitBtn", true);

      try {
        const response = await API.put("/alumni/profile", data);

        Utils.success("Profile completed! You earned 50 points! 🎉");

        // Refresh user data
        await Auth.verifyToken();

        setTimeout(() => {
          if (Auth.user?.verification_status === "verified") {
            Router.navigate("/dashboard");
          } else {
            Utils.success("Profile submitted. Please wait for admin verification before logging in.");
            Auth.logout("/login");
          }
        }, 1500);
      } catch (error) {
        showAlert(error.message || "Failed to save profile. Please try again.");
      } finally {
        Utils.setButtonLoading("#submitBtn", false);
      }
    });

    // Initialize
    updateProgress();
  })(); // Async IIFE
</script>
