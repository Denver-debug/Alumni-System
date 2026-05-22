<!-- Admin Announcements Management -->
<link rel="stylesheet" href="/assets/css/dashboard-improvements.css">
<link rel="stylesheet" href="/assets/css/admin-premium.css">

<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content">
    <div class="content-header">
      <h1>Announcements</h1>
      <div class="topbar-actions">
        <a
          href="#/admin/announcements/create"
          class="btn btn-primary btn-icon"
          id="createAnnouncementBtn"
          >+ Create Announcement</a
        >
      </div>
    </div>

    <div class="admin-content">
      <div class="admin-panel-toolbar mb-lg">
        <div>
          <h2>Publish Announcements</h2>
          <p>Post campus-wide updates and target specific alumni groups.</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="card-improved p-lg mb-lg">
        <div class="grid grid-cols-4 gap-md">
          <input
            type="text"
            id="searchInput"
            class="form-input"
            placeholder="Search announcements..."
          />
          <select id="statusFilter" class="form-input">
            <option value="">All Status</option>
            <option value="draft">Draft</option>
            <option value="published">Published</option>
            <option value="archived">Archived</option>
          </select>
          <select id="priorityFilter" class="form-input">
            <option value="">All Priority</option>
            <option value="normal">Normal</option>
            <option value="high">Important</option>
            <option value="urgent">Urgent</option>
          </select>
          <button class="btn btn-primary" onclick="applyFilters()">
            Filter
          </button>
        </div>
      </div>

      <!-- Announcements Table -->
      <div class="card-improved">
        <div id="announcementsTable">
          <div class="loading-spinner p-xl">Loading...</div>
        </div>
        <div class="card-footer" id="pagination"></div>
      </div>
    </div>
  </main>
</div>

<script>
  (function () {
    let currentPage = 1;
    let filters = {};

    function goToCreateAnnouncement(e) {
      if (e) {
        e.preventDefault();
      }
      Router.navigate("/admin/announcements/create");
    }

    function bindCreateButtons() {
      [
        "#createAnnouncementBtn",
        "#createAnnouncementBtnEmpty",
      ].forEach((selector) => {
        const btn = Utils.$(selector);
        if (btn) {
          btn.onclick = goToCreateAnnouncement;
        }
      });
    }

    loadAnnouncements();
    bindCreateButtons();

    window.applyFilters = function () {
      filters = {
        search: Utils.$("#searchInput").value,
        status: Utils.$("#statusFilter").value,
        priority: Utils.$("#priorityFilter").value,
      };
      currentPage = 1;
      loadAnnouncements();
    };

    async function loadAnnouncements() {
      try {
        const params = {
          page: currentPage,
          limit: 20,
          ...filters,
        };

        const response = await API.admin.getAnnouncements(params);
        const container = Utils.$("#announcementsTable");
        const payload = response?.data || {};
        const announcements = Array.isArray(payload.announcements)
          ? payload.announcements
          : Array.isArray(payload.data)
            ? payload.data
            : Array.isArray(payload)
              ? payload
              : [];

        const total =
          Number(payload?.pagination?.total) ||
          Number(payload?.total) ||
          announcements.length;

        if (!announcements || announcements.length === 0) {
          container.innerHTML =
            '<div class="text-center text-muted p-xl">No announcements found<div class="mt-md"><button class="btn btn-primary btn-sm" id="createAnnouncementBtnEmpty">+ Add Announcement</button></div></div>';
          bindCreateButtons();
          return;
        }

        container.innerHTML = `
        <table class="table-improved">
          <thead>
            <tr>
              <th>Title</th>
              <th>Campus</th>
              <th>Target</th>
              <th>Priority</th>
              <th>Status</th>
              <th>Publish Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            ${announcements
              .map(
                (a) => `
              <tr>
                <td><strong>${Utils.escapeHtml(a.title)}</strong></td>
                <td><span class="badge badge-${a.campus_names || a.campus_name ? "primary" : "secondary"}">${Utils.escapeHtml(a.campus_names || a.campus_name || "All Campuses")}</span></td>
                <td><span class="badge badge-secondary">${a.target_type || "all"}</span></td>
                <td><span class="badge badge-${a.priority === "urgent" ? "error" : a.priority === "high" || a.priority === "important" ? "warning" : "secondary"}">${a.priority}</span></td>
                <td><span class="badge badge-${a.status === "published" ? "success" : a.status === "draft" ? "warning" : "secondary"}">${a.status}</span></td>
                <td>${a.publish_date ? new Date(a.publish_date).toLocaleDateString() : "-"}</td>
                <td>
                  <div class="flex gap-xs">
                    <a href="#/admin/announcements/${a.id}" class="btn btn-sm btn-ghost">Edit</a>
                    <button class="btn btn-sm btn-ghost text-error" onclick="deleteAnnouncement(${a.id})">Delete</button>
                  </div>
                </td>
              </tr>
            `,
              )
              .join("")}
          </tbody>
        </table>
      `;

        // Pagination
        const totalPages =
          Math.max(
            1,
            Number(payload?.pagination?.total_pages) || Math.ceil(total / 20),
          ) || 1;

        if (currentPage > totalPages) {
          currentPage = totalPages;
        }

        Utils.$("#pagination").innerHTML = `
        <div class="flex justify-between items-center">
          <button class="btn btn-sm btn-ghost" onclick="goToPage(${currentPage - 1})" ${currentPage <= 1 ? "disabled" : ""}>Previous</button>
          <span>Page ${currentPage} of ${totalPages}</span>
          <button class="btn btn-sm btn-ghost" onclick="goToPage(${currentPage + 1})" ${currentPage >= totalPages ? "disabled" : ""}>Next</button>
        </div>
      `;

        bindCreateButtons();
      } catch (error) {
        Utils.$("#announcementsTable").innerHTML =
          '<div class="alert alert-error m-lg">Failed to load announcements</div>';
        bindCreateButtons();
      }
    }

    window.goToPage = function (page) {
      currentPage = Math.max(1, page);
      loadAnnouncements();
    };

    window.deleteAnnouncement = async function (id) {
      if (!confirm("Delete this announcement?")) return;
      try {
        await API.admin.deleteAnnouncement(id);
        Utils.success("Announcement deleted");
        loadAnnouncements();
      } catch (error) {
        Utils.error(error.message || "Failed to delete");
      }
    };
  })();
</script>

