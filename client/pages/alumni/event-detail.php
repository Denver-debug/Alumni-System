<!-- Event Detail -->
<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content">
    <div class="content-header">
      <a href="#/events" class="btn btn-ghost btn-sm">← Back to Events</a>
    </div>
    <div class="content-body">
      <div id="eventDetail" class="card p-xl">
        <div class="loading-spinner">Loading...</div>
      </div>
    </div>
  </main>
</div>

<script>
  (function () {
    const id = Router.getParam("id");
    if (id) loadEvent(id);

    async function loadEvent(id) {
      try {
        const response = await API.get(`/events/${id}`);
        const event = response.data;
        const coverImage = API.resolveAssetUrl(event.cover_image || event.image || "");
        const status = event.status || "upcoming";
        const eventTime = event.event_time ? ` ${event.event_time}` : "";

        Utils.$("#eventDetail").innerHTML = `
        ${coverImage ? `<img src="${Utils.escapeHtml(coverImage)}" alt="" class="w-full h-64 object-cover rounded-lg mb-lg" onerror="this.onerror=null; this.src='/assets/images/event-placeholder.svg';">` : ""}
        <div class="flex items-center gap-sm mb-md">
          <span class="badge badge-${status === "upcoming" ? "primary" : status === "ongoing" ? "success" : "secondary"}">${status}</span>
          <span class="badge badge-outline">${event.event_type}</span>
          ${event.points_reward ? `<span class="badge badge-warning">🏆 ${event.points_reward} pts</span>` : ""}
        </div>
        <h1 class="text-2xl font-bold mb-md">${Utils.escapeHtml(event.title)}</h1>
        <div class="grid grid-cols-2 gap-lg mb-lg">
          <div>
            <div class="text-sm text-muted">Date & Time</div>
            <div class="font-medium">${new Date(event.event_date).toLocaleDateString()}${eventTime}</div>
          </div>
          <div>
            <div class="text-sm text-muted">Location</div>
            <div class="font-medium">${event.venue_type === "online" ? "Online Event" : Utils.escapeHtml(event.location || "TBA")}</div>
          </div>
        </div>
        <div class="prose mb-lg">${event.description || "No description provided."}</div>
        <div class="flex gap-md">
          <button class="btn btn-primary" onclick="rsvpEvent(${event.id}, 'going')">I'm Going</button>
          <button class="btn btn-secondary" onclick="rsvpEvent(${event.id}, 'maybe')">Maybe</button>
        </div>
      `;
      } catch (error) {
        Utils.$("#eventDetail").innerHTML =
          '<div class="alert alert-error">Event not found</div>';
      }
    }

    window.rsvpEvent = async function (eventId, status) {
      try {
        await API.post(`/events/${eventId}/rsvp`, { status });
        Utils.success("RSVP updated!");
      } catch (error) {
        Utils.error(error.message || "Failed to RSVP");
      }
    };
  })();
</script>
