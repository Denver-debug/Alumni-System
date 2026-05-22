<!-- Alumni ID Card Page -->
<!-- IMPORTANT: After updating this file, hard refresh the browser with Ctrl+Shift+R to clear cache -->
<style>
  /* ── Layout shell ─────────────────────────────────────────── */
  .dashboard-layout { display: flex !important; min-height: 100vh; }
  .dashboard-layout .sidebar {
    display: flex !important; flex-direction: column !important;
    position: fixed !important; inset: 0 auto 0 0 !important;
    width: 286px !important;
    background: linear-gradient(168deg, rgb(6 78 59) 0%, rgb(4 60 72) 54%, rgb(15 23 42) 100%) !important;
    color: white !important;
    border-right: 1px solid rgb(255 255 255 / 0.15) !important;
    box-shadow: 0 0 0 1px rgb(255 255 255 / 0.05), 0 26px 48px -32px rgb(2 6 23 / 0.72) !important;
    z-index: 90 !important;
  }
  .sidebar-header { padding: 1.15rem 1.05rem 0.95rem; border-bottom: 1px solid rgb(255 255 255 / 0.15); }
  .sidebar-brand { margin-bottom: 0.95rem; }
  .sidebar-brand-name { font-family:"Sora",sans-serif; font-size:.92rem; letter-spacing:.08em; text-transform:uppercase; font-weight:700; color:rgb(237 251 246); }
  .sidebar-brand-subtitle { margin-top:.2rem; font-size:.73rem; letter-spacing:.01em; color:rgb(205 223 240 / 0.82); }
  .alumni-shell-user { display:flex; align-items:center; gap:.75rem; }
  .alumni-shell-avatar { border:2px solid rgb(255 255 255 / 0.22); box-shadow:0 12px 24px -14px rgb(2 6 23 / 0.8); overflow:hidden; }
  .alumni-shell-meta { min-width:0; }
  .alumni-shell-meta > * { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
  .sidebar-nav { flex:1; display:grid; align-content:start; gap:.2rem; padding:.8rem; overflow-y:auto; }
  .sidebar-link { position:relative; border-radius:.85rem; border:1px solid transparent; padding:.64rem .76rem; color:rgb(241 245 249); text-decoration:none; font-size:.86rem; font-weight:600; letter-spacing:.01em; transition:background .16s,border-color .16s,color .16s,transform .16s; display:flex; align-items:center; gap:.75rem; }
  .sidebar-link:hover { background:rgb(255 255 255 / 0.18); color:#fff; border-color:rgb(255 255 255 / 0.14); transform:translateX(1px); }
  .sidebar-link.active { background:linear-gradient(135deg,rgb(16 185 129 / 0.35),rgb(16 185 129 / 0.15)); color:#fff; border-color:rgb(16 185 129 / 0.5); box-shadow:inset 0 1px 0 rgb(255 255 255 / 0.22); }
  .sidebar-link svg { flex-shrink:0; }
  .sidebar-badge { position:absolute; top:.5rem; right:.75rem; padding:.15rem .45rem; font-size:.65rem; font-weight:700; letter-spacing:.05em; text-transform:uppercase; background:linear-gradient(135deg,#f59e0b,#f97316); color:white; border-radius:.35rem; box-shadow:0 2px 8px -2px rgb(245 158 11 / 0.6); }
  .sidebar-footer { padding:.8rem; border-top:1px solid rgb(255 255 255 / 0.15); background:rgba(0,0,0,.15); }
  .alumni-shell-logout { display:flex; align-items:center; gap:.75rem; width:100%; padding:.75rem .85rem; border-radius:.75rem; border:1px solid rgba(239,68,68,.3); background:linear-gradient(135deg,rgba(239,68,68,.15),rgba(220,38,38,.1)); color:#fecaca; font-size:.9rem; font-weight:600; text-decoration:none; cursor:pointer; transition:all .2s; }
  .alumni-shell-logout:hover { background:linear-gradient(135deg,rgba(239,68,68,.25),rgba(220,38,38,.2)); border-color:rgba(239,68,68,.5); color:#fff; transform:translateX(2px); }
  .alumni-shell-logout svg { flex-shrink:0; width:20px; height:20px; }
  .dashboard-layout .main-content { flex:1 !important; display:flex !important; flex-direction:column !important; min-width:0; margin-left:286px !important; background:linear-gradient(135deg,#f0f9ff 0%,#e0f2fe 50%,#f0fdf4 100%); min-height:100vh; }
  .content-wrapper { padding:1rem; max-width:1400px; margin:0 auto; width:100%; }
  .topbar { background:white; border-bottom:1px solid #e5e7eb; padding:1.25rem 2rem; display:flex; align-items:center; justify-content:space-between; box-shadow:0 1px 3px rgba(0,0,0,.05); }
  .page-title { font-size:1.75rem; font-weight:800; color:#047857; margin:0; }
  .topbar-actions { display:flex; gap:.75rem; }
  .card { background:white; border-radius:16px; border:1px solid #e5e7eb; box-shadow:0 4px 6px -1px rgba(0,0,0,.05); overflow:hidden; transition:all .3s; }
  .card:hover { box-shadow:0 10px 15px -3px rgba(0,0,0,.1); transform:translateY(-2px); }
  .card-body { padding:1.75rem; }
  .mb-lg { margin-bottom:1rem; }
  .btn { display:inline-flex; align-items:center; gap:.5rem; padding:.75rem 1.5rem; border-radius:10px; font-weight:600; font-size:.95rem; border:none; cursor:pointer; transition:all .2s; text-decoration:none; }
  .btn-primary { background:linear-gradient(135deg,#047857,#059669); color:white; box-shadow:0 4px 12px rgba(4,120,87,.3); }
  .btn-primary:hover { background:linear-gradient(135deg,#059669,#047857); transform:translateY(-2px); }
  .btn-secondary { background:white; color:#047857; border:2px solid #047857; }
  .btn-secondary:hover { background:#047857; color:white; transform:translateY(-2px); }
  .btn-outline { background:white; color:#6b7280; border:1px solid #d1d5db; }
  .btn-outline:hover { background:#f9fafb; border-color:#047857; color:#047857; }
  .btn-sm { padding:.5rem .75rem; font-size:.875rem; }
  .w-100 { width:100%; }
  .flex { display:flex; }
  .flex-1 { flex:1; }
  .items-start { align-items:flex-start; }
  .gap-md { gap:1rem; }
  .text-lg { font-size:1.125rem; }
  .font-bold { font-weight:700; }
  .mb-sm { margin-bottom:.5rem; }
  .text-secondary { color:#6b7280; }
  .text-primary { color:#047857; }
  .icon-circle { width:48px; height:48px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
  .bg-primary-light { background:rgba(4,120,87,.1); }
  .grid { display:grid; }
  .grid-cols-3 { grid-template-columns:repeat(3,minmax(0,1fr)); }
  .avatar { display:inline-flex; align-items:center; justify-content:center; border-radius:50%; background-color:rgb(16 185 129); color:white; font-weight:600; overflow:hidden; }
  .avatar img { width:100%; height:100%; object-fit:cover; }
  .avatar-md { width:2.5rem; height:2.5rem; font-size:.875rem; }
  .bg-primary { background-color:rgb(16 185 129); }
</style>

<!-- Link to shared ID card CSS -->
<link rel="stylesheet" href="assets/css/id-card-design.css?v=20260518.08">

<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content">
    <header class="topbar">
      <h1 class="page-title">My Alumni ID Card</h1>
      <div class="topbar-actions">
        <button class="btn btn-primary" id="printIdBtnTop">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="6 9 6 2 18 2 18 9"></polyline>
            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
            <rect x="6" y="14" width="12" height="8"></rect>
          </svg>
          Print
        </button>
        <button class="btn btn-secondary" id="downloadIdBtnTop">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
            <polyline points="7 10 12 15 17 10"></polyline>
            <line x1="12" y1="15" x2="12" y2="3"></line>
          </svg>
          Download
        </button>
      </div>
    </header>

    <div class="content-wrapper">

      <!-- Info banner -->
      <div class="card mb-lg">
        <div class="card-body">
          <div class="flex items-start gap-md">
            <div class="icon-circle bg-primary-light text-primary">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
              </svg>
            </div>
            <div class="flex-1">
              <h3 class="text-lg font-bold mb-sm">About Your Alumni ID Card</h3>
              <p class="text-secondary mb-sm">
                This is your official electronic alumni identification card. Use it for event check-ins, alumni status verification, and access to alumni-exclusive services.
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- Card + Actions -->
      <div class="flex gap-md" style="align-items: flex-start;">

        <!-- ID Card display -->
        <div class="flex-1">
          <div class="id-card-container">
            <div class="id-card-wrapper" id="idCardWrapper">

              <!-- ═══════════════════════════════════
                   FRONT CARD NEW DESIGN
              ═══════════════════════════════════ -->
              <div class="id-card-front new-design">
                <div class="front-watermark">
                  <img src="/assets/images/logo.svg" onerror="this.style.display='none'">
                </div>
                <div class="front-green-bg">
                  <div class="front-green-dots"></div>
                  <div class="front-green-lines"></div>
                </div>

                <div class="front-header-new">
                  <h2 id="institutionName">MINDORO STATE UNIVERSITY</h2>
                  <p id="institutionTagline">ALUMNI ASSOCIATION</p>
                  <div class="line"></div>
                </div>

                <div class="front-photo-container">
                  <div class="front-photo">
                    <img src="" alt="Profile" id="cardPhoto" style="display:none;">
                    <div class="photo-placeholder" id="cardPhotoPlaceholder">
                      <span id="cardInitials">A</span>
                    </div>
                  </div>
                  <div class="front-name-under">
                    <div class="field-name-label-under">NAME</div>
                    <div class="field-name-value-under" id="cardName">ALUMNI NAME</div>
                  </div>
                </div>

                <div class="front-right-new">
                  <img src="/assets/images/logo.svg" alt="Logo" class="front-seal-new" id="institutionLogoBadge" onerror="this.style.display='none'">
                  
                  <div class="front-info-new">
                    <div>
                      <div class="field-alumni-id-label">ALUMNI ID</div>
                      <div class="field-alumni-id-value" id="cardAlumniId">BBC-2024-CCS-00001</div>
                    </div>
                    
                    <div class="field-row class-year">Class of <span id="cardGradYear">2024</span></div>
                    
                    <div class="field-row">
                      <span class="label">Student ID</span>
                      <span class="value" id="cardStudentIdFront">2024-00156</span>
                    </div>
                    
                    <div class="field-row">
                      <span class="value" id="cardCollegeFront">College of Engineering</span>
                    </div>

                    <div class="field-row">
                      <span class="label">Program</span>
                      <span class="value" id="cardProgramFront">BS Computer Science</span>
                    </div>

                    <div class="field-row">
                      <span class="label">Section</span>
                      <span class="value" id="cardSectionFront">A</span>
                    </div>
                  </div>
                </div>
              </div><!-- /id-card-front -->

              <!-- ═══════════════════════════════════
                   BACK CARD
              ═══════════════════════════════════ -->
              <div class="id-card-back font-sans">
                
                <div class="back-header">
                  <h3 class="back-title">ALUMNI INFORMATION</h3>
                  <div class="valid-badge">VALID</div>
                </div>

                <div class="back-body">
                  <div class="back-watermark">
                    <img src="/assets/images/logo.svg" alt="Watermark" onerror="this.style.display='none'">
                  </div>

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

              </div><!-- /id-card-back -->

            </div><!-- /id-card-wrapper -->
          </div><!-- /id-card-container -->
        </div><!-- /flex-1 -->

        <!-- Actions sidebar -->
        <div style="width:190px; flex-shrink:0;">
          <div class="card">
            <div class="card-body" style="padding:.9rem;">
              <h4 style="font-size:.85rem; font-weight:700; margin:0 0 .65rem; color:#047857;">Actions</h4>
              <div style="display:flex; flex-direction:column; gap:.45rem;">
                <button class="btn btn-sm btn-outline w-100" id="viewFrontBtn" style="font-size:.82rem; padding:.45rem;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                  </svg>
                  View Front
                </button>
                <button class="btn btn-sm btn-outline w-100" id="viewBackBtn" style="font-size:.82rem; padding:.45rem;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                  </svg>
                  View Back
                </button>
                <button class="btn btn-sm btn-primary w-100" id="printIdBtn" style="font-size:.82rem; padding:.45rem;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                  </svg>
                  Print
                </button>
                <button class="btn btn-sm btn-secondary w-100" id="downloadIdBtn" style="font-size:.82rem; padding:.45rem;">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
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

      </div><!-- /flex gap-md -->
    </div><!-- /content-wrapper -->
  </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js" crossorigin="anonymous"></script>
<script>
(function() {
  'use strict';

  let idCardData = null;
  let qrCodeInstance = null;

  function waitForQRCode(callback, maxAttempts = 30) {
    let attempts = 0;
    const check = () => {
      attempts++;
      if (typeof QRCode !== 'undefined') { callback(); return; }
      if (attempts >= maxAttempts) { callback(); return; }
      setTimeout(check, 100);
    };
    check();
  }

  async function loadIdCard() {
    try {
      const response = await API.alumni.getIdCard();
      if (response && response.data) {
        idCardData = response.data;
        if (idCardData.profile_image) {
          const storedUser = API.getUser() || {};
          const nextUser = { ...storedUser, profile_image: idCardData.profile_image };
          API.setUser(nextUser);
          if (typeof Auth !== 'undefined') Auth.user = nextUser;
        }
        renderCard(idCardData);
      } else {
        throw new Error('Invalid response');
      }
    } catch (error) {
      const user = API.getUser() || {};
      idCardData = createMockData(user);
      renderCard(idCardData);
    }
  }

  function createMockData(user) {
    const alumniId = user.alumni_id || 'BBC-2024-CCS-00001';
    const name = user.name || 'Sample Alumni';
    return {
      alumni_id: alumniId,
      student_id: user.student_id || 'STU-2020-12345',
      name: name,
      email: user.email || 'alumni@example.com',
      phone: user.phone || '(123) 456-7890',
      address: user.address || '',
      status: user.status || 'active',
      profile_image: user.profile_image || null,
      college_name: 'College of Engineering',
      program_name: 'BS Computer Science',
      program_code: 'BSCS',
      section_name: 'A',
      graduation_year: '2024',
      qr_code_data: JSON.stringify({ type: 'alumni_id', alumni_id: alumniId, name, ts: Date.now() }),
      issued_date: new Date().toISOString(),
      created_at: user.created_at || new Date().toISOString()
    };
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
    const photoImg = document.getElementById('cardPhoto');
    const photoPlaceholder = document.getElementById('cardPhotoPlaceholder');
    const initialsEl = document.getElementById('cardInitials');
    const initials = (typeof Utils !== 'undefined')
      ? Utils.getInitials(name || 'Alumni')
      : (name || 'A').split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
    const candidates = getImageUrlCandidates(imageUrl);
    let index = 0;

    if (initialsEl) initialsEl.textContent = initials;

    function showPlaceholder() {
      if (photoImg) {
        photoImg.removeAttribute('src');
        photoImg.style.display = 'none';
      }
      if (photoPlaceholder) {
        photoPlaceholder.style.display = 'flex';
      }
    }

    function tryNext() {
      if (!photoImg || !photoPlaceholder || index >= candidates.length) {
        showPlaceholder();
        return;
      }

      const candidate = candidates[index];
      index += 1;

      photoImg.onload = () => {
        photoImg.style.display = 'block';
        photoPlaceholder.style.display = 'none';
      };
      photoImg.onerror = () => {
        console.warn('Failed to load profile image:', candidate);
        tryNext();
      };
      photoImg.style.display = 'none';
      photoPlaceholder.style.display = 'flex';
      photoImg.src = candidate;
    }

    if (candidates.length) {
      tryNext();
    } else {
      showPlaceholder();
    }
  }

  function renderCard(data) {
    // Institution name
    const branding = window.App?.getBrandingSnapshot?.() || {};
    const instEl = document.getElementById('institutionName');
    if (instEl) instEl.textContent = branding.institutionName || data.institution_name || 'Mindoro State University';

    // Front fields
    const nameEl = document.getElementById('cardName');
    if (nameEl) nameEl.textContent = formatCardName(data).toUpperCase();

    document.getElementById('cardStudentIdFront').textContent = data.student_id || '-';
    document.getElementById('cardAlumniId').textContent       = data.alumni_id || '-';
    document.getElementById('cardGradYear').textContent       = data.graduation_year || '-';
    document.getElementById('cardProgramFront').textContent   = data.program_name || data.program_code || '-';
    document.getElementById('cardSectionFront').textContent   = data.section_name || '-';
    const collegeFront = document.getElementById('cardCollegeFront');
    if (collegeFront) collegeFront.textContent = data.college_name || data.college_code || '-';

    // Back fields
    document.getElementById('cardEmail').textContent     = data.email || '-';
    document.getElementById('cardPhone').textContent     = data.phone || '-';
    document.getElementById('cardMemberSince').textContent = data.member_since || yearFromDate(data.created_at) || '-';
    document.getElementById('cardIssuedDate').textContent = formatDate(data.issued_date);
    document.getElementById('cardStatus').textContent = formatStatus(data.status || 'active');
    document.getElementById('cardAddress').textContent = data.address || data.address_city || '-';

    const currentUser = API.getUser() || {};
    const profileImage = data.profile_image || currentUser.profile_image;
    renderProfilePhoto(profileImage, data.name || currentUser.name);

    generateQRCode(data.qr_code_data);
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

  function generateQRCode(qrData) {
    const qrContainer = document.getElementById('qrCodeCanvas');
    if (!qrData) {
      qrContainer.innerHTML = '<div style="padding:.75rem;text-align:center;color:#6b7280;font-size:.7rem;">No QR data</div>';
      return;
    }
    if (typeof QRCode === 'undefined') {
      qrContainer.innerHTML = '<div style="padding:.75rem;text-align:center;color:#6b7280;font-size:.7rem;">QR unavailable</div>';
      return;
    }
    try {
      qrContainer.innerHTML = '';
      qrCodeInstance = new QRCode(qrContainer, {
        text: qrData,
        width: 108, height: 108,
        colorDark: '#000000', colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M
      });
    } catch (e) {
      qrContainer.innerHTML = '<div style="padding:.75rem;text-align:center;color:#ef4444;font-size:.7rem;">QR error</div>';
    }
  }

  function setupEventListeners() {
    const wrapper = document.getElementById('idCardWrapper');

    console.log('[ID Card] Setting up event listeners - v20260510031332');

    document.getElementById('viewFrontBtn')?.addEventListener('click', () => wrapper?.classList.remove('flipped'));
    document.getElementById('viewBackBtn')?.addEventListener('click',  () => wrapper?.classList.add('flipped'));

    ['printIdBtn', 'printIdBtnTop'].forEach(id =>
      document.getElementById(id)?.addEventListener('click', () => {
        console.log('[ID Card] Opening dedicated print preview');

        if (window.IdCardPrinter && typeof window.IdCardPrinter.print === 'function') {
          window.IdCardPrinter.print({ title: 'My Alumni ID Card' });
          return;
        }

        window.print();
      })
    );

    ['downloadIdBtn', 'downloadIdBtnTop'].forEach(id =>
      document.getElementById(id)?.addEventListener('click', () => {
        if (typeof Utils !== 'undefined') Utils.info('Download coming soon! Use Print → Save as PDF.');
        else alert('Download coming soon! Use Print → Save as PDF.');
      })
    );
  }

  function initialize() {
    waitForQRCode(() => {
      loadIdCard();
      setupEventListeners();
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialize);
  } else {
    initialize();
  }
})();
</script>
