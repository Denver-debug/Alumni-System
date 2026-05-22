<!-- Admin Organization -->
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
      <h1 class="page-title">Organization Structure</h1>
      <div class="flex-1"></div>
    </header>

    <div class="admin-content p-lg">
      <!-- Statistics Cards -->
      <div class="grid grid-cols-4 gap-md mb-lg" id="statsCards" style="display: none;">
        <!-- Stats will be populated dynamically -->
      </div>

      <div class="tabs-improved">
        <button class="tab-btn active" onclick="switchTab('colleges')">
          Colleges
        </button>
        <button class="tab-btn" onclick="switchTab('programs')">
          Programs
        </button>
        <button class="tab-btn" onclick="switchTab('sections')">
          Sections
        </button>
      </div>

      <!-- Colleges Tab -->
      <div class="tab-content active" id="collegesTab">
        <div class="card-improved">
          <div class="card-header">
            <h3 class="card-title">Colleges</h3>
            <div class="flex gap-sm">
              <button class="btn-icon btn-icon-sm btn-ghost" onclick="loadColleges()" title="Refresh">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="1 4 1 10 7 10"></polyline>
                  <polyline points="23 20 23 14 17 14"></polyline>
                  <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path>
                </svg>
              </button>
              <button class="btn-icon btn-primary" onclick="showCollegeModal()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add College
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="mb-lg">
              <input type="text" id="collegeSearch" class="form-input" placeholder="Search colleges..." style="max-width: 300px;" oninput="filterColleges()">
            </div>
            <div style="overflow-x: auto;">
              <table class="table-improved">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Programs</th>
                    <th>Alumni</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="collegesTable">
                  <tr>
                    <td colspan="6" class="text-center">
                      <div class="loading-skeleton" style="height: 2rem; margin: 1rem;"></div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Programs Tab -->
      <div class="tab-content" id="programsTab">
        <div class="card-improved">
          <div class="card-header">
            <h3 class="card-title">Programs</h3>
            <div class="flex gap-sm">
              <button class="btn-icon btn-icon-sm btn-ghost" onclick="loadPrograms()" title="Refresh">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="1 4 1 10 7 10"></polyline>
                  <polyline points="23 20 23 14 17 14"></polyline>
                  <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path>
                </svg>
              </button>
              <button class="btn-icon btn-primary" onclick="showProgramModal()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Program
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="flex gap-md mb-lg" style="flex-wrap: wrap;">
              <select id="collegeFilter" class="form-input" onchange="loadPrograms()" style="max-width: 250px;">
                <option value="">All Colleges</option>
              </select>
              <input type="text" id="programSearch" class="form-input" placeholder="Search programs..." style="max-width: 250px;" oninput="filterPrograms()">
            </div>
            <div style="overflow-x: auto;">
              <table class="table-improved">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Code</th>
                    <th>College</th>
                    <th>Campuses</th>
                    <th>Degree</th>
                    <th>Sections</th>
                    <th>Alumni</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="programsTable">
                  <tr>
                    <td colspan="8" class="text-center">
                      <div class="loading-skeleton" style="height: 2rem; margin: 1rem;"></div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Sections Tab -->
      <div class="tab-content" id="sectionsTab">
        <div class="card-improved">
          <div class="card-header">
            <h3 class="card-title">Sections</h3>
            <div class="flex gap-sm">
              <button class="btn-icon btn-icon-sm btn-ghost" onclick="loadSections()" title="Refresh">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="1 4 1 10 7 10"></polyline>
                  <polyline points="23 20 23 14 17 14"></polyline>
                  <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path>
                </svg>
              </button>
              <button class="btn-icon btn-primary" onclick="showSectionModal()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <line x1="12" y1="5" x2="12" y2="19"></line>
                  <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add Section
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="flex gap-md mb-lg" style="flex-wrap: wrap;">
              <select id="programFilter" class="form-input" onchange="loadSections()" style="max-width: 250px;">
                <option value="">All Programs</option>
              </select>
              <input
                type="number"
                id="batchFilter"
                class="form-input"
                placeholder="Batch Year"
                onchange="loadSections()"
                style="max-width: 150px;"
              />
              <input type="text" id="sectionSearch" class="form-input" placeholder="Search sections..." style="max-width: 200px;" oninput="filterSections()">
            </div>
            <div style="overflow-x: auto;">
              <table class="table-improved">
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Program</th>
                    <th>Campus</th>
                    <th>Batch Year</th>
                    <th>Alumni</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="sectionsTable">
                  <tr>
                    <td colspan="6" class="text-center">
                      <div class="loading-skeleton" style="height: 2rem; margin: 1rem;"></div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- College Modal -->
<div class="modal-improved" id="collegeModal">
  <div class="modal-backdrop"></div>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="collegeModalTitle">Add College</h3>
        <button type="button" class="modal-close" data-dismiss="modal">
          &times;
        </button>
      </div>
      <form id="collegeForm" onsubmit="saveCollege(event)">
        <div class="modal-body">
          <div class="form-improved">
            <div class="form-group">
              <label class="form-label required" for="collegeName">Name</label>
              <input type="text" id="collegeName" class="form-input" required />
            </div>
            <div class="form-group">
              <label class="form-label required" for="collegeCode">Code</label>
              <input
                type="text"
                id="collegeCode"
                class="form-input"
                required
                maxlength="10"
                style="text-transform: uppercase"
              />
            </div>
            <div class="form-group">
              <label class="form-label" for="collegeDescription">Description</label>
              <textarea
                id="collegeDescription"
                class="form-textarea"
                rows="3"
              ></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            onclick="closeModal('collegeModal')"
          >
            Cancel
          </button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
        <input type="hidden" id="collegeId" />
      </form>
    </div>
  </div>
</div>

<!-- Program Modal -->
<div class="modal-improved" id="programModal">
  <div class="modal-backdrop"></div>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="programModalTitle">Add Program</h3>
        <button type="button" class="modal-close" data-dismiss="modal">
          &times;
        </button>
      </div>
      <form id="programForm" onsubmit="saveProgram(event)">
        <div class="modal-body">
          <div class="form-improved">
            <div class="form-group">
              <label class="form-label required" for="programCollege">College</label>
              <select id="programCollege" class="form-select" required></select>
            </div>
            <div class="form-group">
              <label class="form-label required" for="programCampuses">Available at Campuses</label>
              <div id="programCampuses" class="form-checkbox-group" style="border: 1px solid #ddd; padding: 10px; border-radius: 4px; max-height: 150px; overflow-y: auto;">
                <!-- Checkboxes will be populated here -->
              </div>
            </div>
            <div class="form-group">
              <label class="form-label required" for="programName">Name</label>
              <input type="text" id="programName" class="form-input" required />
            </div>
            <div class="form-group">
              <label class="form-label required" for="programCode">Code</label>
              <input
                type="text"
                id="programCode"
                class="form-input"
                required
                maxlength="10"
                style="text-transform: uppercase"
              />
            </div>
            <div class="form-group">
              <label class="form-label" for="programDegree">Degree Type</label>
              <select id="programDegree" class="form-select">
                <option value="bachelor">Bachelor's</option>
                <option value="master">Master's</option>
                <option value="doctorate">Doctorate</option>
                <option value="associate">Associate</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            onclick="closeModal('programModal')"
          >
            Cancel
          </button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
        <input type="hidden" id="programId" />
      </form>
    </div>
  </div>
</div>

<!-- Section Modal -->
<div class="modal-improved" id="sectionModal">
  <div class="modal-backdrop"></div>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="sectionModalTitle">Add Section</h3>
        <button type="button" class="modal-close" data-dismiss="modal">
          &times;
        </button>
      </div>
      <form id="sectionForm" onsubmit="saveSection(event)">
        <div class="modal-body">
          <div class="form-improved">
            <div class="form-group">
              <label class="form-label required" for="sectionProgram">Program</label>
              <select id="sectionProgram" class="form-select" required></select>
            </div>
            <div class="form-group">
              <label class="form-label required" for="sectionCampus">Campus</label>
              <select id="sectionCampus" class="form-select" required>
                <option value="">-- Select Campus --</option>
              </select>
            </div>
            <div class="grid grid-cols-2 gap-md">
              <div class="form-group">
                <label class="form-label required" for="sectionName">Name</label>
                <input
                  type="text"
                  id="sectionName"
                  class="form-input"
                  required
                  placeholder="e.g., Section A"
                />
              </div>
              <div class="form-group">
                <label class="form-label required" for="sectionBatch">Batch Year</label>
                <input
                  type="number"
                  id="sectionBatch"
                  class="form-input"
                  required
                  min="1990"
                  max="2100"
                />
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-secondary"
            onclick="closeModal('sectionModal')"
          >
            Cancel
          </button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
        <input type="hidden" id="sectionId" />
      </form>
    </div>
  </div>
</div>

<script>
  var colleges = [];
  var programs = [];
  var sections = [];
  var campuses = [];
  var allColleges = [];
  var allPrograms = [];
  var allSections = [];

  var showToast = (message, type = "info") => Utils.toast(message, type);
  var escapeHtml = (value) => Utils.escapeHtml(String(value ?? ""));

  (async function initPage() {
    if (!Auth.isAdmin()) {
      window.location.hash = "#/admin/login";
      return;
    }

    await loadCampuses();
    await loadColleges();
  })();

  function switchTab(tab) {
    document
      .querySelectorAll(".tab-btn")
      .forEach((b) => b.classList.remove("active"));
    document
      .querySelectorAll(".tab-content")
      .forEach((c) => c.classList.remove("active"));

    document
      .querySelector(`[onclick="switchTab('${tab}')"]`)
      .classList.add("active");
    document.getElementById(tab + "Tab").classList.add("active");

    if (tab === "programs") loadPrograms();
    else if (tab === "sections") loadSections();
    
    updateStats(tab);
  }

  function updateStats(tab) {
    const statsContainer = document.getElementById("statsCards");
    
    if (tab === "colleges") {
      const totalColleges = colleges.length;
      const activeColleges = colleges.filter(c => c.status === 'active').length;
      const totalPrograms = colleges.reduce((sum, c) => sum + (c.program_count || 0), 0);
      const totalAlumni = colleges.reduce((sum, c) => sum + (c.alumni_count || 0), 0);
      
      statsContainer.innerHTML = `
        <div class="stat-card-improved">
          <div class="stat-icon bg-primary">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value">${totalColleges}</div>
            <div class="stat-label">Total Colleges</div>
          </div>
        </div>
        <div class="stat-card-improved">
          <div class="stat-icon bg-success">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value">${activeColleges}</div>
            <div class="stat-label">Active</div>
          </div>
        </div>
        <div class="stat-card-improved">
          <div class="stat-icon bg-info">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
              <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value">${totalPrograms}</div>
            <div class="stat-label">Total Programs</div>
          </div>
        </div>
        <div class="stat-card-improved">
          <div class="stat-icon bg-warning">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value">${totalAlumni}</div>
            <div class="stat-label">Total Alumni</div>
          </div>
        </div>
      `;
      statsContainer.style.display = 'grid';
    } else if (tab === "programs") {
      const totalPrograms = programs.length;
      const bachelorPrograms = programs.filter(p => p.degree_type === 'bachelor').length;
      const totalSections = programs.reduce((sum, p) => sum + (p.section_count || 0), 0);
      const totalAlumni = programs.reduce((sum, p) => sum + (p.alumni_count || 0), 0);
      
      statsContainer.innerHTML = `
        <div class="stat-card-improved">
          <div class="stat-icon bg-primary">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
              <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value">${totalPrograms}</div>
            <div class="stat-label">Total Programs</div>
          </div>
        </div>
        <div class="stat-card-improved">
          <div class="stat-icon bg-success">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 10v6M2 10l10-5 10 5-10 5z"></path>
              <path d="M6 12v5c3 3 9 3 12 0v-5"></path>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value">${bachelorPrograms}</div>
            <div class="stat-label">Bachelor's</div>
          </div>
        </div>
        <div class="stat-card-improved">
          <div class="stat-icon bg-info">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value">${totalSections}</div>
            <div class="stat-label">Total Sections</div>
          </div>
        </div>
        <div class="stat-card-improved">
          <div class="stat-icon bg-warning">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value">${totalAlumni}</div>
            <div class="stat-label">Total Alumni</div>
          </div>
        </div>
      `;
      statsContainer.style.display = 'grid';
    } else if (tab === "sections") {
      const totalSections = sections.length;
      const currentYear = new Date().getFullYear();
      const recentSections = sections.filter(s => s.batch_year >= currentYear - 5).length;
      const totalAlumni = sections.reduce((sum, s) => sum + (s.alumni_count || 0), 0);
      const avgAlumni = totalSections > 0 ? Math.round(totalAlumni / totalSections) : 0;
      
      statsContainer.innerHTML = `
        <div class="stat-card-improved">
          <div class="stat-icon bg-primary">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="7" height="7"></rect>
              <rect x="14" y="3" width="7" height="7"></rect>
              <rect x="14" y="14" width="7" height="7"></rect>
              <rect x="3" y="14" width="7" height="7"></rect>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value">${totalSections}</div>
            <div class="stat-label">Total Sections</div>
          </div>
        </div>
        <div class="stat-card-improved">
          <div class="stat-icon bg-success">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value">${recentSections}</div>
            <div class="stat-label">Recent (5 yrs)</div>
          </div>
        </div>
        <div class="stat-card-improved">
          <div class="stat-icon bg-info">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value">${totalAlumni}</div>
            <div class="stat-label">Total Alumni</div>
          </div>
        </div>
        <div class="stat-card-improved">
          <div class="stat-icon bg-warning">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <line x1="12" y1="20" x2="12" y2="10"></line>
              <line x1="18" y1="20" x2="18" y2="4"></line>
              <line x1="6" y1="20" x2="6" y2="16"></line>
            </svg>
          </div>
          <div class="stat-info">
            <div class="stat-value">${avgAlumni}</div>
            <div class="stat-label">Avg per Section</div>
          </div>
        </div>
      `;
      statsContainer.style.display = 'grid';
    }
  }

  function filterColleges() {
    const searchTerm = document.getElementById("collegeSearch").value.toLowerCase();
    const filtered = allColleges.filter(c => 
      c.name.toLowerCase().includes(searchTerm) || 
      c.code.toLowerCase().includes(searchTerm)
    );
    colleges = filtered;
    renderColleges();
  }

  function filterPrograms() {
    const searchTerm = document.getElementById("programSearch").value.toLowerCase();
    const filtered = allPrograms.filter(p => 
      p.name.toLowerCase().includes(searchTerm) || 
      p.code.toLowerCase().includes(searchTerm) ||
      p.college_name.toLowerCase().includes(searchTerm)
    );
    programs = filtered;
    renderPrograms();
  }

  function filterSections() {
    const searchTerm = document.getElementById("sectionSearch").value.toLowerCase();
    const filtered = allSections.filter(s => 
      s.name.toLowerCase().includes(searchTerm) || 
      s.program_name.toLowerCase().includes(searchTerm) ||
      String(s.batch_year).includes(searchTerm)
    );
    sections = filtered;
    renderSections(sections);
  }

  async function loadCampuses() {
    try {
      let response;
      try {
        response = await API.get("/campuses/list");
      } catch (primaryError) {
        response = await API.get("/admin/campuses?status=active");
      }

      campuses = response.data || [];
      
      // Populate section campus selector
      const sectionCampusSelect = document.getElementById("sectionCampus");
      if (sectionCampusSelect) {
        sectionCampusSelect.innerHTML = '<option value="">-- Select Campus --</option>';
        campuses.forEach(campus => {
          const option = document.createElement('option');
          option.value = campus.id;
          option.textContent = `${campus.name} (${campus.code})`;
          sectionCampusSelect.appendChild(option);
        });
      }
    } catch (e) {
      console.error("Error loading campuses:", e);
    }
  }

  function populateProgramCampuses(selectedCampusIds = []) {
    const container = document.getElementById("programCampuses");
    if (!container) {
      return;
    }

    if (!campuses.length) {
      container.innerHTML = '<p style="padding: 10px; color: #999;">No campuses available</p>';
      return;
    }
    
    container.innerHTML = campuses
      .map(campus => {
        const isChecked = selectedCampusIds.includes(campus.id) ? 'checked' : '';
        return `
          <div style="margin-bottom: 8px;">
            <label style="display: flex; align-items: center; cursor: pointer;">
              <input type="checkbox" name="campus_ids" value="${campus.id}" ${isChecked} style="margin-right: 8px;">
              <span>${campus.name} (${campus.code})</span>
            </label>
          </div>
        `;
      })
      .join("");
  }

  async function loadColleges() {
    try {
      const response = await API.admin.getOrganization("colleges");
      if (response.success) {
        colleges = response.data;
        allColleges = [...colleges];
        renderColleges();
        populateCollegeSelects();
        updateStats('colleges');
      }
    } catch (e) {
      showToast("Error loading colleges", "error");
    }
  }

  function renderColleges() {
    const tbody = document.getElementById("collegesTable");
    if (!colleges.length) {
      tbody.innerHTML =
        '<tr><td colspan="6" class="text-center"><div class="empty-state"><div class="empty-state-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg></div><div class="empty-state-title">No colleges</div><div class="empty-state-description">Get started by adding your first college</div></div></td></tr>';
      return;
    }
    tbody.innerHTML = colleges
      .map(
        (c) => `
                <tr>
                    <td><strong>${escapeHtml(c.name)}</strong></td>
                    <td><code>${c.code}</code></td>
                    <td>${c.program_count || 0}</td>
                    <td>${c.alumni_count || 0}</td>
                    <td><span class="badge badge-${c.status === "active" ? "success" : "secondary"}">${c.status}</span></td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-icon btn-icon-sm btn-secondary" onclick="editCollege(${c.id})" title="Edit">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                            <button class="btn-icon btn-icon-sm btn-danger" onclick="deleteCollege(${c.id})" title="Delete">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `,
      )
      .join("");
  }

  function populateCollegeSelects() {
    const options = colleges
      .map((c) => `<option value="${c.id}">${c.name}</option>`)
      .join("");
    document.getElementById("collegeFilter").innerHTML =
      '<option value="">All Colleges</option>' + options;
    document.getElementById("programCollege").innerHTML = options;
  }

  async function loadPrograms() {
    const collegeId = document.getElementById("collegeFilter").value;
    try {
      const response = await API.admin.getOrganization("programs", {
        college_id: collegeId,
      });
      if (response.success) {
        programs = response.data;
        allPrograms = [...programs];
        renderPrograms();
        populateProgramSelects();
        updateStats('programs');
      }
    } catch (e) {
      showToast("Error loading programs", "error");
    }
  }

  function renderPrograms() {
    const tbody = document.getElementById("programsTable");
    if (!programs.length) {
      tbody.innerHTML =
        '<tr><td colspan="8" class="text-center"><div class="empty-state"><div class="empty-state-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg></div><div class="empty-state-title">No programs</div><div class="empty-state-description">Add programs to organize your alumni</div></div></td></tr>';
      return;
    }
    tbody.innerHTML = programs
      .map(
        (p) => `
                <tr>
                    <td><strong>${escapeHtml(p.name)}</strong></td>
                    <td><code>${p.code}</code></td>
                    <td>${escapeHtml(p.college_name)}</td>
                    <td>${escapeHtml(p.campus_names || "Unassigned")}</td>
                    <td>${escapeHtml(p.degree_type)}</td>
                    <td>${p.section_count || 0}</td>
                    <td>${p.alumni_count || 0}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-icon btn-icon-sm btn-secondary" onclick="editProgram(${p.id})" title="Edit">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                            <button class="btn-icon btn-icon-sm btn-danger" onclick="deleteProgram(${p.id})" title="Delete">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `,
      )
      .join("");
  }

  function populateProgramSelects() {
    const options = programs
      .map(
        (p) => `<option value="${p.id}">${p.name} (${p.college_name})</option>`,
      )
      .join("");
    document.getElementById("programFilter").innerHTML =
      '<option value="">All Programs</option>' + options;
    document.getElementById("sectionProgram").innerHTML = options;
  }

  async function loadSections() {
    const programId = document.getElementById("programFilter").value;
    const batchYear = document.getElementById("batchFilter").value;
    try {
      const response = await API.admin.getOrganization("sections", {
        program_id: programId,
        batch_year: batchYear,
      });
      if (response.success) {
        sections = Array.isArray(response.data) ? response.data : [];
        allSections = [...sections];
        renderSections(sections);
        updateStats('sections');
      }
    } catch (e) {
      showToast("Error loading sections", "error");
    }
  }

  function renderSections(sections) {
    const tbody = document.getElementById("sectionsTable");
    if (!sections.length) {
      tbody.innerHTML =
        '<tr><td colspan="6" class="text-center"><div class="empty-state"><div class="empty-state-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg></div><div class="empty-state-title">No sections</div><div class="empty-state-description">Create sections to group alumni by batch</div></div></td></tr>';
      return;
    }
    tbody.innerHTML = sections
      .map(
        (s) => `
                <tr>
                    <td><strong>${escapeHtml(s.name)}</strong></td>
                    <td>${escapeHtml(s.program_name)}</td>
                    <td>${escapeHtml(s.campus_name || "Unassigned")}</td>
                    <td>${s.batch_year}</td>
                    <td>${s.alumni_count || 0}</td>
                    <td>
                        <div class="action-buttons">
                            <button class="btn-icon btn-icon-sm btn-secondary" onclick="editSection(${s.id})" title="Edit">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                            <button class="btn-icon btn-icon-sm btn-danger" onclick="deleteSection(${s.id})" title="Delete">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
            `,
      )
      .join("");
  }

  // College CRUD
  function showCollegeModal() {
    document.getElementById("collegeModalTitle").textContent = "Add College";
    document.getElementById("collegeForm").reset();
    document.getElementById("collegeId").value = "";
    Utils.openModal("#collegeModal");
  }

  function editCollege(id) {
    const c = colleges.find((x) => x.id == id);
    if (!c) return;
    document.getElementById("collegeModalTitle").textContent = "Edit College";
    document.getElementById("collegeId").value = c.id;
    document.getElementById("collegeName").value = c.name;
    document.getElementById("collegeCode").value = c.code;
    document.getElementById("collegeDescription").value = c.description || "";
    Utils.openModal("#collegeModal");
  }

  async function saveCollege(e) {
    e.preventDefault();
    const id = document.getElementById("collegeId").value;
    const data = {
      name: document.getElementById("collegeName").value,
      code: document.getElementById("collegeCode").value.toUpperCase(),
      description: document.getElementById("collegeDescription").value,
    };

    try {
      const response = id
        ? await API.admin.updateOrganization("colleges", id, data)
        : await API.admin.createOrganization("colleges", data);
      if (response.success) {
        showToast(response.message, "success");
        closeModal("collegeModal");
        loadColleges();
      } else {
        showToast(response.message, "error");
      }
    } catch (e) {
      showToast("Error saving college", "error");
    }
  }

  async function deleteCollege(id) {
    if (!confirm("Delete this college?")) return;
    try {
      const response = await API.admin.deleteOrganization("colleges", id);
      if (response.success) {
        showToast("College deleted", "success");
        loadColleges();
      } else {
        showToast(response.message, "error");
      }
    } catch (e) {
      showToast("Error deleting college", "error");
    }
  }

  // Program CRUD
  function showProgramModal() {
    document.getElementById("programModalTitle").textContent = "Add Program";
    document.getElementById("programForm").reset();
    document.getElementById("programId").value = "";
    populateProgramCampuses([]);
    Utils.openModal("#programModal");
  }

  function editProgram(id) {
    const p = programs.find((x) => x.id == id);
    if (!p) return;
    document.getElementById("programModalTitle").textContent = "Edit Program";
    document.getElementById("programId").value = p.id;
    document.getElementById("programCollege").value = p.college_id;
    document.getElementById("programName").value = p.name;
    document.getElementById("programCode").value = p.code;
    document.getElementById("programDegree").value =
      p.degree_type || "bachelor";
    
    // Load and populate program campuses
    loadProgramCampuses(id);
    
    Utils.openModal("#programModal");
  }

  async function loadProgramCampuses(programId) {
    try {
      const response = await API.get(`/admin/organization/program-campuses/${programId}`);
      const campusIds = (response.data || []).map(c => c.id);
      populateProgramCampuses(campusIds);
    } catch (e) {
      console.error("Error loading program campuses:", e);
      populateProgramCampuses([]);
    }
  }

  async function saveProgram(e) {
    e.preventDefault();
    const id = document.getElementById("programId").value;
    const campusCheckboxes = document.querySelectorAll('[name="campus_ids"]:checked');
    const campusIds = Array.from(campusCheckboxes).map(cb => parseInt(cb.value));

    if (!campusIds.length) {
      showToast("Please assign this program to at least one campus", "error");
      return;
    }

    const data = {
      college_id: document.getElementById("programCollege").value,
      name: document.getElementById("programName").value,
      code: document.getElementById("programCode").value.toUpperCase(),
      degree_type: document.getElementById("programDegree").value,
    };

    try {
      const response = id
        ? await API.admin.updateOrganization("programs", id, data)
        : await API.admin.createOrganization("programs", data);
      if (response.success) {
        const programId = id || response.data.id;
        await API.post(`/admin/organization/program-campuses/${programId}`, {
          campus_ids: campusIds
        });
        
        showToast(response.message, "success");
        closeModal("programModal");
        loadPrograms();
      } else {
        showToast(response.message, "error");
      }
    } catch (e) {
      showToast("Error saving program", "error");
    }
  }

  async function deleteProgram(id) {
    if (!confirm("Delete this program?")) return;
    try {
      const response = await API.admin.deleteOrganization("programs", id);
      if (response.success) {
        showToast("Program deleted", "success");
        loadPrograms();
      } else {
        showToast(response.message, "error");
      }
    } catch (e) {
      showToast("Error deleting program", "error");
    }
  }

  async function ensureProgramsLoaded() {
    if (programs.length) return;

    try {
      const response = await API.admin.getOrganization("programs");
      if (response.success) {
        programs = Array.isArray(response.data) ? response.data : [];
        populateProgramSelects();
      }
    } catch (e) {
      showToast("Error loading programs", "error");
    }
  }

  // Section CRUD
  async function showSectionModal() {
    await ensureProgramsLoaded();
    document.getElementById("sectionModalTitle").textContent = "Add Section";
    document.getElementById("sectionForm").reset();
    document.getElementById("sectionId").value = "";
    document.getElementById("sectionBatch").value = new Date().getFullYear();
    Utils.openModal("#sectionModal");
  }

  async function editSection(id) {
    await ensureProgramsLoaded();

    const s = sections.find((x) => x.id == id);
    if (!s) {
      showToast("Unable to load section details", "error");
      return;
    }

    const matchedProgram = programs.find(
      (p) => String(p.id) === String(s.program_id) || p.name === s.program_name,
    );

    document.getElementById("sectionModalTitle").textContent = "Edit Section";
    document.getElementById("sectionId").value = s.id;
    document.getElementById("sectionProgram").value =
      matchedProgram?.id ?? s.program_id ?? "";
    document.getElementById("sectionName").value = s.name || "";
    document.getElementById("sectionBatch").value =
      s.batch_year || new Date().getFullYear();
    document.getElementById("sectionCampus").value = s.campus_id || "";
    Utils.openModal("#sectionModal");
  }

  async function saveSection(e) {
    e.preventDefault();
    const id = document.getElementById("sectionId").value;
    const data = {
      program_id: document.getElementById("sectionProgram").value,
      campus_id: document.getElementById("sectionCampus").value,
      name: document.getElementById("sectionName").value,
      batch_year: document.getElementById("sectionBatch").value,
    };

    if (!data.campus_id) {
      showToast("Please select a campus", "error");
      return;
    }

    try {
      const response = id
        ? await API.admin.updateOrganization("sections", id, data)
        : await API.admin.createOrganization("sections", data);
      if (response.success) {
        showToast(response.message, "success");
        closeModal("sectionModal");
        loadSections();
      } else {
        showToast(response.message, "error");
      }
    } catch (e) {
      showToast("Error saving section", "error");
    }
  }

  async function deleteSection(id) {
    if (!confirm("Delete this section?")) return;
    try {
      const response = await API.admin.deleteOrganization("sections", id);
      if (response.success) {
        showToast("Section deleted", "success");
        loadSections();
      } else {
        showToast(response.message, "error");
      }
    } catch (e) {
      showToast("Error deleting section", "error");
    }
  }

  function closeModal(modalId) {
    const selector = String(modalId || "").startsWith("#")
      ? String(modalId)
      : `#${modalId}`;
    Utils.closeModal(selector);
  }
</script>

