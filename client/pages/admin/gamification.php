<!-- Admin Gamification -->
<link rel="stylesheet" href="/assets/css/dashboard-improvements.css">
<link rel="stylesheet" href="/assets/css/admin-premium.css">

<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content">
    <header class="content-header">
      <h1>Gamification</h1>
    </header>

    <div class="admin-content">
      <!-- Stats Cards -->
      <div class="stats-grid" id="statsGrid">
        <div class="stat-card-improved">
          <div class="stat-icon">🏆</div>
          <div class="stat-content">
            <h3 id="totalEarned">0</h3>
            <p>Total Points Earned</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">🎁</div>
          <div class="stat-content">
            <h3 id="totalRedeemed">0</h3>
            <p>Points Redeemed</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">⭐</div>
          <div class="stat-content">
            <h3 id="activeRewards">0</h3>
            <p>Active Rewards</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">📋</div>
          <div class="stat-content">
            <h3 id="pendingRedemptions">0</h3>
            <p>Pending Redemptions</p>
          </div>
        </div>
      </div>

      <div class="tabs">
        <button class="tab-btn active" onclick="switchTab('rewards')">
          Rewards
        </button>
        <button class="tab-btn" onclick="switchTab('redemptions')">
          Redemptions
        </button>
        <button class="tab-btn" onclick="switchTab('leaderboard')">
          Leaderboard
        </button>
        <button class="tab-btn" onclick="switchTab('adjust')">
          Adjust Points
        </button>
      </div>

      <!-- Rewards Tab -->
      <div class="tab-content active" id="rewardsTab">
        <div class="card-improved">
          <div class="card-header">
            <h3>Rewards</h3>
            <button class="btn btn-primary btn-sm" onclick="showRewardModal()">
              + Add Reward
            </button>
          </div>
          <div class="table-container">
            <table class="table-improved">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Points Cost</th>
                  <th>Available</th>
                  <th>Redeemed</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="rewardsTable">
                <tr>
                  <td colspan="6" class="loading">Loading...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Redemptions Tab -->
      <div class="tab-content" id="redemptionsTab">
        <div class="card-improved">
          <div class="card-header">
            <h3>Reward Redemptions</h3>
            <select id="redemptionFilter" onchange="loadRedemptions()">
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="approved">Approved</option>
              <option value="claimed">Claimed</option>
              <option value="rejected">Rejected</option>
            </select>
          </div>
          <div class="table-container">
            <table class="table-improved">
              <thead>
                <tr>
                  <th>Alumni</th>
                  <th>Reward</th>
                  <th>Points</th>
                  <th>Status</th>
                  <th>Date</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="redemptionsTable">
                <tr>
                  <td colspan="6" class="loading">Loading...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Leaderboard Tab -->
      <div class="tab-content" id="leaderboardTab">
        <div class="card-improved">
          <div class="card-header">
            <h3>Top Alumni</h3>
          </div>
          <div class="table-container">
            <table class="table-improved">
              <thead>
                <tr>
                  <th>Rank</th>
                  <th>Alumni</th>
                  <th>Points</th>
                  <th>Badge</th>
                </tr>
              </thead>
              <tbody id="leaderboardTable">
                <tr>
                  <td colspan="4" class="loading">Loading...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Adjust Points Tab -->
      <div class="tab-content" id="adjustTab">
        <div class="card-improved">
          <div class="card-header">
            <h3>Adjust Alumni Points</h3>
          </div>
          <div class="card-body">
            <form id="adjustForm" onsubmit="adjustPoints(event)">
              <div class="form-group">
                <label>Search Alumni</label>
                <input
                  type="text"
                  id="alumniSearch"
                  placeholder="Search by name, email, or alumni ID"
                  oninput="searchAlumni()"
                />
                <div id="alumniResults" class="search-results"></div>
              </div>
              <div class="form-group">
                <label>Selected Alumni</label>
                <div id="selectedAlumni" class="selected-item">
                  No alumni selected
                </div>
                <input type="hidden" id="selectedUserId" />
              </div>
              <div class="form-row">
                <div class="form-group">
                  <label>Points (+/-)</label>
                  <input type="number" id="adjustAmount" required />
                </div>
                <div class="form-group">
                  <label>Reason</label>
                  <input
                    type="text"
                    id="adjustReason"
                    placeholder="Reason for adjustment"
                  />
                </div>
              </div>
              <button type="submit" class="btn btn-primary">
                Adjust Points
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<!-- Reward Modal -->
<div class="modal reward-modal" id="rewardModal" aria-hidden="true">
  <div class="modal-backdrop" data-reward-modal-close></div>
  <div class="modal-dialog reward-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="rewardModalTitle">
    <div class="modal-content reward-modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="rewardModalTitle">Add Reward</h2>
        <button
          type="button"
          class="modal-close"
          aria-label="Close reward modal"
          onclick="closeModal()"
        >
          &times;
        </button>
      </div>
      <form id="rewardForm" class="reward-form" onsubmit="saveReward(event)">
        <div class="modal-body reward-modal-body">
          <div class="form-group">
            <label class="form-label" for="rewardName">Name <span aria-hidden="true">*</span></label>
            <input class="form-input" type="text" id="rewardName" required />
          </div>
          <div class="form-group">
            <label class="form-label" for="rewardDescription">Description</label>
            <textarea class="form-textarea" id="rewardDescription" rows="4"></textarea>
          </div>
          <div class="reward-form-grid">
            <div class="form-group">
              <label class="form-label" for="rewardCost">Points Cost <span aria-hidden="true">*</span></label>
              <input class="form-input" type="number" id="rewardCost" required min="1" step="1" />
            </div>
            <div class="form-group">
              <label class="form-label" for="rewardQuantity">Quantity Available</label>
              <input
                class="form-input"
                type="number"
                id="rewardQuantity"
                min="0"
                step="1"
                placeholder="Unlimited"
              />
            </div>
          </div>
          <div class="form-group">
            <label class="form-label" for="rewardImage">Image URL</label>
            <input class="form-input" type="url" id="rewardImage" placeholder="https://..." />
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="closeModal()">
            Cancel
          </button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
        <input type="hidden" id="rewardId" />
      </form>
    </div>
  </div>
</div>

<script>
  var rewards = [];
  var searchTimeout;

  var showToast = (message, type = "info") => Utils.toast(message, type);
  var escapeHtml = (value) => Utils.escapeHtml(String(value ?? ""));
  var formatDate = (value) => (value ? Utils.formatDate(value, "short") : "-");

  (async function initPage() {
    if (!Auth.isAdmin()) {
      window.location.hash = "#/admin/login";
      return;
    }

    document
      .querySelectorAll("[data-reward-modal-close]")
      .forEach((element) => element.addEventListener("click", closeModal));

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        closeModal();
      }
    });

    await Promise.all([
      loadStats(),
      loadRewards(),
      loadRedemptions(),
      loadLeaderboard(),
    ]);
  })();

  function switchTab(tab) {
    document
      .querySelectorAll(".tab-btn")
      .forEach((b) => b.classList.remove("active"));
    document
      .querySelectorAll(".tab-content")
      .forEach((c) => c.classList.remove("active"));

    document
      .querySelector(`[onclick="switchTab('${tab}')"]`)
      .classList.add("active");
    document.getElementById(tab + "Tab").classList.add("active");

    if (tab === "redemptions") loadRedemptions();
    else if (tab === "leaderboard") loadLeaderboard();
  }

  async function loadStats() {
    try {
      const response = await API.admin.getGamificationPoints();
      if (response.success) {
        const data = response.data;
        document.getElementById("totalEarned").textContent =
          data.total_earned?.toLocaleString() || 0;
        document.getElementById("totalRedeemed").textContent =
          data.total_redeemed?.toLocaleString() || 0;
      }
    } catch (e) {
      console.error("Error loading stats:", e);
    }
  }

  async function loadRewards() {
    try {
      const response = await API.admin.getRewards();
      if (response.success) {
        rewards = Array.isArray(response.data) ? response.data : [];
        renderRewards();
        document.getElementById("activeRewards").textContent = rewards.filter(
          (r) => r.status === "active",
        ).length;
      }
    } catch (e) {
      showToast("Error loading rewards", "error");
    }
  }

  function renderRewards() {
    const tbody = document.getElementById("rewardsTable");
    if (!rewards.length) {
      tbody.innerHTML =
        '<tr><td colspan="6" class="empty">No rewards</td></tr>';
      return;
    }
    tbody.innerHTML = rewards
      .map(
        (r) => `
                <tr>
                    <td><strong>${escapeHtml(r.name)}</strong></td>
                    <td>${r.points_cost} pts</td>
                    <td>${r.quantity_available ?? "Unlimited"}</td>
                    <td>${r.redemption_count || 0}</td>
                    <td><span class="badge badge-${r.status === "active" ? "success" : "secondary"}">${r.status}</span></td>
                    <td class="actions">
                        <button class="btn btn-sm btn-secondary" onclick="editReward(${r.id})">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteReward(${r.id})">Deactivate</button>
                    </td>
                </tr>
            `,
      )
      .join("");
  }

  async function loadRedemptions() {
    const status = document.getElementById("redemptionFilter").value;
    try {
      const response = await API.admin.getRedemptions({ status });
      if (response.success) {
        const redemptions = Array.isArray(response.data)
          ? response.data
          : Array.isArray(response.data?.redemptions)
            ? response.data.redemptions
            : [];

        renderRedemptions(redemptions);
        document.getElementById("pendingRedemptions").textContent =
          redemptions.filter((r) => r.status === "pending").length;
      }
    } catch (e) {
      showToast("Error loading redemptions", "error");
    }
  }

  function renderRedemptions(redemptions) {
    const tbody = document.getElementById("redemptionsTable");
    if (!redemptions.length) {
      tbody.innerHTML =
        '<tr><td colspan="6" class="empty">No redemptions</td></tr>';
      return;
    }
    tbody.innerHTML = redemptions
      .map(
        (r) => `
                <tr>
                    <td>${escapeHtml(r.user_name)}<br><small>${r.alumni_id}</small></td>
                    <td>${escapeHtml(r.reward_name)}</td>
                    <td>${r.points_cost} pts</td>
                    <td><span class="badge badge-${getStatusColor(r.status)}">${r.status}</span></td>
                    <td>${formatDate(r.created_at)}</td>
                    <td class="actions">
                        ${
                          r.status === "pending"
                            ? `
                            <button class="btn btn-sm btn-success" onclick="updateRedemption(${r.id}, 'approved')">Approve</button>
                            <button class="btn btn-sm btn-danger" onclick="updateRedemption(${r.id}, 'rejected')">Reject</button>
                        `
                            : r.status === "approved"
                              ? `
                            <button class="btn btn-sm btn-primary" onclick="updateRedemption(${r.id}, 'claimed')">Mark Claimed</button>
                        `
                              : "-"
                        }
                    </td>
                </tr>
            `,
      )
      .join("");
  }

  function getStatusColor(status) {
    const colors = {
      pending: "warning",
      approved: "primary",
      claimed: "success",
      fulfilled: "success",
      rejected: "danger",
    };
    return colors[status] || "secondary";
  }

  async function updateRedemption(id, status) {
    try {
      const response = await API.admin.updateRedemption(id, { status });
      if (response.success) {
        showToast("Redemption updated", "success");
        loadRedemptions();
      }
    } catch (e) {
      showToast("Error updating redemption", "error");
    }
  }

  async function loadLeaderboard() {
    try {
      const response = await API.gamification.getLeaderboard({ limit: 20 });
      if (response.success) {
        const leaderboard = Array.isArray(response.data)
          ? response.data
          : Array.isArray(response.data?.leaderboard)
            ? response.data.leaderboard
            : [];

        renderLeaderboard(leaderboard);
      }
    } catch (e) {
      showToast("Error loading leaderboard", "error");
    }
  }

  function renderLeaderboard(leaderboard) {
    const tbody = document.getElementById("leaderboardTable");

    if (!leaderboard.length) {
      tbody.innerHTML =
        '<tr><td colspan="4" class="empty">No leaderboard data</td></tr>';
      return;
    }

    tbody.innerHTML = leaderboard
      .map(
        (l, i) => `
                <tr>
                    <td><span class="rank-badge rank-${i + 1}">#${i + 1}</span></td>
                    <td>
                        <div class="user-info">
                            <img src="${l.profile_image || "/assets/images/default-avatar.png"}" alt="" class="avatar-sm">
                            <div>
                                <strong>${escapeHtml(l.name)}</strong>
                                <small>${l.alumni_id}</small>
                            </div>
                        </div>
                    </td>
                    <td>${l.total_points?.toLocaleString() || 0}</td>
                    <td><span class="badge badge-${getBadgeColor(l.badge_level)}">${l.badge_level}</span></td>
                </tr>
            `,
      )
      .join("");
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

  // Reward CRUD
  function openRewardModal() {
    const modal = document.getElementById("rewardModal");
    modal.classList.add("active");
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("modal-open");
    window.setTimeout(() => {
      document.getElementById("rewardName")?.focus();
    }, 80);
  }

  function showRewardModal() {
    document.getElementById("rewardModalTitle").textContent = "Add Reward";
    document.getElementById("rewardForm").reset();
    document.getElementById("rewardId").value = "";
    openRewardModal();
  }

  function editReward(id) {
    const r = rewards.find((x) => x.id == id);
    if (!r) return;
    document.getElementById("rewardModalTitle").textContent = "Edit Reward";
    document.getElementById("rewardId").value = r.id;
    document.getElementById("rewardName").value = r.name;
    document.getElementById("rewardDescription").value = r.description || "";
    document.getElementById("rewardCost").value = r.points_cost;
    document.getElementById("rewardQuantity").value =
      r.quantity_available || "";
    document.getElementById("rewardImage").value = r.image_url || "";
    openRewardModal();
  }

  async function saveReward(e) {
    e.preventDefault();
    const button = e.target.querySelector('button[type="submit"]');
    const id = document.getElementById("rewardId").value;
    const data = {
      name: document.getElementById("rewardName").value,
      description: document.getElementById("rewardDescription").value,
      points_cost: parseInt(document.getElementById("rewardCost").value),
      quantity_available:
        document.getElementById("rewardQuantity").value || null,
      image_url: document.getElementById("rewardImage").value || null,
    };

    try {
      Utils.setButtonLoading(button, true);
      const response = id
        ? await API.admin.updateReward(id, data)
        : await API.admin.createReward(data);
      if (response.success) {
        showToast(response.message, "success");
        closeModal();
        loadRewards();
      }
    } catch (e) {
      showToast("Error saving reward", "error");
    } finally {
      Utils.setButtonLoading(button, false);
    }
  }

  async function deleteReward(id) {
    if (!confirm("Deactivate this reward?")) return;
    try {
      const response = await API.admin.deleteReward(id);
      if (response.success) {
        showToast("Reward deactivated", "success");
        loadRewards();
      }
    } catch (e) {
      showToast("Error deactivating reward", "error");
    }
  }

  // Adjust Points
  function searchAlumni() {
    clearTimeout(searchTimeout);
    const query = document.getElementById("alumniSearch").value.trim();
    if (query.length < 2) {
      document.getElementById("alumniResults").innerHTML = "";
      return;
    }

    searchTimeout = setTimeout(async () => {
      try {
        const response = await API.alumni.search({
          query,
          limit: 5,
        });
        if (response.success) {
          const alumni = Array.isArray(response.data)
            ? response.data
            : Array.isArray(response.data?.alumni)
              ? response.data.alumni
              : [];

          renderAlumniResults(alumni);
        }
      } catch (e) {
        console.error("Search error:", e);
      }
    }, 300);
  }

  function renderAlumniResults(alumni) {
    const results = Array.isArray(alumni) ? alumni : [];
    const container = document.getElementById("alumniResults");
    if (!results.length) {
      container.innerHTML = '<div class="search-item">No results</div>';
      return;
    }
    container.innerHTML = results
      .map(
        (a) => `
                <div class="search-item" onclick="selectAlumni(${a.id}, '${escapeHtml(a.name)}', '${a.alumni_id}')">
                    <strong>${escapeHtml(a.name)}</strong> - ${a.alumni_id}
                </div>
            `,
      )
      .join("");
  }

  function selectAlumni(id, name, alumniId) {
    document.getElementById("selectedUserId").value = id;
    document.getElementById("selectedAlumni").innerHTML =
      `<strong>${name}</strong> (${alumniId})`;
    document.getElementById("alumniResults").innerHTML = "";
    document.getElementById("alumniSearch").value = "";
  }

  async function adjustPoints(e) {
    e.preventDefault();
    const button = e.target.querySelector('button[type="submit"]');
    const userId = document.getElementById("selectedUserId").value;
    if (!userId) {
      showToast("Please select an alumni", "error");
      return;
    }

    const points = parseInt(document.getElementById("adjustAmount").value);
    const reason = document.getElementById("adjustReason").value;

    try {
      Utils.setButtonLoading(button, true);
      const response = await API.admin.adjustPoints({
        user_id: userId,
        points,
        reason,
      });
      if (response.success) {
        showToast("Points adjusted", "success");
        document.getElementById("adjustForm").reset();
        document.getElementById("selectedAlumni").innerHTML =
          "No alumni selected";
        document.getElementById("selectedUserId").value = "";
        loadStats();
      }
    } catch (e) {
      showToast("Error adjusting points", "error");
    } finally {
      Utils.setButtonLoading(button, false);
    }
  }

  function closeModal() {
    const modal = document.getElementById("rewardModal");
    modal.classList.remove("active");
    modal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("modal-open");
  }
</script>

<style>
  .search-results {
    position: absolute;
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 4px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 100;
    width: calc(100% - 2rem);
  }
  .search-item {
    padding: 0.75rem 1rem;
    cursor: pointer;
    border-bottom: 1px solid var(--gray-200);
  }
  .search-item:hover {
    background: var(--gray-50);
  }
  .selected-item {
    padding: 0.75rem;
    background: var(--gray-50);
    border-radius: 4px;
  }

  #rewardModal {
    z-index: 3200 !important;
  }

  #rewardModal .reward-modal-dialog {
    width: min(100%, 620px);
  }

  #rewardModal .reward-modal-content {
    border-radius: 14px !important;
  }

  #rewardModal .modal-header {
    padding: clamp(1.1rem, 2vw, 1.45rem) clamp(1.25rem, 2.4vw, 1.75rem);
  }

  #rewardModal .modal-title {
    font-size: clamp(1.45rem, 2.4vw, 2rem);
    line-height: 1.15;
  }

  #rewardModal .reward-modal-body {
    display: grid;
    gap: 1rem;
    padding: clamp(1.15rem, 2.4vw, 1.75rem);
  }

  #rewardModal .form-group {
    display: grid;
    gap: 0.42rem;
    margin: 0;
  }

  #rewardModal .form-label {
    margin: 0;
    font-weight: 700;
    color: #263244;
  }

  #rewardModal .form-input,
  #rewardModal .form-textarea {
    width: 100%;
    min-height: 2.7rem;
    border-radius: 10px;
  }

  #rewardModal .form-textarea {
    min-height: 104px;
  }

  #rewardModal .reward-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
  }

  #rewardModal .modal-footer {
    padding: 1rem clamp(1.25rem, 2.4vw, 1.75rem);
  }

  @media (max-width: 640px) {
    #rewardModal .reward-form-grid {
      grid-template-columns: 1fr;
    }
  }

  .rank-badge {
    display: inline-block;
    width: 30px;
    height: 30px;
    line-height: 30px;
    text-align: center;
    border-radius: 50%;
    font-weight: bold;
    font-size: 0.8rem;
  }
  .rank-1 {
    background: gold;
    color: #333;
  }
  .rank-2 {
    background: silver;
    color: #333;
  }
  .rank-3 {
    background: #cd7f32;
    color: white;
  }
</style>

