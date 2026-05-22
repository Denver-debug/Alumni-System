<!-- Security Settings -->
<div class="theme-ui-shell">
  <header class="theme-ui-topbar">
    <div class="theme-ui-topbar-left">
      <button
        type="button"
        class="theme-ui-menu-btn"
        id="settingsSidebarToggle"
        aria-label="Toggle Sidebar"
      >
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
      <h1>Security Settings</h1>
    </div>
    <div class="theme-ui-topbar-right">
      <div class="theme-ui-user-meta">
        <div class="theme-ui-user-name" id="settingsTopbarName">Administrator</div>
        <div class="theme-ui-user-role" id="settingsTopbarRole">System Administrator</div>
      </div>
      <button type="button" class="btn btn-danger btn-sm" id="settingsTopbarLogout">Logout</button>
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
          <h2 class="theme-ui-heading">Security Settings</h2>
          <div class="theme-ui-info-card mb-lg">
            <div class="theme-ui-info-title">Login Protection</div>
            <p>
              Configure login lockouts, session timeout rules, and review locked
              accounts from one place.
            </p>
          </div>

          <div class="security-grid mb-xl">
            <div class="security-stat card p-lg">
              <div class="security-stat-value" id="totalAttempts">0</div>
              <div class="security-stat-label">Total Login Attempts (24h)</div>
            </div>
            <div class="security-stat card p-lg">
              <div class="security-stat-value" id="failedAttempts">0</div>
              <div class="security-stat-label">Failed Attempts (24h)</div>
            </div>
            <div class="security-stat card p-lg">
              <div class="security-stat-value" id="lockedAccounts">0</div>
              <div class="security-stat-label">Currently Locked</div>
            </div>
            <div class="security-stat card p-lg">
              <div class="security-stat-value" id="uniqueIPs">0</div>
              <div class="security-stat-label">Unique IPs (24h)</div>
            </div>
          </div>

          <form id="securitySettingsForm" class="security-form mb-xl">
            <div class="form-group">
              <label class="form-label">Enable Login Lockout</label>
              <button type="button" class="security-toggle" id="lockoutToggle" aria-pressed="false">
                <span class="security-toggle-knob"></span>
              </button>
              <div class="text-sm text-secondary mt-sm" id="lockoutStatus">Disabled</div>
            </div>

            <div class="grid grid-cols-3 gap-lg">
              <div class="form-group">
                <label class="form-label" for="maxAttempts">Maximum Login Attempts</label>
                <input type="number" id="maxAttempts" min="1" max="10" value="5" class="form-input" />
              </div>
              <div class="form-group">
                <label class="form-label" for="lockoutDuration">Lockout Duration (minutes)</label>
                <input type="number" id="lockoutDuration" min="5" max="1440" value="30" class="form-input" />
              </div>
              <div class="form-group">
                <label class="form-label" for="sessionTimeout">Session Timeout (minutes)</label>
                <input type="number" id="sessionTimeout" min="15" max="480" value="60" class="form-input" />
              </div>
            </div>

            <div class="mt-lg">
              <button type="submit" class="btn btn-primary">Save Settings</button>
            </div>
          </form>

          <div class="theme-ui-panel card p-lg">
            <h3 class="font-bold mb-lg">Locked Accounts</h3>
            <div class="table-responsive">
              <table class="table">
                <thead>
                  <tr>
                    <th>Email</th>
                    <th>Locked Until</th>
                    <th>Attempts</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="lockedAccountsList">
                  <tr>
                    <td colspan="4" class="text-center">Loading...</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</div>

<style>
  .security-grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 1rem;
  }

  .security-stat {
    text-align: center;
    background: var(--color-surface);
    border: 1px solid var(--color-border);
    border-radius: var(--radius-xl);
  }

  .security-stat-value {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--color-primary);
    line-height: 1;
  }

  .security-stat-label {
    margin-top: 0.4rem;
    font-size: 0.875rem;
    color: var(--color-text-secondary);
  }

  .security-toggle {
    position: relative;
    width: 56px;
    height: 30px;
    border: 0;
    border-radius: 999px;
    background: var(--gray-300);
    padding: 0;
    cursor: pointer;
    transition: background-color var(--transition-normal);
  }

  .security-toggle.active {
    background: var(--color-primary);
  }

  .security-toggle-knob {
    position: absolute;
    top: 3px;
    left: 3px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
    transition: transform var(--transition-normal);
  }

  .security-toggle.active .security-toggle-knob {
    transform: translateX(26px);
  }

  .security-form .form-input {
    width: 100%;
  }

  @media (max-width: 1100px) {
    .security-grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }

  @media (max-width: 700px) {
    .security-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<script>
  (function () {
    const user = API.getUser();
    if (!user || !["admin", "system_admin"].includes(user.role)) {
      Utils.error("Admin access required");
      Router.navigate("/admin/login");
      return;
    }

    if (typeof App.initSettingsShell === "function") {
      App.initSettingsShell({
        currentPath: "/admin/settings/security",
      });
    }

    const lockoutToggle = document.getElementById("lockoutToggle");
    const lockoutStatus = document.getElementById("lockoutStatus");
    const maxAttemptsInput = document.getElementById("maxAttempts");
    const lockoutDurationInput = document.getElementById("lockoutDuration");
    const sessionTimeoutInput = document.getElementById("sessionTimeout");
    const lockedAccountsList = document.getElementById("lockedAccountsList");
    const settingsForm = document.getElementById("securitySettingsForm");

    const state = {
      lockoutEnabled: false,
    };

    function getSettingValue(settings, key, fallback) {
      const entry = settings?.[key];
      if (entry && typeof entry === "object" && "value" in entry) {
        return entry.value;
      }
      return entry ?? fallback;
    }

    function setToggle(enabled) {
      state.lockoutEnabled = Boolean(enabled);
      lockoutToggle.classList.toggle("active", state.lockoutEnabled);
      lockoutToggle.setAttribute("aria-pressed", String(state.lockoutEnabled));
      lockoutStatus.textContent = state.lockoutEnabled ? "Enabled" : "Disabled";
    }

    async function loadSettings() {
      const response = await API.security.getSettings();
      const settings = response?.data || {};

      setToggle(getSettingValue(settings, "enable_login_lockout", false));
      maxAttemptsInput.value = getSettingValue(settings, "max_login_attempts", 5);
      lockoutDurationInput.value = getSettingValue(settings, "lockout_duration_minutes", 30);
      sessionTimeoutInput.value = getSettingValue(settings, "session_timeout_minutes", 60);
    }

    async function loadStats() {
      const response = await API.security.getStats();
      const stats = response?.data || {};
      const today = stats.today || {};

      document.getElementById("totalAttempts").textContent = today.total_attempts ?? 0;
      document.getElementById("failedAttempts").textContent = today.failed ?? 0;
      document.getElementById("lockedAccounts").textContent = stats.active_lockouts ?? 0;
      document.getElementById("uniqueIPs").textContent = Array.isArray(stats.top_failed_ips)
        ? stats.top_failed_ips.length
        : 0;
    }

    async function loadLockedAccounts() {
      const response = await API.security.getLockedAccounts();
      const accounts = Array.isArray(response?.data) ? response.data : [];

      if (!accounts.length) {
        lockedAccountsList.innerHTML = '<tr><td colspan="4" class="text-center">No locked accounts</td></tr>';
        return;
      }

      lockedAccountsList.innerHTML = accounts
        .map((account) => {
          const email = account.email || account.name || "-";
          const lockedUntil = account.locked_until
            ? new Date(account.locked_until).toLocaleString()
            : "-";
          return `
            <tr>
              <td>${Utils.escapeHtml(email)}</td>
              <td>${Utils.escapeHtml(lockedUntil)}</td>
              <td>${Number(account.login_attempts || 0)}</td>
              <td>
                <button type="button" class="btn btn-sm btn-danger" data-unlock-id="${account.user_id}">
                  Unlock
                </button>
              </td>
            </tr>
          `;
        })
        .join("");
    }

    lockoutToggle.addEventListener("click", () => {
      setToggle(!state.lockoutEnabled);
    });

    settingsForm.addEventListener("submit", async (event) => {
      event.preventDefault();

      const payload = {
        enable_login_lockout: state.lockoutEnabled,
        max_login_attempts: parseInt(maxAttemptsInput.value, 10),
        lockout_duration_minutes: parseInt(lockoutDurationInput.value, 10),
        session_timeout_minutes: parseInt(sessionTimeoutInput.value, 10),
      };

      try {
        await API.security.updateSettings(payload);
        Utils.success("Security settings saved successfully.");
      } catch (error) {
        console.error("Error saving settings:", error);
        Utils.error("Failed to save settings. Please try again.");
      }
    });

    lockedAccountsList.addEventListener("click", async (event) => {
      const button = event.target.closest("button[data-unlock-id]");
      if (!button) {
        return;
      }

      if (!confirm("Unlock this account?")) {
        return;
      }

      try {
        await API.security.unlockAccount(button.dataset.unlockId);
        Utils.success("Account unlocked successfully.");
        await Promise.all([loadLockedAccounts(), loadStats()]);
      } catch (error) {
        console.error("Error unlocking account:", error);
        Utils.error("Failed to unlock account. Please try again.");
      }
    });

    Promise.all([loadSettings(), loadStats(), loadLockedAccounts()]).catch((error) => {
      console.error("Security settings failed to load:", error);
      Utils.error("Security settings could not be loaded.");
    });
  })();
</script>
