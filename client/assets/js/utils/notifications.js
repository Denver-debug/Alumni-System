/**
 * Notification Utilities
 * 
 * Toast notification system with auto-dismiss, queue management,
 * and multiple notification types.
 * 
 * Usage:
 *   Notifications.success('Operation completed successfully!');
 *   Notifications.error('An error occurred');
 *   Notifications.warning('Please review your input');
 *   Notifications.info('New updates available');
 */

const Notifications = (() => {
  // Configuration
  const config = {
    duration: 4000, // Default duration in milliseconds
    maxNotifications: 5, // Maximum number of notifications to show at once
    position: 'top-right', // top-right, top-left, bottom-right, bottom-left, top-center, bottom-center
    animationDuration: 300 // Animation duration in milliseconds
  };

  // Notification queue
  let notificationQueue = [];
  let container = null;
  let notificationIdCounter = 0;

  /**
   * Initialize the notification container
   */
  function initContainer() {
    if (container) return;

    container = document.createElement('div');
    container.id = 'notification-container';
    container.className = `notification-container notification-${config.position}`;
    container.setAttribute('aria-live', 'polite');
    container.setAttribute('aria-atomic', 'true');

    // Add container styles
    const style = document.createElement('style');
    style.textContent = `
      .notification-container {
        position: fixed;
        z-index: var(--ds-z-toast, 800);
        display: flex;
        flex-direction: column;
        gap: var(--ds-space-3, 0.75rem);
        pointer-events: none;
        max-width: 24rem;
      }

      .notification-top-right {
        top: var(--ds-space-4, 1rem);
        right: var(--ds-space-4, 1rem);
      }

      .notification-top-left {
        top: var(--ds-space-4, 1rem);
        left: var(--ds-space-4, 1rem);
      }

      .notification-bottom-right {
        bottom: var(--ds-space-4, 1rem);
        right: var(--ds-space-4, 1rem);
      }

      .notification-bottom-left {
        bottom: var(--ds-space-4, 1rem);
        left: var(--ds-space-4, 1rem);
      }

      .notification-top-center {
        top: var(--ds-space-4, 1rem);
        left: 50%;
        transform: translateX(-50%);
      }

      .notification-bottom-center {
        bottom: var(--ds-space-4, 1rem);
        left: 50%;
        transform: translateX(-50%);
      }

      .notification-toast {
        display: flex;
        align-items: flex-start;
        gap: var(--ds-space-3, 0.75rem);
        padding: var(--ds-space-4, 1rem);
        background-color: var(--ds-color-bg-primary, #ffffff);
        border: 1px solid var(--ds-color-border-secondary, #e5e7eb);
        border-radius: var(--ds-radius-lg, 0.75rem);
        box-shadow: var(--ds-shadow-lg, 0 10px 15px -3px rgba(0, 0, 0, 0.1));
        pointer-events: auto;
        cursor: pointer;
        transition: all var(--ds-duration-base, 200ms) var(--ds-ease-in-out, ease-in-out);
        animation: notification-slide-in var(--ds-duration-base, 200ms) var(--ds-ease-out, ease-out);
        min-width: 20rem;
      }

      .notification-toast:hover {
        transform: translateY(-2px);
        box-shadow: var(--ds-shadow-xl, 0 20px 25px -5px rgba(0, 0, 0, 0.1));
      }

      .notification-toast.notification-removing {
        animation: notification-slide-out var(--ds-duration-base, 200ms) var(--ds-ease-in, ease-in);
      }

      .notification-icon {
        flex-shrink: 0;
        width: 1.25rem;
        height: 1.25rem;
        margin-top: 0.125rem;
      }

      .notification-content {
        flex: 1;
        min-width: 0;
      }

      .notification-title {
        font-size: var(--ds-text-sm, 0.875rem);
        font-weight: var(--ds-font-semibold, 600);
        color: var(--ds-color-text-primary, #111827);
        margin: 0 0 var(--ds-space-1, 0.25rem) 0;
        line-height: var(--ds-leading-tight, 1.25);
      }

      .notification-message {
        font-size: var(--ds-text-sm, 0.875rem);
        color: var(--ds-color-text-secondary, #4b5563);
        margin: 0;
        line-height: var(--ds-leading-normal, 1.5);
        word-wrap: break-word;
      }

      .notification-close {
        flex-shrink: 0;
        width: 1.25rem;
        height: 1.25rem;
        padding: 0;
        background: transparent;
        border: none;
        color: var(--ds-color-text-tertiary, #6b7280);
        cursor: pointer;
        transition: color var(--ds-duration-fast, 150ms) var(--ds-ease-in-out, ease-in-out);
      }

      .notification-close:hover {
        color: var(--ds-color-text-primary, #111827);
      }

      .notification-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background-color: currentColor;
        border-radius: 0 0 var(--ds-radius-lg, 0.75rem) var(--ds-radius-lg, 0.75rem);
        transition: width linear;
      }

      /* Type-specific styles */
      .notification-success {
        border-left: 4px solid var(--ds-color-success, #22c55e);
      }

      .notification-success .notification-icon {
        color: var(--ds-color-success, #22c55e);
      }

      .notification-success .notification-progress {
        background-color: var(--ds-color-success, #22c55e);
      }

      .notification-error {
        border-left: 4px solid var(--ds-color-error, #ef4444);
      }

      .notification-error .notification-icon {
        color: var(--ds-color-error, #ef4444);
      }

      .notification-error .notification-progress {
        background-color: var(--ds-color-error, #ef4444);
      }

      .notification-warning {
        border-left: 4px solid var(--ds-color-warning, #f59e0b);
      }

      .notification-warning .notification-icon {
        color: var(--ds-color-warning, #f59e0b);
      }

      .notification-warning .notification-progress {
        background-color: var(--ds-color-warning, #f59e0b);
      }

      .notification-info {
        border-left: 4px solid var(--ds-color-info, #06b6d4);
      }

      .notification-info .notification-icon {
        color: var(--ds-color-info, #06b6d4);
      }

      .notification-info .notification-progress {
        background-color: var(--ds-color-info, #06b6d4);
      }

      /* Animations */
      @keyframes notification-slide-in {
        from {
          opacity: 0;
          transform: translateX(100%);
        }
        to {
          opacity: 1;
          transform: translateX(0);
        }
      }

      @keyframes notification-slide-out {
        from {
          opacity: 1;
          transform: translateX(0);
        }
        to {
          opacity: 0;
          transform: translateX(100%);
        }
      }

      /* Responsive */
      @media (max-width: 767px) {
        .notification-container {
          max-width: calc(100vw - 2rem);
        }

        .notification-toast {
          min-width: auto;
        }
      }
    `;

    if (!document.getElementById('notification-styles')) {
      style.id = 'notification-styles';
      document.head.appendChild(style);
    }

    document.body.appendChild(container);
  }

  /**
   * Get icon SVG for notification type
   */
  function getIcon(type) {
    const icons = {
      success: `
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M16.6667 5L7.50004 14.1667L3.33337 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      `,
      error: `
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M10 6V10M10 14H10.01M18 10C18 14.4183 14.4183 18 10 18C5.58172 18 2 14.4183 2 10C2 5.58172 5.58172 2 10 2C14.4183 2 18 5.58172 18 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      `,
      warning: `
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M10 6V10M10 14H10.01M8.27208 2.5L1.53208 14C1.37497 14.2598 1.29149 14.5578 1.29065 14.8616C1.28982 15.1654 1.37166 15.4639 1.52733 15.7246C1.683 15.9853 1.90665 16.1986 2.17405 16.3423C2.44145 16.486 2.74332 16.5547 3.04708 16.5417H16.5271C16.8308 16.5547 17.1327 16.486 17.4001 16.3423C17.6675 16.1986 17.8911 15.9853 18.0468 15.7246C18.2025 15.4639 18.2843 15.1654 18.2835 14.8616C18.2826 14.5578 18.1992 14.2598 18.0421 14L11.3021 2.5C11.1433 2.24631 10.9188 2.03963 10.6521 1.90162C10.3854 1.76361 10.0857 1.69922 9.78208 1.71429C9.47846 1.72936 9.18555 1.82343 8.93208 1.98629C8.67861 2.14915 8.47308 2.37515 8.33708 2.64L8.27208 2.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      `,
      info: `
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M10 14V10M10 6H10.01M18 10C18 14.4183 14.4183 18 10 18C5.58172 18 2 14.4183 2 10C2 5.58172 5.58172 2 10 2C14.4183 2 18 5.58172 18 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      `
    };

    return icons[type] || icons.info;
  }

  /**
   * Create a notification element
   */
  function createNotification(options) {
    const notification = document.createElement('div');
    notification.className = `notification-toast notification-${options.type}`;
    notification.setAttribute('role', 'alert');
    notification.dataset.notificationId = options.id;

    // Icon
    const icon = document.createElement('div');
    icon.className = 'notification-icon';
    icon.innerHTML = getIcon(options.type);
    notification.appendChild(icon);

    // Content
    const content = document.createElement('div');
    content.className = 'notification-content';

    if (options.title) {
      const title = document.createElement('div');
      title.className = 'notification-title';
      title.textContent = options.title;
      content.appendChild(title);
    }

    const message = document.createElement('div');
    message.className = 'notification-message';
    message.textContent = options.message;
    content.appendChild(message);

    notification.appendChild(content);

    // Close button
    const closeButton = document.createElement('button');
    closeButton.type = 'button';
    closeButton.className = 'notification-close';
    closeButton.setAttribute('aria-label', 'Close notification');
    closeButton.innerHTML = `
      <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M15 5L5 15M5 5L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    `;
    closeButton.addEventListener('click', (e) => {
      e.stopPropagation();
      removeNotification(options.id);
    });
    notification.appendChild(closeButton);

    // Progress bar (if duration is set)
    if (options.duration > 0) {
      const progress = document.createElement('div');
      progress.className = 'notification-progress';
      progress.style.width = '100%';
      progress.style.transition = `width ${options.duration}ms linear`;
      notification.appendChild(progress);

      // Animate progress bar
      setTimeout(() => {
        progress.style.width = '0%';
      }, 10);
    }

    // Click to dismiss
    notification.addEventListener('click', () => {
      removeNotification(options.id);
    });

    return notification;
  }

  /**
   * Show a notification
   */
  function show(options) {
    initContainer();

    // Generate unique ID
    const id = ++notificationIdCounter;

    // Create notification object
    const notification = {
      id,
      type: options.type || 'info',
      title: options.title || null,
      message: options.message || '',
      duration: options.duration !== undefined ? options.duration : config.duration,
      onClose: options.onClose || null
    };

    // Add to queue
    notificationQueue.push(notification);

    // Remove oldest if exceeding max
    if (notificationQueue.length > config.maxNotifications) {
      const oldest = notificationQueue.shift();
      removeNotification(oldest.id);
    }

    // Create and append notification element
    const element = createNotification(notification);
    container.appendChild(element);

    // Auto-dismiss after duration
    if (notification.duration > 0) {
      setTimeout(() => {
        removeNotification(id);
      }, notification.duration);
    }

    return id;
  }

  /**
   * Remove a notification
   */
  function removeNotification(id) {
    const element = container?.querySelector(`[data-notification-id="${id}"]`);
    if (!element) return;

    // Add removing class for animation
    element.classList.add('notification-removing');

    // Remove after animation
    setTimeout(() => {
      if (element.parentNode) {
        element.parentNode.removeChild(element);
      }

      // Remove from queue
      notificationQueue = notificationQueue.filter(n => n.id !== id);

      // Call onClose callback
      const notification = notificationQueue.find(n => n.id === id);
      if (notification && typeof notification.onClose === 'function') {
        notification.onClose();
      }
    }, config.animationDuration);
  }

  /**
   * Clear all notifications
   */
  function clearAll() {
    notificationQueue.forEach(notification => {
      removeNotification(notification.id);
    });
  }

  /**
   * Public API
   */
  return {
    /**
     * Show a success notification
     */
    success(message, options = {}) {
      return show({
        type: 'success',
        message,
        ...options
      });
    },

    /**
     * Show an error notification
     */
    error(message, options = {}) {
      return show({
        type: 'error',
        message,
        duration: options.duration !== undefined ? options.duration : 5000, // Longer duration for errors
        ...options
      });
    },

    /**
     * Show a warning notification
     */
    warning(message, options = {}) {
      return show({
        type: 'warning',
        message,
        ...options
      });
    },

    /**
     * Show an info notification
     */
    info(message, options = {}) {
      return show({
        type: 'info',
        message,
        ...options
      });
    },

    /**
     * Show a custom notification
     */
    show(options) {
      return show(options);
    },

    /**
     * Remove a specific notification
     */
    remove(id) {
      removeNotification(id);
    },

    /**
     * Clear all notifications
     */
    clearAll() {
      clearAll();
    },

    /**
     * Configure notification system
     */
    configure(options) {
      Object.assign(config, options);
      
      // Update container position if it exists
      if (container) {
        container.className = `notification-container notification-${config.position}`;
      }
    }
  };
})();

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
  module.exports = Notifications;
}
