<!-- Admin Users Management -->
<!-- VERSION: 2026-05-12-FIX-v3 -->
<link rel="stylesheet" href="/assets/css/admin-pages-improved.css">
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
      <h1 class="page-title">User Management</h1>
      <div class="topbar-actions">
        <button class="btn btn-primary" onclick="showAddUserModal()">
          + Add User
        </button>
      </div>
    </header>

    <div class="content-body">
      <!-- Tabs -->
      <div class="tabs-improved mb-lg">
        <button
          class="tab-btn active"
          id="tabAdmins"
          onclick="showTab('admins')"
        >
          Administrators
        </button>
        <button
          class="tab-btn"
          id="tabAlumni"
          onclick="showTab('alumni')"
        >
          Alumni Users
        </button>
      </div>

      <!-- Users Table -->
      <div class="card-improved">
        <div class="card-header">
          <h3 class="card-title">Users</h3>
        </div>
        <div class="card-body">
          <div class="flex gap-md items-end mb-lg" style="flex-wrap: wrap;">
            <div class="form-group" style="min-width: 300px; margin: 0;">
              <label class="form-label">Search</label>
              <input
                type="text"
                id="searchInput"
                class="form-input"
                placeholder="Search users..."
              />
            </div>
            <div class="form-group" style="min-width: 220px; margin: 0;">
              <label class="form-label">Campus</label>
              <select id="filterCampus" class="form-input">
                <option value="">All Campuses</option>
              </select>
            </div>
          </div>
          <div style="overflow-x: auto;">
            <div id="usersTable">
              <div class="loading-spinner p-xl">Loading...</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Add User Modal -->
<div id="addAdminModal" class="modal" style="display: none">
  <div class="modal-overlay" onclick="closeModal()"></div>
  <div class="modal-content card p-xl" style="max-width: 600px">
    <h2 class="text-xl font-bold mb-lg">Add User</h2>
    <form id="addAdminForm">
      <div class="form-group">
        <label class="form-label required">Name</label>
        <input type="text" name="name" class="form-input" required />
      </div>
      <div class="form-group">
        <label class="form-label required">Email</label>
        <input type="email" name="email" class="form-input" required />
      </div>
      <div class="form-group">
        <label class="form-label required">Password</label>
        <input
          type="password"
          name="password"
          class="form-input"
          required
          minlength="8"
        />
      </div>
      <div class="form-group">
        <label class="form-label">Role</label>
        <select name="role" class="form-input" onchange="updateCampusVisibility()">
          <option value="alumni">Alumni</option>
          <option value="campus_admin">Campus Admin</option>
          <option value="staff">Staff</option>
          <option value="system_admin">System Admin</option>
        </select>
      </div>
      
      <!-- Alumni-specific fields -->
      <div id="alumniFields" style="display: none;">
        <div class="form-group">
          <label class="form-label required">Campus</label>
          <select name="alumni_campus_id" class="form-input" id="alumniCampusSelect" onchange="loadCollegesForAlumni()">
            <option value="">-- Select Campus --</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required">College</label>
          <select name="college_id" class="form-input" id="collegeSelect" onchange="loadProgramsForAlumni()">
            <option value="">-- Select College --</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required">Program</label>
          <select name="program_id" class="form-input" id="programSelect" onchange="loadSectionsForAlumni()">
            <option value="">-- Select Program --</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required">Section</label>
          <select name="section_id" class="form-input" id="sectionSelect">
            <option value="">-- Select Section --</option>
          </select>
        </div>
      </div>
      
      <!-- Campus Admin/Staff field -->
      <div class="form-group" id="campusField" style="display: none">
        <label class="form-label required">Assign to Campus</label>
        <select name="campus_id" class="form-input" id="campusSelect">
          <option value="">-- Select Campus --</option>
        </select>
      </div>
      
      <div class="flex gap-md mt-lg">
        <button type="submit" class="btn btn-primary">Create User</button>
        <button type="button" class="btn btn-secondary" onclick="closeModal()">
          Cancel
        </button>
      </div>
    </form>
  </div>
</div>

<style>
  .modal {
    position: fixed;
    inset: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .modal-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
  }
  .modal-content {
    position: relative;
    z-index: 1;
  }
</style>

<script>
  (function () {
    let currentTab = "admins";

    loadUsers();
    loadCampuses();

    const searchInput = document.getElementById("searchInput");
    if (searchInput && typeof Utils !== 'undefined' && Utils.$) {
      Utils.$("#searchInput").addEventListener(
        "input",
        Utils.debounce(loadUsers, 300),
      );
    }

    const filterCampus = document.getElementById("filterCampus");
    if (filterCampus && typeof Utils !== 'undefined' && Utils.$) {
      Utils.$("#filterCampus").addEventListener("change", loadUsers);
    }

    window.showTab = function (tab) {
      currentTab = tab;
      
      // Update tab buttons
      const tabAdmins = Utils.$("#tabAdmins");
      const tabAlumni = Utils.$("#tabAlumni");
      
      if (tab === "admins") {
        tabAdmins.classList.add("active");
        tabAlumni.classList.remove("active");
      } else {
        tabAdmins.classList.remove("active");
        tabAlumni.classList.add("active");
      }
      
      loadUsers();
    };

    async function loadCampuses() {
      try {
        const response = await API.get(`/campuses/list`);
        const campuses = response.data || [];
        const select = Utils.$("#campusSelect");
        const filterSelect = Utils.$("#filterCampus");
        
        select.innerHTML = '<option value="">-- Select Campus --</option>';
        filterSelect.innerHTML = '<option value="">All Campuses</option>';
        campuses.forEach(campus => {
          const option = document.createElement('option');
          option.value = campus.id;
          option.textContent = `${campus.name} (${campus.code})`;
          select.appendChild(option);

          const filterOption = document.createElement('option');
          filterOption.value = campus.id;
          filterOption.textContent = `${campus.name} (${campus.code})`;
          filterSelect.appendChild(filterOption);
        });
      } catch (error) {
        console.error('Failed to load campuses:', error);
      }
    }

    window.updateCampusVisibility = function() {
      const role = Utils.$("[name='role']").value;
      const campusField = Utils.$("#campusField");
      const campusSelect = Utils.$("#campusSelect");
      const alumniFields = Utils.$("#alumniFields");
      
      // Show/hide fields based on role
      if (['campus_admin', 'staff'].includes(role)) {
        campusField.style.display = 'block';
        campusSelect.required = true;
        alumniFields.style.display = 'none';
        // Clear alumni fields
        Utils.$("#alumniCampusSelect").value = '';
        Utils.$("#collegeSelect").value = '';
        Utils.$("#programSelect").value = '';
        Utils.$("#sectionSelect").value = '';
      } else if (role === 'alumni') {
        campusField.style.display = 'none';
        campusSelect.required = false;
        campusSelect.value = '';
        alumniFields.style.display = 'block';
        // Load campuses for alumni
        loadCampusesForAlumni();
      } else {
        campusField.style.display = 'none';
        campusSelect.required = false;
        campusSelect.value = '';
        alumniFields.style.display = 'none';
      }
    };

    async function loadCampusesForAlumni() {
      try {
        const response = await API.get(`/campuses/list`);
        const campuses = response.data || [];
        const select = Utils.$("#alumniCampusSelect");
        
        select.innerHTML = '<option value="">-- Select Campus --</option>';
        campuses.forEach(campus => {
          const option = document.createElement('option');
          option.value = campus.id;
          option.textContent = `${campus.name} (${campus.code})`;
          select.appendChild(option);
        });
      } catch (error) {
        console.error('Failed to load campuses for alumni:', error);
      }
    }

    window.loadCollegesForAlumni = async function() {
      const campusId = Utils.$("#alumniCampusSelect").value;
      const collegeSelect = Utils.$("#collegeSelect");
      const programSelect = Utils.$("#programSelect");
      const sectionSelect = Utils.$("#sectionSelect");
      
      // Reset dependent fields
      collegeSelect.innerHTML = '<option value="">-- Select College --</option>';
      programSelect.innerHTML = '<option value="">-- Select Program --</option>';
      sectionSelect.innerHTML = '<option value="">-- Select Section --</option>';
      
      if (!campusId) return;
      
      try {
        const response = await API.get(`/admin/organization/colleges`);
        const colleges = response.data || [];
        
        colleges.forEach(college => {
          const option = document.createElement('option');
          option.value = college.id;
          option.textContent = college.name;
          collegeSelect.appendChild(option);
        });
      } catch (error) {
        console.error('Failed to load colleges:', error);
        Utils.error('Failed to load colleges');
      }
    };

    window.loadProgramsForAlumni = async function() {
      const collegeId = Utils.$("#collegeSelect").value;
      const campusId = Utils.$("#alumniCampusSelect").value;
      const programSelect = Utils.$("#programSelect");
      const sectionSelect = Utils.$("#sectionSelect");
      
      // Reset dependent fields
      programSelect.innerHTML = '<option value="">-- Select Program --</option>';
      sectionSelect.innerHTML = '<option value="">-- Select Section --</option>';
      
      if (!collegeId || !campusId) return;
      
      try {
        const response = await API.get(`/admin/organization/programs?college_id=${collegeId}`);
        const allPrograms = response.data || [];
        
        // Filter programs available at selected campus
        const campusPrograms = await API.get(`/admin/organization/program-campuses-by-campus/${campusId}`);
        const availableProgramIds = (campusPrograms.data || []).map(p => p.id);
        
        const programs = allPrograms.filter(p => availableProgramIds.includes(p.id));
        
        programs.forEach(program => {
          const option = document.createElement('option');
          option.value = program.id;
          option.textContent = program.name;
          programSelect.appendChild(option);
        });
      } catch (error) {
        console.error('Failed to load programs:', error);
        Utils.error('Failed to load programs');
      }
    };

    window.loadSectionsForAlumni = async function() {
      const programId = Utils.$("#programSelect").value;
      const campusId = Utils.$("#alumniCampusSelect").value;
      const sectionSelect = Utils.$("#sectionSelect");
      
      // Reset sections
      sectionSelect.innerHTML = '<option value="">-- Select Section --</option>';
      
      if (!programId || !campusId) return;
      
      try {
        const response = await API.get(`/admin/organization/sections?program_id=${programId}&campus_id=${campusId}`);
        const sections = response.data || [];
        
        sections.forEach(section => {
          const option = document.createElement('option');
          option.value = section.id;
          option.textContent = `${section.name} (Batch ${section.batch_year})`;
          sectionSelect.appendChild(option);
        });
      } catch (error) {
        console.error('Failed to load sections:', error);
        Utils.error('Failed to load sections');
      }
    };

    async function loadUsers() {
      const search = Utils.$("#searchInput").value;
      const role = currentTab === "admins" ? "admin" : "alumni";
      const campusId = Utils.$("#filterCampus").value;

      try {
        const params = new URLSearchParams({ role, search, campus_id: campusId });
        const response = await API.get(`/admin/users?${params}`);
        const users = response.data;
        const container = Utils.$("#usersTable");

        if (!users || users.length === 0) {
          container.innerHTML =
            '<div class="text-center text-muted p-xl">No users found</div>';
          return;
        }

        container.innerHTML = `
        <table class="table">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Role</th>
              <th>Campus</th>
              <th>Status</th>
              <th>Last Login</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            ${users
              .map(
                (user) => `
              <tr>
                <td>
                  <div class="flex items-center gap-sm">
                    <div class="avatar avatar-sm bg-primary">${(user.name || "?")[0].toUpperCase()}</div>
                    <span>${Utils.escapeHtml(user.name || "-")}</span>
                  </div>
                </td>
                <td>${Utils.escapeHtml(user.email)}</td>
                <td><span class="badge badge-${user.role === "system_admin" ? "error" : user.role === "campus_admin" ? "primary" : "secondary"}">${user.role}</span></td>
                <td>${user.campus_name ? Utils.escapeHtml(user.campus_name) : '—'}</td>
                <td><span class="badge badge-${user.status === "active" ? "success" : "warning"}">${user.status || "pending"}</span></td>
                <td>${user.last_login ? new Date(user.last_login).toLocaleDateString() : "Never"}</td>
                <td>
                  <div class="flex gap-xs">
                    ${
                      user.status === "active"
                        ? `<button class="btn btn-xs btn-warning" onclick="toggleStatus(${user.id}, 'inactive')">Deactivate</button>`
                        : `<button class="btn btn-xs btn-success" onclick="toggleStatus(${user.id}, 'active')">Activate</button>`
                    }
                    <button class="btn btn-xs btn-ghost" onclick="resetPassword(${user.id})">Reset Pwd</button>
                    <button class="btn btn-xs btn-error" onclick="deleteUser(${user.id})">Delete</button>
                  </div>
                </td>
              </tr>
            `,
              )
              .join("")}
          </tbody>
        </table>
      `;
      } catch (error) {
        Utils.$("#usersTable").innerHTML =
          '<div class="alert alert-error m-lg">Failed to load users</div>';
      }
    }

    window.showAddUserModal = function () {
      const form = Utils.$("#addAdminForm");
      form.reset();
      
      // Reset campus field visibility
      const campusField = Utils.$("#campusField");
      const campusSelect = Utils.$("#campusSelect");
      const alumniFields = Utils.$("#alumniFields");
      
      campusField.style.display = 'none';
      campusSelect.required = false;
      campusSelect.value = '';
      
      alumniFields.style.display = 'none';
      
      // Show modal
      Utils.$("#addAdminModal").style.display = "flex";
    };

    window.showAddAdminModal = window.showAddUserModal;

    window.closeModal = function () {
      Utils.$("#addAdminModal").style.display = "none";
    };

    Utils.$("#addAdminForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);
      const data = Object.fromEntries(formData);

      // Handle alumni-specific fields
      if (data.role === 'alumni') {
        // Validate alumni required fields
        if (!data.alumni_campus_id || !data.college_id || !data.program_id || !data.section_id) {
          Utils.error("All alumni fields are required (Campus, College, Program, Section)");
          return;
        }
        
        // Use alumni_campus_id as campus_id for alumni
        data.campus_id = data.alumni_campus_id;
        delete data.alumni_campus_id;
      } else {
        // Remove alumni-specific fields for non-alumni roles
        delete data.alumni_campus_id;
        delete data.college_id;
        delete data.program_id;
        delete data.section_id;
      }

      // Convert empty campus_id to null
      if (data.campus_id === '' || data.campus_id === undefined) {
        data.campus_id = null;
      }

      // Validate campus requirement for campus_admin and staff
      if (['campus_admin', 'staff'].includes(data.role) && !data.campus_id) {
        Utils.error("Campus assignment is required for this role");
        return;
      }

      try {
        await API.post("/admin/users", data);
        Utils.success("User created successfully");
        closeModal();
        e.target.reset();
        loadUsers();
      } catch (error) {
        Utils.error(error.message || "Failed to create user");
      }
    });

    window.toggleStatus = async function (id, status) {
      try {
        await API.put(`/admin/users/${id}`, { status });
        Utils.success("Status updated");
        loadUsers();
      } catch (error) {
        Utils.error(error.message || "Failed to update");
      }
    };

    window.resetPassword = async function (id) {
      if (!confirm("Send password reset email to this user?")) return;
      try {
        await API.post(`/admin/users/${id}/reset-password`);
        Utils.success("Reset email sent");
      } catch (error) {
        Utils.error(error.message || "Failed to send reset");
      }
    };

    window.deleteUser = async function (id) {
      if (
        !confirm(
          "Are you sure you want to delete this user? This cannot be undone.",
        )
      )
        return;
      try {
        await API.delete(`/admin/users/${id}`);
        Utils.success("User deleted");
        loadUsers();
      } catch (error) {
        Utils.error(error.message || "Failed to delete");
      }
    };
  })();
</script>

