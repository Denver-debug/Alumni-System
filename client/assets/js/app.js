/**
 * Alumni Management System - Main Application
 */

// Wait for DOM ready
document.addEventListener("DOMContentLoaded", () => {
  App.init();
});

const App = {
  /**
   * Initialize application
   */
  async init() {
    console.log("Initializing Alumni Management System...");

    // Load theme settings
    await this.loadTheme();

    // Initialize auth state
    Auth.init();

    // Setup router
    this.setupRoutes();

    // Initialize router
    Router.init();

    console.log("App initialized");
  },

  /**
   * Load theme settings from API
   */
  async loadTheme() {
    try {
      const response = await API.site.getTheme();
      if (response.data) {
        this.applyTheme(response.data);
      }
    } catch (error) {
      console.log("Using default theme");
    }
  },

  /**
   * Apply theme settings
   */
  applyTheme(settings) {
    const root = document.documentElement;

    settings.forEach((setting) => {
      if (setting.setting_type === "color") {
        root.style.setProperty(
          `--color-${setting.setting_key.replace("_color", "")}`,
          setting.setting_value,
        );
      }
    });
  },

  /**
   * Setup routes
   */
  setupRoutes() {
    // Add auth guard
    Router.beforeEach(async (context) => {
      const publicRoutes = [
        "/",
        "/login",
        "/register",
        "/verify-email",
        "/forgot-password",
        "/reset-password",
        "/complete-profile",
        "/admin/login",
      ];
      const adminRoutes = context.path.startsWith("/admin") && context.path !== "/admin/login";

      // Check auth for protected routes
      if (!publicRoutes.includes(context.path)) {
        const token = API.getToken();

        if (!token) {
          return "/login";
        }

        // Check admin access
        if (adminRoutes) {
          const user = API.getUser();
          if (!user || !["admin", "system_admin"].includes(user.role)) {
            Utils.error("Admin access required");
            return "/login";
          }
        }
      }

      // Redirect logged in users from auth pages
      if (["/login", "/register"].includes(context.path)) {
        const token = API.getToken();
        const user = API.getUser();

        if (token && user) {
          if (["admin", "system_admin"].includes(user.role)) {
            return "/admin/dashboard";
          }
          return "/dashboard";
        }
      }

      return true;
    });

    // Update page title after navigation
    Router.afterEach((context) => {
      const titles = {
        "/": "Welcome",
        "/login": "Login",
        "/register": "Register",
        "/verify-email": "Verify Email",
        "/forgot-password": "Forgot Password",
        "/reset-password": "Reset Password",
        "/complete-profile": "Complete Profile",
        "/dashboard": "Dashboard",
        "/profile": "My Profile",
        "/events": "Events",
        "/announcements": "Announcements",
        "/messages": "Messages",
        "/leaderboard": "Leaderboard",
        "/rewards": "Rewards",
        "/admin/dashboard": "Admin Dashboard",
        "/admin/alumni": "Alumni Management",
        "/admin/events": "Event Management",
        "/admin/announcements": "Announcements",
        "/admin/organization": "Organization",
        "/admin/form-builder": "Form Builder",
        "/admin/gamification": "Gamification",
        "/admin/settings": "Settings",
      };

      document.title = `${titles[context.path] || "Page"} - Alumni System`;
    });

    // Register routes
    Router.registerAll({
      // Public Routes
      "/": () => this.loadPage("pages/home.html"),
      "/login": () => this.loadPage("pages/auth/login.html"),
      "/register": () => this.loadPage("pages/auth/register-new.html"),
      "/verify-email": () => this.loadPage("pages/auth/verify-email.html"),
      "/forgot-password": () =>
        this.loadPage("pages/auth/forgot-password.html"),
      "/reset-password": () => this.loadPage("pages/auth/reset-password.html"),
      "/complete-profile": () => this.loadPage("pages/complete-profile.html"),

      // Admin Login (public)
      "/admin/login": () => this.loadPage("pages/admin/login.html"),

      // Alumni Routes
      "/dashboard": () => this.loadPage("pages/alumni/dashboard.html"),
      "/profile": () => this.loadPage("pages/alumni/profile.html"),
      "/events": () => this.loadPage("pages/alumni/events.html"),
      "/events/:id": (ctx) =>
        this.loadPage("pages/alumni/event-detail.html", ctx),
      "/announcements": () => this.loadPage("pages/alumni/announcements.html"),
      "/announcements/:id": (ctx) =>
        this.loadPage("pages/alumni/announcement-detail.html", ctx),
      "/messages": () => this.loadPage("pages/alumni/messages.html"),
      "/messages/:id": (ctx) =>
        this.loadPage("pages/alumni/conversation.html", ctx),
      "/leaderboard": () => this.loadPage("pages/alumni/leaderboard.html"),
      "/rewards": () => this.loadPage("pages/alumni/rewards.html"),

      // Admin Routes
      "/admin/dashboard": () => this.loadPage("pages/admin/dashboard.html"),
      "/admin/alumni": () => this.loadPage("pages/admin/alumni-list.html"),
      "/admin/alumni/:id": (ctx) =>
        this.loadPage("pages/admin/alumni-detail.html", ctx),
      "/admin/events": () => this.loadPage("pages/admin/events.html"),
      "/admin/events/create": () =>
        this.loadPage("pages/admin/event-form.html"),
      "/admin/events/:id": (ctx) =>
        this.loadPage("pages/admin/event-form.html", ctx),
      "/admin/events/:id/attendance": (ctx) =>
        this.loadPage("pages/admin/event-attendance.html", ctx),
      "/admin/announcements": () =>
        this.loadPage("pages/admin/announcements.html"),
      "/admin/announcements/create": () =>
        this.loadPage("pages/admin/announcement-form.html"),
      "/admin/announcements/:id": (ctx) =>
        this.loadPage("pages/admin/announcement-form.html", ctx),
      "/admin/organization": () =>
        this.loadPage("pages/admin/organization.html"),
      "/admin/form-builder": () =>
        this.loadPage("pages/admin/form-builder.html"),
      "/admin/gamification": () =>
        this.loadPage("pages/admin/gamification.html"),
      "/admin/settings": () => this.loadPage("pages/admin/settings/index.html"),
      "/admin/settings/theme": () =>
        this.loadPage("pages/admin/settings/theme.html"),
      "/admin/settings/site-content": () =>
        this.loadPage("pages/admin/settings/site-content.html"),
      "/admin/settings/email-templates": () =>
        this.loadPage("pages/admin/settings/email-templates.html"),
      "/admin/users": () => this.loadPage("pages/admin/users.html"),

      // 404
      "*": () => this.render404(),
    });
  },

  /**
   * Load page content
   */
  async loadPage(pagePath, context = {}) {
    const appContainer = Utils.$("#app");

    // Show loading
    const hideLoading = Utils.showLoading(appContainer, "Loading...");

    try {
      const response = await fetch(pagePath);

      if (!response.ok) {
        throw new Error("Page not found");
      }

      const html = await response.text();
      appContainer.innerHTML = html;

      // Store context for page scripts
      window.__pageContext = context;

      // Execute page scripts
      const scripts = appContainer.querySelectorAll("script");
      scripts.forEach((script) => {
        const newScript = document.createElement("script");
        if (script.src) {
          newScript.src = script.src;
        } else {
          newScript.textContent = script.textContent;
        }
        script.parentNode.replaceChild(newScript, script);
      });

      // Scroll to top
      window.scrollTo(0, 0);
    } catch (error) {
      console.error("Failed to load page:", error);
      this.render404();
    } finally {
      hideLoading();
    }
  },

  /**
   * Render 404 page
   */
  render404() {
    Utils.render(
      "#app",
      `
            <div class="min-h-screen flex items-center justify-center">
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">404</h1>
                    <p class="text-gray-600 mb-6">Page not found</p>
                    <a href="#/" class="btn btn-primary">Go Home</a>
                </div>
            </div>
        `,
    );
  },

  /**
   * Get current page context
   */
  getContext() {
    return window.__pageContext || {};
  },
};
