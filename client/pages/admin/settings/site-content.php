<!-- Site Content Settings -->
<div class="theme-ui-shell">
  <header class="theme-ui-topbar">
    <div class="theme-ui-topbar-left">
      <button
        type="button"
        class="theme-ui-menu-btn"
        id="settingsSidebarToggle"
        aria-label="Toggle Sidebar"
      >
        <svg
          width="18"
          height="18"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </button>
      <button
        type="button"
        class="theme-ui-close-btn"
        aria-label="Toggle Sidebar"
      >
        <svg
          width="18"
          height="18"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
      <h1>Site Content</h1>
    </div>
    <div class="theme-ui-topbar-right">
      <div class="theme-ui-user-meta">
        <div class="theme-ui-user-name" id="settingsTopbarName">
          Administrator
        </div>
        <div class="theme-ui-user-role" id="settingsTopbarRole">
          System Administrator
        </div>
      </div>
      <button
        type="button"
        class="btn btn-danger btn-sm"
        id="settingsTopbarLogout"
      >
        Logout
      </button>
    </div>
  </header>

  <div class="theme-ui-page">
    <aside class="theme-ui-sidebar" id="settingsSidebar"></aside>
    <button
      type="button"
      class="theme-ui-sidebar-backdrop"
      id="settingsSidebarBackdrop"
      aria-label="Close Sidebar"
    ></button>

    <main class="theme-ui-content">
      <section class="theme-ui-panel content-management-card">
        <div class="theme-ui-panel-body">
          <div class="settings-tabbar" aria-label="Site content sections">
            <button type="button" class="tab-btn active" data-scroll-target="siteGeneralSection">Header</button>
            <button type="button" class="tab-btn" data-scroll-target="siteContactSection">Contact</button>
            <button type="button" class="tab-btn" data-scroll-target="siteHomepageSection">Homepage</button>
            <button type="button" class="tab-btn" data-scroll-target="siteFooterSection">Footer</button>
            <button type="button" class="tab-btn" data-scroll-target="siteIdentitySection">Settings</button>
          </div>

          <div class="settings-info-panel">
            <strong>About Site Content</strong>
            <p class="mb-0 mt-xs">
              These values power the public landing page, footer, alumni IDs, and shared contact details. Layout and colors remain controlled by Theme Settings.
            </p>
          </div>

          <form id="siteContentForm">
            <h3 class="settings-section-title" id="siteGeneralSection">General Information</h3>

            <div class="settings-form-grid mb-xl">
              <div class="form-group">
                <label class="form-label">University Name</label>
                <input
                  type="text"
                  name="institution_name"
                  class="form-input"
                  value="Mindoro State University"
                />
              </div>
              <div class="form-group">
                <label class="form-label">Office / Department Name</label>
                <input
                  type="text"
                  name="department_name"
                  class="form-input"
                  value="Alumni Association"
                />
              </div>
              <div class="form-group">
                <label class="form-label">Site Name</label>
                <input
                  type="text"
                  name="site_name"
                  class="form-input"
                  value="MINSU Alumni"
                />
              </div>
              <div class="form-group">
                <label class="form-label">Site Tagline</label>
                <input
                  type="text"
                  name="site_tagline"
                  class="form-input"
                  value="Stay Connected, Stay Engaged"
                />
              </div>
            </div>

            <div class="form-group mb-xl">
              <label class="form-label">Site Description</label>
              <textarea
                name="site_description"
                class="form-input"
                rows="3"
                placeholder="A brief description of your alumni portal..."
              ></textarea>
            </div>

            <h3 class="settings-section-title" id="siteIdentitySection">Alumni ID Format</h3>

            <div class="settings-form-grid mb-xl">
              <div class="form-group">
                <label class="form-label">Pattern</label>
                <div
                  class="form-input"
                  style="display: flex; align-items: center; background: var(--gray-50)"
                >
                  CampusCode-YearGraduated-CollegeCode-00001
                </div>
                <div class="text-sm text-secondary">
                  Generated from the alumni campus, graduation year, college code, and a 5-digit sequence.
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Preview</label>
                <div
                  class="form-input"
                  id="alumniIdPrefixPreview"
                  style="display: flex; align-items: center; background: var(--gray-50)"
                >
                  BBC-2026-CCS-00001
                </div>
              </div>
            </div>

            <h3 class="settings-section-title" id="siteContactSection">Contact Information</h3>

            <div class="settings-form-grid mb-xl">
              <div class="form-group">
                <label class="form-label">Contact Email</label>
                <input
                  type="email"
                  name="contact_email"
                  class="form-input"
                  placeholder="alumni@minsu.edu.ph"
                />
              </div>
              <div class="form-group">
                <label class="form-label">Contact Phone</label>
                <input
                  type="tel"
                  name="contact_phone"
                  class="form-input"
                  placeholder="+63 XXX XXX XXXX"
                />
              </div>
              <div class="form-group">
                <label class="form-label">Contact Mobile</label>
                <input
                  type="tel"
                  name="contact_mobile"
                  class="form-input"
                  placeholder="09XX XXX XXXX"
                />
              </div>
              <div class="form-group">
                <label class="form-label">Website URL</label>
                <input
                  type="url"
                  name="website_url"
                  class="form-input"
                  placeholder="https://www.minsu.edu.ph"
                />
              </div>
              <div class="form-group">
                <label class="form-label">Office Hours</label>
                <input
                  type="text"
                  name="office_hours"
                  class="form-input"
                  placeholder="Monday - Friday, 8:00 AM - 5:00 PM"
                />
              </div>
              <div class="form-group col-span-2">
                <label class="form-label">Address</label>
                <textarea
                  name="address"
                  class="form-input"
                  rows="2"
                  placeholder="University address..."
                ></textarea>
              </div>
            </div>

            <h3 class="settings-section-title">Social Media</h3>

            <div class="settings-form-grid mb-xl">
              <div class="form-group">
                <label class="form-label">Facebook URL</label>
                <input
                  type="url"
                  name="facebook_url"
                  class="form-input"
                  placeholder="https://facebook.com/..."
                />
              </div>
              <div class="form-group">
                <label class="form-label">Twitter/X URL</label>
                <input
                  type="url"
                  name="twitter_url"
                  class="form-input"
                  placeholder="https://twitter.com/..."
                />
              </div>
              <div class="form-group">
                <label class="form-label">LinkedIn URL</label>
                <input
                  type="url"
                  name="linkedin_url"
                  class="form-input"
                  placeholder="https://linkedin.com/..."
                />
              </div>
              <div class="form-group">
                <label class="form-label">Instagram URL</label>
                <input
                  type="url"
                  name="instagram_url"
                  class="form-input"
                  placeholder="https://instagram.com/..."
                />
              </div>
            </div>

            <h3 class="settings-section-title" id="siteHomepageSection">Homepage Content</h3>

            <div class="form-group mb-lg">
              <label class="form-label">Hero Title</label>
              <input
                type="text"
                name="hero_title"
                class="form-input"
                value="Welcome to MINSU Alumni"
              />
            </div>

            <div class="form-group mb-lg">
              <label class="form-label">Hero Subtitle</label>
              <textarea
                name="hero_subtitle"
                class="form-input"
                rows="2"
                placeholder="Your connection to the MINSU community..."
              ></textarea>
            </div>

            <div class="form-group mb-xl">
              <label class="form-label">About Title</label>
              <input
                type="text"
                name="about_title"
                class="form-input"
                value="About Our Alumni Community"
              />
            </div>

            <div class="form-group mb-lg">
              <label class="form-label">About Section</label>
              <textarea
                name="about_content"
                class="form-input"
                rows="5"
                placeholder="About the alumni association..."
              ></textarea>
            </div>

            <div class="settings-form-grid mb-xl">
              <div class="form-group">
                <label class="form-label">Mission Statement</label>
                <textarea
                  name="mission_statement"
                  class="form-input"
                  rows="4"
                  placeholder="Mission statement shown on public pages..."
                ></textarea>
              </div>
              <div class="form-group">
                <label class="form-label">Vision Statement</label>
                <textarea
                  name="vision_statement"
                  class="form-input"
                  rows="4"
                  placeholder="Vision statement shown on public pages..."
                ></textarea>
              </div>
            </div>

            <div class="settings-form-grid mb-xl">
              <div class="form-group">
                <label class="form-label">Announcement Heading</label>
                <input
                  type="text"
                  name="announcement_heading"
                  class="form-input"
                  placeholder="Latest Alumni Updates"
                />
              </div>
              <div class="form-group">
                <label class="form-label">Events Heading</label>
                <input
                  type="text"
                  name="events_heading"
                  class="form-input"
                  placeholder="Upcoming Alumni Events"
                />
              </div>
            </div>

            <h3 class="settings-section-title" id="siteFooterSection">Footer</h3>

            <div class="form-group mb-lg">
              <label class="form-label">Footer Text</label>
              <input
                type="text"
                name="footer_text"
                class="form-input"
                value="© 2026 MINSU Alumni Association. All rights reserved."
              />
            </div>

            <div class="admin-actions-row">
              <button type="submit" class="btn btn-primary">
                Save Content
              </button>
            </div>
          </form>
        </div>
      </section>
    </main>
  </div>
</div>

<script>
  (function () {
    if (!Auth.isAdmin()) {
      window.location.hash = "#/admin/login";
      return;
    }

    const currentUser = API.getUser() || Auth.user || {};
    const canEditPrefix = currentUser.role === "system_admin";
    const prefixInput = Utils.$("#alumniIdPrefixInput");
    const prefixPreview = Utils.$("#alumniIdPrefixPreview");
    const prefixHint = Utils.$("#alumniIdPrefixHint");

    if (
      typeof App !== "undefined" &&
      typeof App.initSettingsShell === "function"
    ) {
      App.initSettingsShell({
        currentPath: "/admin/settings/site-content",
      });
    }

    setupPrefixField();
    setupContentTabs();

    loadContent();

    function normalizePrefix(value) {
      return String(value || "")
        .trim()
        .toUpperCase()
        .replace(/[^A-Z]/g, "")
        .slice(0, 3);
    }

    function updatePrefixPreview() {
      if (!prefixPreview) return;
      const prefix = normalizePrefix(prefixInput?.value) || "ALM";
      const year = new Date().getFullYear();
      prefixPreview.textContent = `${prefix}-${year}-CCS-00001`;
    }

    function setupPrefixField() {
      if (!prefixInput) return;

      if (!canEditPrefix) {
        prefixInput.disabled = true;
        prefixInput.title = "Only system administrators can change this value.";
        if (prefixHint) {
          prefixHint.textContent =
            "Only system administrators can modify this value.";
        }
      }

      prefixInput.addEventListener("input", () => {
        prefixInput.value = normalizePrefix(prefixInput.value);
        updatePrefixPreview();
      });

      updatePrefixPreview();
    }

    function setupContentTabs() {
      document.querySelectorAll(".settings-tabbar [data-scroll-target]").forEach((button) => {
        button.addEventListener("click", () => {
          document
            .querySelectorAll(".settings-tabbar .tab-btn")
            .forEach((item) => item.classList.remove("active"));
          button.classList.add("active");

          const target = document.getElementById(button.dataset.scrollTarget || "");
          if (target) {
            target.scrollIntoView({ behavior: "smooth", block: "start" });
          }
        });
      });
    }

    async function loadContent() {
      try {
        const response = await API.admin.settings.getSiteContent();
        const content = response.data;

        if (content) {
          Object.keys(content).forEach((key) => {
            const input = document.querySelector(`[name="${key}"]`);
            if (input) input.value = content[key] || "";
          });
        }

        if (prefixInput) {
          prefixInput.value = normalizePrefix(prefixInput.value || "ALM");
          updatePrefixPreview();
        }
      } catch (error) {
        console.error("Failed to load content:", error);
      }
    }

    Utils.$("#siteContentForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const data = Object.fromEntries(formData);

      if (prefixInput) {
        const normalizedPrefix = normalizePrefix(prefixInput.value);

        if (canEditPrefix) {
          if (!/^[A-Z]{3}$/.test(normalizedPrefix)) {
            Utils.error("Alumni ID Prefix must be exactly 3 letters (A-Z).");
            prefixInput.focus();
            return;
          }
          data.alumni_id_prefix = normalizedPrefix;
          prefixInput.value = normalizedPrefix;
        } else {
          delete data.alumni_id_prefix;
        }

        updatePrefixPreview();
      }

      try {
        await API.admin.settings.updateSiteContent(data);
        Utils.success("Content saved");
      } catch (error) {
        Utils.error(error.message || "Failed to save content");
      }
    });
  })();
</script>
