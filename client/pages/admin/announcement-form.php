<!-- Admin Announcement Form -->
<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content">
    <div class="content-header">
      <a href="#/admin/announcements" class="btn btn-ghost btn-sm"
        >← Back to Announcements</a
      >
      <h1 id="pageTitle">Create Announcement</h1>
    </div>

    <div class="content-body">
      <form id="announcementForm" class="card p-xl announcement-form-shell">
        <div class="announcement-form-intro">
          <div>
            <h2 class="text-2xl font-bold mb-xs">Announcement Details</h2>
            <p class="text-secondary">
              Choose the campus, audience, and publishing window before you publish.
            </p>
          </div>
          <div class="announcement-form-pill-row">
            <span class="badge badge-secondary">Campus scoped</span>
            <span class="badge badge-secondary">Targeted delivery</span>
            <span class="badge badge-secondary">Cover image supported</span>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-lg">
          <div class="form-group col-span-2">
            <label class="form-label required">Title</label>
            <input type="text" name="title" class="form-input" required placeholder="e.g., Alumni homecoming update" />
          </div>

          <div class="form-group col-span-2">
            <label class="form-label">Content</label>
            <textarea
              name="content"
              class="form-input"
              rows="9"
              placeholder="Write the announcement details, links, and next steps here..."
            ></textarea>
          </div>

          <div class="form-group">
            <label class="form-label">Campuses</label>
            <select name="campus_ids[]" id="campusSelect" style="display:none" multiple size="5">
              <option value="">All Campuses</option>
            </select>

            <div class="dropdown-checkbox" id="campusDropdown">
              <button type="button" class="btn btn-outline" id="campusDropdownToggle">All Campuses ▾</button>
              <div class="dropdown-checkbox-menu" id="campusDropdownMenu" style="display:none; max-height:220px; overflow:auto; border:1px solid rgba(0,0,0,0.08); background:white; padding:8px; border-radius:6px; position:absolute; z-index:50;">
              </div>
            </div>

            <div class="text-xs text-secondary mt-xs">
              Leave blank to show this announcement to all campuses. Select one or more campuses to target specific sites.
            </div>
          </div>

          <div class="form-group">
            <label class="form-label">Target Audience</label>
            <select
              name="target_type"
              class="form-input"
              onchange="toggleTargetSelect(this.value)"
            >
              <option value="all">All Alumni</option>
              <option value="college">Specific College</option>
              <option value="program">Specific Program</option>
              <option value="section">Specific Section</option>
            </select>
          </div>

          <div class="form-group" id="targetSelectField" style="display: none">
            <label class="form-label">Select Target</label>
            <select name="target_id" id="targetSelect" class="form-input">
              <option value="">Select...</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Priority</label>
            <select name="priority" class="form-input">
              <option value="normal">Normal</option>
              <option value="high">Important</option>
              <option value="urgent">Urgent</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Status</label>
            <select name="status" class="form-input">
              <option value="draft">Draft</option>
              <option value="published">Published</option>
              <option value="archived">Archived</option>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label">Publish Date</label>
            <input type="date" name="publish_date" class="form-input" />
          </div>

          <div class="form-group">
            <label class="form-label">Expire Date</label>
            <input type="date" name="expire_date" class="form-input" />
          </div>

          <div class="form-group col-span-2">
            <label class="form-label">Cover Image</label>
            <input
              type="file"
              name="cover_image_file"
              id="coverImageInput"
              class="form-input"
              accept="image/jpeg,image/png,image/gif,image/webp"
            />
            <div class="text-sm text-secondary mt-xs">
              JPEG, PNG, GIF, or WebP. Maximum size: 5MB.
            </div>
            <input type="hidden" name="cover_image" id="coverImageValue" />
            <div id="coverImagePreview" class="mt-md" style="display: none">
              <img id="coverPreviewImage" alt="" style="max-width: 280px; max-height: 180px; border-radius: var(--radius-md); object-fit: cover;" />
              <div id="coverImageMeta" class="text-sm text-secondary mt-xs"></div>
              <button type="button" class="btn btn-ghost btn-sm mt-sm" id="clearCoverImageBtn">Remove selected image</button>
            </div>
          </div>
        </div>

        <div class="flex gap-md mt-lg">
          <button type="submit" class="btn btn-primary">
            Save Announcement
          </button>
          <a href="#/admin/announcements" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </main>
</div>

<style>
  .announcement-form-shell {
    border: 1px solid rgba(15, 23, 42, 0.08);
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
  }
  .announcement-form-intro {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(15, 23, 42, 0.08);
  }
  .announcement-form-pill-row {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
  }
</style>

<script>
  (function () {
    const form = Utils.$("#announcementForm");
    const announcementId = Router.getParam("id");
    const isEdit = !!announcementId;
    const coverImageInput = Utils.$("#coverImageInput");
    const coverImageValue = Utils.$("#coverImageValue");
    const coverImagePreview = Utils.$("#coverImagePreview");
    const coverPreviewImage = Utils.$("#coverPreviewImage");
    const coverImageMeta = Utils.$("#coverImageMeta");
    const clearCoverImageBtn = Utils.$("#clearCoverImageBtn");
    const campusSelect = Utils.$("#campusSelect");
    const allowedImageTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
    const maxImageSize = 5 * 1024 * 1024;

    // Set default publish date to today
    form.elements.publish_date.value = new Date().toISOString().split("T")[0];

    if (isEdit) {
      Utils.$("#pageTitle").textContent = "Edit Announcement";
      loadCampuses().then(() => loadAnnouncement(announcementId));
    } else {
      loadCampuses();
    }

    // Setup real-time validation
    Validation.setupRealtimeValidation(form, Validation.schemas.announcement);

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
        console.error("Failed to load campuses for announcements:", error);
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

      // sync dropdown checkboxes and label
      const menu = Utils.$('#campusDropdownMenu');
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

      toggle.onclick = (e) => {
        e.stopPropagation();
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
      };

      document.addEventListener('click', (ev) => {
        if (!Utils.$('#campusDropdown')?.contains(ev.target)) {
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

    async function loadAnnouncement(id) {
      try {
        const response = await API.admin.getAnnouncement(id);
        const announcement = response?.data?.announcement || response?.data;

        Object.keys(announcement).forEach((key) => {
          const input = form.elements[key];
          if (input) {
            if (input.type === "file") {
              return;
            }
            if (key === "publish_date" || key === "expire_date") {
              input.value = announcement[key]
                ? announcement[key].split("T")[0]
                : "";
            } else {
              input.value = announcement[key] || "";
            }
          }
        });
        coverImageValue.value = announcement.cover_image || "";
        renderCoverImagePreview(announcement.cover_image || "", "Current image");

        setSelectedCampuses(announcement.campus_ids || (announcement.campus_id ? [announcement.campus_id] : []));

        if (announcement.target_type !== "all") {
          toggleTargetSelect(announcement.target_type);
          if (announcement.target_id) {
            setTimeout(() => {
              Utils.$("#targetSelect").value = String(announcement.target_id);
            }, 100);
          }
        }
      } catch (error) {
        Utils.error("Failed to load announcement");
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
        return `File size exceeds 5MB. Your file: ${formatFileSize(file.size)}.`;
      }

      return "";
    }

    function renderCoverImagePreview(src, metaText = "") {
      const resolvedSrc = src && typeof API !== "undefined" && API.resolveAssetUrl
        ? API.resolveAssetUrl(src)
        : src;

      if (!resolvedSrc) {
        coverImagePreview.style.display = "none";
        coverPreviewImage.removeAttribute("src");
        coverImageMeta.textContent = "";
        return;
      }

      coverPreviewImage.src = resolvedSrc;
      coverImageMeta.textContent = metaText;
      coverImagePreview.style.display = "block";
    }

    coverImageInput.addEventListener("change", () => {
      const file = coverImageInput.files && coverImageInput.files[0];

      if (!file) {
        renderCoverImagePreview(coverImageValue.value, coverImageValue.value ? "Current image" : "");
        return;
      }

      const validationError = validateImageFile(file);
      if (validationError) {
        Utils.error(validationError);
        coverImageInput.value = "";
        renderCoverImagePreview(coverImageValue.value, coverImageValue.value ? "Current image" : "");
        return;
      }

      const reader = new FileReader();
      reader.onload = (event) => {
        renderCoverImagePreview(event.target.result, `${file.name} - ${formatFileSize(file.size)}`);
      };
      reader.readAsDataURL(file);
    });

    clearCoverImageBtn.addEventListener("click", () => {
      coverImageInput.value = "";
      renderCoverImagePreview(coverImageValue.value, coverImageValue.value ? "Current image" : "");
    });

    window.toggleTargetSelect = async function (type) {
      const field = Utils.$("#targetSelectField");
      const select = Utils.$("#targetSelect");

      if (type === "all") {
        field.style.display = "none";
        return;
      }

      field.style.display = "block";
      select.innerHTML = '<option value="">Loading...</option>';

      try {
        let response;
        switch (type) {
          case "college":
            response = await API.organization.getColleges();
            break;
          case "program":
            response = await API.organization.getPrograms();
            break;
          case "section":
            response = await API.organization.getSections();
            break;
          default:
            return;
        }

        select.innerHTML = '<option value="">Select...</option>';
        response.data.forEach((item) => {
          select.innerHTML += `<option value="${item.id}">${Utils.escapeHtml(item.name)}</option>`;
        });
      } catch (error) {
        select.innerHTML = '<option value="">Failed to load</option>';
      }
    };

    form.addEventListener("submit", async (e) => {
      e.preventDefault();

      const button = form.querySelector('button[type="submit"]');
      
      const success = await Validation.validateAndSubmit(
        form,
        Validation.schemas.announcement,
        async (formData) => {
          Utils.setButtonLoading(button, true);
          
          // Clear target_id if targeting all
          if (formData.target_type === "all") {
            formData.target_id = null;
          }

          try {
            const multipartData = new FormData(form);
            const file = coverImageInput.files && coverImageInput.files[0];

            if (!file) {
              multipartData.delete("cover_image_file");
            }

            if (formData.target_type === "all") {
              multipartData.set("target_id", "");
            }

            const selectedCampusIds = Array.from(campusSelect.selectedOptions)
              .map((option) => option.value)
              .filter(Boolean);

            if (!selectedCampusIds.length) {
              multipartData.set("campus_ids[]", "");
            }

            if (isEdit) {
              await API.admin.updateAnnouncement(announcementId, multipartData);
              Utils.success("Announcement updated successfully!");
            } else {
              await API.admin.createAnnouncement(multipartData);
              Utils.success("Announcement created successfully!");
            }
            Router.navigate("/admin/announcements");
          } finally {
            Utils.setButtonLoading(button, false);
          }
        }
      );
    });
  })();
</script>
