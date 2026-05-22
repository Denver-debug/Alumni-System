<!-- Leaderboard -->
<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content">
    <header class="content-header">
      <h1 class="page-title">Leaderboard</h1>
    </header>

    <div class="content-body">
      <!-- My Rank -->
      <div class="card p-lg mb-lg" id="myRank">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-md">
            <div class="avatar avatar-lg bg-primary">
              <span id="myInitials">?</span>
            </div>
            <div>
              <div class="font-bold" id="myName">Loading...</div>
              <div class="text-sm text-muted">
                Your Rank: <span id="myPosition">#-</span>
              </div>
            </div>
          </div>
          <div class="text-right">
            <div class="text-2xl font-bold text-primary" id="myPoints">0</div>
            <div class="text-sm text-muted">points</div>
          </div>
        </div>
      </div>

      <!-- Top Alumni -->
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">Top Alumni</h2>
        </div>
        <div id="leaderboardList">
          <div class="loading-spinner p-xl">Loading leaderboard...</div>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
  (function () {
    const user = API.getUser() || Auth.user || null;
    if (user) {
      Utils.$("#myName").textContent = user.name || "You";
      Utils.$("#myInitials").textContent = Utils.getInitials(user.name || "Y");
    }

    loadLeaderboard();

    async function loadLeaderboard() {
      try {
        const response = await API.get("/gamification/leaderboard");
        const container = Utils.$("#leaderboardList");
        const payload = response?.data || {};
        const data = Array.isArray(payload)
          ? payload
          : Array.isArray(payload.leaderboard)
            ? payload.leaderboard
            : [];

        // Find user's position
        const myIndex = user
          ? data.findIndex((a) => String(a.id) === String(user.id))
          : -1;
        if (myIndex >= 0) {
          Utils.$("#myPosition").textContent = `#${myIndex + 1}`;
          Utils.$("#myPoints").textContent = data[myIndex].total_points || 0;
        }

        if (!data || data.length === 0) {
          container.innerHTML =
            '<div class="p-xl text-center text-muted">No data yet</div>';
          return;
        }

        container.innerHTML = `
        <table class="table">
          <thead>
            <tr>
              <th width="60">Rank</th>
              <th>Alumni</th>
              <th>Badge</th>
              <th class="text-right">Points</th>
            </tr>
          </thead>
          <tbody>
            ${data
              .slice(0, 50)
              .map(
                (alumni, index) => `
              <tr class="${user && String(alumni.id) === String(user.id) ? "bg-primary-50" : ""}">
                <td>
                  <span class="font-bold ${index < 3 ? "text-xl" : ""}">
                    ${index === 0 ? "🥇" : index === 1 ? "🥈" : index === 2 ? "🥉" : `#${index + 1}`}
                  </span>
                </td>
                <td>
                  <div class="flex items-center gap-sm">
                    <div class="avatar avatar-sm bg-primary">${(alumni.name || "?")[0].toUpperCase()}</div>
                    <span>${Utils.escapeHtml(alumni.display_name || alumni.name || "Alumni")}</span>
                  </div>
                </td>
                <td>
                  <span class="badge badge-${getBadgeColor(alumni.badge_level)}">${alumni.badge_level || "bronze"}</span>
                </td>
                <td class="text-right font-bold">${alumni.total_points || 0}</td>
              </tr>
            `,
              )
              .join("")}
          </tbody>
        </table>
      `;
      } catch (error) {
        Utils.$("#leaderboardList").innerHTML =
          '<div class="alert alert-error m-lg">Failed to load leaderboard</div>';
      }
    }

    function getBadgeColor(badge) {
      const colors = {
        bronze: "secondary",
        silver: "info",
        gold: "warning",
        platinum: "primary",
        diamond: "success",
      };
      return colors[String(badge || "").toLowerCase()] || "secondary";
    }
  })();
</script>
