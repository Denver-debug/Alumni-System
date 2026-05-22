<!-- Rewards -->
<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content">
    <header class="content-header">
      <div>
        <h1 class="page-title">Rewards</h1>
        <p class="text-muted">Redeem your points for rewards</p>
      </div>
      <div class="flex items-center gap-md">
        <div class="card p-md">
          <div class="text-sm text-muted">Your Points</div>
          <div class="text-2xl font-bold text-primary" id="myPoints">0</div>
        </div>
      </div>
    </header>

    <div class="content-body">
      <!-- Available Rewards -->
      <div class="card mb-lg">
        <div class="card-header">
          <h2 class="card-title">Available Rewards</h2>
        </div>
        <div id="rewardsList" class="grid grid-cols-3 gap-lg p-lg">
          <div class="loading-spinner">Loading rewards...</div>
        </div>
      </div>

      <!-- My Redemptions -->
      <div class="card">
        <div class="card-header">
          <h2 class="card-title">My Redemptions</h2>
        </div>
        <div id="redemptionsList">
          <div class="loading-spinner p-xl">Loading...</div>
        </div>
      </div>
    </div>
  </main>
</div>

<script>
  (function () {
    let userPoints = 0;

    // Load data sequentially to ensure userPoints is set before rendering rewards
    (async function init() {
      await loadUserPoints();
      await loadRewards();
      await loadRedemptions();
    })();

    async function loadUserPoints() {
      try {
        const response = await API.gamification.getMyPoints();
        userPoints = Number(response?.data?.total_points || 0);
        Utils.$("#myPoints").textContent = userPoints;
        console.log('User points loaded:', userPoints); // Debug log
      } catch (error) {
        console.error("Failed to load points:", error);
      }
    }

    async function loadRewards() {
      try {
        const response = await API.gamification.getRewards();
        const container = Utils.$("#rewardsList");
        const rewards = Array.isArray(response?.data) ? response.data : [];

        console.log('Loading rewards. User points:', userPoints, 'Type:', typeof userPoints); // Debug log

        if (!rewards || rewards.length === 0) {
          container.innerHTML =
            '<div class="text-center text-muted p-xl col-span-3">No rewards available</div>';
          return;
        }

        container.innerHTML = rewards
          .map(
            (reward) => {
              const pointsCost = Number(reward.points_cost || 0);
              const canAfford = userPoints >= pointsCost;
              const isAvailable = reward.can_redeem !== false && (reward.remaining_quantity === null || reward.remaining_quantity > 0);
              
              console.log(`Reward: ${reward.name}, Cost: ${pointsCost}, User Points: ${userPoints}, Can Afford: ${canAfford}`); // Debug log
              
              return `
        <div class="card p-lg text-center">
          <div class="text-4xl mb-md">${reward.icon || "🎁"}</div>
          <h3 class="font-bold mb-sm">${Utils.escapeHtml(reward.name)}</h3>
          <p class="text-sm text-muted mb-md">${Utils.escapeHtml(reward.description || "")}</p>
          <div class="text-lg font-bold text-primary mb-md">${pointsCost} pts</div>
          <div class="text-sm text-muted mb-md">${reward.remaining_quantity ?? reward.quantity_available ?? "Unlimited"} available</div>
          <button class="btn btn-primary btn-sm w-full" 
                  onclick="redeemReward(${reward.id}, ${pointsCost})"
                  ${!canAfford || !isAvailable ? "disabled" : ""}>
            ${!canAfford ? `Need ${pointsCost - userPoints} more points` : !isAvailable ? "Unavailable" : "Redeem"}
          </button>
        </div>
      `;
            }
          )
          .join("");
      } catch (error) {
        Utils.$("#rewardsList").innerHTML =
          '<div class="alert alert-error">Failed to load rewards</div>';
      }
    }

    async function loadRedemptions() {
      try {
        const response = await API.get("/gamification/redemptions");
        const container = Utils.$("#redemptionsList");
        const redemptions = Array.isArray(response?.data) ? response.data : [];

        if (!redemptions || redemptions.length === 0) {
          container.innerHTML =
            '<div class="text-center text-muted p-xl">No redemptions yet</div>';
          return;
        }

        container.innerHTML = `
        <table class="table">
          <thead>
            <tr>
              <th>Reward</th>
              <th>Points</th>
              <th>Status</th>
              <th>Date</th>
            </tr>
          </thead>
          <tbody>
            ${redemptions
              .map(
                (r) => `
              <tr>
                <td>${Utils.escapeHtml(r.reward_name || "Reward")}</td>
                <td>${r.points_cost || 0}</td>
                <td><span class="badge badge-${r.status === "claimed" ? "success" : r.status === "approved" ? "primary" : "warning"}">${r.status}</span></td>
                <td>${new Date(r.created_at).toLocaleDateString()}</td>
              </tr>
            `,
              )
              .join("")}
          </tbody>
        </table>
      `;
      } catch (error) {
        Utils.$("#redemptionsList").innerHTML =
          '<div class="alert alert-error m-lg">Failed to load redemptions</div>';
      }
    }

    window.redeemReward = async function (rewardId, pointsCost) {
      if (userPoints < pointsCost) {
        Utils.error("Not enough points");
        return;
      }

      if (!confirm("Are you sure you want to redeem this reward?")) return;

      try {
        await API.gamification.redeemReward(rewardId);
        Utils.success("Reward redeemed successfully!");
        loadUserPoints();
        loadRewards();
        loadRedemptions();
      } catch (error) {
        Utils.error(error.message || "Failed to redeem reward");
      }
    };
  })();
</script>
