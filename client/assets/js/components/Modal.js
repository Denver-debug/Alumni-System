/**
 * Modal Component
 * 
 * A reusable modal dialog component with backdrop, ESC key support,
 * and accessibility features.
 * 
 * Usage:
 *   const modal = new Modal({
 *     title: 'Modal Title',
 *     content: '<p>Modal content</p>',
 *     size: 'md', // 'sm', 'md', 'lg', 'xl'
 *     onClose: () => console.log('Modal closed')
 *   });
 *   modal.open();
 */

class Modal {
  constructor(options = {}) {
    this.options = {
      title: options.title || '',
      content: options.content || '',
      size: options.size || 'md', // sm, md, lg, xl
      closeOnBackdrop: options.closeOnBackdrop !== false,
      closeOnEsc: options.closeOnEsc !== false,
      showCloseButton: options.showCloseButton !== false,
      footer: options.footer || null,
      onOpen: options.onOpen || null,
      onClose: options.onClose || null,
      className: options.className || ''
    };

    this.isOpen = false;
    this.backdrop = null;
    this.modalContent = null;
    this.previousActiveElement = null;

    this._handleEscKey = this._handleEscKey.bind(this);
    this._handleBackdropClick = this._handleBackdropClick.bind(this);
  }

  /**
   * Create and render the modal DOM structure
   */
  _createModal() {
    // Create backdrop
    this.backdrop = document.createElement('div');
    this.backdrop.className = `modal-backdrop modal-${this.options.size} hidden ${this.options.className}`;
    this.backdrop.setAttribute('role', 'dialog');
    this.backdrop.setAttribute('aria-modal', 'true');
    if (this.options.title) {
      this.backdrop.setAttribute('aria-labelledby', 'modal-title');
    }

    // Create modal content container
    this.modalContent = document.createElement('div');
    this.modalContent.className = 'modal-content';

    // Create modal header
    if (this.options.title || this.options.showCloseButton) {
      const header = document.createElement('div');
      header.className = 'modal-header';

      if (this.options.title) {
        const title = document.createElement('h2');
        title.id = 'modal-title';
        title.className = 'modal-title';
        title.textContent = this.options.title;
        header.appendChild(title);
      }

      if (this.options.showCloseButton) {
        const closeButton = document.createElement('button');
        closeButton.type = 'button';
        closeButton.className = 'modal-close';
        closeButton.setAttribute('aria-label', 'Close modal');
        closeButton.innerHTML = `
          <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M15 5L5 15M5 5L15 15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        `;
        closeButton.addEventListener('click', () => this.close());
        header.appendChild(closeButton);
      }

      this.modalContent.appendChild(header);
    }

    // Create modal body
    const body = document.createElement('div');
    body.className = 'modal-body';
    if (typeof this.options.content === 'string') {
      body.innerHTML = this.options.content;
    } else if (this.options.content instanceof HTMLElement) {
      body.appendChild(this.options.content);
    }
    this.modalContent.appendChild(body);

    // Create modal footer (if provided)
    if (this.options.footer) {
      const footer = document.createElement('div');
      footer.className = 'modal-footer';
      if (typeof this.options.footer === 'string') {
        footer.innerHTML = this.options.footer;
      } else if (this.options.footer instanceof HTMLElement) {
        footer.appendChild(this.options.footer);
      }
      this.modalContent.appendChild(footer);
    }

    // Append modal content to backdrop
    this.backdrop.appendChild(this.modalContent);

    // Append backdrop to body
    document.body.appendChild(this.backdrop);
  }

  /**
   * Open the modal
   */
  open() {
    if (this.isOpen) return;

    // Create modal if it doesn't exist
    if (!this.backdrop) {
      this._createModal();
    }

    // Store currently focused element
    this.previousActiveElement = document.activeElement;

    // Show modal
    this.backdrop.classList.remove('hidden');
    this.isOpen = true;

    // Prevent body scroll
    document.body.classList.add('modal-open');

    // Add event listeners
    if (this.options.closeOnEsc) {
      document.addEventListener('keydown', this._handleEscKey);
    }
    if (this.options.closeOnBackdrop) {
      this.backdrop.addEventListener('click', this._handleBackdropClick);
    }

    // Focus first focusable element in modal
    setTimeout(() => {
      const focusable = this.modalContent.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
      );
      if (focusable.length > 0) {
        focusable[0].focus();
      }
    }, 100);

    // Call onOpen callback
    if (typeof this.options.onOpen === 'function') {
      this.options.onOpen();
    }
  }

  /**
   * Close the modal
   */
  close() {
    if (!this.isOpen) return;

    // Add fade-out animation
    this.backdrop.classList.add('fade-out');

    // Wait for animation to complete
    setTimeout(() => {
      // Hide modal
      this.backdrop.classList.add('hidden');
      this.backdrop.classList.remove('fade-out');
      this.isOpen = false;

      // Restore body scroll
      document.body.classList.remove('modal-open');

      // Remove event listeners
      document.removeEventListener('keydown', this._handleEscKey);
      this.backdrop.removeEventListener('click', this._handleBackdropClick);

      // Restore focus to previous element
      if (this.previousActiveElement) {
        this.previousActiveElement.focus();
        this.previousActiveElement = null;
      }

      // Call onClose callback
      if (typeof this.options.onClose === 'function') {
        this.options.onClose();
      }
    }, 200); // Match animation duration
  }

  /**
   * Destroy the modal and remove from DOM
   */
  destroy() {
    if (this.isOpen) {
      this.close();
    }

    setTimeout(() => {
      if (this.backdrop && this.backdrop.parentNode) {
        this.backdrop.parentNode.removeChild(this.backdrop);
      }
      this.backdrop = null;
      this.modalContent = null;
    }, 200);
  }

  /**
   * Update modal content
   */
  setContent(content) {
    const body = this.modalContent.querySelector('.modal-body');
    if (body) {
      if (typeof content === 'string') {
        body.innerHTML = content;
      } else if (content instanceof HTMLElement) {
        body.innerHTML = '';
        body.appendChild(content);
      }
    }
  }

  /**
   * Update modal title
   */
  setTitle(title) {
    const titleElement = this.modalContent.querySelector('.modal-title');
    if (titleElement) {
      titleElement.textContent = title;
    }
  }

  /**
   * Update modal footer
   */
  setFooter(footer) {
    let footerElement = this.modalContent.querySelector('.modal-footer');
    
    if (!footerElement && footer) {
      // Create footer if it doesn't exist
      footerElement = document.createElement('div');
      footerElement.className = 'modal-footer';
      this.modalContent.appendChild(footerElement);
    }

    if (footerElement) {
      if (typeof footer === 'string') {
        footerElement.innerHTML = footer;
      } else if (footer instanceof HTMLElement) {
        footerElement.innerHTML = '';
        footerElement.appendChild(footer);
      } else if (footer === null) {
        // Remove footer
        footerElement.parentNode.removeChild(footerElement);
      }
    }
  }

  /**
   * Handle ESC key press
   */
  _handleEscKey(event) {
    if (event.key === 'Escape' || event.keyCode === 27) {
      this.close();
    }
  }

  /**
   * Handle backdrop click
   */
  _handleBackdropClick(event) {
    if (event.target === this.backdrop) {
      this.close();
    }
  }

  /**
   * Check if modal is currently open
   */
  isModalOpen() {
    return this.isOpen;
  }
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
  module.exports = Modal;
}
