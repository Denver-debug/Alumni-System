<!-- Admin Alumni Detail -->
<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content">
    <div class="content-header">
      <a href="#/admin/alumni" class="btn btn-ghost btn-sm"
        >← Back to Alumni List</a
      >
    </div>

    <div class="content-body">
      <div id="alumniDetail">
        <div class="loading-spinner p-xl">Loading...</div>
      </div>
    </div>
  </main>
</div>

<script>
  (function () {
    const id = Router.getParam("id");
    if (id) loadAlumni(id);

    async function loadAlumni(id) {
      try {
        const response = await API.admin.getAlumniDetail(id);
        const payload = response?.data || {};
        const alumni = payload.alumni || payload;
        const eventAttendances = Array.isArray(payload.event_attendances)
          ? payload.event_attendances
          : [];
        const alumniName = alumni.name || "Unknown";
        const alumniInitial = alumniName.charAt(0).toUpperCase();

        Utils.$("#alumniDetail").innerHTML = `
        <div class="card p-xl mb-lg">
          <div class="flex items-start gap-xl">
            <div class="avatar avatar-xl bg-primary">
              ${alumni.profile_image ? `<img src="${alumni.profile_image}" alt="">` : `<span>${alumniInitial}</span>`}
            </div>
            <div class="flex-1">
              <div class="flex items-center gap-md mb-sm">
                <h1 class="text-2xl font-bold">${Utils.escapeHtml(alumniName)}</h1>
                <span class="badge badge-${alumni.status === "active" ? "success" : "warning"}">${alumni.status || "pending"}</span>
              </div>
              <div class="text-muted mb-md">${alumni.alumni_id || "No Alumni ID"}</div>
              <div class="grid grid-cols-3 gap-lg">
                <div>
                  <div class="text-sm text-muted">Email</div>
                  <div>${Utils.escapeHtml(alumni.email)}</div>
                </div>
                <div>
                  <div class="text-sm text-muted">Campus</div>
                  <div>${Utils.escapeHtml(alumni.campus_name || "-")}</div>
                </div>
                <div>
                  <div class="text-sm text-muted">College</div>
                  <div>${Utils.escapeHtml(alumni.college_name || "-")}</div>
                </div>
                <div>
                  <div class="text-sm text-muted">Section</div>
                  <div>${Utils.escapeHtml(alumni.section_name || "-")}</div>
                </div>
                <div>
                  <div class="text-sm text-muted">Program</div>
                  <div>${Utils.escapeHtml(alumni.program_name || "-")}</div>
                </div>
                <div>
                  <div class="text-sm text-muted">Batch Year</div>
                  <div>${alumni.batch_year || "-"}</div>
                </div>
                <div>
                  <div class="text-sm text-muted">Graduation Year</div>
                  <div>${alumni.graduation_year || "-"}</div>
                </div>
              </div>
            </div>
            <div class="text-right">
              <div class="text-3xl font-bold text-primary">${alumni.total_points || 0}</div>
              <div class="text-sm text-muted">points</div>
              <div class="badge badge-${getBadgeColor(alumni.badge_level)} mt-sm">${alumni.badge_level || "bronze"}</div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-lg mb-lg">
          <div class="card p-lg">
            <h3 class="font-bold mb-md">Personal Information</h3>
            <div class="space-y-sm">
              <div class="flex justify-between">
                <span class="text-muted">Phone</span>
                <span>${Utils.escapeHtml(alumni.phone || "-")}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-muted">Birthday</span>
                <span>${alumni.birthday || alumni.birthdate ? new Date(alumni.birthday || alumni.birthdate).toLocaleDateString() : "-"}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-muted">Address</span>
                <span>${Utils.escapeHtml(alumni.address || "-")}</span>
              </div>
            </div>
          </div>
          
          <div class="card p-lg">
            <h3 class="font-bold mb-md">Employment</h3>
            <div class="space-y-sm">
              <div class="flex justify-between">
                <span class="text-muted">Status</span>
                <span>${Utils.escapeHtml(alumni.employment_status || "-")}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-muted">Company</span>
                <span>${Utils.escapeHtml(alumni.company_name || alumni.company || "-")}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-muted">Position</span>
                <span>${Utils.escapeHtml(alumni.job_title || "-")}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Event Attendance History</h3>
          </div>
          <div id="eventHistory">
            <div class="loading-spinner p-lg">Loading...</div>
          </div>
        </div>

        <div class="flex gap-md mt-lg">
          <button class="btn btn-primary" onclick="updateStatus('${alumni.id}', 'active')">Activate</button>
          <button class="btn btn-warning" onclick="updateStatus('${alumni.id}', 'inactive')">Deactivate</button>
          <button class="btn btn-error" onclick="deleteAlumni('${alumni.id}')">Delete Account</button>
        </div>
      `;

        renderEventHistory(eventAttendances);
      } catch (error) {
        Utils.$("#alumniDetail").innerHTML =
          '<div class="alert alert-error">Alumni not found</div>';
      }
    }

    function renderEventHistory(events) {
      const container = Utils.$("#eventHistory");

      if (!events || events.length === 0) {
        container.innerHTML =
          '<div class="p-lg text-muted text-center">No event attendance yet</div>';
        return;
      }

      container.innerHTML = `
        <table class="table">
          <thead>
            <tr>
              <th>Event</th>
              <th>Date</th>
              <th>Status</th>
              <th>Points</th>
            </tr>
          </thead>
          <tbody>
            ${events
              .map((e) => {
                const attendanceStatus =
                  e.status || (e.check_in_time ? "attended" : "registered");

                return `
              <tr>
                <td>${Utils.escapeHtml(e.event_title || "Event")}</td>
                <td>${e.event_date ? new Date(e.event_date).toLocaleDateString() : "-"}</td>
                <td><span class="badge badge-${attendanceStatus === "attended" ? "success" : "warning"}">${attendanceStatus}</span></td>
                <td>${e.points_awarded || 0}</td>
              </tr>
            `;
              })
              .join("")}
          </tbody>
        </table>
      `;
    }

    function getBadgeColor(badge) {
      const colors = {
        bronze: "secondary",
        silver: "info",
        gold: "warning",
        platinum: "primary",
        diamond: "success",
      };
      return colors[badge] || "secondary";
    }

    window.updateStatus = async function (id, status) {
      try {
        await API.admin.updateAlumniStatus(id, status);
        Utils.success("Status updated");
        loadAlumni(id);
      } catch (error) {
        Utils.error(error.message || "Failed to update");
      }
    };

    window.deleteAlumni = async function (id) {
      if (
        !confirm(
          "Are you sure you want to delete this alumni? This cannot be undone.",
        )
      )
        return;
      try {
        await API.admin.deleteAlumni(id);
        Utils.success("Alumni deleted");
        Router.navigate("/admin/alumni");
      } catch (error) {
        Utils.error(error.message || "Failed to delete");
      }
    };
  })();
</script>
