<!-- Admin Events Management Page -->
<link rel="stylesheet" href="/assets/css/dashboard-improvements.css">
<link rel="stylesheet" href="/assets/css/admin-premium.css">

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
      <h1 class="page-title">Events Management</h1>
      <div class="topbar-actions">
        <button class="btn btn-primary" id="createEventBtn">
          <svg
            width="16"
            height="16"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" />
          </svg>
          Create Event
        </button>
      </div>
    </header>

    <div class="admin-content p-lg">
      <div class="admin-panel-toolbar mb-lg">
        <div>
          <h2>Plan Alumni Events</h2>
          <p>Create and manage event schedules, attendance, and rewards.</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="card-improved mb-lg">
        <div class="card-body">
          <div class="grid grid-cols-4 gap-md">
            <input
              type="text"
              id="searchInput"
              class="form-input"
              placeholder="Search events..."
            />
            <select id="statusFilter" class="form-select">
              <option value="">All Status</option>
              <option value="upcoming">Upcoming</option>
              <option value="ongoing">Ongoing</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
            <select id="typeFilter" class="form-select">
              <option value="">All Types</option>
              <option value="seminar">Seminar</option>
              <option value="reunion">Reunion</option>
              <option value="workshop">Workshop</option>
              <option value="networking">Networking</option>
              <option value="career_fair">Career Fair</option>
              <option value="webinar">Webinar</option>
              <option value="other">Other</option>
            </select>
            <input type="month" id="monthFilter" class="form-input" />
          </div>
        </div>
      </div>

      <!-- Events Table -->
      <div class="card-improved">
        <div class="card-body p-0">
          <table class="table-improved">
            <thead>
              <tr>
                <th><a href="#" onclick="sortBy('title'); return false;" class="sort-link">Event</a></th>
                <th><a href="#" onclick="sortBy('event_date'); return false;" class="sort-link">Date & Time</a></th>
                <th>Location</th>
                <th>Campus</th>
                <th>Type</th>
                <th>Registrations</th>
                <th>Points</th>
                <th><a href="#" onclick="sortBy('status'); return false;" class="sort-link">Status</a></th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="eventsTable">
              <tr>
                <td colspan="8" class="text-center">Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
        
        <!-- Pagination -->
        <div class="flex justify-between items-center p-md border-t">
          <div class="text-sm text-secondary" id="paginationInfo">Loading...</div>
          <div class="flex gap-sm">
            <button class="btn btn-ghost btn-sm" id="prevPageBtn" onclick="previousPage()">← Previous</button>
            <div class="flex items-center gap-sm">
              <span>Page</span>
              <input type="number" id="pageInput" class="form-input" style="width: 60px; min-height: 32px;" value="1" min="1" />
              <span id="maxPageLabel">of 1</span>
            </div>
            <button class="btn btn-ghost btn-sm" id="nextPageBtn" onclick="nextPage()">Next →</button>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Create/Edit Event Modal -->
<div class="modal-improved" id="eventModal">
  <div class="modal-backdrop"></div>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="eventModalTitle">Create Event</h3>
        <button class="modal-close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <form id="eventForm">
          <input type="hidden" name="id" id="eventId" />

          <div class="grid grid-cols-2 gap-md">
            <div class="form-group col-span-2">
              <label class="form-label required">Event Title</label>
              <input type="text" name="title" class="form-input" required />
            </div>

            <div class="form-group col-span-2">
              <label class="form-label">Description</label>
              <textarea
                name="description"
                class="form-input"
                rows="4"
              ></textarea>
            </div>

            <div class="form-group">
              <label class="form-label required">Event Date</label>
              <input
                type="date"
                name="event_date"
                class="form-input"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label required">Event Time</label>
              <input
                type="time"
                name="event_time"
                class="form-input"
                required
              />
            </div>

            <div class="form-group col-span-2">
              <label class="form-label">Location</label>
              <input
                type="text"
                name="location"
                class="form-input"
                placeholder="Physical address or online link"
              />
            </div>

            <div class="form-group">
              <label class="form-label required">Event Type</label>
              <select name="event_type" class="form-select" required>
                <option value="seminar">Seminar</option>
                <option value="reunion">Reunion</option>
                <option value="workshop">Workshop</option>
                <option value="networking">Networking</option>
                <option value="career_fair">Career Fair</option>
                <option value="webinar">Webinar</option>
                <option value="other">Other</option>
              </select>
            </div>

            <div class="form-group col-span-2">
              <label class="form-label">Campuses</label>
              <select name="campus_ids[]" id="modalCampusSelect" style="display:none" multiple size="5">
                <option value="">All Campuses</option>
              </select>

              <div class="dropdown-checkbox" id="modalCampusDropdown" style="position:relative">
                <button type="button" class="btn btn-outline" id="modalCampusDropdownToggle">All Campuses ▾</button>
                <div class="dropdown-checkbox-menu" id="modalCampusDropdownMenu" style="display:none; max-height:220px; overflow:auto; border:1px solid rgba(0,0,0,0.08); background:white; padding:8px; border-radius:6px; position:absolute; z-index:60;">
                </div>
              </div>

              <div class="text-sm text-secondary mt-xs">
                Leave blank to show this event to all campuses. Select one or more campuses to target specific sites.
              </div>
            </div>

            <div class="form-group">
              <label class="form-label required">Points Reward</label>
              <input
                type="number"
                name="points_reward"
                class="form-input"
                value="20"
                min="0"
                required
              />
            </div>

            <div class="form-group">
              <label class="form-label">Max Attendees</label>
              <input
                type="number"
                name="max_attendees"
                class="form-input"
                placeholder="Leave empty for unlimited"
              />
            </div>

            <div class="form-group">
              <label class="form-label">Registration Deadline</label>
              <input
                type="datetime-local"
                name="registration_deadline"
                class="form-input"
              />
            </div>

            <div class="form-group col-span-2">
              <label class="form-label">Event Image</label>
              <input
                type="file"
                name="event_image"
                id="modalEventImageInput"
                class="form-input"
                accept="image/jpeg,image/png,image/gif,image/webp"
              />
              <div class="text-sm text-secondary mt-xs">JPEG, PNG, GIF, or WebP. Maximum size: 5MB.</div>
              <div id="modalImagePreview" class="mt-md" style="display: none">
                <img id="modalPreviewImage" alt="" style="max-width: 280px; max-height: 180px; border-radius: var(--radius-md); object-fit: cover;" />
                <div id="modalImageMeta" class="text-sm text-secondary mt-xs"></div>
                <button type="button" class="btn btn-ghost btn-sm mt-sm" id="modalClearImageBtn">Remove selected image</button>
              </div>
            </div>

            <div class="form-group col-span-2">
              <label class="form-label">Image URL</label>
              <input
                type="url"
                name="cover_image"
                class="form-input"
                placeholder="https://..."
              />
              <div class="text-sm text-secondary mt-xs">Optional fallback for existing external event images.</div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="saveEventBtn">Save Event</button>
      </div>
    </div>
  </div>
</div>

<!-- Event Attendance Modal -->
<div class="modal-improved" id="attendanceModal">
  <div class="modal-backdrop"></div>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Event Attendance</h3>
        <button class="modal-close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="flex justify-between items-center mb-lg">
          <div>
            <h4 id="attendanceEventTitle">Event</h4>
            <p class="text-secondary" id="attendanceEventDate">Date</p>
          </div>
          <div class="text-right">
            <div class="text-2xl font-bold" id="attendanceCode">------</div>
            <div class="text-sm text-secondary">Attendance Code</div>
          </div>
        </div>

        <div class="card mb-lg" style="background: var(--gray-100)">
          <div class="card-body text-center">
            <div
              id="qrCode"
              style="
                width: 200px;
                height: 200px;
                margin: 0 auto;
                background: white;
                border-radius: var(--radius-md);
              "
            >
              <!-- QR Code will be generated here -->
            </div>
            <p class="text-sm text-secondary mt-md">
              Scan this QR code for attendance
            </p>
          </div>
        </div>

        <div class="flex gap-md mb-lg">
          <button class="btn btn-primary flex-1" id="generateNewCodeBtn">
            Generate New Code
          </button>
          <button class="btn btn-secondary flex-1" id="printQRBtn">
            Print QR Code
          </button>
        </div>

        <h5 class="font-semibold mb-md">Registered Attendees</h5>
        <div class="table-responsive">
          <table class="table-improved">
            <thead>
              <tr>
                <th>Alumni</th>
                <th>Registered At</th>
                <th>Status</th>
                <th>Check-in Time</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="attendeesTable">
              <tr>
                <td colspan="5" class="text-center">Loading...</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js" crossorigin="anonymous"></script>
<script>
  (function () {
    let events = [];
    let qrCodeInstance = null;
    let currentEventId = null;
    let currentPage = 1;
    let pageSize = 20;
    let totalPages = 1;
    let currentSort = 'event_date';
    let currentOrder = 'DESC';
    const allowedImageTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
    const maxImageSize = 5 * 1024 * 1024;
    const imageInput = Utils.$("#modalEventImageInput");
    const imagePreview = Utils.$("#modalImagePreview");
    const previewImage = Utils.$("#modalPreviewImage");
    const imageMeta = Utils.$("#modalImageMeta");
    const clearImageBtn = Utils.$("#modalClearImageBtn");
    const campusSelect = Utils.$("#modalCampusSelect");
    let campusesReady = null;
    let defaultCampusId = "";
    const bind = (selector, eventName, handler) => {
      const element = Utils.$(selector);
      if (element) {
        element.addEventListener(eventName, handler);
      }
      return element;
    };

    function buildAttendanceQrPayload(eventId, code) {
      return JSON.stringify({
        type: "event_attendance",
        event_id: Number(eventId),
        code: String(code || "").trim().toUpperCase(),
      });
    }

    async function loadCampuses() {
      try {
        const [campusResponse, profileResponse] = await Promise.all([
          API.get("/campuses/list"),
          API.admin.getProfile(),
        ]);

        const campuses = Array.isArray(campusResponse?.data) ? campusResponse.data : [];
        const currentUser = profileResponse?.data || profileResponse || {};

        campusSelect.innerHTML = '<option value="">All Campuses</option>' + campuses.map((campus) => {
          return `<option value="${campus.id}">${Utils.escapeHtml(campus.name)} (${Utils.escapeHtml(campus.code || "-")})</option>`;
        }).join("");

        buildModalCampusDropdown();

        defaultCampusId = currentUser.campus_id && ["campus_admin", "staff"].includes(currentUser.role)
          ? String(currentUser.campus_id)
          : "";

        if (defaultCampusId) {
          setSelectedCampuses([defaultCampusId]);
        }
      } catch (error) {
        console.error("Failed to load campuses for events:", error);
      }
    }

    function setSelectedCampuses(campusIds) {
      const selected = new Set((campusIds || []).map((value) => String(value)));
      Array.from(campusSelect.options).forEach((option) => {
        if (!option.value) {
          option.selected = selected.size === 0;
          return;
        }
        option.selected = selected.has(option.value);
      });

      // sync modal dropdown checkboxes and label
      const menu = Utils.$('#modalCampusDropdownMenu');
      if (menu) {
        menu.querySelectorAll('.campus-checkbox').forEach((cb) => {
          const v = cb.getAttribute('data-value');
          if (!v) {
            cb.checked = selected.size === 0;
          } else {
            cb.checked = selected.has(v);
          }
        });
      }
      updateModalCampusToggleLabel();
    }

    function buildModalCampusDropdown() {
      const menu = Utils.$("#modalCampusDropdownMenu");
      const toggle = Utils.$("#modalCampusDropdownToggle");
      menu.innerHTML = '';
      Array.from(campusSelect.options).forEach((opt) => {
        if (!opt.value) {
          const allRow = document.createElement('div');
          allRow.innerHTML = `<label style="display:flex;align-items:center;gap:8px"><input type="checkbox" data-value="" class="campus-checkbox"> <span>All Campuses</span></label>`;
          menu.appendChild(allRow);
          return;
        }
        const row = document.createElement('div');
        row.innerHTML = `<label style="display:flex;align-items:center;gap:8px"><input type="checkbox" data-value="${opt.value}" class="campus-checkbox"> <span>${Utils.escapeHtml(opt.text)}</span></label>`;
        menu.appendChild(row);
      });

      toggle.onclick = (e) => {
        e.stopPropagation();
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
      };

      document.addEventListener('click', (ev) => {
        if (!Utils.$('#modalCampusDropdown')?.contains(ev.target)) {
          menu.style.display = 'none';
        }
      });

      menu.querySelectorAll('.campus-checkbox').forEach((cb) => {
        cb.addEventListener('change', () => {
          const value = cb.getAttribute('data-value');
          if (!value) {
            if (cb.checked) {
              menu.querySelectorAll('.campus-checkbox').forEach((other) => { if (other !== cb) other.checked = false; });
              setSelectedCampuses([]);
            } else {
              setSelectedCampuses([]);
            }
          } else {
            if (cb.checked) {
              const all = menu.querySelector('.campus-checkbox[data-value=""]');
              if (all) all.checked = false;
            }
            const selected = Array.from(menu.querySelectorAll('.campus-checkbox'))
              .filter((c) => c.checked && c.getAttribute('data-value'))
              .map((c) => c.getAttribute('data-value'));
            setSelectedCampuses(selected);
            if (!selected.length) {
              const all = menu.querySelector('.campus-checkbox[data-value=""]');
              if (all) all.checked = true;
            }
          }
        });
      });

      updateModalCampusToggleLabel();
    }

    function updateModalCampusToggleLabel() {
      const toggle = Utils.$("#modalCampusDropdownToggle");
      const menu = Utils.$("#modalCampusDropdownMenu");
      if (!toggle || !menu) return;
      const checked = Array.from(menu.querySelectorAll('.campus-checkbox'))
        .filter((c) => c.checked && c.getAttribute('data-value'))
        .map((c) => c.parentElement.querySelector('span')?.textContent || '');
      if (!checked.length) {
        toggle.textContent = 'All Campuses ▾';
      } else if (checked.length === 1) {
        toggle.textContent = checked[0] + ' ▾';
      } else {
        toggle.textContent = `${checked.length} campuses selected ▾`;
      }
    }

    campusesReady = loadCampuses();

    loadEvents();

    // Filter listeners
    bind(
      "#searchInput",
      "input",
      Utils.debounce(() => { currentPage = 1; loadEvents(); }, 300),
    );
    bind("#statusFilter", "change", () => { currentPage = 1; loadEvents(); });
    bind("#typeFilter", "change", () => { currentPage = 1; loadEvents(); });
    bind("#monthFilter", "change", () => { currentPage = 1; loadEvents(); });
    
    // Page input listener
    bind("#pageInput", "change", (e) => {
      const page = parseInt(e.target.value) || 1;
      currentPage = Math.max(1, Math.min(page, totalPages));
      const pageInput = Utils.$("#pageInput");
      if (pageInput) {
        pageInput.value = currentPage;
      }
      loadEvents();
    });

    async function openCreateEventModal() {
      if (campusesReady) {
        await campusesReady;
      }
      Utils.$("#eventModalTitle").textContent = "Create Event";
      Utils.$("#eventForm").reset();
      Utils.$("#eventId").value = "";
      setSelectedCampuses(defaultCampusId ? [defaultCampusId] : []);
      renderImagePreview("", "");
      Utils.openModal("#eventModal");
    }

    function bindCreateButtons() {
      [
        "#createEventBtn",
        "#createEventBtnEmpty",
      ].forEach((selector) => {
        const btn = Utils.$(selector);
        if (btn) {
          btn.onclick = openCreateEventModal;
        }
      });
    }

    bindCreateButtons();

    // Save event
    bind("#saveEventBtn", "click", saveEvent);
    if (imageInput) {
      imageInput.addEventListener("change", handleImageSelection);
    }
    if (clearImageBtn && imageInput) {
      clearImageBtn.addEventListener("click", () => {
        imageInput.value = "";
        const eventForm = Utils.$("#eventForm");
        const coverImageValue = eventForm?.elements?.cover_image?.value || "";
        renderImagePreview(coverImageValue, coverImageValue ? "Current image" : "");
      });
    }

    // Generate new attendance code
    bind("#generateNewCodeBtn", "click", generateNewCode);
    
    // Print QR Code
    bind("#printQRBtn", "click", printQRCode);
    
    // Global sort/pagination functions
    window.sortBy = function(field) {
      if (currentSort === field) {
        currentOrder = currentOrder === 'DESC' ? 'ASC' : 'DESC';
      } else {
        currentSort = field;
        currentOrder = 'DESC';
      }
      currentPage = 1;
      loadEvents();
    };
    
    window.previousPage = function() {
      if (currentPage > 1) {
        currentPage--;
        Utils.$("#pageInput").value = currentPage;
        loadEvents();
      }
    };
    
    window.nextPage = function() {
      if (currentPage < totalPages) {
        currentPage++;
        Utils.$("#pageInput").value = currentPage;
        loadEvents();
      }
    };

    async function loadEvents() {
      const params = {
        search: Utils.$("#searchInput").value,
        status: Utils.$("#statusFilter").value,
        event_type: Utils.$("#typeFilter").value,
        month: Utils.$("#monthFilter").value,
        page: currentPage,
        limit: pageSize,
        sort: currentSort,
        order: currentOrder,
      };

      try {
        const response = await API.admin.getEvents(params);
        events = response.data?.events || response.data?.data?.events || [];
        
        // Update pagination
        const pagination = response.data?.pagination;
        if (pagination) {
          totalPages = Math.ceil(pagination.total / pageSize);
          const startItem = (currentPage - 1) * pageSize + 1;
          const endItem = Math.min(currentPage * pageSize, pagination.total);
          Utils.$("#paginationInfo").textContent = `Showing ${startItem}-${endItem} of ${pagination.total} events`;
          Utils.$("#maxPageLabel").textContent = `of ${totalPages}`;
          Utils.$("#prevPageBtn").disabled = currentPage === 1;
          Utils.$("#nextPageBtn").disabled = currentPage === totalPages;
        }
        
        renderTable();
      } catch (error) {
        console.error("Load events error:", error);
      }
    }

    function renderTable() {
      const tbody = Utils.$("#eventsTable");

      if (!events.length) {
        tbody.innerHTML =
          '<tr><td colspan="9" class="text-center text-secondary"><div class="p-lg">No events found.<div class="mt-sm"><button class="btn btn-primary btn-sm" id="createEventBtnEmpty">+ Add Event</button></div></div></td></tr>';
        bindCreateButtons();
        return;
      }

      tbody.innerHTML = events
        .map(
          (e) => `
            <tr>
                <td>
                    <div class="font-medium">${Utils.escapeHtml(e.title)}</div>
                </td>
                <td>
                    <div>${Utils.formatDate(e.event_date)}</div>
                    <div class="text-sm text-secondary">${e.event_time}</div>
                </td>
                <td>${Utils.escapeHtml(e.location || "-")}</td>
                <td><span class="badge badge-${e.campus_names ? "primary" : "secondary"}">${Utils.escapeHtml(e.campus_names || "All Campuses")}</span></td>
                <td><span class="badge badge-${getTypeBadge(e.event_type)}">${e.event_type}</span></td>
                <td>${e.registered_count || 0}${e.max_attendees ? ` / ${e.max_attendees}` : ""}</td>
                <td class="font-medium text-primary">${e.points_reward} pts</td>
                <td><span class="badge badge-${getStatusBadge(e.status)}">${e.status}</span></td>
                <td>
                    <div class="flex gap-xs">
                        <button class="btn btn-ghost btn-sm" onclick="editEvent(${e.id})" title="Edit">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        <button class="btn btn-ghost btn-sm" onclick="manageAttendance(${e.id})" title="Attendance">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                            </svg>
                        </button>
                        <button class="btn btn-ghost btn-sm text-danger" onclick="deleteEvent(${e.id})" title="Delete">
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

      bindCreateButtons();
    }

    function getTypeBadge(type) {
      const badges = {
        seminar: "primary",
        reunion: "success",
        workshop: "warning",
        networking: "info",
        career_fair: "danger",
        webinar: "secondary",
      };
      return badges[type] || "default";
    }

    function getStatusBadge(status) {
      const badges = {
        upcoming: "primary",
        ongoing: "success",
        completed: "secondary",
        cancelled: "danger",
      };
      return badges[status] || "default";
    }

    window.editEvent = async (id) => {
      try {
        if (campusesReady) {
          await campusesReady;
        }
        const response = await API.admin.getEvent(id);
        const event = response.data?.event || response.data;

        Utils.$("#eventModalTitle").textContent = "Edit Event";
        Utils.$("#eventId").value = event.id;

        const form = Utils.$("#eventForm");
        form.elements.title.value = event.title;
        form.elements.description.value = event.description || "";
        form.elements.event_date.value = event.event_date;
        form.elements.event_time.value = event.event_time;
        form.elements.location.value = event.location || "";
        form.elements.event_type.value = event.event_type;
        form.elements.points_reward.value = event.points_reward;
        form.elements.max_attendees.value = event.max_attendees || "";
        form.elements.registration_deadline.value =
          event.registration_deadline || "";
        form.elements.cover_image.value = event.cover_image || event.image || "";
        imageInput.value = "";
        renderImagePreview(event.cover_image || event.image || "", "Current image");
        setSelectedCampuses(event.campus_ids || (event.campus_id ? [event.campus_id] : []));

        Utils.openModal("#eventModal");
      } catch (error) {
        Utils.error("Failed to load event");
      }
    };

    async function saveEvent() {
      const form = Utils.$("#eventForm");
      
      // Validate required fields before submission
      const title = form.elements.title?.value?.trim();
      const eventDate = form.elements.event_date?.value;
      const eventTime = form.elements.event_time?.value;
      
      if (!title) {
        Utils.error("Event title is required");
        return;
      }
      
      if (!eventDate) {
        Utils.error("Event date is required");
        return;
      }
      
      if (!eventTime) {
        Utils.error("Event time is required");
        return;
      }
      
      const data = new FormData(form);

      const selectedCampusIds = Array.from(campusSelect.selectedOptions)
        .map((option) => option.value)
        .filter(Boolean);

      if (!imageInput.files || !imageInput.files[0]) {
        data.delete("event_image");
      }

      if (!selectedCampusIds.length) {
        data.set("campus_ids[]", "");
      }
      
      const id = Utils.$("#eventId").value;

      Utils.setButtonLoading("#saveEventBtn", true);

      try {
        if (id) {
          await API.admin.updateEvent(id, data);
          Utils.success("Event updated");
        } else {
          await API.admin.createEvent(data);
          Utils.success("Event created");
        }

        Utils.closeModal("#eventModal");
        loadEvents();
      } catch (error) {
        console.error("Save event error:", error);
        Utils.error(error.message || "Failed to save event");
      } finally {
        Utils.setButtonLoading("#saveEventBtn", false);
      }
    }

    function formatFileSize(bytes) {
      if (!bytes) return "0 KB";
      const mb = bytes / (1024 * 1024);
      return mb >= 1 ? `${mb.toFixed(1)} MB` : `${Math.ceil(bytes / 1024)} KB`;
    }

    function validateImageFile(file) {
      if (!allowedImageTypes.includes(file.type)) {
        return "Invalid file type. Please select a JPEG, PNG, GIF, or WebP image.";
      }

      if (file.size > maxImageSize) {
        return `File size exceeds the maximum limit of 5MB. Your file: ${formatFileSize(file.size)}.`;
      }

      return "";
    }

    function renderImagePreview(src, metaText = "") {
      const resolvedSrc = src && typeof API !== "undefined" && API.resolveAssetUrl
        ? API.resolveAssetUrl(src)
        : src;

      if (!resolvedSrc) {
        imagePreview.style.display = "none";
        previewImage.removeAttribute("src");
        imageMeta.textContent = "";
        return;
      }

      previewImage.src = resolvedSrc;
      imageMeta.textContent = metaText;
      imagePreview.style.display = "block";
    }

    function handleImageSelection() {
      const file = imageInput.files && imageInput.files[0];

      if (!file) {
        renderImagePreview(Utils.$("#eventForm").elements.cover_image.value, "Current image");
        return;
      }

      const validationError = validateImageFile(file);
      if (validationError) {
        Utils.error(validationError);
        imageInput.value = "";
        renderImagePreview(Utils.$("#eventForm").elements.cover_image.value, "Current image");
        return;
      }

      const reader = new FileReader();
      reader.onload = (event) => {
        renderImagePreview(event.target.result, `${file.name} - ${formatFileSize(file.size)}`);

        const img = new Image();
        img.onload = () => {
          imageMeta.textContent = `${file.name} - ${formatFileSize(file.size)} - ${img.naturalWidth}x${img.naturalHeight}`;
        };
        img.src = event.target.result;
      };
      reader.readAsDataURL(file);
    }

    window.deleteEvent = async (id) => {
      if (!confirm("Are you sure you want to delete this event?")) return;

      try {
        await API.admin.deleteEvent(id);
        Utils.success("Event deleted");
        loadEvents();
      } catch (error) {
        Utils.error("Failed to delete event");
      }
    };

    window.manageAttendance = async (id) => {
      currentEventId = id;
      const event = events.find((e) => e.id === id);

      Utils.$("#attendanceEventTitle").textContent = event?.title || "Event";
      Utils.$("#attendanceEventDate").textContent = event
        ? `${Utils.formatDate(event.event_date)} at ${event.event_time}`
        : "";

      await loadAttendanceData(id);
      Utils.openModal("#attendanceModal");
    };

    async function loadAttendanceData(eventId) {
      try {
        const response = await API.events.getAttendance(eventId);
        const data = response.data;

        Utils.$("#attendanceCode").textContent =
          data.attendance_code || "------";

        // Generate QR code
        const qrContainer = Utils.$("#qrCode");
        qrContainer.innerHTML = ''; // Clear previous QR code
        
        if (data.attendance_code && typeof QRCode !== 'undefined') {
          try {
            if (qrCodeInstance) {
              qrCodeInstance.clear();
            }
            qrCodeInstance = new QRCode(qrContainer, {
              text: buildAttendanceQrPayload(eventId, data.attendance_code),
              width: 200,
              height: 200,
              colorDark: '#000000',
              colorLight: '#ffffff',
              correctLevel: QRCode.CorrectLevel.M
            });
          } catch (e) {
            console.error('QR generation error:', e);
            qrContainer.innerHTML = `
              <div class="flex items-center justify-center h-100">
                <span class="text-2xl font-bold">${data.attendance_code}</span>
              </div>
            `;
          }
        } else {
          qrContainer.innerHTML = `
            <div class="flex items-center justify-center h-100">
              <span class="text-2xl font-bold">${data.attendance_code || "------"}</span>
            </div>
          `;
        }

        // Render attendees
        const attendees = data.attendees || [];
        const tbody = Utils.$("#attendeesTable");

        if (!attendees.length) {
          tbody.innerHTML =
            '<tr><td colspan="5" class="text-center text-secondary">No registrations yet</td></tr>';
          return;
        }

        tbody.innerHTML = attendees
          .map(
            (a) => `
                <tr>
                    <td>
                        <div class="flex items-center gap-sm">
                            <div class="avatar avatar-sm bg-primary"><span>${Utils.getInitials(a.name)}</span></div>
                            <div>
                                <div class="font-medium">${Utils.escapeHtml(a.name)}</div>
                                <div class="text-xs text-secondary">${a.alumni_id || ""}</div>
                            </div>
                        </div>
                    </td>
                    <td>${Utils.formatDateTime(a.registered_at)}</td>
                    <td><span class="badge badge-${a.status === "attended" ? "success" : "secondary"}">${a.status}</span></td>
                    <td>${a.check_in_time ? Utils.formatDateTime(a.check_in_time) : "-"}</td>
                    <td>
                        ${
                          a.status !== "attended"
                            ? `
                            <button class="btn btn-success btn-sm" onclick="markAttended(${a.user_id})">Mark Attended</button>
                        `
                            : ""
                        }
                    </td>
                </tr>
            `,
          )
          .join("");
      } catch (error) {
        console.error("Load attendance error:", error);
      }
    }

    async function generateNewCode() {
      if (!currentEventId) return;

      Utils.setButtonLoading("#generateNewCodeBtn", true);

      try {
        const response =
          await API.events.generateAttendanceCode(currentEventId);
        Utils.$("#attendanceCode").textContent = response.data.code;
        
        // Generate QR code
        const qrContainer = Utils.$("#qrCode");
        qrContainer.innerHTML = ''; // Clear previous QR code
        
        if (response.data.code && typeof QRCode !== 'undefined') {
          try {
            if (qrCodeInstance) {
              qrCodeInstance.clear();
            }
            qrCodeInstance = new QRCode(qrContainer, {
              text: buildAttendanceQrPayload(currentEventId, response.data.code),
              width: 200,
              height: 200,
              colorDark: '#000000',
              colorLight: '#ffffff',
              correctLevel: QRCode.CorrectLevel.M
            });
          } catch (e) {
            console.error('QR generation error:', e);
            qrContainer.innerHTML = `
              <div class="flex items-center justify-center h-100">
                <span class="text-2xl font-bold">${response.data.code}</span>
              </div>
            `;
          }
        } else {
          qrContainer.innerHTML = `
            <div class="flex items-center justify-center h-100">
              <span class="text-2xl font-bold">${response.data.code}</span>
            </div>
          `;
        }
        
        Utils.success("New code generated");
      } catch (error) {
        Utils.error("Failed to generate code");
      } finally {
        Utils.setButtonLoading("#generateNewCodeBtn", false);
      }
    }
    
    function printQRCode() {
      const printWindow = window.open('', '', 'height=400,width=600');
      const qrElement = Utils.$("#qrCode");
      const code = Utils.$("#attendanceCode").textContent;
      
      printWindow.document.write(`
        <html>
          <head>
            <title>Print QR Code - ${code}</title>
            <style>
              body { font-family: Arial, sans-serif; text-align: center; padding: 40px; }
              .qr-container { margin: 30px auto; }
              .qr-code { margin: 20px 0; font-size: 48px; font-weight: bold; }
              .code-label { font-size: 14px; color: #666; margin-top: 20px; }
            </style>
          </head>
          <body>
            <h2>Event Attendance QR Code</h2>
            <div class="qr-container">
              <div class="qr-code">${code}</div>
              <div class="code-label">Use this code for attendance verification</div>
            </div>
          </body>
        </html>
      `);
      
      printWindow.document.close();
      printWindow.print();
    }

    window.markAttended = async (attendanceId) => {
      try {
        await API.events.markAttended(currentEventId, attendanceId);
        Utils.success("Marked as attended");
        loadAttendanceData(currentEventId);
      } catch (error) {
        Utils.error("Failed to update attendance");
      }
    };
  })();
</script>

