<!-- Admin Campus Management -->
<!-- VERSION: 2026-05-15-FIX-v5 -->
<link rel="stylesheet" href="assets/css/dashboard-improvements.css">
<link rel="stylesheet" href="assets/css/admin-premium.css">

<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content">
    <header class="admin-topbar">
      <h1 class="page-title">Campus Management</h1>
      <div class="topbar-actions">
        <button class="btn btn-primary" onclick="showCampusModal()">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="12" y1="5" x2="12" y2="19"></line>
            <line x1="5" y1="12" x2="19" y2="12"></line>
          </svg>
          Add Campus
        </button>
      </div>
    </header>

    <div class="admin-content">
      <div class="admin-panel-toolbar mb-lg">
        <div>
          <h2 class="text-2xl font-bold">Campus Management</h2>
          <p class="text-secondary mt-sm">Manage your institution's campus locations</p>
        </div>
      </div>
      
      <!-- Search Bar -->
      <div class="card-improved mb-lg">
        <div class="card-body">
          <div class="flex gap-md items-center">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #6b7280;">
              <circle cx="11" cy="11" r="8"></circle>
              <path d="m21 21-4.35-4.35"></path>
            </svg>
            <input type="text" class="form-input" id="campusSearch" placeholder="Search campuses by name, code, or location..." style="border: none; box-shadow: none; padding: 0; flex: 1;">
          </div>
        </div>
      </div>

      <!-- Campuses Table -->
      <div class="card-improved">
        <div class="card-header">
          <h3 class="card-title">All Campuses</h3>
          <button class="btn-icon btn-icon-sm btn-ghost" onclick="loadCampuses()" title="Refresh">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="1 4 1 10 7 10"></polyline>
              <polyline points="23 20 23 14 17 14"></polyline>
              <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path>
            </svg>
          </button>
        </div>
        <div class="card-body p-0">
          <div style="overflow-x: auto;">
            <table class="table-improved" id="campusesTable">
              <thead>
                <tr>
                  <th>Campus Name</th>
                  <th>Code</th>
                  <th>Location</th>
                  <th>Assigned Admin</th>
                  <th>Status</th>
                  <th>Created</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Campus Modal -->
<div class="modal-improved" id="campusModal">
  <div class="modal-backdrop"></div>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="campusModalTitle">Add Campus</h5>
        <button class="modal-close" onclick="Utils.closeModal('#campusModal')">&times;</button>
      </div>
      <form id="campusForm" onsubmit="saveCampus(event)">
        <div class="modal-body">
          <input type="hidden" id="campusId">
          
          <div class="form-group">
            <label for="campusName" class="form-label required">Campus Name</label>
            <input type="text" class="form-input" id="campusName" required placeholder="e.g., Main Campus">
          </div>

          <div class="form-group">
            <label for="campusCode" class="form-label required">Campus Code</label>
            <input type="text" class="form-input" id="campusCode" placeholder="e.g., MAIN, NORTH" required maxlength="20" style="text-transform: uppercase;">
            <small class="text-secondary">Used in Alumni ID format (e.g., BBC-2026-CCS-00001)</small>
          </div>

          <div class="form-group">
            <label for="campusLocation" class="form-label">Location</label>
            <input type="text" class="form-input" id="campusLocation" placeholder="e.g., Downtown, Metro Manila">
          </div>

          <div class="form-group">
            <label for="campusDescription" class="form-label">Description</label>
            <textarea class="form-textarea" id="campusDescription" rows="3" placeholder="Brief description of the campus"></textarea>
          </div>

          <div class="form-group">
            <label for="campusStatus" class="form-label">Status</label>
            <select class="form-select" id="campusStatus">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="form-group">
            <label for="campusAssignedAdmin" class="form-label">Assigned Admin</label>
            <select class="form-select" id="campusAssignedAdmin">
              <option value="">No admin assigned</option>
            </select>
            <small class="text-secondary">Optional. This just records the campus owner in the system.</small>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="Utils.closeModal('#campusModal')">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Campus</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// Use window scope to prevent redeclaration errors in SPA navigation
if (!window.campusPageState) {
  window.campusPageState = {
    allCampuses: [],
    searchTimeout: null
  };
}

// Execute immediately instead of waiting for DOMContentLoaded (which won't fire in SPA navigation)
(function() {
  loadCampuses();
    
  // Add search input listener with debounce
  const searchInput = document.getElementById('campusSearch');
  if (searchInput) {
    searchInput.addEventListener('input', function() {
      clearTimeout(window.campusPageState.searchTimeout);
      window.campusPageState.searchTimeout = setTimeout(() => {
        filterCampuses(this.value);
      }, 300);
    });
  }
})();

async function loadCampuses() {
    try {
        // Show loading skeleton
        showLoadingSkeleton();
        
        const response = await API.admin.getCampuses();
        window.campusPageState.allCampuses = response.data || [];
        
        renderCampuses(window.campusPageState.allCampuses);
    } catch (error) {
        console.error('Error loading campuses:', error);
        showEmptyState('Error loading campuses. Please try again.');
    }
}

function filterCampuses(searchTerm) {
    if (!searchTerm.trim()) {
        renderCampuses(window.campusPageState.allCampuses);
        return;
    }
    
    const term = searchTerm.toLowerCase();
    const filtered = window.campusPageState.allCampuses.filter(campus => {
        return campus.name.toLowerCase().includes(term) ||
               campus.code.toLowerCase().includes(term) ||
               (campus.location && campus.location.toLowerCase().includes(term));
    });
    
    renderCampuses(filtered);
}

function showLoadingSkeleton() {
    const tbody = document.querySelector('#campusesTable tbody');
    tbody.innerHTML = '';
    
    // Create 5 skeleton rows
    for (let i = 0; i < 5; i++) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><div class="loading-skeleton" style="height: 1rem; width: 70%;"></div></td>
            <td><div class="loading-skeleton" style="height: 1rem; width: 60%;"></div></td>
            <td><div class="loading-skeleton" style="height: 1rem; width: 80%;"></div></td>
            <td><div class="loading-skeleton" style="height: 1.5rem; width: 4rem;"></div></td>
            <td><div class="loading-skeleton" style="height: 1rem; width: 6rem;"></div></td>
            <td>
                <div style="display: flex; gap: 0.5rem;">
                    <div class="loading-skeleton" style="height: 2rem; width: 2rem;"></div>
                    <div class="loading-skeleton" style="height: 2rem; width: 2rem;"></div>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    }
}

function showEmptyState(message = 'No campuses found') {
    const tbody = document.querySelector('#campusesTable tbody');
    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="text-center">
                <div class="empty-state empty-state-compact">
                    <div class="empty-state-icon">
                        <i class="fas fa-building" style="font-size: 3rem;"></i>
                    </div>
                    <div class="empty-state-title">${message}</div>
                    <div class="empty-state-description">
                        ${message === 'No campuses found' ? 'Get started by creating your first campus location.' : ''}
                    </div>
                    ${message === 'No campuses found' ? '<div class="empty-state-action"><button class="btn btn-primary" onclick="showCampusModal()"><i class="fas fa-plus"></i> Add Campus</button></div>' : ''}
                </div>
            </td>
        </tr>
    `;
}

function renderCampuses(campuses) {
    const tbody = document.querySelector('#campusesTable tbody');
    tbody.innerHTML = '';

    if (campuses.length === 0) {
        showEmptyState();
        return;
    }

    campuses.forEach(campus => {
        const row = document.createElement('tr');
        const created = new Date(campus.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
        const statusBadge = campus.status === 'active' 
            ? '<span class="badge badge-success">Active</span>'
            : '<span class="badge badge-secondary">Inactive</span>';
        
        row.innerHTML = `
            <td><strong>${Utils.escapeHtml(campus.name)}</strong></td>
            <td><code style="background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.875rem;">${Utils.escapeHtml(campus.code)}</code></td>
            <td>${Utils.escapeHtml(campus.location || '-')}</td>
          <td>${Utils.escapeHtml(campus.assigned_admin_name || 'Unassigned')}</td>
            <td>${statusBadge}</td>
            <td>${created}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn-icon btn-icon-sm btn-ghost" onclick="editCampus(${campus.id})" title="Edit campus">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                        </svg>
                    </button>
                    <button class="btn-icon btn-icon-sm btn-ghost" onclick="deleteCampus(${campus.id})" title="Delete campus" style="color: #ef4444;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                        </svg>
                    </button>
                </div>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function showCampusModal() {
    document.getElementById('campusId').value = '';
    document.getElementById('campusForm').reset();
  document.getElementById('campusAssignedAdmin').value = '';
    document.getElementById('campusModalTitle').textContent = 'Add Campus';
    document.getElementById('campusModal').classList.add('active');
}

async function editCampus(id) {
    try {
        const response = await API.admin.getCampus(id);
        const campus = response.data;

        document.getElementById('campusId').value = campus.id;
        document.getElementById('campusName').value = campus.name;
        document.getElementById('campusCode').value = campus.code;
        document.getElementById('campusLocation').value = campus.location || '';
        document.getElementById('campusDescription').value = campus.description || '';
        document.getElementById('campusStatus').value = campus.status;
        await loadCampusAdmins(campus.assigned_admin_id || '');
        document.getElementById('campusAssignedAdmin').value = campus.assigned_admin_id || '';
        
        document.getElementById('campusModalTitle').textContent = 'Edit Campus';
        document.getElementById('campusModal').classList.add('active');
    } catch (error) {
        console.error('Error loading campus:', error);
        Utils.error('Error loading campus');
    }
}

async function saveCampus(event) {
    event.preventDefault();
    
    const campusId = document.getElementById('campusId').value;
    const data = {
        name: document.getElementById('campusName').value,
        code: document.getElementById('campusCode').value,
        location: document.getElementById('campusLocation').value,
        description: document.getElementById('campusDescription').value,
    status: document.getElementById('campusStatus').value,
    assigned_admin_id: document.getElementById('campusAssignedAdmin').value || null
    };

    try {
        if (campusId) {
            await API.admin.updateCampus(campusId, data);
        } else {
            await API.admin.createCampus(data);
        }

        document.getElementById('campusModal').classList.remove('active');
        loadCampuses();
        Utils.success(campusId ? 'Campus updated successfully' : 'Campus created successfully');
    } catch (error) {
        console.error('Error saving campus:', error);
        Utils.error('Error: ' + (error.message || 'Failed to save campus'));
    }
}

async function deleteCampus(id) {
    if (!confirm('Are you sure you want to delete this campus?')) return;

    try {
        await API.admin.deleteCampus(id);
        loadCampuses();
        Utils.success('Campus deleted successfully');
    } catch (error) {
        console.error('Error deleting campus:', error);
        Utils.error('Error: ' + (error.message || 'Failed to delete campus'));
    }
}

async function loadCampusAdmins(selectedAdminId = '') {
  const select = document.getElementById('campusAssignedAdmin');
  select.innerHTML = '<option value="">No admin assigned</option>';

  try {
    const response = await API.get('/admin/users', { role: 'admin' });
    const users = response.data || [];

    users.forEach(user => {
      const option = document.createElement('option');
      option.value = user.id;
      option.textContent = `${user.name} (${user.role})`;
      if (String(user.id) === String(selectedAdminId)) {
        option.selected = true;
      }
      select.appendChild(option);
    });
  } catch (error) {
    console.error('Failed to load campus admins:', error);
  }
}

loadCampusAdmins();
</script>

