<!-- Admin Activity Logs -->
<link rel="stylesheet" href="/assets/css/dashboard-improvements.css">
<link rel="stylesheet" href="/assets/css/admin-premium.css">

<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content">
    <header class="admin-topbar">
      <button class="btn btn-ghost sidebar-toggle" id="sidebarToggle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </button>
      <div class="topbar-title-block">
        <h1 class="page-title">Security Center</h1>
        <p class="text-sm text-secondary">
          Monitor admin activity, audit trails, and recent security events.
        </p>
      </div>
      <div class="topbar-actions">
        <a href="#/admin/dashboard" class="btn btn-ghost btn-sm">Back to Dashboard</a>
      </div>
    </header>

    <div class="content-body">
      <div class="card-improved p-lg mb-lg">
        <div class="card-header px-0 pt-0">
          <h3 class="card-title">Recent Activity</h3>
          <span class="text-muted">Latest audit entries</span>
        </div>
        <div id="recentActivityFeed" class="divide-y">
          <div class="loading-skeleton p-lg">Loading recent activity...</div>
        </div>
      </div>

      <div class="card-improved p-lg mb-lg">
        <div class="grid grid-cols-3 gap-md">
          <div class="form-group">
            <label class="form-label">Action Type</label>
            <input
              type="text"
              id="actionFilter"
              class="form-input"
              placeholder="create, update, delete..."
            />
          </div>
          <div class="form-group">
            <label class="form-label">Admin User ID</label>
            <input
              type="number"
              id="adminFilter"
              class="form-input"
              placeholder="Optional"
              min="1"
            />
          </div>
          <div class="form-group">
            <label class="form-label">Page Size</label>
            <select id="limitFilter" class="form-select">
              <option value="10" selected>10</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-md mb-md">
          <div class="form-group">
            <label class="form-label">Start Date</label>
            <input
              type="date"
              id="startDateFilter"
              class="form-input"
              placeholder="Start date"
            />
          </div>
          <div class="form-group">
            <label class="form-label">End Date</label>
            <input
              type="date"
              id="endDateFilter"
              class="form-input"
              placeholder="End date"
            />
          </div>
        </div>

        <div class="flex items-center gap-sm">
          <button class="btn btn-primary" onclick="applyLogFilters()">
            Apply Filters
          </button>
          <button class="btn btn-secondary" onclick="resetLogFilters()">
            Reset
          </button>
          <div class="ml-auto flex gap-sm">
            <button class="btn btn-success" onclick="printAuditReport()">
              📄 Print Report
            </button>
            <button class="btn btn-info" onclick="exportLogsCSV()">
              📊 Export CSV
            </button>
          </div>
        </div>
      </div>

      <div class="card-improved">
        <div class="card-header">
          <h3 class="card-title">Audit Log</h3>
          <span id="logCount" class="text-muted">0 records</span>
        </div>

        <div id="logsTable">
          <div class="loading-spinner p-xl">Loading activity logs...</div>
        </div>

        <div class="p-md border-t flex justify-between items-center">
          <button
            id="prevLogsBtn"
            class="btn btn-sm btn-ghost"
            disabled
            onclick="prevLogs()"
          >
            Previous
          </button>
          <span id="pageIndicator" class="text-sm text-secondary">Page 1</span>
          <button
            id="nextLogsBtn"
            class="btn btn-sm btn-ghost"
            onclick="nextLogs()"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
  (function () {
    const state = {
      limit: 10,
      offset: 0,
      page: 1,
      allLogs: [], // Store all logs for export
      currentFilters: {},
    };

    loadRecentActivity();
    loadLogs();

    async function loadRecentActivity() {
      try {
        const response = await API.admin.getActivities({ limit: 5, offset: 0 });
        const logs = Array.isArray(response?.data) ? response.data : [];
        renderRecentActivity(logs);
      } catch (error) {
        Utils.$("#recentActivityFeed").innerHTML =
          '<div class="alert alert-error m-lg">Failed to load recent activity</div>';
      }
    }

    async function loadLogs() {
      try {
        const params = {
          limit: state.limit,
          offset: state.offset,
        };

        const action = Utils.$("#actionFilter").value.trim();
        const adminId = Utils.$("#adminFilter").value.trim();
        const startDate = Utils.$("#startDateFilter")?.value;
        const endDate = Utils.$("#endDateFilter")?.value;

        if (action) {
          params.action = action;
        }

        if (adminId) {
          params.admin_id = adminId;
        }

        if (startDate) {
          params.start_date = startDate;
        }

        if (endDate) {
          params.end_date = endDate;
        }

        // Store current filters
        state.currentFilters = {
          action,
          admin_id: adminId,
          startDate,
          endDate,
        };

        const response = await API.admin.getActivities(params);
        const logs = Array.isArray(response?.data) ? response.data : [];

        // Store logs for export
        state.allLogs = logs;

        renderLogs(logs);

        Utils.$("#logCount").textContent = `${logs.length} record${
          logs.length === 1 ? "" : "s"
        }`;
        Utils.$("#pageIndicator").textContent = `Page ${state.page}`;
        Utils.$("#prevLogsBtn").disabled = state.page <= 1;
        Utils.$("#nextLogsBtn").disabled = logs.length < state.limit;
      } catch (error) {
        Utils.$("#logsTable").innerHTML =
          '<div class="alert alert-error m-lg">Failed to load activity logs</div>';
      }
    }

    function renderLogs(logs) {
      const tableContainer = Utils.$("#logsTable");

      if (!logs.length) {
        tableContainer.innerHTML =
          '<div class="p-xl text-center text-muted">No logs found for the selected filters.</div>';
        return;
      }

      tableContainer.innerHTML = `
        <table class="table-improved">
          <thead>
            <tr>
              <th>Date</th>
              <th>Admin</th>
              <th>Action</th>
              <th>Target</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            ${logs
              .map((log) => {
                const createdAt = log.created_at
                  ? new Date(log.created_at).toLocaleString()
                  : "-";
                const adminName = log.admin_name || "System";
                const action = log.activity_type || "-";
                const target = [
                  log.target_type || "-",
                  log.target_id ? `#${log.target_id}` : "",
                ]
                  .filter(Boolean)
                  .join(" ");
                const description = log.description || "-";

                return `
                  <tr>
                    <td>${Utils.escapeHtml(createdAt)}</td>
                    <td>${Utils.escapeHtml(adminName)}</td>
                    <td><span class="badge badge-secondary">${Utils.escapeHtml(action)}</span></td>
                    <td>${Utils.escapeHtml(target)}</td>
                    <td>${Utils.escapeHtml(description)}</td>
                  </tr>
                `;
              })
              .join("")}
          </tbody>
        </table>
      `;
    }

    function renderRecentActivity(logs) {
      const container = Utils.$("#recentActivityFeed");

      if (!logs.length) {
        container.innerHTML =
          '<div class="p-lg text-center text-muted">No recent activity found.</div>';
        return;
      }

      container.innerHTML = logs
        .map((log) => {
          const label = log.description || "Activity recorded";
          const actor = log.admin_name || "System";
          const when = log.created_at ? Utils.timeAgo(log.created_at) : "Recently";
          return `
            <div class="p-md">
              <div class="flex items-start justify-between gap-md">
                <div>
                  <div class="font-medium">${Utils.escapeHtml(label)}</div>
                  <div class="text-xs text-secondary mt-xs">${Utils.escapeHtml(actor)} · ${Utils.escapeHtml(when)}</div>
                </div>
                <span class="badge badge-secondary">${Utils.escapeHtml(log.activity_type || log.action || "log")}</span>
              </div>
            </div>
          `;
        })
        .join("");
    }

    window.applyLogFilters = function () {
      state.limit = Number(Utils.$("#limitFilter").value || 50);
      state.offset = 0;
      state.page = 1;
      loadLogs();
    };

    window.resetLogFilters = function () {
      Utils.$("#actionFilter").value = "";
      Utils.$("#adminFilter").value = "";
      Utils.$("#limitFilter").value = "10";
      if (Utils.$("#startDateFilter")) Utils.$("#startDateFilter").value = "";
      if (Utils.$("#endDateFilter")) Utils.$("#endDateFilter").value = "";
      state.limit = 10;
      state.offset = 0;
      state.page = 1;
      state.currentFilters = {};
      loadLogs();
    };

    window.nextLogs = function () {
      state.offset += state.limit;
      state.page += 1;
      loadLogs();
    };

    window.prevLogs = function () {
      if (state.page <= 1) {
        return;
      }

      state.offset = Math.max(0, state.offset - state.limit);
      state.page -= 1;
      loadLogs();
    };

    window.printAuditReport = function () {
      if (!state.allLogs || state.allLogs.length === 0) {
        if (typeof Utils !== 'undefined' && Utils.error) {
          Utils.error('No logs to print');
        } else {
          alert('No logs to print');
        }
        return;
      }

      if (typeof PrintUtils !== 'undefined') {
        PrintUtils.printAuditLogs(state.allLogs, state.currentFilters);
      } else {
        alert('Print utility not available');
      }
    };

    window.exportLogsCSV = function () {
      if (!state.allLogs || state.allLogs.length === 0) {
        if (typeof Utils !== 'undefined' && Utils.error) {
          Utils.error('No logs to export');
        } else {
          alert('No logs to export');
        }
        return;
      }

      if (typeof PrintUtils !== 'undefined') {
        const timestamp = new Date().toISOString().split('T')[0];
        const filename = `audit-logs-${timestamp}.csv`;
        PrintUtils.exportLogsToCSV(state.allLogs, filename);
      } else {
        alert('Export utility not available');
      }
    };
  })();
</script>

