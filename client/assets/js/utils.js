/**
 * Alumni Management System - Utility Functions
 */

const Utils = {
  // =====================================================
  // DOM Utilities
  // =====================================================

  /**
   * Get element by selector
   */
  $(selector, context = document) {
    return context.querySelector(selector);
  },

  /**
   * Get all elements by selector
   */
  $$(selector, context = document) {
    return [...context.querySelectorAll(selector)];
  },

  /**
   * Create element with attributes and content
   */
  createElement(tag, attrs = {}, children = []) {
    const el = document.createElement(tag);

    Object.entries(attrs).forEach(([key, value]) => {
      if (key === "className") {
        el.className = value;
      } else if (key === "dataset") {
        Object.entries(value).forEach(([k, v]) => (el.dataset[k] = v));
      } else if (key.startsWith("on")) {
        el.addEventListener(key.slice(2).toLowerCase(), value);
      } else {
        el.setAttribute(key, value);
      }
    });

    children.forEach((child) => {
      if (typeof child === "string") {
        el.appendChild(document.createTextNode(child));
      } else if (child instanceof Node) {
        el.appendChild(child);
      }
    });

    return el;
  },

  /**
   * Render HTML string safely
   */
  html(strings, ...values) {
    return strings.reduce((result, str, i) => {
      const value = values[i] !== undefined ? this.escapeHtml(values[i]) : "";
      return result + str + value;
    }, "");
  },

  /**
   * Escape HTML characters
   */
  escapeHtml(str) {
    if (typeof str !== "string") return str;
    const div = document.createElement("div");
    div.textContent = str;
    return div.innerHTML;
  },

  /**
   * Set innerHTML with event delegation
   */
  render(container, html) {
    if (typeof container === "string") {
      container = this.$(container);
    }
    container.innerHTML = html;
  },

  // =====================================================
  // Toast Notifications
  // =====================================================

  /**
   * Show toast notification
   */
  toast(message, type = "info", duration = 5000) {
    let container = this.$(".toast-container");

    if (!container) {
      container = this.createElement("div", { className: "toast-container" });
      document.body.appendChild(container);
    }

    const icons = {
      success:
        '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>',
      error:
        '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
      warning:
        '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
      info: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>',
    };

    const toastType = Object.prototype.hasOwnProperty.call(icons, type)
      ? type
      : "info";

    const toast = this.createElement(
      "div",
      { className: `toast toast-${toastType}` },
      [],
    );
    toast.innerHTML = `
            <span class="toast-icon" aria-hidden="true">${icons[toastType]}</span>
            <span class="toast-message">${this.escapeHtml(message)}</span>
            <button type="button" class="toast-close" aria-label="Dismiss notification">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        `;

    container.appendChild(toast);

    const close = () => {
      toast.classList.add("toast-out");
      setTimeout(() => toast.remove(), 300);
    };

    toast.querySelector(".toast-close").addEventListener("click", close);

    if (duration > 0) {
      setTimeout(close, duration);
    }

    return toast;
  },

  success(message) {
    return this.toast(message, "success");
  },
  error(message) {
    return this.toast(message, "error");
  },
  warning(message) {
    return this.toast(message, "warning");
  },
  info(message) {
    return this.toast(message, "info");
  },

  // =====================================================
  // Modal
  // =====================================================

  /**
   * Show modal
   */
  modal(options = {}) {
    const {
      title = "",
      content = "",
      size = "",
      closable = true,
      onClose = null,
      buttons = [],
    } = options;

    const overlay = this.createElement("div", { className: "modal-overlay" });

    const sizeClass = size ? `modal-${size}` : "";

    overlay.innerHTML = `
            <div class="modal ${sizeClass}">
                ${
                  title
                    ? `
                    <div class="modal-header">
                        <h3 class="modal-title">${this.escapeHtml(title)}</h3>
                        ${
                          closable
                            ? `
                            <button class="modal-close">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </button>
                        `
                            : ""
                        }
                    </div>
                `
                    : ""
                }
                <div class="modal-body">${content}</div>
                ${
                  buttons.length
                    ? `
                    <div class="modal-footer">
                        ${buttons
                          .map(
                            (btn) => `
                            <button class="btn ${btn.class || "btn-secondary"}" data-action="${btn.action || "close"}">
                                ${btn.text}
                            </button>
                        `,
                          )
                          .join("")}
                    </div>
                `
                    : ""
                }
            </div>
        `;

    document.body.appendChild(overlay);
    document.body.style.overflow = "hidden";

    // Trigger animation
    requestAnimationFrame(() => overlay.classList.add("active"));

    const close = () => {
      overlay.classList.remove("active");
      document.body.style.overflow = "";
      setTimeout(() => {
        overlay.remove();
        if (onClose) onClose();
      }, 200);
    };

    // Event handlers
    if (closable) {
      overlay.querySelector(".modal-close")?.addEventListener("click", close);
      overlay.addEventListener("click", (e) => {
        if (e.target === overlay) close();
      });
    }

    // Button handlers
    overlay.querySelectorAll("[data-action]").forEach((btn) => {
      btn.addEventListener("click", () => {
        const action = btn.dataset.action;
        if (action === "close") {
          close();
        } else {
          const handler = buttons.find((b) => b.action === action)?.handler;
          if (handler) handler(close);
        }
      });
    });

    return { close, element: overlay };
  },

  /**
   * Open a template modal by selector (for page-level static modals).
   */
  openModal(selector) {
    const modal = typeof selector === "string" ? this.$(selector) : selector;
    if (!modal) return null;

    modal.classList.add("active");
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("overflow-hidden");

    if (!modal.dataset.modalInitialized) {
      const dismiss = (event) => {
        const trigger = event.target.closest("[data-dismiss='modal']");
        if (!trigger) return;
        event.preventDefault();
        this.closeModal(modal);
      };

      const clickOutside = (event) => {
        if (
          event.target.classList.contains("modal-backdrop") ||
          event.target === modal
        ) {
          this.closeModal(modal);
        }
      };

      modal.addEventListener("click", dismiss);
      modal.addEventListener("click", clickOutside);
      modal.dataset.modalInitialized = "true";
    }

    return modal;
  },

  /**
   * Close a template modal by selector.
   */
  closeModal(selector) {
    const modal = typeof selector === "string" ? this.$(selector) : selector;
    if (!modal) return;

    modal.classList.remove("active");
    modal.setAttribute("aria-hidden", "true");

    if (!document.querySelector(".modal[id].active")) {
      document.body.classList.remove("overflow-hidden");
    }
  },

  /**
   * Get initials from a name string.
   */
  getInitials(name = "") {
    const clean = String(name || "").trim();
    if (!clean) return "?";

    return clean
      .split(/\s+/)
      .map((part) => part[0])
      .join("")
      .toUpperCase()
      .slice(0, 2);
  },

  /**
   * Confirm dialog
   */
  confirm(message, options = {}) {
    return new Promise((resolve) => {
      const {
        title = "Confirm",
        confirmText = "Confirm",
        cancelText = "Cancel",
        confirmClass = "btn-primary",
        danger = false,
      } = options;

      this.modal({
        title,
        content: `<p>${this.escapeHtml(message)}</p>`,
        buttons: [
          {
            text: cancelText,
            action: "cancel",
            handler: (close) => {
              close();
              resolve(false);
            },
          },
          {
            text: confirmText,
            action: "confirm",
            class: danger ? "btn-danger" : confirmClass,
            handler: (close) => {
              close();
              resolve(true);
            },
          },
        ],
        onClose: () => resolve(false),
      });
    });
  },

  // =====================================================
  // Loading States
  // =====================================================

  /**
   * Show loading overlay
   */
  showLoading(container = "body", message = "Loading...") {
    const target =
      typeof container === "string" ? this.$(container) : container;
    if (!target) {
      return () => {};
    }

    const isBodyTarget = target === document.body;

    const existing = target.querySelector(".loading-overlay");
    if (existing) existing.remove();

    const overlay = this.createElement("div", {
      className: isBodyTarget
        ? "loading-overlay loading-overlay-fixed"
        : "loading-overlay",
    });
    overlay.innerHTML = `
            <div class="loading-panel" role="status" aria-live="polite">
                <div class="spinner spinner-lg"></div>
                <span class="loading-message">${this.escapeHtml(message)}</span>
            </div>
        `;

    const needsPositionContext =
      !isBodyTarget && window.getComputedStyle(target).position === "static";
    if (needsPositionContext) {
      target.dataset.loadingOverlayPositionAdjusted = "true";
      target.style.position = "relative";
    }

    target.appendChild(overlay);

    return () => {
      overlay.remove();

      if (target.dataset.loadingOverlayPositionAdjusted === "true") {
        target.style.position = "";
        delete target.dataset.loadingOverlayPositionAdjusted;
      }
    };
  },

  /**
   * Set button loading state
   */
  setButtonLoading(button, loading = true) {
    if (typeof button === "string") {
      button = this.$(button);
    }

    if (loading) {
      button.disabled = true;
      button.dataset.originalText = button.innerHTML;
      button.innerHTML = '<span class="spinner"></span> Loading...';
    } else {
      button.disabled = false;
      button.innerHTML = button.dataset.originalText || button.innerHTML;
    }
  },

  // =====================================================
  // Form Utilities
  // =====================================================

  /**
   * Serialize form data to object
   */
  serializeForm(form) {
    if (typeof form === "string") {
      form = this.$(form);
    }

    const formData = new FormData(form);
    const data = {};

    for (const [key, value] of formData.entries()) {
      if (key.endsWith("[]")) {
        const arrayKey = key.slice(0, -2);
        if (!data[arrayKey]) data[arrayKey] = [];
        data[arrayKey].push(value);
      } else {
        data[key] = value;
      }
    }

    return data;
  },

  /**
   * Populate form with data
   */
  populateForm(form, data) {
    if (typeof form === "string") {
      form = this.$(form);
    }

    Object.entries(data).forEach(([key, value]) => {
      const field = form.querySelector(`[name="${key}"]`);
      if (!field) return;

      if (field.type === "checkbox") {
        field.checked = !!value;
      } else if (field.type === "radio") {
        const radio = form.querySelector(`[name="${key}"][value="${value}"]`);
        if (radio) radio.checked = true;
      } else {
        field.value = value || "";
      }
    });
  },

  /**
   * Show form errors
   */
  showFormErrors(form, errors) {
    if (typeof form === "string") {
      form = this.$(form);
    }

    // Clear existing errors
    form.querySelectorAll(".form-error").forEach((el) => el.remove());
    form
      .querySelectorAll(".error")
      .forEach((el) => el.classList.remove("error"));

    // Show new errors
    Object.entries(errors).forEach(([field, message]) => {
      const input = form.querySelector(`[name="${field}"]`);
      if (input) {
        input.classList.add("error");
        const errorEl = this.createElement("div", { className: "form-error" }, [
          message,
        ]);
        input.parentNode.appendChild(errorEl);
      }
    });
  },

  /**
   * Clear form errors
   */
  clearFormErrors(form) {
    if (typeof form === "string") {
      form = this.$(form);
    }

    form.querySelectorAll(".form-error").forEach((el) => el.remove());
    form
      .querySelectorAll(".error")
      .forEach((el) => el.classList.remove("error"));
  },

  // =====================================================
  // Formatting Utilities
  // =====================================================

  /**
   * Format date
   */
  formatDate(date, format = "long") {
    const d = new Date(date);

    if (format === "long") {
      return d.toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
      });
    } else if (format === "short") {
      return d.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
      });
    } else if (format === "relative") {
      return this.timeAgo(d);
    }

    return d.toLocaleDateString();
  },

  /**
   * Format datetime
   */
  formatDateTime(date, includeSeconds = false) {
    const d = new Date(date);
    if (Number.isNaN(d.getTime())) return "-";

    return d.toLocaleString("en-US", {
      year: "numeric",
      month: "short",
      day: "numeric",
      hour: "numeric",
      minute: "2-digit",
      ...(includeSeconds ? { second: "2-digit" } : {}),
    });
  },

  /**
   * Time ago
   */
  timeAgo(date) {
    const seconds = Math.floor((new Date() - new Date(date)) / 1000);

    const intervals = [
      { label: "year", seconds: 31536000 },
      { label: "month", seconds: 2592000 },
      { label: "week", seconds: 604800 },
      { label: "day", seconds: 86400 },
      { label: "hour", seconds: 3600 },
      { label: "minute", seconds: 60 },
      { label: "second", seconds: 1 },
    ];

    for (const interval of intervals) {
      const count = Math.floor(seconds / interval.seconds);
      if (count >= 1) {
        return `${count} ${interval.label}${count > 1 ? "s" : ""} ago`;
      }
    }

    return "just now";
  },

  /**
   * Format number
   */
  formatNumber(num) {
    return new Intl.NumberFormat().format(num);
  },

  /**
   * Truncate text
   */
  truncate(text, length = 100) {
    if (text.length <= length) return text;
    return text.slice(0, length) + "...";
  },

  // =====================================================
  // Misc Utilities
  // =====================================================

  /**
   * Debounce function
   */
  debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  },

  /**
   * Throttle function
   */
  throttle(func, limit = 300) {
    let inThrottle;
    return function executedFunction(...args) {
      if (!inThrottle) {
        func(...args);
        inThrottle = true;
        setTimeout(() => (inThrottle = false), limit);
      }
    };
  },

  /**
   * Copy to clipboard
   */
  async copyToClipboard(text) {
    try {
      await navigator.clipboard.writeText(text);
      this.success("Copied to clipboard");
      return true;
    } catch (err) {
      this.error("Failed to copy");
      return false;
    }
  },

  /**
   * Generate random string
   */
  randomString(length = 8) {
    const chars =
      "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
    return Array.from(
      { length },
      () => chars[Math.floor(Math.random() * chars.length)],
    ).join("");
  },
};

// Export for module usage
if (typeof module !== "undefined" && module.exports) {
  module.exports = Utils;
}
