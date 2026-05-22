<!-- Admin Alumni List -->
<link rel="stylesheet" href="/assets/css/dashboard-improvements.css">
<link rel="stylesheet" href="/assets/css/admin-premium.css">
<link rel="stylesheet" href="/assets/css/alumni-list-improved.css">

<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <div class="flex items-center gap-md">
        <div class="avatar avatar-md bg-primary"><span>A</span></div>
        <div>
          <div class="font-medium text-white">Admin</div>
          <div class="text-xs opacity-75">Administrator</div>
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
            class="sidebar-link"
            >Dashboard</a
          >
          <a
            href="#/admin/alumni"
            data-match="/admin/alumni"
            class="sidebar-link active"
            >All Alumni</a
          >
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
      <button class="sidebar-link text-error" onclick="Auth.logout()">
        Logout
      </button>
    </div>
  </aside>

  <main class="main-content">
    <div class="content-header">
      <h1>All Alumni</h1>
      <div class="topbar-actions">
        <button class="btn btn-primary btn-icon" onclick="exportAlumni()">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
          </svg>
          Export CSV
        </button>
      </div>
    </div>

    <div class="admin-content">
      <section class="admin-page-title-block">
        <h2>All Alumni</h2>
        <p>Search, filter, export, and manage alumni records across campuses and programs.</p>
      </section>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-4 gap-md mb-lg">
        <div class="stat-card-improved">
          <div class="stat-icon bg-primary">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value" id="totalAlumniCount">0</div>
            <div class="stat-label">Total Alumni</div>
          </div>
        </div>

        <div class="stat-card-improved">
          <div class="stat-icon bg-success">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value" id="verifiedAlumniCount">0</div>
            <div class="stat-label">Verified</div>
          </div>
        </div>

        <div class="stat-card-improved">
          <div class="stat-icon bg-warning">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value" id="pendingAlumniCount">0</div>
            <div class="stat-label">Pending</div>
          </div>
        </div>

        <div class="stat-card-improved">
          <div class="stat-icon bg-info">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value" id="totalPointsCount">0</div>
            <div class="stat-label">Total Points</div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="admin-filter-card mb-lg">
        <div class="admin-filter-grid" style="display: grid !important; grid-template-columns: repeat(3, minmax(220px, 1fr)) !important; gap: 1rem !important; align-items: end !important;">
          <div class="form-group" style="grid-column: auto !important; width: auto !important; margin: 0 !important;">
            <label class="form-label">Search</label>
            <input
              type="text"
              id="searchInput"
              class="form-input"
              placeholder="Name, email, ID..."
            />
          </div>
          <div class="form-group" style="grid-column: auto !important; width: auto !important; margin: 0 !important;">
            <label class="form-label">Campus</label>
            <select id="filterCampus" class="form-input">
              <option value="">All Campuses</option>
            </select>
          </div>
          <div class="form-group" style="grid-column: auto !important; width: auto !important; margin: 0 !important;">
            <label class="form-label">College</label>
            <select id="filterCollege" class="form-input">
              <option value="">All Colleges</option>
            </select>
          </div>
          <div class="form-group" style="grid-column: auto !important; width: auto !important; margin: 0 !important;">
            <label class="form-label">Program</label>
            <select id="filterProgram" class="form-input">
              <option value="">All Programs</option>
            </select>
          </div>
          <div class="form-group" style="grid-column: auto !important; width: auto !important; margin: 0 !important;">
            <label class="form-label">Batch Year</label>
            <select id="filterBatch" class="form-input">
              <option value="">All Years</option>
            </select>
          </div>
          <div class="form-group" style="grid-column: auto !important; width: auto !important; margin: 0 !important;">
            <label class="form-label">Status</label>
            <select id="filterStatus" class="form-input">
              <option value="">All Status</option>
              <option value="active">Verified</option>
              <option value="pending">Pending</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="admin-actions-row">
          <button class="btn btn-primary" onclick="applyFilters()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
            Apply Filters
          </button>
          <button class="btn btn-secondary" onclick="clearFilters()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="18" y1="6" x2="6" y2="18"></line>
              <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            Clear
          </button>
          <button class="btn btn-primary" onclick="exportAlumni()">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
              <polyline points="7 10 12 15 17 10"></polyline>
              <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Export
          </button>
        </div>
      </div>

      <!-- Alumni Table -->
      <div class="card-improved admin-record-table-shell">
        <div class="card-header">
          <h2 class="card-title">Alumni List</h2>
          <span class="text-muted" id="totalCount">0 alumni</span>
        </div>
        <div id="alumniTable">
          <div class="loading-spinner p-xl">Loading...</div>
        </div>
        <div
          class="card-footer flex justify-between items-center"
          id="pagination"
        ></div>
      </div>
    </div>
  </main>
</div>

<script>
  (function () {
    let currentPage = 1;
    let filters = {};

    loadCampuses();
    loadColleges();
    loadBatchYears();
    loadAlumni();

    async function loadCampuses() {
      try {
        const response = await API.get('/campuses/list');
        const campuses = response.data || [];
        const select = Utils.$('#filterCampus');
        campuses.forEach((campus) => {
          select.innerHTML += `<option value="${campus.id}">${Utils.escapeHtml(campus.name)} (${Utils.escapeHtml(campus.code)})</option>`;
        });
      } catch (error) {
        console.error('Failed to load campuses:', error);
      }
    }

    async function loadColleges() {
      try {
        const response = await API.organization.getColleges();
        const select = Utils.$("#filterCollege");
        response.data.forEach((college) => {
          select.innerHTML += `<option value="${college.id}">${Utils.escapeHtml(college.name)}</option>`;
        });
      } catch (error) {
        console.error("Failed to load colleges:", error);
      }
    }

    async function loadBatchYears() {
      const select = Utils.$("#filterBatch");
      const currentYear = new Date().getFullYear();
      for (let year = currentYear; year >= currentYear - 20; year--) {
        select.innerHTML += `<option value="${year}">${year}</option>`;
      }
    }

    Utils.$("#filterCollege").addEventListener("change", async function () {
      const collegeId = this.value;
      const programSelect = Utils.$("#filterProgram");
      programSelect.innerHTML = '<option value="">All Programs</option>';

      if (collegeId) {
        try {
          const response = await API.organization.getPrograms(collegeId);
          response.data.forEach((program) => {
            programSelect.innerHTML += `<option value="${program.id}">${Utils.escapeHtml(program.name)}</option>`;
          });
        } catch (error) {
          console.error("Failed to load programs:", error);
        }
      }
    });

    window.applyFilters = function () {
      filters = {
        search: Utils.$("#searchInput").value,
        campus_id: Utils.$("#filterCampus").value,
        college_id: Utils.$("#filterCollege").value,
        program_id: Utils.$("#filterProgram").value,
        batch_year: Utils.$("#filterBatch").value,
        status: Utils.$("#filterStatus").value,
      };
      currentPage = 1;
      loadAlumni();
    };

    window.clearFilters = function () {
      Utils.$("#searchInput").value = "";
      Utils.$("#filterCampus").value = "";
      Utils.$("#filterCollege").value = "";
      Utils.$("#filterProgram").innerHTML =
        '<option value="">All Programs</option>';
      Utils.$("#filterBatch").value = "";
      Utils.$("#filterStatus").value = "";
      filters = {};
      currentPage = 1;
      loadAlumni();
    };

    function displayCode(primary, fallback = "-") {
      const value = String(primary || "").trim();
      return value || fallback;
    }

    function codeCell(code, fullLabel = "") {
      const displayValue = displayCode(code);
      const title = String(fullLabel || code || "").trim();
      return `<span class="admin-code-chip" title="${Utils.escapeHtml(title || displayValue)}">${Utils.escapeHtml(displayValue)}</span>`;
    }

    function resolveProfileImageUrl(imageUrl) {
      const raw = String(imageUrl || "").trim();
      if (!raw) {
        return "";
      }

      if (typeof API !== "undefined" && typeof API.resolveAssetUrl === "function") {
        const resolved = API.resolveAssetUrl(raw);
        if (resolved) {
          return resolved;
        }
      }

      return raw;
    }

    async function loadAlumni() {
      try {
        const params = new URLSearchParams({
          page: currentPage,
          limit: 20,
          ...filters,
        });
        const response = await API.get(`/admin/alumni?${params}`);
        const container = Utils.$("#alumniTable");
        const payload = response.data || {};
        const alumni = Array.isArray(payload.alumni)
          ? payload.alumni
          : Array.isArray(payload.data)
            ? payload.data
            : Array.isArray(payload)
              ? payload
              : [];
        const total = Number.isFinite(Number(payload.pagination?.total))
          ? Number(payload.pagination.total)
          : alumni.length;

        Utils.$("#totalCount").textContent = `${total} alumni`;

        // Update statistics cards
        updateStatistics(alumni, total);

        if (!alumni || alumni.length === 0) {
          container.innerHTML =
            '<div class="text-center text-muted p-xl">No alumni found</div>';
          return;
        }

        container.innerHTML = `
        <table class="table-improved">
          <thead>
            <tr>
              <th>Photo</th>
              <th>Alumni ID</th>
              <th>Alumni</th>
              <th>Campus</th>
              <th>College</th>
              <th>Program</th>
              <th>Batch</th>
              <th>Points</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            ${alumni
              .map((a) => {
                const profileImageUrl = resolveProfileImageUrl(
                  a.profile_image || a.profile_photo_url || "",
                );
                const safeName = Utils.escapeHtml(a.name || "-");
                const initial = (a.name || "A").charAt(0).toUpperCase();
                const safeInitial = Utils.escapeHtml(initial);
                const photoMarkup = profileImageUrl
                  ? `<img src="${Utils.escapeHtml(profileImageUrl)}" alt="${safeName}" class="alumni-photo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                       <div class="alumni-photo-placeholder" style="display:none;">${safeInitial}</div>`
                  : `<div class="alumni-photo-placeholder">${safeInitial}</div>`;

                return `
              <tr>
                <td>
                  ${photoMarkup}
                </td>
                <td><code class="alumni-id-code">${a.alumni_id || "-"}</code></td>
                <td>
                  <div class="admin-table-person">
                    <strong>${safeName}</strong>
                    <span>${Utils.escapeHtml(a.email || "-")}</span>
                  </div>
                </td>
                <td>${codeCell(a.campus_code, a.campus_name)}</td>
                <td>${codeCell(a.college_code, a.college_name)}</td>
                <td>${codeCell(a.program_code, a.program_name)}</td>
                <td>${a.batch_year || "-"}</td>
                <td><span class="points-badge">${a.total_points || 0}</span></td>
                <td><span class="badge badge-${a.status === "active" ? "success" : a.status === "pending" ? "warning" : "danger"}">${a.status || "pending"}</span></td>
                <td>
                  <div class="flex gap-xs">
                    <a href="#/admin/alumni/${a.id}" class="btn btn-sm btn-ghost" title="View Profile">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                      </svg>
                    </a>
                    <a href="#/admin/alumni-id-card?id=${a.id}" class="btn btn-sm btn-ghost" title="View ID Card">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                      </svg>
                    </a>
                    <button class="btn btn-sm btn-ghost text-error" onclick="deleteAlumni(${a.id})" title="Delete">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            `;
              })
              .join("")}
          </tbody>
        </table>
      `;

        // Pagination
        const totalPages = Number.isFinite(
          Number(payload.pagination?.total_pages),
        )
          ? Number(payload.pagination.total_pages)
          : Math.max(1, Math.ceil(total / 20));
        Utils.$("#pagination").innerHTML = `
        <button class="btn btn-sm btn-ghost" onclick="goToPage(${currentPage - 1})" ${currentPage <= 1 ? "disabled" : ""}>Previous</button>
        <span>Page ${currentPage} of ${totalPages}</span>
        <button class="btn btn-sm btn-ghost" onclick="goToPage(${currentPage + 1})" ${currentPage >= totalPages ? "disabled" : ""}>Next</button>
      `;
      } catch (error) {
        console.error("Load alumni error:", error);
        const errorMsg = error?.message || 'Failed to load alumni';
        Utils.$("#alumniTable").innerHTML =
          `<div class="alert alert-error m-lg">${Utils.escapeHtml(errorMsg)}</div>`;
      }
    }

    async function updateStatistics(alumni, total) {
      // Count verified and pending
      const verified = alumni.filter(a => a.status === 'active').length;
      const pending = alumni.filter(a => a.status === 'pending').length;
      const totalPoints = alumni.reduce((sum, a) => sum + (a.total_points || 0), 0);

      // Update stat cards
      Utils.$("#totalAlumniCount").textContent = total;
      Utils.$("#verifiedAlumniCount").textContent = verified;
      Utils.$("#pendingAlumniCount").textContent = pending;
      Utils.$("#totalPointsCount").textContent = totalPoints.toLocaleString();
    }

    window.goToPage = function (page) {
      currentPage = page;
      loadAlumni();
    };

    window.deleteAlumni = async function (id) {
      if (!confirm("Are you sure you want to delete this alumni?")) return;
      try {
        await API.delete(`/admin/alumni/${id}`);
        Utils.success("Alumni deleted");
        loadAlumni();
      } catch (error) {
        Utils.error(error.message || "Failed to delete");
      }
    };

    window.exportAlumni = async function () {
      try {
        const blob = await API.admin.exportAlumni(filters);
        const downloadUrl = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = downloadUrl;
        a.download = `alumni_export_${new Date().toISOString().split("T")[0]}.csv`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(downloadUrl);
        
        Utils.success("Alumni list exported successfully");
      } catch (error) {
        console.error("Export error:", error);
        Utils.error("Failed to export alumni list");
      }
    };
  })();
</script>

