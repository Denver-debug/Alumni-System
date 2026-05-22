<!-- Alumni Events Page -->
<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content">
    <header class="topbar">
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
      <h1 class="page-title">Events</h1>
    </header>

    <div class="content-wrapper p-lg">
      <!-- Event Filters -->
      <div class="card mb-lg">
        <div class="card-body">
          <div class="flex flex-wrap items-center gap-md">
            <div class="flex-1" style="min-width: 200px">
              <input
                type="text"
                id="searchEvents"
                class="form-input"
                placeholder="Search events..."
              />
            </div>
            <select id="statusFilter" class="form-select" style="width: auto">
              <option value="">All Status</option>
              <option value="upcoming" selected>Upcoming</option>
              <option value="ongoing">Ongoing</option>
              <option value="completed">Completed</option>
            </select>
            <select id="typeFilter" class="form-select" style="width: auto">
              <option value="">All Types</option>
              <option value="seminar">Seminar</option>
              <option value="reunion">Reunion</option>
              <option value="workshop">Workshop</option>
              <option value="networking">Networking</option>
              <option value="career_fair">Career Fair</option>
              <option value="webinar">Webinar</option>
              <option value="other">Other</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Event Stats -->
      <div class="grid grid-cols-4 gap-md mb-lg">
        <div class="card">
          <div class="card-body text-center">
            <div class="text-2xl font-bold text-primary" id="upcomingCount">
              0
            </div>
            <div class="text-sm text-secondary">Upcoming</div>
          </div>
        </div>
        <div class="card">
          <div class="card-body text-center">
            <div class="text-2xl font-bold text-success" id="registeredCount">
              0
            </div>
            <div class="text-sm text-secondary">Registered</div>
          </div>
        </div>
        <div class="card">
          <div class="card-body text-center">
            <div class="text-2xl font-bold text-warning" id="attendedCount">
              0
            </div>
            <div class="text-sm text-secondary">Attended</div>
          </div>
        </div>
        <div class="card">
          <div class="card-body text-center">
            <div class="text-2xl font-bold text-info" id="pointsEarned">0</div>
            <div class="text-sm text-secondary">Points Earned</div>
          </div>
        </div>
      </div>

      <!-- Events Grid -->
      <div id="eventsGrid" class="grid grid-cols-3 gap-lg">
        <div class="loading-skeleton">Loading events...</div>
      </div>

      <!-- Pagination -->
      <div id="pagination" class="flex justify-center gap-sm mt-lg"></div>
    </div>
  </main>
</div>

<!-- Event Detail Modal -->
<div class="modal" id="eventDetailModal">
  <div class="modal-backdrop"></div>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="eventTitle">Event Details</h3>
        <button class="modal-close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="eventDetailBody">
        <!-- Will be populated dynamically -->
      </div>
      <div class="modal-footer" id="eventActions">
        <button class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Attendance Modal -->
<div class="modal" id="attendanceModal">
  <div class="modal-backdrop"></div>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Check-in to Event</h3>
        <button class="modal-close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="tabs mb-lg">
          <button class="tab active" data-method="code">Enter Code</button>
          <button class="tab" data-method="qr">Scan QR</button>
        </div>

        <!-- Code Entry -->
        <div id="codeEntry">
          <p class="text-secondary mb-md">
            Enter the attendance code provided at the event:
          </p>
          <input
            type="text"
            id="attendanceCode"
            class="form-input text-center"
            placeholder="Enter event code"
            maxlength="20"
            style="font-size: 1.5rem; letter-spacing: 0.2rem"
          />
        </div>

        <!-- QR Scanner -->
        <div id="qrScanner" class="hidden">
          <p class="text-secondary mb-md">
            Position the QR code in the camera view:
          </p>
          <div
            id="qrVideo"
            style="
              width: 100%;
              aspect-ratio: 1;
              background: #000;
              border-radius: var(--radius-md);
            "
          ></div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button class="btn btn-primary" id="submitAttendance">Check In</button>
      </div>
    </div>
  </div>
</div>

<style>
  .event-card {
    transition:
      transform 0.2s,
      box-shadow 0.2s;
  }
  .event-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
  }
  .event-image {
    height: 160px;
    background: linear-gradient(135deg, var(--primary-100), var(--primary-200));
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-lg) var(--radius-lg) 0 0;
    overflow: hidden;
  }
  .event-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  .event-date-badge {
    background: white;
    border-radius: var(--radius-md);
    padding: var(--spacing-sm);
    text-align: center;
    box-shadow: var(--shadow-md);
    min-width: 60px;
  }
  .event-date-badge .day {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-600);
  }
  .event-date-badge .month {
    font-size: 0.75rem;
    text-transform: uppercase;
    color: var(--gray-500);
  }
</style>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
  (function () {
    let events = [];
    let myRegistrations = [];
    let myAttendance = {};
    let currentPage = 1;
    const perPage = 9;
    let qrScanner = null;
    let qrScannerRunning = false;
    let qrProcessing = false;
    let qrLibraryPromise = null;

    function getFirstValue(source, keys) {
      if (!source || typeof source !== "object") return "";
      for (const key of keys) {
        if (source[key] !== undefined && source[key] !== null && String(source[key]).trim() !== "") {
          return source[key];
        }
      }
      return "";
    }

    function parseAttendanceInput(rawValue) {
      const raw = String(rawValue || "").trim();
      let payload = null;
      let code = "";
      let eventId = "";

      if (!raw) return { code: "", eventId: "" };

      try {
        payload = JSON.parse(raw);
      } catch (error) {
        payload = null;
      }

      if (payload && typeof payload === "object") {
        const source = payload.data && typeof payload.data === "object"
          ? { ...payload.data, ...payload }
          : payload;
        code = getFirstValue(source, ["code", "attendance_code", "attendanceCode", "event_code", "eventCode"]);
        eventId = getFirstValue(source, ["event_id", "eventId"]);
      } else {
        try {
          const parsedUrl = new URL(raw, window.location.origin);
          code =
            parsedUrl.searchParams.get("code") ||
            parsedUrl.searchParams.get("attendance_code") ||
            parsedUrl.searchParams.get("event_code") ||
            "";
          eventId =
            parsedUrl.searchParams.get("event_id") ||
            parsedUrl.searchParams.get("eventId") ||
            "";
        } catch (error) {
          code = raw;
        }
      }

      const normalizedCode = payload && typeof payload === "object" ? code : (code || raw);

      return {
        code: String(normalizedCode || "").trim().toUpperCase(),
        eventId: String(eventId || "").trim(),
      };
    }

    function ensureQRLibrary() {
      if (typeof Html5Qrcode !== "undefined") {
        return Promise.resolve();
      }

      if (qrLibraryPromise) {
        return qrLibraryPromise;
      }

      qrLibraryPromise = new Promise((resolve, reject) => {
        const timeoutId = setTimeout(() => {
          reject(new Error("QR scanner library failed to load"));
        }, 7000);
        const finish = () => {
          clearTimeout(timeoutId);
          resolve();
        };
        const existingScript = document.querySelector('script[src*="html5-qrcode"]');
        if (existingScript) {
          existingScript.addEventListener("load", finish, { once: true });
          existingScript.addEventListener("error", reject, { once: true });
          setTimeout(() => {
            if (typeof Html5Qrcode !== "undefined") finish();
          }, 100);
          return;
        }

        const script = document.createElement("script");
        script.src = "https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js";
        script.async = true;
        script.onload = finish;
        script.onerror = reject;
        document.head.appendChild(script);
      });

      return qrLibraryPromise;
    }

    // Load events on page load
    loadEvents();
    loadMyStats();

    // Filters
    Utils.$("#searchEvents").addEventListener(
      "input",
      Utils.debounce(loadEvents, 300),
    );
    Utils.$("#statusFilter").addEventListener("change", loadEvents);
    Utils.$("#typeFilter").addEventListener("change", loadEvents);

    // Attendance modal tabs
    Utils.$$("#attendanceModal .tab").forEach((tab) => {
      tab.addEventListener("click", () => {
        Utils.$$("#attendanceModal .tab").forEach((t) =>
          t.classList.remove("active"),
        );
        tab.classList.add("active");

        if (tab.dataset.method === "qr") {
          Utils.$("#codeEntry").classList.add("hidden");
          Utils.$("#qrScanner").classList.remove("hidden");
          startQRScanner();
        } else {
          Utils.$("#qrScanner").classList.add("hidden");
          Utils.$("#codeEntry").classList.remove("hidden");
          stopQRScanner();
        }
      });
    });

    async function loadEvents() {
      const search = Utils.$("#searchEvents").value;
      const status = Utils.$("#statusFilter").value;
      const type = Utils.$("#typeFilter").value;
      const grid = Utils.$("#eventsGrid");

      // Show loading skeleton
      grid.innerHTML = Components.loadingSkeleton({ type: 'card' }).repeat(3);

      try {
        const response = await API.events.list({
          search,
          status,
          event_type: type,
          page: currentPage,
          limit: perPage,
        });

        const payload = response?.data || {};
        events = Array.isArray(payload?.events)
          ? payload.events
          : Array.isArray(payload)
            ? payload
            : [];
        myRegistrations = Array.isArray(payload?.my_registrations)
          ? payload.my_registrations.map((id) => Number(id))
          : [];
        myAttendance = payload?.my_attendance || {};

        renderEvents();
        renderPagination(Number(payload?.total || 0));
      } catch (error) {
        console.error("Load events error:", error);
        grid.innerHTML = Components.errorState({
          title: 'Failed to load events',
          message: error.message || 'Please try again later',
          retryHandler: () => loadEvents()
        });
      }
    }

    async function loadMyStats() {
      try {
        const response = await API.events.getMyStats();
        Utils.$("#upcomingCount").textContent = response.data.upcoming || 0;
        Utils.$("#registeredCount").textContent = response.data.registered || 0;
        Utils.$("#attendedCount").textContent = response.data.attended || 0;
        Utils.$("#pointsEarned").textContent = response.data.points_earned || 0;
      } catch (error) {
        console.error("Load stats error:", error);
      }
    }

    function renderEvents() {
      const grid = Utils.$("#eventsGrid");

      if (!events.length) {
        grid.innerHTML = `
                <div class="col-span-3">
                    ${Components.emptyState({
                      icon: '📅',
                      title: 'No events found',
                      message: 'Check back soon for upcoming events and activities',
                      actionText: 'Clear Filters',
                      actionHandler: () => {
                        Utils.$("#searchEvents").value = '';
                        Utils.$("#statusFilter").value = 'upcoming';
                        Utils.$("#typeFilter").value = '';
                        loadEvents();
                      }
                    })}
                </div>
            `;
        return;
      }

      grid.innerHTML = events
        .map((event) => {
          const eventId = Number(event.id);
          const isRegistered = myRegistrations.includes(eventId);
          const hasAttended = myAttendance[eventId]?.attended || false;
          const date = new Date(event.event_date);
          const eventImage = API.resolveAssetUrl(event.cover_image || event.image || "");
          const eventLocation = event.location || event.venue || "TBA";
          const eventTime = event.event_time || "Time TBA";

          return `
                <div class="card event-card cursor-pointer" onclick="showEventDetail(${eventId})">
                    <div class="event-image">
                        ${
                          eventImage
                            ? `<img src="${Utils.escapeHtml(eventImage)}" alt="${Utils.escapeHtml(event.title)}" onerror="this.onerror=null; this.src='/assets/images/event-placeholder.svg';">`
                            : `
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--primary-400)" stroke-width="1.5">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                                <line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        `
                        }
                    </div>
                    <div class="card-body">
                        <div class="flex gap-md mb-md">
                            <div class="event-date-badge">
                                <div class="day">${date.getDate()}</div>
                                <div class="month">${date.toLocaleString("default", { month: "short" })}</div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold mb-xs line-clamp-1">${Utils.escapeHtml(event.title)}</h4>
                              <div class="text-sm text-secondary">${eventTime} · ${Utils.escapeHtml(eventLocation)}</div>
                            </div>
                        </div>
                        <p class="text-sm text-secondary line-clamp-2 mb-md">${Utils.escapeHtml(event.description || "")}</p>
                        <div class="flex justify-between items-center">
                            <span class="badge badge-${getEventTypeBadge(event.event_type)}">${event.event_type}</span>
                            <span class="text-sm font-medium text-primary">+${event.points_reward} pts</span>
                        </div>
                        ${hasAttended ? '<div class="badge badge-success mt-sm">✓ Attended</div>' : isRegistered ? '<div class="badge badge-info mt-sm">Registered</div>' : ""}
                    </div>
                </div>
            `;
        })
        .join("");
    }

    function renderPagination(total) {
      const totalPages = Math.ceil(total / perPage);
      const container = Utils.$("#pagination");

      if (totalPages <= 1) {
        container.innerHTML = "";
        return;
      }

      let html = "";
      for (let i = 1; i <= totalPages; i++) {
        html += `<button class="btn ${i === currentPage ? "btn-primary" : "btn-secondary"} btn-sm" onclick="goToPage(${i})">${i}</button>`;
      }
      container.innerHTML = html;
    }

    function getEventTypeBadge(type) {
      const badges = {
        seminar: "primary",
        reunion: "success",
        workshop: "warning",
        networking: "info",
        career_fair: "danger",
        webinar: "secondary",
        other: "default",
      };
      return badges[type] || "default";
    }

    // Global functions
    window.showEventDetail = async (eventId) => {
      try {
        const response = await API.events.get(eventId);
        const event = response.data;
        const isRegistered = myRegistrations.includes(eventId);
        const hasAttended = myAttendance[eventId]?.attended || false;
        const attendanceInfo = myAttendance[eventId] || {};
        const isPast = new Date(event.event_date) < new Date();

        Utils.$("#eventTitle").textContent = event.title;
        Utils.$("#eventDetailBody").innerHTML = `
                <div class="mb-lg">
                    ${(event.cover_image || event.image) ? `<img src="${Utils.escapeHtml(API.resolveAssetUrl(event.cover_image || event.image))}" alt="" class="w-100 mb-lg" style="border-radius: var(--radius-md); max-height: 300px; object-fit: cover;" onerror="this.onerror=null; this.src='/assets/images/event-placeholder.svg';">` : ""}
                    
                    ${hasAttended ? `
                    <div class="alert alert-success mb-lg">
                        <strong>✓ You attended this event!</strong><br>
                        Check-in time: ${Utils.formatDateTime(attendanceInfo.check_in_time)}<br>
                        Points earned: ${attendanceInfo.points_awarded || 0}
                    </div>
                    ` : ""}
                    
                    <div class="grid grid-cols-2 gap-lg mb-lg">
                        <div>
                            <div class="text-sm text-secondary">Date & Time</div>
                            <div class="font-medium">${Utils.formatDate(event.event_date)} at ${event.event_time}</div>
                        </div>
                        <div>
                            <div class="text-sm text-secondary">Location</div>
                            <div class="font-medium">${Utils.escapeHtml(event.location || "TBA")}</div>
                        </div>
                        <div>
                            <div class="text-sm text-secondary">Type</div>
                            <div class="font-medium">${event.event_type}</div>
                        </div>
                        <div>
                            <div class="text-sm text-secondary">Points Reward</div>
                            <div class="font-medium text-primary">${event.points_reward} points</div>
                        </div>
                        <div>
                            <div class="text-sm text-secondary">Attendees</div>
                            <div class="font-medium">${event.registered_count || 0}${event.max_attendees ? ` / ${event.max_attendees}` : ""}</div>
                        </div>
                        <div>
                            <div class="text-sm text-secondary">Status</div>
                            <span class="badge badge-${event.status === "upcoming" ? "primary" : event.status === "ongoing" ? "success" : "secondary"}">${event.status}</span>
                        </div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-secondary mb-sm">Description</div>
                        <p>${Utils.escapeHtml(event.description || "No description available.")}</p>
                    </div>
                </div>
            `;

        // Action buttons
        let actions =
          '<button class="btn btn-secondary" data-dismiss="modal">Close</button>';

        if (hasAttended) {
          // Already attended - no action needed
          actions += '<button class="btn btn-success" disabled>✓ Attended</button>';
        } else if (!isPast && !isRegistered && event.status === "upcoming") {
          actions += `<button class="btn btn-primary" onclick="registerForEvent(${eventId})">Register for Event</button>`;
        } else if (
          isRegistered &&
          (event.status === "upcoming" || event.status === "ongoing")
        ) {
          actions += `<button class="btn btn-warning" onclick="openAttendanceModal(${eventId})">Check In</button>`;
          if (event.status === "upcoming") {
            actions += `<button class="btn btn-danger" onclick="cancelRegistration(${eventId})">Cancel Registration</button>`;
          }
        }

        Utils.$("#eventActions").innerHTML = actions;
        Utils.openModal("#eventDetailModal");
      } catch (error) {
        Utils.error("Failed to load event details");
      }
    };

    window.registerForEvent = async (eventId) => {
      try {
        await API.events.register(eventId);
        Utils.success("Successfully registered for the event!");
        const normalizedId = Number(eventId);
        if (!myRegistrations.includes(normalizedId)) {
          myRegistrations.push(normalizedId);
        }
        Utils.closeModal("#eventDetailModal");
        loadEvents();
        loadMyStats();
      } catch (error) {
        Utils.error(error.message || "Failed to register");
      }
    };

    window.cancelRegistration = async (eventId) => {
      if (!confirm("Are you sure you want to cancel your registration?"))
        return;

      try {
        await API.events.cancelRegistration(eventId);
        Utils.success("Registration cancelled");
        const normalizedId = Number(eventId);
        myRegistrations = myRegistrations.filter((id) => id !== normalizedId);
        Utils.closeModal("#eventDetailModal");
        loadEvents();
        loadMyStats();
      } catch (error) {
        Utils.error(error.message || "Failed to cancel registration");
      }
    };

    window.openAttendanceModal = (eventId) => {
      Utils.closeModal("#eventDetailModal");
      Utils.$("#attendanceCode").value = "";
      Utils.$("#attendanceModal").dataset.eventId = eventId;
      Utils.$$("#attendanceModal .tab").forEach((tab) => {
        tab.classList.toggle("active", tab.dataset.method === "code");
      });
      Utils.$("#qrScanner").classList.add("hidden");
      Utils.$("#codeEntry").classList.remove("hidden");
      stopQRScanner();
      Utils.openModal("#attendanceModal");
    };

    async function submitAttendanceCode(rawCode, preferredEventId = "") {
      const eventId = Utils.$("#attendanceModal").dataset.eventId;
      const attendance = parseAttendanceInput(rawCode);
      const checkInEventId = preferredEventId || attendance.eventId || eventId;

      if (!checkInEventId) {
        Utils.error("Event is missing. Please reopen the check-in form.");
        return;
      }

      if (!attendance.code || attendance.code.length < 4 || attendance.code.length > 20) {
        Utils.error("Please enter a valid event code");
        return;
      }

      Utils.setButtonLoading("#submitAttendance", true);

      try {
        await API.events.checkIn(checkInEventId, { code: attendance.code });
        Utils.success("Check-in successful! Points awarded.");
        await stopQRScanner();
        Utils.closeModal("#attendanceModal");
        loadMyStats();
      } catch (error) {
        Utils.error(error.message || "Invalid attendance code");
      } finally {
        Utils.setButtonLoading("#submitAttendance", false);
      }
    }

    Utils.$("#submitAttendance").addEventListener("click", async () => {
      await submitAttendanceCode(Utils.$("#attendanceCode").value.trim());
    });

    window.goToPage = (page) => {
      currentPage = page;
      loadEvents();
    };

    async function startQRScanner() {
      if (qrScannerRunning || qrProcessing) {
        return;
      }

      const qrVideo = Utils.$("#qrVideo");
      qrVideo.innerHTML = '<div class="text-center p-xl text-white">Starting camera...</div>';

      try {
        await ensureQRLibrary();
        qrVideo.innerHTML = "";
        qrScanner = new Html5Qrcode("qrVideo");

        await qrScanner.start(
          { facingMode: "environment" },
          {
            fps: 10,
            qrbox: { width: 240, height: 240 },
          },
          async (decodedText) => {
            if (qrProcessing) return;
            qrProcessing = true;
            await stopQRScanner();
            await submitAttendanceCode(decodedText);
            qrProcessing = false;
          },
          () => {}
        );

        qrScannerRunning = true;
      } catch (error) {
        console.error("QR scanner error:", error);
        qrScannerRunning = false;
        qrScanner = null;
        qrVideo.innerHTML =
          '<div class="text-center p-xl text-white">Camera unavailable. Enter the event code instead.</div>';
        Utils.error("Failed to start camera. Please check permissions or use the event code.");
      }
    }

    async function stopQRScanner() {
      if (qrScanner && qrScannerRunning) {
        try {
          await qrScanner.stop();
        } catch (error) {
          console.warn("Unable to stop QR scanner:", error);
        }
      }
      qrScannerRunning = false;
      qrScanner = null;
    }

    Utils.$$("#attendanceModal [data-dismiss='modal'], #attendanceModal .modal-backdrop").forEach((el) => {
      el.addEventListener("click", () => {
        stopQRScanner();
      });
    });

    window.__pageCleanup = async () => {
      await stopQRScanner();
    };
  })();
</script>
