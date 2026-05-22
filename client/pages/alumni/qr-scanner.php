<!-- Alumni - QR Code Scanner for Event Check-in -->
<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content">
    <header class="content-header">
      <button class="btn btn-ghost sidebar-toggle" id="sidebarToggle">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </button>
      <h1 class="page-title">QR Scanner</h1>
    </header>

    <div class="content-body">
      <!-- VERSION: NEW_IMPROVED_V2 - If you see this comment in browser source, new file is loaded -->
      <div style="max-width: 800px; margin: 0 auto; padding: 2rem;">
      <!-- Scanner Section -->
      <div class="scanner-card">
        <div class="scanner-header">
          <h3>Scan Event QR Code</h3>
          <div class="scanner-controls">
            <button class="btn-control" id="toggleCameraBtn" style="display: none;" title="Switch Camera">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                <circle cx="12" cy="13" r="4"></circle>
              </svg>
            </button>
          </div>
        </div>

        <div class="scanner-body">
          <!-- Camera View -->
          <div class="camera-container" id="cameraContainer">
            <div id="qrReader"></div>
            <div class="scanner-overlay" id="scannerOverlay">
              <div class="scanner-frame">
                <div class="corner corner-tl"></div>
                <div class="corner corner-tr"></div>
                <div class="corner corner-bl"></div>
                <div class="corner corner-br"></div>
                <div class="scan-line"></div>
              </div>
              <p class="scanner-hint">Position QR code within the frame</p>
            </div>
          </div>

          <!-- Status Messages -->
          <div id="statusMessage" class="status-message" style="display: none;">
            <div class="status-icon"></div>
            <p class="status-text"></p>
          </div>

          <!-- Instructions -->
          <div class="instructions-box">
            <div class="instruction-item">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                <path d="M2 17l10 5 10-5"></path>
                <path d="M2 12l10 5 10-5"></path>
              </svg>
              <span>Point your camera at the event's QR code</span>
            </div>
            <div class="instruction-item">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <polyline points="12 6 12 12 16 14"></polyline>
              </svg>
              <span>Scanner will automatically detect the code</span>
            </div>
            <div class="instruction-item">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
              <span>You'll receive confirmation and points</span>
            </div>
          </div>

          <!-- Manual Entry -->
          <details class="manual-entry">
            <summary>Can't scan? Enter code manually</summary>
            <form id="manualEntryForm" class="manual-form">
              <input 
                type="text" 
                id="manualCodeInput" 
                class="manual-input" 
                placeholder="Enter event code"
                autocomplete="off"
              >
              <button type="submit" class="btn-submit">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <polyline points="9 11 12 14 22 4"></polyline>
                  <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                </svg>
                Submit
              </button>
            </form>
          </details>
        </div>
      </div>

      <!-- Recent Check-ins -->
      <div class="recent-checkins" id="recentCheckinsCard" style="display: none;">
        <h3>Recent Check-ins</h3>
        <div id="recentCheckinsList"></div>
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
        <div class="success-animation">
          <svg class="checkmark" viewBox="0 0 52 52">
            <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
            <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
          </svg>
        </div>
        <h2 class="modal-title">Check-in Successful!</h2>
        <div class="event-details">
          <h3 id="successEventTitle">Event Name</h3>
          <p id="successEventDate">Event Date</p>
        </div>
        <div class="points-badge">
          <span class="points-label">Points Earned</span>
          <span class="points-value" id="successPoints">+20</span>
        </div>
        <button class="btn-continue" onclick="closeSuccessModal()">Continue Scanning</button>
      </div>
    </div>
  </div>
</div>

<style>
  :root {
    --primary: #10b981;
    --primary-dark: #059669;
    --success: #10b981;
    --error: #ef4444;
    --warning: #f59e0b;
    --info: #3b82f6;
  }

  .scanner-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    margin-bottom: 2rem;
  }

  .scanner-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
  }

  .scanner-header h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
  }

  .scanner-controls {
    display: flex;
    gap: 0.5rem;
  }

  .btn-control {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
  }

  .btn-control:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
  }

  .scanner-body {
    padding: 2rem;
  }

  .camera-container {
    position: relative;
    max-width: 600px;
    margin: 0 auto 2rem;
    border-radius: 16px;
    overflow: hidden;
    background: #1f2937;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
  }

  #qrReader {
    width: 100%;
    min-height: 450px;
    background: #1f2937;
  }

  #qrReader video {
    width: 100% !important;
    height: auto !important;
    display: block !important;
    border-radius: 16px;
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
    z-index: 10;
  }

  .scanner-frame {
    position: relative;
    width: 280px;
    height: 280px;
    background: transparent;
  }

  .corner {
    position: absolute;
    width: 40px;
    height: 40px;
    border: 4px solid var(--primary);
  }

  .corner-tl {
    top: 0;
    left: 0;
    border-right: none;
    border-bottom: none;
    border-radius: 8px 0 0 0;
  }

  .corner-tr {
    top: 0;
    right: 0;
    border-left: none;
    border-bottom: none;
    border-radius: 0 8px 0 0;
  }

  .corner-bl {
    bottom: 0;
    left: 0;
    border-right: none;
    border-top: none;
    border-radius: 0 0 0 8px;
  }

  .corner-br {
    bottom: 0;
    right: 0;
    border-left: none;
    border-top: none;
    border-radius: 0 0 8px 0;
  }

  .scan-line {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, var(--primary), transparent);
    animation: scan 2s ease-in-out infinite;
    box-shadow: 0 0 10px var(--primary);
  }

  @keyframes scan {
    0%, 100% {
      transform: translateY(0);
      opacity: 0;
    }
    50% {
      transform: translateY(280px);
      opacity: 1;
    }
  }

  .scanner-hint {
    position: absolute;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    color: white;
    font-weight: 600;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.8);
    background: rgba(0, 0, 0, 0.7);
    padding: 0.75rem 1.5rem;
    border-radius: 24px;
    font-size: 0.9375rem;
    white-space: nowrap;
    backdrop-filter: blur(10px);
  }

  .status-message {
    text-align: center;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    animation: slideDown 0.3s ease-out;
  }

  @keyframes slideDown {
    from {
      opacity: 0;
      transform: translateY(-20px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .status-message.success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
  }

  .status-message.error {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
  }

  .status-message.info {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
  }

  .status-text {
    margin: 0.5rem 0 0;
    font-weight: 600;
  }

  .instructions-box {
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
  }

  .instruction-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.75rem 0;
    color: #065f46;
  }

  .instruction-item svg {
    flex-shrink: 0;
    color: var(--primary);
  }

  .manual-entry {
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 1rem;
    background: #f9fafb;
  }

  .manual-entry summary {
    cursor: pointer;
    font-weight: 600;
    color: var(--primary);
    list-style: none;
    user-select: none;
  }

  .manual-entry summary::-webkit-details-marker {
    display: none;
  }

  .manual-form {
    display: flex;
    gap: 0.75rem;
    margin-top: 1rem;
  }

  .manual-input {
    flex: 1;
    padding: 0.875rem 1.25rem;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
  }

  .manual-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
  }

  .btn-submit {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.875rem 1.5rem;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
  }

  .recent-checkins {
    background: white;
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
  }

  .recent-checkins h3 {
    margin: 0 0 1.5rem;
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
  }

  .checkin-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-radius: 12px;
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    margin-bottom: 0.75rem;
    animation: slideIn 0.3s ease-out;
  }

  @keyframes slideIn {
    from {
      opacity: 0;
      transform: translateX(-20px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }

  .checkin-info h4 {
    margin: 0 0 0.25rem;
    font-weight: 600;
    color: #111827;
  }

  .checkin-info p {
    margin: 0;
    font-size: 0.875rem;
    color: #6b7280;
  }

  .checkin-points {
    background: var(--primary);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 24px;
    font-weight: 700;
    font-size: 0.875rem;
  }

  /* Success Modal Styles */
  .success-animation {
    margin: 0 auto 2rem;
  }

  .checkmark {
    width: 100px;
    height: 100px;
    margin: 0 auto;
  }

  .checkmark-circle {
    stroke-dasharray: 166;
    stroke-dashoffset: 166;
    stroke-width: 2;
    stroke: var(--success);
    fill: none;
    animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
  }

  .checkmark-check {
    transform-origin: 50% 50%;
    stroke-dasharray: 48;
    stroke-dashoffset: 48;
    stroke: var(--success);
    stroke-width: 3;
    animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
  }

  @keyframes stroke {
    100% {
      stroke-dashoffset: 0;
    }
  }

  .modal-title {
    font-size: 2rem;
    font-weight: 700;
    color: #111827;
    margin: 0 0 1.5rem;
  }

  .event-details {
    background: #f9fafb;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
  }

  .event-details h3 {
    margin: 0 0 0.5rem;
    font-size: 1.25rem;
    font-weight: 700;
    color: #111827;
  }

  .event-details p {
    margin: 0;
    color: #6b7280;
  }

  .points-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border-radius: 12px;
    margin-bottom: 2rem;
  }

  .points-label {
    font-size: 1rem;
    color: #065f46;
    font-weight: 600;
  }

  .points-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--primary);
  }

  .btn-continue {
    width: 100%;
    padding: 1rem;
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
  }

  .btn-continue:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3);
  }

  /* Responsive */
  @media (max-width: 768px) {
    .scanner-body {
      padding: 1rem;
    }

    .scanner-frame {
      width: 220px;
      height: 220px;
    }

    .scan-line {
      animation: scan 2s ease-in-out infinite;
    }

    @keyframes scan {
      0%, 100% {
        transform: translateY(0);
        opacity: 0;
      }
      50% {
        transform: translateY(220px);
        opacity: 1;
      }
    }

    #qrReader {
      min-height: 350px;
    }
  }
</style>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function() {
  'use strict';
  
  let html5QrCode = null;
  let currentCamera = 'environment';
  let recentCheckins = [];
  let isProcessing = false;
  let isScanning = false;
  let listenersReady = false;
  let resumeTimer = null;
  let initRetryTimer = null;
  let isDestroyed = false;

  function getFirstValue(source, keys) {
    if (!source || typeof source !== 'object') return '';
    for (const key of keys) {
      if (source[key] !== undefined && source[key] !== null && String(source[key]).trim() !== '') {
        return source[key];
      }
    }
    return '';
  }

  function parseAttendanceInput(rawValue) {
    const raw = String(rawValue || '').trim();
    let payload = null;
    let code = '';
    let eventId = '';

    if (!raw) {
      return { code: '', eventId: '' };
    }

    try {
      payload = JSON.parse(raw);
    } catch (error) {
      payload = null;
    }

    if (payload && typeof payload === 'object') {
      const source = payload.data && typeof payload.data === 'object'
        ? { ...payload.data, ...payload }
        : payload;
      code = getFirstValue(source, ['code', 'attendance_code', 'attendanceCode', 'event_code', 'eventCode']);
      eventId = getFirstValue(source, ['event_id', 'eventId']);
    } else {
      try {
        const parsedUrl = new URL(raw, window.location.origin);
        code =
          parsedUrl.searchParams.get('code') ||
          parsedUrl.searchParams.get('attendance_code') ||
          parsedUrl.searchParams.get('event_code') ||
          '';
        eventId =
          parsedUrl.searchParams.get('event_id') ||
          parsedUrl.searchParams.get('eventId') ||
          '';
      } catch (error) {
        code = raw;
      }
    }

    const normalizedCode = payload && typeof payload === 'object' ? code : (code || raw);

    return {
      code: String(normalizedCode || '').trim().toUpperCase(),
      eventId: String(eventId || '').trim(),
    };
  }

  function normalizeCheckinResponse(response) {
    const data = response?.data || {};
    const event = data.event && typeof data.event === 'object'
      ? data.event
      : { title: data.event_title || data.event || 'Event' };

    return {
      event,
      points: Number(data.points_awarded ?? data.points_earned ?? event.points_reward ?? 0),
    };
  }

  async function pauseScanner() {
    if (!html5QrCode || !isScanning || typeof html5QrCode.pause !== 'function') {
      return;
    }

    try {
      await html5QrCode.pause();
    } catch (error) {
      console.warn('Unable to pause scanner:', error);
    }
  }

  async function resumeScanner() {
    if (!html5QrCode || !isScanning || typeof html5QrCode.resume !== 'function') {
      return;
    }

    try {
      await html5QrCode.resume();
    } catch (error) {
      console.warn('Unable to resume scanner:', error);
    }
  }

  // Initialize
  init();

  function init() {
    if (isDestroyed) return;

    console.log('Initializing QR Scanner...');

    if (!listenersReady) {
      setupEventListeners();
      loadRecentCheckins();
      listenersReady = true;
    }
    
    // Check if library is loaded
    if (typeof Html5Qrcode === 'undefined') {
      console.log('Waiting for Html5Qrcode library...');
      showStatus('QR scanner is loading. You can still enter the event code manually.', 'info');
      initRetryTimer = setTimeout(init, 100);
      return;
    }

    console.log('Html5Qrcode library loaded');
    
    // Start scanner
    startScanner();
  }

  function setupEventListeners() {
    // Camera toggle
    const toggleBtn = document.getElementById('toggleCameraBtn');
    if (toggleBtn) {
      toggleBtn.addEventListener('click', async () => {
        currentCamera = currentCamera === 'environment' ? 'user' : 'environment';
        await stopScanner();
        await startScanner();
      });
    }

    // Manual entry
    const manualForm = document.getElementById('manualEntryForm');
    if (manualForm) {
      manualForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = document.getElementById('manualCodeInput');
        const code = input.value.trim();
        if (code) {
          await processCheckin(code);
          input.value = '';
        }
      });
    }
  }

  async function startScanner() {
    if (isDestroyed) {
      return;
    }

    if (isScanning) {
      console.log('Scanner already running');
      return;
    }

    showStatus('Initializing camera...', 'info');

    try {
      if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
        throw new Error('Camera access is not available in this browser.');
      }

      // Request camera permission first
      const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: currentCamera } });
      stream.getTracks().forEach(track => track.stop());

      html5QrCode = new Html5Qrcode("qrReader");

      const config = {
        fps: 10,
        qrbox: { width: 280, height: 280 },
        aspectRatio: 1.0,
        formatsToSupport: [Html5QrcodeSupportedFormats.QR_CODE]
      };

      await html5QrCode.start(
        { facingMode: currentCamera },
        config,
        onScanSuccess,
        onScanError
      );

      isScanning = true;
      hideStatus();
      document.getElementById('scannerOverlay').style.display = 'flex';
      document.getElementById('toggleCameraBtn').style.display = 'inline-flex';
      console.log('Scanner started successfully');

    } catch (error) {
      console.error('Scanner start error:', error);
      isScanning = false;
      
      let errorMessage = 'Camera access failed. ';
      if (error.name === 'NotAllowedError') {
        errorMessage += 'Please allow camera access in your browser settings.';
      } else if (error.name === 'NotFoundError') {
        errorMessage += 'No camera found on this device.';
      } else if (error.name === 'NotReadableError') {
        errorMessage += 'Camera is in use by another application.';
      } else if (error.name === 'SecurityError') {
        errorMessage += 'Camera access requires HTTPS.';
      } else {
        errorMessage += error.message || 'Please check your camera and try again.';
      }
      
      showStatus(errorMessage, 'error');
    }
  }

  async function stopScanner() {
    if (resumeTimer) {
      clearTimeout(resumeTimer);
      resumeTimer = null;
    }

    if (initRetryTimer) {
      clearTimeout(initRetryTimer);
      initRetryTimer = null;
    }

    if (html5QrCode && isScanning) {
      try {
        await html5QrCode.stop();
        isScanning = false;
        console.log('Scanner stopped');
      } catch (error) {
        console.error('Scanner stop error:', error);
      }
    }

    if (html5QrCode && typeof html5QrCode.clear === 'function') {
      try {
        html5QrCode.clear();
      } catch (error) {
        console.warn('Scanner clear error:', error);
      }
    }

    isScanning = false;
    html5QrCode = null;
  }

  async function onScanSuccess(decodedText, decodedResult) {
    if (isProcessing) return;
    
    console.log('QR Code detected:', decodedText);
    
    // Visual feedback
    const frame = document.querySelector('.scanner-frame');
    if (frame) {
      frame.style.borderColor = '#10b981';
      setTimeout(() => {
        frame.style.borderColor = '';
      }, 500);
    }
    
    await processCheckin(decodedText);
  }

  function onScanError(error) {
    // Silently ignore scan errors (they happen frequently during scanning)
  }

  async function processCheckin(rawCode) {
    if (isProcessing) return;

    const attendance = parseAttendanceInput(rawCode);
    if (!attendance.code) {
      showStatus('No attendance code found in the scanned QR code.', 'error');
      setTimeout(() => hideStatus(), 5000);
      return;
    }

    isProcessing = true;

    try {
      // Pause scanner
      await pauseScanner();

      showStatus('Processing check-in...', 'info');

      const requestBody = { code: attendance.code };
      if (attendance.eventId) {
        requestBody.event_id = attendance.eventId;
      }

      const response = attendance.eventId
        ? await API.events.checkIn(attendance.eventId, requestBody)
        : await API.post('/events/checkin', requestBody);
      const data = normalizeCheckinResponse(response);
      
      hideStatus();
      
      // Show success modal
      document.getElementById('successEventTitle').textContent = data.event?.title || 'Event';
      document.getElementById('successEventDate').textContent = data.event?.event_date ? 
        Utils.formatDate(data.event.event_date) : '';
      document.getElementById('successPoints').textContent = `+${data.points}`;
      document.getElementById('successModal').classList.add('active');

      // Add to recent check-ins
      const checkin = {
        event: data.event?.title || 'Event',
        time: new Date().toISOString(),
        points: data.points
      };
      
      recentCheckins.unshift(checkin);
      if (recentCheckins.length > 5) {
        recentCheckins = recentCheckins.slice(0, 5);
      }
      
      saveRecentCheckins();
      renderRecentCheckins();

      // Resume scanner after 2 seconds
      resumeTimer = setTimeout(async () => {
        if (!isDestroyed) {
          await resumeScanner();
        }
        isProcessing = false;
        resumeTimer = null;
      }, 2000);

    } catch (error) {
      console.error('Check-in error:', error);
      showStatus(error.message || 'Check-in failed. Please try again.', 'error');
      
      // Resume scanner
      if (!isDestroyed) {
        await resumeScanner();
      }
      isProcessing = false;
      
      setTimeout(() => hideStatus(), 5000);
    }
  }

  function showStatus(message, type = 'info') {
    const statusEl = document.getElementById('statusMessage');
    const textEl = statusEl.querySelector('.status-text');
    
    statusEl.className = `status-message ${type}`;
    textEl.textContent = message;
    statusEl.style.display = 'block';
  }

  function hideStatus() {
    const statusEl = document.getElementById('statusMessage');
    statusEl.style.display = 'none';
  }

  window.closeSuccessModal = function() {
    document.getElementById('successModal').classList.remove('active');
  };

  function loadRecentCheckins() {
    try {
      const stored = localStorage.getItem('recentCheckins');
      if (stored) {
        recentCheckins = JSON.parse(stored);
        renderRecentCheckins();
      }
    } catch (error) {
      console.error('Error loading recent check-ins:', error);
    }
  }

  function saveRecentCheckins() {
    try {
      localStorage.setItem('recentCheckins', JSON.stringify(recentCheckins));
    } catch (error) {
      console.error('Error saving recent check-ins:', error);
    }
  }

  function renderRecentCheckins() {
    const card = document.getElementById('recentCheckinsCard');
    const list = document.getElementById('recentCheckinsList');
    
    if (recentCheckins.length === 0) {
      card.style.display = 'none';
      return;
    }

    card.style.display = 'block';
    list.innerHTML = recentCheckins.map(checkin => `
      <div class="checkin-item">
        <div class="checkin-info">
          <h4>${Utils.escapeHtml(checkin.event)}</h4>
          <p>${Utils.formatDateTime(checkin.time)}</p>
        </div>
        <div class="checkin-points">+${checkin.points} pts</div>
      </div>
    `).join('');
  }

  async function cleanupScannerPage() {
    isDestroyed = true;
    await stopScanner();
  }

  window.__pageCleanup = cleanupScannerPage;
  window.addEventListener('hashchange', () => cleanupScannerPage(), { once: true });
  window.addEventListener('beforeunload', () => cleanupScannerPage(), { once: true });

})();
</script>
