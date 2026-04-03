/**
 * Alumni Management System - Router
 * Hash-based client-side routing
 */

const Router = {
  routes: {},
  currentRoute: null,
  beforeHooks: [],
  afterHooks: [],

  /**
   * Register a route
   */
  register(path, handler, options = {}) {
    this.routes[path] = { handler, options };
    return this;
  },

  /**
   * Register multiple routes
   */
  registerAll(routes) {
    Object.entries(routes).forEach(([path, config]) => {
      if (typeof config === "function") {
        this.register(path, config);
      } else {
        this.register(path, config.handler, config.options || {});
      }
    });
    return this;
  },

  /**
   * Add before navigation hook
   */
  beforeEach(hook) {
    this.beforeHooks.push(hook);
    return this;
  },

  /**
   * Add after navigation hook
   */
  afterEach(hook) {
    this.afterHooks.push(hook);
    return this;
  },

  /**
   * Navigate to a path
   */
  navigate(path) {
    window.location.hash = path.startsWith("#") ? path : `#${path}`;
  },

  /**
   * Replace current route (no history entry)
   */
  replace(path) {
    const hash = path.startsWith("#") ? path : `#${path}`;
    window.location.replace(hash);
  },

  /**
   * Go back in history
   */
  back() {
    window.history.back();
  },

  /**
   * Parse the current hash
   */
  parseHash() {
    const hash = window.location.hash.slice(1) || "/";
    const [pathWithQuery] = hash.split("?");
    const queryString = hash.includes("?") ? hash.split("?")[1] : "";

    return {
      path: pathWithQuery || "/",
      query: Object.fromEntries(new URLSearchParams(queryString)),
    };
  },

  /**
   * Match a path against registered routes
   */
  matchRoute(path) {
    // Exact match
    if (this.routes[path]) {
      return { route: this.routes[path], params: {} };
    }

    // Pattern matching
    for (const [pattern, route] of Object.entries(this.routes)) {
      const regex = this.pathToRegex(pattern);
      const match = path.match(regex);

      if (match) {
        const paramNames = (pattern.match(/:([^/]+)/g) || []).map((p) =>
          p.slice(1),
        );
        const params = {};

        paramNames.forEach((name, index) => {
          params[name] = match[index + 1];
        });

        return { route, params };
      }
    }

    return null;
  },

  /**
   * Convert path pattern to regex
   */
  pathToRegex(pattern) {
    const escaped = pattern.replace(/[.*+?^${}()|[\]\\]/g, "\\$&");
    const withParams = escaped.replace(/\\:([^/]+)/g, "([^/]+)");
    return new RegExp(`^${withParams}$`);
  },

  /**
   * Handle route change
   */
  async handleRouteChange() {
    const { path, query } = this.parseHash();
    const matched = this.matchRoute(path);

    if (!matched) {
      // 404
      if (this.routes["*"]) {
        matched = { route: this.routes["*"], params: {} };
      } else {
        console.error("Route not found:", path);
        return;
      }
    }

    const { route, params } = matched;
    const context = { path, params, query, route: route.options };

    // Run before hooks
    for (const hook of this.beforeHooks) {
      const result = await hook(context);
      if (result === false) {
        return; // Navigation cancelled
      }
      if (typeof result === "string") {
        this.navigate(result); // Redirect
        return;
      }
    }

    // Store current route
    this.currentRoute = context;

    // Execute handler
    try {
      await route.handler(context);
    } catch (error) {
      console.error("Route handler error:", error);
    }

    // Run after hooks
    for (const hook of this.afterHooks) {
      await hook(context);
    }
  },

  /**
   * Initialize router
   */
  init() {
    // Handle hash changes
    window.addEventListener("hashchange", () => this.handleRouteChange());

    // Handle initial load
    if (!window.location.hash) {
      window.location.hash = "#/";
    } else {
      this.handleRouteChange();
    }

    // Handle link clicks
    document.addEventListener("click", (e) => {
      const link = e.target.closest('a[href^="#"]');
      if (link) {
        e.preventDefault();
        this.navigate(link.getAttribute("href"));
      }
    });

    return this;
  },

  /**
   * Get current route info
   */
  getCurrent() {
    return this.currentRoute;
  },
};

/**
 * Auth Guard - Check if user is logged in
 */
function authGuard(context) {
  const token = API.getToken();
  const publicRoutes = [
    "/login",
    "/register",
    "/verify-email",
    "/forgot-password",
    "/reset-password",
    "/",
  ];

  if (!token && !publicRoutes.includes(context.path)) {
    return "/login";
  }

  return true;
}

/**
 * Admin Guard - Check if user is admin
 */
function adminGuard(context) {
  const user = API.getUser();

  if (!user || !["admin", "system_admin"].includes(user.role)) {
    return "/login";
  }

  return true;
}

/**
 * Guest Guard - Redirect logged in users
 */
function guestGuard(context) {
  const token = API.getToken();
  const user = API.getUser();

  if (token && user) {
    if (["admin", "system_admin"].includes(user.role)) {
      return "/admin/dashboard";
    }
    return "/dashboard";
  }

  return true;
}

// Export for module usage
if (typeof module !== "undefined" && module.exports) {
  module.exports = { Router, authGuard, adminGuard, guestGuard };
}
