<!-- Admin Dashboard -->
<link rel="stylesheet" href="/assets/css/dashboard-improvements.css">
<link rel="stylesheet" href="/assets/css/admin-premium.css">
<style>
  body[data-app-section="admin"] .dashboard-layout .dashboard-section-header {
    padding: 1.2rem 2.25rem !important;
    min-height: 4.25rem !important;
    display: flex !important;
    align-items: center !important;
    gap: 1.25rem !important;
  }

  body[data-app-section="admin"] .dashboard-layout .dashboard-section-header .card-title {
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1.45 !important;
  }

  body[data-app-section="admin"] .dashboard-layout .dashboard-section-header .btn {
    margin-left: auto !important;
    flex: 0 0 auto !important;
  }

  body[data-app-section="admin"] .dashboard-layout .dashboard-section-body {
    padding-left: 2.25rem !important;
    padding-right: 2.25rem !important;
  }

  body[data-app-section="admin"] .dashboard-layout #upcomingEvents > a,
  body[data-app-section="admin"] .dashboard-layout #upcomingEvents > div {
    padding-left: 2.25rem !important;
    padding-right: 2.25rem !important;
  }
</style>
<div class="dashboard-layout">
  <!-- Admin Sidebar -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="flex items-center gap-md">
        <div class="avatar avatar-md bg-white">
          <span class="text-primary">AMS</span>
        </div>
        <div>
          <div class="font-bold text-white sidebar-brand-name">
            Alumni System
          </div>
          <div class="text-xs opacity-75 sidebar-brand-subtitle">
            Admin Panel
          </div>
        </div>
      </div>
    </div>

    <nav class="sidebar-nav">
      <section class="sidebar-section default-open" data-key="main">
        <button
          type="button"
          class="sidebar-section-toggle"
          aria-expanded="true"
        >
          <span class="sidebar-section-title">Main</span>
          <svg
            class="sidebar-section-chevron"
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
        <div class="sidebar-section-links">
          <a
            href="#/admin/dashboard"
            data-match="/admin/dashboard"
            class="sidebar-link active"
            >Dashboard</a
          >
          <a
            href="#/admin/alumni"
            data-match="/admin/alumni"
            class="sidebar-link"
            >All Alumni</a
          >
          <a
            href="#/admin/alumni-verification"
            data-match="/admin/alumni-verification"
            class="sidebar-link"
            style="position:relative;"
            >Verification
            <span id="sidebarPendingBadge" style="display:none;position:absolute;top:.45rem;right:.6rem;min-width:1.25rem;height:1.25rem;padding:0 .35rem;border-radius:.65rem;background:linear-gradient(135deg,#f59e0b,#f97316);color:white;font-size:.65rem;font-weight:700;text-align:center;line-height:1.25rem;"></span>
          </a>
        </div>
      </section>

      <section class="sidebar-section default-open" data-key="management">
        <button
          type="button"
          class="sidebar-section-toggle"
          aria-expanded="true"
        >
          <span class="sidebar-section-title">Management</span>
          <svg
            class="sidebar-section-chevron"
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
        <div class="sidebar-section-links">
          <a
            href="#/admin/events"
            data-match="/admin/events"
            class="sidebar-link"
            >Events</a
          >
          <a
            href="#/admin/announcements"
            data-match="/admin/announcements"
            class="sidebar-link"
            >Announcements</a
          >
          <a
            href="#/admin/organization"
            data-match="/admin/organization"
            class="sidebar-link"
            >Organization</a
          >
          <a
            href="#/admin/security-center"
            data-match="/admin/security-center"
            class="sidebar-link"
            >Security Center</a
          >
        </div>
      </section>

      <section class="sidebar-section" data-key="configuration">
        <button
          type="button"
          class="sidebar-section-toggle"
          aria-expanded="false"
        >
          <span class="sidebar-section-title">Configuration</span>
          <svg
            class="sidebar-section-chevron"
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
        <div class="sidebar-section-links">
          <a
            href="#/admin/form-builder"
            data-match="/admin/form-builder"
            class="sidebar-link"
            >Form Builder</a
          >
          <a
            href="#/admin/gamification"
            data-match="/admin/gamification"
            class="sidebar-link"
            >Gamification</a
          >
          <a href="#/admin/users" data-match="/admin/users" class="sidebar-link"
            >Users</a
          >
        </div>
      </section>

      <section class="sidebar-section default-open" data-key="settings">
        <button
          type="button"
          class="sidebar-section-toggle"
          aria-expanded="true"
        >
          <span class="sidebar-section-title">Settings</span>
          <svg
            class="sidebar-section-chevron"
            width="14"
            height="14"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </button>
        <div class="sidebar-section-links">
          <a
            href="#/admin/settings/theme"
            data-match="/admin/settings/theme"
            class="sidebar-link"
            >Theme Settings</a
          >
          <a
            href="#/admin/settings/site-content"
            data-match="/admin/settings/site-content"
            class="sidebar-link"
            >Site Content</a
          >
          <a
            href="#/admin/settings/email-templates"
            data-match="/admin/settings/email-templates"
            class="sidebar-link"
            >Email Management</a
          >
        </div>
      </section>
    </nav>

    <div class="sidebar-footer">
      <div class="flex items-center gap-sm p-md border-t border-gray-700">
        <div class="avatar avatar-sm bg-primary">
          <span id="adminInitials">A</span>
        </div>
        <div class="flex-1">
          <div class="text-sm font-medium text-white" id="adminName">Admin</div>
          <div class="text-xs text-gray-400" id="adminRole">Administrator</div>
        </div>
        <button
          class="btn btn-ghost btn-icon text-gray-400"
          onclick="Auth.logout()"
          title="Logout"
        >
          <svg
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
            <polyline points="16 17 21 12 16 7" />
            <line x1="21" y1="12" x2="9" y2="12" />
          </svg>
        </button>
      </div>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <header class="admin-topbar">
      <button class="btn btn-ghost sidebar-toggle" id="sidebarToggle">
        <svg
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
        >
          <line x1="3" y1="12" x2="21" y2="12" />
          <line x1="3" y1="6" x2="21" y2="6" />
          <line x1="3" y1="18" x2="21" y2="18" />
        </svg>
      </button>
      <h1 class="page-title">System Admin Dashboard</h1>
      <div class="topbar-actions">
        <a href="#/" class="btn btn-ghost btn-sm" target="_blank">View Site</a>
      </div>
    </header>

    <div class="admin-content p-lg">
      <section class="dashboard-hero">
        <h2 id="dashboardWelcome">Welcome back, Administrator!</h2>
        <p>System-wide alumni analytics, engagement monitoring, and content activity.</p>
      </section>

      <!-- Stats Cards -->
      <div class="grid grid-cols-4 gap-lg mb-lg dashboard-charts-grid">
        <div class="stat-card-improved stat-card-primary">
          <div class="stat-icon">
            <svg
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
              <circle cx="9" cy="7" r="4" />
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value" id="totalAlumni">0</div>
            <div class="stat-label">Registered Alumni</div>
          </div>
        </div>

        <div class="stat-card-improved stat-card-success">
          <div class="stat-icon">
            <svg
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
              <line x1="16" y1="2" x2="16" y2="6" />
              <line x1="8" y1="2" x2="8" y2="6" />
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value" id="activeEvents">0</div>
            <div class="stat-label">Active Events</div>
          </div>
        </div>

        <div class="stat-card-improved stat-card-warning">
          <div class="stat-icon">
            <svg
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <circle cx="12" cy="12" r="10" />
              <polyline points="12 6 12 12 16 14" />
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value" id="pendingRegistrations">0</div>
            <div class="stat-label">Pending Registrations</div>
          </div>
        </div>

        <div class="stat-card-improved stat-card-info">
          <div class="stat-icon">
            <svg
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
            >
              <polygon
                points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"
              />
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value" id="totalPoints">0</div>
            <div class="stat-label">Points Distributed</div>
          </div>
        </div>
      </div>

      <!-- Analytics Overview -->
      <div class="grid grid-cols-4 gap-lg mb-lg" id="analyticsOverview">
        <div class="card-improved analytics-card">
          <div class="analytics-card-label">Active Alumni Rate</div>
          <div class="analytics-card-value" id="activeAlumniRate">0%</div>
          <div class="analytics-card-foot" id="activeAlumniRateNote">0 active in the last 30 days</div>
        </div>
        <div class="card analytics-card">
          <div class="analytics-card-label">Employed Alumni</div>
          <div class="analytics-card-value" id="pointsPerAlumni">0</div>
          <div class="analytics-card-foot">Employed or self-employed alumni</div>
        </div>
        <div class="card-improved analytics-card">
          <div class="analytics-card-label">Unemployed Alumni</div>
          <div class="analytics-card-value" id="registrationMomentum">0%</div>
          <div class="analytics-card-foot" id="registrationMomentumNote">Alumni marked unemployed</div>
        </div>
        <div class="card-improved analytics-card">
          <div class="analytics-card-label">Average Alumni / Batch</div>
          <div class="analytics-card-value" id="publishingPulse">0</div>
          <div class="analytics-card-foot">Average records across batches</div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="grid dashboard-chart-grid gap-lg mb-lg">
        <div class="card-improved">
          <div class="card-header">
            <h3 class="card-title">Alumni by College</h3>
          </div>
          <div class="card-body">
            <div id="collegeChart">
              <div class="loading-skeleton">Loading chart...</div>
            </div>
          </div>
        </div>

        <div class="card-improved">
          <div class="card-header">
            <h3 class="card-title">Registrations Trend</h3>
          </div>
          <div class="card-body">
            <div id="registrationChart">
              <div class="loading-skeleton">Loading chart...</div>
            </div>
          </div>
        </div>

        <div class="card-improved">
          <div class="card-header">
            <h3 class="card-title">Employment Trends</h3>
          </div>
          <div class="card-body">
            <div id="employmentTrendsChart">
              <div class="loading-skeleton">Loading chart...</div>
            </div>
          </div>
        </div>

        <div class="card-improved">
          <div class="card-header">
            <h3 class="card-title">Platform Snapshot</h3>
          </div>
          <div class="card-body">
            <div id="platformSnapshotChart">
              <div class="loading-skeleton">Loading chart...</div>
            </div>
          </div>
        </div>
      </div>

      <div class="dashboard-insight-grid mb-lg">
        <div class="card-improved">
          <div class="card-header">
            <h3 class="card-title">Engagement Mix</h3>
          </div>
          <div class="card-body">
            <div id="engagementMixChart">
              <div class="loading-skeleton">Loading chart...</div>
            </div>
          </div>
        </div>

        <div class="card-improved">
          <div class="card-header">
            <h3 class="card-title">Event Health</h3>
          </div>
          <div class="card-body">
            <div id="eventHealthChart">
              <div class="loading-skeleton">Loading chart...</div>
            </div>
          </div>
        </div>

        <div class="card-improved">
          <div class="card-header">
            <h3 class="card-title">Content Activity</h3>
          </div>
          <div class="card-body">
            <div id="contentActivityChart">
              <div class="loading-skeleton">Loading chart...</div>
            </div>
          </div>
        </div>
      </div>

      <div class="grid dashboard-chart-grid gap-lg mb-lg">
        <div class="card-improved">
          <div class="card-header">
            <h3 class="card-title">Alumni by Graduation Year</h3>
          </div>
          <div class="card-body">
            <div id="graduationYearChart">
              <div class="loading-skeleton">Loading chart...</div>
            </div>
          </div>
        </div>

        <div class="card-improved">
          <div class="card-header">
            <h3 class="card-title">Event Attendance Trend</h3>
          </div>
          <div class="card-body">
            <div id="eventAttendanceChart">
              <div class="loading-skeleton">Loading chart...</div>
            </div>
          </div>
        </div>

        <div class="card-improved">
          <div class="card-header">
            <h3 class="card-title">Top Employed Batches</h3>
          </div>
          <div class="card-body">
            <div id="employedBatchesChart">
              <div class="loading-skeleton">Loading chart...</div>
            </div>
          </div>
        </div>

        <div class="card-improved">
          <div class="card-header">
            <h3 class="card-title">Alumni by Batch</h3>
          </div>
          <div class="card-body">
            <div id="batchDistributionChart">
              <div class="loading-skeleton">Loading chart...</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Upcoming Events -->
      <div class="grid grid-cols-2 gap-lg">
        <div class="card-improved">
          <div class="card-header dashboard-section-header">
            <h3 class="card-title">Upcoming Events</h3>
            <a href="#/admin/events" class="btn btn-ghost btn-sm">View All</a>
          </div>
          <div class="card-body p-0">
            <div id="upcomingEvents" class="divide-y">
              <div class="loading-skeleton p-lg">Loading...</div>
            </div>
          </div>
        </div>

        <div class="card-improved">
          <div class="card-header dashboard-section-header">
            <h3 class="card-title">Security Center</h3>
            <a href="#/admin/security-center" class="btn btn-ghost btn-sm">View All</a>
          </div>
          <div class="card-body p-0">
            <div class="p-lg text-secondary dashboard-section-body">
              Admin activity and audit history now live in the Security Center.
              Use the link above to review recent actions and filters.
            </div>
          </div>
        </div>
      </div>

      <!-- Top Alumni -->
      <div class="card-improved mt-lg">
        <div class="card-header dashboard-section-header">
          <h3 class="card-title">Top Alumni by Points</h3>
        </div>
        <div class="card-body p-0">
          <table class="table-improved">
            <thead>
              <tr>
                <th>Rank</th>
                <th>Alumni</th>
                <th>College</th>
                <th>Points</th>
                <th>Badge</th>
                <th>Events Attended</th>
              </tr>
            </thead>
            <tbody id="topAlumni">
              <tr>
                <td colspan="6" class="text-center">Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card-improved mt-lg">
        <div class="card-header dashboard-section-header">
          <h3 class="card-title">Most Active Alumni</h3>
        </div>
        <div class="card-body p-0">
          <table class="table-improved">
            <thead>
              <tr>
                <th>Rank</th>
                <th>Alumni</th>
                <th>Batch</th>
                <th>Events Attended</th>
                <th>Points</th>
              </tr>
            </thead>
            <tbody id="topActiveAlumni">
              <tr>
                <td colspan="5" class="text-center">Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>


<script>
  (function () {
    const user = API.getUser();

    // Check admin access
    if (!user || !["admin", "campus_admin", "staff", "system_admin"].includes(user.role)) {
      Utils.error("Access denied");
      Router.navigate("/login");
      return;
    }

    // Set dashboard title based on role
    const roleTitles = {
      'system_admin': 'System Admin Dashboard',
      'admin': 'Admin Dashboard',
      'campus_admin': 'Campus Admin Dashboard',
      'staff': 'Staff Dashboard'
    };

    // Update title in header
    const pageTitle = document.querySelector('.page-title');
    if (pageTitle) {
      pageTitle.textContent = roleTitles[user.role] || "Admin Dashboard";
    }

    // Update admin info
    setText("#adminName", user.name || "Admin");
    setText("#adminInitials", Utils.getInitials(user.name || "A"));
    
    const roleLabels = {
      'system_admin': 'System Admin',
      'admin': 'Administrator',
      'campus_admin': 'Campus Admin',
      'staff': 'Staff'
    };
    
    setText("#adminRole", roleLabels[user.role] || "Administrator");
    setText("#dashboardWelcome", `Welcome back, ${user.name || "Administrator"}!`);

    // Load dashboard data
    loadDashboard();
    
    // Real-time dashboard refresh every 45 seconds
    setInterval(loadDashboard, 45000);

    async function loadDashboard() {
      const [statsFetch, alumniFetch, eventsFetch] = await Promise.all([
        guardedRequest("dashboard stats", API.admin.getDashboardStats(), {}),
        guardedRequest(
          "leaderboard",
          API.gamification.getLeaderboard({ limit: 10 }),
          { leaderboard: [] },
        ),
        guardedRequest(
          "events",
          API.events.list({ status: "upcoming", limit: 5 }),
          { events: [] },
        ),
      ]);

      const stats = normalizeDashboardStats(unwrapPayload(statsFetch.response));
      const alumniPayload = unwrapPayload(alumniFetch.response);
      const eventsPayload = unwrapPayload(eventsFetch.response);

      setText("#totalAlumni", formatNumber(stats.total_alumni));
      setText("#activeEvents", formatNumber(stats.active_events));
      setText("#pendingRegistrations", formatNumber(stats.pending_registrations));
      setText("#totalPoints", formatNumber(stats.total_points));

      renderAnalyticsOverview(stats);

      if (statsFetch.ok) {
        safeRenderChart("#collegeChart", () => renderCollegeChart(stats.alumni_by_college));
        safeRenderChart(
          "#registrationChart",
          () => renderRegistrationChart(stats.user_growth.length ? stats.user_growth : stats.registrations_trend),
        );
        safeRenderChart("#employmentTrendsChart", () => renderEmploymentTrends(stats.employment_trends));
        safeRenderChart("#platformSnapshotChart", () => renderPlatformSnapshot(stats));
        safeRenderChart("#engagementMixChart", () => renderEngagementMix(stats));
        safeRenderChart("#eventHealthChart", () => renderEventHealth(stats));
        safeRenderChart("#contentActivityChart", () => renderContentActivity(stats));
        safeRenderChart("#graduationYearChart", () => renderGraduationYearChart(stats.alumni_by_graduation_year));
        safeRenderChart("#eventAttendanceChart", () => renderEventAttendanceChart(stats.event_attendance_trend));
        safeRenderChart("#employedBatchesChart", () => renderEmployedBatchesChart(stats.top_employed_batches));
        safeRenderChart("#batchDistributionChart", () => renderBatchDistributionChart(stats.alumni_by_batch));
      } else {
        renderAllChartErrors(statsFetch.error);
      }

      renderTopAlumni(extractList(alumniPayload, ["leaderboard", "alumni", "data"]));
      renderTopActiveAlumni(stats.top_active_alumni);
      renderEvents(extractList(eventsPayload, ["events", "data"]));
    }

    async function guardedRequest(label, promise, fallback) {
      try {
        return { ok: true, response: await promise };
      } catch (error) {
        console.warn(`Dashboard ${label} request failed:`, error);
        return { ok: false, error, response: { data: fallback } };
      }
    }

    function unwrapPayload(response) {
      if (!response || typeof response !== "object") {
        return {};
      }

      let payload = Object.prototype.hasOwnProperty.call(response, "data")
        ? response.data
        : response;

      if (
        payload &&
        typeof payload === "object" &&
        Object.prototype.hasOwnProperty.call(payload, "data") &&
        (Object.prototype.hasOwnProperty.call(payload, "success") ||
          Object.prototype.hasOwnProperty.call(payload, "message"))
      ) {
        payload = payload.data;
      }

      return payload || {};
    }

    function normalizeDashboardStats(payload) {
      const source = payload && typeof payload === "object" ? payload : {};
      const stats = source.stats && typeof source.stats === "object"
        ? { ...source, ...source.stats }
        : source;
      const employmentTrends = normalizeEmploymentData(stats.employment_trends);
      const employmentCount = (keys) =>
        employmentTrends
          .filter((item) => keys.includes(item.status_key))
          .reduce((sum, item) => sum + Number(item.count || 0), 0);
      const alumniByBatch = toChartArray(stats.alumni_by_batch);
      const batchAverage = alumniByBatch.length
        ? alumniByBatch.reduce((sum, item) => sum + Number(item.count || 0), 0) / alumniByBatch.length
        : 0;

      return {
        ...stats,
        total_alumni: Number(stats.total_alumni || 0),
        active_alumni: Number(stats.active_alumni || 0),
        active_events: Number(stats.active_events || 0),
        pending_registrations: Number(stats.pending_registrations || 0),
        total_points: Number(stats.total_points || 0),
        employed_alumni: Number(stats.employed_alumni || employmentCount(["employed", "self_employed"])),
        unemployed_alumni: Number(stats.unemployed_alumni || employmentCount(["unemployed"])),
        average_alumni_per_batch: Number(stats.average_alumni_per_batch || batchAverage || 0),
        active_announcements: Number(stats.active_announcements || 0),
        events_this_month: Number(stats.events_this_month || 0),
        alumni_by_college: toChartArray(stats.alumni_by_college),
        alumni_by_graduation_year: toChartArray(stats.alumni_by_graduation_year),
        alumni_by_batch: alumniByBatch,
        top_employed_batches: toChartArray(stats.top_employed_batches),
        event_attendance_trend: toChartArray(stats.event_attendance_trend),
        top_active_batches: toChartArray(stats.top_active_batches),
        top_active_alumni: Array.isArray(stats.top_active_alumni) ? stats.top_active_alumni : [],
        registrations_trend: toChartArray(stats.registrations_trend || stats.registration_trend),
        user_growth: toChartArray(stats.user_growth),
        employment_trends: employmentTrends,
        engagement_metrics: stats.engagement_metrics && typeof stats.engagement_metrics === "object"
          ? stats.engagement_metrics
          : {},
      };
    }

    function toChartArray(value) {
      if (Array.isArray(value)) {
        return value.filter(Boolean);
      }

      if (value && typeof value === "object") {
        return Object.entries(value).map(([key, count]) => ({
          name: key,
          label: key,
          status_key: key,
          count: Number(count || 0),
        }));
      }

      return [];
    }

    function normalizeEmploymentData(data) {
      if (Array.isArray(data)) {
        return data;
      }

      if (!data || typeof data !== "object") {
        return [];
      }

      const labelMap = {
        employed: "Employed",
        self_employed: "Self-Employed",
        unemployed: "Unemployed",
        student: "Student",
        retired: "Retired",
        not_specified: "Not Specified",
      };

      return Object.entries(data)
        .map(([key, count]) => ({
          status_key: key,
          label: labelMap[key] || key.replace(/_/g, " ").replace(/\b\w/g, (letter) => letter.toUpperCase()),
          count: Number(count || 0),
        }))
        .filter((item) => item.count > 0)
        .sort((a, b) => b.count - a.count);
    }

    function extractList(payload, keys) {
      if (Array.isArray(payload)) {
        return payload;
      }

      if (!payload || typeof payload !== "object") {
        return [];
      }

      for (const key of keys) {
        if (Array.isArray(payload[key])) {
          return payload[key];
        }
      }

      return [];
    }

    function formatNumber(value, digits = 0) {
      const number = Number(value || 0);
      return number.toLocaleString(undefined, {
        maximumFractionDigits: digits,
        minimumFractionDigits: digits,
      });
    }

    function setText(selector, value) {
      const element = Utils.$(selector);
      if (element) {
        element.textContent = value;
      }
    }

    function safeRenderChart(selector, renderer) {
      try {
        renderer();
      } catch (error) {
        console.error(`Failed to render ${selector}:`, error);
        renderChartState(selector, "Chart unavailable", "The data loaded, but this chart could not be drawn.", "error");
      }
    }

    function renderAllChartErrors(error) {
      const message = error?.message || "Unable to load dashboard metrics.";
      [
        "#collegeChart",
        "#registrationChart",
        "#employmentTrendsChart",
        "#platformSnapshotChart",
        "#engagementMixChart",
        "#eventHealthChart",
        "#contentActivityChart",
        "#graduationYearChart",
        "#eventAttendanceChart",
        "#employedBatchesChart",
        "#batchDistributionChart",
      ].forEach((selector) => {
        renderChartState(selector, "Dashboard data unavailable", message, "error");
      });
    }

    function renderChartState(selector, title, detail, type = "empty") {
      const container = Utils.$(selector);
      if (!container) {
        return;
      }

      container.innerHTML = `
        <div class="chart-${type}-state">
          <div class="chart-state-title">${Utils.escapeHtml(title)}</div>
          <div class="chart-state-detail">${Utils.escapeHtml(detail || "")}</div>
        </div>
      `;
    }

    function renderAnalyticsOverview(stats) {
      const engagement = stats.engagement_metrics || {};
      const activeRate = Number(engagement.active_users_rate || 0);
      const activeUsers = Number(engagement.active_users_30d || stats.active_alumni || 0);
      const userGrowth = Array.isArray(stats.user_growth) ? stats.user_growth : [];
      const registrationsTrend = Array.isArray(stats.registrations_trend) ? stats.registrations_trend : [];
      const registrationSeries = userGrowth.length ? userGrowth : registrationsTrend;
      const registrations = registrationSeries.map((item) => Number(item.count || 0));
      const currentMonth = registrations.length ? registrations[registrations.length - 1] : 0;
      const setText = (selector, value) => {
        const element = Utils.$(selector);
        if (element) {
          element.textContent = value;
        }
      };

      setText("#activeAlumniRate", `${Math.round(activeRate)}%`);
      setText("#activeAlumniRateNote", `${activeUsers.toLocaleString()} active in the last 30 days`);
      setText("#pointsPerAlumni", formatNumber(stats.employed_alumni));
      setText("#registrationMomentum", formatNumber(stats.unemployed_alumni));
      setText("#registrationMomentumNote", `${currentMonth.toLocaleString()} new registrations this month`);
      setText("#publishingPulse", formatNumber(stats.average_alumni_per_batch, 1));
    }

    function renderCollegeChart(data) {
      const container = Utils.$("#collegeChart");
      data = toChartArray(data);

      if (!data.length) {
        renderChartState("#collegeChart", "No college data yet", "Alumni distribution will appear when alumni records include colleges.");
        return;
      }

      const total = data.reduce((sum, item) => sum + Number(item.count || 0), 0) || 1;
      const max = Math.max(...data.map((d) => Number(d.count || 0)), 1);
      const palette = [
        { start: "#10b981", end: "#059669" },
        { start: "#3b82f6", end: "#2563eb" },
        { start: "#f59e0b", end: "#d97706" },
        { start: "#14b8a6", end: "#0f766e" },
        { start: "#8b5cf6", end: "#6d28d9" },
        { start: "#f97316", end: "#ea580c" },
      ];
      container.innerHTML = `
            <div class="chart-shell chart-shell-bars">
                <div class="chart-head">
                    <div>
                        <div class="chart-subtitle">Distribution</div>
                        <div class="chart-title">Alumni by College</div>
                    </div>
                    <div class="chart-pill">${data.length} colleges</div>
                </div>
                <div class="chart-list">
                    ${data
                      .map((item, index) => {
                        const count = Number(item.count || 0);
                        const percent = Math.round((count / total) * 100);
                        const width = Math.max((count / max) * 100, count > 0 ? 8 : 0);
                        const accent = palette[index % palette.length];
                        const safeName = Utils.escapeHtml(item.name || "Unknown");
                        const countLabel = count.toLocaleString();
                        return `
                          <div class="chart-row" style="--chart-accent: ${accent.start}; --chart-accent-strong: ${accent.end};">
                              <div class="chart-row-label">
                                  <span class="chart-index">${index + 1}</span>
                                  <span class="truncate" title="${safeName}">${safeName}</span>
                              </div>
                              <div class="chart-row-track"><div class="chart-row-fill" style="width: ${width}%;"></div></div>
                              <div class="chart-row-meta">${countLabel} &middot; ${percent}%</div>
                          </div>
                        `;
                      })
                      .join("")}
                </div>
            </div>
        `;
    }

    function renderRegistrationChart(data) {
      const container = Utils.$("#registrationChart");
      data = toChartArray(data);

      if (window.UserGrowthChart?.render) {
        try {
          window.UserGrowthChart.render(container, data);
          if (!container.querySelector(".loading-skeleton")) {
            return;
          }
        } catch (error) {
          console.warn("UserGrowthChart failed, using fallback renderer:", error);
        }
      }

      if (!data.length) {
        renderChartState("#registrationChart", "No registration data yet", "New alumni registrations will appear here over time.");
        return;
      }

      const values = data.map((item) => Number(item.count || 0));
      const max = Math.max(...values, 1);
      const width = 560;
      const height = 220;
      const paddingX = 28;
      const paddingTop = 16;
      const paddingBottom = 26;
      const innerWidth = width - paddingX * 2;
      const innerHeight = height - paddingTop - paddingBottom;
      const stepX = data.length > 1 ? innerWidth / (data.length - 1) : innerWidth;
      const getPoint = (value, index) => {
        const x = paddingX + (data.length > 1 ? index * stepX : innerWidth / 2);
        const y = paddingTop + (1 - value / max) * innerHeight;
        return { x, y };
      };
      const points = data
        .map((item, index) => {
          const value = Number(item.count || 0);
          const point = getPoint(value, index);
          return `${point.x},${point.y}`;
        })
        .join(" ");
      const areaPath = `M ${paddingX} ${height - paddingBottom} L ${data
        .map((item, index) => {
          const value = Number(item.count || 0);
          const point = getPoint(value, index);
          return `${point.x} ${point.y}`;
        })
        .join(" L ")} L ${width - paddingX} ${height - paddingBottom} Z`;
      const gridLines = 4;
      const grid = Array.from({ length: gridLines + 1 })
        .map((_, index) => {
          const y = paddingTop + (innerHeight / gridLines) * index;
          return `<line class="line-chart-grid" x1="${paddingX}" x2="${width - paddingX}" y1="${y}" y2="${y}"></line>`;
        })
        .join("");

      container.innerHTML = `
            <div class="chart-shell chart-shell-line">
                <div class="chart-head">
                    <div>
                        <div class="chart-subtitle">Trend</div>
                        <div class="chart-title">Registrations Over Time</div>
                    </div>
                    <div class="chart-pill">6 months</div>
                </div>
                <div class="line-chart-wrap">
                    <svg viewBox="0 0 ${width} ${height}" class="line-chart" preserveAspectRatio="none" aria-label="Registrations trend chart">
                        <defs>
                            <linearGradient id="registrationAreaGradient" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="rgba(16, 185, 129, 0.28)" />
                                <stop offset="100%" stop-color="rgba(16, 185, 129, 0.04)" />
                            </linearGradient>
                        </defs>
                        ${grid}
                        <path d="${areaPath}" class="line-chart-area"></path>
                        <polyline points="${points}" class="line-chart-path"></polyline>
                        ${data
                          .map((item, index) => {
                            const value = Number(item.count || 0);
                            const label = Utils.escapeHtml(item.month || item.month_label || item.month_key || "");
                            const point = getPoint(value, index);
                            const labelY = height - paddingBottom + 6;
                            const valueY = Math.max(point.y - 12, paddingTop + 12);
                            const valueLabel = value.toLocaleString();
                            return `
                              <g>
                                  <circle cx="${point.x}" cy="${point.y}" r="5" class="line-chart-dot"></circle>
                                  <circle cx="${point.x}" cy="${point.y}" r="2.2" fill="var(--primary-600)"></circle>
                                  <text x="${point.x}" y="${labelY}" text-anchor="middle" class="line-chart-label">${label}</text>
                                  <text x="${point.x}" y="${valueY}" text-anchor="middle" class="line-chart-value">${valueLabel}</text>
                              </g>
                            `;
                          })
                          .join("")}
                    </svg>
                </div>
            </div>
        `;
    }

    function renderEmploymentTrends(data) {
      const container = Utils.$("#employmentTrendsChart");
      const employmentArray = normalizeEmploymentData(data);

      if (window.EmploymentTrendsChart?.render) {
        try {
          window.EmploymentTrendsChart.render(container, employmentArray);
          if (!container.querySelector(".loading-skeleton")) {
            return;
          }
        } catch (error) {
          console.warn("EmploymentTrendsChart failed, using fallback renderer:", error);
        }
      }

      if (!employmentArray.length) {
        renderChartState("#employmentTrendsChart", "No employment data yet", "Employment information will appear after alumni profiles are completed.");
        return;
      }

      const total = employmentArray.reduce((sum, item) => sum + Number(item.count || 0), 0) || 1;
      const max = Math.max(...employmentArray.map((item) => Number(item.count || 0)), 1);
      const palette = [
        { start: "#14b8a6", end: "#0f766e" },
        { start: "#10b981", end: "#059669" },
        { start: "#3b82f6", end: "#2563eb" },
        { start: "#f59e0b", end: "#d97706" },
        { start: "#8b5cf6", end: "#6d28d9" },
        { start: "#f97316", end: "#ea580c" },
      ];
      container.innerHTML = `
          <div class="chart-shell chart-shell-bars">
            <div class="chart-head">
              <div>
                <div class="chart-subtitle">Workforce</div>
                <div class="chart-title">Employment Trends</div>
              </div>
              <div class="chart-pill">${employmentArray.length} categories</div>
            </div>
            <div class="chart-list">
              ${employmentArray
                .map((item, index) => {
                  const count = Number(item.count || 0);
                  const percent = Math.round((count / total) * 100);
                  const width = Math.max((count / max) * 100, count > 0 ? 8 : 0);
                  const accent = palette[index % palette.length];
                  const safeLabel = Utils.escapeHtml(item.label || item.name || item.status_key || "Unknown");
                  const countLabel = count.toLocaleString();
                  return `
                    <div class="chart-row" style="--chart-accent: ${accent.start}; --chart-accent-strong: ${accent.end};">
                      <div class="chart-row-label">
                        <span class="chart-index">${index + 1}</span>
                        <span class="truncate" title="${safeLabel}">${safeLabel}</span>
                      </div>
                      <div class="chart-row-track"><div class="chart-row-fill" style="width: ${width}%;"></div></div>
                      <div class="chart-row-meta">${countLabel} &middot; ${percent}%</div>
                    </div>
                  `;
                })
                .join("")}
            </div>
          </div>
        `;
    }

    function renderPlatformSnapshot(stats) {
      const container = Utils.$("#platformSnapshotChart");
      const data = [
        { label: "Active Alumni", value: stats.active_alumni || 0, color: "bg-success" },
        { label: "Pending Registrations", value: stats.pending_registrations || 0, color: "bg-warning" },
        { label: "Active Events", value: stats.active_events || 0, color: "bg-primary" },
        { label: "Published Announcements", value: stats.active_announcements || 0, color: "bg-info" },
        { label: "Events This Month", value: stats.events_this_month || 0, color: "bg-secondary" },
      ];

      const colorMap = {
        "bg-success": "#10b981",
        "bg-warning": "#f59e0b",
        "bg-primary": "#3b82f6",
        "bg-info": "#06b6d4",
        "bg-secondary": "#64748b",
      };

      const rawTotal = data.reduce((sum, item) => sum + Number(item.value || 0), 0);
      if (!rawTotal) {
        renderChartState("#platformSnapshotChart", "No platform activity yet", "Registrations, events, and announcements will appear here once available.");
        return;
      }

      const total = rawTotal;
      const max = Math.max(...data.map((item) => Number(item.value || 0)), 1);
      const sortedByValue = [...data].sort((a, b) => Number(b.value || 0) - Number(a.value || 0));
      const highlight = sortedByValue.find((item) => Number(item.value || 0) > 0) || data[0];
      const highlightPercent = Math.min(
        Math.round((Number(highlight?.value || 0) / total) * 100),
        100,
      );
      const ringStops = [];
      let currentStop = 0;
      data.forEach((item) => {
        const value = Number(item.value || 0);
        if (!value) {
          return;
        }
        const percent = (value / total) * 100;
        const start = currentStop;
        const end = Math.min(currentStop + percent, 100);
        const color = colorMap[item.color] || "#94a3b8";
        ringStops.push(`${color} ${start}% ${end}%`);
        currentStop = end;
      });
      if (!ringStops.length) {
        ringStops.push("#e2e8f0 0 100%");
      }

      container.innerHTML = `
          <div class="chart-shell chart-shell-snapshot">
            <div class="chart-head">
              <div>
                <div class="chart-subtitle">Operational snapshot</div>
                <div class="chart-title">Platform Activity Mix</div>
              </div>
              <div class="chart-pill">${Math.round(total)} total</div>
            </div>
            <div class="snapshot-layout">
              <div class="snapshot-ring" style="--ring-percent: ${highlightPercent}%; --snapshot-ring-gradient: conic-gradient(${ringStops.join(", ")});">
                <div class="snapshot-ring-inner">
                  <div class="snapshot-ring-value">${highlightPercent}%</div>
                  <div class="snapshot-ring-label">${Utils.escapeHtml(highlight?.label || "activity")}</div>
                </div>
              </div>
              <div class="snapshot-legend">
                ${data
                  .map((item) => {
                  const value = Number(item.value || 0);
                  const percent = Math.round((value / total) * 100);
                  const valueLabel = value.toLocaleString();
                  return `
                    <div class="snapshot-legend-item">
                      <div class="snapshot-legend-label"><span class="snapshot-dot ${item.color}"></span><span>${Utils.escapeHtml(item.label)}</span></div>
                      <div class="snapshot-legend-meta">${valueLabel} &middot; ${percent}%</div>
                      <div class="snapshot-legend-track"><div class="snapshot-legend-fill ${item.color}" style="width: ${Math.max((value / max) * 100, value > 0 ? 8 : 0)}%"></div></div>
                    </div>
                  `;
                  })
                  .join("")}
              </div>
            </div>
          </div>
        `;
    }

    function renderMiniMetricChart(selector, title, subtitle, items) {
      const container = Utils.$(selector);
      const normalized = (items || [])
        .map((item) => ({
          label: item.label,
          value: Number(item.value || 0),
          color: item.color || "#10b981",
        }))
        .filter((item) => item.label);

      if (!normalized.length || normalized.every((item) => item.value <= 0)) {
        renderChartState(selector, `No ${title.toLowerCase()} data yet`, subtitle || "Metrics will appear once activity is recorded.");
        return;
      }

      const max = Math.max(...normalized.map((item) => item.value), 1);
      const total = normalized.reduce((sum, item) => sum + item.value, 0);
      container.innerHTML = `
        <div class="chart-shell chart-shell-bars">
          <div class="chart-head">
            <div>
              <div class="chart-subtitle">${Utils.escapeHtml(subtitle || "Overview")}</div>
              <div class="chart-title">${Utils.escapeHtml(title)}</div>
            </div>
            <div class="chart-pill">${formatNumber(total)} total</div>
          </div>
          <div class="chart-list">
            ${normalized
              .map((item, index) => {
                const width = Math.max((item.value / max) * 100, item.value > 0 ? 8 : 0);
                const percent = total ? Math.round((item.value / total) * 100) : 0;
                return `
                  <div class="chart-row" style="--chart-accent: ${item.color}; --chart-accent-strong: ${item.color};">
                    <div class="chart-row-label">
                      <span class="chart-index">${index + 1}</span>
                      <span class="truncate">${Utils.escapeHtml(item.label)}</span>
                    </div>
                    <div class="chart-row-track"><div class="chart-row-fill" style="width: ${width}%;"></div></div>
                    <div class="chart-row-meta">${formatNumber(item.value)} &middot; ${percent}%</div>
                  </div>
                `;
              })
              .join("")}
          </div>
        </div>
      `;
    }

    function renderEngagementMix(stats) {
      const engagement = stats.engagement_metrics || {};
      renderMiniMetricChart("#engagementMixChart", "Engagement Mix", "Last 30 days", [
        { label: "Active Alumni", value: engagement.active_users_30d || stats.active_alumni, color: "#10b981" },
        { label: "Pending Registrations", value: stats.pending_registrations, color: "#f59e0b" },
        { label: "Points Distributed", value: stats.total_points, color: "#8b5cf6" },
      ]);
    }

    function renderEventHealth(stats) {
      renderMiniMetricChart("#eventHealthChart", "Event Health", "Operational pulse", [
        { label: "Active Events", value: stats.active_events, color: "#3b82f6" },
        { label: "Events This Month", value: stats.events_this_month, color: "#14b8a6" },
        { label: "Upcoming Queue", value: Math.max(stats.active_events - stats.events_this_month, 0), color: "#64748b" },
      ]);
    }

    function renderContentActivity(stats) {
      const engagement = stats.engagement_metrics || {};
      renderMiniMetricChart("#contentActivityChart", "Content Activity", "Publishing flow", [
        { label: "Published Announcements", value: engagement.published_announcements || stats.active_announcements, color: "#f97316" },
        { label: "Active Announcements", value: stats.active_announcements, color: "#10b981" },
        { label: "Registration Momentum", value: Math.abs(Number(engagement.registration_momentum || 0)), color: "#155dfc" },
      ]);
    }

    function chartItemsFromSeries(data, labelKeys = ["label", "name", "year", "batch", "month_label", "month"]) {
      return toChartArray(data).map((item) => {
        const labelKey = labelKeys.find((key) => item[key] !== undefined && item[key] !== null && item[key] !== "");
        return {
          label: labelKey ? String(item[labelKey]) : "Unknown",
          value: Number(item.count || item.value || 0),
        };
      });
    }

    function renderGraduationYearChart(data) {
      const items = chartItemsFromSeries(data, ["year", "graduation_year", "label", "name"]);
      renderMiniMetricChart(
        "#graduationYearChart",
        "Alumni by Graduation Year",
        "Graduating class distribution",
        items.map((item) => ({ ...item, color: "#10b981" })),
      );
    }

    function renderEventAttendanceChart(data) {
      renderSimpleLineChart(
        "#eventAttendanceChart",
        "Event Attendance",
        "Alumni attendance over time",
        toChartArray(data),
      );
    }

    function renderEmployedBatchesChart(data) {
      const items = chartItemsFromSeries(data, ["batch", "batch_year", "label", "name"]);
      renderMiniMetricChart(
        "#employedBatchesChart",
        "Top Employed Batches",
        "Batches with employed alumni",
        items.map((item) => ({ ...item, label: `Batch ${item.label}`, color: "#14b8a6" })),
      );
    }

    function renderBatchDistributionChart(data) {
      const items = chartItemsFromSeries(data, ["batch", "batch_year", "label", "name"]);
      renderMiniMetricChart(
        "#batchDistributionChart",
        "Alumni by Batch",
        "Batch record distribution",
        items.slice(0, 12).map((item) => ({ ...item, label: `Batch ${item.label}`, color: "#8b5cf6" })),
      );
    }

    function renderSimpleLineChart(selector, title, subtitle, data) {
      const container = Utils.$(selector);
      const series = toChartArray(data);

      if (!series.length) {
        renderChartState(selector, `No ${title.toLowerCase()} data yet`, "The chart will appear after event attendance is recorded.");
        return;
      }

      const values = series.map((item) => Number(item.count || item.value || 0));
      const max = Math.max(...values, 1);
      const width = 560;
      const height = 220;
      const paddingX = 28;
      const paddingTop = 18;
      const paddingBottom = 30;
      const innerWidth = width - paddingX * 2;
      const innerHeight = height - paddingTop - paddingBottom;
      const stepX = series.length > 1 ? innerWidth / (series.length - 1) : innerWidth;
      const pointFor = (value, index) => ({
        x: paddingX + (series.length > 1 ? index * stepX : innerWidth / 2),
        y: paddingTop + (1 - value / max) * innerHeight,
      });
      const points = series
        .map((item, index) => {
          const point = pointFor(Number(item.count || item.value || 0), index);
          return `${point.x},${point.y}`;
        })
        .join(" ");
      const areaPath = `M ${paddingX} ${height - paddingBottom} L ${series
        .map((item, index) => {
          const point = pointFor(Number(item.count || item.value || 0), index);
          return `${point.x} ${point.y}`;
        })
        .join(" L ")} L ${width - paddingX} ${height - paddingBottom} Z`;
      const grid = Array.from({ length: 5 })
        .map((_, index) => {
          const y = paddingTop + (innerHeight / 4) * index;
          return `<line class="line-chart-grid" x1="${paddingX}" x2="${width - paddingX}" y1="${y}" y2="${y}"></line>`;
        })
        .join("");

      container.innerHTML = `
        <div class="chart-shell chart-shell-line">
          <div class="chart-head">
            <div>
              <div class="chart-subtitle">${Utils.escapeHtml(subtitle)}</div>
              <div class="chart-title">${Utils.escapeHtml(title)}</div>
            </div>
            <div class="chart-pill">${formatNumber(values.reduce((sum, value) => sum + value, 0))} total</div>
          </div>
          <div class="line-chart-wrap">
            <svg viewBox="0 0 ${width} ${height}" class="line-chart" preserveAspectRatio="none" aria-label="${Utils.escapeHtml(title)} chart">
              <defs>
                <linearGradient id="${selector.replace(/[^a-z0-9]/gi, "")}Gradient" x1="0" y1="0" x2="0" y2="1">
                  <stop offset="0%" stop-color="rgba(20, 184, 166, 0.28)" />
                  <stop offset="100%" stop-color="rgba(20, 184, 166, 0.04)" />
                </linearGradient>
              </defs>
              ${grid}
              <path d="${areaPath}" class="line-chart-area"></path>
              <polyline points="${points}" class="line-chart-path"></polyline>
              ${series
                .map((item, index) => {
                  const value = Number(item.count || item.value || 0);
                  const point = pointFor(value, index);
                  const label = Utils.escapeHtml(item.month_label || item.month || item.label || item.name || "");
                  return `
                    <g>
                      <circle cx="${point.x}" cy="${point.y}" r="5" class="line-chart-dot"></circle>
                      <circle cx="${point.x}" cy="${point.y}" r="2.2" fill="var(--primary-600)"></circle>
                      <text x="${point.x}" y="${height - paddingBottom + 10}" text-anchor="middle" class="line-chart-label">${label}</text>
                      <text x="${point.x}" y="${Math.max(point.y - 12, paddingTop + 12)}" text-anchor="middle" class="line-chart-value">${formatNumber(value)}</text>
                    </g>
                  `;
                })
                .join("")}
            </svg>
          </div>
        </div>
      `;
    }

    function renderTopAlumni(alumni) {
      const tbody = Utils.$("#topAlumni");
      alumni = Array.isArray(alumni) ? alumni : [];

      if (!alumni.length) {
        tbody.innerHTML =
          '<tr><td colspan="6" class="text-center text-secondary">No alumni data</td></tr>';
        return;
      }

      tbody.innerHTML = alumni
        .map(
          (a, i) => `
            <tr>
                <td>#${i + 1}</td>
                <td>
                    <div class="flex items-center gap-sm">
                        <div class="avatar avatar-sm bg-primary"><span>${Utils.getInitials(a.name || "Alumni")}</span></div>
                        <div>
                            <div class="font-medium">${Utils.escapeHtml(a.name || "Alumni")}</div>
                            <div class="text-xs text-secondary">${a.alumni_id || ""}</div>
                        </div>
                    </div>
                </td>
                <td>${Utils.escapeHtml(a.college_name || "-")}</td>
                <td class="font-bold">${a.total_points || 0}</td>
                <td><span class="badge badge-primary">${a.badge_level || "Bronze"}</span></td>
                <td>${a.events_attended || 0}</td>
            </tr>
        `,
        )
        .join("");
    }

    function renderTopActiveAlumni(alumni) {
      const tbody = Utils.$("#topActiveAlumni");
      if (!tbody) return;

      alumni = Array.isArray(alumni) ? alumni : [];

      if (!alumni.length) {
        tbody.innerHTML =
          '<tr><td colspan="5" class="text-center text-secondary">No active alumni data yet</td></tr>';
        return;
      }

      tbody.innerHTML = alumni
        .map(
          (a, index) => `
            <tr>
              <td>#${index + 1}</td>
              <td>
                <div class="flex items-center gap-sm">
                  <div class="avatar avatar-sm bg-primary"><span>${Utils.getInitials(a.name || "Alumni")}</span></div>
                  <div>
                    <div class="font-medium">${Utils.escapeHtml(a.name || "Alumni")}</div>
                    <div class="text-xs text-secondary">${Utils.escapeHtml(a.alumni_id || "")}</div>
                  </div>
                </div>
              </td>
              <td>${Utils.escapeHtml(a.batch_year || "-")}</td>
              <td class="font-bold">${formatNumber(a.events_attended || 0)}</td>
              <td>${formatNumber(a.total_points || 0)}</td>
            </tr>
          `,
        )
        .join("");
    }

    function renderEvents(events) {
      const container = Utils.$("#upcomingEvents");
      events = Array.isArray(events) ? events : [];

      if (!events.length) {
        container.innerHTML =
          '<div class="p-lg text-center text-secondary">No upcoming events</div>';
        return;
      }

      container.innerHTML = events
        .map(
          (event) => `
            <a href="#/admin/events?id=${event.id}" class="block p-md hover:bg-gray-50">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="font-medium">${Utils.escapeHtml(event.title)}</div>
                        <div class="text-sm text-secondary">${Utils.formatDate(event.event_date)} &middot; ${event.registered_count || 0} registered</div>
                    </div>
                    <span class="badge badge-${event.status === "upcoming" ? "primary" : "success"}">${event.status}</span>
                </div>
            </a>
        `,
        )
        .join("");
    }

    // ── Pending verification badge ───────────────────────────
    (async function loadPendingBadge() {
      try {
        const res = await API.verification.getPending({ limit: 1 });
        const count = res?.data?.total || res?.data?.pending?.length || 0;
        const badge = document.getElementById('sidebarPendingBadge');
        if (badge && count > 0) {
          badge.textContent = count > 99 ? '99+' : count;
          badge.style.display = 'inline-block';
        }
      } catch (_) { /* non-critical */ }
    })();

  })();
</script>
