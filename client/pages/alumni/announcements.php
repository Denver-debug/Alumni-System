<!-- Alumni Announcements List -->
<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <!-- Main Content -->
  <main class="main-content">
    <header class="content-header">
      <h1 class="page-title">Announcements</h1>
    </header>

    <div class="content-body">
      <div id="announcementsList" class="space-y-md">
        <div class="loading-spinner">Loading announcements...</div>
      </div>
    </div>
  </main>
</div>

<script>
  (function () {
    // Load announcements
    loadAnnouncements();

    async function loadAnnouncements() {
      const container = Utils.$("#announcementsList");
      
      // Show loading skeleton
      container.innerHTML = Components.loadingSkeleton({ type: 'list', lines: 5 });

      try {
        const response = await API.get("/announcements");
        const announcements = Array.isArray(response?.data?.announcements)
          ? response.data.announcements
          : Array.isArray(response?.data)
            ? response.data
            : [];

        if (!announcements.length) {
          container.innerHTML = Components.emptyState({
            icon: '📢',
            title: 'No announcements yet',
            message: 'Check back later for important updates and news',
            actionText: 'Refresh',
            actionHandler: () => loadAnnouncements()
          });
          return;
        }

        container.innerHTML = announcements
          .map(
            (item) => `
        <div class="card p-lg cursor-pointer hover:shadow-lg transition" onclick="Router.navigate('/announcements/${item.id}')">
          <div class="flex items-start gap-md">
            ${item.cover_image ? `<img src="${item.cover_image}" alt="" class="w-24 h-24 rounded-lg object-cover">` : ""}
            <div class="flex-1">
              <div class="flex items-center gap-sm mb-xs">
                ${item.priority === "urgent" ? '<span class="badge badge-error">Urgent</span>' : ""}
                ${item.priority === "important" ? '<span class="badge badge-warning">Important</span>' : ""}
                ${item.is_pinned ? '<span class="badge badge-primary">Pinned</span>' : ""}
              </div>
              <h3 class="font-bold text-lg mb-xs">${Utils.escapeHtml(item.title)}</h3>
              <p class="text-secondary text-sm mb-sm">${Utils.escapeHtml(item.excerpt || item.content || "")}</p>
              <div class="text-xs text-muted">
                ${new Date(item.publish_date || item.created_at).toLocaleDateString()}
              </div>
            </div>
          </div>
        </div>
      `,
          )
          .join("");
      } catch (error) {
        console.error('Load announcements error:', error);
        container.innerHTML = Components.errorState({
          title: 'Failed to load announcements',
          message: error.message || 'Please try again later',
          retryHandler: () => loadAnnouncements()
        });
      }
    }
  })();
</script>
