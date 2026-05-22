<!-- Announcement Detail -->
<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content">
    <div class="content-header">
      <a href="#/announcements" class="btn btn-ghost btn-sm">← Back</a>
    </div>
    <div class="content-body">
      <div id="announcementDetail" class="card p-xl">
        <div class="loading-spinner">Loading...</div>
      </div>
    </div>
  </main>
</div>

<script>
  (function () {
    const id = Router.getParam("id");
    if (id) loadAnnouncement(id);

    async function loadAnnouncement(id) {
      try {
        const response = await API.get(`/announcements/${id}`);
        const item = response?.data?.announcement || response?.data || {};
        const contentHtml = Utils.escapeHtml(item.content || "").replace(
          /\n/g,
          "<br>",
        );
        const imageUrl = item.cover_image || item.image_url || "";

        Utils.$("#announcementDetail").innerHTML = `
        ${imageUrl ? `<img src="${imageUrl}" alt="" class="w-full h-64 object-cover rounded-lg mb-lg">` : ""}
        <h1 class="text-2xl font-bold mb-md">${Utils.escapeHtml(item.title)}</h1>
        <div class="text-sm text-muted mb-lg">
          Published: ${new Date(item.publish_date || item.created_at).toLocaleDateString()}
        </div>
        <div class="prose">${contentHtml}</div>
      `;
      } catch (error) {
        Utils.$("#announcementDetail").innerHTML =
          '<div class="alert alert-error">Announcement not found</div>';
      }
    }
  })();
</script>
