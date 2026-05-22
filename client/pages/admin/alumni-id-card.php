<!-- Admin - Alumni ID Card Viewer -->
<link rel="stylesheet" href="/assets/css/admin-pages-improved.css">
<link rel="stylesheet" href="/assets/css/admin-premium.css">
<style>
  body[data-app-section="admin"] #searchResultsCard > .card-header {
    padding: 1.25rem 2rem 1rem !important;
    min-height: 4.25rem !important;
    display: flex !important;
    align-items: center !important;
  }

  body[data-app-section="admin"] #searchResultsCard > .card-header .card-title {
    margin: 0 !important;
    padding: 0 !important;
    line-height: 1.45 !important;
  }

  body[data-app-section="admin"] #searchResultsCard > .card-body {
    padding: clamp(0.9rem, 1.6vw, 1.15rem) clamp(1rem, 2vw, 1.45rem) clamp(1rem, 1.7vw, 1.3rem) !important;
    overflow-x: visible !important;
  }

  body[data-app-section="admin"] #searchResultsCard .id-card-results-table-wrap {
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    overflow-x: auto;
    border: 1px solid #e1e8e4;
    border-radius: 10px;
  }

  body[data-app-section="admin"] #searchResultsCard .table-improved {
    width: 100% !important;
    min-width: 760px !important;
    margin: 0 !important;
  }

  body[data-app-section="admin"] #searchResultsCard .table-improved th,
  body[data-app-section="admin"] #searchResultsCard .table-improved td {
    padding: 1rem 1.15rem !important;
    white-space: normal !important;
    overflow-wrap: anywhere !important;
    word-break: normal !important;
  }
</style>

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
      <h1 class="page-title">Alumni ID Card Viewer</h1>
    </header>

    <div class="admin-content p-lg">
      <!-- Search Section -->
      <div class="card-improved mb-lg">
        <div class="card-header">
          <h3 class="card-title">Search Alumni</h3>
        </div>
        <div class="card-body">
          <form id="searchForm" class="grid grid-cols-3 gap-md">
            <div class="form-group" style="margin: 0;">
              <label class="form-label">Alumni ID</label>
              <input type="text" name="alumni_id" class="form-input" placeholder="e.g., BBC-2024-CCS-00001">
            </div>
            <div class="form-group" style="margin: 0;">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-input" placeholder="Search by name">
            </div>
            <div class="form-group" style="margin: 0;">
              <label class="form-label">&nbsp;</label>
              <button type="submit" class="btn btn-primary w-full">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="11" cy="11" r="8"></circle>
                  <path d="m21 21-4.35-4.35"></path>
                </svg>
                Search
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Search Results -->
      <div class="card-improved mb-lg" id="searchResultsCard" style="display: none;">
        <div class="card-header">
          <h3 class="card-title">Search Results</h3>
        </div>
        <div class="card-body p-0">
          <div class="id-card-results-table-wrap">
            <table class="table-improved">
              <thead>
                <tr>
                  <th>Alumni ID</th>
                  <th>Name</th>
                  <th>College</th>
                  <th>Program</th>
                  <th>Grad Year</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="searchResultsBody">
                <!-- Results will be populated here -->
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ID Card Display with Actions -->
      <div id="idCardSection" style="display: none;">
        <div class="flex gap-md" style="align-items: flex-start;">
          <!-- ID Card -->
          <div class="flex-1">
            <div class="id-card-container">
              <div class="id-card-wrapper" id="idCardWrapper">
            <!-- Front of ID Card -->
            <div class="id-card-front new-design">
              <!-- Green diagonal background -->
              <div class="front-green-bg">
                <div class="front-green-dots"></div>
                <div class="front-green-lines"></div>
              </div>

              <!-- Watermark -->
              <div class="front-watermark">
                <img src="/assets/images/logo.svg" alt="Watermark" id="institutionWatermark" onerror="this.style.display='none'">
              </div>

              <!-- Header -->
              <div class="front-header-new">
                <h2 id="institutionName">MINDORO STATE UNIVERSITY</h2>
                <p id="institutionTagline">ALUMNI ASSOCIATION</p>
                <div class="line"></div>
              </div>

              <!-- Logo Badge -->
              <img src="/assets/images/logo.svg" alt="Logo" class="front-seal-new" id="institutionLogoBadge" onerror="this.style.display='none'">

              <!-- Photo and Name (Left Side) -->
              <div class="front-photo-container">
                <div class="front-photo">
                  <img src="" alt="Profile" id="cardPhoto" style="display: none;">
                  <div class="photo-placeholder" id="cardPhotoPlaceholder">
                    <span id="cardInitials">A</span>
                  </div>
                </div>
                <div class="front-name-under">
                  <div class="field-name-label-under">NAME</div>
                  <div class="field-name-value-under" id="cardName">ALUMNI NAME</div>
                </div>
              </div>

              <!-- Information (Right Side) -->
              <div class="front-right-new">
                <div class="front-info-new">
                  <div class="field-alumni-id-label">ALUMNI ID</div>
                  <div class="field-alumni-id-value" id="cardAlumniId">BBC-2024-CCS-00001</div>
                  
                  <div class="field-row class-year">Class of <span id="cardGradYear">2024</span></div>
                  
                  <div class="field-row">
                    <span class="label">Student ID</span>
                    <span class="value" id="cardStudentIdFront">-</span>
                  </div>

                  <div class="field-row">
                    <span class="value" id="cardCollegeFront">-</span>
                  </div>
                  
                  <div class="field-row">
                    <span class="label">Program</span>
                    <span class="value" id="cardProgramFront">-</span>
                  </div>
                  
                  <div class="field-row">
                    <span class="label">Section</span>
                    <span class="value" id="cardSectionFront">-</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Back of ID Card -->
            <div class="id-card-back">
              <!-- Header -->
              <div class="back-header">
                <h3 class="back-title">ALUMNI INFORMATION</h3>
                <span class="valid-badge">VALID</span>
              </div>

              <!-- Watermark -->
              <div class="back-watermark">
                <img src="/assets/images/logo.svg" alt="Watermark" onerror="this.style.display='none'">
              </div>

              <!-- Body -->
              <div class="back-body">
                <div class="back-left">
                  <div class="info-field">
                    <span class="info-label">EMAIL ADDRESS</span>
                    <span class="info-value" id="cardEmail">-</span>
                  </div>
                  
                  <div class="info-field">
                    <span class="info-label">PHONE NUMBER</span>
                    <span class="info-value" id="cardPhone">-</span>
                  </div>
                  
                  <div class="info-field">
                    <span class="info-label">MEMBER SINCE</span>
                    <span class="info-value" id="cardMemberSince">-</span>
                  </div>
                  
                  <div class="info-field">
                    <span class="info-label">ISSUED DATE</span>
                    <span class="info-value" id="cardIssuedDate">-</span>
                  </div>
                  
                  <div class="info-field">
                    <span class="info-label">STATUS</span>
                    <span class="info-value" id="cardStatus">-</span>
                  </div>

                  <div class="info-field">
                    <span class="info-label">ADDRESS</span>
                    <span class="info-value" id="cardAddress">-</span>
                  </div>
                </div>
                
                <div class="back-right">
                  <div>
                    <div class="qr-container" id="qrCodeCanvas"></div>
                    <div class="qr-label">SCAN TO VERIFY</div>
                  </div>
                  
                  <div class="important-box">
                    <h4 class="important-title">IMPORTANT:</h4>
                    <p class="important-text">Property of Mindoro State University. Surrender upon request. If found, please return to the Alumni Office, Mindoro State University. Website: alumni.msu.edu.ph</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
          </div>
          
          <!-- Actions Sidebar -->
          <div style="width: 200px; flex-shrink: 0;">
            <div class="card">
              <div class="card-body" style="padding: 1rem;">
                <h4 style="font-size: 0.9rem; font-weight: 700; margin: 0 0 0.75rem 0; color: #047857;">Actions</h4>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                  <button class="btn btn-sm btn-outline w-100" id="viewFrontBtn" style="font-size: 0.85rem; padding: 0.5rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                      <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    Front
                  </button>
                  <button class="btn btn-sm btn-outline w-100" id="viewBackBtn" style="font-size: 0.85rem; padding: 0.5rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    </svg>
                    Back
                  </button>
                  <button class="btn btn-sm btn-primary w-100" id="printIdBtn" style="font-size: 0.85rem; padding: 0.5rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <polyline points="6 9 6 2 18 2 18 9"></polyline>
                      <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                      <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    Print
                  </button>
                  <button class="btn btn-sm btn-secondary w-100" id="downloadIdBtn" style="font-size: 0.85rem; padding: 0.5rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                      <polyline points="7 10 12 15 17 10"></polyline>
                      <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Download
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Link to shared ID card CSS -->
<link rel="stylesheet" href="assets/css/id-card-design.css?v=20260518.08">

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js" crossorigin="anonymous"></script>
<script>
  (async function() {
    let currentIdCard = null;
    let allAlumni = []; // Store all alumni for client-side filtering

    // Search alumni
    Utils.$('#searchForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = Utils.serializeForm(e.target);

      console.log('Search form data:', formData); // Debug log

      // If both fields are empty, show all alumni
      if (!formData.alumni_id && !formData.name) {
        displaySearchResults(allAlumni);
        return;
      }

      // Try server-side search first
      try {
        const response = await API.admin.getAlumni(formData);
        console.log('Search response:', response); // Debug log
        let alumni = response.data?.alumni || response.data?.items || response.data || [];
        
        // If server returns all alumni (no filtering), do client-side filtering
        if (alumni.length === allAlumni.length && allAlumni.length > 0) {
          console.log('Server returned all alumni, applying client-side filter');
          alumni = filterAlumniClientSide(allAlumni, formData);
        }
        
        displaySearchResults(alumni);
      } catch (error) {
        console.error('Search error:', error); // Debug log
        // Fallback to client-side filtering
        console.log('Falling back to client-side filtering');
        const filtered = filterAlumniClientSide(allAlumni, formData);
        displaySearchResults(filtered);
      }
    });

    // Client-side filtering function
    function filterAlumniClientSide(alumni, filters) {
      return alumni.filter(a => {
        let matches = true;
        
        // Filter by alumni_id
        if (filters.alumni_id && filters.alumni_id.trim()) {
          const searchId = filters.alumni_id.trim().toLowerCase();
          const alumniId = (a.alumni_id || '').toLowerCase();
          matches = matches && alumniId.includes(searchId);
        }
        
        // Filter by name
        if (filters.name && filters.name.trim()) {
          const searchName = filters.name.trim().toLowerCase();
          const alumniName = (a.name || '').toLowerCase();
          matches = matches && alumniName.includes(searchName);
        }
        
        return matches;
      });
    }

    function displaySearchResults(alumni) {
      const tbody = Utils.$('#searchResultsBody');
      const card = Utils.$('#searchResultsCard');

      console.log('Displaying search results:', alumni); // Debug log
      console.log('Alumni count:', alumni ? alumni.length : 0); // Debug log

      if (!alumni || alumni.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-secondary">No alumni found</td></tr>';
        card.style.display = 'block';
        return;
      }

      tbody.innerHTML = alumni.map(a => `
        <tr>
          <td>${Utils.escapeHtml(a.alumni_id || '-')}</td>
          <td>
            <div class="admin-table-person">
              <strong>${Utils.escapeHtml(a.name || '-')}</strong>
              <span>${Utils.escapeHtml(a.email || '')}</span>
            </div>
          </td>
          <td><span class="admin-code-chip" title="${Utils.escapeHtml(a.college_name || a.college_code || '-')}">${Utils.escapeHtml(a.college_code || '-')}</span></td>
          <td><span class="admin-code-chip" title="${Utils.escapeHtml(a.program_name || a.program_code || '-')}">${Utils.escapeHtml(a.program_code || '-')}</span></td>
          <td>${a.graduation_year || '-'}</td>
          <td>
            <button class="btn btn-sm btn-primary" onclick="viewIdCard('${a.alumni_id}')">
              View ID Card
            </button>
          </td>
        </tr>
      `).join('');

      card.style.display = 'block';
    }

    window.viewIdCard = async function(alumniId) {
      console.log('Viewing ID card for alumni:', alumniId);
      try {
        const response = await API.get(`/admin/alumni/id-card?alumni_id=${alumniId}`);
        console.log('ID card response:', response);
        currentIdCard = response.data;
        renderIdCard(currentIdCard);
        Utils.$('#idCardSection').style.display = 'block';
        Utils.$('#printIdBtn').disabled = false;
        
        // Scroll to ID card
        Utils.$('#idCardSection').scrollIntoView({ behavior: 'smooth' });
      } catch (error) {
        console.error('Failed to load ID card:', error);
        Utils.error(`Failed to load ID card: ${error.message || error.code || 'Unknown error'}`);
      }
    };

    function setText(selector, value) {
      const element = Utils.$(selector);
      if (element) {
        element.textContent = value || '-';
      }
    }

    function getImageUrlCandidates(imageUrl) {
      const raw = String(imageUrl || '').trim();
      const candidates = [];
      const add = (value) => {
        const normalized = String(value || '').trim();
        if (normalized && !candidates.includes(normalized)) {
          candidates.push(normalized);
        }
      };

      if (!raw) {
        return candidates;
      }

      if (API.getAssetUrlCandidates) {
        return API.getAssetUrlCandidates(raw);
      }

      if (/^[a-z][a-z0-9+.-]*:/i.test(raw) || raw.startsWith('//')) {
        add(raw);
        return candidates;
      }

      if (API.resolveAssetUrl) {
        add(API.resolveAssetUrl(raw));
      }

      const apiRoot = (API.baseUrl || '').replace(/\/api\/v1\/?$/, '').replace(/\/+$/, '');
      const uploadPath = raw.replace(/^\/+/, '');

      if (apiRoot) {
        add(`${apiRoot}/${uploadPath}`);
      }

      if (window.location?.origin && uploadPath.startsWith('uploads/')) {
        add(`${window.location.origin}/${uploadPath}`);
      }

      add(raw);
      return candidates;
    }

    function renderProfilePhoto(imageUrl, name) {
      const photoEl = Utils.$('#cardPhoto');
      const placeholderEl = Utils.$('#cardPhotoPlaceholder');
      const initialsEl = Utils.$('#cardInitials');
      const initials = Utils.getInitials(name || 'A');
      const candidates = getImageUrlCandidates(imageUrl);
      let index = 0;

      if (initialsEl) {
        initialsEl.textContent = initials;
      }

      function showPlaceholder() {
        if (photoEl) {
          photoEl.removeAttribute('src');
          photoEl.style.display = 'none';
        }
        if (placeholderEl) {
          placeholderEl.style.display = 'flex';
        }
      }

      function tryNext() {
        if (!photoEl || !placeholderEl || index >= candidates.length) {
          showPlaceholder();
          return;
        }

        const candidate = candidates[index];
        index += 1;

        photoEl.onload = function() {
          photoEl.style.display = 'block';
          placeholderEl.style.display = 'none';
        };
        photoEl.onerror = function() {
          console.warn('Failed to load profile image:', candidate);
          tryNext();
        };
        photoEl.style.display = 'none';
        placeholderEl.style.display = 'flex';
        photoEl.src = candidate;
      }

      if (candidates.length) {
        tryNext();
      } else {
        showPlaceholder();
      }
    }

    function renderIdCard(data) {
      const branding = window.App?.getBrandingSnapshot?.() || {};
      setText('#institutionName', branding.institutionName || data.institution_name || 'Mindoro State University');
      setText('#institutionTagline', branding.institutionTagline || data.institution_tagline || 'Alumni Association');

      // Front side
      const nameEl = Utils.$('#cardName');
      if (nameEl) {
        nameEl.textContent = formatCardName(data).toUpperCase();
      }
      
      setText('#cardStudentIdFront', data.student_id);
      setText('#cardAlumniId', data.alumni_id);
      setText('#cardGradYear', data.graduation_year);
      setText('#cardCollegeFront', data.college_name || data.college_code);
      setText('#cardProgramFront', data.program_name || data.program_code);
      setText('#cardSectionFront', data.section_name);

      // Back side
      setText('#cardEmail', data.email);
      setText('#cardPhone', data.phone);
      setText('#cardMemberSince', data.member_since || yearFromDate(data.created_at));
      setText('#cardIssuedDate', formatDate(data.issued_date));
      setText('#cardStatus', formatStatus(data.status || 'active'));
      setText('#cardAddress', data.address || data.address_city);

      renderProfilePhoto(data.profile_image, data.name);

      // Generate QR code
      if (data.qr_code_data) {
        generateQRCode(data.qr_code_data);
      }
    }

    function formatCardName(data) {
      const clean = (value) => String(value || '').replace(/\s+/g, ' ').trim();
      const firstName = clean(data.first_name);
      const middleName = clean(data.middle_name);
      const lastName = clean(data.last_name);
      const suffix = clean(data.suffix);

      if (firstName || lastName) {
        const middleInitial = middleName ? `${middleName.charAt(0).toUpperCase()}.` : '';
        return [firstName, middleInitial, lastName, suffix].filter(Boolean).join(' ') || clean(data.name) || 'Alumni Name';
      }

      return abbreviateMiddleName(clean(data.name) || 'Alumni Name');
    }

    function abbreviateMiddleName(fullName) {
      const tokens = String(fullName || '').replace(/\s+/g, ' ').trim().split(' ').filter(Boolean);
      if (tokens.length <= 2) return fullName;

      const lastNameParticles = new Set(['DE', 'DEL', 'DELA', 'DE', 'DI', 'DA', 'DOS', 'DAS', 'VAN', 'VON', 'SAN', 'SANTA']);
      const lastStart = tokens.length >= 4 && lastNameParticles.has(tokens[tokens.length - 2].toUpperCase())
        ? tokens.length - 2
        : tokens.length - 1;
      const first = tokens[0];
      const middleInitial = tokens.slice(1, lastStart).find(Boolean)?.charAt(0).toUpperCase();
      const last = tokens.slice(lastStart).join(' ');

      return [first, middleInitial ? `${middleInitial}.` : '', last].filter(Boolean).join(' ');
    }

    function formatDate(value) {
      const raw = String(value || '').trim();
      if (!raw) return '-';
      const datePart = raw.slice(0, 10);
      const date = new Date(`${datePart}T00:00:00`);
      if (Number.isNaN(date.getTime())) return raw;
      return date.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
    }

    function yearFromDate(value) {
      const match = String(value || '').match(/\b(19|20)\d{2}\b/);
      return match ? match[0] : '';
    }

    function formatStatus(value) {
      return String(value || '-')
        .replace(/_/g, ' ')
        .replace(/\b\w/g, (letter) => letter.toUpperCase());
    }

    function generateQRCode(data) {
      const qrContainer = document.getElementById('qrCodeCanvas');
      
      if (!data) {
        qrContainer.innerHTML = '<div style="padding: 1rem; text-align: center; color: #6b7280; font-size: 0.75rem;">No QR data</div>';
        return;
      }
      
      if (typeof QRCode === 'undefined') {
        console.warn('QRCode library not available');
        qrContainer.innerHTML = '<div style="padding: 1rem; text-align: center; color: #6b7280; font-size: 0.75rem;">QR library not loaded</div>';
        return;
      }
      
      try {
        // Clear existing QR code
        qrContainer.innerHTML = '';
        
        // Generate new QR code
        new QRCode(qrContainer, {
          text: data,
          width: 108,
          height: 108,
          colorDark: '#000000',
          colorLight: '#ffffff',
          correctLevel: QRCode.CorrectLevel.M
        });
        
        console.log('QR code generated successfully');
      } catch (error) {
        console.error('Failed to generate QR code:', error);
        qrContainer.innerHTML = '<div style="padding: 1rem; text-align: center; color: #ef4444; font-size: 0.75rem;">QR generation failed</div>';
      }
    }

    // Print ID card
    Utils.$('#printIdBtn').addEventListener('click', () => {
      if (window.IdCardPrinter && typeof window.IdCardPrinter.print === 'function') {
        window.IdCardPrinter.print({ title: 'Alumni ID Card' });
        return;
      }

      window.print();
    });

    // Download ID card
    Utils.$('#downloadIdBtn').addEventListener('click', async () => {
      try {
        const { default: html2canvas } = await import('https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/+esm');
        
        const cardElement = Utils.$('.id-card-front');
        const canvas = await html2canvas(cardElement, {
          scale: 2,
          backgroundColor: '#ffffff'
        });

        const link = document.createElement('a');
        link.download = `alumni-id-${currentIdCard.alumni_id}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();

        Utils.success('ID card downloaded successfully!');
      } catch (error) {
        console.error('Download error:', error);
        Utils.error('Failed to download ID card');
      }
    });

    // View front/back
    Utils.$('#viewFrontBtn').addEventListener('click', () => {
      console.log('Admin: View Front clicked');
      const wrapper = Utils.$('.id-card-wrapper');
      if (wrapper) {
        wrapper.classList.remove('flipped');
        console.log('Admin: Removed flipped class');
      }
    });

    Utils.$('#viewBackBtn').addEventListener('click', () => {
      console.log('Admin: View Back clicked');
      const wrapper = Utils.$('.id-card-wrapper');
      if (wrapper) {
        wrapper.classList.add('flipped');
        console.log('Admin: Added flipped class, classes:', wrapper.className);
        
        // Debug: Check if back card exists and its styles
        const backCard = document.querySelector('.id-card-back');
        if (backCard) {
          console.log('Admin: Back card found, computed display:', window.getComputedStyle(backCard).display);
        } else {
          console.error('Admin: Back card not found!');
        }
      }
    });

    // Load all alumni on page load
    async function loadAllAlumni() {
      try {
        const response = await API.admin.getAlumni({});
        console.log('Initial load response:', response);
        allAlumni = response.data?.alumni || response.data?.items || response.data || [];
        displaySearchResults(allAlumni);
      } catch (error) {
        console.error('Failed to load alumni:', error);
        // Show empty state instead of error on initial load
        const tbody = Utils.$('#searchResultsBody');
        const card = Utils.$('#searchResultsCard');
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-secondary">No alumni found. Use the search form above to find alumni.</td></tr>';
        card.style.display = 'block';
      }
    }

    // Initialize: Load all alumni
    loadAllAlumni();
  })();
</script>

