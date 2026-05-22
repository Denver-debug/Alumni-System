/**
 * Responsive Helpers
 * JavaScript utilities for responsive layout behaviors
 */

const ResponsiveHelpers = {
  /**
   * Initialize all responsive helpers
   */
  init() {
    this.initTableScroll();
    this.initSidebarToggle();
    this.initTouchTargets();
    this.initSafeAreaInsets();
    this.initModalResponsive();
  },

  /**
   * Add scroll indicators to tables on mobile
   */
  initTableScroll() {
    const tableContainers = document.querySelectorAll('.table-container, .table-responsive');
    
    tableContainers.forEach(container => {
      const table = container.querySelector('table');
      if (!table) return;

      // Add scroll indicator class
      const updateScrollIndicator = () => {
        const isScrollable = container.scrollWidth > container.clientWidth;
        const isAtStart = container.scrollLeft === 0;
        const isAtEnd = container.scrollLeft + container.clientWidth >= container.scrollWidth - 1;

        container.classList.toggle('has-scroll', isScrollable);
        container.classList.toggle('scroll-start', isScrollable && isAtStart);
        container.classList.toggle('scroll-end', isScrollable && isAtEnd);
        container.classList.toggle('scroll-middle', isScrollable && !isAtStart && !isAtEnd);
      };

      // Check on load and resize
      updateScrollIndicator();
      window.addEventListener('resize', updateScrollIndicator);
      container.addEventListener('scroll', updateScrollIndicator);
    });
  },

  /**
   * Handle sidebar toggle on mobile
   */
  initSidebarToggle() {
    const sidebar = document.querySelector('.sidebar');
    const menuToggle = document.querySelector('.topbar-menu-btn, .sidebar-toggle, #menuToggle');
    const backdrop = document.querySelector('.sidebar-overlay, .sidebar-backdrop, #sidebarBackdrop');
    
    if (!sidebar || !menuToggle) return;

    // Create backdrop if it doesn't exist
    let sidebarBackdrop = backdrop;
    if (!sidebarBackdrop) {
      sidebarBackdrop = document.createElement('div');
      sidebarBackdrop.className = 'sidebar-backdrop';
      sidebarBackdrop.style.cssText = `
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1100;
        display: none;
        opacity: 0;
        transition: opacity 0.24s ease;
      `;
      document.body.appendChild(sidebarBackdrop);
    }

    // Toggle sidebar
    const toggleSidebar = () => {
      const isOpen = sidebar.classList.contains('sidebar-open') || sidebar.classList.contains('active') || sidebar.classList.contains('open');
      
      if (isOpen) {
        sidebar.classList.remove('sidebar-open', 'active', 'open');
        sidebarBackdrop.style.display = 'none';
        setTimeout(() => {
          sidebarBackdrop.style.opacity = '0';
        }, 10);
        document.body.style.overflow = '';
      } else {
        sidebar.classList.add('sidebar-open', 'active', 'open');
        sidebarBackdrop.style.display = 'block';
        setTimeout(() => {
          sidebarBackdrop.style.opacity = '1';
        }, 10);
        document.body.style.overflow = 'hidden';
      }
    };

    // Close sidebar
    const closeSidebar = () => {
      sidebar.classList.remove('sidebar-open', 'active', 'open');
      sidebarBackdrop.style.display = 'none';
      sidebarBackdrop.style.opacity = '0';
      document.body.style.overflow = '';
    };

    // Event listeners
    menuToggle.addEventListener('click', toggleSidebar);
    sidebarBackdrop.addEventListener('click', closeSidebar);

    // Close on escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && sidebar.classList.contains('sidebar-open')) {
        closeSidebar();
      }
    });

    // Close sidebar when clicking a link on mobile
    if (window.innerWidth < 1024) {
      const sidebarLinks = sidebar.querySelectorAll('.sidebar-link, .sidebar-nav a');
      sidebarLinks.forEach(link => {
        link.addEventListener('click', () => {
          setTimeout(closeSidebar, 100);
        });
      });
    }

    // Close sidebar on window resize to desktop
    window.addEventListener('resize', () => {
      if (window.innerWidth >= 1024) {
        closeSidebar();
      }
    });
  },

  /**
   * Ensure touch targets are at least 44x44px on mobile
   */
  initTouchTargets() {
    if (window.innerWidth >= 768) return;

    const touchElements = document.querySelectorAll('button, a, input[type="checkbox"], input[type="radio"], .btn, .tab, .dropdown-item');
    
    touchElements.forEach(element => {
      const rect = element.getBoundingClientRect();
      const minSize = 44;

      // Add padding if element is too small
      if (rect.height < minSize) {
        const paddingNeeded = (minSize - rect.height) / 2;
        const currentPadding = parseFloat(getComputedStyle(element).paddingTop) || 0;
        element.style.paddingTop = `${currentPadding + paddingNeeded}px`;
        element.style.paddingBottom = `${currentPadding + paddingNeeded}px`;
      }
    });
  },

  /**
   * Apply safe area insets for notched devices
   */
  initSafeAreaInsets() {
    // Check if device supports safe area insets
    const supportsSafeArea = CSS.supports('padding: env(safe-area-inset-top)');
    
    if (supportsSafeArea) {
      document.documentElement.style.setProperty('--safe-area-top', 'env(safe-area-inset-top)');
      document.documentElement.style.setProperty('--safe-area-right', 'env(safe-area-inset-right)');
      document.documentElement.style.setProperty('--safe-area-bottom', 'env(safe-area-inset-bottom)');
      document.documentElement.style.setProperty('--safe-area-left', 'env(safe-area-inset-left)');
    } else {
      document.documentElement.style.setProperty('--safe-area-top', '0px');
      document.documentElement.style.setProperty('--safe-area-right', '0px');
      document.documentElement.style.setProperty('--safe-area-bottom', '0px');
      document.documentElement.style.setProperty('--safe-area-left', '0px');
    }
  },

  /**
   * Make modals responsive on mobile
   */
  initModalResponsive() {
    const modals = document.querySelectorAll('.ds-modal-backdrop, .modal-overlay, .modal');
    
    modals.forEach(modal => {
      const modalContent = modal.querySelector('.ds-modal-content, .modal-dialog, .modal-content');
      if (!modalContent) return;

      // Adjust modal size on mobile
      const adjustModalSize = () => {
        if (window.innerWidth < 768) {
          modalContent.style.maxWidth = 'calc(100vw - 2rem)';
          modalContent.style.margin = '1rem';
          modalContent.style.maxHeight = 'calc(100vh - 2rem)';
        } else {
          modalContent.style.maxWidth = '';
          modalContent.style.margin = '';
          modalContent.style.maxHeight = '';
        }
      };

      adjustModalSize();
      window.addEventListener('resize', adjustModalSize);
    });
  },

  /**
   * Detect if device is mobile
   */
  isMobile() {
    return window.innerWidth < 768;
  },

  /**
   * Detect if device is tablet
   */
  isTablet() {
    return window.innerWidth >= 768 && window.innerWidth < 1024;
  },

  /**
   * Detect if device is desktop
   */
  isDesktop() {
    return window.innerWidth >= 1024;
  },

  /**
   * Get current breakpoint
   */
  getBreakpoint() {
    if (this.isMobile()) return 'mobile';
    if (this.isTablet()) return 'tablet';
    return 'desktop';
  }
};

// Auto-initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => ResponsiveHelpers.init());
} else {
  ResponsiveHelpers.init();
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
  module.exports = ResponsiveHelpers;
}
