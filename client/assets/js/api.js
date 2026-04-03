/**
 * Alumni Management System - API Client
 * Handles all HTTP requests to the backend
 */

const API = {
  // Backend server URL - change this if running on different ports
  baseUrl: "http://localhost:8000/api/v1",

  /**
   * Get stored auth token
   */
  getToken() {
    return localStorage.getItem("alumni_token");
  },

  /**
   * Set auth token
   */
  setToken(token) {
    localStorage.setItem("alumni_token", token);
  },

  /**
   * Remove auth token
   */
  removeToken() {
    localStorage.removeItem("alumni_token");
    localStorage.removeItem("alumni_user");
  },

  /**
   * Get stored user data
   */
  getUser() {
    const userData = localStorage.getItem("alumni_user");
    return userData ? JSON.parse(userData) : null;
  },

  /**
   * Set user data
   */
  setUser(user) {
    localStorage.setItem("alumni_user", JSON.stringify(user));
  },

  /**
   * Make HTTP request
   */
  async request(endpoint, options = {}) {
    const url = `${this.baseUrl}${endpoint}`;
    const token = this.getToken();

    const headers = {
      "Content-Type": "application/json",
      ...options.headers,
    };

    if (token) {
      headers["Authorization"] = `Bearer ${token}`;
    }

    const config = {
      ...options,
      headers,
    };

    if (
      options.body &&
      typeof options.body === "object" &&
      !(options.body instanceof FormData)
    ) {
      config.body = JSON.stringify(options.body);
    }

    // Handle FormData (file uploads)
    if (options.body instanceof FormData) {
      delete headers["Content-Type"]; // Let browser set it
    }

    try {
      const response = await fetch(url, config);
      const data = await response.json();

      // Handle unauthorized
      if (response.status === 401) {
        this.removeToken();
        window.location.hash = "#/login";
        throw new Error(data.message || "Unauthorized");
      }

      if (!response.ok) {
        throw {
          status: response.status,
          message: data.message || "Request failed",
          errors: data.errors || {},
        };
      }

      return data;
    } catch (error) {
      if (error.status) {
        throw error;
      }
      throw {
        status: 0,
        message: "Network error. Please check your connection.",
        errors: {},
      };
    }
  },

  // HTTP Methods
  get(endpoint, params = {}) {
    const queryString = new URLSearchParams(params).toString();
    const url = queryString ? `${endpoint}?${queryString}` : endpoint;
    return this.request(url, { method: "GET" });
  },

  post(endpoint, body = {}) {
    return this.request(endpoint, { method: "POST", body });
  },

  put(endpoint, body = {}) {
    return this.request(endpoint, { method: "PUT", body });
  },

  patch(endpoint, body = {}) {
    return this.request(endpoint, { method: "PATCH", body });
  },

  delete(endpoint) {
    return this.request(endpoint, { method: "DELETE" });
  },

  /**
   * Upload file with FormData
   */
  upload(endpoint, formData, method = "POST") {
    return this.request(endpoint, {
      method,
      body: formData,
      headers: {}, // Let browser set Content-Type for FormData
    });
  },

  // =====================================================
  // AUTH API
  // =====================================================

  auth: {
    register(data) {
      return API.post("/auth/register", data);
    },

    login(email, password) {
      return API.post("/auth/login", { email, password });
    },

    googleLogin(idToken) {
      return API.post("/auth/google", { idToken });
    },

    verifyEmail(email, code) {
      return API.post("/auth/verify-email", { email, code });
    },

    resendVerification(email) {
      return API.post("/auth/resend-verification", { email });
    },

    forgotPassword(email) {
      return API.post("/auth/forgot-password", { email });
    },

    resetPassword(email, code, password, password_confirmation) {
      return API.post("/auth/reset-password", {
        email,
        code,
        password,
        password_confirmation,
      });
    },

    getProfile() {
      return API.get("/auth/profile");
    },

    updateProfile(data) {
      if (data instanceof FormData) {
        return API.upload("/auth/profile", data, "PUT");
      }
      return API.put("/auth/profile", data);
    },

    changePassword(currentPassword, newPassword, newPasswordConfirmation) {
      return API.post("/auth/change-password", {
        current_password: currentPassword,
        new_password: newPassword,
        new_password_confirmation: newPasswordConfirmation,
      });
    },
  },

  // =====================================================
  // ALUMNI API
  // =====================================================

  alumni: {
    getDashboard() {
      return API.get("/alumni/dashboard");
    },

    getProfile() {
      return API.get("/alumni/profile");
    },

    updateProfile(data) {
      return API.put("/alumni/profile", data);
    },

    getPoints(params = {}) {
      return API.get("/alumni/points", params);
    },

    search(query, params = {}) {
      return API.get("/alumni/search", { q: query, ...params });
    },
  },

  // =====================================================
  // EVENTS API
  // =====================================================

  events: {
    getAll(params = {}) {
      return API.get("/events", params);
    },

    getById(id) {
      return API.get(`/events/${id}`);
    },

    rsvp(eventId, status) {
      return API.post(`/events/${eventId}/rsvp`, { status });
    },

    checkin(eventId, code) {
      return API.post(`/events/${eventId}/checkin`, { code });
    },
  },

  // =====================================================
  // ANNOUNCEMENTS API
  // =====================================================

  announcements: {
    getAll(params = {}) {
      return API.get("/announcements", params);
    },

    getById(id) {
      return API.get(`/announcements/${id}`);
    },
  },

  // =====================================================
  // MESSAGING API
  // =====================================================

  messaging: {
    getConversations(params = {}) {
      return API.get("/messaging/conversations", params);
    },

    createConversation(data) {
      return API.post("/messaging/conversations", data);
    },

    getMessages(conversationId, params = {}) {
      return API.get(`/messaging/messages/${conversationId}`, params);
    },

    sendMessage(conversationId, content, type = "text") {
      return API.post(`/messaging/messages/${conversationId}`, {
        content,
        message_type: type,
      });
    },

    joinOrgGroup(type, batchYear = null) {
      return API.post("/messaging/group", { type, batch_year: batchYear });
    },
  },

  // =====================================================
  // GAMIFICATION API
  // =====================================================

  gamification: {
    getLeaderboard(params = {}) {
      return API.get("/gamification/leaderboard", params);
    },

    getPoints() {
      return API.get("/gamification/points");
    },

    getHistory(params = {}) {
      return API.get("/gamification/history", params);
    },

    getRewards(params = {}) {
      return API.get("/rewards", params);
    },

    redeemReward(rewardId) {
      return API.post(`/rewards/${rewardId}/redeem`);
    },
  },

  // =====================================================
  // ORGANIZATION API
  // =====================================================

  organization: {
    getColleges() {
      return API.get("/colleges");
    },

    getPrograms(collegeId = null) {
      const params = collegeId ? { college_id: collegeId } : {};
      return API.get("/programs", params);
    },

    getSections(programId = null) {
      const params = programId ? { program_id: programId } : {};
      return API.get("/sections", params);
    },
  },

  // =====================================================
  // FORM BUILDER API
  // =====================================================

  formFields: {
    getAll() {
      return API.get("/form-fields");
    },
  },

  // =====================================================
  // SITE API
  // =====================================================

  site: {
    getTheme() {
      return API.get("/site/theme");
    },

    getContent(section = null) {
      const params = section ? { section } : {};
      return API.get("/site/content", params);
    },
  },

  // =====================================================
  // ADMIN API
  // =====================================================

  admin: {
    login(email, password) {
      return API.post("/admin/login", { email, password });
    },

    getDashboard() {
      return API.get("/admin/dashboard");
    },

    getActivities(params = {}) {
      return API.get("/admin/activities", params);
    },

    // Alumni Management
    getAlumni(params = {}) {
      return API.get("/admin/alumni", params);
    },

    getAlumniById(id) {
      return API.get(`/admin/alumni/${id}`);
    },

    updateAlumni(id, data) {
      return API.put(`/admin/alumni/${id}`, data);
    },

    deleteAlumni(id, permanent = false) {
      return API.delete(`/admin/alumni/${id}${permanent ? '?permanent=true' : ''}`);
    },

    exportAlumni(params = {}) {
      const queryString = new URLSearchParams(params).toString();
      window.open(`${API.baseUrl}/admin/alumni/export?${queryString}`, '_blank');
    },

    // Event Management
    getEvents(params = {}) {
      return API.get("/admin/events", params);
    },

    createEvent(data) {
      return API.post("/admin/events", data);
    },

    getEvent(id) {
      return API.get(`/admin/events/${id}`);
    },

    updateEvent(id, data) {
      return API.put(`/admin/events/${id}`, data);
    },

    deleteEvent(id) {
      return API.delete(`/admin/events/${id}`);
    },

    getEventCodes(eventId) {
      return API.get(`/admin/events/${eventId}/codes`);
    },

    createEventCode(eventId, data) {
      return API.post(`/admin/events/${eventId}/codes`, data);
    },

    markAttendance(eventId, userId, status = 'attended') {
      return API.post(`/admin/events/${eventId}/attendance`, { user_id: userId, status });
    },

    // Announcement Management
    getAnnouncements(params = {}) {
      return API.get("/admin/announcements", params);
    },

    createAnnouncement(data) {
      return API.post("/admin/announcements", data);
    },

    getAnnouncement(id) {
      return API.get(`/admin/announcements/${id}`);
    },

    updateAnnouncement(id, data) {
      return API.put(`/admin/announcements/${id}`, data);
    },

    deleteAnnouncement(id) {
      return API.delete(`/admin/announcements/${id}`);
    },

    // Organization Management
    getOrganization(type, params = {}) {
      return API.get(`/admin/organization/${type}`, params);
    },

    createOrganization(type, data) {
      return API.post(`/admin/organization/${type}`, data);
    },

    updateOrganization(type, id, data) {
      return API.put(`/admin/organization/${type}/${id}`, data);
    },

    deleteOrganization(type, id) {
      return API.delete(`/admin/organization/${type}/${id}`);
    },

    // Form Builder
    getFormFields() {
      return API.get("/admin/form-fields");
    },

    createFormField(data) {
      return API.post("/admin/form-fields", data);
    },

    updateFormField(id, data) {
      return API.put(`/admin/form-fields/${id}`, data);
    },

    deleteFormField(id) {
      return API.delete(`/admin/form-fields/${id}`);
    },

    // Gamification
    getGamificationPoints() {
      return API.get("/admin/gamification/points");
    },

    adjustPoints(data) {
      return API.post("/admin/gamification/points/adjust", data);
    },

    getRewards(params = {}) {
      return API.get("/admin/gamification/rewards", params);
    },

    createReward(data) {
      return API.post("/admin/gamification/rewards", data);
    },

    updateReward(id, data) {
      return API.put(`/admin/gamification/rewards/${id}`, data);
    },

    deleteReward(id) {
      return API.delete(`/admin/gamification/rewards/${id}`);
    },

    getRedemptions(params = {}) {
      return API.get("/admin/gamification/redemptions", params);
    },

    updateRedemption(id, data) {
      return API.put(`/admin/gamification/redemptions/${id}`, data);
    },

    // Settings
    getThemeSettings() {
      return API.get("/admin/settings/theme");
    },

    updateThemeSettings(data) {
      return API.put("/admin/settings/theme", data);
    },

    getSiteContent() {
      return API.get("/admin/settings/site");
    },

    updateSiteContent(data) {
      return API.put("/admin/settings/site", data);
    },

    getEmailSettings() {
      return API.get("/admin/settings/email");
    },

    updateEmailSettings(data) {
      return API.put("/admin/settings/email", data);
    },
  },
};

// Export for module usage
if (typeof module !== "undefined" && module.exports) {
  module.exports = API;
}
