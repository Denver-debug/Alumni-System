<!-- Alumni Dashboard -->
<link rel="stylesheet" href="/assets/css/dashboard-improvements.css">

<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <!-- Main Content -->
  <main class="main-content">
    <!-- Top Bar -->
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
      <h1 class="page-title">Dashboard</h1>
      <div class="topbar-actions">
        <button
          class="btn btn-ghost btn-icon"
          onclick="location.hash = '#/messages'"
        >
          <svg
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
            <path d="M13.73 21a2 2 0 0 1-3.46 0" />
          </svg>
        </button>
      </div>
    </header>

    <!-- Dashboard Content -->
    <div class="content-wrapper">
      <!-- Verification Status Banner -->
      <div class="verification-banner" id="verificationBanner" style="display: none;">
        <div class="banner-content">
          <div class="banner-icon" id="bannerIcon">⏳</div>
          <div class="banner-text">
            <h4 id="bannerTitle">Account Verification Pending</h4>
            <p id="bannerMessage">Your account is being reviewed by our admin team.</p>
          </div>
        </div>
      </div>
      
      <!-- Welcome Card -->
      <div class="card alumni-highlight-card">
        <div class="card-body alumni-highlight-card-body">
          <div class="flex justify-between items-start flex-wrap gap-lg">
            <div>
              <h2 class="text-2xl font-bold mb-sm" id="welcomeText">
                Welcome back!
              </h2>
              <p class="text-secondary mb-md" id="lastLoginText">
                Ready to connect with your fellow alumni?
              </p>
              <a href="#/events" class="btn btn-primary"> Browse Events </a>
            </div>
            <div class="text-right">
              <div class="text-sm alumni-points-label">Your Points</div>
              <div class="text-3xl font-bold alumni-points-value" id="totalPoints">0</div>
              <div class="badge badge-light mt-sm" id="badgeLevel">Bronze</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-4 gap-lg" style="margin-bottom: 1rem;" id="statsGrid">
        <div class="card">
          <div class="card-body">
            <div class="flex justify-between items-start">
              <div>
                <div class="text-secondary text-sm">Events Attended</div>
                <div class="text-2xl font-bold mt-sm" id="eventsAttended">
                  0
                </div>
              </div>
              <div class="avatar avatar-md bg-primary-light">
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="var(--primary-600)"
                  stroke-width="2"
                >
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                  <line x1="16" y1="2" x2="16" y2="6" />
                  <line x1="8" y1="2" x2="8" y2="6" />
                </svg>
              </div>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <div class="flex justify-between items-start">
              <div>
                <div class="text-secondary text-sm">Connections</div>
                <div class="text-2xl font-bold mt-sm" id="connections">0</div>
              </div>
              <div class="avatar avatar-md bg-success-light">
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="var(--success-600)"
                  stroke-width="2"
                >
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                  <circle cx="9" cy="7" r="4" />
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87" />
                  <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                </svg>
              </div>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <div class="flex justify-between items-start">
              <div>
                <div class="text-secondary text-sm">Messages</div>
                <div class="text-2xl font-bold mt-sm" id="unreadMessages">
                  0
                </div>
              </div>
              <div class="avatar avatar-md bg-warning-light">
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="var(--warning-600)"
                  stroke-width="2"
                >
                  <path
                    d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
                  />
                </svg>
              </div>
            </div>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <div class="flex justify-between items-start">
              <div>
                <div class="text-secondary text-sm">Rewards Claimed</div>
                <div class="text-2xl font-bold mt-sm" id="rewardsClaimed">
                  0
                </div>
              </div>
              <div class="avatar avatar-md bg-danger-light">
                <svg
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="var(--danger-600)"
                  stroke-width="2"
                >
                  <circle cx="12" cy="8" r="7" />
                  <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88" />
                </svg>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Two Column Layout -->
      <div class="grid grid-cols-2 gap-lg" style="margin-bottom: 1rem;">
        <!-- Upcoming Events -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Upcoming Events</h3>
            <a href="#/events" class="btn btn-ghost btn-sm">View All</a>
          </div>
          <div class="card-body p-0">
            <div id="upcomingEvents" class="divide-y">
              <div class="loading-skeleton p-lg">Loading events...</div>
            </div>
          </div>
        </div>

        <!-- Recent Announcements -->
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Announcements</h3>
          </div>
          <div class="card-body p-0">
            <div id="announcements" class="divide-y">
              <div class="loading-skeleton p-lg">Loading announcements...</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Leaderboard Preview -->
      <div class="card">
        <div class="card-header">
          <h3 class="card-title">🏆 Top Alumni This Month</h3>
          <a href="#/leaderboard" class="btn btn-ghost btn-sm"
            >Full Leaderboard</a
          >
        </div>
        <div class="card-body p-0">
          <div id="leaderboardPreview">
            <div class="loading-skeleton p-lg">Loading leaderboard...</div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<style>
  .content-wrapper {
    max-width: 1400px;
    margin: 0 auto;
    padding: 1.25rem 1.5rem;
    display: grid;
    gap: 1.25rem;
  }

  .card {
    border: 1px solid var(--gray-100);
    transition:
      transform 0.2s ease,
      box-shadow 0.2s ease,
      border-color 0.2s ease;
  }

  .card:hover {
    transform: translateY(-2px);
    border-color: var(--primary-100);
    box-shadow: 0 8px 24px -8px rgba(16, 185, 129, 0.2);
  }

  .divide-y > * + * {
    border-top: 1px solid var(--gray-200);
  }

  /* Stats Cards */
  #statsGrid .card {
    background: linear-gradient(135deg, #ffffff, #f9fafb);
  }

  #statsGrid .card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px -8px rgba(16, 185, 129, 0.25);
  }

  #statsGrid .text-2xl {
    color: var(--primary-700);
  }

  /* Badge Styles */
  .badge-light {
    background-color: rgba(255, 255, 255, 0.25);
    color: #ffffff !important;
    font-weight: 600;
    border: 1px solid rgba(255, 255, 255, 0.3);
  }

  /* Empty State Improvements */
  .empty-state {
    text-align: center;
    padding: 2rem;
    color: var(--gray-600);
  }

  .empty-state-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
  }

  /* Link Hover States */
  a.block:hover {
    background-color: var(--gray-50) !important;
    transition: background-color 0.2s ease;
  }

  /* Table Improvements */
  .table tbody tr:hover {
    background-color: var(--gray-50);
  }

  @media (max-width: 1024px) {
    .grid-cols-4 {
      grid-template-columns: repeat(2, 1fr);
    }
    .grid-cols-2 {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 768px) {
    .grid-cols-4 {
      grid-template-columns: 1fr;
    }

    .content-wrapper {
      padding: 1rem;
      gap: 1rem;
    }

    .alumni-highlight-card-body {
      padding: 1.25rem !important;
    }
  }
</style>

<script>
  (function () {
    const user = API.getUser();

    if (!user) {
      Router.navigate("/login");
      return;
    }

    // Update welcome
    Utils.$("#welcomeText").textContent =
      `Welcome back, ${user.name?.split(" ")[0] || "Alumni"}!`;

    // Load dashboard data
    loadDashboard();

    async function loadDashboard() {
      try {
        // Show loading skeletons
        Utils.$("#upcomingEvents").innerHTML = Components.loadingSkeleton({ type: 'list', lines: 3 });
        Utils.$("#announcements").innerHTML = Components.loadingSkeleton({ type: 'list', lines: 3 });
        Utils.$("#leaderboardPreview").innerHTML = Components.loadingSkeleton({ type: 'table' });

        // Load stats and data in parallel
        const [
          statsRes,
          eventsRes,
          announcementsRes,
          leaderboardRes,
        ] = await Promise.all([
          API.gamification.getMyPoints().catch(() => ({ data: {} })),
          API.events
            .list({ status: "upcoming", limit: 5 })
            .catch(() => ({ data: { events: [] } })),
          API.announcements
            .list({ limit: 5 })
            .catch(() => ({ data: { announcements: [] } })),
          API.gamification
            .getLeaderboard({ limit: 5 })
            .catch(() => ({ data: { leaderboard: [] } })),
        ]);

        // Update points display
        if (statsRes?.data) {
          Utils.$("#totalPoints").textContent = statsRes.data.total_points || 0;
          Utils.$("#badgeLevel").textContent =
            statsRes.data.badge_level || "Bronze";
          Utils.$("#eventsAttended").textContent =
            statsRes.data.events_attended || 0;
          Utils.$("#rewardsClaimed").textContent =
            statsRes.data.rewards_claimed || 0;
        }

        // Update events
        renderEvents(eventsRes?.data?.events || []);

        // Update announcements
        renderAnnouncements(announcementsRes?.data?.announcements || []);

        // Update leaderboard
        renderLeaderboard(leaderboardRes?.data?.leaderboard || []);
      } catch (error) {
        console.error("Dashboard load error:", error);
        Utils.error("Failed to load dashboard data. Please refresh the page.");
      }
    }

    function renderEvents(events) {
      const container = Utils.$("#upcomingEvents");

      if (!events.length) {
        container.innerHTML = Components.emptyState({
          icon: '📅',
          title: 'No upcoming events',
          message: 'Check back soon for new events and activities',
          actionText: 'Browse All Events',
          actionHref: '#/events'
        });
        return;
      }

      container.innerHTML = events
        .map(
          (event) => `
            <a href="#/events/${event.id}" class="block p-md hover:bg-gray-50">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="font-medium">${Utils.escapeHtml(event.title)}</div>
                        <div class="text-sm text-secondary mt-xs">
                          ${Utils.formatDate(event.event_date)}${event.event_time ? ` at ${event.event_time}` : ""}
                        </div>
                    </div>
                    <span class="badge badge-primary">${event.points_reward} pts</span>
                </div>
            </a>
        `,
        )
        .join("");
    }

    function renderAnnouncements(announcements) {
      const container = Utils.$("#announcements");

      if (!announcements.length) {
        container.innerHTML = Components.emptyState({
          icon: '📢',
          title: 'No announcements',
          message: 'Stay tuned for important updates and news',
          actionText: null
        });
        return;
      }

      container.innerHTML = announcements
        .map(
          (ann) => `
            <div class="p-md">
                <div class="flex items-start gap-sm">
                    ${ann.priority === "urgent" ? '<span class="badge badge-danger">Urgent</span>' : ""}
                    ${ann.priority === "important" ? '<span class="badge badge-warning">Important</span>' : ""}
                    <div class="flex-1">
                        <div class="font-medium">${Utils.escapeHtml(ann.title)}</div>
                        <div class="text-sm text-secondary mt-xs line-clamp-2">${Utils.escapeHtml(ann.content?.substring(0, 100) || "")}...</div>
                        <div class="text-xs text-muted mt-sm">${Utils.timeAgo(ann.created_at)}</div>
                    </div>
                </div>
            </div>
        `,
        )
        .join("");
    }

    function renderLeaderboard(leaders) {
      const container = Utils.$("#leaderboardPreview");

      if (!leaders.length) {
        container.innerHTML = Components.emptyState({
          icon: '🏆',
          title: 'No leaderboard data',
          message: 'Start earning points by attending events and participating',
          actionText: 'View Leaderboard',
          actionHref: '#/leaderboard'
        });
        return;
      }

      container.innerHTML = `
            <table class="table">
                <thead>
                    <tr>
                        <th>Rank</th>
                        <th>Alumni</th>
                        <th>Points</th>
                        <th>Badge</th>
                    </tr>
                </thead>
                <tbody>
                    ${leaders
                      .map(
                        (leader, i) => `
                        <tr>
                            <td>
                                ${i === 0 ? "🥇" : i === 1 ? "🥈" : i === 2 ? "🥉" : i + 1}
                            </td>
                            <td>
                                <div class="flex items-center gap-sm">
                                    <div class="avatar avatar-sm bg-primary">
                                        <span>${Utils.getInitials(leader.name)}</span>
                                    </div>
                                    ${Utils.escapeHtml(leader.name)}
                                </div>
                            </td>
                            <td class="font-bold">${leader.total_points || 0}</td>
                            <td><span class="badge badge-${getBadgeColor(leader.badge_level)}">${leader.badge_level || "Bronze"}</span></td>
                        </tr>
                    `,
                      )
                      .join("")}
                </tbody>
            </table>
        `;
    }

    function getBadgeColor(level) {
      const colors = {
        bronze: "secondary",
        silver: "default",
        gold: "warning",
        platinum: "primary",
        diamond: "success",
      };
      return colors[String(level || "").toLowerCase()] || "secondary";
    }
  })();
</script>


<style>
.verification-banner {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    border: 1px solid #fbbf24;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
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

.verification-banner.verified {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    border-color: #10b981;
}

.verification-banner.rejected {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    border-color: #ef4444;
}

.banner-content {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.banner-icon {
    font-size: 2rem;
    flex-shrink: 0;
}

.banner-text h4 {
    margin: 0 0 0.25rem 0;
    font-size: 1.125rem;
    color: var(--gray-900);
}

.banner-text p {
    margin: 0;
    color: var(--gray-700);
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .verification-banner {
        padding: 1rem;
    }
    
    .banner-icon {
        font-size: 1.5rem;
    }
    
    .banner-text h4 {
        font-size: 1rem;
    }
}
</style>

<script>
// Check verification status
async function checkVerificationStatus() {
    try {
        const response = await API.verification.getStatus();
        const status = response.data.verification_status;
        const banner = document.getElementById('verificationBanner');
        const icon = document.getElementById('bannerIcon');
        const title = document.getElementById('bannerTitle');
        const message = document.getElementById('bannerMessage');
        
        if (status === 'pending') {
            banner.style.display = 'block';
            banner.className = 'verification-banner';
            icon.textContent = '⏳';
            title.textContent = 'Account Verification Pending';
            message.textContent = 'Your account is being reviewed by our admin team. You\'ll receive an email once verified.';
        } else if (status === 'verified') {
            // Show verified message briefly
            banner.style.display = 'block';
            banner.className = 'verification-banner verified';
            icon.textContent = '✅';
            title.textContent = 'Account Verified!';
            message.textContent = 'Your account has been verified. Welcome to the alumni system!';
            
            // Hide after 5 seconds
            setTimeout(() => {
                banner.style.display = 'none';
            }, 5000);
        } else if (status === 'rejected') {
            banner.style.display = 'block';
            banner.className = 'verification-banner rejected';
            icon.textContent = '❌';
            title.textContent = 'Account Not Approved';
            message.textContent = response.data.rejection_reason || 'Your registration was not approved. Please contact the admin for more information.';
        }
    } catch (error) {
        console.error('Error checking verification status:', error);
        // Don't show banner if there's an error
    }
}

// Check immediately (SPA navigation doesn't trigger DOMContentLoaded)
if (typeof API !== 'undefined' && API.verification) {
    checkVerificationStatus();
}
</script>
