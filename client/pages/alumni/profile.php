<!-- Alumni Profile Page -->
<div class="dashboard-layout">
  <!-- Include sidebar via JS -->
  <aside class="sidebar" id="sidebar"></aside>

  <!-- Main Content -->
  <main class="main-content">
    <header class="topbar">
      <button class="btn btn-ghost sidebar-toggle" id="sidebarToggle">
        <svg
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <line x1="3" y1="12" x2="21" y2="12" />
          <line x1="3" y1="6" x2="21" y2="6" />
          <line x1="3" y1="18" x2="21" y2="18" />
        </svg>
      </button>
      <h1 class="page-title">My Profile</h1>
    </header>

    <div class="content-wrapper p-lg profile-page">
      <!-- Profile Header -->
      <div class="card mb-lg profile-hero-card">
        <div class="card-body p-xl">
          <div class="flex items-start gap-xl flex-wrap profile-hero-grid">
            <!-- Avatar Section -->
            <div class="text-center profile-avatar-block">
              <div
                class="avatar avatar-xl mb-md"
                style="width: 100px; height: 100px; font-size: 1.75rem"
              >
                <img
                  src=""
                  alt=""
                  id="profileAvatar"
                  style="display: none"
                />
                <span id="profileInitials">A</span>
              </div>
              <button class="btn btn-secondary btn-sm" id="changeAvatarBtn">
                Change Photo
              </button>
              <input
                type="file"
                id="avatarInput"
                accept="image/*"
                style="display: none"
              />
              <p class="profile-completion-mini" id="profileCompletionMini">
                Complete your key profile details
              </p>
            </div>

            <!-- Info Section -->
            <div class="flex-1 profile-info-block">
              <div>
                <h2 class="text-2xl font-bold" id="profileName">
                  Loading...
                </h2>
                <p class="text-secondary" id="profileAlumniId">-</p>
              </div>
              <button class="btn btn-primary" id="editProfileBtn">
                Edit Profile
              </button>

              <div class="grid grid-cols-3 gap-lg mt-lg profile-meta-grid">
                <div>
                  <div class="text-sm text-secondary">Email</div>
                  <div class="font-medium" id="profileEmail">-</div>
                </div>
                <div>
                  <div class="text-sm text-secondary">College</div>
                  <div class="font-medium" id="profileCollege">-</div>
                </div>
                <div>
                  <div class="text-sm text-secondary">Program</div>
                  <div class="font-medium" id="profileProgram">-</div>
                </div>
                <div>
                  <div class="text-sm text-secondary">Section</div>
                  <div class="font-medium" id="profileSection">-</div>
                </div>
                <div>
                  <div class="text-sm text-secondary">Batch Year</div>
                  <div class="font-medium" id="profileBatchYear">-</div>
                </div>
                <div>
                  <div class="text-sm text-secondary">Graduation Year</div>
                  <div class="font-medium" id="profileGradYear">-</div>
                </div>
              </div>
            </div>

            <!-- Points Card -->
            <div class="card profile-points-card">
              <div class="card-body text-center">
                <div class="text-sm text-secondary">Total Points</div>
                <div class="text-3xl font-bold text-primary" id="profilePoints">
                  0
                </div>
                <div class="badge badge-primary mt-sm" id="profileBadge">
                  Bronze
                </div>
                <div class="profile-completion-wrap mt-md">
                  <div class="profile-completion-label">
                    <span>Profile Completion</span>
                    <span id="profileCompletionPercent">0%</span>
                  </div>
                  <div class="profile-completion-track">
                    <div
                      class="profile-completion-fill"
                      id="profileCompletionBar"
                    ></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabs -->
      <div class="tabs mb-lg profile-tabs">
        <button class="tab active" data-tab="details">Profile Details</button>
        <button class="tab" data-tab="activity">Activity History</button>
        <button class="tab" data-tab="security">Security</button>
      </div>

      <!-- Tab Content: Details -->
      <div class="tab-content" id="tab-details">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Profile Information</h3>
          </div>
          <div class="card-body">
            <form id="profileForm">
              <div id="dynamicFields">
                <!-- Dynamic form fields will be loaded here -->
                <div class="loading-skeleton">Loading profile fields...</div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Tab Content: Activity -->
      <div class="tab-content hidden" id="tab-activity">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Points History</h3>
          </div>
          <div class="card-body p-0">
            <div id="pointsHistory">
              <div class="loading-skeleton p-lg">Loading history...</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab Content: Security -->
      <div class="tab-content hidden" id="tab-security">
        <div class="grid grid-cols-2 gap-lg">
          <!-- Change Password -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Change Password</h3>
            </div>
            <div class="card-body">
              <form id="passwordForm">
                <div class="form-group">
                  <label class="form-label">Current Password</label>
                  <input
                    type="password"
                    name="current_password"
                    class="form-input"
                    required
                  />
                </div>
                <div class="form-group">
                  <label class="form-label">New Password</label>
                  <input
                    type="password"
                    name="new_password"
                    class="form-input"
                    required
                  />
                </div>
                <div class="form-group">
                  <label class="form-label">Confirm New Password</label>
                  <input
                    type="password"
                    name="confirm_password"
                    class="form-input"
                    required
                  />
                </div>
                <button
                  type="submit"
                  class="btn btn-primary"
                  id="changePasswordBtn"
                >
                  Update Password
                </button>
              </form>
            </div>
          </div>

          <!-- Account Info -->
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Account Information</h3>
            </div>
            <div class="card-body">
              <div class="mb-md">
                <div class="text-sm text-secondary">Account Created</div>
                <div class="font-medium" id="accountCreated">-</div>
              </div>
              <div class="mb-md">
                <div class="text-sm text-secondary">Last Login</div>
                <div class="font-medium" id="lastLogin">-</div>
              </div>
              <div class="mb-md">
                <div class="text-sm text-secondary">Email Verified</div>
                <div id="emailVerified">-</div>
              </div>
              <div>
                <div class="text-sm text-secondary">Auth Provider</div>
                <div class="font-medium" id="authProvider">-</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Edit Profile Modal -->
<div class="modal" id="editProfileModal">
  <div class="modal-backdrop"></div>
  <div class="modal-dialog modal-md">
    <div class="modal-content compact-modal">
      <div class="modal-header">
        <h3 class="modal-title">Edit Profile</h3>
        <button class="modal-close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form id="editProfileForm">
          <div class="grid grid-cols-2 gap-sm">
            <div class="form-group">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" class="form-input" required />
            </div>
            <div class="form-group">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-input" readonly style="background-color: var(--gray-100); cursor: not-allowed;" />
            </div>
            <div class="form-group">
              <label class="form-label">College</label>
              <select name="college_id" class="form-select" id="collegeSelect">
                <option value="">Select College</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Program</label>
              <select name="program_id" class="form-select" id="programSelect">
                <option value="">Select Program</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Section</label>
              <select name="section_id" class="form-select" id="sectionSelect">
                <option value="">Select Section</option>
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Batch Year</label>
              <input
                type="number"
                name="batch_year"
                class="form-input"
                min="1950"
                max="2100"
              />
            </div>
            <div class="form-group">
              <label class="form-label">Graduation Year</label>
              <input
                type="number"
                name="graduation_year"
                class="form-input"
                min="1950"
                max="2100"
              />
            </div>
          </div>

          <!-- Dynamic Fields -->
          <div id="editDynamicFields" class="mt-md grid grid-cols-2 gap-sm">
            <!-- Will be populated with additional fields -->
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="saveProfileBtn">
          Save Changes
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  .profile-page {
    max-width: 1240px;
    margin: 0 auto;
    padding: 1rem;
  }

  .profile-hero-card {
    position: relative;
    overflow: visible;
    background: transparent;
    border: 0;
    box-shadow: none;
    margin-bottom: 1rem;
  }
  
  .profile-hero-card .card-body {
    padding: 1.5rem;
  }

  .profile-hero-card::before {
    content: none;
  }

  .profile-hero-grid {
    position: relative;
    z-index: 1;
    align-items: stretch;
    gap: 1.5rem;
  }

  .profile-avatar-block {
    min-width: 160px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
  }

  .profile-avatar-block .avatar {
    border: 3px solid #ffffff;
    box-shadow: var(--shadow-lg);
    background: linear-gradient(145deg, var(--primary-500), var(--primary-700));
    color: #ffffff;
  }

  .profile-avatar-block .avatar img {
    object-fit: cover;
  }

  .profile-completion-mini {
    margin-top: var(--spacing-md);
    font-size: var(--font-size-xs);
    color: var(--gray-600);
    max-width: 160px;
    line-height: 1.45;
  }

  .profile-info-block {
    min-width: 320px;
    display: grid;
    grid-template-columns: minmax(0, 1fr) auto;
    align-items: start;
    gap: 0.75rem 1rem;
  }

  .profile-info-block > div:first-child {
    min-width: 0;
  }

  #editProfileBtn {
    justify-self: end;
    align-self: start;
  }

  #profileAlumniId {
    font-family: var(--font-family-heading);
    letter-spacing: 0.05em;
    font-weight: 600;
    color: var(--primary-700);
  }

  .profile-meta-grid {
    grid-column: 1 / -1;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.75rem;
    margin-top: 1rem;
  }
  
  .profile-meta-grid > div {
    padding: 0.5rem 0.75rem;
    border-radius: var(--radius-lg);
    background: rgb(255 255 255 / 0.64);
    border: 1px solid var(--gray-100);
    transition:
      transform var(--transition-fast),
      box-shadow var(--transition-fast),
      border-color var(--transition-fast);
  }

  .profile-meta-grid > div:hover {
    transform: translateY(-2px);
    border-color: rgb(16 185 129 / 0.28);
    box-shadow: 0 12px 22px -18px rgb(5 150 105 / 0.45);
  }

  .profile-points-card {
    min-width: 250px;
    display: flex;
    background:
      radial-gradient(circle at 84% 14%, rgb(167 243 208 / 0.42), transparent 34%),
      #f0fdf4;
    border: 1px solid rgb(5 150 105 / 0.2);
    box-shadow: 0 18px 28px -24px rgb(5 150 105 / 0.52);
  }

  .profile-points-card .card-body {
    width: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 0.2rem;
  }

  #profilePoints {
    letter-spacing: -0.02em;
  }

  .profile-completion-wrap {
    text-align: left;
  }

  .profile-completion-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: var(--font-size-xs);
    color: var(--gray-700);
    margin-bottom: 0.375rem;
  }

  .profile-completion-track {
    width: 100%;
    height: 8px;
    background: rgb(5 150 105 / 0.16);
    border-radius: var(--radius-full);
    overflow: hidden;
  }

  .profile-completion-fill {
    height: 100%;
    width: 0;
    border-radius: var(--radius-full);
    background: linear-gradient(90deg, var(--primary-500), var(--primary-700));
    transition: width var(--transition-normal);
  }

  .profile-tabs {
    display: inline-flex;
    gap: 0.5rem;
    padding: 0.5rem;
    border-radius: var(--radius-xl);
    background: #ffffff;
    border: 1px solid var(--gray-200);
    box-shadow: var(--shadow-sm);
  }

  .profile-tabs .tab {
    border-radius: var(--radius-lg);
    padding-inline: 1rem;
    transition:
      transform var(--transition-fast),
      box-shadow var(--transition-fast);
  }

  .profile-tabs .tab:hover {
    transform: translateY(-1px);
  }

  .profile-tabs .tab.active {
    background: var(--primary-600);
    color: #ffffff;
    box-shadow: var(--shadow-sm);
  }

  /* Compact Modal Styles */
  .compact-modal {
    max-height: 85vh;
    display: flex;
    flex-direction: column;
  }

  .compact-modal .modal-header {
    padding: 1rem 1.25rem;
    flex-shrink: 0;
  }

  .compact-modal .modal-header .modal-title {
    font-size: 1.25rem;
  }

  .compact-modal .modal-body {
    padding: 1rem 1.25rem;
    overflow-y: auto;
    flex: 1;
    min-height: 0;
  }

  .compact-modal .modal-footer {
    padding: 1rem 1.25rem;
    flex-shrink: 0;
  }

  .compact-modal .form-group {
    margin-bottom: 0.75rem;
  }

  .compact-modal .form-label {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
  }

  .compact-modal .form-input,
  .compact-modal .form-select {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
  }

  .compact-modal .grid.gap-sm {
    gap: 0.5rem;
  }
  
  .compact-modal .form-group.full-width {
    grid-column: 1 / -1;
  }

  .modal-md {
    max-width: 600px;
  }

  @media (prefers-reduced-motion: reduce) {
    .profile-meta-grid > div,
    .profile-tabs .tab {
      transition: none;
    }

    .profile-meta-grid > div:hover,
    .profile-tabs .tab:hover {
      transform: none;
    }
  }

  @media (max-width: 1024px) {
    .profile-points-card {
      width: 100%;
      min-width: 0;
    }

    .profile-meta-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 720px) {
    .profile-page {
      padding-inline: 0;
    }

    .profile-hero-grid {
      gap: var(--spacing-lg);
    }

    .profile-info-block {
      min-width: 0;
      width: 100%;
      grid-template-columns: 1fr;
    }

    #editProfileBtn {
      justify-self: start;
    }

    .profile-meta-grid {
      grid-template-columns: 1fr;
    }

    .profile-tabs {
      width: 100%;
      justify-content: space-between;
    }

    .profile-tabs .tab {
      flex: 1;
      text-align: center;
      padding-inline: 0.5rem;
      font-size: var(--font-size-xs);
    }

    .compact-modal {
      max-height: 95vh;
    }

    .modal-md {
      max-width: 95%;
    }
  }
</style>

<script>
  (function () {
    const user = API.getUser();
    let profile = null;
    let formFields = [];
    const avatarImageEl = Utils.$("#profileAvatar");
    const avatarInitialsEl = Utils.$("#profileInitials");
    let avatarImageCandidates = [];
    let avatarImageCandidateIndex = 0;

    avatarImageEl.addEventListener("load", () => {
      avatarImageEl.style.display = "block";
      avatarInitialsEl.style.display = "none";
    });

    avatarImageEl.addEventListener("error", () => {
      if (tryNextAvatarImage()) {
        return;
      }

      avatarImageEl.style.display = "none";
      avatarInitialsEl.style.display = "inline";
    });

    function getProfileImageCandidates(imageUrl) {
      const raw = String(imageUrl || "").trim();
      if (!raw) return [];
      if (API.getAssetUrlCandidates) {
        return API.getAssetUrlCandidates(raw);
      }
      return [resolveProfileImageUrl(raw)];
    }

    function tryNextAvatarImage() {
      if (avatarImageCandidateIndex >= avatarImageCandidates.length) {
        return false;
      }

      avatarImageEl.src = avatarImageCandidates[avatarImageCandidateIndex];
      avatarImageCandidateIndex += 1;
      return true;
    }

    function setProfileAvatar(imageUrl) {
      avatarImageCandidates = getProfileImageCandidates(imageUrl);
      avatarImageCandidateIndex = 0;

      if (!avatarImageCandidates.length) {
        avatarImageEl.removeAttribute("src");
        avatarImageEl.style.display = "none";
        avatarInitialsEl.style.display = "inline";
        return;
      }

      avatarImageEl.style.display = "none";
      avatarInitialsEl.style.display = "inline";
      tryNextAvatarImage();
    }

    function resolveProfileImageUrl(imageUrl) {
      if (!imageUrl) return "";
      if (API.resolveAssetUrl) {
        const resolved = API.resolveAssetUrl(imageUrl);
        if (resolved) return resolved;
      }
      if (/^https?:\/\//i.test(imageUrl) || imageUrl.startsWith("data:")) {
        return imageUrl;
      }

      const apiRoot = (API.baseUrl || "").replace(/\/api\/v1\/?$/, "").replace(/\/+$/, "");
      return `${apiRoot}${imageUrl.startsWith("/") ? "" : "/"}${imageUrl}`;
    }

    function formatBadgeLabel(level) {
      return String(level || "Bronze")
        .toLowerCase()
        .replace(/(^|\s)\S/g, (char) => char.toUpperCase());
    }

    function updateCompletionIndicators(profileData) {
      const requiredFields = [
        "name",
        "college_id",
        "program_id",
        "section_id",
        "batch_year",
        "graduation_year",
        "phone",
      ];

      const completed = requiredFields.reduce((count, field) => {
        const value = profileData?.[field];
        return value === null || value === undefined || value === ""
          ? count
          : count + 1;
      }, 0);

      const percent = Math.round((completed / requiredFields.length) * 100);
      Utils.$("#profileCompletionPercent").textContent = `${percent}%`;
      Utils.$("#profileCompletionBar").style.width = `${percent}%`;
      Utils.$("#profileCompletionMini").textContent =
        `${completed} of ${requiredFields.length} key fields completed`;
    }

    async function loadProgramsForCollege(collegeId, selectedProgramId = "") {
      const programSelect = Utils.$("#programSelect");
      const sectionSelect = Utils.$("#sectionSelect");

      programSelect.innerHTML = '<option value="">Select Program</option>';
      sectionSelect.innerHTML = '<option value="">Select Section</option>';

      if (!collegeId) {
        return;
      }

      const programs = await API.organization.getPrograms(collegeId);
      const programList = Array.isArray(programs?.data) ? programs.data : [];

      programSelect.innerHTML += programList
        .map(
          (program) =>
            `<option value="${program.id}">${Utils.escapeHtml(program.name)}</option>`,
        )
        .join("");

      if (selectedProgramId) {
        programSelect.value = String(selectedProgramId);
      }
    }

    async function loadSectionsForProgram(programId, selectedSectionId = "") {
      const sectionSelect = Utils.$("#sectionSelect");
      sectionSelect.innerHTML = '<option value="">Select Section</option>';

      if (!programId) {
        return;
      }

      const sections = await API.organization.getSections(programId);
      const sectionList = Array.isArray(sections?.data) ? sections.data : [];

      sectionSelect.innerHTML += sectionList
        .map(
          (section) =>
            `<option value="${section.id}">${Utils.escapeHtml(section.name)}</option>`,
        )
        .join("");

      if (selectedSectionId) {
        sectionSelect.value = String(selectedSectionId);
      }
    }

    function setActiveTab(tabName) {
      Utils.$$(".tab").forEach((tab) => {
        tab.classList.toggle("active", tab.dataset.tab === tabName);
      });

      Utils.$$(".tab-content").forEach((panel) => {
        const isActive = panel.id === `tab-${tabName}`;
        panel.classList.toggle("active", isActive);
        panel.classList.toggle("hidden", !isActive);
      });

      if (tabName === "activity") {
        loadPointsHistory();
      }
    }

    // Load profile
    loadProfile();

    // Tab switching
    Utils.$$(".tab").forEach((tab) => {
      tab.addEventListener("click", () => {
        setActiveTab(tab.dataset.tab);
      });
    });

    setActiveTab(Utils.$(".tab.active")?.dataset.tab || "details");

    // Avatar upload
    Utils.$("#changeAvatarBtn").addEventListener("click", () => {
      Utils.$("#avatarInput").click();
    });

    Utils.$("#avatarInput").addEventListener("change", async (e) => {
      const file = e.target.files[0];
      if (!file) return;

      try {
        const formData = new FormData();
        formData.append("profile_image", file);

        const response = await API.auth.updateProfile(formData);
        const uploadedUser =
          response.data?.user && !Array.isArray(response.data.user)
            ? response.data.user
            : {};
        const nextProfileImage =
          uploadedUser.profile_image || response.data?.profile_image || "";
        const currentStoredUser = API.getUser() || Auth.user || {};
        const updatedUser = {
          ...currentStoredUser,
          ...uploadedUser,
          profile_image: nextProfileImage || currentStoredUser.profile_image,
        };

        if (updatedUser.id) {
          API.setUser(updatedUser);
          Auth.user = updatedUser;
        }

        setProfileAvatar(nextProfileImage || URL.createObjectURL(file));
        Utils.success("Profile photo updated!");
        
        await loadProfile();
        
        // Trigger profile update event
        window.dispatchEvent(new CustomEvent('profileUpdated', { 
          detail: response.data 
        }));
        
        // Update sidebar
        if (typeof App !== 'undefined' && App.populateAlumniSidebarIdentity) {
          const sidebar = document.querySelector('#sidebar');
          if (sidebar) {
            App.populateAlumniSidebarIdentity(sidebar);
          }
        }
        
      } catch (error) {
        Utils.error(error.message || "Failed to update photo");
      } finally {
        e.target.value = "";
      }
    });

    // Edit profile modal
    Utils.$("#editProfileBtn").addEventListener("click", async () => {
      try {
        // Ensure profile is loaded
        if (!profile) {
          await loadProfile();
        }
        
        await populateEditModal();
        Utils.openModal("#editProfileModal");
      } catch (error) {
        console.error("Failed to prepare profile form:", error);
        Utils.error("Failed to prepare profile form");
      }
    });

    // Save profile
    Utils.$("#saveProfileBtn").addEventListener("click", async () => {
      const form = Utils.$("#editProfileForm");
      
      Utils.setButtonLoading("#saveProfileBtn", true);
      
      try {
        const formData = new FormData(form);
        const data = {};
        
        formData.forEach((value, key) => {
          data[key] = value;
        });
        
        const response = await API.alumni.updateProfile(data);
        const responseProfile =
          response?.data?.profile && typeof response.data.profile === "object"
            ? response.data.profile
            : response?.data && typeof response.data === "object"
              ? response.data
              : null;
        
        // Update stored user data with profile image preserved
        if (responseProfile) {
          const currentUser = API.getUser() || {};
          const updatedUser = {
            ...currentUser,
            ...responseProfile,
            // Ensure profile_image is preserved
            profile_image: responseProfile.profile_image || currentUser.profile_image
          };
          API.setUser(updatedUser);
          Auth.user = updatedUser;
          profile = { ...(profile || {}), ...responseProfile };
        }
        
        Utils.success("Profile updated successfully!");
        Utils.closeModal("#editProfileModal");
        
        // Reload profile to show updated data
        await loadProfile();
        
        // Trigger a custom event to update other parts of the app
        window.dispatchEvent(new CustomEvent('profileUpdated', { 
          detail: response.data 
        }));
        
        // Update sidebar if it exists
        if (typeof App !== 'undefined' && App.populateAlumniSidebarIdentity) {
          const sidebar = document.querySelector('#sidebar');
          if (sidebar) {
            App.populateAlumniSidebarIdentity(sidebar);
          }
        }
      } catch (error) {
        Utils.error(error.message || "Failed to update profile");
      } finally {
        Utils.setButtonLoading("#saveProfileBtn", false);
      }
    });

    // Change password
    Utils.$("#passwordForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      
      const success = await Validation.validateAndSubmit(
        e.target,
        Validation.schemas.changePassword,
        async (formData) => {
          Utils.setButtonLoading("#changePasswordBtn", true);
          
          try {
            await API.auth.changePassword(
              formData.current_password,
              formData.new_password,
              formData.confirm_password,
            );
            Utils.success("Password changed successfully!");
            e.target.reset();
          } finally {
            Utils.setButtonLoading("#changePasswordBtn", false);
          }
        }
      );
    });

    // College/Program/Section cascade
    Utils.$("#collegeSelect").addEventListener("change", async (e) => {
      try {
        await loadProgramsForCollege(e.target.value);
      } catch (error) {
        Utils.error("Failed to load programs");
      }
    });

    Utils.$("#programSelect").addEventListener("change", async (e) => {
      try {
        await loadSectionsForProgram(e.target.value);
      } catch (error) {
        Utils.error("Failed to load sections");
      }
    });

    async function loadProfile() {
      try {
        const [profileRes, fieldsRes] = await Promise.all([
          API.alumni.getProfile(),
          API.formBuilder.getFields({ is_active: true }),
        ]);

        profile = profileRes.data;
        formFields = fieldsRes.data || [];
        const currentStoredUser = API.getUser() || Auth.user || {};

        if (profile?.id) {
          const nextStoredUser = {
            ...currentStoredUser,
            id: profile.id,
            alumni_id: profile.alumni_id,
            email: profile.email,
            name: profile.name,
            role: profile.role || currentStoredUser.role,
            profile_image: profile.profile_image || currentStoredUser.profile_image || "",
          };

          API.setUser(nextStoredUser);
          Auth.user = nextStoredUser;
        }

        // Update display
        Utils.$("#profileName").textContent = profile.name || "Unknown";
        Utils.$("#profileAlumniId").textContent = profile.alumni_id || "-";
        Utils.$("#profileEmail").textContent = profile.email || "-";
        Utils.$("#profileCollege").textContent = profile.college_name || "-";
        Utils.$("#profileProgram").textContent = profile.program_name || "-";
        Utils.$("#profileSection").textContent = profile.section_name || "-";
        Utils.$("#profileBatchYear").textContent = profile.batch_year || "-";
        Utils.$("#profileGradYear").textContent =
          profile.graduation_year || "-";
        Utils.$("#profilePoints").textContent = profile.total_points || 0;
        Utils.$("#profileBadge").textContent = formatBadgeLabel(
          profile.badge_level,
        );
        avatarInitialsEl.textContent = Utils.getInitials(profile.name || "A");
        updateCompletionIndicators(profile);

        const profileImage = profile.profile_image || currentStoredUser.profile_image || "";
        if (profileImage) {
          setProfileAvatar(profileImage);
        } else {
          setProfileAvatar("");
        }

        // Security tab info
        Utils.$("#accountCreated").textContent = Utils.formatDate(
          profile.created_at,
        );
        Utils.$("#lastLogin").textContent = profile.last_login
          ? Utils.formatDateTime(profile.last_login)
          : "Never";
        Utils.$("#emailVerified").innerHTML = profile.email_verified
          ? '<span class="badge badge-success">Verified</span>'
          : '<span class="badge badge-warning">Not Verified</span>';
        Utils.$("#authProvider").textContent = profile.auth_provider || "Email";

        // Render dynamic fields in profile tab
        renderDynamicFields();
      } catch (error) {
        console.error("Load profile error:", error);
        Utils.error("Failed to load profile");
      }
    }

    async function ensureFormFieldsLoaded() {
      if (Array.isArray(formFields) && formFields.length) {
        return;
      }

      const response = await API.formBuilder.getFields({ is_active: true });
      formFields = Array.isArray(response?.data) ? response.data : [];
    }

    function renderDynamicFields() {
      const container = Utils.$("#dynamicFields");

      if (!formFields.length) {
        container.innerHTML =
          '<p class="text-secondary">No additional profile fields.</p>';
        return;
      }

      const html = formFields
        .map((field) => {
          const rawValue = profile?.custom_fields?.[field.field_name];
          const value =
            rawValue === null || rawValue === undefined || rawValue === ""
              ? "-"
              : String(rawValue);
          return `
                <div class="mb-md">
                    <div class="text-sm text-secondary">${Utils.escapeHtml(field.field_label)}</div>
                    <div class="font-medium">${Utils.escapeHtml(value)}</div>
                </div>
            `;
        })
        .join("");

      container.innerHTML = `<div class="grid grid-cols-3 gap-lg">${html}</div>`;
    }

    async function populateEditModal() {
      const form = Utils.$("#editProfileForm");
      
      console.log('Populating edit modal with profile:', profile);

      await ensureFormFieldsLoaded();

      // Load colleges
      const colleges = await API.organization.getColleges();
      const collegeList = Array.isArray(colleges?.data) ? colleges.data : [];
      Utils.$("#collegeSelect").innerHTML =
        '<option value="">Select College</option>' +
        collegeList
          .map(
            (c) =>
              `<option value="${c.id}">${Utils.escapeHtml(c.name)}</option>`,
          )
          .join("");

      // Populate basic form fields with current values
      if (form.elements.name) {
        form.elements.name.value = profile?.name || "";
        console.log('Set name:', profile?.name);
      }
      if (form.elements.email) {
        form.elements.email.value = profile?.email || "";
        console.log('Set email:', profile?.email);
      }
      if (form.elements.batch_year) {
        form.elements.batch_year.value = profile?.batch_year || "";
        console.log('Set batch_year:', profile?.batch_year);
      }
      if (form.elements.graduation_year) {
        form.elements.graduation_year.value = profile?.graduation_year || "";
        console.log('Set graduation_year:', profile?.graduation_year);
      }

      // Remove required attribute from email field since it's readonly
      if (form.elements.email) form.elements.email.removeAttribute('required');

      // Set college and load programs/sections
      if (profile?.college_id) {
        Utils.$("#collegeSelect").value = String(profile.college_id);
        console.log('Set college_id:', profile.college_id);
        await loadProgramsForCollege(profile.college_id, profile.program_id);
        await loadSectionsForProgram(profile.program_id, profile.section_id);
      }

      // Add additional fields to edit modal with current values
      const additionalFieldsHTML = `
        <div class="form-group">
          <label class="form-label">Suffix</label>
          <input type="text" name="suffix" class="form-input" value="${Utils.escapeHtml(profile?.suffix || '')}" placeholder="Jr., Sr., III, etc." />
        </div>
        <div class="form-group">
          <label class="form-label">Phone Number</label>
          <input type="tel" name="mobile" class="form-input" value="${Utils.escapeHtml(profile?.mobile || '')}" placeholder="+63 9XX XXX XXXX" />
        </div>
        <div class="form-group full-width">
          <label class="form-label">Street Address</label>
          <input type="text" name="address_street" class="form-input" value="${Utils.escapeHtml(profile?.address_street || '')}" />
        </div>
        <div class="form-group">
          <label class="form-label">City</label>
          <input type="text" name="address_city" class="form-input" value="${Utils.escapeHtml(profile?.address_city || '')}" />
        </div>
        <div class="form-group">
          <label class="form-label">Province</label>
          <input type="text" name="address_province" class="form-input" value="${Utils.escapeHtml(profile?.address_province || '')}" />
        </div>
        <div class="form-group">
          <label class="form-label">Employment Status</label>
          <select name="employment_status" class="form-select">
            <option value="">Select Status</option>
            <option value="employed" ${profile?.employment_status === 'employed' ? 'selected' : ''}>Employed</option>
            <option value="self_employed" ${profile?.employment_status === 'self_employed' ? 'selected' : ''}>Self-Employed</option>
            <option value="unemployed" ${profile?.employment_status === 'unemployed' ? 'selected' : ''}>Unemployed</option>
            <option value="student" ${profile?.employment_status === 'student' ? 'selected' : ''}>Student</option>
            <option value="retired" ${profile?.employment_status === 'retired' ? 'selected' : ''}>Retired</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Current Employer</label>
          <input type="text" name="current_employer" class="form-input" value="${Utils.escapeHtml(profile?.current_employer || '')}" />
        </div>
        <div class="form-group">
          <label class="form-label">Job Title</label>
          <input type="text" name="job_title" class="form-input" value="${Utils.escapeHtml(profile?.job_title || '')}" />
        </div>
        <div class="form-group full-width">
          <label class="form-label">LinkedIn Profile</label>
          <input type="url" name="linkedin_url" class="form-input" value="${Utils.escapeHtml(profile?.linkedin_url || '')}" placeholder="https://linkedin.com/in/yourprofile" />
        </div>
        <div class="form-group full-width">
          <label class="form-label">Facebook Profile</label>
          <input type="url" name="facebook_url" class="form-input" value="${Utils.escapeHtml(profile?.facebook_url || '')}" placeholder="https://facebook.com/yourprofile" />
        </div>
        <div class="form-group full-width">
          <label class="form-label">Instagram Profile</label>
          <input type="url" name="instagram_url" class="form-input" value="${Utils.escapeHtml(profile?.instagram_url || '')}" placeholder="https://instagram.com/yourprofile" />
        </div>
      `;

      // Render dynamic fields in edit modal
      const editFieldsContainer = Utils.$("#editDynamicFields");
      editFieldsContainer.innerHTML = additionalFieldsHTML + formFields
        .map((field) => renderFormField(field))
        .join("");
    }

    function renderFormField(field) {
      const rawValue = profile?.custom_fields?.[field.field_name];
      const value =
        rawValue === null || rawValue === undefined ? "" : String(rawValue);
      let inputHtml = "";

      switch (field.field_type) {
        case "text":
        case "email":
        case "phone":
        case "url":
          inputHtml = `<input type="${field.field_type}" name="${field.field_name}" class="form-input" value="${Utils.escapeHtml(value)}" ${field.is_required ? "required" : ""}>`;
          break;
        case "textarea":
          inputHtml = `<textarea name="${field.field_name}" class="form-input" rows="3" ${field.is_required ? "required" : ""}>${Utils.escapeHtml(value)}</textarea>`;
          break;
        case "select":
          const options = field.field_options || [];
          inputHtml = `<select name="${field.field_name}" class="form-select" ${field.is_required ? "required" : ""}>
                    <option value="">Select...</option>
                    ${options.map((opt) => `<option value="${opt}" ${value === opt ? "selected" : ""}>${Utils.escapeHtml(opt)}</option>`).join("")}
                </select>`;
          break;
        case "date":
          inputHtml = `<input type="date" name="${field.field_name}" class="form-input" value="${value}" ${field.is_required ? "required" : ""}>`;
          break;
        case "checkbox":
          inputHtml = `<input type="checkbox" name="${field.field_name}" class="form-check-input" ${value === "true" || value === true ? "checked" : ""}>`;
          break;
        default:
          inputHtml = `<input type="text" name="${field.field_name}" class="form-input" value="${Utils.escapeHtml(value)}">`;
      }

      return `
            <div class="form-group" style="width: ${field.column_width || "100%"};">
                <label class="form-label ${field.is_required ? "required" : ""}">${Utils.escapeHtml(field.field_label)}</label>
                ${inputHtml}
            </div>
        `;
    }

    async function loadPointsHistory() {
      try {
        const response = await API.gamification.getPointsHistory();
        const container = Utils.$("#pointsHistory");

        if (!response.data?.length) {
          container.innerHTML =
            '<div class="p-lg text-center text-secondary">No activity yet.</div>';
          return;
        }

        container.innerHTML = `
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Activity</th>
                            <th>Points</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${response.data
                          .map(
                            (item) => `
                            <tr>
                                <td>${Utils.formatDate(item.created_at)}</td>
                                <td>${Utils.escapeHtml(item.description)}</td>
                                <td class="${item.type === "earned" ? "text-success" : "text-danger"}">
                                    ${item.type === "earned" ? "+" : "-"}${item.points}
                                </td>
                            </tr>
                        `,
                          )
                          .join("")}
                    </tbody>
                </table>
            `;
      } catch (error) {
        console.error("Load history error:", error);
      }
    }

  })();
</script>
