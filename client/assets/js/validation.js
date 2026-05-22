/**
 * Alumni Management System - Form Validation
 * Client-side validation utilities
 */

const Validation = {
  /**
   * Validation rules
   */
  rules: {
    required: (value) => {
      return value !== null && value !== undefined && String(value).trim() !== '';
    },

    email: (value) => {
      if (!value) return true; // Allow empty if not required
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(String(value).trim());
    },

    phone: (value) => {
      if (!value) return true;
      const phoneRegex = /^[\d\s\-\+\(\)]+$/;
      return phoneRegex.test(String(value).trim()) && value.replace(/\D/g, '').length >= 10;
    },

    url: (value) => {
      if (!value) return true;
      try {
        new URL(value);
        return true;
      } catch {
        return false;
      }
    },

    minLength: (value, min) => {
      if (!value) return true;
      return String(value).length >= min;
    },

    maxLength: (value, max) => {
      if (!value) return true;
      return String(value).length <= max;
    },

    min: (value, min) => {
      if (!value) return true;
      return Number(value) >= min;
    },

    max: (value, max) => {
      if (!value) return true;
      return Number(value) <= max;
    },

    pattern: (value, pattern) => {
      if (!value) return true;
      const regex = new RegExp(pattern);
      return regex.test(String(value));
    },

    match: (value, matchValue) => {
      return value === matchValue;
    },

    alphanumeric: (value) => {
      if (!value) return true;
      return /^[a-zA-Z0-9]+$/.test(String(value));
    },

    alpha: (value) => {
      if (!value) return true;
      return /^[a-zA-Z\s]+$/.test(String(value));
    },

    numeric: (value) => {
      if (!value) return true;
      return /^\d+$/.test(String(value));
    },

    date: (value) => {
      if (!value) return true;
      const date = new Date(value);
      return !isNaN(date.getTime());
    },

    futureDate: (value) => {
      if (!value) return true;
      const date = new Date(value);
      return date > new Date();
    },

    pastDate: (value) => {
      if (!value) return true;
      const date = new Date(value);
      return date < new Date();
    },
  },

  /**
   * Error messages
   */
  messages: {
    required: 'This field is required',
    email: 'Please enter a valid email address',
    phone: 'Please enter a valid phone number',
    url: 'Please enter a valid URL',
    minLength: 'Must be at least {min} characters',
    maxLength: 'Must be no more than {max} characters',
    min: 'Must be at least {min}',
    max: 'Must be no more than {max}',
    pattern: 'Invalid format',
    match: 'Fields do not match',
    alphanumeric: 'Only letters and numbers allowed',
    alpha: 'Only letters allowed',
    numeric: 'Only numbers allowed',
    date: 'Please enter a valid date',
    futureDate: 'Date must be in the future',
    pastDate: 'Date must be in the past',
  },

  /**
   * Validate a single field
   */
  validateField(value, rules, fieldName = 'Field') {
    const errors = [];

    // Handle required rule first
    if (rules.required && !this.rules.required(value)) {
      return [this.messages.required];
    }

    // Skip other validations if field is empty and not required
    if (!rules.required && !value) {
      return [];
    }

    // Validate each rule
    Object.entries(rules).forEach(([rule, ruleValue]) => {
      if (rule === 'required') return; // Already handled

      if (this.rules[rule]) {
        const isValid = typeof ruleValue === 'boolean' && ruleValue
          ? this.rules[rule](value)
          : this.rules[rule](value, ruleValue);

        if (!isValid) {
          let message = this.messages[rule] || 'Invalid value';
          // Replace placeholders
          message = message.replace('{min}', ruleValue).replace('{max}', ruleValue);
          errors.push(message);
        }
      }
    });

    return errors;
  },

  /**
   * Validate entire form
   */
  validateForm(formData, schema) {
    const errors = {};

    Object.entries(schema).forEach(([field, rules]) => {
      const value = formData[field];
      const fieldErrors = this.validateField(value, rules, field);

      if (fieldErrors.length > 0) {
        errors[field] = fieldErrors[0]; // Show first error only
      }
    });

    return errors;
  },

  /**
   * Common validation schemas
   */
  schemas: {
    login: {
      email: { required: true, email: true },
      password: { required: true, minLength: 8 },
    },

    register: {
      name: { required: true, minLength: 2, maxLength: 100 },
      email: { required: true, email: true },
      password: { required: true, minLength: 8 },
      password_confirmation: { required: true },
    },

    profile: {
      name: { required: true, minLength: 2, maxLength: 100 },
      email: { required: true, email: true },
      phone: { phone: true },
      college_id: { required: true },
      program_id: { required: true },
      section_id: { required: true },
      batch_year: { required: true, min: 1950, max: 2100 },
      graduation_year: { required: true, min: 1950, max: 2100 },
    },

    changePassword: {
      current_password: { required: true },
      new_password: { required: true, minLength: 8 },
      confirm_password: { required: true },
    },

    event: {
      title: { required: true, minLength: 3, maxLength: 200 },
      description: { minLength: 10 },
      event_date: { required: true, date: true },
      event_time: {},
      location: {},
      max_attendees: { min: 1 },
      points_reward: { min: 0 },
    },

    announcement: {
      title: { required: true, minLength: 3, maxLength: 200 },
      content: { required: true, minLength: 10 },
      priority: { required: true },
    },
  },

  /**
   * Setup real-time validation for a form
   */
  setupRealtimeValidation(form, schema) {
    if (typeof form === 'string') {
      form = document.querySelector(form);
    }

    if (!form) return;

    Object.keys(schema).forEach((fieldName) => {
      const field = form.querySelector(`[name="${fieldName}"]`);
      if (!field) return;

      // Validate on blur
      field.addEventListener('blur', () => {
        const value = field.value;
        const errors = this.validateField(value, schema[fieldName], fieldName);

        this.showFieldError(field, errors[0]);
      });

      // Clear error on input
      field.addEventListener('input', () => {
        this.clearFieldError(field);
      });
    });
  },

  /**
   * Show field error
   */
  showFieldError(field, message) {
    this.clearFieldError(field);

    if (!message) return;

    field.classList.add('error');

    const errorEl = document.createElement('div');
    errorEl.className = 'form-error';
    errorEl.textContent = message;

    field.parentNode.appendChild(errorEl);
  },

  /**
   * Clear field error
   */
  clearFieldError(field) {
    field.classList.remove('error');

    const errorEl = field.parentNode.querySelector('.form-error');
    if (errorEl) {
      errorEl.remove();
    }
  },

  /**
   * Show all form errors
   */
  showFormErrors(form, errors) {
    if (typeof form === 'string') {
      form = document.querySelector(form);
    }

    // Clear existing errors
    this.clearFormErrors(form);

    // Show new errors
    Object.entries(errors).forEach(([field, message]) => {
      const input = form.querySelector(`[name="${field}"]`);
      if (input) {
        this.showFieldError(input, message);
      }
    });

    // Focus first error field
    const firstErrorField = form.querySelector('.error');
    if (firstErrorField) {
      firstErrorField.focus();
    }
  },

  /**
   * Clear all form errors
   */
  clearFormErrors(form) {
    if (typeof form === 'string') {
      form = document.querySelector(form);
    }

    form.querySelectorAll('.form-error').forEach((el) => el.remove());
    form.querySelectorAll('.error').forEach((el) => el.classList.remove('error'));
  },

  /**
   * Validate and submit form
   */
  async validateAndSubmit(form, schema, submitHandler) {
    if (typeof form === 'string') {
      form = document.querySelector(form);
    }

    const formData = Utils.serializeForm(form);

    // Validate password confirmation if present
    if (formData.password && formData.password_confirmation) {
      if (formData.password !== formData.password_confirmation) {
        this.showFormErrors(form, {
          password_confirmation: 'Passwords do not match',
        });
        return false;
      }
    }

    if (formData.new_password && formData.confirm_password) {
      if (formData.new_password !== formData.confirm_password) {
        this.showFormErrors(form, {
          confirm_password: 'Passwords do not match',
        });
        return false;
      }
    }

    // Validate form
    const errors = this.validateForm(formData, schema);

    if (Object.keys(errors).length > 0) {
      this.showFormErrors(form, errors);
      return false;
    }

    // Submit form
    try {
      await submitHandler(formData);
      return true;
    } catch (error) {
      // Show server errors if available
      if (error.errors && typeof error.errors === 'object') {
        this.showFormErrors(form, error.errors);
      }
      throw error;
    }
  },
};

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = Validation;
}
