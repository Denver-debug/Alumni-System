/**
 * Alumni Management System - Auth State Management
 */

const Auth = {
  user: null,
  token: null,
  isLoading: false,
  listeners: [],

  /**
   * Initialize auth state
   */
  init() {
    this.token = API.getToken();
    this.user = API.getUser();

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
    if (!this.token) return false;

    try {
      this.setLoading(true);
      const response = await API.auth.getProfile();
      this.user = response.data.user;
      API.setUser(this.user);
      this.notify();
      return true;
    } catch (error) {
      console.error("Token verification failed:", error);
      this.logout();
      return false;
    } finally {
      this.setLoading(false);
    }
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

      API.setToken(this.token);
      API.setUser(this.user);

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

      API.setToken(this.token);
      API.setUser(this.user);

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
  logout() {
    this.token = null;
    this.user = null;
    API.removeToken();
    this.notify();
    Router.navigate("/login");
  },

  /**
   * Check if user is authenticated
   */
  isAuthenticated() {
    return !!this.token && !!this.user;
  },

  /**
   * Check if user is admin
   */
  isAdmin() {
    return this.user && ["admin", "system_admin"].includes(this.user.role);
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
