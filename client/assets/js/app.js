/**
 * Alumni Management System - Main Application
 */

// Wait for DOM ready
document.addEventListener("DOMContentLoaded", () => {
  App.init().catch((error) => {
    console.error("App initialization failed:", error);
    App.renderStartupError(error);
  });
});

const App = {
  cacheBuster: "20260518.05",
  firebaseReady: false,
  firebaseInitError: "",
  _globalNotificationInterval: null,
  _globalNotificationSnapshotReady: false,
  _messageNotificationSnapshot: null,
  _lastMessageNotificationKey: "",
  _lastIncomingCallId: "",
  _activeRingingCallId: "",
  _globalRingtoneInterval: null,
  _globalCallNotification: null,
  _globalNotificationPollBusy: false,
  _notificationPermissionAsked: false,
  _notificationAudioContext: null,
  _notificationAudioUnlockBound: false,

  /**
   * Force template refreshes after frontend updates to avoid stale browser cache.
   */
  getCacheBustedPath(path) {
    const rawPath = String(path || "").trim();
    if (!rawPath || /^(https?:|data:|blob:)/i.test(rawPath)) {
      return rawPath;
    }

    const delimiter = rawPath.includes("?") ? "&" : "?";
    return `${rawPath}${delimiter}v=${this.cacheBuster}`;
  },

  resolvePagePath(path) {
    const rawPath = String(path || "").trim();
    if (/^pages\/.+\.html$/i.test(rawPath)) {
      return rawPath.replace(/\.html$/i, ".php");
    }

    return rawPath;
  },

  resolvePageAssetPath(path) {
    const rawPath = String(path || "").trim();
    if (!rawPath || /^(https?:|data:|blob:)/i.test(rawPath)) {
      return rawPath;
    }

    if (rawPath.startsWith("/assets/")) {
      return `assets/${rawPath.slice("/assets/".length)}`;
    }

    return rawPath;
  },

  normalizePageAssetReferences(html) {
    return String(html || "")
      .replace(/(\b(?:href|src)=["'])\/assets\//gi, "$1assets/")
      .replace(/(url\(["']?)\/assets\//gi, "$1assets/");
  },

  cacheBustPageStyles(container) {
    if (!container) {
      return;
    }

    container
      .querySelectorAll('link[rel="stylesheet"][href]')
      .forEach((link) => {
        const href = link.getAttribute("href");
        if (!href || /^(https?:|data:|blob:)/i.test(href)) {
          return;
        }

        link.setAttribute(
          "href",
          this.getCacheBustedPath(this.resolvePageAssetPath(href)),
        );
      });
  },

  injectAdminLayoutGuard(container) {
    if (!container || document.body?.dataset?.appSection !== "admin") {
      return;
    }

    const existing = document.getElementById("admin-layout-guard-style");
    if (existing) {
      existing.remove();
    }

    const style = document.createElement("style");
    style.id = "admin-layout-guard-style";
    style.textContent = `
      body[data-app-section="admin"] .dashboard-layout .admin-content,
      body[data-app-section="admin"] .dashboard-layout .content-body,
      body[data-app-section="admin"] .dashboard-layout .content-wrapper {
        width: 100% !important;
        max-width: none !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding: clamp(1.35rem, 2.3vw, 2rem) clamp(1.5rem, 2.8vw, 2.35rem) !important;
        box-sizing: border-box !important;
      }

      body[data-app-section="admin"] .dashboard-layout .card,
      body[data-app-section="admin"] .dashboard-layout .card-improved,
      body[data-app-section="admin"] .dashboard-layout .admin-panel {
        width: 100% !important;
      }

      body[data-app-section="admin"] .dashboard-layout .card > .card-header,
      body[data-app-section="admin"] .dashboard-layout .card-improved > .card-header,
      body[data-app-section="admin"] .dashboard-layout .admin-panel-header {
        padding: clamp(1.15rem, 1.8vw, 1.45rem) clamp(1.65rem, 2.5vw, 2.35rem) !important;
        min-height: 4.15rem !important;
      }

      body[data-app-section="admin"] .dashboard-layout .card > .card-header .card-title,
      body[data-app-section="admin"] .dashboard-layout .card-improved > .card-header .card-title,
      body[data-app-section="admin"] .dashboard-layout .card-header h2,
      body[data-app-section="admin"] .dashboard-layout .card-header h3,
      body[data-app-section="admin"] .dashboard-layout .admin-panel-title {
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1.42 !important;
      }

      body[data-app-section="admin"] .dashboard-layout .card-body:not(.p-0),
      body[data-app-section="admin"] .dashboard-layout .admin-panel-body {
        padding: clamp(1.2rem, 2vw, 1.65rem) clamp(1.65rem, 2.5vw, 2.35rem) !important;
      }

      body[data-app-section="admin"] .dashboard-layout .card-body.p-0 > .p-lg,
      body[data-app-section="admin"] .dashboard-layout .card-body.p-0 > .p-md,
      body[data-app-section="admin"] .dashboard-layout .card-body.p-0 > .text-secondary,
      body[data-app-section="admin"] .dashboard-layout .card-body.p-0 > .divide-y > *,
      body[data-app-section="admin"] .dashboard-layout #upcomingEvents > * {
        padding-left: clamp(1.65rem, 2.5vw, 2.35rem) !important;
        padding-right: clamp(1.65rem, 2.5vw, 2.35rem) !important;
      }

      body[data-app-section="admin"] .dashboard-layout .table,
      body[data-app-section="admin"] .dashboard-layout .data-table,
      body[data-app-section="admin"] .dashboard-layout .table-improved {
        width: 100% !important;
      }

      body[data-app-section="admin"] .dashboard-layout .table th:first-child,
      body[data-app-section="admin"] .dashboard-layout .table td:first-child,
      body[data-app-section="admin"] .dashboard-layout .data-table th:first-child,
      body[data-app-section="admin"] .dashboard-layout .data-table td:first-child,
      body[data-app-section="admin"] .dashboard-layout .table-improved th:first-child,
      body[data-app-section="admin"] .dashboard-layout .table-improved td:first-child {
        padding-left: clamp(1.65rem, 2.5vw, 2.35rem) !important;
      }

      body[data-app-section="admin"] .dashboard-layout .table th:last-child,
      body[data-app-section="admin"] .dashboard-layout .table td:last-child,
      body[data-app-section="admin"] .dashboard-layout .data-table th:last-child,
      body[data-app-section="admin"] .dashboard-layout .data-table td:last-child,
      body[data-app-section="admin"] .dashboard-layout .table-improved th:last-child,
      body[data-app-section="admin"] .dashboard-layout .table-improved td:last-child {
        padding-right: clamp(1.65rem, 2.5vw, 2.35rem) !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar,
      body[data-app-section="admin"] .theme-ui-sidebar {
        background: var(--admin-surface) !important;
        border-right-color: var(--admin-border) !important;
        box-shadow: 10px 0 28px -30px rgb(15 23 42 / 0.65) !important;
      }

      body[data-app-section="admin"][data-admin-sidebar-style="dark"] .dashboard-layout .sidebar,
      body[data-app-section="admin"][data-admin-sidebar-style="dark"] .theme-ui-sidebar {
        background: linear-gradient(180deg, #0f172a 0%, #111c31 100%) !important;
        border-right-color: #26344c !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-link,
      body[data-app-section="admin"] .dashboard-layout .sidebar-section-toggle,
      body[data-app-section="admin"] .theme-ui-sidebar-link,
      body[data-app-section="admin"] .theme-ui-sidebar-toggle {
        color: var(--admin-sidebar-link) !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-link:hover,
      body[data-app-section="admin"] .dashboard-layout .sidebar-section-toggle:hover,
      body[data-app-section="admin"] .theme-ui-sidebar-link:hover,
      body[data-app-section="admin"] .theme-ui-sidebar-toggle:hover {
        background: var(--admin-sidebar-link-hover) !important;
        color: var(--admin-heading) !important;
      }

      body[data-app-section="admin"][data-admin-sidebar-style="dark"] .dashboard-layout .sidebar-link:hover,
      body[data-app-section="admin"][data-admin-sidebar-style="dark"] .theme-ui-sidebar-link:hover {
        color: #f8fafc !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-link.active,
      body[data-app-section="admin"] .theme-ui-sidebar-link.active {
        background: var(--admin-sidebar-link-active) !important;
        border-color: var(--admin-sidebar-link-active-border) !important;
        color: var(--admin-sidebar-link-active-text) !important;
        box-shadow: inset 3px 0 0 var(--color-primary) !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-section-title,
      body[data-app-section="admin"] .dashboard-layout .sidebar-section-chevron {
        color: var(--admin-sidebar-link) !important;
      }

      body[data-app-section="admin"] .dashboard-layout .dashboard-charts-grid > .stat-card-improved,
      body[data-app-section="admin"] .dashboard-layout .stats-grid > .stat-card-improved,
      body[data-app-section="admin"] .dashboard-layout .stat-card-improved {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: clamp(0.85rem, 1.4vw, 1.1rem) !important;
        min-height: 116px !important;
        padding: clamp(1rem, 1.6vw, 1.25rem) !important;
        overflow: hidden !important;
      }

      body[data-app-section="admin"] .dashboard-layout .stat-card-improved .stat-icon {
        position: relative !important;
        inset: auto !important;
        flex: 0 0 48px !important;
        width: 48px !important;
        height: 48px !important;
        min-width: 48px !important;
        margin: 0 !important;
      }

      body[data-app-section="admin"] .dashboard-layout .stat-card-improved .stat-info {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        display: grid !important;
        gap: 0.18rem !important;
      }

      body[data-app-section="admin"] .dashboard-layout .stat-card-improved .stat-value,
      body[data-app-section="admin"] .dashboard-layout .analytics-card-value {
        font-size: clamp(1.45rem, 2.2vw, 1.85rem) !important;
        line-height: 1.1 !important;
      }

      body[data-app-section="admin"] .dashboard-layout .analytics-card {
        display: grid !important;
        align-content: center !important;
        justify-items: start !important;
        min-height: 104px !important;
        padding: clamp(1rem, 1.6vw, 1.2rem) !important;
        text-align: left !important;
      }

      body[data-app-section="admin"] .dashboard-layout .table-container,
      body[data-app-section="admin"] .dashboard-layout .table-responsive,
      body[data-app-section="admin"] .dashboard-layout .card-body.p-0:has(> table) {
        border: 1px solid var(--admin-line) !important;
        border-radius: 10px !important;
        background: #ffffff !important;
        overflow-x: auto !important;
        overflow-y: hidden !important;
      }

      body[data-app-section="admin"] .dashboard-layout .table,
      body[data-app-section="admin"] .dashboard-layout .data-table,
      body[data-app-section="admin"] .dashboard-layout .table-improved {
        width: 100% !important;
        min-width: 720px !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        table-layout: auto !important;
        background: #ffffff !important;
      }

      body[data-app-section="admin"] .dashboard-layout .table th,
      body[data-app-section="admin"] .dashboard-layout .table td,
      body[data-app-section="admin"] .dashboard-layout .data-table th,
      body[data-app-section="admin"] .dashboard-layout .data-table td,
      body[data-app-section="admin"] .dashboard-layout .table-improved th,
      body[data-app-section="admin"] .dashboard-layout .table-improved td {
        padding: 0.9rem 1rem !important;
        max-width: 26rem !important;
        white-space: normal !important;
        overflow-wrap: anywhere !important;
        word-break: normal !important;
        border-bottom: 1px solid #e6ede8 !important;
        line-height: 1.45 !important;
        vertical-align: middle !important;
      }

      body[data-app-section="admin"] .dashboard-layout .table thead th,
      body[data-app-section="admin"] .dashboard-layout .data-table thead th,
      body[data-app-section="admin"] .dashboard-layout .table-improved thead th {
        background: linear-gradient(180deg, #f8fbf9 0%, #eef5f1 100%) !important;
        color: #334155 !important;
        border-bottom: 1px solid #d9e4dd !important;
        font-size: 0.75rem !important;
        font-weight: 800 !important;
        letter-spacing: 0.05em !important;
        text-transform: uppercase !important;
        white-space: nowrap !important;
      }

      body[data-app-section="admin"] .dashboard-layout .table tbody tr,
      body[data-app-section="admin"] .dashboard-layout .data-table tbody tr,
      body[data-app-section="admin"] .dashboard-layout .table-improved tbody tr {
        background: #ffffff !important;
      }

      body[data-app-section="admin"] .dashboard-layout .table tbody tr:hover,
      body[data-app-section="admin"] .dashboard-layout .data-table tbody tr:hover,
      body[data-app-section="admin"] .dashboard-layout .table-improved tbody tr:hover {
        background: var(--primary-50) !important;
        box-shadow: inset 3px 0 0 var(--color-primary) !important;
      }

      body[data-app-section="admin"] .dashboard-layout .table td .flex,
      body[data-app-section="admin"] .dashboard-layout .table-improved td .flex {
        flex-wrap: wrap !important;
      }

      body[data-app-section="admin"] .dashboard-layout .table .action-buttons,
      body[data-app-section="admin"] .dashboard-layout .table-improved .action-buttons {
        flex-wrap: nowrap !important;
      }

      body[data-app-section="admin"] .dashboard-layout .dashboard-sidebar-backdrop {
        display: none !important;
        opacity: 0 !important;
        pointer-events: none !important;
      }

      body[data-app-section="admin"] .modal-improved {
        position: fixed !important;
        inset: 0 !important;
        z-index: 3000 !important;
        display: none !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 1rem !important;
      }

      body[data-app-section="admin"] .modal-improved.active {
        display: flex !important;
      }

      body[data-app-section="admin"] .modal-improved .modal-backdrop {
        position: absolute !important;
        inset: 0 !important;
        z-index: 0 !important;
        display: block !important;
        padding: 0 !important;
        background: rgb(15 23 42 / 0.58) !important;
      }

      body[data-app-section="admin"] .theme-ui-content {
        flex: 1 1 auto !important;
        width: auto !important;
        max-width: none !important;
        min-width: 0 !important;
        display: block !important;
        padding: clamp(1.4rem, 2.4vw, 2.25rem) clamp(1.5rem, 2.8vw, 2.35rem) !important;
        box-sizing: border-box !important;
      }

      body[data-app-section="admin"] .theme-ui-shell {
        --admin-sidebar-width: 284px;
      }

      body[data-app-section="admin"] .theme-ui-topbar {
        padding-left: clamp(0.75rem, 1.6vw, 1.25rem) !important;
        padding-right: clamp(0.75rem, 1.6vw, 1.25rem) !important;
      }

      body[data-app-section="admin"] .theme-ui-topbar-left {
        justify-content: flex-start !important;
        margin-left: 0 !important;
      }

      body[data-app-section="admin"] .theme-ui-topbar-left h1 {
        margin: 0 !important;
        text-align: left !important;
      }

      body[data-app-section="admin"] .theme-ui-page {
        display: flex !important;
        width: 100% !important;
        padding-left: var(--admin-sidebar-width) !important;
      }

      body[data-app-section="admin"] .theme-ui-shell.settings-sidebar-collapsed .theme-ui-page {
        padding-left: 0 !important;
      }

      body[data-app-section="admin"] .theme-ui-sidebar {
        width: var(--admin-sidebar-width) !important;
      }

      body[data-app-section="admin"] .theme-ui-content > .theme-ui-panel {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
      }

      body[data-app-section="admin"] .theme-ui-content > .theme-ui-panel > .theme-ui-panel-body {
        padding: clamp(1.35rem, 2.3vw, 2rem) clamp(1.65rem, 2.5vw, 2.35rem) !important;
      }

      @media (max-width: 980px) {
        body[data-app-section="admin"] .dashboard-layout .dashboard-sidebar-backdrop {
          display: block !important;
          position: fixed !important;
          inset: 72px 0 0 !important;
          border: 0 !important;
          background: rgb(15 23 42 / 0.32) !important;
          z-index: 125 !important;
          transition: opacity 0.16s ease !important;
        }

        body[data-app-section="admin"] .dashboard-layout .dashboard-sidebar-backdrop.active {
          opacity: 1 !important;
          pointer-events: auto !important;
        }
      }

      @media (max-width: 760px) {
        body[data-app-section="admin"] .dashboard-layout .admin-content,
        body[data-app-section="admin"] .dashboard-layout .content-body,
        body[data-app-section="admin"] .dashboard-layout .content-wrapper,
        body[data-app-section="admin"] .theme-ui-content {
          padding: 1rem !important;
        }

        body[data-app-section="admin"] .theme-ui-page {
          padding-left: 0 !important;
        }

        body[data-app-section="admin"] .dashboard-layout .card > .card-header,
        body[data-app-section="admin"] .dashboard-layout .card-improved > .card-header,
        body[data-app-section="admin"] .dashboard-layout .card-body:not(.p-0),
        body[data-app-section="admin"] .theme-ui-content > .theme-ui-panel > .theme-ui-panel-body {
          padding-left: 1rem !important;
          padding-right: 1rem !important;
        }
      }
    `;

    container.appendChild(style);
  },

  injectAdminModernDesign(container) {
    if (!container || document.body?.dataset?.appSection !== "admin") {
      return;
    }

    const existing = document.getElementById("admin-modern-design-style");
    if (existing) {
      existing.remove();
    }

    const style = document.createElement("style");
    style.id = "admin-modern-design-style";
    style.textContent = `
      body[data-app-section="admin"] {
        --admin-modern-bg: color-mix(in srgb, var(--color-primary) 7%, #f8faf7 93%);
        --admin-modern-bg-strong: color-mix(in srgb, var(--color-primary) 11%, #ffffff 89%);
        --admin-modern-surface: #ffffff;
        --admin-modern-surface-muted: #f7faf7;
        --admin-modern-line: #dfe7df;
        --admin-modern-line-strong: #cfdcce;
        --admin-modern-heading: color-mix(in srgb, var(--color-primary) 34%, #020617 66%);
        --admin-modern-text: #122033;
        --admin-modern-muted: #5c6a7d;
        --admin-modern-primary: color-mix(in srgb, var(--color-primary) 82%, #082f19 18%);
        --admin-modern-primary-dark: color-mix(in srgb, var(--color-primary) 58%, #052e16 42%);
        --admin-modern-primary-soft: color-mix(in srgb, var(--color-primary) 14%, #ffffff 86%);
        --admin-modern-blue: #155dfc;
        --admin-modern-red: #ef0000;
        --admin-modern-amber: #f59e0b;
        --admin-modern-purple: #8b5cf6;
        --admin-modern-radius: 8px;
        --admin-modern-shadow: 0 10px 26px -22px rgb(15 23 42 / 0.8), 0 2px 8px -6px rgb(15 23 42 / 0.32);
        --admin-modern-topbar-height: 76px;
        --admin-sidebar-width: 304px;
        background: var(--admin-modern-bg) !important;
        color: var(--admin-modern-text) !important;
      }

      body[data-app-section="admin"] #app {
        min-height: 100vh !important;
        background: var(--admin-modern-bg) !important;
      }

      body[data-app-section="admin"] .dashboard-layout,
      body[data-app-section="admin"] .theme-ui-shell {
        min-height: 100vh !important;
        background:
          radial-gradient(circle at 18% -10%, rgb(255 255 255 / 0.82), transparent 34rem),
          linear-gradient(180deg, var(--admin-modern-bg-strong), var(--admin-modern-bg)) !important;
      }

      body[data-app-section="admin"] .dashboard-layout {
        padding-left: var(--admin-sidebar-width) !important;
      }

      body[data-app-section="admin"] .dashboard-layout.sidebar-collapsed {
        padding-left: 0 !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar,
      body[data-app-section="admin"] .theme-ui-sidebar,
      body[data-app-section="admin"][data-admin-sidebar-style="dark"] .dashboard-layout .sidebar,
      body[data-app-section="admin"][data-admin-sidebar-style="dark"] .theme-ui-sidebar {
        width: var(--admin-sidebar-width) !important;
        top: var(--admin-modern-topbar-height) !important;
        height: calc(100vh - var(--admin-modern-topbar-height)) !important;
        padding: 18px 14px 22px !important;
        background: #ffffff !important;
        border-right: 1px solid var(--admin-modern-line) !important;
        box-shadow: 12px 0 24px -26px rgb(15 23 42 / 0.66) !important;
      }

      body[data-app-section="admin"] .dashboard-layout.sidebar-collapsed .sidebar {
        transform: translateX(-106%) !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-nav,
      body[data-app-section="admin"] .theme-ui-sidebar {
        gap: 8px !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-section,
      body[data-app-section="admin"] .theme-ui-sidebar-section {
        margin: 0 0 10px !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-section-toggle,
      body[data-app-section="admin"] .theme-ui-sidebar-toggle {
        min-height: 34px !important;
        padding: 8px 12px !important;
        border-radius: var(--admin-modern-radius) !important;
        color: #314155 !important;
        background: transparent !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-section-title,
      body[data-app-section="admin"] .theme-ui-sidebar-toggle span {
        color: #314155 !important;
        font-size: 0.72rem !important;
        font-weight: 800 !important;
        letter-spacing: 0.06em !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-section-links,
      body[data-app-section="admin"] .theme-ui-sidebar-links {
        gap: 5px !important;
        margin-top: 4px !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-link,
      body[data-app-section="admin"] .theme-ui-sidebar-link {
        display: flex !important;
        align-items: center !important;
        min-height: 40px !important;
        padding: 9px 12px !important;
        border: 1px solid transparent !important;
        border-radius: var(--admin-modern-radius) !important;
        color: #253348 !important;
        font-size: 0.93rem !important;
        font-weight: 500 !important;
        gap: 12px !important;
        box-shadow: none !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-link:hover,
      body[data-app-section="admin"] .theme-ui-sidebar-link:hover,
      body[data-app-section="admin"] .dashboard-layout .sidebar-section-toggle:hover,
      body[data-app-section="admin"] .theme-ui-sidebar-toggle:hover {
        background: var(--admin-modern-primary-soft) !important;
        border-color: color-mix(in srgb, var(--color-primary) 20%, #e5e7eb 80%) !important;
        color: var(--admin-modern-heading) !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-link.active,
      body[data-app-section="admin"] .theme-ui-sidebar-link.active {
        background: color-mix(in srgb, var(--color-primary) 15%, #ffffff 85%) !important;
        border-color: color-mix(in srgb, var(--color-primary) 22%, #dbe7dc 78%) !important;
        color: var(--admin-modern-heading) !important;
        box-shadow: inset 4px 0 0 var(--admin-modern-primary) !important;
      }

      body[data-app-section="admin"] .sidebar-link-icon,
      body[data-app-section="admin"] .theme-ui-link-icon {
        width: 18px !important;
        height: 18px !important;
        flex: 0 0 18px !important;
        display: inline-grid !important;
        place-items: center !important;
        color: #526173 !important;
      }

      body[data-app-section="admin"] .sidebar-link-icon svg,
      body[data-app-section="admin"] .theme-ui-link-icon svg {
        width: 18px !important;
        height: 18px !important;
      }

      body[data-app-section="admin"] .sidebar-link.active .sidebar-link-icon,
      body[data-app-section="admin"] .theme-ui-sidebar-link.active .theme-ui-link-icon {
        color: var(--admin-modern-primary-dark) !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-link-badge,
      body[data-app-section="admin"] .theme-ui-link-badge {
        border-radius: 999px !important;
        background: #ff4d5f !important;
        color: #ffffff !important;
        box-shadow: none !important;
      }

      body[data-app-section="admin"] .dashboard-layout .content-header,
      body[data-app-section="admin"] .dashboard-layout .admin-topbar,
      body[data-app-section="admin"] .theme-ui-topbar {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        flex-wrap: nowrap !important;
        height: var(--admin-modern-topbar-height) !important;
        min-height: var(--admin-modern-topbar-height) !important;
        padding: 0 clamp(1rem, 2vw, 1.65rem) !important;
        background: #ffffff !important;
        border-bottom: 1px solid var(--admin-modern-line) !important;
        box-shadow: 0 10px 22px -24px rgb(15 23 42 / 0.82) !important;
        gap: 1rem !important;
        text-align: left !important;
      }

      body[data-app-section="admin"] .dashboard-layout .content-header > *,
      body[data-app-section="admin"] .dashboard-layout .admin-topbar > * {
        flex-shrink: 0 !important;
      }

      body[data-app-section="admin"] .dashboard-layout .content-header > h1,
      body[data-app-section="admin"] .dashboard-layout .admin-topbar > h1,
      body[data-app-section="admin"] .dashboard-layout .content-header > .page-title,
      body[data-app-section="admin"] .dashboard-layout .admin-topbar > .page-title {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        text-align: left !important;
        margin: 0 !important;
      }

      /* Remove flex-1 spacer elements that center the title */
      body[data-app-section="admin"] .dashboard-layout .content-header > .flex-1,
      body[data-app-section="admin"] .dashboard-layout .admin-topbar > .flex-1 {
        display: none !important;
      }

      body[data-app-section="admin"] .dashboard-layout .content-header h1,
      body[data-app-section="admin"] .dashboard-layout .admin-topbar h1,
      body[data-app-section="admin"] .dashboard-layout .admin-topbar .page-title,
      body[data-app-section="admin"] .theme-ui-topbar h1 {
        color: var(--admin-modern-heading) !important;
        font-size: clamp(1.45rem, 2.2vw, 1.85rem) !important;
        font-weight: 800 !important;
        line-height: 1.15 !important;
        margin: 0 !important;
        flex: 1 1 auto !important;
        text-align: left !important;
        justify-content: flex-start !important;
      }

      body[data-app-section="admin"] .dashboard-layout .content-header > *:first-child,
      body[data-app-section="admin"] .dashboard-layout .admin-topbar > *:first-child {
        display: flex !important;
        align-items: center !important;
        gap: 1rem !important;
        flex: 0 0 auto !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-toggle,
      body[data-app-section="admin"] .theme-ui-sidebar-toggle-btn,
      body[data-app-section="admin"] .theme-ui-menu-btn,
      body[data-app-section="admin"] .theme-ui-close-btn {
        width: 42px !important;
        height: 42px !important;
        min-width: 42px !important;
        padding: 0 !important;
        display: inline-grid !important;
        place-items: center !important;
        border-radius: var(--admin-modern-radius) !important;
        background: #ffffff !important;
        border: 1px solid var(--admin-modern-line) !important;
        color: var(--admin-modern-heading) !important;
        box-shadow: 0 6px 16px -14px rgb(15 23 42 / 0.9) !important;
      }

      body[data-app-section="admin"] .dashboard-layout .main-content {
        padding-top: var(--admin-modern-topbar-height) !important;
        min-width: 0 !important;
      }

      body[data-app-section="admin"] .dashboard-layout .admin-content,
      body[data-app-section="admin"] .dashboard-layout .content-body,
      body[data-app-section="admin"] .dashboard-layout .content-wrapper,
      body[data-app-section="admin"] .theme-ui-content {
        width: 100% !important;
        max-width: 1460px !important;
        margin: 0 auto !important;
        padding: clamp(1.55rem, 2.5vw, 2.35rem) clamp(1.1rem, 2.5vw, 2.25rem) !important;
      }

      body[data-app-section="admin"] .theme-ui-page {
        padding-left: var(--admin-sidebar-width) !important;
      }

      body[data-app-section="admin"] .theme-ui-shell.settings-sidebar-collapsed .theme-ui-page {
        padding-left: 0 !important;
      }

      body[data-app-section="admin"] .admin-panel-toolbar,
      body[data-app-section="admin"] .dashboard-section-header {
        display: flex !important;
        align-items: flex-end !important;
        justify-content: space-between !important;
        gap: 1rem !important;
        margin-bottom: 1.4rem !important;
      }

      body[data-app-section="admin"] .admin-panel-toolbar h2,
      body[data-app-section="admin"] .content-body > h2,
      body[data-app-section="admin"] .content-wrapper > h2 {
        color: #031126 !important;
        font-size: clamp(1.7rem, 2.8vw, 2.2rem) !important;
        line-height: 1.12 !important;
        font-weight: 800 !important;
        margin: 0 !important;
      }

      body[data-app-section="admin"] .admin-panel-toolbar p,
      body[data-app-section="admin"] .text-secondary,
      body[data-app-section="admin"] .text-muted {
        color: var(--admin-modern-muted) !important;
      }

      body[data-app-section="admin"] .card,
      body[data-app-section="admin"] .card-improved,
      body[data-app-section="admin"] .stat-card,
      body[data-app-section="admin"] .stat-card-improved,
      body[data-app-section="admin"] .analytics-card,
      body[data-app-section="admin"] .admin-panel,
      body[data-app-section="admin"] .theme-ui-panel,
      body[data-app-section="admin"] .field-type-card {
        background: var(--admin-modern-surface) !important;
        border: 1px solid var(--admin-modern-line) !important;
        border-radius: var(--admin-modern-radius) !important;
        box-shadow: var(--admin-modern-shadow) !important;
      }

      body[data-app-section="admin"] .card-header,
      body[data-app-section="admin"] .card-improved .card-header,
      body[data-app-section="admin"] .admin-panel-header,
      body[data-app-section="admin"] .theme-ui-panel-header {
        background: #ffffff !important;
        border-bottom: 1px solid var(--admin-modern-line) !important;
        padding: 1rem 1.25rem !important;
        min-height: 4rem !important;
      }

      body[data-app-section="admin"] .card-body,
      body[data-app-section="admin"] .card-improved .card-body,
      body[data-app-section="admin"] .admin-panel-body,
      body[data-app-section="admin"] .theme-ui-panel-body {
        padding: 1.2rem 1.25rem !important;
      }

      body[data-app-section="admin"] .card-title,
      body[data-app-section="admin"] .card-header h2,
      body[data-app-section="admin"] .card-header h3,
      body[data-app-section="admin"] .admin-panel-title,
      body[data-app-section="admin"] .theme-ui-panel-title {
        color: #031126 !important;
        font-size: 1.06rem !important;
        font-weight: 800 !important;
      }

      body[data-app-section="admin"] .stat-card-improved {
        min-height: 118px !important;
        align-items: center !important;
      }

      body[data-app-section="admin"] .stat-card-improved .stat-icon {
        width: 42px !important;
        height: 42px !important;
        border-radius: 10px !important;
        box-shadow: none !important;
      }

      body[data-app-section="admin"] .stat-card-improved .stat-value,
      body[data-app-section="admin"] .analytics-card-value {
        color: var(--admin-modern-primary) !important;
        font-size: clamp(1.65rem, 2.5vw, 2.2rem) !important;
        font-weight: 800 !important;
      }

      body[data-app-section="admin"] .stat-card-improved .stat-label,
      body[data-app-section="admin"] .analytics-card-label {
        color: #526173 !important;
        font-weight: 600 !important;
      }

      body[data-app-section="admin"] .grid,
      body[data-app-section="admin"] .stats-grid,
      body[data-app-section="admin"] .dashboard-charts-grid {
        gap: 1rem !important;
      }

      body[data-app-section="admin"] .form-input,
      body[data-app-section="admin"] .form-select,
      body[data-app-section="admin"] .form-textarea,
      body[data-app-section="admin"] input[type="text"],
      body[data-app-section="admin"] input[type="email"],
      body[data-app-section="admin"] input[type="password"],
      body[data-app-section="admin"] input[type="number"],
      body[data-app-section="admin"] input[type="date"],
      body[data-app-section="admin"] input[type="month"],
      body[data-app-section="admin"] input[type="time"],
      body[data-app-section="admin"] select,
      body[data-app-section="admin"] textarea {
        min-height: 42px !important;
        border: 1px solid #ccd6d0 !important;
        border-radius: var(--admin-modern-radius) !important;
        background: #ffffff !important;
        color: #031126 !important;
        box-shadow: none !important;
        font-size: 0.95rem !important;
      }

      body[data-app-section="admin"] .form-input:focus,
      body[data-app-section="admin"] .form-select:focus,
      body[data-app-section="admin"] .form-textarea:focus,
      body[data-app-section="admin"] input:focus,
      body[data-app-section="admin"] select:focus,
      body[data-app-section="admin"] textarea:focus {
        border-color: color-mix(in srgb, var(--color-primary) 42%, #94a3b8 58%) !important;
        box-shadow: 0 0 0 3px color-mix(in srgb, var(--color-primary) 14%, transparent 86%) !important;
        outline: none !important;
      }

      body[data-app-section="admin"] .form-label {
        color: #253348 !important;
        font-weight: 650 !important;
      }

      body[data-app-section="admin"] .btn,
      body[data-app-section="admin"] .btn-icon,
      body[data-app-section="admin"] button {
        border-radius: var(--admin-modern-radius) !important;
        font-weight: 700 !important;
      }

      body[data-app-section="admin"] .btn-primary {
        background: var(--admin-modern-primary-dark) !important;
        border-color: var(--admin-modern-primary-dark) !important;
        color: #ffffff !important;
        box-shadow: 0 10px 18px -16px color-mix(in srgb, var(--color-primary) 62%, #000 38%) !important;
      }

      body[data-app-section="admin"] .btn-primary:hover {
        background: var(--admin-modern-primary) !important;
        border-color: var(--admin-modern-primary) !important;
      }

      body[data-app-section="admin"] .btn-danger,
      body[data-app-section="admin"] .admin-shell-logout,
      body[data-app-section="admin"] .theme-ui-logout-btn {
        background: var(--admin-modern-red) !important;
        border-color: var(--admin-modern-red) !important;
        color: #ffffff !important;
        min-height: 40px !important;
        padding: 0 1rem !important;
      }

      body[data-app-section="admin"] .btn-secondary,
      body[data-app-section="admin"] .btn-ghost {
        border-color: #cbd5d1 !important;
        background: #ffffff !important;
        color: #253348 !important;
      }

      body[data-app-section="admin"] .admin-shell-user,
      body[data-app-section="admin"] .theme-ui-user {
        display: flex !important;
        align-items: center !important;
        gap: 1rem !important;
        margin-left: auto !important;
        flex: 0 0 auto !important;
      }

      body[data-app-section="admin"] .topbar-actions {
        display: flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
        margin-left: auto !important;
        flex: 0 0 auto !important;
      }

      body[data-app-section="admin"] .dashboard-layout .content-header,
      body[data-app-section="admin"] .dashboard-layout .admin-topbar {
        display: flex !important;
        flex-wrap: nowrap !important;
      }

      body[data-app-section="admin"] .dashboard-layout .content-header > .sidebar-toggle,
      body[data-app-section="admin"] .dashboard-layout .admin-topbar > .sidebar-toggle {
        order: -1 !important;
        flex: 0 0 auto !important;
      }

      body[data-app-section="admin"] .dashboard-layout .content-header > h1,
      body[data-app-section="admin"] .dashboard-layout .admin-topbar > h1,
      body[data-app-section="admin"] .dashboard-layout .content-header > .page-title,
      body[data-app-section="admin"] .dashboard-layout .admin-topbar > .page-title {
        order: 0 !important;
        flex: 1 1 auto !important;
        min-width: 0 !important;
      }

      body[data-app-section="admin"] .dashboard-layout .topbar-actions {
        order: 10 !important;
        margin-left: auto !important;
        flex: 0 0 auto !important;
      }

      body[data-app-section="admin"] .dashboard-layout .admin-shell-user {
        order: 20 !important;
        margin-left: 0 !important;
        flex: 0 0 auto !important;
        display: flex !important;
        align-items: center !important;
        gap: 1rem !important;
      }

      /* When there's NO topbar-actions, push logout to the right */
      body[data-app-section="admin"] .dashboard-layout .content-header:not(:has(.topbar-actions)):not(:has(#addFieldBtn)) .admin-shell-user,
      body[data-app-section="admin"] .dashboard-layout .admin-topbar:not(:has(.topbar-actions)) .admin-shell-user {
        margin-left: auto !important;
      }

      /* Special case for #addFieldBtn which is not in .topbar-actions */
      body[data-app-section="admin"] .dashboard-layout .content-header > #addFieldBtn {
        order: 10 !important;
        margin-left: auto !important;
        flex: 0 0 auto !important;
      }

      /* When #addFieldBtn exists, ensure logout stays on the right */
      body[data-app-section="admin"] .dashboard-layout .content-header:has(#addFieldBtn) .admin-shell-user {
        margin-left: 0 !important;
        order: 20 !important;
      }

      /* Settings pages (theme-ui-topbar) layout */
      body[data-app-section="admin"] .theme-ui-topbar {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        flex-wrap: nowrap !important;
        gap: 1rem !important;
      }

      body[data-app-section="admin"] .theme-ui-topbar-left {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        display: flex !important;
        align-items: center !important;
        gap: 1rem !important;
      }

      body[data-app-section="admin"] .theme-ui-topbar-left h1 {
        flex: 1 1 auto !important;
        min-width: 0 !important;
        margin: 0 !important;
      }

      body[data-app-section="admin"] .theme-ui-topbar-right,
      body[data-app-section="admin"] .theme-ui-user {
        flex: 0 0 auto !important;
        margin-left: auto !important;
        display: flex !important;
        align-items: center !important;
        gap: 1rem !important;
      }

      body[data-app-section="admin"] .theme-ui-topbar .theme-ui-sidebar-toggle-btn {
        order: -1 !important;
        flex: 0 0 auto !important;
      }

      body[data-app-section="admin"] .admin-shell-user-meta,
      body[data-app-section="admin"] .theme-ui-user-meta {
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-end !important;
        gap: 0.125rem !important;
      }

      body[data-app-section="admin"] .admin-shell-user-name,
      body[data-app-section="admin"] .theme-ui-user-name {
        color: #031126 !important;
        font-weight: 700 !important;
        font-size: 0.9rem !important;
        line-height: 1.2 !important;
        white-space: nowrap !important;
      }

      body[data-app-section="admin"] .admin-shell-user-role,
      body[data-app-section="admin"] .theme-ui-user-role {
        color: #5c6a7d !important;
        font-size: 0.8rem !important;
        line-height: 1.2 !important;
        white-space: nowrap !important;
      }

      body[data-app-section="admin"] .admin-shell-logout,
      body[data-app-section="admin"] .theme-ui-logout-btn {
        white-space: nowrap !important;
      }

      body[data-app-section="admin"] .table-container,
      body[data-app-section="admin"] .table-responsive,
      body[data-app-section="admin"] .card-body.p-0,
      body[data-app-section="admin"] div[style*="overflow-x: auto"] {
        border-radius: var(--admin-modern-radius) !important;
        border: 1px solid var(--admin-modern-line) !important;
        background: #ffffff !important;
        box-shadow: var(--admin-modern-shadow) !important;
      }

      body[data-app-section="admin"] .card-body.p-0 > div[style*="overflow-x: auto"],
      body[data-app-section="admin"] .card-body div[style*="overflow-x: auto"] {
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
      }

      body[data-app-section="admin"] .table,
      body[data-app-section="admin"] .data-table,
      body[data-app-section="admin"] .table-improved {
        width: 100% !important;
        min-width: 760px !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        background: #ffffff !important;
      }

      body[data-app-section="admin"] .table thead th,
      body[data-app-section="admin"] .data-table thead th,
      body[data-app-section="admin"] .table-improved thead th {
        background: var(--admin-modern-primary-dark) !important;
        color: #ffffff !important;
        border-bottom: 0 !important;
        padding: 0.9rem 1rem !important;
        font-size: 0.74rem !important;
        font-weight: 800 !important;
        letter-spacing: 0.03em !important;
        text-transform: uppercase !important;
        white-space: nowrap !important;
      }

      body[data-app-section="admin"] .table tbody td,
      body[data-app-section="admin"] .data-table tbody td,
      body[data-app-section="admin"] .table-improved tbody td {
        padding: 0.85rem 1rem !important;
        border-bottom: 1px solid #e5ebe7 !important;
        color: #122033 !important;
        vertical-align: middle !important;
      }

      body[data-app-section="admin"] .table tbody tr:hover,
      body[data-app-section="admin"] .data-table tbody tr:hover,
      body[data-app-section="admin"] .table-improved tbody tr:hover {
        background: var(--admin-modern-primary-soft) !important;
        box-shadow: inset 4px 0 0 var(--admin-modern-primary) !important;
      }

      body[data-app-section="admin"] .badge,
      body[data-app-section="admin"] .status-badge,
      body[data-app-section="admin"] .points-badge {
        border-radius: 999px !important;
        padding: 0.25rem 0.62rem !important;
        font-weight: 700 !important;
        line-height: 1.1 !important;
      }

      body[data-app-section="admin"] .tabs,
      body[data-app-section="admin"] .tabs-improved {
        background: #ffffff !important;
        border: 1px solid var(--admin-modern-line) !important;
        border-radius: var(--admin-modern-radius) !important;
        padding: 0.38rem !important;
        gap: 0.35rem !important;
        box-shadow: var(--admin-modern-shadow) !important;
      }

      body[data-app-section="admin"] .tabs .tab-btn,
      body[data-app-section="admin"] .tabs-improved .tab-btn {
        border: 0 !important;
        background: #f4f6f5 !important;
        color: #253348 !important;
        border-radius: var(--admin-modern-radius) !important;
        min-height: 40px !important;
        padding: 0.55rem 0.95rem !important;
      }

      body[data-app-section="admin"] .tabs .tab-btn.active,
      body[data-app-section="admin"] .tabs-improved .tab-btn.active {
        background: var(--admin-modern-primary-dark) !important;
        color: #ffffff !important;
      }

      body[data-app-section="admin"] .modal,
      body[data-app-section="admin"] .modal-improved {
        position: fixed !important;
        inset: 0 !important;
        z-index: 3000 !important;
        display: none !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 1rem !important;
      }

      body[data-app-section="admin"] .modal.active,
      body[data-app-section="admin"] .modal[style*="display: block"],
      body[data-app-section="admin"] .modal[style*="display: flex"],
      body[data-app-section="admin"] .modal-improved[style*="display: block"],
      body[data-app-section="admin"] .modal-improved[style*="display: flex"],
      body[data-app-section="admin"] .modal-improved.active {
        display: flex !important;
      }

      body[data-app-section="admin"] .modal-overlay,
      body[data-app-section="admin"] .modal-backdrop,
      body[data-app-section="admin"] .modal-improved .modal-backdrop {
        position: absolute !important;
        inset: 0 !important;
        z-index: 1 !important;
        background: rgb(15 23 42 / 0.56) !important;
        backdrop-filter: blur(2px) !important;
        pointer-events: auto !important;
      }

      body[data-app-section="admin"] .modal-content,
      body[data-app-section="admin"] .modal-improved .modal-content {
        position: relative !important;
        z-index: 10 !important;
        width: min(100%, 760px) !important;
        max-height: min(88vh, 900px) !important;
        overflow-y: auto !important;
        border: 1px solid var(--admin-modern-line) !important;
        border-radius: var(--admin-modern-radius) !important;
        background: #ffffff !important;
        box-shadow: 0 24px 70px -30px rgb(15 23 42 / 0.72) !important;
        pointer-events: auto !important;
      }

      body[data-app-section="admin"] .modal-dialog {
        position: relative !important;
        width: min(94vw, 760px) !important;
        max-height: 90vh !important;
        z-index: 10 !important;
        pointer-events: none !important;
      }

      body[data-app-section="admin"] .modal-dialog > * {
        pointer-events: auto !important;
      }

      body[data-app-section="admin"] .modal-dialog.modal-lg {
        width: min(94vw, 960px) !important;
      }

      body[data-app-section="admin"] .alert,
      body[data-app-section="admin"] .info-box,
      body[data-app-section="admin"] .theme-info-box {
        border-radius: var(--admin-modern-radius) !important;
      }

      body[data-app-section="admin"] .field-type-card,
      body[data-app-section="admin"] .field-item,
      body[data-app-section="admin"] .reward-card,
      body[data-app-section="admin"] .announcement-card {
        border-radius: var(--admin-modern-radius) !important;
        background: #ffffff !important;
        border: 1px solid var(--admin-modern-line) !important;
      }

      body[data-app-section="admin"] .admin-page-title-block,
      body[data-app-section="admin"] .dashboard-hero {
        margin-bottom: 1.35rem !important;
      }

      body[data-app-section="admin"] .admin-page-title-block h2,
      body[data-app-section="admin"] .dashboard-hero h2 {
        color: #031126 !important;
        font-size: clamp(1.85rem, 3vw, 2.35rem) !important;
        font-weight: 850 !important;
        line-height: 1.08 !important;
        margin: 0 0 0.35rem !important;
      }

      body[data-app-section="admin"] .admin-page-title-block p,
      body[data-app-section="admin"] .dashboard-hero p {
        color: var(--admin-modern-muted) !important;
        margin: 0 !important;
        font-size: 1rem !important;
      }

      body[data-app-section="admin"] .admin-filter-card,
      body[data-app-section="admin"] .content-management-card,
      body[data-app-section="admin"] .email-management-card,
      body[data-app-section="admin"] .form-builder-board {
        background: #ffffff !important;
        border: 1px solid var(--admin-modern-line) !important;
        border-radius: var(--admin-modern-radius) !important;
        box-shadow: var(--admin-modern-shadow) !important;
        padding: clamp(1rem, 2vw, 1.6rem) !important;
      }

      body[data-app-section="admin"] .admin-filter-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(220px, 1fr)) !important;
        gap: 1rem !important;
        align-items: end !important;
      }

      body[data-app-section="admin"] .admin-filter-card .form-group,
      body[data-app-section="admin"] .admin-filter-grid > .form-group {
        display: grid !important;
        width: auto !important;
        min-width: 0 !important;
        margin: 0 !important;
        grid-column: auto !important;
      }

      body[data-app-section="admin"] .admin-filter-card .form-input,
      body[data-app-section="admin"] .admin-filter-card select {
        width: 100% !important;
      }

      body[data-app-section="admin"] .admin-actions-row {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: 0.65rem !important;
        margin-top: 1rem !important;
      }

      body[data-app-section="admin"] .admin-record-table-shell {
        overflow-x: auto !important;
        background: #ffffff !important;
        border: 1px solid var(--admin-modern-line) !important;
        border-radius: var(--admin-modern-radius) !important;
        box-shadow: var(--admin-modern-shadow) !important;
      }

      body[data-app-section="admin"] .admin-record-table-shell .card-header {
        box-shadow: none !important;
      }

      body[data-app-section="admin"] .settings-tabbar,
      body[data-app-section="admin"] .email-tabbar {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: 0.5rem !important;
        margin-bottom: 1.05rem !important;
      }

      body[data-app-section="admin"] .settings-tabbar .tab-btn,
      body[data-app-section="admin"] .email-tabbar .tab-btn {
        min-height: 42px !important;
        padding: 0.55rem 1rem !important;
        border: 0 !important;
        background: #f3f5f4 !important;
        color: #253348 !important;
      }

      body[data-app-section="admin"] .settings-tabbar .tab-btn.active,
      body[data-app-section="admin"] .email-tabbar .tab-btn.active {
        background: var(--admin-modern-primary-dark) !important;
        color: #ffffff !important;
      }

      body[data-app-section="admin"] .settings-info-panel,
      body[data-app-section="admin"] .email-info-panel {
        border: 1px solid color-mix(in srgb, var(--color-primary) 22%, #cbd5e1 78%) !important;
        background: color-mix(in srgb, var(--color-primary) 6%, #ffffff 94%) !important;
        border-radius: var(--admin-modern-radius) !important;
        color: #1f3326 !important;
        padding: 1rem 1.15rem !important;
        margin-bottom: 1.4rem !important;
      }

      body[data-app-section="admin"] .settings-section-title,
      body[data-app-section="admin"] .email-section-title {
        display: flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        color: #031126 !important;
        font-size: 1.12rem !important;
        font-weight: 800 !important;
        margin: 1.35rem 0 0.9rem !important;
        padding-top: 1.15rem !important;
        border-top: 1px solid var(--admin-modern-line) !important;
      }

      body[data-app-section="admin"] .settings-section-title:first-child,
      body[data-app-section="admin"] .email-section-title:first-child {
        border-top: 0 !important;
        padding-top: 0 !important;
        margin-top: 0 !important;
      }

      body[data-app-section="admin"] .settings-form-grid,
      body[data-app-section="admin"] .email-settings-grid {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        gap: 1.25rem 1.5rem !important;
      }

      body[data-app-section="admin"] .settings-form-grid .form-group,
      body[data-app-section="admin"] .email-settings-grid .form-group {
        display: flex !important;
        flex-direction: column !important;
        gap: 0.5rem !important;
      }

      body[data-app-section="admin"] .settings-form-grid .form-group.col-span-2,
      body[data-app-section="admin"] .email-settings-grid .form-group.col-span-2 {
        grid-column: span 2 !important;
      }

      body[data-app-section="admin"] .email-template-layout {
        display: grid !important;
        grid-template-columns: 280px minmax(0, 1fr) !important;
        gap: 1.5rem !important;
        align-items: start !important;
      }

      body[data-app-section="admin"] .field-section-grid {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
        gap: 1rem !important;
      }

      body[data-app-section="admin"] .form-builder-board > .card-header {
        background: var(--admin-modern-primary-dark) !important;
        border-bottom-color: var(--admin-modern-primary-dark) !important;
        color: #ffffff !important;
      }

      body[data-app-section="admin"] .form-builder-board > .card-header .card-title,
      body[data-app-section="admin"] .form-builder-board > .card-header p,
      body[data-app-section="admin"] .form-builder-board > .card-header .text-secondary {
        color: #ffffff !important;
      }

      body[data-app-section="admin"] .form-builder-board > .card-header .badge {
        background: rgb(255 255 255 / 0.18) !important;
        color: #ffffff !important;
      }

      body[data-app-section="admin"] .field-section-card {
        background: #f8faf9 !important;
        border: 1px solid #e8eee9 !important;
        border-radius: var(--admin-modern-radius) !important;
        padding: 1rem !important;
        min-height: 300px !important;
      }

      body[data-app-section="admin"] .field-section-card h3 {
        color: #031126 !important;
        font-size: 1rem !important;
        font-weight: 800 !important;
        margin: 0 0 0.85rem !important;
        padding-bottom: 0.75rem !important;
        border-bottom: 1px solid color-mix(in srgb, var(--color-primary) 24%, #e5e7eb 76%) !important;
      }

      body[data-app-section="admin"] .field-toggle-row {
        display: grid !important;
        grid-template-columns: 1fr auto auto auto !important;
        align-items: center !important;
        gap: 0.55rem !important;
        padding: 0.48rem 0 !important;
        color: #253348 !important;
        font-size: 0.9rem !important;
      }

      body[data-app-section="admin"] .field-toggle {
        width: 42px !important;
        height: 22px !important;
        border-radius: 999px !important;
        border: 0 !important;
        background: #cfd6dd !important;
        position: relative !important;
      }

      body[data-app-section="admin"] .field-toggle::after {
        content: "" !important;
        position: absolute !important;
        width: 18px !important;
        height: 18px !important;
        top: 2px !important;
        left: 2px !important;
        border-radius: 999px !important;
        background: #ffffff !important;
        transition: transform 0.18s ease !important;
      }

      body[data-app-section="admin"] .field-toggle.is-active {
        background: var(--admin-modern-primary-dark) !important;
      }

      body[data-app-section="admin"] .field-toggle.is-active::after {
        transform: translateX(20px) !important;
      }

      body[data-app-section="admin"] .dashboard-insight-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 1rem !important;
      }

      @media (max-width: 980px) {
        body[data-app-section="admin"] {
          --admin-sidebar-width: min(82vw, 304px);
          --admin-modern-topbar-height: 72px;
        }

        body[data-app-section="admin"] .dashboard-layout,
        body[data-app-section="admin"] .theme-ui-page {
          padding-left: 0 !important;
        }

        body[data-app-section="admin"] .dashboard-layout .sidebar,
        body[data-app-section="admin"] .theme-ui-sidebar {
          transform: translateX(-106%) !important;
        }

        body[data-app-section="admin"] .dashboard-layout .sidebar.open,
        body[data-app-section="admin"] .theme-ui-sidebar.open {
          transform: translateX(0) !important;
        }

        body[data-app-section="admin"] .dashboard-layout .admin-content,
        body[data-app-section="admin"] .dashboard-layout .content-body,
        body[data-app-section="admin"] .dashboard-layout .content-wrapper,
        body[data-app-section="admin"] .theme-ui-content {
          padding: 1rem !important;
        }

        body[data-app-section="admin"] .admin-shell-user-meta,
        body[data-app-section="admin"] .theme-ui-user-meta {
          display: none !important;
        }

        body[data-app-section="admin"] .dashboard-layout .content-header,
        body[data-app-section="admin"] .dashboard-layout .admin-topbar {
          flex-wrap: wrap !important;
          gap: 0.75rem !important;
        }

        body[data-app-section="admin"] .dashboard-layout .topbar-actions {
          gap: 0.5rem !important;
        }

        body[data-app-section="admin"] .dashboard-layout .content-header > h1,
        body[data-app-section="admin"] .dashboard-layout .admin-topbar > h1,
        body[data-app-section="admin"] .dashboard-layout .content-header > .page-title,
        body[data-app-section="admin"] .dashboard-layout .admin-topbar > .page-title {
          flex: 1 1 100% !important;
          order: 1 !important;
        }

        body[data-app-section="admin"] .dashboard-layout .sidebar-toggle {
          order: 0 !important;
        }

        body[data-app-section="admin"] .dashboard-layout .topbar-actions {
          order: 2 !important;
        }

        body[data-app-section="admin"] .dashboard-layout .admin-shell-user {
          order: 3 !important;
        }

        body[data-app-section="admin"] .admin-filter-grid,
        body[data-app-section="admin"] .settings-form-grid,
        body[data-app-section="admin"] .email-settings-grid,
        body[data-app-section="admin"] .email-template-layout,
        body[data-app-section="admin"] .field-section-grid,
        body[data-app-section="admin"] .dashboard-insight-grid {
          grid-template-columns: 1fr !important;
        }
      }

      @media (max-width: 680px) {
        body[data-app-section="admin"] .grid-cols-4,
        body[data-app-section="admin"] .grid-cols-3,
        body[data-app-section="admin"] .grid-cols-2,
        body[data-app-section="admin"] .stats-grid {
          grid-template-columns: 1fr !important;
        }

        body[data-app-section="admin"] .admin-panel-toolbar,
        body[data-app-section="admin"] .dashboard-section-header,
        body[data-app-section="admin"] .card-header,
        body[data-app-section="admin"] .card-improved .card-header {
          align-items: flex-start !important;
          flex-direction: column !important;
        }

        body[data-app-section="admin"] .topbar-actions .btn {
          padding-left: 0.75rem !important;
          padding-right: 0.75rem !important;
        }
      }

      /* Final admin shell pass: fixed headers, calmer data grids, and mobile table cards. */
      body[data-app-section="admin"] .dashboard-layout,
      body[data-app-section="admin"] .theme-ui-shell {
        min-width: 0 !important;
        overflow-x: clip !important;
      }

      body[data-app-section="admin"] .dashboard-layout .content-header,
      body[data-app-section="admin"] .dashboard-layout .admin-topbar,
      body[data-app-section="admin"] .theme-ui-topbar {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        z-index: 1500 !important;
        width: auto !important;
        max-width: none !important;
        overflow: hidden !important;
      }

      body[data-app-section="admin"] .theme-ui-shell {
        padding-top: var(--admin-modern-topbar-height) !important;
      }

      body[data-app-section="admin"] .dashboard-layout .main-content {
        width: 100% !important;
        min-width: 0 !important;
      }

      body[data-app-section="admin"] .theme-ui-page {
        min-height: calc(100vh - var(--admin-modern-topbar-height)) !important;
      }

      body[data-app-section="admin"] .dashboard-sidebar-backdrop,
      body[data-app-section="admin"] .theme-ui-sidebar-backdrop {
        top: var(--admin-modern-topbar-height) !important;
      }

      body[data-app-section="admin"] .dashboard-layout .admin-content,
      body[data-app-section="admin"] .dashboard-layout .content-body,
      body[data-app-section="admin"] .dashboard-layout .content-wrapper,
      body[data-app-section="admin"] .theme-ui-content {
        min-width: 0 !important;
      }

      body[data-app-section="admin"] .card > .card-header,
      body[data-app-section="admin"] .card-improved > .card-header,
      body[data-app-section="admin"] .admin-panel-header,
      body[data-app-section="admin"] .theme-ui-panel-header {
        padding: clamp(1.1rem, 1.8vw, 1.45rem) clamp(1.25rem, 2.3vw, 1.8rem) !important;
        min-height: 4.1rem !important;
      }

      body[data-app-section="admin"] .card > .card-body:not(.p-0),
      body[data-app-section="admin"] .card-improved > .card-body:not(.p-0),
      body[data-app-section="admin"] .admin-panel-body,
      body[data-app-section="admin"] .theme-ui-panel-body {
        padding: clamp(1.05rem, 1.9vw, 1.45rem) clamp(1.25rem, 2.3vw, 1.8rem) !important;
      }

      body[data-app-section="admin"] .card > .card-body.p-0:has(table),
      body[data-app-section="admin"] .card-improved > .card-body.p-0:has(table),
      body[data-app-section="admin"] .card > .card-body.p-0:has(.admin-table-scroll),
      body[data-app-section="admin"] .card-improved > .card-body.p-0:has(.admin-table-scroll),
      body[data-app-section="admin"] .card > .card-body.p-0:has(.table-responsive),
      body[data-app-section="admin"] .card-improved > .card-body.p-0:has(.table-responsive),
      body[data-app-section="admin"] .card > .card-body.p-0:has(.id-card-results-table-wrap),
      body[data-app-section="admin"] .card-improved > .card-body.p-0:has(.id-card-results-table-wrap) {
        padding: clamp(0.95rem, 1.6vw, 1.2rem) clamp(1.25rem, 2.4vw, 1.8rem) clamp(1.05rem, 1.8vw, 1.35rem) !important;
      }

      body[data-app-section="admin"] .dashboard-layout .content-header h1,
      body[data-app-section="admin"] .dashboard-layout .admin-topbar h1,
      body[data-app-section="admin"] .dashboard-layout .admin-topbar .page-title,
      body[data-app-section="admin"] .theme-ui-topbar h1 {
        min-width: 0 !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
      }

      body[data-app-section="admin"] .dashboard-layout .topbar-actions,
      body[data-app-section="admin"] .dashboard-layout .admin-shell-user,
      body[data-app-section="admin"] .theme-ui-topbar-right,
      body[data-app-section="admin"] .theme-ui-user {
        min-width: 0 !important;
      }

      body[data-app-section="admin"] .admin-table-scroll,
      body[data-app-section="admin"] .table-container,
      body[data-app-section="admin"] .table-responsive,
      body[data-app-section="admin"] .admin-record-table-shell,
      body[data-app-section="admin"] .card-body.p-0:has(> table),
      body[data-app-section="admin"] div[style*="overflow-x: auto"] {
        width: 100% !important;
        max-width: 100% !important;
        overflow-x: auto !important;
        overflow-y: hidden !important;
        -webkit-overflow-scrolling: touch !important;
        border: 1px solid var(--admin-modern-line) !important;
        border-radius: var(--admin-modern-radius) !important;
        background: #ffffff !important;
        box-shadow: var(--admin-modern-shadow) !important;
      }

      body[data-app-section="admin"] .admin-record-table-shell {
        overflow: hidden !important;
      }

      body[data-app-section="admin"] .admin-record-table-shell > .card-header {
        padding-left: clamp(1.15rem, 2vw, 1.65rem) !important;
        padding-right: clamp(1.15rem, 2vw, 1.65rem) !important;
      }

      body[data-app-section="admin"] .admin-record-table-shell > .admin-table-scroll,
      body[data-app-section="admin"] .admin-record-table-shell > .table-container,
      body[data-app-section="admin"] .admin-record-table-shell > .table-responsive,
      body[data-app-section="admin"] .admin-record-table-shell > div[id$="Table"] {
        margin: 0 !important;
        padding: clamp(0.85rem, 1.5vw, 1.15rem) clamp(1rem, 2vw, 1.45rem) !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
      }

      body[data-app-section="admin"] .admin-record-table-shell > div[id$="Table"] > .admin-table-scroll,
      body[data-app-section="admin"] .admin-record-table-shell > div[id$="Table"] > .table-responsive,
      body[data-app-section="admin"] .admin-record-table-shell > div[id$="Table"] > .table-container,
      body[data-app-section="admin"] .card-body.p-0 .admin-table-scroll,
      body[data-app-section="admin"] .card-body.p-0 .table-responsive,
      body[data-app-section="admin"] .card-body.p-0 .id-card-results-table-wrap {
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
      }

      body[data-app-section="admin"] .admin-record-table-shell > .card-footer {
        padding: clamp(0.85rem, 1.6vw, 1.1rem) clamp(1rem, 2vw, 1.45rem) !important;
        gap: 0.75rem !important;
        flex-wrap: wrap !important;
      }

      body[data-app-section="admin"] .admin-table-scroll::-webkit-scrollbar,
      body[data-app-section="admin"] .table-container::-webkit-scrollbar,
      body[data-app-section="admin"] .table-responsive::-webkit-scrollbar,
      body[data-app-section="admin"] .admin-record-table-shell::-webkit-scrollbar {
        height: 10px !important;
      }

      body[data-app-section="admin"] .admin-table-scroll::-webkit-scrollbar-track,
      body[data-app-section="admin"] .table-container::-webkit-scrollbar-track,
      body[data-app-section="admin"] .table-responsive::-webkit-scrollbar-track,
      body[data-app-section="admin"] .admin-record-table-shell::-webkit-scrollbar-track {
        background: #eef3f0 !important;
      }

      body[data-app-section="admin"] .admin-table-scroll::-webkit-scrollbar-thumb,
      body[data-app-section="admin"] .table-container::-webkit-scrollbar-thumb,
      body[data-app-section="admin"] .table-responsive::-webkit-scrollbar-thumb,
      body[data-app-section="admin"] .admin-record-table-shell::-webkit-scrollbar-thumb {
        background: #bac8c0 !important;
        border-radius: 999px !important;
        border: 2px solid #eef3f0 !important;
      }

      body[data-app-section="admin"] .admin-responsive-table,
      body[data-app-section="admin"] .table,
      body[data-app-section="admin"] .data-table,
      body[data-app-section="admin"] .table-improved,
      body[data-app-section="admin"] .compact-table {
        width: 100% !important;
        min-width: 920px !important;
        table-layout: auto !important;
        border-collapse: separate !important;
        border-spacing: 0 !important;
        background: #ffffff !important;
      }

      body[data-app-section="admin"] .admin-responsive-table thead th,
      body[data-app-section="admin"] .table thead th,
      body[data-app-section="admin"] .data-table thead th,
      body[data-app-section="admin"] .table-improved thead th,
      body[data-app-section="admin"] .compact-table thead th {
        position: sticky !important;
        top: 0 !important;
        z-index: 2 !important;
        background: #f3f7f5 !important;
        color: #263244 !important;
        border-bottom: 1px solid #d8e3dd !important;
        padding: 0.9rem 1.15rem !important;
        font-size: 0.72rem !important;
        font-weight: 800 !important;
        letter-spacing: 0.04em !important;
        text-transform: uppercase !important;
        white-space: nowrap !important;
      }

      body[data-app-section="admin"] .admin-responsive-table tbody td,
      body[data-app-section="admin"] .table tbody td,
      body[data-app-section="admin"] .data-table tbody td,
      body[data-app-section="admin"] .table-improved tbody td,
      body[data-app-section="admin"] .compact-table tbody td {
        padding: 0.95rem 1.15rem !important;
        border-bottom: 1px solid #e7eee9 !important;
        color: #182334 !important;
        line-height: 1.42 !important;
        vertical-align: middle !important;
        white-space: normal !important;
        overflow-wrap: anywhere !important;
        word-break: normal !important;
        max-width: 24rem !important;
      }

      body[data-app-section="admin"] .admin-responsive-table th:first-child,
      body[data-app-section="admin"] .admin-responsive-table td:first-child,
      body[data-app-section="admin"] .table th:first-child,
      body[data-app-section="admin"] .table td:first-child,
      body[data-app-section="admin"] .data-table th:first-child,
      body[data-app-section="admin"] .data-table td:first-child,
      body[data-app-section="admin"] .table-improved th:first-child,
      body[data-app-section="admin"] .table-improved td:first-child,
      body[data-app-section="admin"] .compact-table th:first-child,
      body[data-app-section="admin"] .compact-table td:first-child {
        padding-left: clamp(1.35rem, 2.5vw, 1.9rem) !important;
      }

      body[data-app-section="admin"] .admin-responsive-table th:last-child,
      body[data-app-section="admin"] .admin-responsive-table td:last-child,
      body[data-app-section="admin"] .table th:last-child,
      body[data-app-section="admin"] .table td:last-child,
      body[data-app-section="admin"] .data-table th:last-child,
      body[data-app-section="admin"] .data-table td:last-child,
      body[data-app-section="admin"] .table-improved th:last-child,
      body[data-app-section="admin"] .table-improved td:last-child,
      body[data-app-section="admin"] .compact-table th:last-child,
      body[data-app-section="admin"] .compact-table td:last-child {
        padding-right: clamp(1.35rem, 2.5vw, 1.9rem) !important;
        text-align: right !important;
      }

      body[data-app-section="admin"] .admin-responsive-table td:last-child .flex,
      body[data-app-section="admin"] .admin-responsive-table td:last-child .action-buttons,
      body[data-app-section="admin"] .table td:last-child .flex,
      body[data-app-section="admin"] .table td:last-child .action-buttons,
      body[data-app-section="admin"] .data-table td:last-child .flex,
      body[data-app-section="admin"] .data-table td:last-child .action-buttons,
      body[data-app-section="admin"] .table-improved td:last-child .flex,
      body[data-app-section="admin"] .table-improved td:last-child .action-buttons,
      body[data-app-section="admin"] .compact-table td:last-child .flex,
      body[data-app-section="admin"] .compact-table td:last-child .action-buttons {
        justify-content: flex-end !important;
      }

      body[data-app-section="admin"] .admin-table-person {
        display: grid !important;
        min-width: 10rem !important;
        gap: 0.18rem !important;
      }

      body[data-app-section="admin"] .admin-table-person strong {
        color: #1e293b !important;
        font-weight: 750 !important;
        line-height: 1.25 !important;
      }

      body[data-app-section="admin"] .admin-table-person span {
        color: #647386 !important;
        font-size: 0.83rem !important;
        line-height: 1.35 !important;
        overflow-wrap: anywhere !important;
      }

      body[data-app-section="admin"] .admin-code-chip,
      body[data-app-section="admin"] .alumni-id-code {
        display: inline-flex !important;
        align-items: center !important;
        max-width: 100% !important;
        min-height: 1.95rem !important;
        padding: 0.32rem 0.62rem !important;
        border: 1px solid #bde8cd !important;
        border-radius: 6px !important;
        background: #eefcf4 !important;
        color: #047857 !important;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace !important;
        font-size: 0.82rem !important;
        font-weight: 800 !important;
        line-height: 1.1 !important;
        letter-spacing: 0 !important;
        white-space: nowrap !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-link,
      body[data-app-section="admin"] .theme-ui-sidebar-link {
        min-height: 40px !important;
        padding: 9px 12px !important;
        font-size: 0.94rem !important;
        font-weight: 400 !important;
        line-height: 1.25 !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-link.active,
      body[data-app-section="admin"] .theme-ui-sidebar-link.active {
        font-weight: 400 !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-section-title,
      body[data-app-section="admin"] .theme-ui-sidebar-toggle span {
        font-weight: 700 !important;
      }

      body[data-app-section="admin"] .dashboard-layout .sidebar-section-toggle,
      body[data-app-section="admin"] .theme-ui-sidebar-toggle {
        min-height: 34px !important;
        padding: 8px 12px !important;
      }

      body[data-app-section="admin"] .sidebar-link-icon,
      body[data-app-section="admin"] .theme-ui-link-icon {
        width: 18px !important;
        height: 18px !important;
        flex: 0 0 18px !important;
      }

      body[data-app-section="admin"] .theme-ui-menu-btn {
        display: none !important;
      }

      body[data-app-section="admin"] .theme-ui-close-btn {
        display: inline-grid !important;
      }

      body[data-app-section="admin"] .admin-responsive-table tbody tr:nth-child(even),
      body[data-app-section="admin"] .table tbody tr:nth-child(even),
      body[data-app-section="admin"] .data-table tbody tr:nth-child(even),
      body[data-app-section="admin"] .table-improved tbody tr:nth-child(even),
      body[data-app-section="admin"] .compact-table tbody tr:nth-child(even) {
        background: #fbfdfc !important;
      }

      body[data-app-section="admin"] .admin-responsive-table tbody tr:hover,
      body[data-app-section="admin"] .table tbody tr:hover,
      body[data-app-section="admin"] .data-table tbody tr:hover,
      body[data-app-section="admin"] .table-improved tbody tr:hover,
      body[data-app-section="admin"] .compact-table tbody tr:hover {
        background: var(--admin-modern-primary-soft) !important;
        box-shadow: inset 4px 0 0 var(--admin-modern-primary) !important;
      }

      body[data-app-section="admin"] .admin-responsive-table .action-buttons,
      body[data-app-section="admin"] .table .action-buttons,
      body[data-app-section="admin"] .table-improved .action-buttons,
      body[data-app-section="admin"] .compact-table .action-buttons,
      body[data-app-section="admin"] .admin-responsive-table td .flex {
        display: flex !important;
        align-items: center !important;
        gap: 0.42rem !important;
        flex-wrap: wrap !important;
      }

      @media (max-width: 980px) {
        body[data-app-section="admin"] .dashboard-layout .content-header,
        body[data-app-section="admin"] .dashboard-layout .admin-topbar,
        body[data-app-section="admin"] .theme-ui-topbar {
          flex-wrap: nowrap !important;
          gap: 0.65rem !important;
          padding-left: 0.75rem !important;
          padding-right: 0.75rem !important;
        }

        body[data-app-section="admin"] .dashboard-layout .content-header > h1,
        body[data-app-section="admin"] .dashboard-layout .admin-topbar > h1,
        body[data-app-section="admin"] .dashboard-layout .content-header > .page-title,
        body[data-app-section="admin"] .dashboard-layout .admin-topbar > .page-title {
          flex: 1 1 auto !important;
          order: 0 !important;
        }

        body[data-app-section="admin"] .dashboard-layout .sidebar-toggle {
          order: -1 !important;
        }

        body[data-app-section="admin"] .dashboard-layout .topbar-actions,
        body[data-app-section="admin"] .dashboard-layout .admin-shell-user {
          order: 10 !important;
          flex: 0 0 auto !important;
        }

        body[data-app-section="admin"] .dashboard-sidebar-backdrop,
        body[data-app-section="admin"] .theme-ui-sidebar-backdrop {
          inset: var(--admin-modern-topbar-height) 0 0 0 !important;
        }

        body[data-app-section="admin"] .theme-ui-menu-btn {
          display: inline-grid !important;
        }

        body[data-app-section="admin"] .theme-ui-close-btn {
          display: none !important;
        }

        body[data-app-section="admin"] .theme-ui-shell.settings-sidebar-open .theme-ui-menu-btn,
        body[data-app-section="admin"] .theme-ui-shell:has(.theme-ui-sidebar.open) .theme-ui-menu-btn {
          display: none !important;
        }

        body[data-app-section="admin"] .theme-ui-shell.settings-sidebar-open .theme-ui-close-btn,
        body[data-app-section="admin"] .theme-ui-shell:has(.theme-ui-sidebar.open) .theme-ui-close-btn {
          display: inline-grid !important;
        }

        body[data-app-section="admin"] .dashboard-layout .grid-cols-6,
        body[data-app-section="admin"] .dashboard-layout .grid-cols-5,
        body[data-app-section="admin"] .dashboard-layout .grid-cols-4,
        body[data-app-section="admin"] .dashboard-layout .grid-cols-3,
        body[data-app-section="admin"] .dashboard-layout .grid-cols-2,
        body[data-app-section="admin"] .dashboard-layout .dashboard-charts-grid,
        body[data-app-section="admin"] .dashboard-layout #analyticsOverview,
        body[data-app-section="admin"] .dashboard-layout .dashboard-chart-grid,
        body[data-app-section="admin"] .dashboard-layout .dashboard-insight-grid,
        body[data-app-section="admin"] .dashboard-layout .field-section-grid,
        body[data-app-section="admin"] .dashboard-layout .admin-filter-grid,
        body[data-app-section="admin"] .theme-ui-content .settings-form-grid,
        body[data-app-section="admin"] .theme-ui-content .email-settings-grid,
        body[data-app-section="admin"] .theme-ui-content .email-template-layout {
          grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
          gap: 0.9rem !important;
        }

        body[data-app-section="admin"] .dashboard-layout .card,
        body[data-app-section="admin"] .dashboard-layout .card-improved,
        body[data-app-section="admin"] .dashboard-layout .admin-panel,
        body[data-app-section="admin"] .theme-ui-panel {
          min-width: 0 !important;
          overflow: hidden !important;
        }

        body[data-app-section="admin"] .dashboard-layout .stat-card-improved,
        body[data-app-section="admin"] .dashboard-layout .analytics-card {
          min-height: 98px !important;
          padding: 0.95rem !important;
        }

        body[data-app-section="admin"] .dashboard-layout .card-body > [id$="Chart"],
        body[data-app-section="admin"] .dashboard-layout .card-body > .chart-container {
          width: 100% !important;
          min-width: 0 !important;
          max-width: 100% !important;
          min-height: 220px !important;
          overflow-x: auto !important;
          overflow-y: hidden !important;
          -webkit-overflow-scrolling: touch !important;
        }

        body[data-app-section="admin"] .dashboard-layout .card-body > [id$="Chart"] canvas,
        body[data-app-section="admin"] .dashboard-layout .card-body > [id$="Chart"] svg,
        body[data-app-section="admin"] .dashboard-layout .card-body > .chart-container canvas,
        body[data-app-section="admin"] .dashboard-layout .card-body > .chart-container svg {
          max-width: 100% !important;
        }
      }

      @media (max-width: 680px) {
        body[data-app-section="admin"] {
          --admin-modern-topbar-height: 64px;
        }

        body[data-app-section="admin"] .dashboard-layout .admin-content,
        body[data-app-section="admin"] .dashboard-layout .content-body,
        body[data-app-section="admin"] .dashboard-layout .content-wrapper,
        body[data-app-section="admin"] .theme-ui-content {
          padding: 0.85rem !important;
        }

        body[data-app-section="admin"] .dashboard-layout .content-header h1,
        body[data-app-section="admin"] .dashboard-layout .admin-topbar h1,
        body[data-app-section="admin"] .dashboard-layout .admin-topbar .page-title,
        body[data-app-section="admin"] .theme-ui-topbar h1 {
          font-size: 1.05rem !important;
        }

        body[data-app-section="admin"] .dashboard-layout .dashboard-hero,
        body[data-app-section="admin"] .dashboard-layout .admin-page-title-block {
          margin-bottom: 0.9rem !important;
        }

        body[data-app-section="admin"] .dashboard-layout .dashboard-hero h2,
        body[data-app-section="admin"] .dashboard-layout .admin-page-title-block h2 {
          font-size: clamp(1.32rem, 8vw, 1.75rem) !important;
          line-height: 1.12 !important;
        }

        body[data-app-section="admin"] .dashboard-layout .dashboard-hero p,
        body[data-app-section="admin"] .dashboard-layout .admin-page-title-block p {
          font-size: 0.9rem !important;
        }

        body[data-app-section="admin"] .dashboard-layout .grid,
        body[data-app-section="admin"] .dashboard-layout .grid-cols-6,
        body[data-app-section="admin"] .dashboard-layout .grid-cols-5,
        body[data-app-section="admin"] .dashboard-layout .grid-cols-4,
        body[data-app-section="admin"] .dashboard-layout .grid-cols-3,
        body[data-app-section="admin"] .dashboard-layout .grid-cols-2,
        body[data-app-section="admin"] .dashboard-layout .dashboard-charts-grid,
        body[data-app-section="admin"] .dashboard-layout #analyticsOverview,
        body[data-app-section="admin"] .dashboard-layout .dashboard-chart-grid,
        body[data-app-section="admin"] .dashboard-layout .dashboard-insight-grid,
        body[data-app-section="admin"] .dashboard-layout .field-section-grid,
        body[data-app-section="admin"] .dashboard-layout .admin-filter-grid,
        body[data-app-section="admin"] .theme-ui-content .settings-form-grid,
        body[data-app-section="admin"] .theme-ui-content .email-settings-grid,
        body[data-app-section="admin"] .theme-ui-content .email-template-layout {
          grid-template-columns: 1fr !important;
          gap: 0.8rem !important;
        }

        body[data-app-section="admin"] .dashboard-layout .card-header,
        body[data-app-section="admin"] .dashboard-layout .card-improved .card-header,
        body[data-app-section="admin"] .dashboard-layout .dashboard-section-header,
        body[data-app-section="admin"] .theme-ui-panel-header {
          min-height: auto !important;
          padding: 0.9rem 1rem !important;
          gap: 0.55rem !important;
        }

        body[data-app-section="admin"] .dashboard-layout .dashboard-section-header {
          align-items: flex-start !important;
          flex-direction: column !important;
        }

        body[data-app-section="admin"] .dashboard-layout .dashboard-section-header .btn {
          margin-left: 0 !important;
          width: 100% !important;
          justify-content: center !important;
        }

        body[data-app-section="admin"] .dashboard-layout .dashboard-section-body,
        body[data-app-section="admin"] .dashboard-layout #upcomingEvents > a,
        body[data-app-section="admin"] .dashboard-layout #upcomingEvents > div {
          padding-left: 1rem !important;
          padding-right: 1rem !important;
        }

        body[data-app-section="admin"] .dashboard-layout .card > .card-body:not(.p-0),
        body[data-app-section="admin"] .dashboard-layout .card-improved > .card-body:not(.p-0),
        body[data-app-section="admin"] .theme-ui-panel-body {
          padding: 0.95rem 1rem !important;
        }

        body[data-app-section="admin"] .dashboard-layout .stat-card-improved {
          min-height: 92px !important;
          padding: 0.9rem !important;
          gap: 0.75rem !important;
        }

        body[data-app-section="admin"] .dashboard-layout .stat-card-improved .stat-icon {
          width: 40px !important;
          height: 40px !important;
          min-width: 40px !important;
          flex-basis: 40px !important;
        }

        body[data-app-section="admin"] .dashboard-layout .stat-card-improved .stat-value,
        body[data-app-section="admin"] .dashboard-layout .analytics-card-value {
          font-size: clamp(1.35rem, 8vw, 1.85rem) !important;
        }

        body[data-app-section="admin"] .dashboard-layout .analytics-card {
          min-height: 92px !important;
          padding: 0.95rem 1rem !important;
        }

        body[data-app-section="admin"] .dashboard-layout .card-body > [id$="Chart"],
        body[data-app-section="admin"] .dashboard-layout .card-body > .chart-container {
          min-height: 190px !important;
        }

        body[data-app-section="admin"] .dashboard-layout .field-type-card,
        body[data-app-section="admin"] .dashboard-layout .field-section-card {
          min-height: auto !important;
          padding: 0.9rem !important;
        }

        body[data-app-section="admin"] .dashboard-layout .field-toggle-row {
          grid-template-columns: 1fr auto auto auto !important;
          gap: 0.45rem !important;
          align-items: center !important;
        }

        body[data-app-section="admin"] .dashboard-layout .field-toggle-row .field-info {
          min-width: 0 !important;
        }

        body[data-app-section="admin"] .dashboard-layout .field-toggle-row .field-name,
        body[data-app-section="admin"] .dashboard-layout .field-toggle-row .field-meta {
          overflow-wrap: anywhere !important;
        }

        body[data-app-section="admin"] .dashboard-layout .sidebar-toggle,
        body[data-app-section="admin"] .theme-ui-sidebar-toggle-btn,
        body[data-app-section="admin"] .theme-ui-close-btn,
        body[data-app-section="admin"] .theme-ui-menu-btn {
          width: 38px !important;
          height: 38px !important;
          min-width: 38px !important;
        }

        body[data-app-section="admin"] .dashboard-layout .admin-shell-logout,
        body[data-app-section="admin"] .theme-ui-logout-btn,
        body[data-app-section="admin"] #settingsTopbarLogout {
          min-height: 36px !important;
          padding: 0 0.7rem !important;
          font-size: 0.78rem !important;
        }

        body[data-app-section="admin"] .admin-table-scroll,
        body[data-app-section="admin"] .table-container,
        body[data-app-section="admin"] .table-responsive,
        body[data-app-section="admin"] .admin-record-table-shell,
        body[data-app-section="admin"] .card-body.p-0:has(> table) {
          overflow: visible !important;
          border: 0 !important;
          border-radius: 0 !important;
          background: transparent !important;
          box-shadow: none !important;
        }

        body[data-app-section="admin"] .admin-record-table-shell {
          border: 1px solid var(--admin-modern-line) !important;
          border-radius: var(--admin-modern-radius) !important;
          background: #ffffff !important;
          box-shadow: var(--admin-modern-shadow) !important;
        }

        body[data-app-section="admin"] .admin-record-table-shell > .admin-table-scroll,
        body[data-app-section="admin"] .admin-record-table-shell > .table-container,
        body[data-app-section="admin"] .admin-record-table-shell > .table-responsive,
        body[data-app-section="admin"] .admin-record-table-shell > div[id$="Table"] {
          margin: 0 !important;
          padding: 0.75rem !important;
          width: 100% !important;
          max-width: 100% !important;
        }

        body[data-app-section="admin"] .admin-record-table-shell > .card-footer {
          padding: 0.75rem !important;
          justify-content: center !important;
        }

        body[data-app-section="admin"] .admin-responsive-table {
          display: block !important;
          min-width: 0 !important;
          background: transparent !important;
        }

        body[data-app-section="admin"] .admin-responsive-table thead {
          display: none !important;
        }

        body[data-app-section="admin"] .admin-responsive-table tbody {
          display: grid !important;
          gap: 0.8rem !important;
        }

        body[data-app-section="admin"] .admin-responsive-table tr {
          display: grid !important;
          width: 100% !important;
          padding: 0.78rem 0.9rem !important;
          border: 1px solid var(--admin-modern-line) !important;
          border-radius: var(--admin-modern-radius) !important;
          background: #ffffff !important;
          box-shadow: var(--admin-modern-shadow) !important;
        }

        body[data-app-section="admin"] .admin-responsive-table tbody tr:hover {
          box-shadow: var(--admin-modern-shadow) !important;
        }

        body[data-app-section="admin"] .admin-responsive-table td {
          display: grid !important;
          grid-template-columns: minmax(6.5rem, 36%) minmax(0, 1fr) !important;
          gap: 0.85rem !important;
          align-items: start !important;
          max-width: none !important;
          min-height: 2.25rem !important;
          padding: 0.62rem 0 !important;
          border-bottom: 1px solid #edf2ef !important;
          background: transparent !important;
          white-space: normal !important;
          overflow-wrap: anywhere !important;
        }

        body[data-app-section="admin"] .admin-responsive-table td::before {
          content: attr(data-label) !important;
          color: #647386 !important;
          font-size: 0.72rem !important;
          font-weight: 800 !important;
          letter-spacing: 0.04em !important;
          text-transform: uppercase !important;
          line-height: 1.35 !important;
        }

        body[data-app-section="admin"] .admin-responsive-table td:last-child {
          border-bottom: 0 !important;
          text-align: left !important;
        }

        body[data-app-section="admin"] .admin-responsive-table td[colspan] {
          grid-template-columns: 1fr !important;
        }

        body[data-app-section="admin"] .admin-responsive-table td[colspan]::before {
          display: none !important;
        }

        body[data-app-section="admin"] .admin-responsive-table .action-buttons,
        body[data-app-section="admin"] .admin-responsive-table td .flex {
          justify-content: flex-start !important;
        }

        body[data-app-section="admin"] .admin-responsive-table .action-buttons .btn,
        body[data-app-section="admin"] .admin-responsive-table td .btn,
        body[data-app-section="admin"] .dashboard-layout .topbar-actions .btn,
        body[data-app-section="admin"] .dashboard-layout .content-header > .btn,
        body[data-app-section="admin"] .dashboard-layout .admin-topbar > .btn,
        body[data-app-section="admin"] .admin-shell-user .btn,
        body[data-app-section="admin"] .theme-ui-topbar .btn,
        body[data-app-section="admin"] .theme-ui-user .btn {
          width: auto !important;
          flex: 0 0 auto !important;
        }
      }

      @media (max-width: 420px) {
        body[data-app-section="admin"] .admin-responsive-table td {
          grid-template-columns: 1fr !important;
          gap: 0.24rem !important;
        }
      }
    `;

    container.appendChild(style);
  },

  enhanceAdminTables(container = document) {
    if (!container || document.body?.dataset?.appSection !== "admin") {
      this.disconnectAdminTableObserver();
      return;
    }

    const tables = container.querySelectorAll(
      "table.table, table.data-table, table.table-improved, table.compact-table",
    );

    tables.forEach((table) => {
      table.classList.add("admin-responsive-table");

      const parent = table.parentElement;
      const hasScrollShell = table.closest(
        ".admin-table-scroll, .table-container, .table-responsive, .admin-record-table-shell",
      );

      if (
        parent &&
        !hasScrollShell &&
        !parent.classList.contains("card-body")
      ) {
        const shell = document.createElement("div");
        shell.className = "admin-table-scroll";
        parent.insertBefore(shell, table);
        shell.appendChild(table);
      } else if (
        parent &&
        !hasScrollShell &&
        parent.classList.contains("card-body") &&
        !parent.classList.contains("p-0")
      ) {
        const shell = document.createElement("div");
        shell.className = "admin-table-scroll";
        parent.insertBefore(shell, table);
        shell.appendChild(table);
      }

      this.applyAdminTableLabels(table);
    });

    this.observeAdminTableEnhancements(container);
  },

  applyAdminTableLabels(table) {
    if (!table) {
      return;
    }

    const headers = Array.from(table.querySelectorAll("thead th")).map(
      (header) =>
        String(header.textContent || "")
          .replace(/\s+/g, " ")
          .trim(),
    );

    table.querySelectorAll("tbody tr").forEach((row) => {
      Array.from(row.children).forEach((cell, index) => {
        if (cell.tagName !== "TD" || cell.hasAttribute("data-label")) {
          return;
        }

        const label = headers[index] || "";
        if (label) {
          cell.setAttribute("data-label", label);
        }
      });
    });
  },

  observeAdminTableEnhancements(container = document) {
    if (this._adminTableObserver) {
      this._adminTableObserver.disconnect();
      this._adminTableObserver = null;
    }

    if (!container || document.body?.dataset?.appSection !== "admin") {
      return;
    }

    let scheduled = false;
    this._adminTableObserver = new MutationObserver(() => {
      if (scheduled) {
        return;
      }

      scheduled = true;
      window.requestAnimationFrame(() => {
        scheduled = false;
        const newTables = container.querySelector(
          "table.table:not(.admin-responsive-table), table.data-table:not(.admin-responsive-table), table.table-improved:not(.admin-responsive-table), table.compact-table:not(.admin-responsive-table)",
        );

        if (newTables) {
          this.enhanceAdminTables(container);
          return;
        }

        container
          .querySelectorAll("table.admin-responsive-table")
          .forEach((table) => this.applyAdminTableLabels(table));
      });
    });

    this._adminTableObserver.observe(container, {
      childList: true,
      subtree: true,
    });
  },

  disconnectAdminTableObserver() {
    if (!this._adminTableObserver) {
      return;
    }

    this._adminTableObserver.disconnect();
    this._adminTableObserver = null;
  },

  /**
   * Initialize application
   */
  async init() {
    console.log("Initializing Alumni Management System...");

    // Load visual and branding settings before route rendering.
    await Promise.all([
      this.loadTheme(),
      this.loadSiteContent(),
      this.initFirebase(),
    ]);

    // Initialize auth state
    Auth.init();
    Auth.subscribe(() => this.syncGlobalAlumniNotifications());

    // Setup router
    this.setupRoutes();

    // Bind delegated admin shell listeners once for dynamically injected pages.
    this.bindGlobalAdminShellEvents();

    // Initialize router
    Router.init();
    this.syncGlobalAlumniNotifications();

    console.log("App initialized");
  },

  /**
   * Render a startup error state if initialization fails.
   */
  renderStartupError(error) {
    const message =
      error?.message ||
      "The application could not finish initialization. Please refresh and try again.";

    const appContainer = document.getElementById("app");
    if (!appContainer) {
      return;
    }

    appContainer.innerHTML = `
      <div class="min-h-screen flex items-center justify-center p-lg">
        <div class="card elevated p-xl" style="max-width: 600px; width: 100%;">
          <h2 class="text-2xl font-bold mb-md">Unable to Start Frontend</h2>
          <p class="text-secondary mb-md">${Utils.escapeHtml(message)}</p>
          <div class="bg-gray-50 p-md rounded-lg mb-lg text-sm text-secondary">
            If you are running the frontend from a custom host/port, ensure API is reachable and assets are served from the client directory.
          </div>
          <div class="flex gap-sm">
            <button class="btn btn-primary" onclick="window.location.reload()">Reload</button>
            <a class="btn btn-secondary" href="#/">Go Home</a>
          </div>
        </div>
      </div>
    `;
  },

  /**
   * Load theme settings from API
   */
  async loadTheme() {
    try {
      const response = await API.site.getTheme();
      if (response.data) {
        this.applyTheme(response.data);
      }
    } catch (error) {
      console.log("Using default theme");
    }
  },

  /**
   * Load site content/branding settings from API.
   */
  async loadSiteContent() {
    try {
      const response = await API.site.getContent();
      if (response.data) {
        this.applySiteContent(response.data);
      }
    } catch (error) {
      console.log("Using default site content");
    }
  },

  /**
   * Initialize Firebase Auth using backend-provided public config.
   */
  async initFirebase() {
    this.firebaseReady = false;
    this.firebaseInitError = "";
    window.FirebaseAuthReady = false;

    if (typeof firebase === "undefined") {
      this.firebaseInitError = "Firebase SDK is not loaded";
      return false;
    }

    try {
      const response = await API.site.getFirebaseConfig();
      const payload = response?.data || {};
      const firebaseConfig = payload.firebase || payload || {};
      const requiredKeys = ["apiKey", "authDomain", "projectId", "appId"];

      const missing = Array.isArray(payload.missing)
        ? payload.missing
        : requiredKeys.filter(
            (key) => !String(firebaseConfig[key] || "").trim(),
          );

      if (payload.isConfigured === false || missing.length > 0) {
        this.firebaseInitError = `Firebase config missing: ${missing.join(", ")}`;
        return false;
      }

      if (!firebase.apps || firebase.apps.length === 0) {
        firebase.initializeApp(firebaseConfig);
      }

      this.firebaseReady = true;
      window.FirebaseAuthReady = true;
      return true;
    } catch (error) {
      console.warn("Firebase initialization skipped:", error);
      this.firebaseInitError =
        error?.message || "Unable to load Firebase configuration";
      return false;
    }
  },

  /**
   * Store and apply normalized site content payload.
   */
  applySiteContent(content) {
    this._siteContent = this.normalizeSiteContent(content);
    this.applyBranding();
  },

  /**
   * Normalize array/object site-content payloads into key-value pairs.
   */
  normalizeSiteContent(content) {
    const normalized = {};

    if (Array.isArray(content)) {
      content.forEach((item) => {
        if (!item || !item.content_key) {
          return;
        }
        normalized[item.content_key] = item.content_value;
      });
      return normalized;
    }

    if (content && typeof content === "object") {
      Object.assign(normalized, content);
    }

    return normalized;
  },

  /**
   * Apply theme settings
   */
  applyTheme(settings) {
    const root = document.documentElement;
    const normalized = {};

    if (Array.isArray(settings)) {
      settings.forEach((setting) => {
        if (!setting || !setting.setting_key) {
          return;
        }
        normalized[setting.setting_key] = setting.setting_value;
      });
    } else if (settings && typeof settings === "object") {
      Object.assign(normalized, settings);
    } else {
      return;
    }

    this._themeSettings = { ...normalized };
    this.applyAdminSidebarStyle(normalized.sidebar_style);

    const normalizeHexColor = (value) => {
      const raw = String(value || "").trim();
      if (/^#[0-9a-fA-F]{6}$/.test(raw)) {
        return raw.toLowerCase();
      }

      if (/^#[0-9a-fA-F]{3}$/.test(raw)) {
        const expanded = raw
          .slice(1)
          .split("")
          .map((char) => `${char}${char}`)
          .join("");
        return `#${expanded.toLowerCase()}`;
      }

      return "";
    };

    const hexToRgb = (hexValue) => {
      const hex = normalizeHexColor(hexValue);
      if (!hex) {
        return null;
      }

      const value = hex.slice(1);
      return {
        r: parseInt(value.slice(0, 2), 16),
        g: parseInt(value.slice(2, 4), 16),
        b: parseInt(value.slice(4, 6), 16),
      };
    };

    const rgbToHex = (r, g, b) =>
      `#${[r, g, b]
        .map((channel) => {
          const value = Math.max(0, Math.min(255, Math.round(channel)));
          return value.toString(16).padStart(2, "0");
        })
        .join("")}`;

    const mixColors = (base, target, targetWeight) => {
      const safeWeight = Math.max(0, Math.min(1, Number(targetWeight) || 0));
      return rgbToHex(
        base.r * (1 - safeWeight) + target.r * safeWeight,
        base.g * (1 - safeWeight) + target.g * safeWeight,
        base.b * (1 - safeWeight) + target.b * safeWeight,
      );
    };

    const colorToRgba = (hexValue, alpha = 1) => {
      const rgb = hexToRgb(hexValue);
      if (!rgb) {
        return "";
      }

      const safeAlpha = Math.max(0, Math.min(1, Number(alpha) || 0));
      return `rgb(${rgb.r} ${rgb.g} ${rgb.b} / ${safeAlpha})`;
    };

    const white = { r: 255, g: 255, b: 255 };
    const black = { r: 0, g: 0, b: 0 };

    const keyMap = {
      primary_color: "--color-primary",
      secondary_color: "--color-secondary",
      accent_color: "--color-accent",
      background_color: "--color-background",
      text_color: "--color-text",
    };

    Object.entries(keyMap).forEach(([apiKey, cssVar]) => {
      if (normalized[apiKey]) {
        root.style.setProperty(cssVar, normalized[apiKey]);
      }
    });

    const primaryBase = hexToRgb(normalized.primary_color || "#10b981");
    const secondaryBase = hexToRgb(normalized.secondary_color || "#6b7280");
    const accentBase = hexToRgb(normalized.accent_color || "#f59e0b");
    const backgroundBase = hexToRgb(normalized.background_color || "#f9fafb");
    const textBase = hexToRgb(normalized.text_color || "#1f2937");

    if (accentBase) {
      const accentScale = {
        100: mixColors(accentBase, white, 0.82),
        200: mixColors(accentBase, white, 0.64),
        400: mixColors(accentBase, white, 0.24),
        600: mixColors(accentBase, black, 0.14),
        700: mixColors(accentBase, black, 0.26),
        900: mixColors(accentBase, black, 0.58),
      };

      root.style.setProperty("--color-accent-soft", accentScale[100]);
      root.style.setProperty("--color-accent-muted", accentScale[200]);
      root.style.setProperty("--color-accent-strong", accentScale[700]);
      root.style.setProperty("--color-accent-ink", accentScale[900]);
      root.style.setProperty(
        "--color-accent-glow",
        colorToRgba(accentScale[600], 0.24),
      );
    }

    if (textBase) {
      root.style.setProperty(
        "--color-text-secondary",
        mixColors(textBase, white, 0.34),
      );
      root.style.setProperty(
        "--color-header-text",
        mixColors(textBase, black, 0.12),
      );
      root.style.setProperty(
        "--color-header-muted",
        mixColors(textBase, white, 0.38),
      );
    }

    if (backgroundBase && textBase) {
      const panelBackground = mixColors(backgroundBase, white, 0.82);
      const panelBorder = mixColors(backgroundBase, textBase, 0.14);

      root.style.setProperty("--color-surface", panelBackground);
      root.style.setProperty("--color-panel-bg", panelBackground);
      root.style.setProperty("--color-panel-border", panelBorder);
      root.style.setProperty("--color-border", panelBorder);
      root.style.setProperty(
        "--color-header-bg",
        mixColors(backgroundBase, white, 0.68),
      );
      root.style.setProperty(
        "--color-header-border",
        mixColors(backgroundBase, textBase, 0.18),
      );
      root.style.setProperty("--loading-overlay-bg", "rgb(248 250 252 / 0.78)");
    }

    if (primaryBase) {
      const primaryScale = {
        50: mixColors(primaryBase, white, 0.9),
        100: mixColors(primaryBase, white, 0.8),
        200: mixColors(primaryBase, white, 0.62),
        300: mixColors(primaryBase, white, 0.42),
        400: mixColors(primaryBase, white, 0.2),
        500: mixColors(primaryBase, white, 0.08),
        600: mixColors(primaryBase, black, 0.08),
        700: mixColors(primaryBase, black, 0.2),
        800: mixColors(primaryBase, black, 0.34),
        900: mixColors(primaryBase, black, 0.5),
        950: mixColors(primaryBase, black, 0.66),
      };

      Object.entries(primaryScale).forEach(([scaleKey, scaleValue]) => {
        root.style.setProperty(`--primary-${scaleKey}`, scaleValue);
      });

      root.style.setProperty("--color-primary-light", primaryScale[500]);
      root.style.setProperty("--color-primary-dark", primaryScale[800]);
      root.style.setProperty("--color-focus-ring", primaryScale[300]);
      root.style.setProperty("--color-surface-soft", primaryScale[50]);
      root.style.setProperty("--auth-header-gradient-start", primaryScale[600]);
      root.style.setProperty("--auth-header-gradient-end", primaryScale[800]);

      const authGradientEnd = secondaryBase
        ? mixColors(secondaryBase, black, 0.32)
        : primaryScale[900];
      root.style.setProperty(
        "--auth-page-gradient-start",
        colorToRgba(primaryScale[700], 0.82),
      );
      root.style.setProperty(
        "--auth-page-gradient-end",
        colorToRgba(authGradientEnd, 0.9),
      );

      const overlayTintHex = mixColors(primaryBase, black, 0.72);
      const overlayTint = hexToRgb(overlayTintHex);
      if (overlayTint) {
        root.style.setProperty(
          "--color-overlay-bg",
          `rgb(${overlayTint.r} ${overlayTint.g} ${overlayTint.b} / 0.52)`,
        );
      }
    }

    const toFontStack = (fontValue) => {
      const value = String(fontValue || "").trim();
      if (!value) {
        return "";
      }

      if (value.includes(",")) {
        return value;
      }

      return `"${value}", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif`;
    };

    const bodyFont = normalized.body_font || normalized.font_family;
    const headingFont = normalized.heading_font || bodyFont;

    if (bodyFont) {
      root.style.setProperty("--font-family", toFontStack(bodyFont));
    }

    if (headingFont) {
      root.style.setProperty("--font-family-heading", toFontStack(headingFont));
    }

    const radiusPresets = {
      none: { sm: "0", md: "0", lg: "0", xl: "0", xxl: "0" },
      sm: {
        sm: "0.125rem",
        md: "0.25rem",
        lg: "0.375rem",
        xl: "0.5rem",
        xxl: "0.625rem",
      },
      md: {
        sm: "0.25rem",
        md: "0.375rem",
        lg: "0.5rem",
        xl: "0.75rem",
        xxl: "1rem",
      },
      lg: {
        sm: "0.375rem",
        md: "0.5rem",
        lg: "0.75rem",
        xl: "1rem",
        xxl: "1.25rem",
      },
    };

    const radiusKey = String(normalized.border_radius || "").toLowerCase();
    const radiusSet = radiusPresets[radiusKey];

    if (radiusSet) {
      root.style.setProperty("--radius-sm", radiusSet.sm);
      root.style.setProperty("--radius-md", radiusSet.md);
      root.style.setProperty("--radius-lg", radiusSet.lg);
      root.style.setProperty("--radius-xl", radiusSet.xl);
      root.style.setProperty("--radius-2xl", radiusSet.xxl);
    }

    const faviconUrl = String(normalized.favicon_url || "").trim();
    if (faviconUrl) {
      let favicon = document.querySelector('link[rel="icon"]');
      if (!favicon) {
        favicon = document.createElement("link");
        favicon.setAttribute("rel", "icon");
        document.head.appendChild(favicon);
      }
      favicon.setAttribute("href", faviconUrl);
    }

    if (Object.prototype.hasOwnProperty.call(normalized, "custom_css")) {
      const styleId = "custom-theme-css";
      const cssText = String(normalized.custom_css || "").trim();
      let customStyle = document.getElementById(styleId);

      if (!cssText) {
        if (customStyle) {
          customStyle.remove();
        }
      } else {
        if (!customStyle) {
          customStyle = document.createElement("style");
          customStyle.id = styleId;
          document.head.appendChild(customStyle);
        }
        customStyle.textContent = cssText;
      }
    }

    const logoUrl = this.resolveThemeAssetUrl(normalized.logo_url || "");
    const authBackgroundImageUrl = this.resolveThemeAssetUrl(
      normalized.auth_background_image_url || "",
    );

    if (authBackgroundImageUrl) {
      const escapedUrl = authBackgroundImageUrl.replace(/"/g, '\\"');
      root.style.setProperty("--auth-background-image", `url("${escapedUrl}")`);
      root.style.setProperty(
        "--landing-background-image",
        `url("${escapedUrl}")`,
      );
    } else {
      root.style.setProperty("--auth-background-image", "none");
      root.style.setProperty("--landing-background-image", "none");
    }

    this.applyThemeLogoImages(logoUrl);
    this.applyBranding();
  },

  /**
   * Apply admin sidebar style preference across dashboard-layout templates.
   */
  applyAdminSidebarStyle(sidebarStyle) {
    if (!document.body) {
      return;
    }

    const nextStyle =
      String(sidebarStyle || "dark")
        .trim()
        .toLowerCase() === "dark"
        ? "dark"
        : "light";

    document.body.dataset.adminSidebarStyle = nextStyle;
  },

  /**
   * Determine active app section so route-specific styles can be scoped safely.
   */
  setActiveAppSection(path) {
    if (!document.body) {
      return;
    }

    const safePath = String(path || "");
    let section = "public";

    if (safePath.startsWith("/admin") && safePath !== "/admin/login") {
      section = "admin";
    } else if (
      [
        "/dashboard",
        "/profile",
        "/events",
        "/announcements",
        "/messages",
        "/leaderboard",
        "/rewards",
        "/id-card",
        "/qr-scanner",
      ].some((route) => safePath === route || safePath.startsWith(`${route}/`))
    ) {
      section = "alumni";
    }

    document.body.dataset.appSection = section;
  },

  /**
   * Build a single branding snapshot from theme + site content values.
   */
  getBrandingSnapshot() {
    const theme = this._themeSettings || {};
    const content = this._siteContent || {};
    const defaultInstitutionName = "Mindoro State University";
    const fallbackInstitutionName = String(
      content.university_name ||
        content.school_name ||
        content.institution ||
        defaultInstitutionName,
    ).trim();
    let institutionName = String(
      theme.institution_name ||
        content.institution_name ||
        content.university_name ||
        content.school_name ||
        fallbackInstitutionName,
    ).trim();
    const departmentName = String(
      theme.department_name ||
        content.department_name ||
        content.college_department_name ||
        content.department ||
        content.office_name ||
        "Alumni Association",
    ).trim();
    const siteName = String(
      content.site_name ||
        content.system_name ||
        content.app_name ||
        `${institutionName} Alumni`,
    ).trim();

    if (
      institutionName &&
      siteName &&
      institutionName.toLowerCase() === siteName.toLowerCase()
    ) {
      if (
        fallbackInstitutionName &&
        fallbackInstitutionName.toLowerCase() !== siteName.toLowerCase()
      ) {
        institutionName = fallbackInstitutionName;
      } else if (
        defaultInstitutionName.toLowerCase() !== siteName.toLowerCase()
      ) {
        institutionName = defaultInstitutionName;
      }
    }
    const shortName = this.deriveShortBrand(siteName || institutionName);
    const logoUrl = this.resolveThemeAssetUrl(
      theme.logo_url ||
        content.logo_url ||
        content.site_logo ||
        content.site_logo_url ||
        content.logo ||
        "assets/images/logo.svg",
    );
    const year = new Date().getFullYear();
    const footerText = String(
      content.footer_text ||
        `Â© ${year} ${institutionName}. All rights reserved.`,
    ).trim();

    return {
      institutionName,
      departmentName,
      siteName,
      shortName,
      logoUrl,
      footerText,
      appTitle: siteName || "Alumni System",
    };
  },

  /**
   * Derive a short brand label from institution/site names.
   */
  deriveShortBrand(value) {
    const source = String(value || "").trim();
    if (!source) {
      return "AMS";
    }

    const tokens = source
      .replace(/[^a-zA-Z0-9\s]/g, " ")
      .split(/\s+/)
      .filter(Boolean)
      .filter(
        (token) =>
          !["of", "the", "and", "for", "alumni"].includes(token.toLowerCase()),
      );

    if (!tokens.length) {
      return source.slice(0, 4).toUpperCase();
    }

    if (tokens.length === 1) {
      return tokens[0].slice(0, 4).toUpperCase();
    }

    return tokens
      .slice(0, 5)
      .map((token) => token[0].toUpperCase())
      .join("");
  },

  /**
   * Apply branding labels, titles, and known text tokens on the active page.
   */
  applyBranding(options = {}) {
    if (!document.body) {
      return;
    }

    const branding = this.getBrandingSnapshot();
    const currentPath = (window.location.hash || "#/").replace(/^#/, "") || "/";
    const signature = JSON.stringify(branding);
    const force = options.force === true;

    if (
      !force &&
      this._brandingSignature === signature &&
      this._brandingAppliedPath === currentPath
    ) {
      return;
    }

    this._brandingSignature = signature;
    this._brandingAppliedPath = currentPath;

    this.applyThemeLogoImages(branding.logoUrl);

    const setText = (selector, value) => {
      if (!value) {
        return;
      }

      document.querySelectorAll(selector).forEach((element) => {
        if (element && element.textContent !== value) {
          element.textContent = value;
        }
      });
    };

    const setAlt = (selector, value) => {
      if (!value) {
        return;
      }

      document.querySelectorAll(selector).forEach((element) => {
        if (element && element.tagName === "IMG") {
          element.alt = value;
        }
      });
    };

    setText(
      '.nav-logo-text, .footer-logo-text, .auth-logo-text, [data-branding="short"]',
      branding.shortName,
    );
    setText(
      '.sidebar-brand-name, [data-branding="site"], [data-branding="site-name"]',
      branding.siteName,
    );
    setText(
      '.sidebar-brand-subtitle, .nav-subtitle, .auth-subtitle, [data-branding="institution"]',
      branding.institutionName,
    );
    setText(".nav-title", branding.siteName);
    setText('[data-branding="footer"]', branding.footerText);
    setAlt("img.nav-logo, img.footer-logo", branding.shortName);
    setAlt(".auth-logo img", `${branding.siteName} logo`);

    const footerBrand = document.querySelector(".footer-brand p");
    if (footerBrand) {
      footerBrand.innerHTML = `${Utils.escapeHtml(branding.institutionName)}<br />${Utils.escapeHtml(branding.siteName)}`;
    }

    const footerBottom = document.querySelector(".footer-bottom p");
    if (footerBottom) {
      footerBottom.textContent = branding.footerText;
    }

    const textReplacements = [
      { from: /Mindoro State University/g, to: branding.institutionName },
      { from: /Office of the Admission/g, to: branding.departmentName },
      { from: /All Alumnis/g, to: "All Alumni" },
      { from: /MINSU Alumni/g, to: branding.siteName },
      { from: /\bMINSU\b/g, to: branding.shortName },
      { from: /Alumni System/g, to: branding.siteName },
    ].filter((item) => item.to);

    const walker = document.createTreeWalker(
      document.body,
      NodeFilter.SHOW_TEXT,
      {
        acceptNode(node) {
          if (!node || !node.parentElement) {
            return NodeFilter.FILTER_REJECT;
          }

          const parentTag = node.parentElement.tagName;
          if (
            ["SCRIPT", "STYLE", "TEXTAREA", "CODE", "PRE"].includes(parentTag)
          ) {
            return NodeFilter.FILTER_REJECT;
          }

          return NodeFilter.FILTER_ACCEPT;
        },
      },
    );

    const nodes = [];
    while (walker.nextNode()) {
      nodes.push(walker.currentNode);
    }

    nodes.forEach((node) => {
      let nextValue = node.nodeValue;
      if (!nextValue || !nextValue.trim()) {
        return;
      }

      textReplacements.forEach(({ from, to }) => {
        nextValue = nextValue.replace(from, to);
      });

      if (nextValue !== node.nodeValue) {
        node.nodeValue = nextValue;
      }
    });
  },

  /**
   * Resolve theme asset URLs so backend-hosted uploads work on frontend origin.
   */
  resolveThemeAssetUrl(assetUrl) {
    const raw = String(assetUrl || "").trim();
    if (!raw) {
      return "";
    }

    if (/^(https?:|data:|blob:)/i.test(raw)) {
      return raw;
    }

    if (raw.startsWith("//")) {
      const protocol = window?.location?.protocol || "https:";
      return `${protocol}${raw}`;
    }

    let apiOrigin = "";
    try {
      apiOrigin = new URL(API.baseUrl, window.location.origin).origin;
    } catch {
      apiOrigin = window.location.origin;
    }

    if (/^\/?uploads\//i.test(raw)) {
      const uploadPath = raw.startsWith("/") ? raw : `/${raw}`;
      return `${apiOrigin}${uploadPath}`;
    }

    return raw;
  },

  /**
   * Apply current logo URL to default brand logos across dynamically loaded pages.
   */
  applyThemeLogoImages(resolvedLogoUrl = "") {
    const fallbackLogo = "assets/images/logo.svg";
    const nextLogo = resolvedLogoUrl || fallbackLogo;

    document.querySelectorAll("img").forEach((image) => {
      const attrSource = String(image.getAttribute("src") || "").trim();
      const currentSource = String(image.src || "");
      const explicitThemeLogo = image.dataset.themeLogo === "true";
      const isDefaultLogo =
        /(^|\/)assets\/images\/logo\.(png|svg)$/i.test(attrSource) ||
        /\/assets\/images\/logo\.(png|svg)(?:$|[?#])/i.test(currentSource);
      const isAuthLogo = image.closest(".auth-logo");

      if (
        !explicitThemeLogo &&
        !isDefaultLogo &&
        !isAuthLogo &&
        !image.dataset.appliedThemeLogo
      ) {
        return;
      }

      if (image.dataset.appliedThemeLogo === nextLogo) {
        return;
      }

      image.dataset.appliedThemeLogo = nextLogo;
      image.src = nextLogo;
      image.style.display = "";
    });
  },

  /**
   * Setup routes
   */
  setupRoutes() {
    // Add auth guard
    Router.beforeEach(async (context) => {
      const publicRoutes = [
        "/",
        "/login",
        "/register",
        "/verify-email",
        "/forgot-password",
        "/reset-password",
        "/admin/login",
      ];
      const adminRoutes =
        context.path.startsWith("/admin") && context.path !== "/admin/login";
      const authRoutes = ["/login", "/register", "/admin/login"];

      // Define restricted pages for campus_admin and staff
      const systemAdminOnlyPages = [
        "/admin/settings",
        "/admin/settings/theme",
        "/admin/settings/site-content",
        "/admin/settings/email-templates",
        "/admin/settings/security",
        "/admin/campuses",
        "/admin/organization",
        "/admin/logs",
        "/admin/security-center",
      ];

      const campusAdminRestrictedPages = [
        "/admin/alumni-verification",
        "/admin/users",
        "/admin/form-builder",
        "/admin/settings",
        "/admin/settings/theme",
        "/admin/settings/site-content",
        "/admin/settings/email-templates",
        "/admin/campuses",
        "/admin/logs",
        "/admin/security-center",
      ];

      const staffRestrictedPages = [
        "/admin/alumni-verification",
        "/admin/events",
        "/admin/announcements",
        "/admin/settings",
        "/admin/settings/theme",
        "/admin/settings/site-content",
        "/admin/settings/email-templates",
        "/admin/settings/security",
        "/admin/campuses",
        "/admin/organization",
        "/admin/logs",
        "/admin/security-center",
        "/admin/users",
        "/admin/gamification",
        "/admin/form-builder",
      ];

      const getVerifiedUser = async () => {
        const token = API.getToken();

        if (!token) {
          return null;
        }

        const isValidSession =
          typeof Auth !== "undefined" && typeof Auth.verifyToken === "function"
            ? await Auth.verifyToken()
            : Boolean(API.getUser());

        if (!isValidSession) {
          return null;
        }

        return (typeof Auth !== "undefined" && Auth.user) || API.getUser();
      };

      // Check auth for protected routes
      if (!publicRoutes.includes(context.path)) {
        const user = await getVerifiedUser();

        if (!user) {
          return adminRoutes ? "/admin/login" : "/login";
        }

        // Check admin access and role-based restrictions
        if (adminRoutes) {
          const userRole = user.role;

          // Check if user has any admin role
          if (
            !["admin", "system_admin", "campus_admin", "staff"].includes(
              userRole,
            )
          ) {
            Utils.error("Admin access required");
            return "/login";
          }

          // Check role-based page restrictions
          if (
            userRole === "staff" &&
            staffRestrictedPages.some((page) => context.path.startsWith(page))
          ) {
            Utils.error("You don't have permission to access this page");
            return "/admin/dashboard";
          }

          if (
            userRole === "campus_admin" &&
            campusAdminRestrictedPages.some((page) =>
              context.path.startsWith(page),
            )
          ) {
            Utils.error("You don't have permission to access this page");
            return "/admin/dashboard";
          }
        }
      }

      // Redirect logged in users from auth pages
      if (authRoutes.includes(context.path)) {
        const user = await getVerifiedUser();

        if (user) {
          if (
            ["admin", "system_admin", "campus_admin", "staff"].includes(
              user.role,
            )
          ) {
            return "/admin/dashboard";
          }
          return "/dashboard";
        }
      }

      return true;
    });

    // Update page title after navigation
    Router.afterEach((context) => {
      const titles = {
        "/": "Welcome",
        "/features": "Features",
        "/about": "About",
        "/contact": "Contact",
        "/faq": "FAQ",
        "/privacy": "Privacy Policy",
        "/terms": "Terms of Service",
        "/login": "Login",
        "/register": "Register",
        "/verify-email": "Verify Email",
        "/forgot-password": "Forgot Password",
        "/reset-password": "Reset Password",
        "/complete-profile": "Complete Profile",
        "/dashboard": "Dashboard",
        "/profile": "My Profile",
        "/events": "Events",
        "/announcements": "Announcements",
        "/messages": "Messages",
        "/leaderboard": "Leaderboard",
        "/rewards": "Rewards",
        "/id-card": "My ID Card",
        "/qr-scanner": "QR Scanner",
        "/admin/dashboard": "Admin Dashboard",
        "/admin/logs": "Security Center",
        "/admin/security-center": "Security Center",
        "/admin/alumni": "All Alumni",
        "/admin/events": "Event Management",
        "/admin/announcements": "Announcements",
        "/admin/organization": "Organization",
        "/admin/form-builder": "Form Builder",
        "/admin/gamification": "Gamification",
        "/admin/settings": "Settings",
      };

      this.setActiveAppSection(context.path);

      const branding = this.getBrandingSnapshot();
      const titleSuffix = branding.appTitle || "Alumni System";
      document.title = `${titles[context.path] || "Page"} - ${titleSuffix}`;
    });

    // Register routes
    Router.registerAll({
      // Public Routes
      "/": () => this.loadPage("pages/home.php"),
      "/features": (ctx) => this.loadPage("pages/home.php", ctx),
      "/about": (ctx) => this.loadPage("pages/home.php", ctx),
      "/contact": () => this.loadPage("pages/public/contact.php"),
      "/faq": () => this.loadPage("pages/public/faq.php"),
      "/privacy": () => this.loadPage("pages/public/privacy.php"),
      "/terms": () => this.loadPage("pages/public/terms.php"),
      "/login": () => this.loadPage("pages/auth/login.php"),
      "/register": () => this.loadPage("pages/auth/register-new.php"),
      "/verify-email": () => this.loadPage("pages/auth/verify-email.php"),
      "/forgot-password": () => this.loadPage("pages/auth/forgot-password.php"),
      "/reset-password": () => this.loadPage("pages/auth/reset-password.php"),
      "/complete-profile": () => this.loadPage("pages/complete-profile.php"),

      // Admin Login (public)
      "/admin/login": () => this.loadPage("pages/admin/login.php"),

      // Alumni Routes
      "/dashboard": () => this.loadPage("pages/alumni/dashboard.php"),
      "/profile": () => this.loadPage("pages/alumni/profile.php"),
      "/events": () => this.loadPage("pages/alumni/events.php"),
      "/events/:id": (ctx) =>
        this.loadPage("pages/alumni/event-detail.php", ctx),
      "/announcements": () => this.loadPage("pages/alumni/announcements.php"),
      "/announcements/:id": (ctx) =>
        this.loadPage("pages/alumni/announcement-detail.php", ctx),
      "/messages": () => this.loadPage("pages/alumni/messages.php"),
      "/messages/:id": (ctx) =>
        this.loadPage("pages/alumni/conversation.php", ctx),
      "/leaderboard": () => this.loadPage("pages/alumni/leaderboard.php"),
      "/rewards": () => this.loadPage("pages/alumni/rewards.php"),
      "/id-card": () => this.loadPage("pages/alumni/id-card.php"),
      "/qr-scanner": () => this.loadPage("pages/alumni/qr-scanner.php"),

      // Admin Routes
      "/admin/dashboard": (ctx) =>
        this.loadPage("pages/admin/dashboard.php", ctx),
      "/admin/security-center": (ctx) =>
        this.loadPage("pages/admin/logs.php", ctx),
      "/admin/logs": (ctx) => this.loadPage("pages/admin/logs.php", ctx),
      "/admin/alumni": (ctx) =>
        this.loadPage("pages/admin/alumni-list.php", ctx),
      "/admin/alumni-verification": (ctx) =>
        this.loadPage("pages/admin/alumni-verification.php", ctx),
      "/admin/alumni/:id": (ctx) =>
        this.loadPage("pages/admin/alumni-detail.php", ctx),
      "/admin/events": (ctx) => this.loadPage("pages/admin/events.php", ctx),
      "/admin/events/create": (ctx) =>
        this.loadPage("pages/admin/event-form.php", ctx),
      "/admin/events/:id": (ctx) =>
        this.loadPage("pages/admin/event-form.php", ctx),
      "/admin/events/:id/attendance": (ctx) =>
        this.loadPage("pages/admin/event-attendance.php", ctx),
      "/admin/announcements": (ctx) =>
        this.loadPage("pages/admin/announcements.php", ctx),
      "/admin/announcements/create": (ctx) =>
        this.loadPage("pages/admin/announcement-form.php", ctx),
      "/admin/announcements/:id": (ctx) =>
        this.loadPage("pages/admin/announcement-form.php", ctx),
      "/admin/organization": (ctx) =>
        this.loadPage("pages/admin/organization.php", ctx),
      "/admin/form-builder": (ctx) =>
        this.loadPage("pages/admin/form-builder.php", ctx),
      "/admin/gamification": (ctx) =>
        this.loadPage("pages/admin/gamification.php", ctx),
      "/admin/qr-scanner": (ctx) =>
        this.loadPage("pages/admin/qr-scanner.php", ctx),
      "/admin/alumni-id-card": (ctx) =>
        this.loadPage("pages/admin/alumni-id-card.php", ctx),
      "/admin/settings": (ctx) =>
        this.loadPage("pages/admin/settings/index.php", ctx),
      "/admin/settings/theme": (ctx) =>
        this.loadPage("pages/admin/settings/theme.php", ctx),
      "/admin/settings/site-content": (ctx) =>
        this.loadPage("pages/admin/settings/site-content.php", ctx),
      "/admin/settings/security": (ctx) =>
        this.loadPage("pages/admin/settings/security.php", ctx),
      "/admin/settings/email-templates": (ctx) =>
        this.loadPage("pages/admin/settings/email-templates.php", ctx),
      "/admin/users": (ctx) => this.loadPage("pages/admin/users.php", ctx),
      "/admin/campuses": (ctx) =>
        this.loadPage("pages/admin/campuses.php", ctx),

      // 404
      "*": () => this.render404(),
    });
  },

  /**
   * Load page content
   */
  async loadPage(pagePath, context = {}) {
    const appContainer = Utils.$("#app");
    const currentPath =
      context.path || (window.location.hash || "#/").replace(/^#/, "") || "/";
    await this.cleanupCurrentPage();
    this.setActiveAppSection(currentPath);
    if (document.body?.dataset?.appSection !== "admin") {
      this.disconnectAdminTableObserver();
    }

    // Show loading
    const hideLoading = Utils.showLoading(appContainer, "Loading...");

    try {
      const resolvedPagePath = this.resolvePagePath(pagePath);
      const response = await fetch(this.getCacheBustedPath(resolvedPagePath), {
        cache: "no-store",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      if (!response.ok) {
        throw new Error("Page not found");
      }

      const html = await response.text();
      appContainer.innerHTML = this.normalizePageAssetReferences(html);
      this.cacheBustPageStyles(appContainer);

      // Store context for page scripts
      window.__pageContext = context;

      // Execute page scripts
      const scripts = appContainer.querySelectorAll("script");
      scripts.forEach((script) => {
        const newScript = document.createElement("script");
        const scriptSource = script.getAttribute("src");
        if (scriptSource) {
          newScript.src = this.getCacheBustedPath(
            this.resolvePageAssetPath(scriptSource),
          );
        } else {
          newScript.textContent = script.textContent;
        }
        script.parentNode.replaceChild(newScript, script);
      });

      await this.hydrateAdminSidebar(context);
      await this.hydrateAlumniSidebar(context);

      if (this._themeSettings) {
        this.applyTheme(this._themeSettings);
      }

      this.applyBranding({ force: true });
      this.injectAdminLayoutGuard(appContainer);
      this.injectAdminModernDesign(appContainer);
      this.enhanceAdminTables(appContainer);
      this.reconcileAdminSidebarState();

      // Scroll to top
      window.scrollTo(0, 0);
    } catch (error) {
      console.error("Failed to load page:", error);
      this.render404();
    } finally {
      this.syncGlobalAlumniNotifications();
      hideLoading();
    }
  },

  /**
   * Render 404 page
   */
  render404() {
    Utils.render(
      "#app",
      `
            <div class="min-h-screen flex items-center justify-center">
                <div class="text-center">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">404</h1>
                    <p class="text-gray-600 mb-6">Page not found</p>
                    <a href="#/" class="btn btn-primary">Go Home</a>
                </div>
            </div>
        `,
    );
  },

  /**
   * Get current page context
   */
  getContext() {
    return window.__pageContext || {};
  },

  /**
   * Give the outgoing page a chance to stop timers, calls, scanners, and media.
   */
  async cleanupCurrentPage() {
    const cleanup = window.__pageCleanup;
    window.__pageCleanup = null;

    if (typeof cleanup === "function") {
      try {
        await cleanup({ reason: "route-change" });
      } catch (error) {
        console.warn("Page cleanup failed:", error);
      }
    }

    this.stopPageMedia();
  },

  /**
   * Last-resort media cleanup for camera/microphone streams attached to DOM nodes.
   */
  stopPageMedia(root = document) {
    try {
      root.querySelectorAll("video, audio").forEach((media) => {
        const stream = media.srcObject;
        if (stream && typeof stream.getTracks === "function") {
          stream.getTracks().forEach((track) => track.stop());
        }
        media.pause?.();
        media.srcObject = null;
        media.removeAttribute("src");
        media.load?.();
      });
    } catch (error) {
      console.warn("Media cleanup failed:", error);
    }
  },

  getCurrentRoutePath() {
    return (window.location.hash || "#/").replace(/^#/, "") || "/";
  },

  isMessagesRoute(path = this.getCurrentRoutePath()) {
    const normalizedPath = String(path || "").trim();
    return (
      normalizedPath === "/messages" || normalizedPath.startsWith("/messages/")
    );
  },

  getGlobalNotificationUser() {
    return (
      (typeof Auth !== "undefined" && Auth.user) ||
      (typeof API !== "undefined" && API.getUser && API.getUser()) ||
      null
    );
  },

  isAlumniNotificationUser(user = this.getGlobalNotificationUser()) {
    const role = String(user?.role || "").toLowerCase();
    return role === "alumni";
  },

  syncGlobalAlumniNotifications() {
    const token =
      typeof API !== "undefined" && API.getToken ? API.getToken() : "";
    const user = this.getGlobalNotificationUser();
    const shouldRun = Boolean(token && this.isAlumniNotificationUser(user));

    if (!shouldRun) {
      this.stopGlobalAlumniNotifications();
      return;
    }

    this.startGlobalAlumniNotifications();

    if (this.isMessagesRoute()) {
      this.stopGlobalRingtone();
      this.closeGlobalCallNotification();
      this.hideGlobalCallOverlay();
    }
  },

  startGlobalAlumniNotifications() {
    if (this._globalNotificationInterval) {
      return;
    }

    this._globalNotificationSnapshotReady = false;
    this._messageNotificationSnapshot = {};
    this._lastMessageNotificationKey = "";
    this._lastIncomingCallId = "";
    this.ensureNotificationAudioUnlock();
    this.requestBrowserNotificationPermission();
    this.pollGlobalAlumniNotifications();
    this._globalNotificationInterval = window.setInterval(
      () => this.pollGlobalAlumniNotifications(),
      4000,
    );
  },

  stopGlobalAlumniNotifications() {
    if (this._globalNotificationInterval) {
      window.clearInterval(this._globalNotificationInterval);
      this._globalNotificationInterval = null;
    }

    this._globalNotificationPollBusy = false;
    this._globalNotificationSnapshotReady = false;
    this._messageNotificationSnapshot = null;
    this._lastMessageNotificationKey = "";
    this._lastIncomingCallId = "";
    this.stopGlobalRingtone();
    this.closeGlobalCallNotification();
    this.hideGlobalCallOverlay();
    this._globalIncomingCall = null;
  },

  async pollGlobalAlumniNotifications() {
    if (this._globalNotificationPollBusy) {
      return;
    }

    const token =
      typeof API !== "undefined" && API.getToken ? API.getToken() : "";
    if (!token || !this.isAlumniNotificationUser()) {
      this.stopGlobalAlumniNotifications();
      return;
    }

    this._globalNotificationPollBusy = true;

    try {
      const [callsResult, conversationsResult] = await Promise.allSettled([
        API.messaging.getIncomingCalls(),
        API.messaging.getConversations(),
      ]);

      if (callsResult.status === "fulfilled") {
        this.handleGlobalIncomingCalls(
          this.extractArrayPayload(callsResult.value),
        );
      } else {
        this.handleGlobalNotificationError(callsResult.reason);
      }

      if (conversationsResult.status === "fulfilled") {
        this.handleGlobalMessageNotifications(
          this.extractArrayPayload(conversationsResult.value),
        );
      } else {
        this.handleGlobalNotificationError(conversationsResult.reason);
      }
    } catch (error) {
      this.handleGlobalNotificationError(error);
    } finally {
      this._globalNotificationPollBusy = false;
    }
  },

  extractArrayPayload(response) {
    const payload = response?.data;
    if (Array.isArray(payload)) {
      return payload;
    }

    if (Array.isArray(payload?.items)) {
      return payload.items;
    }

    if (Array.isArray(payload?.conversations)) {
      return payload.conversations;
    }

    if (Array.isArray(payload?.calls)) {
      return payload.calls;
    }

    return [];
  },

  handleGlobalNotificationError(error) {
    if (error?.status === 401 || error?.code === "unauthorized") {
      this.stopGlobalAlumniNotifications();
      return;
    }

    console.warn("Global alumni notifications poll failed:", error);
  },

  handleGlobalIncomingCalls(calls = []) {
    const incoming =
      calls.find((call) => String(call?.status || "ringing") === "ringing") ||
      calls[0] ||
      null;

    if (!incoming) {
      this._lastIncomingCallId = "";
      this.stopGlobalRingtone();
      this.closeGlobalCallNotification();
      this.hideGlobalCallOverlay();
      this._globalIncomingCall = null;
      return;
    }

    const callId = String(incoming.id || incoming.call_id || "").trim();
    if (!callId) {
      return;
    }

    if (this.isMessagesRoute()) {
      this._lastIncomingCallId = callId;
      this.stopGlobalRingtone();
      this.closeGlobalCallNotification();
      this.hideGlobalCallOverlay();
      this._globalIncomingCall = null;
      return;
    }

    this.startGlobalRingtone(callId);

    this._globalIncomingCall = incoming;
    this.showGlobalCallOverlay(incoming);

    if (this._lastIncomingCallId === callId) {
      return;
    }

    this._lastIncomingCallId = callId;

    const callerName =
      String(
        incoming.caller_name || incoming.conversation_name || "Alumni",
      ).trim() || "Alumni";
    const callType =
      String(incoming.call_type || "audio").toLowerCase() === "video"
        ? "video"
        : "audio";

    this.showInAppNotification(
      `Incoming ${callType} call`,
      `${callerName} is calling you. Answer or decline from the call banner.`,
    );

    this.closeGlobalCallNotification();
    this._globalCallNotification = this.showBrowserNotification(
      `Incoming ${callType} call`,
      `${callerName} is calling you.`,
      {
        tag: `alumni-call-${callId}`,
        requireInteraction: true,
      },
    );
  },

  handleGlobalMessageNotifications(conversations = []) {
    const user = this.getGlobalNotificationUser() || {};
    const currentUserId = Number(user.id || 0);
    const previousSnapshot = this._messageNotificationSnapshot || {};
    const nextSnapshot = {};
    const isFirstPoll = !this._globalNotificationSnapshotReady;
    let candidate = null;

    conversations.forEach((conversation) => {
      const conversationId = String(
        conversation?.id || conversation?.conversation_id || "",
      ).trim();

      if (!conversationId) {
        return;
      }

      const unread = Number(conversation.unread_count || 0);
      const signature = [
        conversation.last_message_time ||
          conversation.last_message_at ||
          conversation.updated_at ||
          conversation.created_at ||
          "",
        conversation.last_sender_id || "",
        conversation.last_message_type || "",
        conversation.last_message || "",
      ].join("|");

      nextSnapshot[conversationId] = { unread, signature };

      const previous = previousSnapshot[conversationId];
      const previousUnread = Number(previous?.unread || 0);
      const lastSenderId = Number(conversation.last_sender_id || 0);
      const isFromAnotherUser = !lastSenderId || lastSenderId !== currentUserId;
      const isNewUnread =
        !isFirstPoll &&
        unread > 0 &&
        isFromAnotherUser &&
        (!previous ||
          unread > previousUnread ||
          (previous.signature !== signature && unread >= previousUnread));

      if (isNewUnread && !candidate) {
        candidate = {
          conversation,
          conversationId,
          signature,
        };
      }
    });

    this._messageNotificationSnapshot = nextSnapshot;
    this._globalNotificationSnapshotReady = true;

    if (!candidate || this.isMessagesRoute()) {
      return;
    }

    const notificationKey = `${candidate.conversationId}:${candidate.signature}`;
    if (this._lastMessageNotificationKey === notificationKey) {
      return;
    }

    this._lastMessageNotificationKey = notificationKey;
    const details = this.getGlobalConversationNotificationDetails(
      candidate.conversation,
      currentUserId,
    );

    this.playMessageNotificationSound();
    this.showInAppNotification(
      "New message",
      `${details.name}: ${details.preview}`,
    );
    this.showBrowserNotification(
      `New message from ${details.name}`,
      details.preview,
      {
        tag: `alumni-message-${candidate.conversationId}`,
      },
    );
  },

  getGlobalConversationNotificationDetails(conversation, currentUserId = 0) {
    const participants = Array.isArray(conversation?.participants)
      ? conversation.participants
      : [];
    const otherParticipant =
      participants.find(
        (participant) =>
          Number(participant?.id || 0) !== Number(currentUserId || 0),
      ) ||
      participants[0] ||
      {};
    const name =
      String(
        conversation?.display_name ||
          conversation?.name ||
          otherParticipant.name ||
          "Alumni",
      ).trim() || "Alumni";
    const messageType = String(
      conversation?.last_message_type || "text",
    ).toLowerCase();
    let preview = String(conversation?.last_message || "").trim();

    if (!preview && messageType !== "text") {
      preview = "Sent an attachment";
    }

    if (!preview) {
      preview = "Sent a new message";
    }

    if (preview.length > 110) {
      preview = `${preview.slice(0, 107)}...`;
    }

    return { name, preview };
  },

  getNotificationPreferences() {
    if (this._notificationPreferences) {
      return this._notificationPreferences;
    }

    const defaults = {
      soundEnabled: true,
      messageTone: "chime",
      ringtoneTone: "pulse",
      volume: 1,
      desktopEnabled: true,
    };

    let stored = {};
    if (typeof localStorage !== "undefined") {
      try {
        stored = JSON.parse(
          localStorage.getItem("alumniNotificationPreferences") || "{}",
        );
      } catch {
        stored = {};
      }
    }

    this._notificationPreferences = Object.assign({}, defaults, stored);
    return this._notificationPreferences;
  },

  setNotificationPreferences(next = {}) {
    const current = this.getNotificationPreferences();
    this._notificationPreferences = Object.assign({}, current, next);

    if (typeof localStorage !== "undefined") {
      try {
        localStorage.setItem(
          "alumniNotificationPreferences",
          JSON.stringify(this._notificationPreferences),
        );
      } catch {
        // Ignore storage failures.
      }
    }

    return this._notificationPreferences;
  },

  getNotificationToneOptions() {
    return {
      message: [
        { value: "chime", label: "Chime" },
        { value: "soft", label: "Soft" },
        { value: "pop", label: "Pop" },
      ],
      ringtone: [
        { value: "pulse", label: "Pulse" },
        { value: "classic", label: "Classic" },
        { value: "digital", label: "Digital" },
      ],
    };
  },

  getNotificationTonePresets() {
    if (this._notificationTonePresets) {
      return this._notificationTonePresets;
    }

    this._notificationTonePresets = {
      message: {
        chime: [
          { frequency: 880, start: 0, duration: 0.1, gain: 0.035 },
          { frequency: 1175, start: 0.13, duration: 0.12, gain: 0.03 },
        ],
        soft: [
          { frequency: 620, start: 0, duration: 0.14, gain: 0.022 },
          { frequency: 740, start: 0.16, duration: 0.14, gain: 0.02 },
        ],
        pop: [
          { frequency: 980, start: 0, duration: 0.08, gain: 0.034 },
          { frequency: 980, start: 0.12, duration: 0.08, gain: 0.03 },
        ],
      },
      ringtone: {
        pulse: [
          { frequency: 659, start: 0, duration: 0.18, gain: 0.045 },
          { frequency: 880, start: 0.22, duration: 0.18, gain: 0.045 },
          { frequency: 659, start: 0.52, duration: 0.18, gain: 0.04 },
          { frequency: 880, start: 0.74, duration: 0.18, gain: 0.04 },
        ],
        classic: [
          { frequency: 440, start: 0, duration: 0.2, gain: 0.05 },
          { frequency: 660, start: 0.24, duration: 0.2, gain: 0.05 },
          { frequency: 440, start: 0.6, duration: 0.2, gain: 0.045 },
          { frequency: 660, start: 0.84, duration: 0.2, gain: 0.045 },
        ],
        digital: [
          { frequency: 1040, start: 0, duration: 0.1, gain: 0.04 },
          { frequency: 820, start: 0.18, duration: 0.1, gain: 0.035 },
          { frequency: 1040, start: 0.36, duration: 0.1, gain: 0.04 },
        ],
      },
    };

    return this._notificationTonePresets;
  },

  getNotificationTonePreset(type = "message", key = "") {
    const presets = this.getNotificationTonePresets();
    const group = presets[type] || {};
    if (group[key]) {
      return group[key];
    }

    const fallback = type === "ringtone" ? "pulse" : "chime";
    return group[fallback] || [];
  },

  getNotificationVolume() {
    const volume = Number(this.getNotificationPreferences().volume);
    if (Number.isFinite(volume)) {
      return Math.min(1, Math.max(0, volume));
    }
    return 1;
  },

  isNotificationSoundEnabled() {
    return this.getNotificationPreferences().soundEnabled !== false;
  },

  isDesktopNotificationEnabled() {
    return this.getNotificationPreferences().desktopEnabled !== false;
  },

  previewNotificationSound(type = "message", presetKey = "") {
    const notes = this.getNotificationTonePreset(type, presetKey);
    this.playNotificationTone(notes, { volume: this.getNotificationVolume() });
  },

  setPendingCallAction(action) {
    if (typeof localStorage === "undefined") {
      return;
    }

    if (!action) {
      localStorage.removeItem("alumniPendingCallAction");
      return;
    }

    try {
      localStorage.setItem("alumniPendingCallAction", JSON.stringify(action));
    } catch {
      // Ignore storage failures.
    }
  },

  consumePendingCallAction() {
    if (typeof localStorage === "undefined") {
      return null;
    }

    const raw = localStorage.getItem("alumniPendingCallAction");
    if (!raw) {
      return null;
    }

    localStorage.removeItem("alumniPendingCallAction");
    try {
      return JSON.parse(raw);
    } catch {
      return null;
    }
  },

  ensureGlobalCallOverlay() {
    if (this._globalCallOverlay || typeof document === "undefined") {
      return this._globalCallOverlay || null;
    }

    const overlay = document.createElement("div");
    overlay.id = "globalCallOverlay";
    overlay.className = "global-call-overlay";
    overlay.setAttribute("aria-live", "polite");
    overlay.innerHTML = `
      <div class="global-call-card" role="dialog" aria-label="Incoming call">
        <div class="global-call-avatar" id="globalCallAvatar">A</div>
        <div class="global-call-body">
          <div class="global-call-title" id="globalCallTitle">Incoming call</div>
          <div class="global-call-subtitle" id="globalCallSubtitle">An alumni is calling</div>
        </div>
        <div class="global-call-actions">
          <button type="button" class="btn btn-primary" data-call-action="accept">Answer</button>
          <button type="button" class="btn btn-secondary" data-call-action="decline">Decline</button>
        </div>
      </div>
    `;

    overlay.addEventListener("click", (event) => {
      const actionButton = event.target.closest("[data-call-action]");
      if (!actionButton) {
        return;
      }

      const action = actionButton.dataset.callAction;
      if (action === "accept") {
        this.acceptGlobalIncomingCall();
        return;
      }

      if (action === "decline") {
        this.declineGlobalIncomingCall();
      }
    });

    document.body.appendChild(overlay);
    this.injectGlobalCallOverlayStyles();
    this._globalCallOverlay = overlay;
    return overlay;
  },

  showGlobalCallOverlay(call) {
    const overlay = this.ensureGlobalCallOverlay();
    if (!overlay) {
      return;
    }

    const callId = String(call?.id || call?.call_id || "").trim();
    if (!callId) {
      return;
    }

    const callerName =
      String(call?.caller_name || call?.conversation_name || "Alumni").trim() ||
      "Alumni";
    const callType =
      String(call?.call_type || "audio").toLowerCase() === "video"
        ? "Video"
        : "Audio";

    overlay.dataset.callId = callId;

    const title = overlay.querySelector("#globalCallTitle");
    const subtitle = overlay.querySelector("#globalCallSubtitle");
    const avatar = overlay.querySelector("#globalCallAvatar");

    if (title) {
      title.textContent = `Incoming ${callType} call`;
    }

    if (subtitle) {
      subtitle.textContent = `${callerName} is calling you`;
    }

    if (avatar) {
      avatar.textContent = this.getCallAvatarLabel(callerName);
    }

    overlay.classList.add("is-visible");
  },

  hideGlobalCallOverlay() {
    if (!this._globalCallOverlay) {
      return;
    }

    this._globalCallOverlay.classList.remove("is-visible");
  },

  acceptGlobalIncomingCall() {
    const callId = String(
      this._globalIncomingCall?.id ||
        this._globalIncomingCall?.call_id ||
        this._globalCallOverlay?.dataset?.callId ||
        "",
    ).trim();

    if (!callId) {
      return;
    }

    this.setPendingCallAction({
      callId,
      action: "accept",
      ts: Date.now(),
    });
    this.hideGlobalCallOverlay();
    this.stopGlobalRingtone(callId);
    this.navigateToMessages();
  },

  async declineGlobalIncomingCall() {
    const callId = String(
      this._globalIncomingCall?.id ||
        this._globalIncomingCall?.call_id ||
        this._globalCallOverlay?.dataset?.callId ||
        "",
    ).trim();

    if (!callId) {
      return;
    }

    if (typeof API !== "undefined" && API.messaging?.respondCall) {
      try {
        await API.messaging.respondCall(callId, "declined");
      } catch (error) {
        console.warn("Failed to decline incoming call:", error);
      }
    }

    this.hideGlobalCallOverlay();
    this.stopGlobalRingtone(callId);
  },

  getCallAvatarLabel(name = "") {
    if (
      typeof Utils !== "undefined" &&
      typeof Utils.getInitials === "function"
    ) {
      return Utils.getInitials(name || "?") || "?";
    }

    const parts = String(name || "?")
      .trim()
      .split(/\s+/)
      .filter(Boolean);
    if (!parts.length) {
      return "?";
    }

    return parts
      .map((part) => part[0])
      .join("")
      .toUpperCase()
      .slice(0, 2);
  },

  injectGlobalCallOverlayStyles() {
    if (
      this._globalCallOverlayStylesInjected ||
      typeof document === "undefined"
    ) {
      return;
    }

    const style = document.createElement("style");
    style.id = "global-call-overlay-styles";
    style.textContent = `
      .global-call-overlay {
        position: fixed;
        right: 18px;
        bottom: 20px;
        z-index: 2600;
        display: none;
        pointer-events: none;
      }

      .global-call-overlay.is-visible {
        display: block;
      }

      .global-call-card {
        display: grid;
        grid-template-columns: auto minmax(0, 1fr) auto;
        align-items: center;
        gap: 12px;
        padding: 12px 14px;
        border-radius: 16px;
        background: #0f172a;
        color: #f8fafc;
        border: 1px solid rgba(255, 255, 255, 0.16);
        box-shadow: 0 18px 40px -20px rgba(15, 23, 42, 0.85);
        pointer-events: auto;
        min-width: 260px;
      }

      .global-call-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(148, 163, 184, 0.2);
        color: #f8fafc;
        font-weight: 800;
        font-size: 0.9rem;
      }

      .global-call-title {
        font-weight: 800;
        font-size: 0.98rem;
      }

      .global-call-subtitle {
        font-size: 0.85rem;
        color: rgba(226, 232, 240, 0.82);
      }

      .global-call-actions {
        display: flex;
        align-items: center;
        gap: 6px;
      }

      .global-call-actions .btn {
        padding: 6px 10px;
        font-size: 0.78rem;
        white-space: nowrap;
      }

      @media (max-width: 640px) {
        .global-call-overlay {
          right: 12px;
          left: 12px;
          bottom: 12px;
        }

        .global-call-card {
          grid-template-columns: auto 1fr;
        }

        .global-call-actions {
          grid-column: 1 / -1;
          justify-content: flex-end;
        }
      }
    `;

    document.head.appendChild(style);
    this._globalCallOverlayStylesInjected = true;
  },

  showInAppNotification(title, body = "") {
    if (typeof Utils === "undefined" || typeof Utils.info !== "function") {
      return null;
    }

    const message = body ? `${title}: ${body}` : title;
    const toast = Utils.info(message);

    if (toast && typeof toast.addEventListener === "function") {
      toast.style.cursor = "pointer";
      toast.addEventListener("click", () => this.navigateToMessages());
    }

    return toast;
  },

  requestBrowserNotificationPermission(force = false) {
    if (!this.isDesktopNotificationEnabled()) {
      return;
    }

    if (
      (!force && this._notificationPermissionAsked) ||
      typeof window === "undefined" ||
      !("Notification" in window) ||
      Notification.permission !== "default"
    ) {
      return;
    }

    this._notificationPermissionAsked = true;

    try {
      const request = Notification.requestPermission();
      if (request && typeof request.catch === "function") {
        request.catch(() => {});
      }
    } catch {
      // Browsers that require a user gesture will simply keep in-app toasts active.
    }
  },

  showBrowserNotification(title, body, options = {}) {
    if (
      typeof window === "undefined" ||
      !("Notification" in window) ||
      !this.isDesktopNotificationEnabled() ||
      Notification.permission !== "granted"
    ) {
      return null;
    }

    try {
      const notification = new Notification(title, {
        body,
        tag: options.tag || title,
        renotify: true,
        requireInteraction: options.requireInteraction === true,
        silent: true,
        icon: this.getNotificationIconUrl(),
      });

      notification.onclick = () => {
        window.focus();
        notification.close();
        if (typeof options.onClick === "function") {
          options.onClick();
          return;
        }
        this.navigateToMessages();
      };

      if (!options.requireInteraction) {
        window.setTimeout(() => notification.close(), 8000);
      }

      return notification;
    } catch (error) {
      console.warn("Unable to show browser notification:", error);
      return null;
    }
  },

  closeGlobalCallNotification() {
    if (!this._globalCallNotification) {
      return;
    }

    try {
      this._globalCallNotification.close();
    } catch {
      // Notification may already be closed by the browser.
    }

    this._globalCallNotification = null;
  },

  getNotificationIconUrl() {
    try {
      return new URL("assets/images/logo.svg", document.baseURI).href;
    } catch {
      return "assets/images/logo.svg";
    }
  },

  navigateToMessages() {
    if (
      typeof Router !== "undefined" &&
      typeof Router.navigate === "function"
    ) {
      Router.navigate("/messages");
      return;
    }

    window.location.hash = "#/messages";
  },

  ensureNotificationAudioUnlock() {
    if (this._notificationAudioUnlockBound || typeof document === "undefined") {
      return;
    }

    this._notificationAudioUnlockBound = true;
    const unlock = () => {
      const context = this.getNotificationAudioContext();
      if (context && context.state === "suspended" && context.resume) {
        context.resume().catch(() => {});
      }

      document.removeEventListener("pointerdown", unlock);
      document.removeEventListener("touchstart", unlock);
      document.removeEventListener("keydown", unlock);
    };

    document.addEventListener("pointerdown", unlock, { passive: true });
    document.addEventListener("touchstart", unlock, { passive: true });
    document.addEventListener("keydown", unlock);
  },

  getNotificationAudioContext() {
    if (typeof window === "undefined") {
      return null;
    }

    const AudioContextClass = window.AudioContext || window.webkitAudioContext;
    if (!AudioContextClass) {
      return null;
    }

    if (!this._notificationAudioContext) {
      try {
        this._notificationAudioContext = new AudioContextClass();
      } catch (error) {
        console.warn("Notification audio is unavailable:", error);
        return null;
      }
    }

    return this._notificationAudioContext;
  },

  playNotificationTone(notes = [], options = {}) {
    const context = this.getNotificationAudioContext();
    if (!context || !notes.length) {
      return;
    }

    const volume = Number(options.volume);
    const volumeScale = Number.isFinite(volume)
      ? Math.min(1, Math.max(0, volume))
      : 1;
    if (volumeScale <= 0) {
      return;
    }

    if (context.state === "suspended" && context.resume) {
      context.resume().catch(() => {});
    }

    const baseTime = context.currentTime + 0.02;

    notes.forEach((note) => {
      const start = baseTime + Number(note.start || 0);
      const duration = Math.max(0.05, Number(note.duration || 0.12));
      const end = start + duration;
      const oscillator = context.createOscillator();
      const gain = context.createGain();
      const targetGain = Math.max(
        0.0001,
        Number(note.gain || 0.04) * volumeScale,
      );

      oscillator.type = note.type || "sine";
      oscillator.frequency.setValueAtTime(Number(note.frequency || 880), start);
      gain.gain.setValueAtTime(0.0001, start);
      gain.gain.exponentialRampToValueAtTime(targetGain, start + 0.02);
      gain.gain.exponentialRampToValueAtTime(0.0001, end);

      oscillator.connect(gain);
      gain.connect(context.destination);
      oscillator.start(start);
      oscillator.stop(end + 0.03);
    });
  },

  playMessageNotificationSound() {
    if (!this.isNotificationSoundEnabled()) {
      return;
    }

    const settings = this.getNotificationPreferences();
    this.playNotificationTone(
      this.getNotificationTonePreset("message", settings.messageTone),
      {
        volume: this.getNotificationVolume(),
      },
    );
  },

  playRingtonePulse() {
    if (!this.isNotificationSoundEnabled()) {
      return;
    }

    const settings = this.getNotificationPreferences();
    this.playNotificationTone(
      this.getNotificationTonePreset("ringtone", settings.ringtoneTone),
      {
        volume: this.getNotificationVolume(),
      },
    );
  },

  startGlobalRingtone(callId) {
    const normalizedCallId = String(callId || "").trim();
    if (
      this._globalRingtoneInterval &&
      (!normalizedCallId || this._activeRingingCallId === normalizedCallId)
    ) {
      return;
    }

    this.stopGlobalRingtone();
    this._activeRingingCallId = normalizedCallId;
    this.playRingtonePulse();
    this._globalRingtoneInterval = window.setInterval(
      () => this.playRingtonePulse(),
      1800,
    );
  },

  stopGlobalRingtone(callId = "") {
    const normalizedCallId = String(callId || "").trim();
    if (
      normalizedCallId &&
      this._activeRingingCallId &&
      this._activeRingingCallId !== normalizedCallId
    ) {
      return;
    }

    if (this._globalRingtoneInterval) {
      window.clearInterval(this._globalRingtoneInterval);
      this._globalRingtoneInterval = null;
    }

    this._activeRingingCallId = "";
  },

  /**
   * Determine whether a route belongs to authenticated alumni pages.
   */
  isAlumniRoute(path) {
    const normalizedPath = String(path || "").trim();
    if (!normalizedPath) {
      return false;
    }

    return [
      "/dashboard",
      "/profile",
      "/id-card",
      "/events",
      "/announcements",
      "/messages",
      "/leaderboard",
      "/rewards",
      "/qr-scanner",
    ].some(
      (route) =>
        normalizedPath === route || normalizedPath.startsWith(`${route}/`),
    );
  },

  /**
   * Normalize nested alumni route paths to their base sidebar entries.
   */
  getAlumniSidebarPath(path) {
    const normalizedPath = String(path || "").trim();
    if (!normalizedPath) {
      return "/dashboard";
    }

    if (normalizedPath.startsWith("/events/")) {
      return "/events";
    }

    if (normalizedPath.startsWith("/announcements/")) {
      return "/announcements";
    }

    if (normalizedPath.startsWith("/messages/")) {
      return "/messages";
    }

    return normalizedPath;
  },

  /**
   * Sidebar links for authenticated alumni routes.
   */
  getAlumniSidebarLinks() {
    return [
      {
        key: "dashboard",
        label: "Dashboard",
        href: "#/dashboard",
        match: "/dashboard",
        icon: '<rect x="3" y="3" width="7" height="7" /><rect x="14" y="3" width="7" height="7" /><rect x="14" y="14" width="7" height="7" /><rect x="3" y="14" width="7" height="7" />',
      },
      {
        key: "profile",
        label: "My Profile",
        href: "#/profile",
        match: "/profile",
        icon: '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" />',
      },
      {
        key: "id-card",
        label: "My ID Card",
        href: "#/id-card",
        match: "/id-card",
        icon: '<rect x="3" y="4" width="18" height="16" rx="2" ry="2" /><line x1="7" y1="8" x2="17" y2="8" /><line x1="7" y1="12" x2="17" y2="12" /><line x1="7" y1="16" x2="11" y2="16" />',
      },
      {
        key: "qr-scanner",
        label: "QR Scanner",
        href: "#/qr-scanner",
        match: "/qr-scanner",
        icon: '<path d="M4 7V5a1 1 0 0 1 1-1h2" /><path d="M17 4h2a1 1 0 0 1 1 1v2" /><path d="M20 17v2a1 1 0 0 1-1 1h-2" /><path d="M7 20H5a1 1 0 0 1-1-1v-2" /><path d="M7 12h10" />',
      },
      {
        key: "events",
        label: "Events",
        href: "#/events",
        match: "/events",
        icon: '<rect x="3" y="4" width="18" height="18" rx="2" ry="2" /><line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" /><line x1="3" y1="10" x2="21" y2="10" />',
      },
      {
        key: "announcements",
        label: "Announcements",
        href: "#/announcements",
        match: "/announcements",
        icon: '<path d="M22 17H2a3 3 0 0 0 3-3V9a7 7 0 0 1 14 0v5a3 3 0 0 0 3 3z" /><path d="M9.5 17v.5a2.5 2.5 0 0 0 5 0V17" />',
      },
      {
        key: "messages",
        label: "Messages",
        href: "#/messages",
        match: "/messages",
        icon: '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />',
      },
      {
        key: "leaderboard",
        label: "Leaderboard",
        href: "#/leaderboard",
        match: "/leaderboard",
        icon: '<path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6" /><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18" /><path d="M4 22h16" /><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22" /><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22" /><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z" />',
      },
      {
        key: "rewards",
        label: "Rewards",
        href: "#/rewards",
        match: "/rewards",
        icon: '<circle cx="12" cy="8" r="7" /><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88" />',
      },
    ];
  },

  /**
   * Render and bind shared alumni sidebar shell.
   */
  async hydrateAlumniSidebar(context = {}) {
    const path =
      context.path || (window.location.hash || "#/dashboard").replace(/^#/, "");
    if (!this.isAlumniRoute(path)) {
      return;
    }

    const targetSidebar = Utils.$("#sidebar");
    if (!targetSidebar) {
      return;
    }

    try {
      this.renderAlumniSidebar(targetSidebar, { currentPath: path });
      const layout = targetSidebar.closest(".dashboard-layout");
      if (
        layout &&
        this._alumniSidebarDesktopCollapsed &&
        !window.matchMedia("(max-width: 980px)").matches
      ) {
        layout.classList.add("sidebar-collapsed");
      }

      this.ensureAlumniSidebarToggle();
      this.closeAlumniSidebar();
      this.ensureAlumniSidebarBackdrop();
      this.reconcileAlumniSidebarState();
      this.updateAlumniSidebarToggleState();
    } catch (error) {
      console.warn("Failed to hydrate alumni sidebar:", error);
    }
  },

  /**
   * Render alumni sidebar with unified navigation and profile summary.
   */
  renderAlumniSidebar(sidebar, options = {}) {
    if (!sidebar) {
      return;
    }

    const activePath = this.getAlumniSidebarPath(options.currentPath);
    const links = this.getAlumniSidebarLinks();

    sidebar.innerHTML = `
      <div class="sidebar-header alumni-shell-header">
        <div class="alumni-shell-brand-row">
          <div class="sidebar-brand">
            <div class="sidebar-brand-name" data-branding="site-name">Alumni Portal</div>
            <div class="sidebar-brand-subtitle" data-branding="institution">Mindoro State University</div>
          </div>
          <button type="button" class="alumni-sidebar-action sidebar-toggle" aria-label="Hide sidebar" title="Hide sidebar">
            ${this.getAdminSidebarToggleIconMarkup("close")}
          </button>
        </div>

        <div class="alumni-shell-user">
          <div class="avatar avatar-md bg-primary alumni-shell-avatar">
            <img src="" alt="" id="sidebarAvatar" onerror="this.style.display = 'none'" />
            <span id="sidebarInitials">A</span>
          </div>
          <div class="alumni-shell-meta">
            <div class="font-medium text-white" id="sidebarName">Alumni</div>
            <div class="text-xs opacity-75" id="sidebarAlumniId">Alumni Member</div>
          </div>
        </div>
      </div>

      <nav class="sidebar-nav">
        ${links
          .map((link) => {
            const isActive =
              activePath === link.match ||
              (link.match && activePath.startsWith(`${link.match}/`));

            return `
              <a href="${link.href}" class="sidebar-link ${isActive ? "active" : ""}" data-match="${Utils.escapeHtml(link.match)}">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                  ${link.icon}
                </svg>
                <span>${Utils.escapeHtml(link.label)}</span>
                ${link.badge ? `<span class="sidebar-badge">${Utils.escapeHtml(link.badge)}</span>` : ""}
              </a>
            `;
          })
          .join("")}
      </nav>

      <div class="sidebar-footer">
        <button type="button" class="sidebar-link text-danger w-100 alumni-shell-logout">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
          Logout
        </button>
      </div>
    `;

    this.populateAlumniSidebarIdentity(sidebar);

    const logoutButton = sidebar.querySelector(".alumni-shell-logout");
    if (logoutButton && !logoutButton.dataset.boundAlumniShellLogout) {
      logoutButton.dataset.boundAlumniShellLogout = "true";
      logoutButton.addEventListener("click", () => Auth.logout());
    }
  },

  /**
   * Populate alumni identity section in the shared sidebar.
   */
  populateAlumniSidebarIdentity(sidebar) {
    const user = API.getUser() || Auth.user || {};
    const userName = String(user.name || "Alumni Member").trim();
    const userInitials = Utils.getInitials(userName || "A");

    const nameElement = sidebar.querySelector("#sidebarName");
    const alumniIdElement = sidebar.querySelector("#sidebarAlumniId");
    const initialsElement = sidebar.querySelector("#sidebarInitials");
    const avatarImage = sidebar.querySelector("#sidebarAvatar");

    if (nameElement) {
      nameElement.textContent = userName || "Alumni Member";
    }

    if (alumniIdElement) {
      alumniIdElement.textContent =
        String(user.alumni_id || "Alumni Member").trim() || "Alumni Member";
    }

    if (initialsElement) {
      initialsElement.textContent = userInitials || "A";
    }

    if (avatarImage) {
      const profileImage = String(user.profile_image || "").trim();
      const showInitials = () => {
        if (initialsElement) {
          initialsElement.style.display = "inline-flex";
        }
        avatarImage.removeAttribute("src");
        avatarImage.style.display = "none";
      };

      const showImage = () => {
        if (initialsElement) {
          initialsElement.style.display = "none";
        }
        avatarImage.style.display = "block";
      };

      if (profileImage) {
        const candidates = API.getAssetUrlCandidates
          ? API.getAssetUrlCandidates(profileImage)
          : [profileImage];
        let candidateIndex = 0;
        const showNextCandidate = () => {
          if (candidateIndex >= candidates.length) {
            showInitials();
            return;
          }
          avatarImage.src = candidates[candidateIndex];
          candidateIndex += 1;
          showImage();
        };

        avatarImage.onload = showImage;
        avatarImage.onerror = showNextCandidate;
        showNextCandidate();
      } else {
        showInitials();
      }
    }
  },

  /**
   * Load the shared admin sidebar template into pages that expose a sidebar slot.
   */
  async hydrateAdminSidebar(context = {}) {
    const path =
      context.path ||
      (window.location.hash || "#/admin/dashboard").replace(/^#/, "");
    if (!path.startsWith("/admin") || path === "/admin/login") {
      return;
    }

    const targetSidebar = Utils.$("#sidebar") || Utils.$("#adminSidebar");
    if (!targetSidebar) {
      return;
    }

    try {
      this.renderAdminSidebar(targetSidebar, { currentPath: path });
      const layout = targetSidebar.closest(".dashboard-layout");
      if (
        layout &&
        this._adminSidebarDesktopCollapsed &&
        !window.matchMedia("(max-width: 980px)").matches
      ) {
        layout.classList.add("sidebar-collapsed");
      }

      this.ensureAdminSidebarToggle();
      this.ensureAdminTopbarUser();
      this.closeAdminSidebar();
      this.ensureAdminSidebarBackdrop();
      this.updateAdminSidebarToggleState();
    } catch (error) {
      console.warn("Failed to hydrate admin sidebar:", error);
    }
  },

  getAdminSidebarIconMarkup(iconKey = "") {
    const key = String(iconKey || "").toLowerCase();
    const icons = {
      dashboard:
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="3" width="7" height="7" rx="1.5"></rect><rect x="14" y="14" width="7" height="7" rx="1.5"></rect><rect x="3" y="14" width="7" height="7" rx="1.5"></rect></svg>',
      alumni:
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"></path><circle cx="9.5" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>',
      verification:
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6 9 17l-5-5"></path><path d="M21 12a9 9 0 1 1-3.3-6.96"></path></svg>',
      scanner:
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7V5a1 1 0 0 1 1-1h2"></path><path d="M17 4h2a1 1 0 0 1 1 1v2"></path><path d="M20 17v2a1 1 0 0 1-1 1h-2"></path><path d="M7 20H5a1 1 0 0 1-1-1v-2"></path><path d="M7 12h10"></path></svg>',
      card: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="M3 10h18"></path><path d="M7 15h3"></path></svg>',
      events:
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"></rect><path d="M16 2v4"></path><path d="M8 2v4"></path><path d="M3 10h18"></path></svg>',
      announcements:
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 18-5v12L3 14v-3Z"></path><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"></path></svg>',
      organization:
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"></rect><rect x="14" y="3" width="7" height="7" rx="1"></rect><rect x="8.5" y="14" width="7" height="7" rx="1"></rect><path d="M6.5 10v2h11v-2"></path><path d="M12 12v2"></path></svg>',
      security:
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 13c0 5-3.5 7.5-8 9-4.5-1.5-8-4-8-9V5l8-3 8 3v8Z"></path></svg>',
      form: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 6h13"></path><path d="M8 12h13"></path><path d="M8 18h13"></path><path d="M3 6h.01"></path><path d="M3 12h.01"></path><path d="M3 18h.01"></path></svg>',
      gamification:
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 21h8"></path><path d="M12 17v4"></path><path d="M7 4h10v4a5 5 0 0 1-10 0V4Z"></path><path d="M17 5h3a2 2 0 0 1 0 4h-3"></path><path d="M7 5H4a2 2 0 0 0 0 4h3"></path></svg>',
      users:
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path></svg>',
      campus:
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"></path><path d="M5 21V7l8-4 6 4v14"></path><path d="M9 21v-8h6v8"></path><path d="M9 9h.01"></path><path d="M13 9h.01"></path></svg>',
      theme:
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="13.5" cy="6.5" r=".5"></circle><circle cx="17.5" cy="10.5" r=".5"></circle><circle cx="8.5" cy="7.5" r=".5"></circle><circle cx="6.5" cy="12.5" r=".5"></circle><path d="M12 2a10 10 0 0 0 0 20 2 2 0 0 0 1.5-3.3 1 1 0 0 1 .8-1.7H16a6 6 0 0 0 0-12l-4-.01Z"></path></svg>',
      content:
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path><path d="M14 2v6h6"></path><path d="M8 13h8"></path><path d="M8 17h5"></path></svg>',
      email:
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="14" rx="2"></rect><path d="m3 7 9 6 9-6"></path></svg>',
      settings:
        '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5Z"></path><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06A2 2 0 1 1 7.04 4.3l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09A1.65 1.65 0 0 0 15 4.6a1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9c.14.47.51.84 1 1h.09a2 2 0 0 1 0 4h-.09c-.49.16-.86.53-1 1Z"></path></svg>',
    };

    const matchKey = key.includes("dashboard")
      ? "dashboard"
      : key.includes("verification")
        ? "verification"
        : key.includes("qr")
          ? "scanner"
          : key.includes("id-card")
            ? "card"
            : key.includes("alumni")
              ? "alumni"
              : key.includes("event")
                ? "events"
                : key.includes("announcement")
                  ? "announcements"
                  : key.includes("organization")
                    ? "organization"
                    : key.includes("security")
                      ? "security"
                      : key.includes("form")
                        ? "form"
                        : key.includes("gamification")
                          ? "gamification"
                          : key.includes("user")
                            ? "users"
                            : key.includes("campus")
                              ? "campus"
                              : key.includes("theme")
                                ? "theme"
                                : key.includes("content")
                                  ? "content"
                                  : key.includes("email")
                                    ? "email"
                                    : key.includes("setting")
                                      ? "settings"
                                      : "";

    return icons[matchKey] || icons.settings;
  },

  /**
   * Render the dashboard-layout sidebar from the shared admin/settings section config.
   */
  renderAdminSidebar(sidebar, options = {}) {
    if (!sidebar) {
      return;
    }

    const currentPath = this.getAdminSidebarPath(options.currentPath);
    const sections =
      Array.isArray(options.sections) && options.sections.length
        ? options.sections
        : this.getSettingsSidebarSections();

    sidebar.innerHTML = sections
      .map((section) => {
        const sectionKey = String(section?.key || "").trim();
        const sectionTitle = String(section?.title || "Section").trim();
        const sectionLinks = Array.isArray(section?.links) ? section.links : [];
        const isDefaultOpen = !!section?.defaultOpen;

        const hasActiveLink = sectionLinks.some((link) => {
          const matchPath = String(link?.match || "").trim();
          return (
            currentPath === matchPath ||
            (matchPath && currentPath.startsWith(`${matchPath}/`))
          );
        });

        const isCollapsed = isDefaultOpen ? false : !hasActiveLink;

        const linksHtml = sectionLinks
          .map((link) => {
            const href = String(link?.href || "#").trim();
            const hrefPath = href.replace(/^#/, "");
            const matchPath = String(link?.match || hrefPath).trim();
            const label = String(link?.label || "Link").replace(
              /All Alumnis/g,
              "All Alumni",
            );
            const isActive =
              currentPath === matchPath ||
              (matchPath && currentPath.startsWith(`${matchPath}/`));

            return `
              <a href="${href}" class="sidebar-link ${isActive ? "active" : ""}" data-match="${Utils.escapeHtml(matchPath)}">
                <span class="sidebar-link-icon" aria-hidden="true">${this.getAdminSidebarIconMarkup(link?.icon || matchPath)}</span>
                <span>${Utils.escapeHtml(label)}</span>
                ${link?.badge ? `<span class="sidebar-link-badge">${Utils.escapeHtml(String(link.badge))}</span>` : ""}
              </a>
            `;
          })
          .join("");

        return `
          <section class="sidebar-section ${isDefaultOpen ? "default-open" : ""} ${isCollapsed ? "collapsed" : ""}" data-key="${Utils.escapeHtml(sectionKey)}">
            <button type="button" class="sidebar-section-toggle" aria-expanded="${String(!isCollapsed)}">
              <span class="sidebar-section-title">${Utils.escapeHtml(sectionTitle)}</span>
              <svg class="sidebar-section-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </button>
            <div class="sidebar-section-links">
              ${linksHtml}
            </div>
          </section>
          `;
      })
      .join("");

    this.syncAdminSidebarSections(sidebar, currentPath);
  },

  /**
   * Apply active link and collapse state for grouped sidebar sections.
   */
  syncAdminSidebarSections(targetSidebar, currentPath) {
    const activePath = this.getAdminSidebarPath(currentPath);

    targetSidebar.querySelectorAll(".sidebar-link").forEach((link) => {
      const href = link.getAttribute("href") || "";
      const hrefPath = href.replace(/^#/, "");
      const matchPath = link.getAttribute("data-match") || hrefPath;
      const isActive =
        hrefPath === activePath ||
        currentPath === matchPath ||
        currentPath.startsWith(`${matchPath}/`);
      link.classList.toggle("active", isActive);
    });

    // Collapse all sections first, except default-open ones
    targetSidebar.querySelectorAll(".sidebar-section").forEach((section) => {
      const isDefaultOpen = section.classList.contains("default-open");
      if (!isDefaultOpen) {
        section.classList.add("collapsed");
        const toggle = section.querySelector(".sidebar-section-toggle");
        if (toggle) {
          toggle.setAttribute("aria-expanded", "false");
        }
      }
    });

    // Then expand only the section with the active link
    targetSidebar.querySelectorAll(".sidebar-section").forEach((section) => {
      const toggle = section.querySelector(".sidebar-section-toggle");
      const hasActiveLink = !!section.querySelector(".sidebar-link.active");
      const isDefaultOpen = section.classList.contains("default-open");

      // Only expand if it has an active link or is default-open
      const shouldExpand = hasActiveLink || isDefaultOpen;

      section.classList.toggle("collapsed", !shouldExpand);

      if (toggle) {
        toggle.setAttribute("aria-expanded", String(shouldExpand));

        if (!toggle.dataset.boundSidebarToggle) {
          toggle.dataset.boundSidebarToggle = "true";
          toggle.addEventListener("click", () => {
            const willCollapse = !section.classList.contains("collapsed");

            // When expanding a section, collapse all other non-default-open sections
            if (!willCollapse) {
              targetSidebar
                .querySelectorAll(".sidebar-section")
                .forEach((otherSection) => {
                  if (
                    otherSection !== section &&
                    !otherSection.classList.contains("default-open")
                  ) {
                    otherSection.classList.add("collapsed");
                    otherSection
                      .querySelector(".sidebar-section-toggle")
                      ?.setAttribute("aria-expanded", "false");
                  }
                });
            }

            section.classList.toggle("collapsed", willCollapse);
            toggle.setAttribute("aria-expanded", String(!willCollapse));
          });
        }
      }
    });
  },

  /**
   * Bind delegated events once so dynamically injected dashboard shells work everywhere.
   */
  bindGlobalAdminShellEvents() {
    if (this._adminShellEventsBound) {
      return;
    }

    this._adminShellEventsBound = true;

    document.addEventListener("click", (event) => {
      const appSection = document.body?.dataset?.appSection || "";
      const toggleButton = event.target.closest(".sidebar-toggle");
      if (toggleButton) {
        event.preventDefault();
        if (appSection === "admin") {
          this.toggleAdminSidebar();
        } else if (appSection === "alumni") {
          this.toggleAlumniSidebar();
        }
        return;
      }

      const backdrop = event.target.closest(".dashboard-sidebar-backdrop");
      if (backdrop) {
        event.preventDefault();
        if (appSection === "admin") {
          this.closeAdminSidebar();
        } else if (appSection === "alumni") {
          this.closeAlumniSidebar();
        }
        return;
      }

      const navLink = event.target.closest(".dashboard-layout .sidebar-link");
      if (navLink && window.matchMedia("(max-width: 980px)").matches) {
        if (appSection === "admin") {
          this.closeAdminSidebar();
        } else if (appSection === "alumni") {
          this.closeAlumniSidebar();
        }
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        this.closeAdminSidebar();
        this.closeAlumniSidebar();
      }
    });

    window.addEventListener("resize", () => {
      this.reconcileAdminSidebarState();
      this.reconcileAlumniSidebarState();
      this.updateAdminSidebarToggleState();
      this.updateAlumniSidebarToggleState();
    });
  },

  ensureAlumniSidebarBackdrop() {
    if (document.body?.dataset?.appSection !== "alumni") {
      return null;
    }

    const layout = document.querySelector(".dashboard-layout");
    if (!layout) {
      return null;
    }

    let backdrop = layout.querySelector(".dashboard-sidebar-backdrop");
    if (!backdrop) {
      backdrop = document.createElement("button");
      backdrop.type = "button";
      backdrop.className = "dashboard-sidebar-backdrop";
      backdrop.setAttribute("aria-label", "Close sidebar");
      backdrop.setAttribute("tabindex", "-1");

      const mainContent = layout.querySelector(".main-content");
      if (mainContent) {
        layout.insertBefore(backdrop, mainContent);
      } else {
        layout.appendChild(backdrop);
      }
    }

    return backdrop;
  },

  updateAlumniSidebarToggleState() {
    if (document.body?.dataset?.appSection !== "alumni") {
      return;
    }

    const layout = document.querySelector(".dashboard-layout");
    if (!layout) {
      return;
    }

    const sidebar = layout.querySelector("#sidebar, .sidebar");
    const headerToggle = layout.querySelector(
      ".topbar .sidebar-toggle, .content-header .sidebar-toggle",
    );
    const sidebarToggle = sidebar?.querySelector(".sidebar-toggle");

    if (!sidebar) {
      return;
    }

    const isMobile = window.matchMedia("(max-width: 980px)").matches;
    const sidebarVisible = isMobile
      ? sidebar.classList.contains("open")
      : !layout.classList.contains("sidebar-collapsed");

    // Update header toggle button (should show opposite of sidebar state)
    if (headerToggle) {
      const iconType = sidebarVisible ? "close" : "menu";
      if (headerToggle.dataset.sidebarIconType !== iconType) {
        headerToggle.dataset.sidebarIconType = iconType;
        headerToggle.innerHTML = this.getAdminSidebarToggleIconMarkup(iconType);
      }
      const label = sidebarVisible ? "Hide sidebar" : "Show sidebar";
      headerToggle.setAttribute("aria-label", label);
      headerToggle.setAttribute("title", label);
    }

    // Sidebar toggle should always show close icon
    if (sidebarToggle && sidebarToggle !== headerToggle) {
      if (sidebarToggle.dataset.sidebarIconType !== "close") {
        sidebarToggle.dataset.sidebarIconType = "close";
        sidebarToggle.innerHTML = this.getAdminSidebarToggleIconMarkup("close");
      }
      sidebarToggle.setAttribute("aria-label", "Hide sidebar");
      sidebarToggle.setAttribute("title", "Hide sidebar");
    }
  },

  ensureAlumniSidebarToggle() {
    if (document.body?.dataset?.appSection !== "alumni") {
      return null;
    }

    const layout = document.querySelector(".dashboard-layout");
    if (!layout) {
      return null;
    }

    const header = layout.querySelector(".topbar, .content-header");
    if (!header) {
      return null;
    }

    let toggle = header.querySelector(".sidebar-toggle");
    if (toggle) {
      this.updateAlumniSidebarToggleState();
      return toggle;
    }

    toggle = document.createElement("button");
    toggle.type = "button";
    toggle.className = "btn btn-ghost sidebar-toggle";
    toggle.setAttribute("aria-label", "Show sidebar");
    toggle.innerHTML = this.getAdminSidebarToggleIconMarkup("menu");

    const heading = header.querySelector("h1, .page-title");
    if (heading) {
      heading.insertAdjacentElement("beforebegin", toggle);
    } else {
      header.prepend(toggle);
    }

    this.updateAlumniSidebarToggleState();

    return toggle;
  },

  toggleAlumniSidebar(forceOpen = null) {
    if (document.body?.dataset?.appSection !== "alumni") {
      return;
    }

    const sidebar = Utils.$("#sidebar");
    if (!sidebar) {
      return;
    }

    const isMobile = window.matchMedia("(max-width: 980px)").matches;
    const backdrop = this.ensureAlumniSidebarBackdrop();
    const layout = sidebar.closest(".dashboard-layout");

    if (!isMobile) {
      const isOpen = !(
        layout && layout.classList.contains("sidebar-collapsed")
      );
      const nextOpen = forceOpen === null ? !isOpen : !!forceOpen;

      if (layout) {
        layout.classList.toggle("sidebar-collapsed", !nextOpen);
        layout.classList.remove("sidebar-open");
      }

      this._alumniSidebarDesktopCollapsed = !nextOpen;
      sidebar.classList.remove("open");
      if (backdrop) {
        backdrop.classList.remove("active");
      }
      this.updateAlumniSidebarToggleState();
      return;
    }

    const isOpen = sidebar.classList.contains("open");
    const nextOpen = forceOpen === null ? !isOpen : !!forceOpen;
    sidebar.classList.toggle("open", nextOpen);

    if (layout) {
      layout.classList.toggle("sidebar-open", nextOpen);
    }

    if (backdrop) {
      backdrop.classList.toggle("active", nextOpen);
    }

    this.updateAlumniSidebarToggleState();
  },

  closeAlumniSidebar() {
    if (document.body?.dataset?.appSection !== "alumni") {
      return;
    }

    if (!window.matchMedia("(max-width: 980px)").matches) {
      this.updateAlumniSidebarToggleState();
      return;
    }

    this.toggleAlumniSidebar(false);
  },

  reconcileAlumniSidebarState() {
    if (document.body?.dataset?.appSection !== "alumni") {
      return;
    }

    const layout = document.querySelector(".dashboard-layout");
    const sidebar = layout?.querySelector("#sidebar, .sidebar");

    if (!layout || !sidebar) {
      return;
    }

    const backdrop = this.ensureAlumniSidebarBackdrop();
    const isMobile = window.matchMedia("(max-width: 980px)").matches;

    if (isMobile) {
      layout.classList.remove("sidebar-collapsed");
      layout.classList.toggle(
        "sidebar-open",
        sidebar.classList.contains("open"),
      );

      if (backdrop) {
        backdrop.classList.toggle("active", sidebar.classList.contains("open"));
      }
    } else {
      sidebar.classList.remove("open");
      layout.classList.remove("sidebar-open");

      if (backdrop) {
        backdrop.classList.remove("active");
      }

      layout.classList.toggle(
        "sidebar-collapsed",
        !!this._alumniSidebarDesktopCollapsed,
      );
    }

    this.updateAlumniSidebarToggleState();
  },

  ensureAdminSidebarBackdrop() {
    const layout = document.querySelector(".dashboard-layout");
    if (!layout) {
      return null;
    }

    let backdrop = layout.querySelector(".dashboard-sidebar-backdrop");
    if (!backdrop) {
      backdrop = document.createElement("button");
      backdrop.type = "button";
      backdrop.className = "dashboard-sidebar-backdrop";
      backdrop.setAttribute("aria-label", "Close sidebar");
      backdrop.setAttribute("tabindex", "-1");

      const mainContent = layout.querySelector(".main-content");
      if (mainContent) {
        layout.insertBefore(backdrop, mainContent);
      } else {
        layout.appendChild(backdrop);
      }
    }

    return backdrop;
  },

  getAdminSidebarToggleIconMarkup(iconType = "menu") {
    if (iconType === "close") {
      return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
    }

    return '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>';
  },

  updateAdminSidebarToggleState() {
    if (document.body?.dataset?.appSection !== "admin") {
      return;
    }

    const layout = document.querySelector(".dashboard-layout");
    if (!layout) {
      return;
    }

    const sidebar = layout.querySelector("#sidebar, #adminSidebar, .sidebar");
    const toggle = layout.querySelector(
      ".content-header .sidebar-toggle, .admin-topbar .sidebar-toggle",
    );

    if (!sidebar || !toggle) {
      return;
    }

    const isMobile = window.matchMedia("(max-width: 980px)").matches;
    const sidebarVisible = isMobile
      ? sidebar.classList.contains("open")
      : !layout.classList.contains("sidebar-collapsed");

    const iconType = sidebarVisible ? "close" : "menu";
    if (toggle.dataset.sidebarIconType !== iconType) {
      toggle.dataset.sidebarIconType = iconType;
      toggle.innerHTML = this.getAdminSidebarToggleIconMarkup(iconType);
    }

    const label = sidebarVisible ? "Hide sidebar" : "Show sidebar";
    toggle.setAttribute("aria-label", label);
    toggle.setAttribute("title", label);
  },

  ensureAdminSidebarToggle() {
    if (document.body?.dataset?.appSection !== "admin") {
      return null;
    }

    const layout = document.querySelector(".dashboard-layout");
    if (!layout) {
      return null;
    }

    const header = layout.querySelector(".content-header, .admin-topbar");
    if (!header) {
      return null;
    }

    let toggle = header.querySelector(".sidebar-toggle");
    if (toggle) {
      this.updateAdminSidebarToggleState();
      return toggle;
    }

    toggle = document.createElement("button");
    toggle.type = "button";
    toggle.className = "btn btn-secondary btn-sm sidebar-toggle";
    toggle.setAttribute("aria-label", "Show sidebar");
    toggle.innerHTML = this.getAdminSidebarToggleIconMarkup("menu");

    const heading = header.querySelector("h1, .page-title");
    if (heading) {
      heading.insertAdjacentElement("beforebegin", toggle);
    } else {
      header.prepend(toggle);
    }

    this.updateAdminSidebarToggleState();

    return toggle;
  },

  /**
   * Ensure each admin dashboard header has the same user-meta/logout block as Theme Settings.
   */
  ensureAdminTopbarUser() {
    if (document.body?.dataset?.appSection !== "admin") {
      return null;
    }

    const layout = document.querySelector(".dashboard-layout");
    if (!layout) {
      return null;
    }

    const header = layout.querySelector(".content-header, .admin-topbar");
    if (!header) {
      return null;
    }

    // Ensure header has proper flexbox layout
    header.style.display = "flex";
    header.style.alignItems = "center";
    header.style.justifyContent = "flex-start";
    header.style.gap = "1rem";
    header.style.flexWrap = "nowrap";

    // Find or create the title element
    let titleElement = header.querySelector("h1, .page-title");
    if (titleElement) {
      titleElement.style.flex = "1 1 auto";
      titleElement.style.minWidth = "0";
      titleElement.style.textAlign = "left";
      titleElement.style.margin = "0";
    }

    let shellUser = header.querySelector(".admin-shell-user");
    if (!shellUser) {
      shellUser = document.createElement("div");
      shellUser.className = "admin-shell-user";
      shellUser.style.flex = "0 0 auto";
      shellUser.style.marginLeft = "auto";
      shellUser.innerHTML = `
        <div class="admin-shell-user-meta">
          <div class="admin-shell-user-name">Administrator</div>
          <div class="admin-shell-user-role">Administrator</div>
        </div>
        <button type="button" class="btn btn-danger btn-sm admin-shell-logout">Logout</button>
      `;

      // Insert at the end of the header to ensure proper positioning
      header.appendChild(shellUser);
    } else {
      // Ensure existing shellUser has proper styling
      shellUser.style.flex = "0 0 auto";
      shellUser.style.marginLeft = "auto";
    }

    const user = API.getUser() || {};
    const nameElement = shellUser.querySelector(".admin-shell-user-name");
    const roleElement = shellUser.querySelector(".admin-shell-user-role");
    const logoutButton = shellUser.querySelector(".admin-shell-logout");

    if (nameElement) {
      nameElement.textContent = user.name || "Administrator";
    }

    if (roleElement) {
      roleElement.textContent =
        user.role === "system_admin" ? "System Administrator" : "Administrator";
    }

    if (logoutButton && !logoutButton.dataset.boundAdminShellLogout) {
      logoutButton.dataset.boundAdminShellLogout = "true";
      logoutButton.addEventListener("click", () => Auth.logout());
    }

    return shellUser;
  },

  toggleAdminSidebar(forceOpen = null) {
    if (document.body?.dataset?.appSection !== "admin") {
      return;
    }

    const sidebar = Utils.$("#sidebar") || Utils.$("#adminSidebar");
    if (!sidebar) {
      return;
    }

    const layout = sidebar.closest(".dashboard-layout");
    const isMobile = window.matchMedia("(max-width: 980px)").matches;
    const backdrop = this.ensureAdminSidebarBackdrop();

    if (isMobile) {
      const isOpen = sidebar.classList.contains("open");
      const nextOpen = forceOpen === null ? !isOpen : !!forceOpen;
      sidebar.classList.toggle("open", nextOpen);

      if (layout) {
        layout.classList.remove("sidebar-collapsed");
      }

      if (backdrop) {
        backdrop.classList.toggle("active", nextOpen);
      }

      this.updateAdminSidebarToggleState();

      return;
    }

    const currentOpen = !(
      layout && layout.classList.contains("sidebar-collapsed")
    );
    const nextOpen = forceOpen === null ? !currentOpen : !!forceOpen;

    if (layout) {
      layout.classList.toggle("sidebar-collapsed", !nextOpen);
    }

    this._adminSidebarDesktopCollapsed = !nextOpen;
    sidebar.classList.remove("open");

    if (backdrop) {
      backdrop.classList.remove("active");
    }

    this.updateAdminSidebarToggleState();
  },

  closeAdminSidebar() {
    if (document.body?.dataset?.appSection !== "admin") {
      return;
    }

    if (!window.matchMedia("(max-width: 980px)").matches) {
      this.updateAdminSidebarToggleState();
      return;
    }

    this.toggleAdminSidebar(false);
  },

  reconcileAdminSidebarState() {
    if (document.body?.dataset?.appSection !== "admin") {
      return;
    }

    const layout = document.querySelector(".dashboard-layout");
    const sidebar = layout?.querySelector("#sidebar, #adminSidebar, .sidebar");

    if (!layout || !sidebar) {
      return;
    }

    const backdrop = this.ensureAdminSidebarBackdrop();
    const isMobile = window.matchMedia("(max-width: 980px)").matches;

    if (!isMobile) {
      sidebar.classList.remove("open");

      if (backdrop) {
        backdrop.classList.remove("active");
      }

      layout.classList.toggle(
        "sidebar-collapsed",
        !!this._adminSidebarDesktopCollapsed,
      );
    } else {
      layout.classList.remove("sidebar-collapsed");

      if (backdrop) {
        backdrop.classList.toggle("active", sidebar.classList.contains("open"));
      }
    }

    this.updateAdminSidebarToggleState();
  },

  /**
   * Shared sidebar configuration for authenticated admin routes.
   */
  getSettingsSidebarSections() {
    const user =
      API.getUser() || (typeof Auth !== "undefined" && Auth.user) || {};
    const userRole = user.role || "";

    const allSections = [
      {
        key: "main",
        title: "Main",
        defaultOpen: false,
        links: [
          {
            label: "Dashboard",
            href: "#/admin/dashboard",
            match: "/admin/dashboard",
          },
          {
            label: "All Alumni",
            href: "#/admin/alumni",
            match: "/admin/alumni",
          },
        ],
      },
      {
        key: "tools",
        title: "Tools",
        defaultOpen: false,
        links: [
          {
            label: "QR Scanner",
            href: "#/admin/qr-scanner",
            match: "/admin/qr-scanner",
          },
          {
            label: "Alumni ID Cards",
            href: "#/admin/alumni-id-card",
            match: "/admin/alumni-id-card",
          },
        ],
      },
      {
        key: "management",
        title: "Management",
        defaultOpen: false,
        links: [
          {
            label: "Alumni Verification",
            href: "#/admin/alumni-verification",
            match: "/admin/alumni-verification",
            roles: ["admin", "system_admin"],
          },
          {
            label: "Events",
            href: "#/admin/events",
            match: "/admin/events",
            roles: ["admin", "system_admin", "campus_admin"],
          },
          {
            label: "Announcements",
            href: "#/admin/announcements",
            match: "/admin/announcements",
            roles: ["admin", "system_admin", "campus_admin"],
          },
          {
            label: "Organization",
            href: "#/admin/organization",
            match: "/admin/organization",
            roles: ["admin", "system_admin", "campus_admin"], // Hidden from staff
          },
          {
            label: "Security Center",
            href: "#/admin/security-center",
            match: "/admin/security-center",
            roles: ["admin", "system_admin"], // Hidden from campus_admin and staff
          },
        ],
      },
      {
        key: "configuration",
        title: "Configuration",
        defaultOpen: false,
        links: [
          {
            label: "Form Builder",
            href: "#/admin/form-builder",
            match: "/admin/form-builder",
            roles: ["admin", "system_admin"],
          },
          {
            label: "Gamification",
            href: "#/admin/gamification",
            match: "/admin/gamification",
            roles: ["admin", "system_admin", "campus_admin"], // Hidden from staff
          },
          {
            label: "Users",
            href: "#/admin/users",
            match: "/admin/users",
            roles: ["admin", "system_admin"],
          },
          {
            label: "Campus Management",
            href: "#/admin/campuses",
            match: "/admin/campuses",
            roles: ["admin", "system_admin"], // Hidden from campus_admin and staff
          },
        ],
      },
      {
        key: "settings",
        title: "Settings",
        defaultOpen: false,
        roles: ["admin", "system_admin"], // Entire section hidden from campus_admin and staff
        links: [
          {
            label: "Theme Settings",
            href: "#/admin/settings/theme",
            match: "/admin/settings/theme",
          },
          {
            label: "Site Content",
            href: "#/admin/settings/site-content",
            match: "/admin/settings/site-content",
          },
          {
            label: "Security Settings",
            href: "#/admin/settings/security",
            match: "/admin/settings/security",
          },
          {
            label: "Email Management",
            href: "#/admin/settings/email-templates",
            match: "/admin/settings/email-templates",
          },
        ],
      },
    ];

    // Filter sections and links based on user role
    return allSections
      .filter((section) => {
        // If section has role restrictions, check them
        if (section.roles && !section.roles.includes(userRole)) {
          return false;
        }
        return true;
      })
      .map((section) => {
        // Filter links within each section
        const filteredLinks = section.links.filter((link) => {
          // If link has role restrictions, check them
          if (link.roles && !link.roles.includes(userRole)) {
            return false;
          }
          return true;
        });

        return {
          ...section,
          links: filteredLinks,
        };
      })
      .filter((section) => section.links.length > 0); // Remove empty sections
  },

  /**
   * Normalize settings route path for active-link highlighting.
   */
  getSettingsSidebarPath(path) {
    const normalizedPath = String(path || "")
      .trim()
      .split(/[?#]/)[0];
    if (!normalizedPath) {
      return "/admin/settings/theme";
    }

    if (normalizedPath === "/admin/settings") {
      return "/admin/settings/theme";
    }

    return normalizedPath;
  },

  /**
   * Render unified settings sidebar and bind collapse behavior.
   */
  renderSettingsSidebar(sidebar, options = {}) {
    if (!sidebar) {
      return;
    }

    const currentPath = this.getSettingsSidebarPath(options.currentPath);
    const sections =
      Array.isArray(options.sections) && options.sections.length
        ? options.sections
        : this.getSettingsSidebarSections();

    sidebar.innerHTML = sections
      .map((section) => {
        const sectionKey = String(section?.key || "");
        const sectionTitle = String(section?.title || "Section");
        const sectionLinks = Array.isArray(section?.links) ? section.links : [];
        const isDefaultOpen = !!section?.defaultOpen;
        const hasActiveLink = sectionLinks.some((link) => {
          const matchPath = String(link?.match || "").trim();
          return (
            currentPath === matchPath ||
            (matchPath && currentPath.startsWith(`${matchPath}/`))
          );
        });

        const isCollapsed = isDefaultOpen ? false : !hasActiveLink;

        const linksHtml = sectionLinks
          .map((link) => {
            const href = String(link?.href || "#").trim();
            const hrefPath = href.replace(/^#/, "");
            const matchPath = String(link?.match || hrefPath).trim();
            const label = String(link?.label || "Link").replace(
              /All Alumnis/g,
              "All Alumni",
            );
            const exactMatchOnly =
              matchPath === "/admin/settings" ||
              matchPath === "/admin/dashboard";
            const isActive = exactMatchOnly
              ? currentPath === matchPath
              : currentPath === matchPath ||
                (matchPath && currentPath.startsWith(`${matchPath}/`));

            return `
              <a href="${href}" class="theme-ui-sidebar-link sidebar-link ${isActive ? "active" : ""}" data-match="${Utils.escapeHtml(matchPath)}">
                <span class="theme-ui-link-icon sidebar-link-icon" aria-hidden="true">${this.getAdminSidebarIconMarkup(link?.icon || matchPath)}</span>
                <span>${Utils.escapeHtml(label)}</span>
                ${link?.badge ? `<span class="theme-ui-link-badge">${Utils.escapeHtml(String(link.badge))}</span>` : ""}
              </a>
            `;
          })
          .join("");

        return `
          <section class="theme-ui-sidebar-section sidebar-section ${isCollapsed ? "collapsed" : ""}" data-key="${Utils.escapeHtml(sectionKey)}" data-default-open="${String(isDefaultOpen)}">
            <button type="button" class="theme-ui-sidebar-toggle sidebar-section-toggle" aria-expanded="${String(!isCollapsed)}">
              <span class="sidebar-section-title">${Utils.escapeHtml(sectionTitle)}</span>
              <svg class="sidebar-section-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </button>
            <div class="theme-ui-sidebar-links sidebar-section-links">
              ${linksHtml}
            </div>
          </section>
          `;
      })
      .join("");

    sidebar.querySelectorAll(".theme-ui-sidebar-toggle").forEach((toggle) => {
      if (toggle.dataset.boundSettingsToggle) {
        return;
      }

      toggle.dataset.boundSettingsToggle = "true";
      toggle.addEventListener("click", () => {
        const section = toggle.closest(".theme-ui-sidebar-section");
        if (!section) {
          return;
        }

        const willCollapse = !section.classList.contains("collapsed");

        if (!willCollapse) {
          sidebar
            .querySelectorAll(".theme-ui-sidebar-section")
            .forEach((otherSection) => {
              if (
                otherSection !== section &&
                otherSection.dataset.defaultOpen !== "true"
              ) {
                otherSection.classList.add("collapsed");
                otherSection
                  .querySelector(".theme-ui-sidebar-toggle")
                  ?.setAttribute("aria-expanded", "false");
              }
            });
        }

        section.classList.toggle("collapsed", willCollapse);
        toggle.setAttribute("aria-expanded", String(!willCollapse));
      });
    });
  },

  /**
   * Bind topbar profile block for settings-shell pages.
   */
  bindSettingsTopbarUser(options = {}) {
    const nameElement =
      options.nameElement ||
      document.getElementById(options.nameId || "settingsTopbarName");
    const roleElement =
      options.roleElement ||
      document.getElementById(options.roleId || "settingsTopbarRole");
    const logoutButton =
      options.logoutButton ||
      document.getElementById(options.logoutId || "settingsTopbarLogout");

    const user = API.getUser() || {};
    if (nameElement) {
      nameElement.textContent = user.name || "Administrator";
    }

    if (roleElement) {
      roleElement.textContent =
        user.role === "system_admin" ? "System Administrator" : "Administrator";
    }

    if (logoutButton && !logoutButton.dataset.boundSettingsTopbarLogout) {
      logoutButton.dataset.boundSettingsTopbarLogout = "true";
      logoutButton.addEventListener("click", () => Auth.logout());
    }
  },

  /**
   * Initialize shared settings shell interactions for a page instance.
   */
  initSettingsShell(options = {}) {
    const sidebar = document.getElementById(
      options.sidebarId || "settingsSidebar",
    );
    if (!sidebar) {
      return;
    }

    const shell = sidebar.closest(".theme-ui-shell");

    const sidebarBackdrop = document.getElementById(
      options.backdropId || "settingsSidebarBackdrop",
    );
    const sidebarToggle = document.getElementById(
      options.toggleId || "settingsSidebarToggle",
    );
    const sidebarClose =
      options.closeButton ||
      shell?.querySelector(".theme-ui-close-btn") ||
      document.querySelector(".theme-ui-close-btn");
    const currentPath =
      String(
        options.currentPath ||
          (window.location.hash || "#/admin/settings/theme").replace(/^#/, ""),
      ).trim() || "/admin/settings/theme";

    this.renderSettingsSidebar(sidebar, {
      currentPath,
      sections: options.sections,
      storagePrefix: options.storagePrefix,
    });

    if (
      shell &&
      this._settingsSidebarDesktopCollapsed &&
      !window.matchMedia("(max-width: 980px)").matches
    ) {
      shell.classList.add("settings-sidebar-collapsed");
    }

    const setMobileSidebarVisible = (visible) => {
      sidebar.classList.toggle("open", visible);
      if (shell) {
        shell.classList.toggle("settings-sidebar-open", visible);
      }
      if (sidebarBackdrop) {
        sidebarBackdrop.classList.toggle("active", visible);
      }
    };

    const setDesktopSidebarVisible = (visible) => {
      if (!shell) {
        return;
      }

      shell.classList.toggle("settings-sidebar-collapsed", !visible);
      this._settingsSidebarDesktopCollapsed = !visible;
    };

    const closeSidebar = () => {
      if (window.matchMedia("(max-width: 980px)").matches) {
        setMobileSidebarVisible(false);
      } else {
        setDesktopSidebarVisible(false);
      }
    };

    const toggleSidebar = () => {
      if (window.matchMedia("(max-width: 980px)").matches) {
        const willOpen = !sidebar.classList.contains("open");
        setMobileSidebarVisible(willOpen);
      } else {
        const willShow =
          !shell || shell.classList.contains("settings-sidebar-collapsed");
        setDesktopSidebarVisible(willShow);
      }
    };

    if (sidebarToggle && !sidebarToggle.dataset.boundSettingsShellToggle) {
      sidebarToggle.dataset.boundSettingsShellToggle = "true";
      sidebarToggle.addEventListener("click", toggleSidebar);
    }

    if (
      sidebarBackdrop &&
      !sidebarBackdrop.dataset.boundSettingsShellBackdrop
    ) {
      sidebarBackdrop.dataset.boundSettingsShellBackdrop = "true";
      sidebarBackdrop.addEventListener("click", closeSidebar);
    }

    if (sidebarClose && !sidebarClose.dataset.boundSettingsShellClose) {
      sidebarClose.dataset.boundSettingsShellClose = "true";
      sidebarClose.setAttribute("aria-label", "Toggle Sidebar");
      sidebarClose.setAttribute("title", "Toggle Sidebar");
      sidebarClose.addEventListener("click", (event) => {
        event.preventDefault();
        toggleSidebar();
      });
    }

    sidebar.querySelectorAll(".theme-ui-sidebar-link").forEach((link) => {
      if (link.dataset.boundSettingsLinkClose) {
        return;
      }

      link.dataset.boundSettingsLinkClose = "true";
      link.addEventListener("click", () => {
        if (window.matchMedia("(max-width: 980px)").matches) {
          closeSidebar();
        }
      });
    });

    this._activeSettingsShell = {
      sidebar,
      backdrop: sidebarBackdrop,
    };

    if (!this._settingsShellEscapeBound) {
      this._settingsShellEscapeBound = true;
      document.addEventListener("keydown", (event) => {
        if (event.key !== "Escape") {
          return;
        }

        const activeShell = this._activeSettingsShell;
        if (!activeShell?.sidebar) {
          return;
        }

        activeShell.sidebar.classList.remove("open");
        if (activeShell.backdrop) {
          activeShell.backdrop.classList.remove("active");
        }

        const activeSidebarShell =
          activeShell.sidebar.closest(".theme-ui-shell");
        if (activeSidebarShell) {
          activeSidebarShell.classList.remove("settings-sidebar-open");
        }
        if (
          activeSidebarShell &&
          !window.matchMedia("(max-width: 980px)").matches
        ) {
          activeSidebarShell.classList.add("settings-sidebar-collapsed");
          this._settingsSidebarDesktopCollapsed = true;
        }
      });
    }

    this.bindSettingsTopbarUser({
      nameId: options.nameId,
      roleId: options.roleId,
      logoutId: options.logoutId,
    });
  },

  /**
   * Map nested admin routes to their sidebar parent route.
   */
  getAdminSidebarPath(path) {
    const normalizedPath = String(path || "")
      .trim()
      .split(/[?#]/)[0];

    if (normalizedPath === "/admin/settings") {
      return "/admin/settings/theme";
    }

    if (
      normalizedPath === "/admin/logs" ||
      normalizedPath.startsWith("/admin/logs/")
    ) {
      return "/admin/security-center";
    }

    const sidebarRoots = [
      "/admin/dashboard",
      "/admin/alumni",
      "/admin/alumni-verification",
      "/admin/qr-scanner",
      "/admin/alumni-id-card",
      "/admin/events",
      "/admin/announcements",
      "/admin/organization",
      "/admin/security-center",
      "/admin/logs",
      "/admin/form-builder",
      "/admin/gamification",
      "/admin/users",
      "/admin/campuses",
      "/admin/settings",
    ];

    const matched = [...sidebarRoots]
      .sort((a, b) => b.length - a.length)
      .find(
        (root) =>
          normalizedPath === root || normalizedPath.startsWith(`${root}/`),
      );

    return matched || normalizedPath;
  },
};
