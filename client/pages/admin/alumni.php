<!-- Admin Alumni List Page -->
<div class="dashboard-layout">
  <aside class="sidebar" id="adminSidebar"></aside>

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
      <h1 class="page-title">Alumni Management</h1>
      <div class="topbar-actions">
        <button class="btn btn-primary" id="exportBtn">
          <svg
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
            <polyline points="7 10 12 15 17 10" />
            <line x1="12" y1="15" x2="12" y2="3" />
          </svg>
          Export
        </button>
      </div>
    </header>

    <div class="admin-content p-lg">
      <!-- Filters -->
      <div class="card mb-lg">
        <div class="card-body">
          <div class="grid grid-cols-5 gap-md">
            <div>
              <input
                type="text"
                id="searchInput"
                class="form-input"
                placeholder="Search name, email, ID..."
              />
            </div>
            <div>
              <select id="collegeFilter" class="form-select">
                <option value="">All Colleges</option>
              </select>
            </div>
            <div>
              <select id="programFilter" class="form-select">
                <option value="">All Programs</option>
              </select>
            </div>
            <div>
              <select id="batchFilter" class="form-select">
                <option value="">All Batches</option>
              </select>
            </div>
            <div>
              <select id="statusFilter" class="form-select">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="blocked">Blocked</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats -->
      <div class="grid grid-cols-4 gap-md mb-lg">
        <div class="stat-mini">
          <span class="stat-mini-value" id="totalCount">0</span>
          <span class="stat-mini-label">Total</span>
        </div>
        <div class="stat-mini">
          <span class="stat-mini-value text-success" id="activeCount">0</span>
          <span class="stat-mini-label">Active</span>
        </div>
        <div class="stat-mini">
          <span class="stat-mini-value text-warning" id="inactiveCount">0</span>
          <span class="stat-mini-label">Inactive</span>
        </div>
        <div class="stat-mini">
          <span class="stat-mini-value text-danger" id="blockedCount">0</span>
          <span class="stat-mini-label">Blocked</span>
        </div>
      </div>

      <!-- Alumni Table -->
      <div class="card">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>
                    <input
                      type="checkbox"
                      id="selectAll"
                      class="form-check-input"
                    />
                  </th>
                  <th>Alumni</th>
                  <th>Alumni ID</th>
                  <th>College / Program</th>
                  <th>Batch</th>
                  <th>Points</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="alumniTable">
                <tr>
                  <td colspan="8" class="text-center">Loading...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="card-footer">
          <div class="flex justify-between items-center">
            <div class="text-sm text-secondary">
              Showing <span id="showingFrom">0</span>-<span id="showingTo"
                >0</span
              >
              of <span id="totalRecords">0</span>
            </div>
            <div id="pagination" class="flex gap-xs"></div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- View Alumni Modal -->
<div class="modal" id="viewAlumniModal">
  <div class="modal-backdrop"></div>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Alumni Details</h3>
        <button class="modal-close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="alumniDetailBody">
        <!-- Will be populated -->
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<style>
  .stat-mini {
    background: white;
    padding: var(--spacing-md) var(--spacing-lg);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    gap: var(--spacing-sm);
    box-shadow: var(--shadow-sm);
  }
  .stat-mini-value {
    font-size: 1.25rem;
    font-weight: 700;
  }
  .stat-mini-label {
    font-size: 0.875rem;
    color: var(--gray-500);
  }
  .table-responsive {
    overflow-x: auto;
  }
</style>

<script>
  (function () {
    let alumni = [];
    let currentPage = 1;
    const perPage = 20;
    let totalRecords = 0;

    // Load filters and data
    loadFilters();
    loadAlumni();

    // Filter event listeners
    Utils.$("#searchInput").addEventListener(
      "input",
      Utils.debounce(loadAlumni, 300),
    );
    Utils.$("#collegeFilter").addEventListener("change", () => {
      loadProgramsForCollege(Utils.$("#collegeFilter").value);
      loadAlumni();
    });
    Utils.$("#programFilter").addEventListener("change", loadAlumni);
    Utils.$("#batchFilter").addEventListener("change", loadAlumni);
    Utils.$("#statusFilter").addEventListener("change", loadAlumni);

    // Select all
    Utils.$("#selectAll").addEventListener("change", (e) => {
      Utils.$$(".alumni-checkbox").forEach(
        (cb) => (cb.checked = e.target.checked),
      );
    });

    // Export
    Utils.$("#exportBtn").addEventListener("click", exportAlumni);

    async function loadFilters() {
      try {
        const [colleges, batches] = await Promise.all([
          API.organization.getColleges(),
          API.admin.getBatchYears(),
        ]);

        // Populate colleges
        Utils.$("#collegeFilter").innerHTML =
          '<option value="">All Colleges</option>' +
          (colleges.data || [])
            .map(
              (c) =>
                `<option value="${c.id}">${Utils.escapeHtml(c.name)}</option>`,
            )
            .join("");

        // Populate batch years
        Utils.$("#batchFilter").innerHTML =
          '<option value="">All Batches</option>' +
          (batches.data || [])
            .map((b) => `<option value="${b}">${b}</option>`)
            .join("");
      } catch (error) {
        console.error("Load filters error:", error);
      }
    }

    async function loadProgramsForCollege(collegeId) {
      Utils.$("#programFilter").innerHTML =
        '<option value="">All Programs</option>';

      if (!collegeId) return;

      try {
        const programs = await API.organization.getPrograms(collegeId);
        Utils.$("#programFilter").innerHTML =
          '<option value="">All Programs</option>' +
          (programs.data || [])
            .map(
              (p) =>
                `<option value="${p.id}">${Utils.escapeHtml(p.name)}</option>`,
            )
            .join("");
      } catch (error) {
        console.error("Load programs error:", error);
      }
    }

    async function loadAlumni() {
      const params = {
        search: Utils.$("#searchInput").value,
        college_id: Utils.$("#collegeFilter").value,
        program_id: Utils.$("#programFilter").value,
        batch_year: Utils.$("#batchFilter").value,
        status: Utils.$("#statusFilter").value,
        page: currentPage,
        limit: perPage,
      };

      try {
        const response = await API.admin.getAlumni(params);
        alumni = response.data.alumni || [];
        totalRecords = response.data.total || 0;

        // Update stats
        Utils.$("#totalCount").textContent =
          response.data.stats?.total || totalRecords;
        Utils.$("#activeCount").textContent = response.data.stats?.active || 0;
        Utils.$("#inactiveCount").textContent =
          response.data.stats?.inactive || 0;
        Utils.$("#blockedCount").textContent =
          response.data.stats?.blocked || 0;

        renderTable();
        renderPagination();
      } catch (error) {
        console.error("Load alumni error:", error);
        Utils.$("#alumniTable").innerHTML =
          '<tr><td colspan="8" class="text-center text-danger">Failed to load data</td></tr>';
      }
    }

    function renderTable() {
      const tbody = Utils.$("#alumniTable");

      if (!alumni.length) {
        tbody.innerHTML =
          '<tr><td colspan="8" class="text-center text-secondary">No alumni found</td></tr>';
        return;
      }

      tbody.innerHTML = alumni
        .map(
          (a) => `
            <tr>
                <td><input type="checkbox" class="form-check-input alumni-checkbox" value="${a.id}"></td>
                <td>
                    <div class="flex items-center gap-sm">
                        <div class="avatar avatar-sm bg-primary">
                            ${a.profile_image ? `<img src="${a.profile_image}" alt="">` : `<span>${Utils.getInitials(a.name)}</span>`}
                        </div>
                        <div>
                            <div class="font-medium">${Utils.escapeHtml(a.name)}</div>
                            <div class="text-xs text-secondary">${Utils.escapeHtml(a.email)}</div>
                        </div>
                    </div>
                </td>
                <td><code>${a.alumni_id || "-"}</code></td>
                <td>
                    <div class="text-sm"><span class="admin-code-chip" title="${Utils.escapeHtml(a.college_name || a.college_code || "-")}">${Utils.escapeHtml(a.college_code || "-")}</span></div>
                    <div class="text-xs text-secondary mt-xs"><span class="admin-code-chip" title="${Utils.escapeHtml(a.program_name || a.program_code || "-")}">${Utils.escapeHtml(a.program_code || "-")}</span></div>
                </td>
                <td>${a.batch_year || "-"}</td>
                <td>
                    <span class="font-medium">${a.total_points || 0}</span>
                    <span class="badge badge-sm badge-${getBadgeColor(a.badge_level)} ml-xs">${a.badge_level || "Bronze"}</span>
                </td>
                <td>
                    <span class="badge badge-${getStatusColor(a.status)}">${a.status || "active"}</span>
                </td>
                <td>
                    <div class="flex gap-xs">
                        <button class="btn btn-ghost btn-sm" onclick="viewAlumni(${a.id})" title="View">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                        <button class="btn btn-ghost btn-sm" onclick="toggleStatus(${a.id}, '${a.status}')" title="Toggle Status">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </button>
                        <button class="btn btn-ghost btn-sm text-danger" onclick="deleteAlumni(${a.id})" title="Delete">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
        `,
        )
        .join("");
    }

    function renderPagination() {
      const totalPages = Math.ceil(totalRecords / perPage);
      const container = Utils.$("#pagination");

      Utils.$("#showingFrom").textContent = (currentPage - 1) * perPage + 1;
      Utils.$("#showingTo").textContent = Math.min(
        currentPage * perPage,
        totalRecords,
      );
      Utils.$("#totalRecords").textContent = totalRecords;

      if (totalPages <= 1) {
        container.innerHTML = "";
        return;
      }

      let html = "";
      for (let i = 1; i <= Math.min(totalPages, 10); i++) {
        html += `<button class="btn ${i === currentPage ? "btn-primary" : "btn-secondary"} btn-sm" onclick="goToPage(${i})">${i}</button>`;
      }
      container.innerHTML = html;
    }

    function getBadgeColor(level) {
      const colors = {
        Bronze: "secondary",
        Silver: "default",
        Gold: "warning",
        Platinum: "primary",
        Diamond: "success",
      };
      return colors[level] || "secondary";
    }

    function getStatusColor(status) {
      const colors = {
        active: "success",
        inactive: "warning",
        blocked: "danger",
      };
      return colors[status] || "secondary";
    }

    window.goToPage = (page) => {
      currentPage = page;
      loadAlumni();
    };

    window.viewAlumni = async (id) => {
      try {
        const response = await API.admin.getAlumniDetail(id);
        const a = response.data;

        Utils.$("#alumniDetailBody").innerHTML = `
                <div class="grid grid-cols-3 gap-lg">
                    <div class="col-span-1">
                        <div class="text-center">
                            <div class="avatar avatar-2xl mx-auto mb-md" style="margin: 0 auto; width: 100px; height: 100px;">
                                ${a.profile_image ? `<img src="${a.profile_image}" alt="">` : `<span style="font-size: 2rem;">${Utils.getInitials(a.name)}</span>`}
                            </div>
                            <h4 class="font-bold">${Utils.escapeHtml(a.name)}</h4>
                            <p class="text-secondary">${a.alumni_id || "-"}</p>
                            <span class="badge badge-${getStatusColor(a.status)}">${a.status}</span>
                        </div>
                    </div>
                    <div class="col-span-2">
                        <h5 class="font-semibold mb-md">Profile Information</h5>
                        <div class="grid grid-cols-2 gap-md">
                            <div><div class="text-xs text-secondary">Email</div><div>${Utils.escapeHtml(a.email)}</div></div>
                            <div><div class="text-xs text-secondary">College</div><div>${Utils.escapeHtml(a.college_name || "-")}</div></div>
                            <div><div class="text-xs text-secondary">Program</div><div>${Utils.escapeHtml(a.program_name || "-")}</div></div>
                            <div><div class="text-xs text-secondary">Section</div><div>${Utils.escapeHtml(a.section_name || "-")}</div></div>
                            <div><div class="text-xs text-secondary">Batch Year</div><div>${a.batch_year || "-"}</div></div>
                            <div><div class="text-xs text-secondary">Graduation Year</div><div>${a.graduation_year || "-"}</div></div>
                            <div><div class="text-xs text-secondary">Total Points</div><div class="font-bold">${a.total_points || 0}</div></div>
                            <div><div class="text-xs text-secondary">Badge Level</div><div><span class="badge badge-primary">${a.badge_level || "Bronze"}</span></div></div>
                            <div><div class="text-xs text-secondary">Email Verified</div><div>${a.email_verified ? "✓ Yes" : "✗ No"}</div></div>
                            <div><div class="text-xs text-secondary">Joined</div><div>${Utils.formatDate(a.created_at)}</div></div>
                        </div>
                    </div>
                </div>
            `;

        Utils.openModal("#viewAlumniModal");
      } catch (error) {
        Utils.error("Failed to load alumni details");
      }
    };

    window.toggleStatus = async (id, currentStatus) => {
      const newStatus =
        currentStatus === "active"
          ? "inactive"
          : currentStatus === "inactive"
            ? "blocked"
            : "active";

      if (!confirm(`Change status to "${newStatus}"?`)) return;

      try {
        await API.admin.updateAlumniStatus(id, newStatus);
        Utils.success("Status updated");
        loadAlumni();
      } catch (error) {
        Utils.error("Failed to update status");
      }
    };

    window.deleteAlumni = async (id) => {
      if (
        !confirm(
          "Are you sure you want to delete this alumni? This action cannot be undone.",
        )
      )
        return;

      try {
        await API.admin.deleteAlumni(id);
        Utils.success("Alumni deleted");
        loadAlumni();
      } catch (error) {
        Utils.error("Failed to delete alumni");
      }
    };

    async function exportAlumni() {
      Utils.setButtonLoading("#exportBtn", true);

      try {
        const blob = await API.admin.exportAlumni({
          college_id: Utils.$("#collegeFilter").value,
          program_id: Utils.$("#programFilter").value,
          batch_year: Utils.$("#batchFilter").value,
          status: Utils.$("#statusFilter").value,
        });

        // Download CSV
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = `alumni_export_${new Date().toISOString().split("T")[0]}.csv`;
        a.click();
        URL.revokeObjectURL(url);

        Utils.success("Export downloaded");
      } catch (error) {
        Utils.error("Export failed");
      } finally {
        Utils.setButtonLoading("#exportBtn", false);
      }
    }

    function includeAdminSidebar(activePage) {
      fetch("pages/admin/dashboard.php")
        .then((r) => r.text())
        .then((html) => {
          const doc = new DOMParser().parseFromString(html, "text/html");
          const sidebar = doc.querySelector(".admin-sidebar, .sidebar");
          if (sidebar) {
            Utils.$("#adminSidebar").innerHTML = sidebar.innerHTML;

            // Update user info
            const user = API.getUser();
            if (user) {
              Utils.$("#adminName").textContent = user.name || "Admin";
              Utils.$("#adminInitials").textContent = Utils.getInitials(
                user.name || "A",
              );
            }

            // Set active link
            Utils.$$(".sidebar-link").forEach((link) => {
              link.classList.remove("active");
              if (link.getAttribute("href") === `#/admin/${activePage}`) {
                link.classList.add("active");
              }
            });
          }
        });

      setTimeout(() => {
        const toggle = Utils.$("#sidebarToggle");
        if (toggle) {
          toggle.addEventListener("click", () => {
            Utils.$("#adminSidebar").classList.toggle("open");
          });
        }
      }, 500);
    }
  })();
</script>
