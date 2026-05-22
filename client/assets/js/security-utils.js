/**
 * Alumni Management System - Security Utilities
 * XSS protection, CSRF tokens, password strength, rate limiting
 */

const SecurityUtils = {
  // CSRF token storage
  csrfToken: null,
  csrfTokenExpiry: null,

  // Rate limiting storage
  rateLimits: new Map(),

  /**
   * Initialize security utilities
   */
  init() {
    this.generateCSRFToken();
    console.log('[SecurityUtils] Initialized');
  },

  // =====================================================
  // XSS PROTECTION
  // =====================================================

  /**
   * Sanitize HTML to prevent XSS
   */
  sanitizeHTML(html) {
    if (!html) return '';

    const div = document.createElement('div');
    div.textContent = html;
    return div.innerHTML;
  },

  /**
   * Escape HTML entities
   */
  escapeHTML(text) {
    if (!text) return '';

    const map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#x27;',
      '/': '&#x2F;',
    };

    return String(text).replace(/[&<>"'/]/g, (char) => map[char]);
  },

  /**
   * Strip HTML tags
   */
  stripHTML(html) {
    if (!html) return '';

    const div = document.createElement('div');
    div.innerHTML = html;
    return div.textContent || div.innerText || '';
  },

  /**
   * Validate and sanitize URL
   */
  sanitizeURL(url) {
    if (!url) return '';

    try {
      const parsed = new URL(url);
      
      // Only allow http and https protocols
      if (!['http:', 'https:'].includes(parsed.protocol)) {
        return '';
      }

      return parsed.href;
    } catch (error) {
      return '';
    }
  },

  // =====================================================
  // CSRF PROTECTION
  // =====================================================

  /**
   * Generate CSRF token
   */
  generateCSRFToken() {
    this.csrfToken = this.generateSecureRandom(32);
    this.csrfTokenExpiry = Date.now() + (60 * 60 * 1000); // 1 hour
    
    // Store in session storage
    try {
      sessionStorage.setItem('csrf_token', this.csrfToken);
      sessionStorage.setItem('csrf_token_expiry', this.csrfTokenExpiry.toString());
    } catch (error) {
      console.error('[SecurityUtils] Error storing CSRF token:', error);
    }

    return this.csrfToken;
  },

  /**
   * Get CSRF token
   */
  getCSRFToken() {
    // Check if token expired
    if (this.csrfTokenExpiry && Date.now() > this.csrfTokenExpiry) {
      return this.generateCSRFToken();
    }

    // Try to get from memory
    if (this.csrfToken) {
      return this.csrfToken;
    }

    // Try to get from session storage
    try {
      const token = sessionStorage.getItem('csrf_token');
      const expiry = sessionStorage.getItem('csrf_token_expiry');

      if (token && expiry && Date.now() < parseInt(expiry)) {
        this.csrfToken = token;
        this.csrfTokenExpiry = parseInt(expiry);
        return token;
      }
    } catch (error) {
      console.error('[SecurityUtils] Error getting CSRF token:', error);
    }

    // Generate new token
    return this.generateCSRFToken();
  },

  /**
   * Validate CSRF token
   */
  validateCSRFToken(token) {
    if (!token) return false;

    const validToken = this.getCSRFToken();
    return token === validToken;
  },

  /**
   * Add CSRF token to form
   */
  addCSRFTokenToForm(form) {
    if (typeof form === 'string') {
      form = document.querySelector(form);
    }

    if (!form) return;

    // Remove existing CSRF input
    const existing = form.querySelector('input[name="csrf_token"]');
    if (existing) {
      existing.remove();
    }

    // Add new CSRF input
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'csrf_token';
    input.value = this.getCSRFToken();
    form.appendChild(input);
  },

  // =====================================================
  // PASSWORD SECURITY
  // =====================================================

  /**
   * Check password strength
   */
  checkPasswordStrength(password) {
    if (!password) {
      return { score: 0, feedback: ['Password is required'] };
    }

    let score = 0;
    const feedback = [];

    // Length check
    if (password.length >= 8) {
      score++;
    } else {
      feedback.push('Password should be at least 8 characters');
    }

    if (password.length >= 12) {
      score++;
    }

    // Uppercase check
    if (/[A-Z]/.test(password)) {
      score++;
    } else {
      feedback.push('Add uppercase letters');
    }

    // Lowercase check
    if (/[a-z]/.test(password)) {
      score++;
    } else {
      feedback.push('Add lowercase letters');
    }

    // Number check
    if (/\d/.test(password)) {
      score++;
    } else {
      feedback.push('Add numbers');
    }

    // Special character check
    if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
      score++;
    } else {
      feedback.push('Add special characters');
    }

    // Common password check
    const commonPasswords = ['password', '12345678', 'qwerty', 'abc123', 'password123'];
    if (commonPasswords.includes(password.toLowerCase())) {
      score = 0;
      feedback.push('This is a commonly used password');
    }

    // Normalize score to 0-4
    const normalizedScore = Math.min(Math.floor(score / 1.5), 4);

    return {
      score: normalizedScore,
      feedback: feedback.length > 0 ? feedback : ['Strong password'],
      strength: ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'][normalizedScore],
    };
  },

  /**
   * Get password strength color
   */
  getPasswordStrengthColor(score) {
    const colors = ['#ef4444', '#f59e0b', '#eab308', '#84cc16', '#10b981'];
    return colors[score] || colors[0];
  },

  /**
   * Validate password requirements
   */
  validatePassword(password) {
    const errors = [];

    if (!password) {
      errors.push('Password is required');
      return errors;
    }

    if (password.length < 8) {
      errors.push('Password must be at least 8 characters');
    }

    if (!/[A-Z]/.test(password)) {
      errors.push('Password must contain at least one uppercase letter');
    }

    if (!/[a-z]/.test(password)) {
      errors.push('Password must contain at least one lowercase letter');
    }

    if (!/\d/.test(password)) {
      errors.push('Password must contain at least one number');
    }

    if (!/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) {
      errors.push('Password must contain at least one special character');
    }

    return errors;
  },

  // =====================================================
  // RATE LIMITING
  // =====================================================

  /**
   * Rate limit a function
   */
  rateLimit(key, maxRequests, timeWindow) {
    const now = Date.now();
    
    // Get or create rate limit entry
    if (!this.rateLimits.has(key)) {
      this.rateLimits.set(key, {
        requests: [],
        blocked: false,
        blockedUntil: null,
      });
    }

    const limit = this.rateLimits.get(key);

    // Check if blocked
    if (limit.blocked && now < limit.blockedUntil) {
      const remainingTime = Math.ceil((limit.blockedUntil - now) / 1000);
      throw new Error(`Rate limit exceeded. Please wait ${remainingTime} seconds.`);
    }

    // Clear old requests
    limit.requests = limit.requests.filter(time => now - time < timeWindow);

    // Check if limit exceeded
    if (limit.requests.length >= maxRequests) {
      limit.blocked = true;
      limit.blockedUntil = now + timeWindow;
      
      const remainingTime = Math.ceil(timeWindow / 1000);
      throw new Error(`Rate limit exceeded. Please wait ${remainingTime} seconds.`);
    }

    // Add current request
    limit.requests.push(now);
    limit.blocked = false;
    limit.blockedUntil = null;

    return true;
  },

  /**
   * Clear rate limit for a key
   */
  clearRateLimit(key) {
    this.rateLimits.delete(key);
  },

  /**
   * Clear all rate limits
   */
  clearAllRateLimits() {
    this.rateLimits.clear();
  },

  // =====================================================
  // SECURE RANDOM
  // =====================================================

  /**
   * Generate cryptographically secure random string
   */
  generateSecureRandom(length = 32) {
    const array = new Uint8Array(length);
    
    if (window.crypto && window.crypto.getRandomValues) {
      window.crypto.getRandomValues(array);
    } else {
      // Fallback for older browsers
      for (let i = 0; i < length; i++) {
        array[i] = Math.floor(Math.random() * 256);
      }
    }

    return Array.from(array, byte => byte.toString(16).padStart(2, '0')).join('');
  },

  /**
   * Generate secure random number
   */
  generateSecureRandomNumber(min, max) {
    const range = max - min + 1;
    const array = new Uint32Array(1);
    
    if (window.crypto && window.crypto.getRandomValues) {
      window.crypto.getRandomValues(array);
      return min + (array[0] % range);
    } else {
      return min + Math.floor(Math.random() * range);
    }
  },

  // =====================================================
  // INPUT VALIDATION
  // =====================================================

  /**
   * Validate email format
   */
  validateEmail(email) {
    if (!email) return false;

    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  },

  /**
   * Validate phone number
   */
  validatePhone(phone) {
    if (!phone) return false;

    const cleaned = phone.replace(/\D/g, '');
    return cleaned.length >= 10;
  },

  /**
   * Validate URL
   */
  validateURL(url) {
    if (!url) return false;

    try {
      new URL(url);
      return true;
    } catch {
      return false;
    }
  },

  /**
   * Validate alphanumeric
   */
  validateAlphanumeric(text) {
    if (!text) return false;

    return /^[a-zA-Z0-9]+$/.test(text);
  },

  // =====================================================
  // CONTENT SECURITY
  // =====================================================

  /**
   * Check for suspicious content
   */
  isSuspiciousContent(content) {
    if (!content) return false;

    const suspiciousPatterns = [
      /<script/i,
      /javascript:/i,
      /on\w+\s*=/i, // Event handlers
      /<iframe/i,
      /<object/i,
      /<embed/i,
      /eval\(/i,
      /expression\(/i,
    ];

    return suspiciousPatterns.some(pattern => pattern.test(content));
  },

  /**
   * Validate file upload
   */
  validateFileUpload(file, options = {}) {
    const {
      maxSize = 5 * 1024 * 1024, // 5MB
      allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'],
    } = options;

    const errors = [];

    if (!file) {
      errors.push('No file selected');
      return errors;
    }

    // Check file size
    if (file.size > maxSize) {
      errors.push(`File size must be less than ${maxSize / 1024 / 1024}MB`);
    }

    // Check file type
    if (!allowedTypes.includes(file.type)) {
      errors.push(`File type ${file.type} is not allowed`);
    }

    return errors;
  },

  // =====================================================
  // UTILITY FUNCTIONS
  // =====================================================

  /**
   * Hash string (simple hash for client-side)
   */
  async hashString(str) {
    if (!str) return '';

    if (window.crypto && window.crypto.subtle) {
      const encoder = new TextEncoder();
      const data = encoder.encode(str);
      const hashBuffer = await window.crypto.subtle.digest('SHA-256', data);
      const hashArray = Array.from(new Uint8Array(hashBuffer));
      return hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
    } else {
      // Simple fallback hash
      let hash = 0;
      for (let i = 0; i < str.length; i++) {
        const char = str.charCodeAt(i);
        hash = ((hash << 5) - hash) + char;
        hash = hash & hash;
      }
      return hash.toString(16);
    }
  },

  /**
   * Compare strings securely (timing-safe)
   */
  secureCompare(a, b) {
    if (!a || !b) return false;
    if (a.length !== b.length) return false;

    let result = 0;
    for (let i = 0; i < a.length; i++) {
      result |= a.charCodeAt(i) ^ b.charCodeAt(i);
    }

    return result === 0;
  },
};

// Auto-initialize
if (typeof window !== 'undefined') {
  window.addEventListener('load', () => {
    SecurityUtils.init();
  });
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = SecurityUtils;
}
