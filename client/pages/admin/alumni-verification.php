<!-- Alumni Verification Page -->
<link rel="stylesheet" href="/assets/css/admin-pages-improved.css">
<link rel="stylesheet" href="/assets/css/admin-premium.css">

<div class="dashboard-layout">
    <aside class="sidebar" id="sidebar"></aside>

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
            <h1 class="page-title">Alumni Verification</h1>
            <div class="flex-1"></div>
        </header>

        <div class="admin-content p-lg">
            <div class="verification-container">
                <!-- Enhanced Statistics Cards -->
                <div class="grid grid-cols-4 gap-md mb-lg">
                    <div class="stat-card-improved">
                        <div class="stat-icon bg-warning">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value" id="pendingCount">0</div>
                            <div class="stat-label">Pending</div>
                        </div>
                    </div>
                    <div class="stat-card-improved">
                        <div class="stat-icon bg-success">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                <polyline points="22 4 12 14.01 9 11.01"></polyline>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value" id="verifiedToday">0</div>
                            <div class="stat-label">Verified Today</div>
                        </div>
                    </div>
                    <div class="stat-card-improved">
                        <div class="stat-icon bg-primary">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="8.5" cy="7" r="4"></circle>
                                <polyline points="17 11 19 13 23 9"></polyline>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value" id="verifiedCount">0</div>
                            <div class="stat-label">Total Verified</div>
                        </div>
                    </div>
                    <div class="stat-card-improved">
                        <div class="stat-icon bg-danger">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <div class="stat-value" id="rejectedCount">0</div>
                            <div class="stat-label">Rejected</div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Table -->
                <div class="card-improved">
                    <div class="card-header">
                        <h3 class="card-title">Pending Registrations</h3>
                        <button class="btn-icon btn-icon-sm btn-ghost" onclick="refreshData()" title="Refresh">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="1 4 1 10 7 10"></polyline>
                                <polyline points="23 20 23 14 17 14"></polyline>
                                <path d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 0 1 3.51 15"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Filters -->
                        <div class="flex gap-md mb-lg" style="flex-wrap: wrap;">
                            <input type="text" id="alumniSearch" class="form-input" placeholder="Search by name or email..." style="max-width: 300px;" oninput="filterAlumni()">
                            <select id="collegeFilterVerif" class="form-input" onchange="filterAlumni()" style="max-width: 200px;">
                                <option value="">All Colleges</option>
                            </select>
                            <select id="programFilterVerif" class="form-input" onchange="filterAlumni()" style="max-width: 200px;">
                                <option value="">All Programs</option>
                            </select>
                        </div>
                        <div style="overflow-x: auto;">
                            <table class="table-improved">
                                <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>College</th>
                                        <th>Program</th>
                                        <th>Batch</th>
                                        <th>Registered</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="pendingAlumniList">
                                    <tr>
                                        <td colspan="8" class="text-center">Loading...</td>
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

<!-- Enhanced Verification Modal -->
<div class="modal-improved" id="verificationModal" aria-hidden="true">
    <div class="modal-backdrop"></div>
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Verify Alumni</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="alumni-details" id="alumniDetails"></div>
                <div class="form-improved">
                    <div class="form-group">
                        <label class="form-label" for="actionNotes">Notes / Reason</label>
                        <textarea
                            id="actionNotes"
                            class="form-textarea"
                            rows="4"
                            placeholder="Enter notes or rejection reason..."
                        ></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button
                    class="btn-icon btn-success"
                    id="confirmVerifyBtn"
                    style="display: none;"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Confirm Verify
                </button>
                <button
                    class="btn-icon btn-danger"
                    id="confirmRejectBtn"
                    style="display: none;"
                >
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    Confirm Reject
                </button>
            </div>
        </div>
    </div>
</div>



<script>
    (function () {
        const user = API.getUser();

        if (!user || !["admin", "system_admin"].includes(user.role)) {
            Utils.error("Admin access required");
            Router.navigate("/admin/login");
            return;
        }

        let currentAlumniId = null;
        let allAlumni = [];
        let filteredAlumni = [];

        function escapeHtml(text) {
            const div = document.createElement("div");
            div.textContent = text || "";
            return div.innerHTML.replace(/'/g, "&#39;");
        }

        async function loadStats() {
            try {
                const response = await API.verification.getStats();
                const stats = response.data || {};

                document.getElementById("pendingCount").textContent = stats.pending || 0;
                document.getElementById("verifiedToday").textContent =
                    stats.verified_today || 0;
                document.getElementById("verifiedCount").textContent =
                    stats.verified || 0;
                document.getElementById("rejectedCount").textContent =
                    stats.rejected || 0;
            } catch (error) {
                console.error("Error loading stats:", error);
            }
        }

        async function loadPendingAlumni() {
            try {
                const response = await API.verification.getPending();
                allAlumni = response.data || [];
                filteredAlumni = [...allAlumni];

                // Populate filters
                populateFilters();
                
                renderAlumni();
            } catch (error) {
                console.error("Error loading pending alumni:", error);
                document.getElementById("pendingAlumniList").innerHTML =
                    '<tr><td colspan="8" class="text-center text-danger">Error loading data. Please refresh the page.</td></tr>';
            }
        }

        function populateFilters() {
            // Get unique colleges and programs
            const colleges = [...new Set(allAlumni.map(a => a.college_name).filter(Boolean))];
            const programs = [...new Set(allAlumni.map(a => a.program_name).filter(Boolean))];

            const collegeFilter = document.getElementById("collegeFilterVerif");
            const programFilter = document.getElementById("programFilterVerif");

            collegeFilter.innerHTML = '<option value="">All Colleges</option>' +
                colleges.map(c => `<option value="${escapeHtml(c)}">${escapeHtml(c)}</option>`).join('');

            programFilter.innerHTML = '<option value="">All Programs</option>' +
                programs.map(p => `<option value="${escapeHtml(p)}">${escapeHtml(p)}</option>`).join('');
        }

        window.filterAlumni = function() {
            const searchTerm = document.getElementById("alumniSearch").value.toLowerCase();
            const collegeFilter = document.getElementById("collegeFilterVerif").value;
            const programFilter = document.getElementById("programFilterVerif").value;

            filteredAlumni = allAlumni.filter(a => {
                const matchesSearch = !searchTerm || 
                    (a.name && a.name.toLowerCase().includes(searchTerm)) ||
                    (a.email && a.email.toLowerCase().includes(searchTerm));
                
                const matchesCollege = !collegeFilter || a.college_name === collegeFilter;
                const matchesProgram = !programFilter || a.program_name === programFilter;

                return matchesSearch && matchesCollege && matchesProgram;
            });

            renderAlumni();
        };

        window.refreshData = function() {
            loadStats();
            loadPendingAlumni();
            Utils.success("Data refreshed");
        };

        function renderAlumni() {
            const tbody = document.getElementById("pendingAlumniList");
            const alumni = filteredAlumni;

            if (!alumni.length) {
                tbody.innerHTML =
                    '<tr><td colspan="8" class="text-center"><div class="empty-state"><div class="empty-state-icon"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="17" y1="11" x2="23" y2="11"></line></svg></div><div class="empty-state-title">No pending registrations</div><div class="empty-state-description">All alumni registrations have been processed</div></div></td></tr>';
                return;
            }

            tbody.innerHTML = alumni
                .map(
                    (a) => `
                        <tr>
                            <td>
                                ${a.profile_photo_url 
                                    ? `<img src="${escapeHtml(a.profile_photo_url)}" alt="${escapeHtml(a.name)}" class="alumni-photo" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                       <div class="alumni-photo-placeholder" style="display:none;">${(a.name || 'A').charAt(0).toUpperCase()}</div>`
                                    : `<div class="alumni-photo-placeholder">${(a.name || 'A').charAt(0).toUpperCase()}</div>`
                                }
                            </td>
                            <td><strong>${escapeHtml(a.name) || "N/A"}</strong></td>
                            <td class="text-muted">${escapeHtml(a.email) || "N/A"}</td>
                            <td>${escapeHtml(a.college_name || "N/A")}</td>
                            <td>${escapeHtml(a.program_name || "N/A")}</td>
                            <td>${escapeHtml(a.batch_year || "N/A")}</td>
                            <td>${a.created_at ? new Date(a.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : "-"}</td>
                            <td>
                                <div class="action-buttons">
                                    <button
                                        type="button"
                                        class="btn-icon btn-icon-sm btn-success"
                                        data-action="verify"
                                        data-id="${a.id}"
                                        data-name="${escapeHtml(a.name)}"
                                        data-email="${escapeHtml(a.email)}"
                                        data-college="${escapeHtml(a.college_name || "N/A")}" 
                                        data-program="${escapeHtml(a.program_name || "N/A")}" 
                                        data-batch="${escapeHtml(a.batch_year || "N/A")}"
                                        data-photo="${escapeHtml(a.profile_photo_url || '')}"
                                        title="Verify">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </button>
                                    <button
                                        type="button"
                                        class="btn-icon btn-icon-sm btn-danger"
                                        data-action="reject"
                                        data-id="${a.id}"
                                        data-name="${escapeHtml(a.name)}"
                                        data-email="${escapeHtml(a.email)}"
                                        data-college="${escapeHtml(a.college_name || "N/A")}" 
                                        data-program="${escapeHtml(a.program_name || "N/A")}" 
                                        data-batch="${escapeHtml(a.batch_year || "N/A")}"
                                        data-photo="${escapeHtml(a.profile_photo_url || '')}"
                                        title="Reject">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <line x1="18" y1="6" x2="6" y2="18"></line>
                                            <line x1="6" y1="6" x2="18" y2="18"></line>
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `,
                )
                .join("");
        }

        function showModal(action, payload) {
            currentAlumniId = Number(payload.id);
            const modal = document.getElementById("verificationModal");
            const title = document.getElementById("modalTitle");
            const details = document.getElementById("alumniDetails");
            const notes = document.getElementById("actionNotes");

            const photoHtml = payload.photo 
                ? `<div style="text-align: center; margin-bottom: 1rem;"><img src="${payload.photo}" alt="${payload.name}" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid #e5e7eb;"></div>`
                : `<div style="text-align: center; margin-bottom: 1rem;"><div style="width: 100px; height: 100px; border-radius: 50%; background: linear-gradient(135deg, #047857, #059669); color: white; display: inline-flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 700;">${(payload.name || 'A').charAt(0).toUpperCase()}</div></div>`;

            details.innerHTML = `
                ${photoHtml}
                <div style="background: #f9fafb; padding: 1rem; border-radius: 8px;">
                    <p style="margin: 0.5rem 0;"><strong>Name:</strong> ${payload.name || "N/A"}</p>
                    <p style="margin: 0.5rem 0;"><strong>Email:</strong> ${payload.email || "N/A"}</p>
                    <p style="margin: 0.5rem 0;"><strong>College:</strong> ${payload.college || "N/A"}</p>
                    <p style="margin: 0.5rem 0;"><strong>Program:</strong> ${payload.program || "N/A"}</p>
                    <p style="margin: 0.5rem 0;"><strong>Batch:</strong> ${payload.batch || "N/A"}</p>
                </div>
            `;

            notes.value = "";

            const verifyBtn = document.getElementById("confirmVerifyBtn");
            const rejectBtn = document.getElementById("confirmRejectBtn");

            if (action === "verify") {
                title.textContent = "Verify Alumni";
                notes.placeholder = "Enter verification notes (optional)...";
                verifyBtn.style.display = "inline-flex";
                rejectBtn.style.display = "none";
            } else {
                title.textContent = "Reject Alumni Registration";
                notes.placeholder = "Enter rejection reason (required)...";
                verifyBtn.style.display = "none";
                rejectBtn.style.display = "inline-flex";
            }

            modal.classList.add("active");
            modal.setAttribute("aria-hidden", "false");
            document.body.classList.add("modal-open");
            window.setTimeout(() => notes.focus(), 0);
        }

        function closeModal() {
            const modal = document.getElementById("verificationModal");
            modal.classList.remove("active");
            modal.setAttribute("aria-hidden", "true");
            document.body.classList.remove("modal-open");
            currentAlumniId = null;
        }

        // Make closeModal globally accessible for inline onclick handlers
        window.closeModal = closeModal;

        document.getElementById("pendingAlumniList").addEventListener("click", (event) => {
            const button = event.target.closest("button[data-action]");
            if (!button) {
                return;
            }

            const payload = {
                id: button.dataset.id,
                name: button.dataset.name,
                email: button.dataset.email,
                college: button.dataset.college,
                program: button.dataset.program,
                batch: button.dataset.batch,
                photo: button.dataset.photo,
            };

            showModal(button.dataset.action, payload);
        });

        document.getElementById("confirmVerifyBtn").addEventListener("click", async () => {
            const notes = document.getElementById("actionNotes").value.trim();

            try {
                await API.verification.verify(currentAlumniId, notes || null);
                Utils.success("Alumni verified successfully.");
                closeModal();
                loadPendingAlumni();
                loadStats();
            } catch (error) {
                console.error("Error verifying alumni:", error);
                Utils.error("Failed to verify alumni. Please try again.");
            }
        });

        document.getElementById("confirmRejectBtn").addEventListener("click", async () => {
            const reason = document.getElementById("actionNotes").value.trim();

            if (!reason) {
                Utils.error("Rejection reason is required.");
                return;
            }

            try {
                await API.verification.reject(currentAlumniId, reason);
                Utils.success("Alumni registration rejected.");
                closeModal();
                loadPendingAlumni();
                loadStats();
            } catch (error) {
                console.error("Error rejecting alumni:", error);
                Utils.error("Failed to reject alumni. Please try again.");
            }
        });

        document.getElementById("verificationModal").addEventListener("click", (event) => {
            if (event.target.classList.contains("modal-backdrop")) {
                closeModal();
            }
        });

        loadStats();
        loadPendingAlumni();

        setInterval(() => {
            loadStats();
            loadPendingAlumni();
        }, 30000);
    })();
</script>

