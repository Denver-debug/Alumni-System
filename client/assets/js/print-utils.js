/**
 * Alumni Management System - Print Utilities
 * Print reports, PDF generation, CSV export
 */

const PrintUtils = {
  /**
   * Print audit logs report
   */
  printAuditLogs(logs, filters = {}) {
    if (!logs || !logs.length) {
      if (typeof Utils !== "undefined" && Utils.error) {
        Utils.error("No logs to print");
      } else {
        alert("No logs to print");
      }
      return;
    }

    const printWindow = window.open("", "_blank");

    if (!printWindow) {
      alert("Please allow popups to print reports");
      return;
    }

    const html = this.generateAuditLogHTML(logs, filters);

    printWindow.document.write(html);
    printWindow.document.close();

    printWindow.onload = () => {
      setTimeout(() => {
        printWindow.print();
      }, 250);
    };
  },

  /**
   * Generate audit log HTML for printing.
   * Reports with more than 10 entries are condensed into one analyzed page.
   */
  generateAuditLogHTML(logs, filters = {}) {
    const now = new Date();
    const dateStr = now.toLocaleDateString();
    const timeStr = now.toLocaleTimeString();
    const filterSummary = this.getFilterSummary(filters);
    const isCondensed = logs.length > 10;

    return `
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Audit Log Report - ${dateStr}</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-size: ${isCondensed ? "9px" : "11px"};
      line-height: 1.35;
      color: #333;
      padding: ${isCondensed ? "10px" : "14px"};
    }

    .header {
      text-align: center;
      margin-bottom: ${isCondensed ? "10px" : "18px"};
      padding-bottom: ${isCondensed ? "8px" : "12px"};
      border-bottom: 3px solid #10b981;
    }

    .header h1 {
      font-size: ${isCondensed ? "16px" : "20px"};
      color: #10b981;
      margin-bottom: 4px;
    }

    .header .subtitle {
      font-size: ${isCondensed ? "10px" : "12px"};
      color: #666;
    }

    .meta-info {
      display: flex;
      justify-content: space-between;
      gap: 8px;
      margin-bottom: ${isCondensed ? "8px" : "14px"};
      padding: ${isCondensed ? "8px" : "10px"};
      background: #f9fafb;
      border-radius: 6px;
    }

    .meta-info div {
      flex: 1;
    }

    .meta-info strong {
      display: block;
      color: #10b981;
      margin-bottom: 3px;
    }

    .filters {
      margin-bottom: ${isCondensed ? "8px" : "14px"};
      padding: ${isCondensed ? "8px" : "10px"};
      background: #f0fdf4;
      border-left: 4px solid #10b981;
      border-radius: 4px;
    }

    .filters h3 {
      font-size: ${isCondensed ? "10px" : "12px"};
      color: #10b981;
      margin-bottom: 5px;
    }

    .filters p {
      margin: 2px 0;
      color: #666;
    }

    .summary {
      margin-bottom: ${isCondensed ? "8px" : "14px"};
      padding: ${isCondensed ? "8px" : "10px"};
      background: #f9fafb;
      border-radius: 6px;
    }

    .summary h3,
    .section h3 {
      font-size: ${isCondensed ? "10px" : "12px"};
      color: #10b981;
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: 0;
    }

    .summary-grid {
      display: grid;
      grid-template-columns: ${isCondensed ? "repeat(4, 1fr)" : "repeat(3, 1fr)"};
      gap: ${isCondensed ? "6px" : "10px"};
    }

    .summary-item {
      text-align: center;
      padding: ${isCondensed ? "6px" : "8px"};
      background: white;
      border-radius: 6px;
      border: 1px solid #e5e7eb;
    }

    .summary-item .value {
      font-size: ${isCondensed ? "14px" : "20px"};
      font-weight: 700;
      color: #10b981;
      display: block;
      margin-bottom: 3px;
    }

    .summary-item .label {
      font-size: ${isCondensed ? "7px" : "10px"};
      color: #666;
      text-transform: uppercase;
      letter-spacing: 0;
    }

    .report-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      margin-bottom: 8px;
    }

    .section {
      border: 1px solid #d1fae5;
      border-radius: 6px;
      padding: 8px;
      background: #ffffff;
      page-break-inside: avoid;
    }

    .bar-row,
    .timeline-row,
    .admin-row {
      display: grid;
      grid-template-columns: minmax(72px, 1fr) 2fr 34px;
      gap: 6px;
      align-items: center;
      margin-bottom: 5px;
    }

    .admin-row {
      grid-template-columns: minmax(90px, 1fr) 42px;
    }

    .bar-label,
    .timeline-label,
    .admin-label {
      font-weight: 600;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }

    .bar-track {
      height: 7px;
      background: #e5e7eb;
      border-radius: 999px;
      overflow: hidden;
    }

    .bar-fill {
      height: 100%;
      background: #10b981;
    }

    .bar-value,
    .timeline-value,
    .admin-value {
      text-align: right;
      color: #4b5563;
      font-size: 8px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: ${isCondensed ? "8px" : "14px"};
      page-break-inside: avoid;
    }

    thead {
      background: #10b981;
      color: white;
    }

    th {
      padding: ${isCondensed ? "5px 4px" : "8px 6px"};
      text-align: left;
      font-weight: 600;
      font-size: ${isCondensed ? "8px" : "10px"};
      text-transform: uppercase;
      letter-spacing: 0;
    }

    td {
      padding: ${isCondensed ? "4px" : "7px 6px"};
      border-bottom: 1px solid #e5e7eb;
      vertical-align: top;
    }

    tbody tr:nth-child(even) {
      background: #f9fafb;
    }

    tbody tr:hover {
      background: #f0fdf4;
    }

    .badge {
      display: inline-block;
      padding: ${isCondensed ? "2px 5px" : "3px 7px"};
      border-radius: 12px;
      font-size: ${isCondensed ? "7px" : "9px"};
      font-weight: 600;
      text-transform: uppercase;
      background: #e5e7eb;
      color: #374151;
    }

    .compact-note {
      color: #6b7280;
      font-size: 8px;
      margin-top: 4px;
    }

    .report-insights p {
      margin-bottom: 5px;
    }

    .footer {
      margin-top: ${isCondensed ? "8px" : "16px"};
      padding-top: ${isCondensed ? "8px" : "12px"};
      border-top: 2px solid #e5e7eb;
      text-align: center;
      color: #666;
      font-size: ${isCondensed ? "8px" : "10px"};
    }

    @media print {
      body {
        padding: 0;
      }

      .no-print {
        display: none !important;
      }

      table {
        page-break-inside: avoid;
      }

      tr {
        page-break-inside: avoid;
        page-break-after: auto;
      }

      thead {
        display: table-header-group;
      }

      tfoot {
        display: table-footer-group;
      }
    }

    @page {
      margin: 1cm;
    }
  </style>
</head>
<body data-report-format="${isCondensed ? "condensed" : "full"}" data-page-count="${isCondensed ? "1" : "auto"}" data-aggregated="${isCondensed ? "true" : "false"}">
  <div class="header">
    <h1>Security Audit Log Report</h1>
    <div class="subtitle">Alumni Management System</div>
  </div>

  <div class="meta-info">
    <div>
      <strong>Report Generated</strong>
      ${dateStr} at ${timeStr}
    </div>
    <div>
      <strong>Total Records</strong>
      ${logs.length} entries
    </div>
    <div>
      <strong>Report Type</strong>
      Audit Trail
    </div>
  </div>

  ${filterSummary ? `
  <div class="filters">
    <h3>Applied Filters</h3>
    ${filterSummary}
  </div>
  ` : ""}

  ${this.generateSummarySection(logs)}
  ${isCondensed ? this.generateCondensedAuditSections(logs) : this.generateRawAuditTable(logs)}

  <div class="footer">
    <p><strong>Alumni Management System</strong> - Security Audit Log</p>
    <p>This report contains ${logs.length} audit log entries${isCondensed ? ", condensed into an analyzed one-page summary" : ""}</p>
    <p>Generated on ${dateStr} at ${timeStr}</p>
    <p style="margin-top: 10px; font-size: 10px;">
      This is a confidential document. Unauthorized access or distribution is prohibited.
    </p>
  </div>
</body>
</html>
    `;
  },

  /**
   * Generate summary section
   */
  generateSummarySection(logs) {
    const stats = this.calculateLogStats(logs);
    const values = [
      ["Total Logs", stats.totalLogs],
      ["Unique Admins", stats.uniqueAdmins],
      ["Action Types", stats.uniqueActions],
      ["Date Range", stats.dateRange],
    ];

    return `
  <div class="summary">
    <h3>Summary Statistics</h3>
    <div class="summary-grid">
      ${values.map(([label, value]) => `
      <div class="summary-item">
        <span class="value">${this.escapeHTML(value)}</span>
        <span class="label">${this.escapeHTML(label)}</span>
      </div>
      `).join("")}
    </div>
  </div>
    `;
  },

  /**
   * Calculate log statistics
   */
  calculateLogStats(logs) {
    const admins = new Set();
    const actions = new Set();
    const timestamps = [];

    logs.forEach((log) => {
      admins.add(log.admin_id || log.admin_name || "System");
      actions.add(this.getActionLabel(log));

      const timestamp = this.getLogDate(log);
      if (timestamp) timestamps.push(timestamp);
    });

    timestamps.sort((a, b) => a - b);

    return {
      totalLogs: logs.length,
      uniqueAdmins: admins.size,
      uniqueActions: actions.size,
      dateRange: this.formatDateRange(timestamps),
    };
  },

  /**
   * Generate condensed report sections for large audit logs
   */
  generateCondensedAuditSections(logs) {
    const actionDistribution = this.analyzeActionDistribution(logs);
    const topAdmins = this.identifyTopAdmins(logs);
    const criticalEvents = this.extractCriticalEvents(logs, 10);
    const timeDistribution = this.calculateTimeDistribution(logs);

    return `
  <div class="report-grid analysis-sections">
    <div class="section action-distribution">
      <h3>Action Distribution</h3>
      ${this.generateDistributionBars(actionDistribution)}
    </div>

    <div class="section top-admins">
      <h3>Top Admins</h3>
      ${topAdmins.map((admin) => `
        <div class="admin-row">
          <div class="admin-label">${this.escapeHTML(admin.name)}</div>
          <div class="admin-value">${admin.count} log${admin.count === 1 ? "" : "s"}</div>
        </div>
      `).join("") || '<p class="compact-note">No administrator activity recorded.</p>'}
    </div>
  </div>

  <div class="report-grid analysis-sections">
    <div class="section activity-timeline">
      <h3>Activity Timeline</h3>
      ${this.generateTimelineRows(timeDistribution)}
    </div>

    <div class="section report-insights">
      <h3>Key Insights</h3>
      ${this.generateInsights(logs, actionDistribution, topAdmins)}
    </div>
  </div>

  <div class="section critical-events">
    <h3>Recent Critical Events</h3>
    ${this.generateRawAuditTable(criticalEvents, true)}
    <p class="compact-note">Showing the ${criticalEvents.length} highest-priority recent entries from ${logs.length} total logs.</p>
  </div>
    `;
  },

  /**
   * Generate full or compact audit table
   */
  generateRawAuditTable(logs, compact = false) {
    return `
  <table class="${compact ? "compact-table" : "raw-table"}">
    <thead>
      <tr>
        <th style="width: ${compact ? "18%" : "15%"}">Date & Time</th>
        <th style="width: ${compact ? "17%" : "15%"}">Admin User</th>
        <th style="width: ${compact ? "14%" : "12%"}">Action</th>
        <th style="width: ${compact ? "16%" : "18%"}">Target</th>
        <th style="width: ${compact ? "35%" : "40%"}">Description</th>
      </tr>
    </thead>
    <tbody>
      ${logs.map((log) => this.generateLogRow(log, compact)).join("")}
    </tbody>
  </table>
    `;
  },

  /**
   * Analyze action frequency
   */
  analyzeActionDistribution(logs) {
    const counts = new Map();

    logs.forEach((log) => {
      const action = this.getActionLabel(log);
      counts.set(action, (counts.get(action) || 0) + 1);
    });

    return [...counts.entries()]
      .map(([label, count]) => ({
        label,
        count,
        percent: logs.length ? Math.round((count / logs.length) * 100) : 0,
      }))
      .sort((a, b) => b.count - a.count || a.label.localeCompare(b.label));
  },

  /**
   * Identify administrators with the most activity
   */
  identifyTopAdmins(logs, limit = 5) {
    const counts = new Map();

    logs.forEach((log) => {
      const name = this.getAdminLabel(log);
      counts.set(name, (counts.get(name) || 0) + 1);
    });

    return [...counts.entries()]
      .map(([name, count]) => ({ name, count }))
      .sort((a, b) => b.count - a.count || a.name.localeCompare(b.name))
      .slice(0, limit);
  },

  /**
   * Extract recent and important audit events
   */
  extractCriticalEvents(logs, limit = 10) {
    return [...logs]
      .sort((a, b) => {
        const priorityDiff = this.getEventPriority(b) - this.getEventPriority(a);
        if (priorityDiff !== 0) return priorityDiff;

        const dateA = this.getLogDate(a)?.getTime() || 0;
        const dateB = this.getLogDate(b)?.getTime() || 0;
        return dateB - dateA;
      })
      .slice(0, limit);
  },

  /**
   * Analyze activity by day
   */
  calculateTimeDistribution(logs, limit = 6) {
    const counts = new Map();

    logs.forEach((log) => {
      const date = this.getLogDate(log);
      const key = date ? date.toLocaleDateString() : "Unknown";
      counts.set(key, (counts.get(key) || 0) + 1);
    });

    return [...counts.entries()]
      .map(([label, count]) => ({
        label,
        count,
        percent: logs.length ? Math.round((count / logs.length) * 100) : 0,
      }))
      .sort((a, b) => b.count - a.count || a.label.localeCompare(b.label))
      .slice(0, limit);
  },

  generateDistributionBars(items) {
    if (!items.length) {
      return '<p class="compact-note">No actions recorded.</p>';
    }

    return items.slice(0, 6).map((item) => `
      <div class="bar-row">
        <div class="bar-label">${this.escapeHTML(item.label)}</div>
        <div class="bar-track"><div class="bar-fill" style="width: ${item.percent}%"></div></div>
        <div class="bar-value">${item.count} (${item.percent}%)</div>
      </div>
    `).join("");
  },

  generateTimelineRows(items) {
    if (!items.length) {
      return '<p class="compact-note">No timeline data available.</p>';
    }

    return items.map((item) => `
      <div class="timeline-row">
        <div class="timeline-label">${this.escapeHTML(item.label)}</div>
        <div class="bar-track"><div class="bar-fill" style="width: ${item.percent}%"></div></div>
        <div class="timeline-value">${item.count}</div>
      </div>
    `).join("");
  },

  generateInsights(logs, actionDistribution, topAdmins) {
    const dominantAction = actionDistribution[0];
    const dominantAdmin = topAdmins[0];
    const criticalCount = logs.filter((log) => this.getEventPriority(log) >= 3).length;

    return `
      <p><strong>Total activity:</strong> ${logs.length} audit entries reviewed.</p>
      <p><strong>Most common action:</strong> ${dominantAction ? `${this.escapeHTML(dominantAction.label)} (${dominantAction.percent}%)` : "None"}</p>
      <p><strong>Most active admin:</strong> ${dominantAdmin ? `${this.escapeHTML(dominantAdmin.name)} (${dominantAdmin.count})` : "None"}</p>
      <p><strong>Critical events:</strong> ${criticalCount} high-priority entr${criticalCount === 1 ? "y" : "ies"} found.</p>
    `;
  },

  /**
   * Generate log row HTML
   */
  generateLogRow(log, compact = false) {
    const createdAt = log.created_at
      ? new Date(log.created_at).toLocaleString()
      : "-";
    const adminName = this.escapeHTML(this.getAdminLabel(log));
    const action = this.escapeHTML(this.getActionLabel(log));
    const target = this.getTargetLabel(log);
    const description = compact
      ? this.escapeHTML(this.truncateText(log.description || "-", 90))
      : this.escapeHTML(log.description || "-");

    return `
      <tr class="log-row">
        <td>${this.escapeHTML(createdAt)}</td>
        <td>${adminName}</td>
        <td><span class="badge">${action}</span></td>
        <td>${this.escapeHTML(target)}</td>
        <td>${description}</td>
      </tr>
    `;
  },

  getActionLabel(log) {
    return String(log.activity_type || log.action || "unknown").trim() || "unknown";
  },

  getAdminLabel(log) {
    return String(
      log.admin_name || log.admin_email || (log.admin_id ? `Admin #${log.admin_id}` : "System"),
    ).trim();
  },

  getTargetLabel(log) {
    return [
      log.target_type || "-",
      log.target_id ? `#${log.target_id}` : "",
    ].filter(Boolean).join(" ");
  },

  getLogDate(log) {
    if (!log.created_at) return null;
    const date = new Date(log.created_at);
    return Number.isNaN(date.getTime()) ? null : date;
  },

  getEventPriority(log) {
    const haystack = `${this.getActionLabel(log)} ${log.description || ""}`.toLowerCase();
    if (/(delete|remove|purge|revoke|block|ban)/.test(haystack)) return 5;
    if (/(failed|failure|reject|denied|unauthorized|invalid)/.test(haystack)) return 4;
    if (/(update|change|reset|cancel)/.test(haystack)) return 3;
    if (/(create|approve|login|logout)/.test(haystack)) return 2;
    return 1;
  },

  formatDateRange(sortedDates) {
    if (!sortedDates.length) return "-";
    const first = sortedDates[0].toLocaleDateString();
    const last = sortedDates[sortedDates.length - 1].toLocaleDateString();
    return first === last ? first : `${first} - ${last}`;
  },

  truncateText(text, maxLength) {
    const str = String(text || "");
    return str.length <= maxLength ? str : `${str.slice(0, maxLength - 3)}...`;
  },

  /**
   * Get filter summary
   */
  getFilterSummary(filters) {
    const parts = [];

    if (filters.action) {
      parts.push(`<p><strong>Action:</strong> ${this.escapeHTML(filters.action)}</p>`);
    }

    if (filters.admin_id) {
      parts.push(`<p><strong>Admin ID:</strong> ${this.escapeHTML(filters.admin_id)}</p>`);
    }

    if (filters.startDate) {
      parts.push(`<p><strong>Start Date:</strong> ${this.escapeHTML(filters.startDate)}</p>`);
    }

    if (filters.endDate) {
      parts.push(`<p><strong>End Date:</strong> ${this.escapeHTML(filters.endDate)}</p>`);
    }

    return parts.length > 0 ? parts.join("") : null;
  },

  /**
   * Export logs to CSV
   */
  exportLogsToCSV(logs, filename = "audit-logs.csv") {
    if (!logs || !logs.length) {
      if (typeof Utils !== "undefined" && Utils.error) {
        Utils.error("No logs to export");
      } else {
        alert("No logs to export");
      }
      return;
    }

    const headers = ["Date & Time", "Admin User", "Admin ID", "Action", "Target Type", "Target ID", "Description", "IP Address"];

    const rows = logs.map((log) => [
      log.created_at ? new Date(log.created_at).toLocaleString() : "",
      log.admin_name || "System",
      log.admin_id || "",
      log.activity_type || log.action || "",
      log.target_type || "",
      log.target_id || "",
      log.description || "",
      log.ip_address || "",
    ]);

    const csvContent = [
      headers.join(","),
      ...rows.map((row) => row.map((cell) => this.escapeCSV(cell)).join(",")),
    ].join("\n");

    const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);

    link.setAttribute("href", url);
    link.setAttribute("download", filename);
    link.style.visibility = "hidden";

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    if (typeof Utils !== "undefined" && Utils.success) {
      Utils.success("CSV exported successfully");
    }
  },

  /**
   * Escape CSV cell
   */
  escapeCSV(cell) {
    if (cell === null || cell === undefined) return "";

    const str = String(cell);

    if (str.includes(",") || str.includes('"') || str.includes("\n")) {
      return `"${str.replace(/"/g, '""')}"`;
    }

    return str;
  },

  /**
   * Escape HTML
   */
  escapeHTML(text) {
    if (text === null || text === undefined) return "";

    const map = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#x27;",
    };

    return String(text).replace(/[&<>"']/g, (char) => map[char]);
  },

  /**
   * Print generic report
   */
  printReport(title, content, options = {}) {
    const {
      subtitle = "",
      footer = "",
      styles = "",
    } = options;

    const printWindow = window.open("", "_blank");

    if (!printWindow) {
      alert("Please allow popups to print reports");
      return;
    }

    const html = `
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>${this.escapeHTML(title)}</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      padding: 20px;
      color: #333;
    }
    .header {
      text-align: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 3px solid #10b981;
    }
    .header h1 {
      font-size: 24px;
      color: #10b981;
      margin-bottom: 10px;
    }
    .footer {
      margin-top: 30px;
      padding-top: 20px;
      border-top: 2px solid #e5e7eb;
      text-align: center;
      color: #666;
      font-size: 11px;
    }
    ${styles}
  </style>
</head>
<body>
  <div class="header">
    <h1>${this.escapeHTML(title)}</h1>
    ${subtitle ? `<div class="subtitle">${this.escapeHTML(subtitle)}</div>` : ""}
  </div>
  <div class="content">
    ${content}
  </div>
  ${footer ? `<div class="footer">${footer}</div>` : ""}
</body>
</html>
    `;

    printWindow.document.write(html);
    printWindow.document.close();

    printWindow.onload = () => {
      setTimeout(() => {
        printWindow.print();
      }, 250);
    };
  },
};

// Export for module usage
if (typeof module !== "undefined" && module.exports) {
  module.exports = PrintUtils;
}
