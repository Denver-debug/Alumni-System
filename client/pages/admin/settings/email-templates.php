<!-- Email Templates Settings -->
<link rel="stylesheet" href="/assets/css/admin-pages-improved.css">
<link rel="stylesheet" href="/assets/css/admin-premium.css">

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
      <h1>Email Management</h1>
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
      <section class="theme-ui-panel email-management-card">
        <div class="theme-ui-panel-body">
          <div class="email-tabbar" aria-label="Email settings sections">
            <button type="button" class="tab-btn active" data-scroll-target="emailContactPanel">Sender & SMTP</button>
            <button type="button" class="tab-btn" data-scroll-target="emailTemplatesPanel">Email Templates</button>
          </div>

          <div class="email-info-panel">
            Configure sender identity, SMTP delivery, and reusable email templates for alumni notifications.
          </div>

          <form id="emailSettingsForm" class="mb-xl">
            <h3 class="email-section-title" id="emailContactPanel">Sender & SMTP Settings</h3>
            <div class="email-settings-grid">
              <div class="form-group">
                <label class="form-label">From Name</label>
                <input type="text" name="from_name" class="form-input" placeholder="Alumni System" />
              </div>
              <div class="form-group">
                <label class="form-label">From Email</label>
                <input type="email" name="from_email" class="form-input" placeholder="alumni@minsu.edu.ph" />
              </div>
              <div class="form-group">
                <label class="form-label">SMTP Host</label>
                <input type="text" name="smtp_host" class="form-input" placeholder="smtp.hostinger.com" />
              </div>
              <div class="form-group">
                <label class="form-label">SMTP Port</label>
                <input type="number" name="smtp_port" class="form-input" placeholder="587" />
              </div>
              <div class="form-group">
                <label class="form-label">SMTP Username</label>
                <input type="text" name="smtp_username" class="form-input" autocomplete="username" />
              </div>
              <div class="form-group">
                <label class="form-label">SMTP Password</label>
                <input type="password" name="smtp_password" class="form-input" autocomplete="new-password" placeholder="Leave unchanged to keep saved password" />
              </div>
              <div class="form-group">
                <label class="form-label">Encryption</label>
                <select name="smtp_encryption" class="form-input">
                  <option value="tls">TLS</option>
                  <option value="ssl">SSL</option>
                  <option value="">None</option>
                </select>
              </div>
            </div>
            <div class="admin-actions-row">
              <button type="submit" class="btn-icon btn-primary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                  <polyline points="17 21 17 13 7 13 7 21"></polyline>
                  <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                Save Email Settings
              </button>
            </div>
          </form>

          <h3 class="email-section-title" id="emailTemplatesPanel">Email Templates</h3>
          <div class="email-template-layout">
            <!-- Template List -->
            <div class="card-improved">
              <div class="card-header">
                <h3 class="card-title">Templates</h3>
              </div>
              <div id="templateList" class="p-md">
                <div class="loading-skeleton" style="height: 3rem; margin: 0.5rem;"></div>
              </div>
            </div>

            <!-- Editor -->
            <div class="card-improved" style="grid-column: span 2;">
              <div class="card-header">
                <h3 class="card-title" id="editorTitle">Select a template</h3>
              </div>
              <form id="templateForm" class="p-lg form-improved" style="display: none">
                <div class="form-group">
                  <label class="form-label required">Subject</label>
                  <input
                    type="text"
                    name="subject"
                    class="form-input"
                    required
                  />
                </div>

                <div class="form-group">
                  <label class="form-label required">Body</label>
                  <textarea
                    name="body"
                    class="form-textarea font-mono"
                    rows="15"
                    required
                  ></textarea>
                </div>

                <div class="alert alert-info mb-lg" style="background: #dbeafe; border: 1px solid #93c5fd; padding: 1rem; border-radius: 8px;">
                  <strong style="color: #1e40af;">Available Variables:</strong>
                  <div id="availableVars" class="mt-sm text-sm font-mono" style="color: #1e40af; margin-top: 0.5rem;"></div>
                </div>

                <div class="flex gap-md">
                  <button type="submit" class="btn-icon btn-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                      <polyline points="17 21 17 13 7 13 7 21"></polyline>
                      <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    Save Template
                  </button>
                  <button
                    type="button"
                    class="btn-icon btn-secondary"
                    onclick="previewTemplate()"
                  >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                      <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Preview
                  </button>
                  <button
                    type="button"
                    class="btn-icon btn-ghost"
                    onclick="resetTemplate()"
                  >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="1 4 1 10 7 10"></polyline>
                      <polyline points="23 20 23 14 17 14"></polyline>
                      <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path>
                    </svg>
                    Reset to Default
                  </button>
                </div>
              </form>
              <div id="noTemplate" class="p-xl text-center text-muted">
                <div class="empty-state">
                  <div class="empty-state-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                      <polyline points="22,6 12,13 2,6"></polyline>
                    </svg>
                  </div>
                  <div class="empty-state-title">Select a template</div>
                  <div class="empty-state-description">Choose a template from the list to edit its content</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
  </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" class="modal-improved" style="display: none">
  <div class="modal-backdrop" onclick="closePreview()"></div>
  <div class="modal-dialog" style="max-width: 700px;">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Email Preview</h3>
        <button class="modal-close" onclick="closePreview()">&times;</button>
      </div>
      <div class="modal-body" id="previewContent"></div>
    </div>
  </div>
</div>

<style>
  .template-item {
    padding: 0.875rem 1rem;
    cursor: pointer;
    border-bottom: 1px solid #e5e7eb;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  .template-item:hover {
    background: #f9fafb;
  }
  .template-item.active {
    background: #d1fae5;
    border-left: 3px solid #047857;
    font-weight: 600;
    color: #047857;
  }
  .template-item::before {
    content: "📧";
    font-size: 1.25rem;
  }
  .grid-cols-3 {
    grid-template-columns: 300px 1fr;
  }
  @media (max-width: 1024px) {
    .grid-cols-3 {
      grid-template-columns: 1fr;
    }
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
        currentPath: "/admin/settings/email-templates",
      });
    }

    let currentTemplate = null;
    let templateValues = {};

    const templates = [
      {
        id: "verification",
        name: "Email Verification",
        vars: ["{{name}}", "{{code}}", "{{expiry}}"],
      },
      {
        id: "password_reset",
        name: "Password Reset",
        vars: ["{{name}}", "{{code}}", "{{expiry}}"],
      },
      {
        id: "welcome",
        name: "Welcome Email",
        vars: ["{{name}}", "{{alumni_id}}", "{{login_url}}"],
      },
      {
        id: "event_reminder",
        name: "Event Reminder",
        vars: [
          "{{name}}",
          "{{event_title}}",
          "{{event_date}}",
          "{{event_location}}",
        ],
      },
      {
        id: "event_registration",
        name: "Event Registration",
        vars: ["{{name}}", "{{event_title}}", "{{event_date}}"],
      },
      {
        id: "points_earned",
        name: "Points Earned",
        vars: ["{{name}}", "{{points}}", "{{activity}}", "{{total_points}}"],
      },
      {
        id: "announcement",
        name: "New Announcement",
        vars: [
          "{{name}}",
          "{{announcement_title}}",
          "{{announcement_excerpt}}",
        ],
      },
    ];

    // Render template list
    Utils.$("#templateList").innerHTML = templates
      .map(
        (t) => `
    <div class="template-item" data-id="${t.id}" onclick="selectTemplate('${t.id}')">
      ${t.name}
    </div>
  `,
      )
      .join("");

    setupEmailTabs();
    setupEmailSettingsForm();
    loadTemplateValues();

    function getBranding() {
      if (
        typeof App !== "undefined" &&
        typeof App.getBrandingSnapshot === "function"
      ) {
        return App.getBrandingSnapshot();
      }

      return {
        institutionName: "Mindoro State University",
        siteName: "Alumni Network",
      };
    }

    async function loadTemplateValues() {
      try {
        const response = await API.admin.settings.getEmail();
        populateEmailSettings(response?.data || {});
        const existingTemplates = Array.isArray(response?.data?.templates)
          ? response.data.templates
          : [];

        templateValues = {};
        existingTemplates.forEach((template) => {
          const key = template.template_key || template.key || template.type;
          if (!key) return;
          templateValues[key] = {
            subject: template.subject || "",
            body: template.body || "",
          };
        });
      } catch (error) {
        console.error("Failed to load email templates:", error);
      }
    }

    function setupEmailTabs() {
      document.querySelectorAll(".email-tabbar [data-scroll-target]").forEach((button) => {
        button.addEventListener("click", () => {
          document
            .querySelectorAll(".email-tabbar .tab-btn")
            .forEach((item) => item.classList.remove("active"));
          button.classList.add("active");

          const target = document.getElementById(button.dataset.scrollTarget || "");
          if (target) {
            target.scrollIntoView({ behavior: "smooth", block: "start" });
          }
        });
      });
    }

    function populateEmailSettings(data) {
      const settings = data.settings || {};
      const values = {
        from_name: settings.from_name || data.fromName || "Alumni System",
        from_email: settings.from_email || data.fromEmail || "",
        smtp_host: settings.smtp_host || data.smtpHost || "",
        smtp_port: settings.smtp_port || data.smtpPort || "587",
        smtp_username: settings.smtp_username || data.smtpUsername || "",
        smtp_password: settings.smtp_password || data.smtpPassword || "",
        smtp_encryption:
          settings.smtp_encryption ||
          (data.smtpSecure ? "ssl" : "tls"),
      };

      Object.entries(values).forEach(([key, value]) => {
        const input = document.querySelector(`#emailSettingsForm [name="${key}"]`);
        if (input) {
          input.value = value || "";
        }
      });
    }

    function setupEmailSettingsForm() {
      const form = Utils.$("#emailSettingsForm");
      if (!form) return;

      form.addEventListener("submit", async (event) => {
        event.preventDefault();
        const data = Object.fromEntries(new FormData(form));

        if (data.smtp_password === "********") {
          delete data.smtp_password;
        }

        try {
          await API.admin.settings.updateEmail(data);
          Utils.success("Email settings saved");
        } catch (error) {
          Utils.error(error.message || "Failed to save email settings");
        }
      });
    }

    window.selectTemplate = async function (id) {
      currentTemplate = templates.find((t) => t.id === id);

      // Update UI
      document
        .querySelectorAll(".template-item")
        .forEach((el) => el.classList.remove("active"));
      document.querySelector(`[data-id="${id}"]`).classList.add("active");

      Utils.$("#editorTitle").textContent = currentTemplate.name;
      Utils.$("#availableVars").textContent = currentTemplate.vars.join(", ");
      Utils.$("#templateForm").style.display = "block";
      Utils.$("#noTemplate").style.display = "none";

      // Load template data
      const saved = templateValues[id] || {};
      Utils.$('[name="subject"]').value =
        saved.subject || getDefaultSubject(id);
      Utils.$('[name="body"]').value = saved.body || getDefaultBody(id);
    };

    function getDefaultSubject(id) {
      const branding = getBranding();
      const subjects = {
        verification: `Verify Your Email - ${branding.siteName}`,
        password_reset: `Reset Your Password - ${branding.siteName}`,
        welcome: `Welcome to ${branding.siteName}!`,
        event_reminder: "Reminder: {{event_title}} is Coming Up!",
        event_registration: "Event Registration Confirmed",
        points_earned: "You Earned {{points}} Points!",
        announcement: "New Announcement: {{announcement_title}}",
      };
      return subjects[id] || "";
    }

    function getDefaultBody(id) {
      const branding = getBranding();
      const bodies = {
        verification: `Hello {{name}},

Your verification code is: {{code}}

This code will expire in {{expiry}} minutes.

If you didn't request this, please ignore this email.

Best regards,
${branding.siteName} Team`,
        password_reset: `Hello {{name}},

Your password reset code is: {{code}}

This code will expire in {{expiry}} minutes.

If you didn't request this, please ignore this email.

Best regards,
${branding.siteName} Team`,
        welcome: `Welcome {{name}}!

Your alumni account has been created successfully.

Your Alumni ID: {{alumni_id}}

Login here: {{login_url}}

Best regards,
${branding.siteName} Team`,
      };
      return bodies[id] || "";
    }

    Utils.$("#templateForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      if (!currentTemplate) return;

      const data = {
        subject: Utils.$('[name="subject"]').value,
        body: Utils.$('[name="body"]').value,
      };

      try {
        await API.admin.settings.updateEmailTemplate(currentTemplate.id, data);
        templateValues[currentTemplate.id] = {
          subject: data.subject,
          body: data.body,
        };
        Utils.success("Template saved");
      } catch (error) {
        Utils.error(error.message || "Failed to save template");
      }
    });

    window.previewTemplate = function () {
      if (!currentTemplate) return;

      let subject = Utils.$('[name="subject"]').value;
      let body = Utils.$('[name="body"]').value;

      // Replace variables with sample data
      const branding = getBranding();
      const sampleData = {
        "{{name}}": "John Doe",
        "{{code}}": "123456",
        "{{expiry}}": "10",
        "{{alumni_id}}": "BBC-2026-CCS-00001",
        "{{login_url}}": `${window.location.origin}/#/login`,
        "{{event_title}}": "Alumni Homecoming 2026",
        "{{event_date}}": "March 15, 2026",
        "{{event_location}}": `${branding.institutionName} Main Campus`,
        "{{points}}": "50",
        "{{activity}}": "Event Attendance",
        "{{total_points}}": "250",
        "{{announcement_title}}": "Important Update",
        "{{announcement_excerpt}}": "Lorem ipsum dolor sit amet...",
      };

      Object.keys(sampleData).forEach((key) => {
        subject = subject.replace(
          new RegExp(key.replace(/[{}]/g, "\\$&"), "g"),
          sampleData[key],
        );
        body = body.replace(
          new RegExp(key.replace(/[{}]/g, "\\$&"), "g"),
          sampleData[key],
        );
      });

      Utils.$("#previewContent").innerHTML = `
      <div class="mb-lg" style="padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
        <strong style="color: #374151;">Subject:</strong> 
        <div style="margin-top: 0.5rem; color: #1f2937;">${Utils.escapeHtml(subject)}</div>
      </div>
      <div style="white-space: pre-wrap; line-height: 1.6; color: #374151;">${Utils.escapeHtml(body)}</div>
    `;
      Utils.$("#previewModal").style.display = "flex";
    };

    window.closePreview = function () {
      Utils.$("#previewModal").style.display = "none";
    };

    window.resetTemplate = function () {
      if (!currentTemplate) return;
      if (!confirm("Reset this template to default?")) return;

      Utils.$('[name="subject"]').value = getDefaultSubject(currentTemplate.id);
      Utils.$('[name="body"]').value = getDefaultBody(currentTemplate.id);
    };
  })();
</script>

