<!-- Theme Settings -->
<div class="theme-ui-shell">
  <header class="theme-ui-topbar">
    <div class="theme-ui-topbar-left">
      <button
        type="button"
        class="theme-ui-menu-btn"
        id="themeSidebarToggle"
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
      <h1>Theme Settings</h1>
    </div>
    <div class="theme-ui-topbar-right">
      <div class="theme-ui-user-meta">
        <div class="theme-ui-user-name" id="themeTopbarName">Administrator</div>
        <div class="theme-ui-user-role" id="themeTopbarRole">
          System Administrator
        </div>
      </div>
      <button
        type="button"
        class="btn btn-danger btn-sm"
        id="themeTopbarLogout"
      >
        Logout
      </button>
    </div>
  </header>

  <div class="theme-ui-page">
    <aside class="theme-ui-sidebar" id="themeSidebar"></aside>
    <button
      type="button"
      class="theme-ui-sidebar-backdrop"
      id="themeSidebarBackdrop"
      aria-label="Close Sidebar"
    ></button>

    <main class="theme-ui-content">
      <section class="theme-ui-panel card">
        <div class="theme-ui-panel-body">
          <h2 class="theme-ui-heading">
            <svg
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <circle cx="13.5" cy="6.5" r="2.5"></circle>
              <circle cx="17.5" cy="10.5" r="2.5"></circle>
              <circle cx="8.5" cy="7.5" r="2.5"></circle>
              <path
                d="M12 21c5 0 9-3.4 9-8.2S17.8 3 13.8 3H8.9C5 3 3 5.3 3 8.6c0 1.9 1.2 3.1 2.6 4.2 1.1.9 1.2 2 .5 3.3-.6 1.2-.2 2.3.9 3.2 1.1.7 2.4 1.1 4 1.1z"
              ></path>
            </svg>
            Customize Website Theme
          </h2>

          <div class="theme-ui-info-card">
            <div class="theme-ui-info-title">About Theme Settings</div>
            <p>
              These settings control the appearance of the entire site. Changes
              to branding, color palette, and typography are reflected in public
              pages.
            </p>
          </div>

          <form id="themeForm" class="theme-ui-form" novalidate>
            <div class="theme-ui-grid-two">
              <div class="form-group">
                <label class="form-label" for="primaryColorText"
                  >Primary Color</label
                >
                <div class="theme-ui-color-row">
                  <input
                    type="color"
                    name="primary_color"
                    id="primaryColor"
                    value="#10b981"
                    class="theme-ui-color-picker"
                  />
                  <input
                    type="text"
                    class="form-input"
                    id="primaryColorText"
                    value="#10b981"
                    autocomplete="off"
                  />
                </div>
                <div class="form-hint">
                  Main color used across actions and highlights.
                </div>
              </div>

              <div class="form-group">
                <label class="form-label" for="secondaryColorText"
                  >Secondary Color</label
                >
                <div class="theme-ui-color-row">
                  <input
                    type="color"
                    name="secondary_color"
                    id="secondaryColor"
                    value="#6b7280"
                    class="theme-ui-color-picker"
                  />
                  <input
                    type="text"
                    class="form-input"
                    id="secondaryColorText"
                    value="#6b7280"
                    autocomplete="off"
                  />
                </div>
                <div class="form-hint">
                  Support color for secondary controls and badges.
                </div>
              </div>
            </div>

            <div class="theme-ui-grid-two">
              <div class="form-group">
                <label class="form-label" for="institutionName"
                  >Institution Name</label
                >
                <input
                  type="text"
                  name="institution_name"
                  id="institutionName"
                  class="form-input"
                  placeholder="Mindoro State University"
                />
              </div>

              <div class="form-group">
                <label class="form-label" for="departmentName"
                  >College / Department Name</label
                >
                <input
                  type="text"
                  name="department_name"
                  id="departmentName"
                  class="form-input"
                  placeholder="Office of the Admission"
                />
                <div class="form-hint">
                  Shown below the institution name in public branding blocks.
                </div>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="logoUrl">Logo</label>
              <div class="theme-ui-upload-row">
                <input
                  type="url"
                  name="logo_url"
                  id="logoUrl"
                  class="form-input"
                  placeholder="https://example.com/logo.png"
                />
                <button
                  type="button"
                  class="btn btn-primary"
                  id="logoUploadBtn"
                >
                  Upload
                </button>
              </div>
              <input
                type="file"
                id="logoFileInput"
                accept="image/png,image/jpeg,image/webp,image/gif"
                class="hidden"
              />
              <div class="form-hint">
                Use an HTTPS image URL. You can paste or set it from Upload.
              </div>

              <div class="theme-ui-logo-preview" id="logoPreviewFrame">
                <div class="theme-ui-logo-preview-label">Preview:</div>
                <img
                  id="themeLogoPreview"
                  src="assets/images/logo.svg"
                  alt="Theme Logo Preview"
                />
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="authBackgroundImageUrl"
                >Auth Background Image</label
              >
              <div class="theme-ui-upload-row">
                <input
                  type="url"
                  name="auth_background_image_url"
                  id="authBackgroundImageUrl"
                  class="form-input"
                  placeholder="https://example.com/auth-background.jpg"
                />
                <button
                  type="button"
                  class="btn btn-secondary"
                  id="backgroundUploadBtn"
                >
                  Upload
                </button>
              </div>
              <input
                type="file"
                id="backgroundFileInput"
                accept="image/png,image/jpeg,image/webp,image/gif"
                class="hidden"
              />
              <div class="form-hint">
                Optional image shown behind Login/Register gradient overlays.
              </div>
              <div
                class="theme-ui-bg-preview"
                id="themeBackgroundPreview"
              ></div>
            </div>

            <div class="theme-ui-grid-three">
              <div class="form-group">
                <label class="form-label">Heading Font</label>
                <select name="heading_font" class="form-input">
                  <option value="Inter">Inter</option>
                  <option value="Roboto">Roboto</option>
                  <option value="Open Sans">Open Sans</option>
                  <option value="Lato">Lato</option>
                  <option value="Poppins">Poppins</option>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label">Body Font</label>
                <select name="body_font" class="form-input">
                  <option value="Inter">Inter</option>
                  <option value="Roboto">Roboto</option>
                  <option value="Open Sans">Open Sans</option>
                  <option value="Lato">Lato</option>
                  <option value="Poppins">Poppins</option>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label">Border Radius</label>
                <select name="border_radius" class="form-input">
                  <option value="none">None</option>
                  <option value="sm">Small</option>
                  <option value="md">Medium</option>
                  <option value="lg">Large</option>
                </select>
              </div>
            </div>

            <div class="theme-ui-grid-two">
              <div class="form-group">
                <label class="form-label">Accent Color</label>
                <div class="theme-ui-color-row">
                  <input
                    type="color"
                    name="accent_color"
                    id="accentColor"
                    value="#f59e0b"
                    class="theme-ui-color-picker"
                  />
                  <input
                    type="text"
                    class="form-input"
                    id="accentColorText"
                    value="#f59e0b"
                    autocomplete="off"
                  />
                </div>
              </div>

              <div class="form-group">
                <label class="form-label">Favicon URL</label>
                <input
                  type="url"
                  name="favicon_url"
                  class="form-input"
                  placeholder="https://example.com/favicon.ico"
                />
              </div>
            </div>

            <div class="theme-ui-grid-two">
              <div class="form-group">
                <label class="form-label">Sidebar Style</label>
                <select name="sidebar_style" class="form-input">
                  <option value="dark">Dark</option>
                  <option value="light">Light</option>
                </select>
              </div>

              <div class="form-group">
                <label class="form-label">Custom CSS</label>
                <textarea
                  name="custom_css"
                  class="form-input"
                  rows="2"
                  placeholder="Optional CSS overrides"
                ></textarea>
              </div>
            </div>

            <div class="theme-ui-live-title">Live Preview</div>
            <div class="theme-ui-live-preview" id="themeLivePreview">
              <div class="theme-ui-live-header">
                <img
                  id="themeLiveLogo"
                  src="assets/images/logo.svg"
                  alt="Live Preview Logo"
                />
                <div class="theme-ui-live-header-text">
                  <strong id="themeLiveInstitution"
                    >Mindoro State University</strong
                  >
                  <em id="themeLiveDepartment">Office of the Admission</em>
                  <span>Online Alumni System</span>
                </div>
              </div>
              <div class="theme-ui-live-body">
                <p>Sample body text with the selected font family.</p>
                <div class="theme-ui-live-actions">
                  <button type="button" class="btn btn-primary">
                    Primary Button
                  </button>
                  <button type="button" class="btn btn-secondary">
                    Secondary Button
                  </button>
                </div>
              </div>
            </div>

            <div class="theme-ui-form-actions">
              <button type="submit" class="btn btn-primary" id="saveThemeBtn">
                Save Changes
              </button>
              <button
                type="button"
                class="btn btn-secondary"
                id="undoThemeBtn"
                disabled
              >
                Undo Changes
              </button>
              <button type="button" class="btn btn-outline" id="resetThemeBtn">
                Reset to Original Theme
              </button>
            </div>
          </form>
        </div>
      </section>
    </main>
  </div>
</div>

<style>
  .theme-ui-shell {
    min-height: 100vh;
    background: #eef2ec;
  }

  .theme-ui-topbar {
    height: 72px;
    padding: 0 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
    border-bottom: 1px solid #d9dfd7;
    position: sticky;
    top: 0;
    z-index: 120;
  }

  .theme-ui-topbar-left {
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .theme-ui-topbar-left h1 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
    color: #1f5221;
  }

  .theme-ui-close-btn,
  .theme-ui-menu-btn {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    border: 1px solid transparent;
    background: transparent;
    color: #1f2937;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    cursor: pointer;
    transition:
      background-color var(--transition-fast),
      border-color var(--transition-fast),
      color var(--transition-fast);
  }

  .theme-ui-close-btn:hover,
  .theme-ui-menu-btn:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
    color: #0f172a;
    text-decoration: none;
  }

  .theme-ui-menu-btn {
    display: none;
  }

  .theme-ui-topbar-right {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .theme-ui-user-meta {
    text-align: right;
    line-height: 1.2;
  }

  .theme-ui-user-name {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
  }

  .theme-ui-user-role {
    font-size: 0.85rem;
    color: #6b7280;
  }

  .theme-ui-page {
    display: flex;
    min-height: calc(100vh - 72px);
  }

  .theme-ui-sidebar {
    width: 284px;
    flex-shrink: 0;
    background: #f4f5f4;
    border-right: 1px solid #d9dfd7;
    padding: 14px 12px;
    overflow-y: auto;
    position: sticky;
    top: 72px;
    height: calc(100vh - 72px);
  }

  .theme-ui-sidebar-section {
    margin-bottom: 14px;
  }

  .theme-ui-sidebar-toggle {
    width: 100%;
    border: 0;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 0.76rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    color: #334155;
    padding: 6px 10px;
    text-transform: uppercase;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color var(--transition-fast);
  }

  .theme-ui-sidebar-toggle:hover {
    background: #e5e7eb;
  }

  .theme-ui-sidebar-toggle svg {
    transition: transform var(--transition-fast);
  }

  .theme-ui-sidebar-section.collapsed .theme-ui-sidebar-toggle svg {
    transform: rotate(-90deg);
  }

  .theme-ui-sidebar-links {
    display: grid;
    gap: 4px;
    margin-top: 6px;
  }

  .theme-ui-sidebar-section.collapsed .theme-ui-sidebar-links {
    display: none;
  }

  .theme-ui-sidebar-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 10px;
    text-decoration: none;
    color: #334155;
    font-size: 0.98rem;
    border: 1px solid transparent;
    transition:
      background-color var(--transition-fast),
      color var(--transition-fast),
      border-color var(--transition-fast);
  }

  .theme-ui-sidebar-link:hover {
    background: #e8ece8;
    border-color: #d1d5db;
    color: #0f172a;
    text-decoration: none;
  }

  .theme-ui-sidebar-link.active {
    background: #c8d8c2;
    color: #1f5221;
    font-weight: 400;
  }

  .theme-ui-link-badge {
    margin-left: auto;
    font-size: 0.68rem;
    line-height: 1;
    padding: 4px 7px;
    border-radius: 999px;
    background: #ef4444;
    color: #ffffff;
    font-weight: 700;
  }

  .theme-ui-content {
    flex: 1;
    padding: 30px;
    display: flex;
    justify-content: center;
    align-items: flex-start;
  }

  .theme-ui-panel {
    width: min(960px, 100%);
    border: 1px solid #dbe3d8;
    box-shadow: 0 18px 36px -28px rgb(15 23 42 / 0.4);
  }

  .theme-ui-panel-body {
    padding: 26px 30px 30px;
  }

  .theme-ui-heading {
    margin: 0 0 18px;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #0f2a4c;
    font-size: 2.1rem;
  }

  .theme-ui-info-card {
    border: 1px solid #b9d3f5;
    background: #e9f1fb;
    border-radius: 10px;
    padding: 14px 16px;
    margin-bottom: 24px;
    color: #1e40af;
  }

  .theme-ui-info-title {
    font-weight: 700;
    margin-bottom: 8px;
    color: #1e3a8a;
  }

  .theme-ui-info-card p {
    margin: 0;
    line-height: 1.6;
  }

  .theme-ui-form {
    display: grid;
    gap: 8px;
  }

  .theme-ui-grid-two {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
  }

  .theme-ui-grid-three {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 16px;
  }

  .theme-ui-color-row {
    display: grid;
    grid-template-columns: 78px 1fr;
    gap: 10px;
    align-items: center;
  }

  .theme-ui-color-picker {
    width: 78px;
    height: 42px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 4px;
    background: #ffffff;
  }

  .theme-ui-upload-row {
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 10px;
  }

  .theme-ui-logo-preview {
    margin-top: 10px;
    background: #f3f4f6;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    padding: 12px;
    min-height: 78px;
  }

  .theme-ui-logo-preview-label {
    font-size: 0.86rem;
    color: #64748b;
    margin-bottom: 6px;
  }

  .theme-ui-logo-preview img {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    object-fit: cover;
    border: 1px solid #d1d5db;
    background: #ffffff;
  }

  .theme-ui-bg-preview {
    margin-top: 10px;
    min-height: 110px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    background-image: linear-gradient(
      135deg,
      var(--primary-200),
      var(--primary-100)
    );
    background-size: cover;
    background-position: center;
    box-shadow: inset 0 0 0 1px rgb(255 255 255 / 0.55);
  }

  .theme-ui-live-title {
    margin-top: 4px;
    margin-bottom: 10px;
    font-size: 2rem;
    font-weight: 700;
    color: #0f172a;
  }

  .theme-ui-live-preview {
    border-radius: 12px;
    border: 1px solid #d7dfd2;
    overflow: hidden;
    background: #ffffff;
    --preview-primary: #10b981;
    --preview-secondary: #6b7280;
  }

  .theme-ui-live-header {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 18px;
    color: #ffffff;
    background: linear-gradient(
      135deg,
      var(--preview-primary),
      var(--preview-secondary)
    );
  }

  .theme-ui-live-header img {
    width: 52px;
    height: 52px;
    border-radius: 10px;
    object-fit: cover;
    border: 1px solid rgb(255 255 255 / 0.5);
    background: rgb(255 255 255 / 0.2);
  }

  .theme-ui-live-header-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
    line-height: 1.25;
  }

  .theme-ui-live-header-text strong {
    font-size: 1.08rem;
    color: #ffffff;
  }

  .theme-ui-live-header-text em,
  .theme-ui-live-header-text span {
    font-size: 0.94rem;
    color: rgb(255 255 255 / 0.9);
  }

  .theme-ui-live-body {
    padding: 18px;
    font-size: 1.02rem;
    color: #334155;
  }

  .theme-ui-live-body p {
    margin-bottom: 12px;
  }

  .theme-ui-live-actions {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
  }

  .theme-ui-form-actions {
    margin-top: 18px;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
  }

  .theme-ui-sidebar-backdrop {
    display: none;
  }

  /* Bridge local preview-page CSS with shared themed settings shell variables. */
  .theme-ui-shell {
    background: var(--admin-bg);
  }

  .theme-ui-topbar {
    background: var(--admin-header-bg);
    border-bottom-color: var(--admin-header-border);
  }

  .theme-ui-topbar-left h1,
  .theme-ui-user-name,
  .theme-ui-close-btn,
  .theme-ui-menu-btn {
    color: var(--admin-heading);
  }

  .theme-ui-user-role {
    color: var(--admin-header-muted);
  }

  .theme-ui-close-btn:hover,
  .theme-ui-menu-btn:hover {
    background: var(--primary-50);
    border-color: var(--primary-200);
    color: var(--primary-900);
  }

  .theme-ui-sidebar {
    background: var(--admin-surface);
    border-right-color: var(--admin-border);
  }

  .theme-ui-sidebar-toggle {
    color: var(--admin-sidebar-link);
  }

  .theme-ui-sidebar-toggle:hover {
    background: var(--admin-sidebar-link-hover);
  }

  .theme-ui-sidebar-link {
    color: var(--admin-sidebar-link);
  }

  .theme-ui-sidebar-link:hover {
    background: var(--admin-sidebar-link-hover);
    border-color: var(--admin-muted-border);
    color: var(--admin-heading);
  }

  .theme-ui-sidebar-link.active {
    background: var(--admin-sidebar-link-active);
    color: var(--admin-sidebar-link-active-text);
  }

  .theme-ui-content {
    background:
      radial-gradient(circle at 100% -10%, var(--primary-100), transparent 36%),
      linear-gradient(
        180deg,
        var(--admin-bg) 0%,
        var(--color-surface-soft) 100%
      );
  }

  .theme-ui-panel {
    border-color: var(--admin-muted-border);
    background: var(--admin-surface-strong);
  }

  .theme-ui-heading,
  .theme-ui-live-title {
    color: var(--admin-heading);
  }

  @media (max-width: 1200px) {
    .theme-ui-content {
      padding: 20px;
    }

    .theme-ui-grid-three {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 980px) {
    .theme-ui-topbar {
      padding: 0 12px;
    }

    .theme-ui-topbar-left h1 {
      font-size: 1.6rem;
    }

    .theme-ui-menu-btn {
      display: inline-flex;
    }

    .theme-ui-sidebar {
      position: fixed;
      left: 0;
      top: 72px;
      bottom: 0;
      z-index: 130;
      transform: translateX(-110%);
      transition: transform var(--transition-normal);
    }

    .theme-ui-sidebar.open {
      transform: translateX(0);
    }

    .theme-ui-sidebar-backdrop {
      display: block;
      position: fixed;
      inset: 72px 0 0;
      border: 0;
      background: rgb(15 23 42 / 0.3);
      opacity: 0;
      pointer-events: none;
      z-index: 125;
      transition: opacity var(--transition-fast);
    }

    .theme-ui-sidebar-backdrop.active {
      opacity: 1;
      pointer-events: auto;
    }

    .theme-ui-grid-two,
    .theme-ui-grid-three {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 760px) {
    .theme-ui-topbar-right {
      gap: 8px;
    }

    .theme-ui-user-meta {
      display: none;
    }

    .theme-ui-content {
      padding: 12px;
    }

    .theme-ui-panel-body {
      padding: 16px;
    }

    .theme-ui-heading {
      font-size: 1.65rem;
    }

    .theme-ui-live-title {
      font-size: 1.5rem;
    }
  }
</style>

<script>
  (async function () {
    if (!Auth.isAdmin()) {
      window.location.hash = "#/admin/login";
      return;
    }

    const defaults = {
      primary_color: "#10b981",
      secondary_color: "#6b7280",
      accent_color: "#f59e0b",
      background_color: "#f8fafc",
      text_color: "#1f2937",
      heading_font: "Inter",
      body_font: "Inter",
      font_family: "Inter",
      logo_url: "",
      auth_background_image_url: "",
      favicon_url: "",
      sidebar_style: "dark",
      border_radius: "md",
      custom_css: "",
    };

    const siteDefaults = {
      institution_name: "Mindoro State University",
      department_name: "Office of the Admission",
    };

    const form = document.getElementById("themeForm");
    const saveButton = document.getElementById("saveThemeBtn");
    const undoButton = document.getElementById("undoThemeBtn");
    const resetButton = document.getElementById("resetThemeBtn");
    const uploadButton = document.getElementById("logoUploadBtn");
    const logoFileInput = document.getElementById("logoFileInput");
    const backgroundUploadButton = document.getElementById(
      "backgroundUploadBtn",
    );
    const backgroundFileInput = document.getElementById("backgroundFileInput");
    const logoImage = document.getElementById("themeLogoPreview");
    const backgroundPreview = document.getElementById("themeBackgroundPreview");
    const liveLogo = document.getElementById("themeLiveLogo");
    const livePreview = document.getElementById("themeLivePreview");
    const liveBody = livePreview.querySelector(".theme-ui-live-body");
    const liveInstitution = document.getElementById("themeLiveInstitution");
    const liveDepartment = document.getElementById("themeLiveDepartment");

    const fallbackLogo = "assets/images/logo.svg";

    const previewCache = {
      logoUrl: "",
      authBackgroundImage: "",
      primaryColor: "",
      secondaryColor: "",
      bodyFont: "",
      headingFont: "",
      institutionName: "",
      departmentName: "",
    };

    let updateTimer = null;
    let updateFrame = null;

    let initialTheme = { ...defaults };
    let initialSite = { ...siteDefaults };

    bindPreviewImageFallback(logoImage);
    bindPreviewImageFallback(liveLogo);

    if (
      typeof App !== "undefined" &&
      typeof App.initSettingsShell === "function"
    ) {
      App.initSettingsShell({
        currentPath: "/admin/settings/theme",
        sidebarId: "themeSidebar",
        toggleId: "themeSidebarToggle",
        backdropId: "themeSidebarBackdrop",
        nameId: "themeTopbarName",
        roleId: "themeTopbarRole",
        logoutId: "themeTopbarLogout",
      });
    }

    bindFormInteractions();
    await loadSettings();

    function bindFormInteractions() {
      const colorBindings = [
        { picker: "primaryColor", text: "primaryColorText" },
        { picker: "secondaryColor", text: "secondaryColorText" },
        { picker: "accentColor", text: "accentColorText" },
      ];

      colorBindings.forEach(({ picker, text }) => {
        const pickerInput = document.getElementById(picker);
        const textInput = document.getElementById(text);

        pickerInput.addEventListener("input", () => {
          textInput.value = pickerInput.value;
        });

        textInput.addEventListener("input", () => {
          const normalized = normalizeHexColor(textInput.value);
          if (normalized) {
            pickerInput.value = normalized;
          }
        });
      });

      form.addEventListener("input", (event) => {
        const target = event.target;
        if (
          target &&
          [
            "primaryColorText",
            "secondaryColorText",
            "accentColorText",
          ].includes(target.id) &&
          !normalizeHexColor(target.value)
        ) {
          updateActionState();
          return;
        }

        onFormChange();
      });

      form.addEventListener("submit", async (event) => {
        event.preventDefault();
        const themePayload = collectThemeState();
        const sitePayload = collectSiteState();

        Utils.setButtonLoading("#saveThemeBtn", true, "Saving...");

        try {
          await Promise.all([
            API.admin.settings.updateTheme(themePayload),
            API.admin.settings.updateSiteContent(sitePayload),
          ]);

          initialTheme = { ...themePayload };
          initialSite = { ...sitePayload };

          if (
            typeof App !== "undefined" &&
            typeof App.applyTheme === "function"
          ) {
            App.applyTheme(themePayload);
          }

          if (
            typeof App !== "undefined" &&
            typeof App.applySiteContent === "function"
          ) {
            App.applySiteContent(sitePayload);
          }

          updateLivePreview(themePayload, sitePayload);
          updateActionState(themePayload, sitePayload);
          Utils.success("Theme settings saved");
        } catch (error) {
          Utils.error(error.message || "Failed to save theme settings");
        } finally {
          Utils.setButtonLoading("#saveThemeBtn", false, "Save Changes");
        }
      });

      uploadButton.addEventListener("click", () => {
        if (!logoFileInput) {
          return;
        }

        logoFileInput.click();
      });

      logoFileInput?.addEventListener("change", async () => {
        const selectedFile = logoFileInput.files?.[0];
        if (!selectedFile) {
          return;
        }

        if (selectedFile.size > 5 * 1024 * 1024) {
          Utils.error("Logo must be 5MB or smaller.");
          logoFileInput.value = "";
          return;
        }

        const formData = new FormData();
        formData.append("logo", selectedFile);

        Utils.setButtonLoading("#logoUploadBtn", true, "Uploading...");

        try {
          const response = await API.admin.settings.uploadLogo(formData);
          const uploadedLogoUrl =
            response?.data?.logo_url || response?.data?.url || "";

          if (!uploadedLogoUrl) {
            throw new Error("Upload response did not include a logo URL.");
          }

          form.elements.logo_url.value = uploadedLogoUrl;
          onFormChange();
          Utils.success("Logo uploaded successfully.");
        } catch (error) {
          Utils.error(error.message || "Failed to upload logo.");
        } finally {
          Utils.setButtonLoading("#logoUploadBtn", false, "Upload");
          logoFileInput.value = "";
        }
      });

      backgroundUploadButton?.addEventListener("click", () => {
        if (!backgroundFileInput) {
          return;
        }

        backgroundFileInput.click();
      });

      backgroundFileInput?.addEventListener("change", async () => {
        const selectedFile = backgroundFileInput.files?.[0];
        if (!selectedFile) {
          return;
        }

        if (selectedFile.size > 7 * 1024 * 1024) {
          Utils.error("Background image must be 7MB or smaller.");
          backgroundFileInput.value = "";
          return;
        }

        const formData = new FormData();
        formData.append("background", selectedFile);

        Utils.setButtonLoading("#backgroundUploadBtn", true, "Uploading...");

        try {
          const response = await API.admin.settings.uploadBackground(formData);
          const uploadedBackgroundUrl =
            response?.data?.auth_background_image_url ||
            response?.data?.url ||
            "";

          if (!uploadedBackgroundUrl) {
            throw new Error(
              "Upload response did not include a background URL.",
            );
          }

          form.elements.auth_background_image_url.value = uploadedBackgroundUrl;
          onFormChange();
          Utils.success("Background image uploaded successfully.");
        } catch (error) {
          Utils.error(error.message || "Failed to upload background image.");
        } finally {
          Utils.setButtonLoading("#backgroundUploadBtn", false, "Upload");
          backgroundFileInput.value = "";
        }
      });

      undoButton.addEventListener("click", () => {
        flushPendingFormUpdates();
        applyStateToForm(initialTheme, initialSite);

        applyQueuedFormState(initialTheme, initialSite);
      });

      resetButton.addEventListener("click", () => {
        if (
          !window.confirm(
            "Reset current form values to original theme defaults?",
          )
        ) {
          return;
        }

        flushPendingFormUpdates();
        applyStateToForm(defaults, siteDefaults);
        applyQueuedFormState(defaults, siteDefaults);
      });
    }

    async function loadSettings() {
      try {
        const [themeResponse, siteResponse] = await Promise.all([
          API.admin.settings.getTheme(),
          API.admin.settings.getSiteContent().catch(() => ({ data: {} })),
        ]);

        const theme = { ...defaults, ...(themeResponse.data || {}) };
        if (!theme.body_font && theme.font_family) {
          theme.body_font = theme.font_family;
        }
        if (!theme.heading_font && theme.font_family) {
          theme.heading_font = theme.font_family;
        }
        if (!theme.font_family && theme.body_font) {
          theme.font_family = theme.body_font;
        }

        const siteContent = {
          ...siteDefaults,
          ...(siteResponse.data || {}),
        };

        initialTheme = collectThemeState(theme);
        initialSite = collectSiteState(siteContent);

        applyStateToForm(initialTheme, initialSite);

        if (
          typeof App !== "undefined" &&
          typeof App.applyTheme === "function"
        ) {
          App.applyTheme(initialTheme);
        }

        if (
          typeof App !== "undefined" &&
          typeof App.applySiteContent === "function"
        ) {
          App.applySiteContent(initialSite);
        }

        updateLivePreview(initialTheme, initialSite);
        updateActionState(initialTheme, initialSite);
      } catch (error) {
        console.error("Failed to load theme settings:", error);
        Utils.error("Failed to load theme settings");
      }
    }

    function collectThemeState(source = null) {
      const values = source || Object.fromEntries(new FormData(form));
      const bodyFont = (
        values.body_font ||
        values.font_family ||
        defaults.body_font
      ).trim();

      return {
        primary_color:
          normalizeHexColor(values.primary_color) || defaults.primary_color,
        secondary_color:
          normalizeHexColor(values.secondary_color) || defaults.secondary_color,
        accent_color:
          normalizeHexColor(values.accent_color) || defaults.accent_color,
        background_color:
          normalizeHexColor(values.background_color) ||
          defaults.background_color,
        text_color: normalizeHexColor(values.text_color) || defaults.text_color,
        heading_font: (values.heading_font || defaults.heading_font).trim(),
        body_font: bodyFont,
        font_family: bodyFont,
        logo_url: String(values.logo_url || "").trim(),
        auth_background_image_url: String(
          values.auth_background_image_url || "",
        ).trim(),
        favicon_url: String(values.favicon_url || "").trim(),
        sidebar_style: String(
          values.sidebar_style || defaults.sidebar_style,
        ).trim(),
        border_radius: String(
          values.border_radius || defaults.border_radius,
        ).trim(),
        custom_css: String(values.custom_css || "").trim(),
      };
    }

    function collectSiteState(source = null) {
      const values = source || Object.fromEntries(new FormData(form));
      return {
        institution_name: String(
          values.institution_name || siteDefaults.institution_name,
        ).trim(),
        department_name: String(
          values.department_name || siteDefaults.department_name,
        ).trim(),
      };
    }

    function applyStateToForm(theme, site) {
      const mergedTheme = { ...defaults, ...(theme || {}) };
      const mergedSite = { ...siteDefaults, ...(site || {}) };

      Object.entries(mergedTheme).forEach(([key, value]) => {
        const input = form.elements[key];
        if (input) {
          input.value = value;
        }
      });

      Object.entries(mergedSite).forEach(([key, value]) => {
        const input = form.elements[key];
        if (input) {
          input.value = value;
        }
      });

      document.getElementById("primaryColorText").value =
        mergedTheme.primary_color;
      document.getElementById("secondaryColorText").value =
        mergedTheme.secondary_color;
      document.getElementById("accentColorText").value =
        mergedTheme.accent_color;

      document.getElementById("primaryColor").value = mergedTheme.primary_color;
      document.getElementById("secondaryColor").value =
        mergedTheme.secondary_color;
      document.getElementById("accentColor").value = mergedTheme.accent_color;
    }

    function onFormChange() {
      queueFormUpdate();
    }

    function queueFormUpdate() {
      if (updateTimer) {
        window.clearTimeout(updateTimer);
      }

      updateTimer = window.setTimeout(() => {
        updateTimer = null;
        if (updateFrame) {
          window.cancelAnimationFrame(updateFrame);
        }

        updateFrame = window.requestAnimationFrame(() => {
          updateFrame = null;
          applyQueuedFormState();
        });
      }, 90);
    }

    function flushPendingFormUpdates() {
      if (updateTimer) {
        window.clearTimeout(updateTimer);
        updateTimer = null;
      }

      if (updateFrame) {
        window.cancelAnimationFrame(updateFrame);
        updateFrame = null;
      }
    }

    function applyQueuedFormState(themeState = null, siteState = null) {
      const theme = themeState || collectThemeState();
      const site = siteState || collectSiteState();

      if (typeof App !== "undefined" && typeof App.applyTheme === "function") {
        App.applyTheme(theme);
      }

      updateLivePreview(theme, site);
      updateActionState(theme, site);
    }

    function updateLivePreview(theme, site) {
      const safeTheme = theme || collectThemeState();
      const safeSite = site || collectSiteState();

      const logoUrl = resolvePreviewLogoUrl(safeTheme.logo_url) || fallbackLogo;
      syncPreviewImageSource(logoImage, logoUrl);
      syncPreviewImageSource(liveLogo, logoUrl);

      const authBackgroundUrl = resolvePreviewLogoUrl(
        safeTheme.auth_background_image_url,
      );
      const safeAuthBackground = String(authBackgroundUrl || "").trim();
      const escapedAuthBackground = safeAuthBackground.replace(/"/g, '\\"');
      const authBackgroundPreviewLayer = safeAuthBackground
        ? `linear-gradient(135deg, rgb(15 23 42 / 0.42), rgb(15 23 42 / 0.56)), url("${escapedAuthBackground}")`
        : "linear-gradient(135deg, var(--primary-200), var(--primary-100))";

      if (
        backgroundPreview &&
        previewCache.authBackgroundImage !== authBackgroundPreviewLayer
      ) {
        backgroundPreview.style.backgroundImage = authBackgroundPreviewLayer;
        previewCache.authBackgroundImage = authBackgroundPreviewLayer;
      }

      const institutionName =
        safeSite.institution_name || siteDefaults.institution_name;
      const departmentName =
        safeSite.department_name || siteDefaults.department_name;

      if (previewCache.institutionName !== institutionName) {
        liveInstitution.textContent = institutionName;
        previewCache.institutionName = institutionName;
      }

      if (previewCache.departmentName !== departmentName) {
        liveDepartment.textContent = departmentName;
        previewCache.departmentName = departmentName;
      }

      if (previewCache.primaryColor !== safeTheme.primary_color) {
        livePreview.style.setProperty(
          "--preview-primary",
          safeTheme.primary_color,
        );
        previewCache.primaryColor = safeTheme.primary_color;
      }

      if (previewCache.secondaryColor !== safeTheme.secondary_color) {
        livePreview.style.setProperty(
          "--preview-secondary",
          safeTheme.secondary_color,
        );
        previewCache.secondaryColor = safeTheme.secondary_color;
      }

      const bodyFontStack = toFontStack(safeTheme.body_font);
      if (previewCache.bodyFont !== bodyFontStack) {
        liveBody.style.fontFamily = bodyFontStack;
        previewCache.bodyFont = bodyFontStack;
      }

      const headingFontStack = toFontStack(safeTheme.heading_font);
      if (previewCache.headingFont !== headingFontStack) {
        liveInstitution.style.fontFamily = headingFontStack;
        previewCache.headingFont = headingFontStack;
      }
    }

    function bindPreviewImageFallback(image) {
      if (!image || image.dataset.previewFallbackBound === "true") {
        return;
      }

      image.dataset.previewFallbackBound = "true";
      image.addEventListener("error", () => {
        if (image.dataset.currentSrc === fallbackLogo) {
          return;
        }

        image.dataset.currentSrc = fallbackLogo;
        image.src = fallbackLogo;
      });
    }

    function syncPreviewImageSource(image, nextSource) {
      if (!image) {
        return;
      }

      const safeSource =
        String(nextSource || fallbackLogo).trim() || fallbackLogo;
      if (image.dataset.currentSrc === safeSource) {
        return;
      }

      image.dataset.currentSrc = safeSource;
      image.src = safeSource;
    }

    function updateActionState(themeState = null, siteState = null) {
      const currentTheme = themeState || collectThemeState();
      const currentSite = siteState || collectSiteState();

      const hasThemeChanges =
        JSON.stringify(currentTheme) !== JSON.stringify(initialTheme);
      const hasSiteChanges =
        JSON.stringify(currentSite) !== JSON.stringify(initialSite);

      undoButton.disabled = !(hasThemeChanges || hasSiteChanges);
    }

    function normalizeHexColor(value) {
      const raw = String(value || "").trim();
      if (/^#[0-9a-fA-F]{6}$/.test(raw)) {
        return raw.toLowerCase();
      }

      if (/^#[0-9a-fA-F]{3}$/.test(raw)) {
        const expanded = raw
          .slice(1)
          .split("")
          .map((char) => `${char}${char}`)
          .join("");
        return `#${expanded.toLowerCase()}`;
      }

      return "";
    }

    function resolvePreviewLogoUrl(url) {
      const raw = String(url || "").trim();
      if (!raw) {
        return "";
      }

      if (
        typeof App !== "undefined" &&
        typeof App.resolveThemeAssetUrl === "function"
      ) {
        return App.resolveThemeAssetUrl(raw);
      }

      if (/^\/?uploads\//i.test(raw)) {
        try {
          const apiOrigin = new URL(API.baseUrl, window.location.origin).origin;
          const uploadPath = raw.startsWith("/") ? raw : `/${raw}`;
          return `${apiOrigin}${uploadPath}`;
        } catch {
          return raw;
        }
      }

      return raw;
    }

    function toFontStack(fontValue) {
      const value = String(fontValue || "").trim();
      if (!value) {
        return "Inter, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
      }

      if (value.includes(",")) {
        return value;
      }

      return `"${value}", -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif`;
    }
  })();
</script>
