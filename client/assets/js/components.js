/**
 * Alumni Management System - Reusable Components
 * UI components for consistent user experience
 */

const Components = {
  /**
   * Render empty state
   */
  emptyState(options = {}) {
    const {
      icon = '📭',
      title = 'No items yet',
      message = 'Check back later for updates',
      actionText = null,
      actionHref = null,
      actionHandler = null,
    } = options;

    const actionButton = actionText
      ? actionHref
        ? `<a href="${actionHref}" class="btn btn-primary">${Utils.escapeHtml(actionText)}</a>`
        : `<button class="btn btn-primary" data-action="empty-state-action">${Utils.escapeHtml(actionText)}</button>`
      : '';

    const html = `
      <div class="empty-state">
        <div class="empty-state-icon">${icon}</div>
        <h3 class="empty-state-title">${Utils.escapeHtml(title)}</h3>
        <p class="empty-state-message">${Utils.escapeHtml(message)}</p>
        ${actionButton}
      </div>
    `;

    // If action handler provided, attach it
    if (actionHandler && actionText && !actionHref) {
      setTimeout(() => {
        const button = document.querySelector('[data-action="empty-state-action"]');
        if (button) {
          button.addEventListener('click', actionHandler);
        }
      }, 0);
    }

    return html;
  },

  /**
   * Render loading skeleton
   */
  loadingSkeleton(options = {}) {
    const { lines = 3, type = 'default' } = options;

    if (type === 'card') {
      return `
        <div class="loading-skeleton">
          <div class="skeleton-header"></div>
          <div class="skeleton-line"></div>
          <div class="skeleton-line"></div>
          <div class="skeleton-line short"></div>
        </div>
      `;
    }

    if (type === 'list') {
      return `
        <div class="loading-skeleton">
          ${Array.from({ length: lines })
            .map(
              () => `
            <div class="skeleton-list-item">
              <div class="skeleton-avatar"></div>
              <div class="skeleton-content">
                <div class="skeleton-line"></div>
                <div class="skeleton-line short"></div>
              </div>
            </div>
          `,
            )
            .join('')}
        </div>
      `;
    }

    if (type === 'table') {
      return `
        <div class="loading-skeleton">
          <table class="table">
            <thead>
              <tr>
                ${Array.from({ length: 4 })
                  .map(() => '<th><div class="skeleton-line"></div></th>')
                  .join('')}
              </tr>
            </thead>
            <tbody>
              ${Array.from({ length: 5 })
                .map(
                  () => `
                <tr>
                  ${Array.from({ length: 4 })
                    .map(() => '<td><div class="skeleton-line"></div></td>')
                    .join('')}
                </tr>
              `,
                )
                .join('')}
            </tbody>
          </table>
        </div>
      `;
    }

    // Default skeleton
    return `
      <div class="loading-skeleton">
        ${Array.from({ length: lines })
          .map(
            (_, i) => `
          <div class="skeleton-line ${i === lines - 1 ? 'short' : ''}"></div>
        `,
          )
          .join('')}
      </div>
    `;
  },

  /**
   * Render error state
   */
  errorState(options = {}) {
    const {
      title = 'Something went wrong',
      message = 'Please try again later',
      retryText = 'Try Again',
      retryHandler = null,
    } = options;

    const retryButton = retryHandler
      ? `<button class="btn btn-primary" data-action="error-retry">${Utils.escapeHtml(retryText)}</button>`
      : '';

    const html = `
      <div class="error-state">
        <div class="error-state-icon">⚠️</div>
        <h3 class="error-state-title">${Utils.escapeHtml(title)}</h3>
        <p class="error-state-message">${Utils.escapeHtml(message)}</p>
        ${retryButton}
      </div>
    `;

    // Attach retry handler
    if (retryHandler) {
      setTimeout(() => {
        const button = document.querySelector('[data-action="error-retry"]');
        if (button) {
          button.addEventListener('click', retryHandler);
        }
      }, 0);
    }

    return html;
  },

  /**
   * Render confirmation dialog
   */
  async confirmDialog(options = {}) {
    const {
      title = 'Confirm Action',
      message = 'Are you sure you want to proceed?',
      confirmText = 'Confirm',
      cancelText = 'Cancel',
      danger = false,
    } = options;

    return Utils.confirm(message, {
      title,
      confirmText,
      cancelText,
      danger,
    });
  },

  /**
   * Render success message
   */
  successMessage(message, duration = 5000) {
    return Utils.success(message, duration);
  },

  /**
   * Render error message
   */
  errorMessage(message, duration = 5000) {
    return Utils.error(message, duration);
  },

  /**
   * Render info message
   */
  infoMessage(message, duration = 5000) {
    return Utils.info(message, duration);
  },

  /**
   * Render warning message
   */
  warningMessage(message, duration = 5000) {
    return Utils.warning(message, duration);
  },

  /**
   * Render badge
   */
  badge(text, type = 'default') {
    const validTypes = ['default', 'primary', 'success', 'warning', 'danger', 'info'];
    const badgeType = validTypes.includes(type) ? type : 'default';

    return `<span class="badge badge-${badgeType}">${Utils.escapeHtml(text)}</span>`;
  },

  /**
   * Render avatar
   */
  avatar(options = {}) {
    const {
      name = '',
      image = null,
      size = 'md',
      className = '',
    } = options;

    const initials = Utils.getInitials(name);
    const sizeClass = `avatar-${size}`;

    if (image) {
      return `
        <div class="avatar ${sizeClass} ${className}">
          <img src="${Utils.escapeHtml(image)}" alt="${Utils.escapeHtml(name)}" />
        </div>
      `;
    }

    return `
      <div class="avatar ${sizeClass} bg-primary ${className}">
        <span>${Utils.escapeHtml(initials)}</span>
      </div>
    `;
  },

  /**
   * Render card
   */
  card(options = {}) {
    const {
      title = null,
      content = '',
      footer = null,
      headerActions = null,
      className = '',
    } = options;

    const header = title
      ? `
      <div class="card-header">
        <h3 class="card-title">${Utils.escapeHtml(title)}</h3>
        ${headerActions || ''}
      </div>
    `
      : '';

    const footerHtml = footer
      ? `
      <div class="card-footer">
        ${footer}
      </div>
    `
      : '';

    return `
      <div class="card ${className}">
        ${header}
        <div class="card-body">
          ${content}
        </div>
        ${footerHtml}
      </div>
    `;
  },

  /**
   * Render stat card
   */
  statCard(options = {}) {
    const {
      label = '',
      value = 0,
      icon = null,
      trend = null,
      trendUp = true,
      className = '',
    } = options;

    const iconHtml = icon
      ? `
      <div class="avatar avatar-md bg-primary-light">
        ${icon}
      </div>
    `
      : '';

    const trendHtml = trend
      ? `
      <div class="stat-trend ${trendUp ? 'trend-up' : 'trend-down'}">
        ${trendUp ? '↑' : '↓'} ${Utils.escapeHtml(trend)}
      </div>
    `
      : '';

    return `
      <div class="card stat-card ${className}">
        <div class="card-body">
          <div class="flex justify-between items-start">
            <div>
              <div class="text-secondary text-sm">${Utils.escapeHtml(label)}</div>
              <div class="text-2xl font-bold mt-sm">${Utils.escapeHtml(String(value))}</div>
              ${trendHtml}
            </div>
            ${iconHtml}
          </div>
        </div>
      </div>
    `;
  },

  /**
   * Render progress bar
   */
  progressBar(options = {}) {
    const {
      value = 0,
      max = 100,
      label = null,
      showPercentage = true,
      className = '',
    } = options;

    const percentage = Math.round((value / max) * 100);

    const labelHtml = label || showPercentage
      ? `
      <div class="progress-label">
        ${label ? `<span>${Utils.escapeHtml(label)}</span>` : ''}
        ${showPercentage ? `<span>${percentage}%</span>` : ''}
      </div>
    `
      : '';

    return `
      <div class="progress-wrapper ${className}">
        ${labelHtml}
        <div class="progress">
          <div class="progress-bar" style="width: ${percentage}%"></div>
        </div>
      </div>
    `;
  },

  /**
   * Render pagination
   */
  pagination(options = {}) {
    const {
      currentPage = 1,
      totalPages = 1,
      onPageChange = null,
      maxVisible = 5,
    } = options;

    if (totalPages <= 1) return '';

    const pages = [];
    let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
    let endPage = Math.min(totalPages, startPage + maxVisible - 1);

    if (endPage - startPage < maxVisible - 1) {
      startPage = Math.max(1, endPage - maxVisible + 1);
    }

    // Previous button
    pages.push(`
      <button class="pagination-btn" data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}>
        Previous
      </button>
    `);

    // First page
    if (startPage > 1) {
      pages.push(`<button class="pagination-btn" data-page="1">1</button>`);
      if (startPage > 2) {
        pages.push(`<span class="pagination-ellipsis">...</span>`);
      }
    }

    // Page numbers
    for (let i = startPage; i <= endPage; i++) {
      pages.push(`
        <button class="pagination-btn ${i === currentPage ? 'active' : ''}" data-page="${i}">
          ${i}
        </button>
      `);
    }

    // Last page
    if (endPage < totalPages) {
      if (endPage < totalPages - 1) {
        pages.push(`<span class="pagination-ellipsis">...</span>`);
      }
      pages.push(`<button class="pagination-btn" data-page="${totalPages}">${totalPages}</button>`);
    }

    // Next button
    pages.push(`
      <button class="pagination-btn" data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''}>
        Next
      </button>
    `);

    const html = `
      <div class="pagination">
        ${pages.join('')}
      </div>
    `;

    // Attach click handlers
    if (onPageChange) {
      setTimeout(() => {
        document.querySelectorAll('.pagination-btn').forEach((btn) => {
          btn.addEventListener('click', () => {
            const page = parseInt(btn.dataset.page);
            if (!isNaN(page) && page >= 1 && page <= totalPages) {
              onPageChange(page);
            }
          });
        });
      }, 0);
    }

    return html;
  },
};

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
  module.exports = Components;
}
