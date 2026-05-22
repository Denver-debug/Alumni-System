<!-- Admin Event Form -->
<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content">
    <div class="content-header">
      <a href="#/admin/events" class="btn btn-ghost btn-sm">← Back to Events</a>
      <h1 id="pageTitle">Create Event</h1>
    </div>

    <div class="content-body">
      <form id="eventForm" class="card p-xl">
        <div class="grid grid-cols-2 gap-lg">
          <div class="form-group col-span-2">
            <label class="form-label required">Event Title</label>
            <input type="text" name="title" class="form-input" required />
          </div>

          <div class="form-group col-span-2">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-input" rows="4"></textarea>
          </div>

          <div class="form-group">
            <label class="form-label required">Event Date</label>
            <input type="date" name="event_date" class="form-input" required />
          </div>

          <div class="form-group">
            <label class="form-label">Event Time</label>
            <input type="time" name="event_time" class="form-input" />
          </div>

          <div class="form-group">
            <label class="form-label">Event Type</label>
            <select name="event_type" class="form-input">
              <option value="seminar">Seminar</option>
              <option value="reunion">Reunion</option>
              <option value="workshop">Workshop</option>
              <option value="networking">Networking</option>
              <option value="career_fair">Career Fair</option>
              <option value="other">Other</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Venue Type</label>
            <select
              name="venue_type"
              class="form-input"
              onchange="toggleVenueFields(this.value)"
            >
              <option value="physical">Physical Venue</option>
              <option value="online">Online</option>
              <option value="hybrid">Hybrid</option>
            </select>
          </div>

          <div class="form-group col-span-2">
            <label class="form-label">Campuses</label>
            <select name="campus_ids[]" id="campusSelect" style="display:none" multiple size="5">
              <option value="">All Campuses</option>
            </select>

            <div class="dropdown-checkbox" id="campusDropdown">
              <button type="button" class="btn btn-outline" id="campusDropdownToggle">All Campuses ▾</button>
              <div class="dropdown-checkbox-menu" id="campusDropdownMenu" style="display:none; max-height:220px; overflow:auto; border:1px solid rgba(0,0,0,0.08); background:white; padding:8px; border-radius:6px; position:absolute; z-index:50;">
                <!-- checkbox items populated here -->
              </div>
            </div>

            <div class="text-sm text-secondary mt-xs">
              Leave blank to show this event to all campuses. Select one or more campuses to target specific sites.
            </div>
          </div>

          <div class="form-group" id="locationField">
            <label class="form-label">Location</label>
            <input
              type="text"
              name="location"
              class="form-input"
              placeholder="Venue address"
            />
          </div>

          <div class="form-group" id="onlineLinkField" style="display: none">
            <label class="form-label">Online Link</label>
            <input
              type="url"
              name="online_link"
              class="form-input"
              placeholder="https://zoom.us/..."
            />
          </div>

          <div class="form-group">
            <label class="form-label">Maximum Attendees</label>
            <input
              type="number"
              name="max_attendees"
              class="form-input"
              min="0"
              placeholder="0 = Unlimited"
            />
          </div>

          <div class="form-group">
            <label class="form-label">Registration Deadline</label>
            <input
              type="date"
              name="registration_deadline"
              class="form-input"
            />
          </div>

          <div class="form-group">
            <label class="form-label">Points Reward</label>
            <input
              type="number"
              name="points_reward"
              class="form-input"
              min="0"
              value="20"
            />
          </div>

          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
              <option value="draft">Draft</option>
              <option value="upcoming">Upcoming</option>
              <option value="ongoing">Ongoing</option>
              <option value="completed">Completed</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>

          <div class="form-group col-span-2">
            <label class="form-label">Event Image</label>
            <input
              type="file"
              name="event_image"
              id="eventImageInput"
              class="form-input"
              accept="image/jpeg,image/png,image/gif,image/webp"
            />
            <div class="text-sm text-secondary mt-xs">
              JPEG, PNG, GIF, or WebP. Maximum size: 5MB.
            </div>
            <div id="imagePreview" class="mt-md" style="display: none">
              <img id="previewImage" alt="" style="max-width: 280px; max-height: 180px; border-radius: var(--radius-md); object-fit: cover;" />
              <div id="imageMeta" class="text-sm text-secondary mt-xs"></div>
              <button type="button" class="btn btn-ghost btn-sm mt-sm" id="clearImageBtn">Remove selected image</button>
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
            <div class="text-sm text-secondary mt-xs">
              Optional fallback for existing externally hosted event images.
            </div>
          </div>
        </div>

        <div class="flex gap-md mt-lg">
          <button type="submit" class="btn btn-primary">Save Event</button>
          <a href="#/admin/events" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </main>
</div>

<script>
  (function () {
    const form = Utils.$("#eventForm");
    const eventId = Router.getParam("id");
    const isEdit = !!eventId;
    const imageInput = Utils.$("#eventImageInput");
    const imagePreview = Utils.$("#imagePreview");
    const previewImage = Utils.$("#previewImage");
    const imageMeta = Utils.$("#imageMeta");
    const clearImageBtn = Utils.$("#clearImageBtn");
    const campusSelect = Utils.$("#campusSelect");
    const allowedImageTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
    const maxImageSize = 5 * 1024 * 1024;

    if (isEdit) {
      Utils.$("#pageTitle").textContent = "Edit Event";
      loadCampuses().then(() => loadEvent(eventId));
    } else {
      loadCampuses();
    }

    // Setup real-time validation
    Validation.setupRealtimeValidation(form, Validation.schemas.event);

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

        buildCampusDropdown();

        if (!isEdit && currentUser.campus_id && ["campus_admin", "staff"].includes(currentUser.role)) {
          setSelectedCampuses([String(currentUser.campus_id)]);
        }
      } catch (error) {
        console.error("Failed to load campuses for events:", error);
      }
    }

    function buildCampusDropdown() {
      const menu = Utils.$("#campusDropdownMenu");
      const toggle = Utils.$("#campusDropdownToggle");
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

      // toggle menu visibility
      toggle.onclick = (e) => {
        e.stopPropagation();
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
      };

      // close on outside click
      document.addEventListener('click', (ev) => {
        if (!Utils.$('#campusDropdown')?.contains(ev.target)) {
          menu.style.display = 'none';
        }
      });

      // checkbox handlers
      menu.querySelectorAll('.campus-checkbox').forEach((cb) => {
        cb.addEventListener('change', () => {
          const value = cb.getAttribute('data-value');
          if (!value) {
            // All Campuses toggled
            if (cb.checked) {
              // uncheck others
              menu.querySelectorAll('.campus-checkbox').forEach((other) => {
                if (other !== cb) other.checked = false;
              });
              setSelectedCampuses([]);
            } else {
              // if unchecked and no other checked, keep none selected
              setSelectedCampuses([]);
            }
          } else {
            // uncheck "All Campuses" if selecting specific
            if (cb.checked) {
              const all = menu.querySelector('.campus-checkbox[data-value=""]');
              if (all) all.checked = false;
            }
            // build selected list
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

      // initialize toggle label
      updateCampusToggleLabel();
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

      // sync checkboxes
      const menu = Utils.$("#campusDropdownMenu");
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
      updateCampusToggleLabel();
    }

    function updateCampusToggleLabel() {
      const toggle = Utils.$("#campusDropdownToggle");
      const menu = Utils.$("#campusDropdownMenu");
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

    async function loadEvent(id) {
      try {
        const response = await API.admin.getEvent(id);
        const event = response.data?.event || response.data;

        Object.keys(event).forEach((key) => {
          const input = form.elements[key];
          if (input) {
            input.value = event[key] || "";
          }
        });

        renderImagePreview(event.cover_image || event.image || "", "Current image");
        toggleVenueFields(event.venue_type);
        setSelectedCampuses(event.campus_ids || (event.campus_id ? [event.campus_id] : []));
      } catch (error) {
        Utils.error("Failed to load event");
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

    imageInput.addEventListener("change", () => {
      const file = imageInput.files && imageInput.files[0];

      if (!file) {
        renderImagePreview(form.elements.cover_image.value, "Current image");
        return;
      }

      const validationError = validateImageFile(file);
      if (validationError) {
        Utils.error(validationError);
        imageInput.value = "";
        renderImagePreview(form.elements.cover_image.value, "Current image");
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
    });

    clearImageBtn.addEventListener("click", () => {
      imageInput.value = "";
      renderImagePreview(form.elements.cover_image.value, form.elements.cover_image.value ? "Current image" : "");
    });

    window.toggleVenueFields = function (type) {
      const locationField = Utils.$("#locationField");
      const onlineField = Utils.$("#onlineLinkField");

      if (type === "online") {
        locationField.style.display = "none";
        onlineField.style.display = "block";
      } else if (type === "hybrid") {
        locationField.style.display = "block";
        onlineField.style.display = "block";
      } else {
        locationField.style.display = "block";
        onlineField.style.display = "none";
      }
    };

    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const button = form.querySelector('button[type="submit"]');
      
      const success = await Validation.validateAndSubmit(
        form,
        Validation.schemas.event,
        async () => {
          Utils.setButtonLoading(button, true);
          try {
            const multipartData = new FormData(form);
            const file = imageInput.files && imageInput.files[0];

            if (!file) {
              multipartData.delete("event_image");
            }

            const selectedCampusIds = Array.from(campusSelect.selectedOptions)
              .map((option) => option.value)
              .filter(Boolean);

            if (!selectedCampusIds.length) {
              multipartData.set("campus_ids[]", "");
            }

            if (isEdit) {
              await API.admin.updateEvent(eventId, multipartData);
              Utils.success("Event updated successfully!");
            } else {
              await API.admin.createEvent(multipartData);
              Utils.success("Event created successfully!");
            }
            Router.navigate("/admin/events");
          } finally {
            Utils.setButtonLoading(button, false);
          }
        }
      );
    });
  })();
</script>
