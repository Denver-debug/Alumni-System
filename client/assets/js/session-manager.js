/**
 * Alumni Management System - Session Manager
 * Handles session timeout, activity tracking, and token management
 */

const SessionManager = {
  config: {
    inactivityTimeout: 30 * 60 * 1000, // 30 minutes
    warningTime: 2 * 60 * 1000, // 2 minutes before timeout
    tokenRefreshInterval: 5 * 60 * 1000, // 5 minutes
    maxConcurrentSessions: 3,
    enableActivityTracking: true,
    storageKey: 'alumni_session',
  },

  state: {
    lastActivity: Date.now(),
    warningShown: false,
    refreshTimer: null,
    inactivityTimer: null,
    warningTimer: null,
    isActive: false,
  },

  /**
   * Initialize session manager
   */
  init() {
    if (this.state.isActive) return;

    this.state.isActive = true;
    this.state.lastActivity = Date.now();

    // Setup activity tracking
    if (this.config.enableActivityTracking) {
      this.setupActivityTracking();
    }

    // Setup timers
    this.setupTimers();

    // Check token expiration
    this.checkTokenExpiration();

    // Setup token refresh
    this.setupTokenRefresh();

    // Check for concurrent sessions
    this.checkConcurrentSessions();

    console.log('[SessionManager] Initialized');
  },

  /**
   * Configure session manager
   */
  configure(options = {}) {
    this.config = { ...this.config, ...options };
  },

  /**
   * Setup activity tracking
   */
  setupActivityTracking() {
    const events = ['mousedown', 'keydown', 'scroll', 'touchstart', 'click'];

    events.forEach(event => {
      document.addEventListener(event, () => this.trackActivity(), { passive: true });
    });
  },

  /**
   * Track user activity
   */
  trackActivity() {
    this.state.lastActivity = Date.now();
    this.state.warningShown = false;

    // Reset timers
    this.resetTimers();

    // Save session data
    this.saveSessionData();
  },

  /**
   * Setup inactivity timers
   */
  setupTimers() {
    this.resetTimers();
  },

  /**
   * Reset all timers
   */
  resetTimers() {
    // Clear existing timers
    if (this.state.inactivityTimer) {
      clearTimeout(this.state.inactivityTimer);
    }
    if (this.state.warningTimer) {
      clearTimeout(this.state.warningTimer);
    }

    // Set warning timer
    this.state.warningTimer = setTimeout(() => {
      this.showInactivityWarning();
    }, this.config.inactivityTimeout - this.config.warningTime);

    // Set logout timer
    this.state.inactivityTimer = setTimeout(() => {
      this.handleInactivityTimeout();
    }, this.config.inactivityTimeout);
  },

  /**
   * Show inactivity warning
   */
  showInactivityWarning() {
    if (this.state.warningShown) return;
    this.state.warningShown = true;

    const remainingTime = Math.ceil(this.config.warningTime / 1000);

    if (typeof Utils !== 'undefined' && Utils.confirm) {
      Utils.confirm(
        `Your session will expire in ${remainingTime / 60} minutes due to inactivity. Do you want to continue?`,
        {
          title: 'Session Expiring',
          confirmText: 'Continue Session',
          cancelText: 'Logout',
          onConfirm: () => {
            this.trackActivity();
            Utils.success('Session extended');
          },
          onCancel: () => {
            this.logout('Session ended by user');
          }
        }
      );
    } else {
      const continueSession = confirm(
        `Your session will expire in ${remainingTime / 60} minutes due to inactivity. Click OK to continue.`
      );

      if (continueSession) {
        this.trackActivity();
      } else {
        this.logout('Session ended by user');
      }
    }
  },

  /**
   * Handle inactivity timeout
   */
  handleInactivityTimeout() {
    console.log('[SessionManager] Session expired due to inactivity');
    this.logout('Session expired due to inactivity');
  },

  /**
   * Check token expiration
   */
  checkTokenExpiration() {
    const token = API.getToken();
    if (!token) return false;

    try {
      // Decode JWT token (simple base64 decode)
      const payload = this.decodeToken(token);
      
      if (!payload || !payload.exp) {
        return true; // Assume valid if no expiration
      }

      const expirationTime = payload.exp * 1000; // Convert to milliseconds
      const currentTime = Date.now();

      if (currentTime >= expirationTime) {
        console.log('[SessionManager] Token expired');
        this.logout('Token expired');
        return false;
      }

      return true;
    } catch (error) {
      console.error('[SessionManager] Error checking token expiration:', error);
      return true; // Assume valid on error
    }
  },

  /**
   * Decode JWT token
   */
  decodeToken(token) {
    try {
      const parts = token.split('.');
      if (parts.length !== 3) return null;

      const payload = parts[1];
      const decoded = atob(payload.replace(/-/g, '+').replace(/_/g, '/'));
      return JSON.parse(decoded);
    } catch (error) {
      console.error('[SessionManager] Error decoding token:', error);
      return null;
    }
  },

  /**
   * Setup automatic token refresh
   */
  setupTokenRefresh() {
    if (this.state.refreshTimer) {
      clearInterval(this.state.refreshTimer);
    }

    this.state.refreshTimer = setInterval(() => {
      this.refreshToken();
    }, this.config.tokenRefreshInterval);
  },

  /**
   * Refresh authentication token
   */
  async refreshToken() {
    if (!API.getToken()) return;

    try {
      console.log('[SessionManager] Refreshing token...');
      await API.refreshAuthToken();
      console.log('[SessionManager] Token refreshed successfully');
    } catch (error) {
      console.error('[SessionManager] Token refresh failed:', error);
      
      // If refresh fails with 401, logout
      if (error.status === 401) {
        this.logout('Token refresh failed');
      }
    }
  },

  /**
   * Check for concurrent sessions
   */
  checkConcurrentSessions() {
    const sessionData = this.getSessionData();
    
    if (!sessionData) {
      this.saveSessionData();
      return;
    }

    // Check if session ID changed (indicates new session)
    const currentSessionId = this.getSessionId();
    if (sessionData.sessionId !== currentSessionId) {
      console.warn('[SessionManager] Concurrent session detected');
      
      if (typeof Utils !== 'undefined' && Utils.warning) {
        Utils.warning('You have been logged in from another device or browser.');
      }
    }

    this.saveSessionData();
  },

  /**
   * Get session ID
   */
  getSessionId() {
    let sessionId = sessionStorage.getItem('alumni_session_id');
    
    if (!sessionId) {
      sessionId = this.generateSessionId();
      sessionStorage.setItem('alumni_session_id', sessionId);
    }

    return sessionId;
  },

  /**
   * Generate session ID
   */
  generateSessionId() {
    return `${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
  },

  /**
   * Save session data
   */
  saveSessionData() {
    try {
      const data = {
        lastActivity: this.state.lastActivity,
        sessionId: this.getSessionId(),
        timestamp: Date.now(),
      };

      localStorage.setItem(this.config.storageKey, JSON.stringify(data));
    } catch (error) {
      console.error('[SessionManager] Error saving session data:', error);
    }
  },

  /**
   * Get session data
   */
  getSessionData() {
    try {
      const data = localStorage.getItem(this.config.storageKey);
      return data ? JSON.parse(data) : null;
    } catch (error) {
      console.error('[SessionManager] Error getting session data:', error);
      return null;
    }
  },

  /**
   * Clear session data
   */
  clearSessionData() {
    try {
      localStorage.removeItem(this.config.storageKey);
      sessionStorage.removeItem('alumni_session_id');
    } catch (error) {
      console.error('[SessionManager] Error clearing session data:', error);
    }
  },

  /**
   * Check if session is valid
   */
  isSessionValid() {
    const token = API.getToken();
    if (!token) return false;

    // Check token expiration
    if (!this.checkTokenExpiration()) {
      return false;
    }

    // Check inactivity
    const timeSinceActivity = Date.now() - this.state.lastActivity;
    if (timeSinceActivity >= this.config.inactivityTimeout) {
      return false;
    }

    return true;
  },

  /**
   * Logout user
   */
  logout(reason = 'User logout') {
    console.log(`[SessionManager] Logging out: ${reason}`);

    // Clear timers
    if (this.state.inactivityTimer) {
      clearTimeout(this.state.inactivityTimer);
    }
    if (this.state.warningTimer) {
      clearTimeout(this.state.warningTimer);
    }
    if (this.state.refreshTimer) {
      clearInterval(this.state.refreshTimer);
    }

    // Clear session data
    this.clearSessionData();

    // Reset state
    this.state.isActive = false;

    // Logout via Auth
    if (typeof Auth !== 'undefined' && Auth.logout) {
      Auth.logout('/login');
    } else {
      window.location.hash = '#/login';
    }
  },

  /**
   * Destroy session manager
   */
  destroy() {
    this.logout('Session manager destroyed');
  },

  /**
   * Get session info
   */
  getSessionInfo() {
    return {
      isValid: this.isSessionValid(),
      lastActivity: new Date(this.state.lastActivity),
      timeSinceActivity: Date.now() - this.state.lastActivity,
      timeUntilTimeout: this.config.inactivityTimeout - (Date.now() - this.state.lastActivity),
      sessionId: this.getSessionId(),
    };
  },
};

// Auto-initialize when token exists
if (typeof window !== 'undefined') {
  window.addEventListener('load', () => {
    if (API.getToken()) {
      SessionManager.init();
    }
  });
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = SessionManager;
}
