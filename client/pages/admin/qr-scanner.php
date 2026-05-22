<!-- Admin - QR Code Scanner for Event Check-in -->
<link rel="stylesheet" href="/assets/css/admin-pages-improved.css">
<link rel="stylesheet" href="/assets/css/admin-premium.css">

<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content">
    <header class="admin-topbar">
      <button class="btn btn-ghost sidebar-toggle" id="sidebarToggle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="3" y1="12" x2="21" y2="12" />
          <line x1="3" y1="6" x2="21" y2="6" />
          <line x1="3" y1="18" x2="21" y2="18" />
        </svg>
      </button>
      <h1 class="page-title">QR Code Scanner</h1>
    </header>

    <div class="admin-content p-lg scanner-page">
      <!-- Event Selection -->
      <div class="card-improved mb-lg">
        <div class="card-header">
          <h3 class="card-title">Select Event</h3>
        </div>
        <div class="card-body">
          <!-- Search and Filter Controls -->
          <div class="grid grid-cols-3 gap-md mb-md">
            <div class="form-group" style="margin: 0;">
              <label class="form-label">Search Events</label>
              <input 
                type="text" 
                id="eventSearch" 
                class="form-input" 
                placeholder="Search by title or location..."
              >
            </div>
            <div class="form-group" style="margin: 0;">
              <label class="form-label">Sort By</label>
              <select id="eventSort" class="form-select">
                <option value="upcoming">Upcoming First</option>
                <option value="date">Date (Earliest)</option>
                <option value="date-desc">Date (Latest)</option>
                <option value="title">Title (A-Z)</option>
                <option value="title-desc">Title (Z-A)</option>
              </select>
            </div>
            <div class="form-group" style="margin: 0;">
              <label class="form-label">Event Status</label>
              <select id="eventFilter" class="form-select">
                <option value="all">All Events</option>
                <option value="upcoming,ongoing">Upcoming & Ongoing</option>
                <option value="upcoming">Upcoming Only</option>
                <option value="ongoing">Ongoing Now</option>
                <option value="past">Past Events</option>
              </select>
            </div>
          </div>

          <!-- Event Dropdown -->
          <div class="form-group" style="margin-bottom: 0.5rem;">
            <label class="form-label">Event</label>
            <select id="eventSelect" class="form-select">
              <option value="">Select an event...</option>
            </select>
            <small class="text-secondary mt-xs" id="eventCount" style="display: block; margin-top: 0.5rem;">0 events available</small>
          </div>

          <!-- Event List View -->
          <div class="events-list-block mt-md">
            <button
              type="button"
              class="events-list-toggle"
              id="toggleEventsList"
              aria-expanded="false"
              aria-controls="eventsListPanel"
            >
              <span>View All Events</span>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </button>
            <div class="events-list-panel" id="eventsListPanel" hidden>
              <div id="eventsList" class="events-list">
                <p class="text-secondary text-center py-md">Loading events...</p>
              </div>
            </div>
          </div>

          <div id="eventInfo" class="mt-md" style="display: none;">
            <div class="alert alert-info">
              <strong id="selectedEventTitle"></strong><br>
              <span id="selectedEventDate"></span> • <span id="selectedEventLocation"></span><br>
              <small id="selectedEventStatus" class="text-secondary"></small>
            </div>
          </div>
        </div>
      </div>

      <!-- Scanner Section -->
      <div class="card-improved mb-lg" id="scannerCard" style="display: none;">
        <div class="card-header">
          <h3 class="card-title">Scan Alumni QR Code</h3>
          <button class="btn btn-sm btn-secondary" id="toggleCameraBtn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
              <circle cx="12" cy="13" r="4"></circle>
            </svg>
            Switch Camera
          </button>
        </div>
        <div class="card-body">
          <div class="scanner-container">
            <div id="qrReader"></div>
            <div class="scanner-overlay">
              <div class="scanner-frame"></div>
              <p class="scanner-instruction">Position QR code within the frame</p>
            </div>
          </div>

          <!-- Manual Entry -->
          <div class="mt-lg">
            <details>
              <summary class="cursor-pointer text-primary font-medium" style="padding: 0.5rem 0;">Manual Entry</summary>
              <div class="mt-md">
                <form id="manualEntryForm" class="flex gap-md">
                  <input 
                    type="text" 
                    id="manualQrInput" 
                    class="form-input flex-1" 
                    placeholder="Paste QR code data or Alumni ID"
                  >
                  <button type="submit" class="btn btn-primary">Check In</button>
                </form>
              </div>
            </details>
          </div>
        </div>
      </div>

      <!-- Recent Check-ins -->
      <div class="card-improved" id="recentCheckinsCard" style="display: none;">
        <div class="card-header">
          <h3 class="card-title">Recent Check-ins</h3>
          <span class="badge badge-primary" id="checkinCount">0</span>
        </div>
        <div class="card-body p-0">
          <div style="overflow-x: auto;">
            <table class="table-improved">
              <thead>
                <tr>
                  <th>Time</th>
                  <th>Alumni ID</th>
                  <th>Name</th>
                  <th>Points</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="recentCheckinsBody">
                <!-- Recent check-ins will be populated here -->
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Success Modal -->
<div class="modal" id="successModal">
  <div class="modal-backdrop"></div>
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body text-center p-xl">
        <div class="success-icon mb-md">
          <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
        </div>
        <h2 class="text-2xl font-bold mb-sm">Check-in Successful!</h2>
        <div class="alumni-info mb-md">
          <div class="avatar avatar-xl mb-sm" id="successAvatar">
            <span id="successInitials">A</span>
          </div>
          <h3 class="text-xl font-bold" id="successName">Alumni Name</h3>
          <p class="text-secondary" id="successAlumniId">BBC-2024-CCS-00001</p>
        </div>
        <div class="points-awarded">
          <span class="text-lg">Points Earned:</span>
          <span class="text-3xl font-bold text-primary ml-sm" id="successPoints">+10</span>
        </div>
        <button class="btn btn-primary mt-lg" data-dismiss="modal">Continue Scanning</button>
      </div>
    </div>
  </div>
</div>

<style>
  .scanner-page {
    max-width: 1000px;
    margin: 0 auto;
  }

  .scanner-container {
    position: relative;
    max-width: 500px;
    margin: 0 auto;
    border-radius: var(--radius-lg);
    overflow: hidden;
    background: var(--gray-900);
  }

  #qrReader {
    width: 100%;
    min-height: 400px;
  }

  #qrReader video {
    width: 100% !important;
    height: auto !important;
    display: block !important;
  }

  #qrReader canvas {
    display: none !important;
  }

  .scanner-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    pointer-events: none;
  }

  .scanner-frame {
    width: 250px;
    height: 250px;
    border: 3px solid var(--primary-500);
    border-radius: var(--radius-lg);
    box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5);
    animation: pulse 2s ease-in-out infinite;
  }

  @keyframes pulse {
    0%, 100% {
      border-color: var(--primary-500);
      box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5);
    }
    50% {
      border-color: var(--primary-400);
      box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.6);
    }
  }

  .scanner-instruction {
    position: absolute;
    bottom: 2rem;
    color: white;
    font-weight: 600;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
    background: rgba(0, 0, 0, 0.7);
    padding: 0.5rem 1rem;
    border-radius: var(--radius-full);
  }

  .success-icon {
    display: inline-flex;
    padding: 1rem;
    background: var(--success-100);
    border-radius: 50%;
    color: var(--success-600);
  }

  .alumni-info {
    padding: 1.5rem;
    background: var(--gray-50);
    border-radius: var(--radius-lg);
  }

  .points-awarded {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    background: var(--primary-50);
    border-radius: var(--radius-lg);
  }

  details summary {
    list-style: none;
  }

  details summary::-webkit-details-marker {
    display: none;
  }

  .events-list-block {
    border: 1px solid #dbe6df;
    border-radius: 12px;
    background: #fbfdfc;
    overflow: hidden;
  }

  .events-list-toggle {
    width: 100%;
    border: 0;
    background: transparent;
    color: var(--primary-700);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.9rem 1rem;
    font: inherit;
    font-weight: 700;
    cursor: pointer;
  }

  .events-list-toggle:hover {
    background: #f0faf4;
  }

  .events-list-toggle svg {
    width: 1rem;
    height: 1rem;
    flex: 0 0 auto;
    transition: transform var(--transition-fast);
  }

  .events-list-toggle[aria-expanded="true"] svg {
    transform: rotate(180deg);
  }

  .events-list-panel {
    border-top: 1px solid #dbe6df;
    padding: 0.9rem 1rem 1rem;
  }

  .events-list {
    display: grid;
    gap: 0.75rem;
    max-height: 24rem;
    overflow-y: auto;
    padding-right: 0.25rem;
  }

  .event-card {
    width: 100%;
    border: 1px solid #dbe6df;
    border-radius: 10px;
    padding: 1rem;
    background: #ffffff;
    color: inherit;
    font: inherit;
    text-align: left;
    cursor: pointer;
    transition:
      background-color var(--transition-fast),
      border-color var(--transition-fast);
  }

  .event-card:hover,
  .event-card:focus-visible {
    background: #f7fbf8;
    border-color: #b9d8c4;
    outline: none;
  }

  .event-card svg {
    width: 1rem !important;
    height: 1rem !important;
    max-width: 1rem !important;
    max-height: 1rem !important;
    flex: 0 0 auto;
    vertical-align: -0.15em;
  }

  @media (max-width: 768px) {
    .scanner-frame {
      width: 200px;
      height: 200px;
    }
  }
</style>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
  (async function() {
    let html5QrCode = null;
    let selectedEventId = null;
    let recentCheckins = [];
    let currentCamera = 'environment'; // 'environment' or 'user'
    let allEvents = []; // Store all events for filtering/searching
    let isScannerRunning = false;
    let isProcessing = false;
    let scannerLibraryPromise = null;

    function ensureScannerLibrary() {
      if (typeof Html5Qrcode !== "undefined") {
        return Promise.resolve();
      }

      if (scannerLibraryPromise) {
        return scannerLibraryPromise;
      }

      scannerLibraryPromise = new Promise((resolve, reject) => {
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

      return scannerLibraryPromise;
    }

    async function pauseScanner() {
      if (!html5QrCode || !isScannerRunning || typeof html5QrCode.pause !== "function") {
        return;
      }

      try {
        await html5QrCode.pause();
      } catch (error) {
        console.warn("Unable to pause scanner:", error);
      }
    }

    async function resumeScanner() {
      if (!html5QrCode || !isScannerRunning || typeof html5QrCode.resume !== "function") {
        return;
      }

      try {
        await html5QrCode.resume();
      } catch (error) {
        console.warn("Unable to resume scanner:", error);
      }
    }

    function extractEvents(response) {
      const payload = response?.data || {};
      if (Array.isArray(payload)) return payload;
      if (Array.isArray(payload.events)) return payload.events;
      if (Array.isArray(payload.items)) return payload.items;
      if (Array.isArray(payload.data)) return payload.data;
      if (Array.isArray(payload.data?.events)) return payload.data.events;
      return [];
    }

    function getEventTimeLabel(event) {
      const rawTime = String(event?.event_time || '').trim();
      if (rawTime) {
        return rawTime.slice(0, 5);
      }

      const date = new Date(event?.event_date);
      if (Number.isNaN(date.getTime())) {
        return '';
      }

      return date.toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit',
      });
    }

    function isEventPast(event) {
      const date = new Date(event?.event_date);
      if (Number.isNaN(date.getTime())) {
        return false;
      }

      return (
        event?.status === 'completed' ||
        date.getTime() + 4 * 60 * 60 * 1000 < Date.now()
      );
    }

    function getEventById(eventId) {
      return allEvents.find((event) => String(event.id) === String(eventId));
    }

    function normalizeEventId(eventId) {
      const normalized = Number.parseInt(String(eventId || ''), 10);
      return Number.isFinite(normalized) && normalized > 0 ? normalized : 0;
    }

    function selectEvent(eventId) {
      const select = Utils.$('#eventSelect');
      const normalizedEventId = normalizeEventId(eventId);
      select.value = normalizedEventId ? String(normalizedEventId) : '';
      select.dispatchEvent(new Event('change', { bubbles: true }));
    }

    // Load and filter events
    async function loadEvents() {
      try {
        // Show loading state
        const eventsList = Utils.$('#eventsList');
        eventsList.innerHTML = '<p class="text-secondary text-center py-md">Loading events...</p>';
        
        // Load ALL events, not just upcoming/ongoing
        const response = await API.admin.getEvents({
          limit: 100,
          sort: 'event_date',
          order: 'DESC',
        });
        allEvents = extractEvents(response);
        
        console.log('Loaded events:', allEvents.length);
        
        // Populate dropdown and list
        updateEventDisplay();
        renderEventsList();
      } catch (error) {
        console.error('Failed to load events:', error);
        const eventsList = Utils.$('#eventsList');
        eventsList.innerHTML = '<p class="text-danger text-center py-md">Failed to load events. Please refresh the page.</p>';
        allEvents = [];
        updateEventDisplay();
        renderEventsList();
        Utils.error('Could not load events for QR scanning. Please refresh and select an event again.');
      }
    }

    // Get filtered events based on search and filters
    function getFilteredEvents() {
      let filtered = [...allEvents];
      const searchQuery = Utils.$('#eventSearch').value.toLowerCase();
      const statusFilter = Utils.$('#eventFilter').value;
      const sortBy = Utils.$('#eventSort').value;

      // Apply status filter
      if (statusFilter === 'past') {
        filtered = filtered.filter((event) => isEventPast(event));
      } else if (statusFilter !== 'all') {
        const statuses = statusFilter.split(',');
        filtered = filtered.filter(e => statuses.includes(e.status));
      }

      // Apply search
      if (searchQuery) {
        filtered = filtered.filter(e => 
          (e.title || '').toLowerCase().includes(searchQuery) ||
          (e.location || '').toLowerCase().includes(searchQuery) ||
          (e.description || '').toLowerCase().includes(searchQuery)
        );
      }

      // Apply sort
      filtered.sort((a, b) => {
        const dateA = new Date(a.event_date);
        const dateB = new Date(b.event_date);

        switch (sortBy) {
          case 'date':
            return dateA - dateB;
          case 'date-desc':
            return dateB - dateA;
          case 'title':
            return (a.title || '').localeCompare(b.title || '');
          case 'title-desc':
            return (b.title || '').localeCompare(a.title || '');
          case 'upcoming':
          default:
            // Upcoming events first, sorted by date
            const now = new Date();
            const aIsUpcoming = dateA > now;
            const bIsUpcoming = dateB > now;
            if (aIsUpcoming !== bIsUpcoming) {
              return bIsUpcoming - aIsUpcoming;
            }
            return dateA - dateB;
        }
      });

      return filtered;
    }

    // Update event select dropdown
    function updateEventDisplay() {
      const filtered = getFilteredEvents();
      const select = Utils.$('#eventSelect');
      
      select.innerHTML = '<option value="">Select an event...</option>' +
        filtered.map(e => `
          <option value="${e.id}" 
                  data-title="${Utils.escapeHtml(e.title)}"
                  data-date="${e.event_date}"
                  data-location="${Utils.escapeHtml(e.location || 'TBA')}"
                  data-status="${e.status || 'unknown'}">
            ${Utils.escapeHtml(e.title)} - ${Utils.formatDate(e.event_date)}
          </option>
        `).join('');

      Utils.$('#eventCount').textContent = `${filtered.length} events available`;
    }

    // Render full events list
    function renderEventsList() {
      const filtered = getFilteredEvents();
      const container = Utils.$('#eventsList');

      if (filtered.length === 0) {
        container.innerHTML = '<p class="text-secondary text-center py-md">No events found</p>';
        return;
      }

      container.innerHTML = filtered.map(e => {
        const eventDate = new Date(e.event_date);
        const now = new Date();
        const isOngoing = eventDate <= now;
        const pastEvent = isEventPast(e);
        const statusColor = pastEvent ? 'gray' : (isOngoing ? 'success' : 'info');
        const statusText = pastEvent ? 'Past' : (isOngoing ? 'Ongoing' : 'Upcoming');
        const eventTime = getEventTimeLabel(e);

        return `
          <button type="button" class="event-card text-left" data-event-id="${e.id}">
            <div class="flex justify-between items-start mb-xs">
              <h4 class="font-semibold">${Utils.escapeHtml(e.title)}</h4>
              <span class="badge badge-${statusColor}">${statusText}</span>
            </div>
            <p class="text-sm text-secondary mb-xs">
              <svg class="inline w-4 h-4 mr-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              ${Utils.formatDate(e.event_date)}${eventTime ? ` ${eventTime}` : ''}
            </p>
            <p class="text-sm text-secondary">
              <svg class="inline w-4 h-4 mr-xs" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
              </svg>
              ${Utils.escapeHtml(e.location || 'TBA')}
            </p>
            ${e.description ? `<p class="text-sm text-secondary mt-xs">${Utils.escapeHtml(e.description.substring(0, 100))}...</p>` : ''}
          </button>
        `;
      }).join('');
    }

    Utils.$('#toggleEventsList').addEventListener('click', () => {
      const button = Utils.$('#toggleEventsList');
      const panel = Utils.$('#eventsListPanel');
      const isExpanded = button.getAttribute('aria-expanded') === 'true';
      button.setAttribute('aria-expanded', String(!isExpanded));
      panel.hidden = isExpanded;
    });

    Utils.$('#eventsList').addEventListener('click', (event) => {
      const card = event.target.closest('[data-event-id]');
      if (!card) return;
      selectEvent(card.dataset.eventId);
    });

    // Event listeners for search/filter/sort
    Utils.$('#eventSearch').addEventListener('input', () => {
      updateEventDisplay();
      renderEventsList();
    });

    Utils.$('#eventFilter').addEventListener('change', () => {
      updateEventDisplay();
      renderEventsList();
    });

    Utils.$('#eventSort').addEventListener('change', () => {
      updateEventDisplay();
      renderEventsList();
    });

    // Event selection
    Utils.$('#eventSelect').addEventListener('change', async (e) => {
      const eventId = e.target.value;
      
      if (!eventId) {
        selectedEventId = null;
        Utils.$('#eventInfo').style.display = 'none';
        Utils.$('#scannerCard').style.display = 'none';
        Utils.$('#recentCheckinsCard').style.display = 'none';
        stopScanner();
        return;
      }

      selectedEventId = normalizeEventId(eventId);
      if (!selectedEventId) {
        Utils.error('Please select a valid event');
        return;
      }

      const option = e.target.selectedOptions[0];
      const selectedEvent = getEventById(selectedEventId);
      
      Utils.$('#selectedEventTitle').textContent =
        selectedEvent?.title || option?.dataset?.title || 'Selected Event';
      Utils.$('#selectedEventDate').textContent = Utils.formatDate(
        selectedEvent?.event_date || option?.dataset?.date,
      );
      Utils.$('#selectedEventLocation').textContent =
        selectedEvent?.location || option?.dataset?.location || 'TBA';
      Utils.$('#selectedEventStatus').textContent =
        `Status: ${String(selectedEvent?.status || option?.dataset?.status || 'unknown').toUpperCase()}`;
      Utils.$('#eventInfo').style.display = 'block';
      Utils.$('#scannerCard').style.display = 'block';
      Utils.$('#recentCheckinsCard').style.display = 'block';

      await startScanner();
    });

    // Start QR scanner
    async function startScanner() {
      if (html5QrCode) {
        await stopScanner();
      }

      try {
        await ensureScannerLibrary();
        html5QrCode = new Html5Qrcode("qrReader");
        await html5QrCode.start(
          { facingMode: currentCamera },
          {
            fps: 10,
            qrbox: { width: 250, height: 250 }
          },
          onScanSuccess,
          onScanError
        );
        isScannerRunning = true;
      } catch (error) {
        console.error('Scanner start error:', error);
        isScannerRunning = false;
        html5QrCode = null;
        Utils.error('Failed to start camera. Please check permissions or use manual entry.');
      }
    }

    // Stop scanner
    async function stopScanner() {
      if (html5QrCode && isScannerRunning) {
        try {
          await html5QrCode.stop();
        } catch (error) {
          console.error('Scanner stop error:', error);
        }
      }
      isScannerRunning = false;
      html5QrCode = null;
    }

    window.__pageCleanup = async () => {
      await stopScanner();
    };

    // Toggle camera
    Utils.$('#toggleCameraBtn').addEventListener('click', async () => {
      currentCamera = currentCamera === 'environment' ? 'user' : 'environment';
      if (selectedEventId) {
        await startScanner();
      }
    });

    // Scan success handler
    async function onScanSuccess(decodedText, decodedResult) {
      // Visual feedback
      const scannerFrame = document.querySelector('.scanner-frame');
      if (scannerFrame) {
        scannerFrame.style.borderColor = '#10b981'; // Green
        scannerFrame.style.boxShadow = '0 0 0 9999px rgba(16, 185, 129, 0.3)';
        setTimeout(() => {
          scannerFrame.style.borderColor = '';
          scannerFrame.style.boxShadow = '';
        }, 500);
      }
      
      await processCheckin(decodedText);
    }

    function onScanError(error) {
      // Ignore scan errors (they happen frequently)
      // Could add visual feedback for continuous scanning
    }

    // Process check-in
    async function processCheckin(qrData) {
      if (!selectedEventId) {
        Utils.error('Please select an event first');
        return;
      }

      const eventId = normalizeEventId(selectedEventId);
      if (!eventId || !getEventById(eventId)) {
        Utils.error('Selected event is not available. Please reload events and choose it again.');
        return;
      }

      if (isProcessing) {
        return;
      }

      isProcessing = true;

      try {
        // Pause scanner during processing
        await pauseScanner();

        const response = await API.post('/admin/events/scan-qr', {
          event_id: eventId,
          qr_data: qrData
        });

        const data = response.data || {};
        
        // Show success modal
        showSuccessModal(data);
        
        // Add to recent check-ins
        addRecentCheckin(data);

        // Resume scanner after delay
        setTimeout(async () => {
          await resumeScanner();
          isProcessing = false;
        }, 2000);

      } catch (error) {
        console.error('Check-in error:', error);
        Utils.error(error.message || 'Check-in failed. Please try again.');
        await resumeScanner();
        isProcessing = false;
      }
    }

    // Show success modal
    function showSuccessModal(data) {
      const alumni = data.alumni || {};
      const name = alumni.name || 'Alumni';
      const alumniId = alumni.alumni_id || alumni.student_id || '-';
      const points = Number(data.points_awarded ?? data.points_earned ?? 0);

      Utils.$('#successName').textContent = name;
      Utils.$('#successAlumniId').textContent = alumniId;
      Utils.$('#successPoints').textContent = `+${points}`;
      Utils.$('#successInitials').textContent = Utils.getInitials(name);
      
      Utils.openModal('#successModal');
      
      // Auto-close after 3 seconds
      setTimeout(() => {
        Utils.closeModal('#successModal');
      }, 3000);
    }

    // Add to recent check-ins
    function addRecentCheckin(data) {
      const alumni = data.alumni || {};
      recentCheckins.unshift({
        time: new Date(),
        alumni_id: alumni.alumni_id || alumni.student_id || '-',
        name: alumni.name || 'Alumni',
        points: Number(data.points_awarded ?? data.points_earned ?? 0)
      });

      // Keep only last 10
      if (recentCheckins.length > 10) {
        recentCheckins = recentCheckins.slice(0, 10);
      }

      renderRecentCheckins();
    }

    // Render recent check-ins
    function renderRecentCheckins() {
      const tbody = Utils.$('#recentCheckinsBody');
      Utils.$('#checkinCount').textContent = recentCheckins.length;

      if (recentCheckins.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-secondary">No check-ins yet</td></tr>';
        return;
      }

      tbody.innerHTML = recentCheckins.map(c => `
        <tr>
          <td>${new Date(c.time).toLocaleTimeString()}</td>
          <td>${Utils.escapeHtml(c.alumni_id)}</td>
          <td>${Utils.escapeHtml(c.name)}</td>
          <td><span class="badge badge-success">+${c.points}</span></td>
          <td><span class="badge badge-success">✓ Checked In</span></td>
        </tr>
      `).join('');
    }

    // Manual entry
    Utils.$('#manualEntryForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const input = Utils.$('#manualQrInput').value.trim();
      
      if (!input) {
        Utils.error('Please enter QR data or Alumni ID');
        return;
      }

      // If it looks like an alumni ID, convert to QR format
      let qrData = input;
      if (!input.startsWith('{')) {
        qrData = JSON.stringify({
          type: 'alumni_id',
          alumni_id: input.toUpperCase(),
          timestamp: Date.now()
        });
      }

      await processCheckin(qrData);
      Utils.$('#manualQrInput').value = '';
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', async () => {
      await stopScanner();
    });

    // Initialize
    await loadEvents();
    renderRecentCheckins();
  })();
</script>

