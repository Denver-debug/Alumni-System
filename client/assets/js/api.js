/**
 * Alumni Management System - API Client
 * Handles all HTTP requests to the backend
 */

const API = {
  // Resolve backend URL at runtime: same-origin by default, explicit override optional.
  baseUrl: (() => {
    let storageBase = null;
    try {
      if (typeof localStorage !== "undefined") {
        storageBase = localStorage.getItem("alumni_api_base_url");
      }
    } catch {
      storageBase = null;
    }

    const explicitBase =
      (typeof window !== "undefined" &&
        (window.ALUMNI_API_BASE_URL || window.__ALUMNI_API_BASE_URL)) ||
      storageBase;

    if (typeof explicitBase === "string" && explicitBase.trim()) {
      return explicitBase.replace(/\/+$/, "");
    }

    if (
      typeof window !== "undefined" &&
      window.location &&
      /^https?:$/.test(window.location.protocol)
    ) {
      const { hostname, port, origin, pathname } = window.location;
      const isLocalHost = hostname === "localhost" || hostname === "127.0.0.1";

      if (isLocalHost) {
        // If running on port 5500 (Live Server) or similar, use port 8000 for API
        if (port === "5500" || port === "5501" || port === "3000") {
          return "http://localhost:8000";
        }

        // If running on port 8000 (PHP built-in server), use same origin
        if (port === "8000") {
          return `${origin}`;
        }

        // Laragon setup: detect if running from /alumni-system/client/
        if (pathname.includes("/alumni-system/")) {
          return "http://localhost/alumni-system/server";
        }

        // Default: assume API is on port 8000
        return "http://localhost:8000";
      }

      // Same-origin API for production
      return `${origin}/api/v1`;
    }

    // Default fallback
    return "http://localhost:8000";
  })(),

  /**
   * Resolve backend-hosted uploads when the frontend is served from another origin.
   */
  getAssetUrlCandidates(assetUrl) {
    const raw = String(assetUrl || "").trim();

    if (!raw) {
      return [];
    }

    if (/^[a-z][a-z0-9+.-]*:/i.test(raw) || raw.startsWith("//")) {
      return [raw];
    }

    const normalizedPath = raw.replace(/^\/+/, "");
    const candidates = [];
    const addCandidate = (value) => {
      const normalized = String(value || "").trim();
      if (normalized && !candidates.includes(normalized)) {
        candidates.push(normalized);
      }
    };
    const apiBase = this.baseUrl
      .replace(/\/api\/v1\/?$/, "")
      .replace(/\/+$/, "");
    const origin =
      typeof window !== "undefined" && window.location?.origin
        ? window.location.origin
        : "";

    if (normalizedPath.startsWith("server/uploads/")) {
      addCandidate(origin ? `${origin}/${normalizedPath}` : "");
      addCandidate(
        apiBase ? `${apiBase}/${normalizedPath.replace(/^server\//, "")}` : "",
      );
      addCandidate(raw);
      return candidates;
    }

    if (normalizedPath.startsWith("uploads/")) {
      addCandidate(apiBase ? `${apiBase}/${normalizedPath}` : "");
      addCandidate(origin ? `${origin}/${normalizedPath}` : "");
      addCandidate(origin ? `${origin}/server/${normalizedPath}` : "");
      addCandidate(raw);
      return candidates;
    }

    if (
      typeof window !== "undefined" &&
      window.document?.baseURI &&
      typeof URL !== "undefined"
    ) {
      try {
        addCandidate(new URL(raw, window.document.baseURI).href);
      } catch {
        // Fall through to raw path.
      }
    }

    addCandidate(raw);
    return candidates;
  },

  resolveAssetUrl(assetUrl) {
    return this.getAssetUrlCandidates(assetUrl)[0] || "";
  },

  /**
   * Get stored auth token
   */
  getToken() {
    try {
      return localStorage.getItem("alumni_token");
    } catch {
      return null;
    }
  },

  /**
   * Set auth token
   */
  setToken(token) {
    try {
      localStorage.setItem("alumni_token", token);
    } catch {
      // Ignore storage write failures and rely on in-memory request state.
    }
  },

  /**
   * Remove auth token
   */
  removeToken() {
    try {
      localStorage.removeItem("alumni_token");
      localStorage.removeItem("alumni_user");
    } catch {
      // Ignore storage cleanup failures.
    }
  },

  _refreshPromise: null,

  async refreshAuthToken(options = {}) {
    if (this._refreshPromise) {
      return this._refreshPromise;
    }

    this._refreshPromise = this.request("/auth/refresh", {
      method: "POST",
      skipAuthRefresh: true,
      skipAuthRedirect: options.skipAuthRedirect === true,
    })
      .then((response) => {
        const token = response?.data?.token;
        const user = response?.data?.user;

        if (!token) {
          throw {
            status: 401,
            code: "refresh_missing_token",
            message: "Failed to refresh session token.",
            errors: {},
          };
        }

        this.setToken(token);
        if (user) {
          this.setUser(user);
        }

        return token;
      })
      .finally(() => {
        this._refreshPromise = null;
      });

    return this._refreshPromise;
  },

  /**
   * Get stored user data
   */
  getUser() {
    try {
      const userData = localStorage.getItem("alumni_user");
      return userData ? JSON.parse(userData) : null;
    } catch {
      try {
        localStorage.removeItem("alumni_user");
      } catch {
        // Ignore cleanup failure.
      }
      return null;
    }
  },

  /**
   * Set user data
   */
  setUser(user) {
    try {
      localStorage.setItem("alumni_user", JSON.stringify(user));
    } catch {
      // Ignore storage write failures and proceed.
    }
  },

  /**
   * Make HTTP request
   */
  async request(endpoint, options = {}) {
    const requestOptions = { ...options };
    const skipAuthRefresh = requestOptions.skipAuthRefresh === true;
    const hasRetriedAuth = requestOptions.hasRetriedAuth === true;
    const skipAuthRedirect = requestOptions.skipAuthRedirect === true;
    delete requestOptions.skipAuthRefresh;
    delete requestOptions.hasRetriedAuth;
    delete requestOptions.skipAuthRedirect;

    const url = `${this.baseUrl}${endpoint}`;
    const token = this.getToken();

    const headers = {
      "Content-Type": "application/json",
      ...requestOptions.headers,
    };

    if (token) {
      headers["Authorization"] = `Bearer ${token}`;
    }

    // Add CSRF token for state-changing requests
    if (
      typeof SecurityUtils !== "undefined" &&
      ["POST", "PUT", "PATCH", "DELETE"].includes(requestOptions.method)
    ) {
      headers["X-CSRF-Token"] = SecurityUtils.getCSRFToken();
    }

    // Track activity for session management
    if (
      typeof SessionManager !== "undefined" &&
      SessionManager.state.isActive
    ) {
      SessionManager.trackActivity();
    }

    const config = {
      ...requestOptions,
      headers,
    };

    if (
      requestOptions.body &&
      typeof requestOptions.body === "object" &&
      !(
        typeof FormData !== "undefined" &&
        requestOptions.body instanceof FormData
      )
    ) {
      config.body = JSON.stringify(requestOptions.body);
    }

    // Handle FormData (file uploads)
    if (
      typeof FormData !== "undefined" &&
      requestOptions.body instanceof FormData
    ) {
      delete headers["Content-Type"]; // Let browser set it
    }

    try {
      const response = await fetch(url, config);
      const contentType = response.headers.get("content-type") || "";
      let data = null;
      let rawText = "";

      if (contentType.includes("application/json")) {
        try {
          data = await response.json();
        } catch (parseError) {
          throw {
            status: response.status,
            code: "invalid_json_response",
            message: "API returned malformed JSON.",
            errors: {},
          };
        }
      } else {
        rawText = await response.text();

        // Some servers miss content-type headers but still return JSON.
        try {
          data = rawText ? JSON.parse(rawText) : null;
        } catch {
          data = null;
        }

        if (!data || typeof data !== "object") {
          const fallbackMessage = response.ok
            ? "Success"
            : `Request failed with status ${response.status}`;

          const trimmedPreview = rawText.trim().slice(0, 250);

          data = {
            success: response.ok,
            message: fallbackMessage,
            errors: {},
            data: response.ok ? rawText : null,
            response_preview: trimmedPreview,
          };
        }
      }

      if (!data || typeof data !== "object") {
        data = {
          success: response.ok,
          message: response.ok
            ? "Success"
            : `Request failed with status ${response.status}`,
          errors: {},
        };
      }

      // Handle unauthorized
      if (response.status === 401) {
        if (!skipAuthRefresh && !hasRetriedAuth) {
          await this.refreshAuthToken({ skipAuthRedirect });
          return this.request(endpoint, {
            ...requestOptions,
            hasRetriedAuth: true,
            skipAuthRedirect,
          });
        }

        this.removeToken();
        if (!skipAuthRedirect) {
          window.location.hash = "#/login";
        }
        throw {
          status: 401,
          code: "unauthorized",
          message: data.message || "Session expired. Please log in again.",
          errors: data.errors || {},
        };
      }

      if (!response.ok) {
        throw {
          status: response.status,
          code: `http_${response.status}`,
          message: data.message || "Request failed",
          errors: data.errors || {},
          response_preview: data.response_preview || "",
        };
      }

      return data;
    } catch (error) {
      if (error.status) {
        throw error;
      }

      if (error instanceof SyntaxError) {
        throw {
          status: 0,
          code: "invalid_response_format",
          message: "Received an invalid response format from the server.",
          errors: {},
        };
      }

      if (error instanceof TypeError) {
        throw {
          status: 0,
          code: "network_or_cors",
          message:
            "Unable to reach the API. Check server URL, CORS policy, and network connectivity.",
          errors: {},
        };
      }

      throw {
        status: 0,
        code: "unexpected_client_error",
        message: error.message || "Unexpected client error while calling API.",
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

    getProfile(options = {}) {
      return API.request("/auth/profile", {
        method: "GET",
        ...options,
      });
    },

    updateProfile(data) {
      if (data instanceof FormData) {
        return API.upload("/auth/profile", data, "POST");
      }
      return API.put("/auth/profile", data);
    },

    changePassword(currentPassword, newPassword, newPasswordConfirmation) {
      if (
        typeof currentPassword === "object" &&
        currentPassword !== null &&
        !Array.isArray(currentPassword)
      ) {
        const payload = currentPassword;
        return API.post("/auth/change-password", {
          current_password:
            payload.current_password || payload.currentPassword || "",
          new_password: payload.new_password || payload.newPassword || "",
          new_password_confirmation:
            payload.new_password_confirmation ||
            payload.newPasswordConfirmation ||
            payload.confirm_password ||
            "",
        });
      }

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
      if (data instanceof FormData) {
        return API.upload("/alumni/profile", data, "POST");
      }
      return API.put("/alumni/profile", data);
    },

    getIdCard() {
      return API.get("/alumni/id-card");
    },

    getPoints(params = {}) {
      return API.get("/alumni/points", params);
    },

    search(query, params = {}) {
      // Support both search("keyword", { ... }) and search({ query: "keyword", ... }).
      if (typeof query === "object" && query !== null) {
        return API.get("/alumni/search", query);
      }

      const queryParams = { ...params, query };
      if (queryParams.q && !queryParams.query) {
        queryParams.query = queryParams.q;
      }
      delete queryParams.q;

      return API.get("/alumni/search", queryParams);
    },
  },

  // =====================================================
  // EVENTS API
  // =====================================================

  events: {
    getAll(params = {}) {
      return API.get("/events", params);
    },

    list(params = {}) {
      return this.getAll(params).then((response) => {
        if (Array.isArray(response?.data?.my_registrations)) {
          response.data.my_registrations = response.data.my_registrations.map(
            (id) => Number(id),
          );
        }
        return response;
      });
    },

    getById(id) {
      return API.get(`/events/${id}`);
    },

    get(id) {
      return this.getById(id);
    },

    async create(data) {
      if (typeof FormData !== "undefined" && data instanceof FormData) {
        return API.upload("/admin/events", data, "POST");
      }

      return API.post("/admin/events", data);
    },

    async update(id, data) {
      if (typeof FormData !== "undefined" && data instanceof FormData) {
        return API.upload(`/admin/events/${id}`, data, "POST");
      }

      return API.put(`/admin/events/${id}`, data);
    },

    delete(id) {
      return API.delete(`/admin/events/${id}`);
    },

    register(eventId) {
      return API.post(`/events/${eventId}/register`, { event_id: eventId });
    },

    cancelRegistration(eventId) {
      return API.post(`/events/${eventId}/cancel-registration`, {
        event_id: eventId,
      });
    },

    async getMyStats() {
      return API.get("/events/my-stats");
    },

    async getAttendance(eventId) {
      const eventResponse = await API.admin.getEvent(eventId);
      const eventData = eventResponse.data || {};
      const codesResponse = await API.admin
        .getEventCodes(eventId)
        .catch(() => ({ data: [] }));

      const codes = Array.isArray(codesResponse.data)
        ? codesResponse.data
        : Array.isArray(codesResponse.data?.codes)
          ? codesResponse.data.codes
          : [];

      return {
        success: true,
        data: {
          event: eventData.event || eventData,
          attendees: eventData.attendees || [],
          stats: eventData.stats || {},
          attendance_code:
            codes[0]?.code || eventData.attendance_codes?.[0]?.code || null,
          attendance_codes: codes,
        },
      };
    },

    generateAttendanceCode(eventId, data = {}) {
      return API.admin.createEventCode(eventId, data);
    },

    markAttended(eventId, userId) {
      return API.admin.markAttendance(eventId, userId, "attended");
    },

    rsvp(eventId, status) {
      if (!status || status === "going") {
        return this.register(eventId);
      }

      if (status === "not_going") {
        return this.cancelRegistration(eventId);
      }

      return API.post(`/events/${eventId}/rsvp`, { status });
    },

    checkin(eventId, code) {
      const attendanceCode =
        typeof code === "object" && code !== null
          ? code.code || code.attendance_code || ""
          : code;

      return API.post(`/events/${eventId}/checkin`, {
        event_id: eventId,
        code: attendanceCode,
      });
    },

    checkIn(eventId, code) {
      return this.checkin(eventId, code);
    },
  },

  // =====================================================
  // ANNOUNCEMENTS API
  // =====================================================

  announcements: {
    getAll(params = {}) {
      return API.get("/announcements", params);
    },

    list(params = {}) {
      return this.getAll(params);
    },

    getById(id) {
      return API.get(`/announcements/${id}`);
    },

    get(id) {
      return this.getById(id);
    },

    create(data) {
      if (typeof FormData !== "undefined" && data instanceof FormData) {
        return API.upload("/admin/announcements", data, "POST");
      }
      return API.post("/admin/announcements", data);
    },

    update(id, data) {
      if (typeof FormData !== "undefined" && data instanceof FormData) {
        return API.upload(`/admin/announcements/${id}`, data, "POST");
      }
      return API.put(`/admin/announcements/${id}`, data);
    },

    delete(id) {
      return API.delete(`/admin/announcements/${id}`);
    },
  },

  // =====================================================
  // MESSAGING API
  // =====================================================

  messaging: {
    getConversations(params = {}) {
      return API.get("/messaging/conversations", params);
    },

    async createConversation(data) {
      const response = await API.post("/messaging/conversations", data);

      if (response?.data?.conversation_id && !response.data.id) {
        response.data.id = response.data.conversation_id;
      }

      return response;
    },

    getMessages(conversationId, params = {}) {
      return API.get(
        `/messaging/conversations/${conversationId}/messages`,
        params,
      );
    },

    sendMessage(conversationId, content, type = "text") {
      if (typeof content === "object" && content !== null) {
        return API.post(`/messaging/conversations/${conversationId}/messages`, {
          content: content.content || "",
          message_type: content.message_type || type,
        });
      }

      return API.post(`/messaging/conversations/${conversationId}/messages`, {
        content,
        message_type: type,
      });
    },

    sendAttachment(conversationId, file, options = {}) {
      const formData = new FormData();
      formData.append("attachment", file, file.name || "attachment");
      formData.append("content", options.content || file.name || "Attachment");
      formData.append(
        "message_type",
        options.messageType || options.message_type || "file",
      );
      return API.upload(
        `/messaging/conversations/${conversationId}/messages`,
        formData,
        "POST",
      );
    },

    markAsRead(conversationId) {
      return API.put(`/messaging/conversations/${conversationId}/read`);
    },

    async joinOrgGroup(type, batchYear = null) {
      const response = await API.post("/messaging/group", {
        type,
        batch_year: batchYear,
      });

      if (response?.data?.conversation_id && !response.data.id) {
        response.data.id = response.data.conversation_id;
      }

      return response;
    },

    // New messaging endpoints
    getConversation(conversationId) {
      return API.get(`/messaging/conversations/${conversationId}`);
    },

    searchAlumni(params = {}) {
      return API.get("/messaging/alumni/search", params);
    },

    startCall(conversationId, type = "audio") {
      return API.post("/messaging/calls", {
        conversation_id: conversationId,
        call_type: type,
      });
    },

    getIncomingCalls() {
      return API.get("/messaging/calls/incoming");
    },

    getCall(callId) {
      return API.get(`/messaging/calls/${callId}`);
    },

    getCallSignals(callId, after = 0) {
      return API.get(`/messaging/calls/${callId}/signals`, { after });
    },

    sendCallSignal(callId, signalType, payload) {
      return API.post(`/messaging/calls/${callId}/signals`, {
        signal_type: signalType,
        payload,
      });
    },

    respondCall(callId, action) {
      return API.put(`/messaging/calls/${callId}/respond`, { action });
    },

    endCall(callId) {
      return API.put(`/messaging/calls/${callId}/end`);
    },
  },

  // =====================================================
  // VERIFICATION API
  // =====================================================

  verification: {
    // Admin endpoints
    getPending() {
      return API.get("/admin/alumni/pending");
    },

    getStats() {
      return API.get("/admin/alumni/verification-stats");
    },

    verify(alumniId, notes = null) {
      return API.put(`/admin/alumni/${alumniId}/verify`, { notes });
    },

    reject(alumniId, reason) {
      return API.put(`/admin/alumni/${alumniId}/reject`, { reason });
    },

    // Alumni endpoints
    getStatus() {
      return API.get("/alumni/verification-status");
    },

    getNotifications() {
      return API.get("/alumni/notifications");
    },

    markNotificationRead(notificationId) {
      return API.put(`/alumni/notifications/${notificationId}/read`);
    },
  },

  // =====================================================
  // ANALYTICS API
  // =====================================================

  analytics: {
    getDashboard() {
      return API.get("/admin/analytics/dashboard");
    },

    getDistribution() {
      return API.get("/admin/analytics/alumni-distribution");
    },

    getEngagement() {
      return API.get("/admin/analytics/engagement");
    },

    export(type = "alumni") {
      const url = `${API.baseUrl}/admin/analytics/export?type=${type}`;
      window.open(url, "_blank");
    },
  },

  // =====================================================
  // SECURITY SETTINGS API
  // =====================================================

  security: {
    getSettings() {
      return API.get("/admin/security/settings");
    },

    updateSettings(settings) {
      return API.put("/admin/security/settings", settings);
    },

    getLockedAccounts() {
      return API.get("/admin/security/locked-accounts");
    },

    unlockAccount(userId) {
      return API.put(`/admin/security/unlock/${userId}`);
    },

    getLoginAttempts(params = {}) {
      return API.get("/admin/security/login-attempts", params);
    },

    getStats() {
      return API.get("/admin/security/stats");
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

    getMyPoints() {
      return this.getPoints();
    },

    getHistory(params = {}) {
      return API.get("/gamification/history", params);
    },

    getPointsHistory(params = {}) {
      return this.getHistory(params);
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

  formBuilder: {
    getFields(params = {}) {
      return API.get("/form-fields", params);
    },

    createField(data) {
      return API.post("/admin/form-fields", data);
    },

    updateField(id, data) {
      return API.put(`/admin/form-fields/${id}`, { ...data, id });
    },

    deleteField(id) {
      return API.delete(`/admin/form-fields/${id}`);
    },

    async reorder(order = []) {
      if (!Array.isArray(order) || order.length === 0) {
        return Promise.resolve({ success: true, data: [] });
      }

      const updates = order.map((item) =>
        API.put(`/admin/form-fields/${item.id}`, {
          id: item.id,
          display_order: item.display_order,
        }),
      );

      await Promise.all(updates);
      return { success: true };
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

    getFirebaseConfig() {
      return API.get("/site/firebase-config");
    },
  },

  // =====================================================
  // ADMIN API
  // =====================================================

  admin: {
    login(email, password) {
      return API.post("/admin/login", { email, password });
    },

    getProfile() {
      return API.auth.getProfile();
    },

    getDashboard() {
      return API.get("/admin/dashboard");
    },

    getDashboardStats() {
      return this.getDashboard();
    },

    getActivities(params = {}) {
      return API.get("/admin/activities", params);
    },

    getActivityLogs(params = {}) {
      return this.getActivities(params);
    },

    // Alumni Management
    getAlumni(params = {}) {
      return API.get("/admin/alumni", params);
    },

    getAlumniById(id) {
      return API.get(`/admin/alumni/${id}`);
    },

    getAlumniDetail(id) {
      return this.getAlumniById(id);
    },

    updateAlumni(id, data) {
      return API.put(`/admin/alumni/${id}`, data);
    },

    updateAlumniStatus(id, status) {
      return this.updateAlumni(id, { status });
    },

    getBatchYears(range = 40) {
      const currentYear = new Date().getFullYear();
      const years = Array.from({ length: range }, (_, i) => currentYear - i);
      return Promise.resolve({ success: true, data: years });
    },

    deleteAlumni(id, permanent = false) {
      return API.delete(
        `/admin/alumni/${id}${permanent ? "?permanent=true" : ""}`,
      );
    },

    getAlumniIdCard(alumniId) {
      return API.get(`/admin/alumni/id-card?alumni_id=${alumniId}`);
    },

    async exportAlumni(params = {}) {
      const queryString = new URLSearchParams(params).toString();
      const url = `${API.baseUrl}/admin/alumni/export${queryString ? `?${queryString}` : ""}`;

      const fetchExport = async () => {
        const headers = {};
        const token = API.getToken();
        if (token) {
          headers.Authorization = `Bearer ${token}`;
        }

        const response = await fetch(url, { headers });

        if (response.status === 401) {
          const error = new Error("Unauthorized");
          error.status = 401;
          throw error;
        }

        if (!response.ok) {
          const error = new Error(`Export failed (${response.status})`);
          error.status = response.status;
          throw error;
        }

        return response.blob();
      };

      try {
        return await fetchExport();
      } catch (error) {
        if (error && error.status === 401) {
          await API.refreshAuthToken({ skipAuthRedirect: true });
          return fetchExport();
        }
        throw error;
      }
    },

    // Event Management
    getEvents(params = {}) {
      return API.get("/admin/events", params);
    },

    createEvent(data) {
      if (typeof FormData !== "undefined" && data instanceof FormData) {
        return API.upload("/admin/events", data, "POST");
      }

      return API.post("/admin/events", data);
    },

    getEvent(id) {
      return API.get(`/admin/events/${id}`);
    },

    updateEvent(id, data) {
      if (typeof FormData !== "undefined" && data instanceof FormData) {
        return API.upload(`/admin/events/${id}`, data, "POST");
      }

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

    markAttendance(eventId, userId, status = "attended") {
      return API.post(`/admin/events/${eventId}/attendance`, {
        user_id: userId,
        status,
      });
    },

    // Announcement Management
    getAnnouncements(params = {}) {
      return API.get("/admin/announcements", params);
    },

    createAnnouncement(data) {
      if (typeof FormData !== "undefined" && data instanceof FormData) {
        return API.upload("/admin/announcements", data, "POST");
      }
      return API.post("/admin/announcements", data);
    },

    getAnnouncement(id) {
      return API.get(`/admin/announcements/${id}`);
    },

    updateAnnouncement(id, data) {
      if (typeof FormData !== "undefined" && data instanceof FormData) {
        return API.upload(`/admin/announcements/${id}`, data, "POST");
      }
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

    // Campus Management
    getCampuses(params = {}) {
      return API.get("/admin/campuses", params);
    },

    getCampus(id) {
      return API.get(`/admin/campuses/${id}`);
    },

    createCampus(data) {
      return API.post("/admin/campuses", data);
    },

    updateCampus(id, data) {
      return API.put(`/admin/campuses/${id}`, data);
    },

    deleteCampus(id) {
      return API.delete(`/admin/campuses/${id}`);
    },

    // Settings
    getThemeSettings() {
      return API.get("/admin/settings/theme");
    },

    updateThemeSettings(data) {
      return API.put("/admin/settings/theme", data);
    },

    uploadThemeLogo(formData) {
      return API.upload("/admin/settings/theme/logo-upload", formData, "POST");
    },

    uploadThemeBackground(formData) {
      return API.upload(
        "/admin/settings/theme/background-upload",
        formData,
        "POST",
      );
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

    settings: {
      getTheme() {
        return API.admin.getThemeSettings();
      },

      updateTheme(data) {
        return API.admin.updateThemeSettings(data);
      },

      uploadLogo(formData) {
        return API.admin.uploadThemeLogo(formData);
      },

      uploadBackground(formData) {
        return API.admin.uploadThemeBackground(formData);
      },

      getSiteContent() {
        return API.admin.getSiteContent();
      },

      updateSiteContent(data) {
        return API.admin.updateSiteContent(data);
      },

      getEmail() {
        return API.admin.getEmailSettings();
      },

      updateEmail(data) {
        return API.admin.updateEmailSettings(data);
      },

      async getEmailTemplates() {
        const response = await API.admin.getEmailSettings();
        return response?.data?.templates || [];
      },

      updateEmailTemplate(templateKey, data) {
        return API.put("/admin/settings/email", {
          template: {
            key: templateKey,
            subject: data.subject,
            body: data.body,
          },
        });
      },
    },
  },
};

// Export for module usage
if (typeof module !== "undefined" && module.exports) {
  module.exports = API;
}
