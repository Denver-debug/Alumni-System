<!-- Conversation / Messages Detail -->
<div class="dashboard-layout">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content" style="display: flex; flex-direction: column">
    <div
      class="content-header"
      style="border-bottom: 1px solid var(--gray-200)"
    >
      <div class="flex items-center gap-md">
        <a href="#/messages" class="btn btn-ghost btn-sm">←</a>
        <div class="avatar avatar-sm bg-primary">
          <span id="conversationAvatar">?</span>
        </div>
        <div>
          <div class="font-medium" id="conversationName">Loading...</div>
          <div class="text-xs text-muted" id="conversationType"></div>
        </div>
      </div>
    </div>

    <div
      class="flex-1 overflow-auto p-lg"
      id="messagesContainer"
      style="background: var(--gray-50)"
    >
      <div class="loading-spinner">Loading messages...</div>
    </div>

    <div
      class="p-md"
      style="border-top: 1px solid var(--gray-200); background: white"
    >
      <form id="messageForm" class="flex gap-sm">
        <input
          type="text"
          id="messageInput"
          class="form-input flex-1"
          placeholder="Type a message..."
          autocomplete="off"
        />
        <button type="submit" class="btn btn-primary">Send</button>
      </form>
    </div>
  </main>
</div>

<script>
  (function () {
    const user = API.getUser() || Auth.user || {};
    const currentUserId = Number(user.id || 0);

    const id = Router.getParam("id");
    if (id) {
      loadConversation(id);
      loadMessages(id);
    }

    async function loadConversation(id) {
      try {
        const response = await API.messaging.getConversations();
        const conversations = Array.isArray(response?.data)
          ? response.data
          : [];
        const conv = conversations.find(
          (item) => String(item.id) === String(id),
        );

        if (!conv) {
          throw new Error("Conversation not found");
        }

        const conversationName =
          conv.display_name || conv.name || "Conversation";
        Utils.$("#conversationName").textContent = conversationName;
        Utils.$("#conversationType").textContent = conv.type;
        Utils.$("#conversationAvatar").textContent = (conversationName ||
          "?")[0].toUpperCase();
      } catch (error) {
        console.error("Failed to load conversation:", error);
      }
    }

    async function loadMessages(conversationId) {
      try {
        const response = await API.messaging.getMessages(conversationId);
        const container = Utils.$("#messagesContainer");

        if (!response.data || response.data.length === 0) {
          container.innerHTML =
            '<div class="text-center text-muted p-xl">No messages yet. Start the conversation!</div>';
          return;
        }

        container.innerHTML = response.data
          .map(
            (msg) => `
        <div class="flex mb-md ${Number(msg.sender_id) === currentUserId ? "justify-end" : "justify-start"}">
          <div class="max-w-xs lg:max-w-md p-sm rounded-lg ${Number(msg.sender_id) === currentUserId ? "bg-primary text-white" : "bg-white shadow"}">
            ${Number(msg.sender_id) !== currentUserId ? `<div class="text-xs font-medium mb-xs">${Utils.escapeHtml(msg.sender_name || "Unknown")}</div>` : ""}
            <div>${Utils.escapeHtml(msg.content)}</div>
            <div class="text-xs opacity-75 mt-xs">${new Date(msg.created_at).toLocaleTimeString()}</div>
          </div>
        </div>
      `,
          )
          .join("");

        container.scrollTop = container.scrollHeight;
      } catch (error) {
        Utils.$("#messagesContainer").innerHTML =
          '<div class="alert alert-error">Failed to load messages</div>';
      }
    }

    Utils.$("#messageForm").addEventListener("submit", async (e) => {
      e.preventDefault();
      const input = Utils.$("#messageInput");
      const content = input.value.trim();
      if (!content) return;

      try {
        await API.messaging.sendMessage(id, content);
        input.value = "";
        loadMessages(id);
      } catch (error) {
        Utils.error("Failed to send message");
      }
    });
  })();
</script>
