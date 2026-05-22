(function () {
  class NavigationManager {
    static init(options = {}) {
      const selectors = {
        sidebar: options.sidebarSelector || '#sidebar',
        toggle: options.toggleSelector || '#sidebarToggle',
      };

      const sidebar = document.querySelector(selectors.sidebar);
      const toggle = document.querySelector(selectors.toggle);
      if (!sidebar) return;

      const collapse = () => {
        sidebar.classList.add('sidebar-collapsed');
        document.body.classList.add('sidebar-collapsed');
      };

      const expand = () => {
        sidebar.classList.remove('sidebar-collapsed');
        document.body.classList.remove('sidebar-collapsed');
      };

      sidebar.querySelectorAll('.sidebar-section-toggle').forEach((button) => {
        button.addEventListener('click', () => {
          const section = button.closest('.sidebar-section');
          if (!section) return;
          const isOpen = section.classList.toggle('expanded');
          button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
      });

      if (toggle) {
        toggle.addEventListener('click', () => {
          if (sidebar.classList.contains('sidebar-collapsed')) {
            expand();
          } else {
            collapse();
          }
        });
      }

      return { collapse, expand };
    }
  }

  window.NavigationManager = NavigationManager;
})();
