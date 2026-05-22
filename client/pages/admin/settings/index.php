<!-- Admin Settings Index -->
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
      <h1>Settings</h1>
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
      <section class="theme-ui-panel card">
        <div class="theme-ui-panel-body">
          <h2 class="theme-ui-heading">Settings</h2>

          <div class="theme-ui-info-card">
            <div class="theme-ui-info-title">Administration Settings</div>
            <p>
              Manage platform behavior, email delivery, and quick links to
              Theme, Site Content, and Email Management.
            </p>
          </div>

          <div class="grid grid-cols-3 gap-lg settings-grid">
            <a
              href="#/admin/settings/site-content"
              class="card p-xl text-center settings-card interactive"
            >
              <h3 class="font-bold mb-sm">Site Content</h3>
              <p class="text-sm text-muted">
                Manage homepage content, contact details, and social links.
              </p>
            </a>

            <a
              href="#/admin/settings/security"
              class="card p-xl text-center settings-card interactive"
            >
              <h3 class="font-bold mb-sm">Security Settings</h3>
              <p class="text-sm text-muted">
                Configure login lockouts, session timeouts, and audit data.
              </p>
            </a>

            <a
              href="#/admin/settings/theme"
              class="card p-xl text-center settings-card interactive"
            >
              <h3 class="font-bold mb-sm">Theme</h3>
              <p class="text-sm text-muted">
                Configure visual colors and branding options.
              </p>
            </a>

            <a
              href="#/admin/settings/email-templates"
              class="card p-xl text-center settings-card interactive"
            >
              <h3 class="font-bold mb-sm">Email Templates</h3>
              <p class="text-sm text-muted">
                Update message templates and notification content.
              </p>
            </a>

            <div class="card p-xl">
              <h3 class="font-bold mb-lg">General Settings</h3>
              <form id="generalSettingsForm">
                <div class="form-group">
                  <label class="form-label">Maintenance Mode</label>
                  <label class="flex items-center gap-sm">
                    <input type="checkbox" id="maintenanceMode" />
                    <span>Enable maintenance mode</span>
                  </label>
                </div>

                <div class="form-group">
                  <label class="form-label">Registration</label>
                  <label class="flex items-center gap-sm">
                    <input type="checkbox" id="registrationEnabled" checked />
                    <span>Allow new registrations</span>
                  </label>
                </div>

                <div class="form-group">
                  <label class="form-label">Email Verification</label>
                  <label class="flex items-center gap-sm">
                    <input type="checkbox" id="emailVerification" checked />
                    <span>Require email verification</span>
                  </label>
                </div>

                <button type="submit" class="btn btn-primary">
                  Save Settings
                </button>
              </form>
            </div>

            <div class="card p-xl">
              <h3 class="font-bold mb-lg">Email Configuration</h3>
              <form id="emailConfigForm">
                <div class="form-group">
                  <label class="form-label">SMTP Host</label>
                  <input
                    type="text"
                    name="smtp_host"
                    class="form-input"
                    placeholder="smtp.gmail.com"
                  />
                </div>

                <div class="form-group">
                  <label class="form-label">SMTP Port</label>
                  <input
                    type="number"
                    name="smtp_port"
                    class="form-input"
                    placeholder="587"
                  />
                </div>

                <div class="form-group">
                  <label class="form-label">SMTP Username</label>
                  <input
                    type="text"
                    name="smtp_username"
                    class="form-input"
                    placeholder="email@domain.com"
                  />
                </div>

                <div class="form-group">
                  <label class="form-label">SMTP Password</label>
                  <input
                    type="password"
                    name="smtp_password"
                    class="form-input"
                    placeholder="password"
                  />
                </div>

                <div class="form-group">
                  <label class="form-label">From Email</label>
                  <input
                    type="email"
                    name="from_email"
                    class="form-input"
                    placeholder="noreply@example.com"
                  />
                </div>

                <div class="form-group">
                  <label class="form-label">From Name</label>
                  <input
                    type="text"
                    name="from_name"
                    class="form-input"
                    placeholder="Alumni System"
                  />
                </div>

                <button type="submit" class="btn btn-primary">
                  Save Email Config
                </button>
              </form>
            </div>

            <div class="card p-xl">
              <h3 class="font-bold mb-lg">System Information</h3>
              <div class="space-y-sm text-sm">
                <div class="flex justify-between">
                  <span class="text-muted">Version</span>
                  <span>1.0.0</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-muted">PHP Version</span>
                  <span id="phpVersion">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-muted">Database</span>
                  <span>MySQL</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-muted">Total Alumni</span>
                  <span id="totalAlumni">-</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-muted">Total Events</span>
                  <span id="totalEvents">-</span>
                </div>
              </div>
              <button
                class="btn btn-secondary w-full mt-lg"
                onclick="clearCache()"
              >
                Clear Cache
              </button>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</div>

<style>
  .settings-card {
    transition:
      transform 0.2s ease,
      box-shadow 0.2s ease;
  }

  .settings-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  }
</style>

<script>
  (function () {
    if (!Auth.isAdmin()) {
      window.location.hash = "#/admin/login";
      return;
    }

    if (
      typeof App !== "undefined" &&
      typeof App.initSettingsShell === "function"
    ) {
      App.initSettingsShell({
        currentPath: "/admin/settings",
      });
    }

    loadSettings();
    loadEmailConfig();
    loadSystemInfo();

    async function loadSettings() {
      try {
        const response = await API.get("/settings/general");
        if (response.data) {
          Utils.$("#maintenanceMode").checked =
            !!response.data.maintenance_mode;
          Utils.$("#registrationEnabled").checked =
            response.data.registration_enabled !== false;
          Utils.$("#emailVerification").checked =
            response.data.email_verification_required !== false;
        }
      } catch (error) {
        console.error("Failed to load settings:", error);
      }
    }

    async function loadSystemInfo() {
      try {
        const response = await API.get("/settings/system-info");
        if (response.data) {
          Utils.$("#phpVersion").textContent = response.data.php_version || "-";
          Utils.$("#totalAlumni").textContent = response.data.total_alumni || 0;
          Utils.$("#totalEvents").textContent = response.data.total_events || 0;
        }
      } catch (error) {
        console.error("Failed to load system info:", error);
      }
    }

    async function loadEmailConfig() {
      try {
        const response = await API.admin.settings.getEmail();
        const payload = response?.data || {};
        const settings = payload.settings || {};
        const form = Utils.$("#emailConfigForm");
        if (!form) return;

        const values = {
          smtp_host: settings.smtp_host ?? payload.smtpHost ?? "",
          smtp_port: settings.smtp_port ?? payload.smtpPort ?? "587",
          smtp_username: settings.smtp_username ?? payload.smtpUsername ?? "",
          from_email: settings.from_email ?? payload.fromEmail ?? "",
          from_name: settings.from_name ?? payload.fromName ?? "",
        };

        Object.entries(values).forEach(([key, value]) => {
          const input = form.elements[key];
          if (input && value !== null && value !== undefined) {
            input.value = String(value);
          }
        });

        const smtpPasswordInput = form.elements.smtp_password;
        if (smtpPasswordInput) {
          const passwordIsStored =
            settings.smtp_password === "********" ||
            payload.smtpPassword === "********";

          smtpPasswordInput.value = "";
          if (passwordIsStored) {
            smtpPasswordInput.placeholder = "********";
          }
        }
      } catch (error) {
        console.error("Failed to load email configuration:", error);
      }
    }

    Utils.$("#generalSettingsForm").addEventListener("submit", async (e) => {
      e.preventDefault();

      const data = {
        maintenance_mode: Utils.$("#maintenanceMode").checked,
        registration_enabled: Utils.$("#registrationEnabled").checked,
        email_verification_required: Utils.$("#emailVerification").checked,
      };

      try {
        await API.put("/settings/general", data);
        Utils.success("Settings saved");
      } catch (error) {
        Utils.error(error.message || "Failed to save settings");
      }
    });

    Utils.$("#emailConfigForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const data = Object.fromEntries(formData);

      if (!data.smtp_password) {
        delete data.smtp_password;
      }

      try {
        await API.admin.settings.updateEmail(data);
        Utils.success("Email configuration saved");
      } catch (error) {
        Utils.error(error.message || "Failed to save email config");
      }
    });

    window.clearCache = async function () {
      try {
        await API.post("/settings/clear-cache");
        Utils.success("Cache cleared");
      } catch (error) {
        Utils.error("Failed to clear cache");
      }
    };
  })();
</script>
