/**
 * Alumni Management System - Auth State Management
 */

const Auth = {
  user: null,
  token: null,
  isLoading: false,
  hasVerifiedSession: false,
  verifiedToken: null,
  verificationPromise: null,
  verifyingToken: null,
  listeners: [],

  /**
   * Initialize auth state
   */
  init() {
    this.token = API.getToken();
    this.user = API.getUser();
    this.hasVerifiedSession = false;
    this.verifiedToken = null;

    // Verify token if exists
    if (this.token) {
      this.verifyToken();
    }

    return this;
  },

  /**
   * Subscribe to auth state changes
   */
  subscribe(callback) {
    this.listeners.push(callback);
    // Return unsubscribe function
    return () => {
      this.listeners = this.listeners.filter((l) => l !== callback);
    };
  },

  /**
   * Notify listeners of state change
   */
  notify() {
    this.listeners.forEach((callback) =>
      callback({
        user: this.user,
        token: this.token,
        isLoading: this.isLoading,
        isAuthenticated: this.isAuthenticated(),
        isAdmin: this.isAdmin(),
      }),
    );
  },

  /**
   * Set loading state
   */
  setLoading(loading) {
    this.isLoading = loading;
    this.notify();
  },

  /**
   * Verify current token
   */
  async verifyToken() {
    const storedToken = API.getToken();
    this.token = storedToken || this.token;
    this.user = API.getUser() || this.user;

    if (!this.token) {
      this.hasVerifiedSession = false;
      this.verifiedToken = null;
      return false;
    }

    if (
      this.hasVerifiedSession &&
      this.verifiedToken === this.token &&
      this.user
    ) {
      return true;
    }

    if (this.verificationPromise && this.verifyingToken === this.token) {
      return this.verificationPromise;
    }

    const tokenToVerify = this.token;
    this.verifyingToken = tokenToVerify;
    this.verificationPromise = (async () => {
      try {
        this.setLoading(true);
        const response = await API.auth.getProfile({
          skipAuthRedirect: true,
          skipAuthRefresh: true,
        });

        const currentToken = API.getToken() || this.token;
        if (currentToken !== tokenToVerify) {
          return this.isAuthenticated();
        }

        const responseUser = response?.data?.user || {};
        this.token = tokenToVerify;
        this.user = { ...(this.user || {}), ...responseUser };
        this.hasVerifiedSession = true;
        this.verifiedToken = this.token;
        API.setUser(this.user);
        this.notify();
        return true;
      } catch (error) {
        console.error("Token verification failed:", error);
        const currentToken = API.getToken() || this.token;
        if (currentToken !== tokenToVerify) {
          return this.isAuthenticated();
        }

        this.token = null;
        this.user = null;
        this.hasVerifiedSession = false;
        this.verifiedToken = null;
        API.removeToken();
        this.notify();
        return false;
      } finally {
        this.setLoading(false);
        this.verificationPromise = null;
        this.verifyingToken = null;
      }
    })();

    return this.verificationPromise;
  },

  /**
   * Refresh user profile data without tearing down the session.
   */
  async hydrateUserProfile(options = {}) {
    const requireImage = options.requireImage === true;

    if (!this.token) {
      return false;
    }

    if (requireImage && this.user && this.user.profile_image) {
      return true;
    }

    try {
      const response = await API.auth.getProfile({
        skipAuthRedirect: true,
        skipAuthRefresh: true,
      });
      const responseUser = response?.data?.user;

      if (responseUser) {
        this.user = { ...(this.user || {}), ...responseUser };
        API.setUser(this.user);
        return true;
      }
    } catch (error) {
      console.warn("Profile refresh failed:", error);
    }

    return false;
  },

  /**
   * Register new user
   */
  async register(data) {
    try {
      this.setLoading(true);
      const response = await API.auth.register(data);
      return response;
    } finally {
      this.setLoading(false);
    }
  },

  /**
   * Login with email/password
   */
  async login(email, password) {
    try {
      this.setLoading(true);
      const response = await API.auth.login(email, password);

      this.token = response.data.token;
      this.user = response.data.user;

      // Restore cached profile image if available
      try {
        const cached = localStorage.getItem("alumni_profile_cache");
        if (cached) {
          const cachedData = JSON.parse(cached);
          // Only use cached image if it's for the same user and not too old (30 days)
          const cacheAge = new Date() - new Date(cachedData.cached_at);
          const thirtyDays = 30 * 24 * 60 * 60 * 1000;

          if (cachedData.email === this.user.email && cacheAge < thirtyDays) {
            console.log(
              "Restoring cached profile image:",
              cachedData.profile_image,
            );
            // Use cached image only as fallback when server response is missing
            if (!this.user.profile_image && cachedData.profile_image) {
              this.user.profile_image = cachedData.profile_image;
              console.log("Profile image restored from cache (fallback)");
            }
            // Fallback for name and alumni_id (already correct)
            this.user.name = cachedData.name || this.user.name;
            this.user.alumni_id = cachedData.alumni_id || this.user.alumni_id;
          } else {
            console.log("Cache expired or different user");
          }
        } else {
          console.log("No cached profile found");
        }
      } catch (e) {
        console.error("Failed to restore cached profile:", e);
      }

      // Save token and user (with restored image) to localStorage
      API.setToken(this.token);
      API.setUser(this.user);

      // Ensure profile image stays available after re-login.
      await this.hydrateUserProfile({ requireImage: true });
      this.hasVerifiedSession = true;
      this.verifiedToken = this.token;

      // Initialize session manager
      if (typeof SessionManager !== "undefined") {
        SessionManager.init();
      }

      this.notify();
      return response;
    } finally {
      this.setLoading(false);
    }
  },

  /**
   * Login with Google
   */
  async googleLogin(idToken) {
    try {
      this.setLoading(true);
      const response = await API.auth.googleLogin(idToken);

      this.token = response.data.token;
      this.user = response.data.user;

      // Restore cached profile image if available
      try {
        const cached = localStorage.getItem("alumni_profile_cache");
        if (cached) {
          const cachedData = JSON.parse(cached);
          // Only use cached image if it's for the same user and not too old (30 days)
          const cacheAge = new Date() - new Date(cachedData.cached_at);
          const thirtyDays = 30 * 24 * 60 * 60 * 1000;

          if (cachedData.email === this.user.email && cacheAge < thirtyDays) {
            console.log(
              "Restoring cached profile image (Google):",
              cachedData.profile_image,
            );
            // Use cached image only as fallback when server response is missing
            if (!this.user.profile_image && cachedData.profile_image) {
              this.user.profile_image = cachedData.profile_image;
              console.log(
                "Profile image restored from cache (Google fallback)",
              );
            }
            // Fallback for name and alumni_id (already correct)
            this.user.name = cachedData.name || this.user.name;
            this.user.alumni_id = cachedData.alumni_id || this.user.alumni_id;
          } else {
            console.log("Cache expired or different user (Google)");
          }
        } else {
          console.log("No cached profile found (Google)");
        }
      } catch (e) {
        console.error("Failed to restore cached profile (Google):", e);
      }

      // Save token and user (with restored image) to localStorage
      API.setToken(this.token);
      API.setUser(this.user);

      // Ensure profile image stays available after re-login.
      await this.hydrateUserProfile({ requireImage: true });
      this.hasVerifiedSession = true;
      this.verifiedToken = this.token;

      // Initialize session manager
      if (typeof SessionManager !== "undefined") {
        SessionManager.init();
      }

      this.notify();
      return response;
    } finally {
      this.setLoading(false);
    }
  },

  /**
   * Verify email
   */
  async verifyEmail(email, code) {
    try {
      this.setLoading(true);
      const response = await API.auth.verifyEmail(email, code);

      if (response.data.token) {
        this.token = response.data.token;
        this.user = response.data.user;

        API.setToken(this.token);
        API.setUser(this.user);
        this.hasVerifiedSession = true;
        this.verifiedToken = this.token;

        // Initialize session manager
        if (typeof SessionManager !== "undefined") {
          SessionManager.init();
        }

        this.notify();
      }

      return response;
    } finally {
      this.setLoading(false);
    }
  },

  /**
   * Resend verification code
   */
  async resendVerification(email) {
    return API.auth.resendVerification(email);
  },

  /**
   * Request password reset
   */
  async forgotPassword(email) {
    return API.auth.forgotPassword(email);
  },

  /**
   * Reset password
   */
  async resetPassword(email, code, password, passwordConfirmation) {
    return API.auth.resetPassword(email, code, password, passwordConfirmation);
  },

  /**
   * Update profile
   */
  async updateProfile(data) {
    try {
      this.setLoading(true);
      const response = await API.auth.updateProfile(data);

      // Refresh user data
      await this.verifyToken();

      return response;
    } finally {
      this.setLoading(false);
    }
  },

  /**
   * Change password
   */
  async changePassword(currentPassword, newPassword, confirmPassword) {
    return API.auth.changePassword(
      currentPassword,
      newPassword,
      confirmPassword,
    );
  },

  /**
   * Logout
   */
  logout(redirectPath = "/") {
    // Preserve profile data before clearing
    const user = this.user || API.getUser();
    if (user) {
      const profileCache = {
        profile_image: user.profile_image,
        name: user.name,
        alumni_id: user.alumni_id,
        email: user.email,
        cached_at: new Date().toISOString(),
      };

      console.log("Caching profile data on logout:", profileCache);

      try {
        localStorage.setItem(
          "alumni_profile_cache",
          JSON.stringify(profileCache),
        );
        console.log("Profile cache saved successfully");
      } catch (e) {
        console.error("Failed to cache profile data:", e);
      }
    } else {
      console.log("No user data to cache");
    }

    // Destroy session manager
    if (
      typeof SessionManager !== "undefined" &&
      SessionManager.state.isActive
    ) {
      SessionManager.destroy();
    }

    this.token = null;
    this.user = null;
    this.hasVerifiedSession = false;
    this.verifiedToken = null;
    this.verificationPromise = null;
    this.verifyingToken = null;
    API.removeToken();
    this.notify();
    const targetPath = String(redirectPath || "").trim() || "/";
    Router.navigate(targetPath);
  },

  /**
   * Check if user is authenticated
   */
  isAuthenticated() {
    return (
      !!this.token &&
      !!this.user &&
      this.hasVerifiedSession &&
      this.verifiedToken === this.token
    );
  },

  /**
   * Check if user is admin
   */
  isAdmin() {
    return (
      this.isAuthenticated() &&
      ["admin", "system_admin", "campus_admin", "staff"].includes(
        this.user.role,
      )
    );
  },

  /**
   * Check if user is system admin
   */
  isSystemAdmin() {
    return this.user && this.user.role === "system_admin";
  },

  /**
   * Get user's display name
   */
  getDisplayName() {
    if (!this.user) return "Guest";
    return this.user.name || this.user.email.split("@")[0];
  },

  /**
   * Get user's initials
   */
  getInitials() {
    const name = this.getDisplayName();
    return name
      .split(" ")
      .map((n) => n[0])
      .join("")
      .toUpperCase()
      .slice(0, 2);
  },
};

// Export for module usage
if (typeof module !== "undefined" && module.exports) {
  module.exports = Auth;
}
