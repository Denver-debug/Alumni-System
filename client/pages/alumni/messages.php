<!-- Alumni Messages Page - Messenger-style layout -->
<div class="dashboard-layout messages-dashboard-shell sidebar-collapsed">
  <aside class="sidebar" id="sidebar"></aside>

  <main class="main-content messages-main">
    <section class="messenger-surface" id="messengerSurface" data-view="list">
      <aside class="messenger-list-panel" aria-label="Conversations">
        <header class="messenger-list-header">
          <div class="messenger-title-row">
            <h1>Chats</h1>
            <div class="messenger-title-actions">
              <button
                type="button"
                class="messenger-icon-btn sidebar-toggle"
                id="sidebarToggle"
                aria-label="Open alumni menu"
                title="Open alumni menu"
              >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true">
                  <circle cx="5" cy="12" r="1.6"></circle>
                  <circle cx="12" cy="12" r="1.6"></circle>
                  <circle cx="19" cy="12" r="1.6"></circle>
                </svg>
                <span class="messenger-action-dot" aria-hidden="true"></span>
              </button>
              <button type="button" class="messenger-icon-btn messenger-desktop-only" id="messagesFocusBtn" aria-label="Expand messages" title="Expand messages">
                <svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                  <path d="M8 3H3v5"></path>
                  <path d="M16 3h5v5"></path>
                  <path d="M21 16v5h-5"></path>
                  <path d="M3 16v5h5"></path>
                  <path d="m3 3 6 6"></path>
                  <path d="m21 3-6 6"></path>
                  <path d="m21 21-6-6"></path>
                  <path d="m3 21 6-6"></path>
                </svg>
              </button>
              <button type="button" class="messenger-icon-btn" id="newMessageBtn" aria-label="New message" title="New message">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" aria-hidden="true">
                  <path d="M12 20h9"></path>
                  <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4 11.5-11.5Z"></path>
                </svg>
              </button>
            </div>
          </div>

          <label class="messenger-search" for="searchConversations">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
              <circle cx="11" cy="11" r="7"></circle>
              <path d="m20 20-3.9-3.9"></path>
            </svg>
            <input type="search" id="searchConversations" placeholder="search in messages" autocomplete="off">
          </label>

          <nav class="messenger-tabs" aria-label="Conversation filters">
            <button type="button" class="messenger-tab active" data-filter="all">All</button>
            <button type="button" class="messenger-tab" data-filter="unread">Unread</button>
            <button type="button" class="messenger-tab" data-filter="groups">Groups</button>
            <button type="button" class="messenger-tab" data-filter="section" data-org-group="section">Section</button>
            <button type="button" class="messenger-tab" data-filter="college" data-org-group="college">College</button>
            <button type="button" class="messenger-tab" data-filter="batch" data-org-group="program">Batch</button>
          </nav>
        </header>

        <div class="messenger-thread-list" id="conversationsList">
          <div class="messenger-loading-state">
            <div class="messenger-spinner"></div>
            <p>Loading conversations...</p>
          </div>
        </div>

        <a class="messenger-mobile-footer" href="#/messages">See all in Messenger</a>
      </aside>

      <section class="messenger-chat-panel" aria-label="Messages">
        <div class="messenger-empty-chat" id="noChatSelected">
          <div class="messenger-empty-icon">
            <svg width="54" height="54" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
              <path d="M21 15a4 4 0 0 1-4 4H8l-5 3V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4v8Z"></path>
            </svg>
          </div>
          <h2>Choose a conversation</h2>
          <p>Choose a conversation from the list to keep in touch.</p>
        </div>

        <div class="messenger-chat-content hidden" id="chatContent">
          <header class="messenger-chat-header">
            <button type="button" class="messenger-icon-btn messenger-chat-back" id="backToChatsBtn" aria-label="Back to chats">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" aria-hidden="true">
                <path d="m15 18-6-6 6-6"></path>
              </svg>
            </button>

            <div class="messenger-chat-person">
              <div id="chatAvatar" class="messenger-chat-avatar-slot"></div>
              <div class="messenger-chat-copy">
                <div class="messenger-chat-name" id="chatName">Conversation</div>
                <div class="messenger-chat-status" id="chatStatus">Active now</div>
              </div>
            </div>

            <div class="messenger-chat-actions">
              <button type="button" class="messenger-chat-action" data-call-type="audio" aria-label="Start audio call">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M6.62 10.79c1.44 2.83 3.76 5.15 6.59 6.59l2.2-2.2a1.5 1.5 0 0 1 1.52-.36c1.11.37 2.31.57 3.57.57A1.5 1.5 0 0 1 22 16.89V20.5A1.5 1.5 0 0 1 20.5 22C10.28 22 2 13.72 2 3.5A1.5 1.5 0 0 1 3.5 2H7.1a1.5 1.5 0 0 1 1.5 1.5c0 1.26.2 2.46.57 3.57.16.52.04 1.08-.35 1.47l-2.2 2.25Z"></path>
                </svg>
              </button>
              <button type="button" class="messenger-chat-action" data-call-type="video" aria-label="Start video call">
                <svg width="23" height="23" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M4.5 6.5A2.5 2.5 0 0 1 7 4h7a2.5 2.5 0 0 1 2.5 2.5v1.1l3.1-2.1A1.55 1.55 0 0 1 22 6.8v10.4a1.55 1.55 0 0 1-2.4 1.3l-3.1-2.1v1.1A2.5 2.5 0 0 1 14 20H7a2.5 2.5 0 0 1-2.5-2.5v-11Z"></path>
                </svg>
              </button>
              <button type="button" class="messenger-chat-action" id="conversationInfoBtn" aria-label="Conversation information">
                <svg width="23" height="23" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                  <path d="M12 2a10 10 0 1 0 .01 0H12Zm1 15h-2v-6h2v6Zm0-8h-2V7h2v2Z"></path>
                </svg>
              </button>
            </div>
          </header>

          <div class="messenger-messages-area" id="messagesArea"></div>

          <footer class="messenger-composer">
            <button type="button" class="messenger-compose-tool" id="voiceMessageBtn" aria-label="Record voice message">
              <svg width="21" height="21" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 14a3 3 0 0 0 3-3V5a3 3 0 0 0-6 0v6a3 3 0 0 0 3 3Z"></path>
                <path d="M19 11a7 7 0 0 1-14 0H3a9 9 0 0 0 8 8.94V23h2v-3.06A9 9 0 0 0 21 11h-2Z"></path>
              </svg>
            </button>
            <button type="button" class="messenger-compose-tool" id="imageUploadBtn" aria-label="Attach image">
              <svg width="21" height="21" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2ZM8.5 8.5A1.5 1.5 0 1 1 7 7a1.5 1.5 0 0 1 1.5 1.5ZM19 18H5l4.5-5.5 3.25 3.9 2.5-3.15L19 18Z"></path>
              </svg>
            </button>
            <button type="button" class="messenger-compose-tool messenger-compose-extra" id="fileUploadBtn" aria-label="Attach file">
              <svg width="21" height="21" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 2a10 10 0 1 0 0 20h1v-4h-1a6 6 0 1 1 6-6v1h4v-1A10 10 0 0 0 12 2Zm0 7a3 3 0 1 0 0 6h1V9h-1Z"></path>
              </svg>
            </button>
            <textarea id="messageInput" placeholder="Aa" rows="1" aria-label="Type a message"></textarea>
            <button type="button" class="messenger-emoji-btn" id="emojiBtn" aria-label="Choose emoji">
              <svg width="23" height="23" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20ZM8.5 9.5a1.25 1.25 0 1 1 0-2.5 1.25 1.25 0 0 1 0 2.5Zm7 0a1.25 1.25 0 1 1 0-2.5 1.25 1.25 0 0 1 0 2.5ZM12 17.5A5.1 5.1 0 0 1 7.4 15h9.2a5.1 5.1 0 0 1-4.6 2.5Z"></path>
              </svg>
            </button>
            <button type="button" class="messenger-send-btn" id="sendBtn" aria-label="Send message">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M3.4 20.4 22 12 3.4 3.6 3 10l10 2-10 2 .4 6.4Z"></path>
              </svg>
            </button>
            <input type="file" id="imageAttachmentInput" class="messenger-file-input" accept="image/*">
            <input type="file" id="fileAttachmentInput" class="messenger-file-input" accept=".pdf,.doc,.docx,image/*,audio/*">
            <div class="messenger-emoji-popover" id="emojiPopover" hidden></div>
          </footer>
        </div>
      </section>

      <aside class="messenger-info-panel is-empty" id="conversationInfoPanel" aria-label="Conversation details">
        <div class="messenger-info-profile">
          <div id="infoAvatar" class="messenger-info-avatar-slot"></div>
          <h2 id="infoName">Conversation details</h2>
          <p id="infoStatus">Conversation details will appear here.</p>
          <div class="messenger-encryption-pill">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M17 9V7A5 5 0 0 0 7 7v2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-8a2 2 0 0 0-2-2h-1Zm-8 0V7a3 3 0 0 1 6 0v2H9Z"></path>
            </svg>
            End-to-end encrypted
          </div>
        </div>

        <div class="messenger-info-actions">
          <button type="button" id="profileActionBtn" aria-label="Open profile">
            <span>
              <svg width="23" height="23" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 12a5 5 0 1 0-5-5 5 5 0 0 0 5 5Zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5Z"></path>
              </svg>
            </span>
            Profile
          </button>
          <button type="button" id="muteActionBtn" aria-label="Mute conversation">
            <span>
              <svg width="23" height="23" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22Zm7-6v-5a7 7 0 1 0-14 0v5l-2 2v1h18v-1l-2-2Z"></path>
              </svg>
            </span>
            Mute
          </button>
          <button type="button" id="searchInConversationBtn" aria-label="Search in conversation">
            <span>
              <svg width="23" height="23" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" aria-hidden="true">
                <circle cx="11" cy="11" r="7"></circle>
                <path d="m20 20-3.9-3.9"></path>
              </svg>
            </span>
            Search
          </button>
        </div>

        <div class="messenger-info-sections">
          <details open>
            <summary>Chat info</summary>
            <p id="infoMeta">No conversation selected.</p>
          </details>
          <details id="customizeChatDetail">
            <summary>Customize chat</summary>
            <div class="messenger-customize-panel">
              <div class="form-group">
                <label for="chatNicknameInput">Nickname</label>
                <input id="chatNicknameInput" class="form-input" placeholder="Set a nickname for this chat">
              </div>
              <div class="form-group">
                <label for="chatThemeSelect">Theme</label>
                <select id="chatThemeSelect" class="form-select">
                  <option value="">Default</option>
                  <option value="green">Green</option>
                  <option value="blue">Blue</option>
                  <option value="purple">Purple</option>
                </select>
              </div>
              <div class="form-actions">
                <button type="button" class="btn btn-secondary" id="resetChatCustomize">Reset</button>
                <button type="button" class="btn btn-primary" id="saveChatCustomize">Save</button>
              </div>
            </div>
          </details>
          <details>
            <summary>Notifications</summary>
            <div class="messenger-info-settings">
              <div class="messenger-setting-toggle">
                <span>Desktop notifications</span>
                <input type="checkbox" id="desktopNotificationsToggle" aria-label="Enable desktop notifications">
              </div>
              <div class="messenger-setting-toggle">
                <span>Sound alerts</span>
                <input type="checkbox" id="soundAlertsToggle" aria-label="Enable sound alerts">
              </div>
              <div class="messenger-setting-row">
                <label for="messageSoundSelect">Message tone</label>
                <div class="messenger-setting-inline">
                  <select id="messageSoundSelect" class="form-select"></select>
                  <button type="button" class="messenger-setting-preview" id="previewMessageSound">Preview</button>
                </div>
              </div>
              <div class="messenger-setting-row">
                <label for="ringtoneSoundSelect">Call ringtone</label>
                <div class="messenger-setting-inline">
                  <select id="ringtoneSoundSelect" class="form-select"></select>
                  <button type="button" class="messenger-setting-preview" id="previewCallSound">Preview</button>
                </div>
              </div>
            </div>
          </details>
          <details id="mediaFilesDetail">
            <summary>Media & files</summary>
            <div id="mediaFilesList" class="media-grid">No shared media yet.</div>
          </details>
          <details id="privacySupportDetail">
            <summary>Privacy & support</summary>
            <div class="messenger-privacy-panel">
              <div class="messenger-setting-toggle">
                <span>Allow read receipts</span>
                <input type="checkbox" id="readReceiptsToggle">
              </div>
              <div class="messenger-setting-toggle">
                <span>Allow message forwarding</span>
                <input type="checkbox" id="forwardingToggle">
              </div>
              <div class="form-actions mt-sm">
                <button type="button" class="btn btn-danger" id="reportConversationBtn">Report conversation</button>
              </div>
            </div>
          </details>
        </div>
      </aside>
    </section>
  </main>
</div>

<div class="modal" id="newMessageModal">
  <div class="modal-backdrop"></div>
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">New Message</h3>
        <button class="modal-close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <div class="message-compose-tabs" role="tablist" aria-label="Message type">
          <button type="button" class="message-compose-tab active" data-compose-mode="direct">Direct message</button>
          <button type="button" class="message-compose-tab" data-compose-mode="group">Group chat</button>
        </div>

        <div class="search-section">
          <div class="form-group">
            <input type="text" id="searchAlumni" class="form-input" placeholder="Search by name or email...">
          </div>

          <div class="filters-grid">
            <select id="filterBatch" class="form-select">
              <option value="">All Batches</option>
            </select>
            <select id="filterCollege" class="form-select">
              <option value="">All Colleges</option>
            </select>
            <select id="filterProgram" class="form-select">
              <option value="">All Programs</option>
            </select>
          </div>
        </div>

        <div class="group-compose-panel hidden" id="groupComposePanel">
          <input type="text" id="groupNameInput" class="form-input" placeholder="Group chat name">
          <div class="group-quick-actions">
            <button type="button" class="btn btn-secondary" data-org-shortcut="section">Section GC</button>
            <button type="button" class="btn btn-secondary" data-org-shortcut="college">College GC</button>
            <button type="button" class="btn btn-secondary" data-org-shortcut="program">Batch GC</button>
          </div>
          <div class="selected-members" id="selectedGroupMembers"></div>
          <button type="button" class="btn btn-primary" id="createGroupBtn" disabled>Create Group Chat</button>
        </div>
        
        <div id="alumniResults" class="alumni-results"></div>
      </div>
    </div>
  </div>
</div>

<div class="call-overlay hidden" id="callOverlay" aria-hidden="true">
  <div class="call-card" data-call-mode="idle">
    <div class="call-card-header">
      <div class="call-heading">
        <div class="call-label" id="callLabel">Call</div>
        <h3 id="callTitle">Conversation</h3>
        <div class="call-status-row">
          <span class="call-status" id="callStatus">Starting call...</span>
          <span class="call-timer" id="callTimer">--:--</span>
        </div>
      </div>
      <button type="button" class="call-close" id="endCallBtn" aria-label="End call">&times;</button>
    </div>
    <div class="call-media">
      <div class="call-avatar" id="callAvatar">A</div>
      <video id="remoteCallVideo" autoplay playsinline></video>
      <audio id="remoteCallAudio" autoplay></audio>
      <video id="localCallVideo" autoplay muted playsinline></video>
    </div>
    <div class="call-actions">
      <button type="button" class="btn btn-primary hidden" id="acceptCallBtn">Accept</button>
      <button type="button" class="btn btn-secondary hidden" id="declineCallBtn">Decline</button>
      <button type="button" class="btn btn-danger" id="endCallFooterBtn">End Call</button>
    </div>
  </div>
</div>

<style>
  body[data-app-section="alumni"] .messages-dashboard-shell {
    --messenger-bg: #eef5f3;
    --messenger-panel: #ffffff;
    --messenger-panel-soft: #f7fbf9;
    --messenger-border: #d9e7e2;
    --messenger-text: #10201c;
    --messenger-muted: #64736e;
    --messenger-blue: #047857;
    --messenger-purple: #0f766e;
    --messenger-green: #1f9a3a;
    background: var(--messenger-bg) !important;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messages-main {
    min-height: 100vh;
    height: 100vh;
    overflow: hidden;
    background: var(--messenger-bg) !important;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-surface,
  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-panel,
  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-content {
    min-height: 0;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-surface {
    width: 100%;
    height: 100vh;
    min-height: 640px;
    display: grid;
    grid-template-columns:
      clamp(330px, 24vw, 450px)
      minmax(420px, 1fr)
      clamp(300px, 24vw, 464px);
    gap: 18px;
    padding: 20px;
    overflow: hidden;
    background: var(--messenger-bg);
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-surface.is-focus-mode {
    grid-template-columns: clamp(310px, 22vw, 390px) minmax(460px, 1fr);
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-surface.is-focus-mode .messenger-info-panel {
    display: none;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .hidden {
    display: none !important;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-list-panel,
  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-panel,
  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-panel {
    min-width: 0;
    min-height: 0;
    background: var(--messenger-panel);
    border: 1px solid var(--messenger-border);
    border-radius: 10px;
    box-shadow: 0 14px 32px -30px rgb(15 23 42 / 0.72), 0 1px 2px rgb(15 23 42 / 0.07);
    overflow: hidden;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-list-panel {
    display: flex;
    flex-direction: column;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-list-header {
    flex: 0 0 auto;
    padding: 18px 18px 10px;
    background: #ffffff;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 18px;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-title-row h1 {
    margin: 0;
    color: var(--messenger-text);
    font-family: var(--font-family-heading, "Sora", sans-serif);
    font-size: 1.72rem;
    line-height: 1.08;
    font-weight: 800;
    letter-spacing: 0;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-title-actions {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-icon-btn {
    position: relative;
    width: 42px;
    height: 42px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    padding: 0;
    border: 0;
    border-radius: 50%;
    background: #e6f1ed;
    color: #0f3d34;
    cursor: pointer;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-icon-btn:hover {
    background: #d5e8e1;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-action-dot {
    position: absolute;
    top: 7px;
    right: 7px;
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: #059669;
    border: 2px solid #e9edf2;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-search {
    display: flex;
    align-items: center;
    gap: 9px;
    min-height: 45px;
    padding: 0 14px;
    border-radius: 999px;
    background: #eef4f1;
    color: #5f6f69;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-search input {
    width: 100%;
    min-height: 0;
    border: 0 !important;
    padding: 0;
    outline: 0;
    background: transparent !important;
    box-shadow: none !important;
    color: var(--messenger-text) !important;
    font-size: 0.99rem;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-search input::placeholder {
    color: #5f6f69;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-tabs {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 12px;
    overflow-x: auto;
    scrollbar-width: none;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-tabs::-webkit-scrollbar {
    display: none;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-tab {
    position: relative;
    min-height: 40px;
    padding: 0 14px;
    border: 0;
    border-radius: 999px;
    background: transparent;
    color: var(--messenger-text);
    font-size: 0.98rem;
    font-weight: 700;
    white-space: nowrap;
    cursor: pointer;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-tab.active {
    background: #ddf4ea;
    color: #047857;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread-list {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
    padding: 8px 10px 16px;
    background: #ffffff;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread-list::-webkit-scrollbar,
  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-messages-area::-webkit-scrollbar {
    width: 8px;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread-list::-webkit-scrollbar-thumb,
  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-messages-area::-webkit-scrollbar-thumb {
    background: #9a9da1;
    border-radius: 999px;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread {
    width: 100%;
    min-height: 88px;
    display: grid;
    grid-template-columns: 66px minmax(0, 1fr) auto;
    align-items: center;
    gap: 10px;
    padding: 10px;
    border: 0;
    border-radius: 10px;
    background: transparent;
    color: var(--messenger-text);
    text-align: left;
    cursor: pointer;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread:hover {
    background: #f2f3f5;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread.active {
    background: #edf8f3;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread-main {
    min-width: 0;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread-name {
    margin-bottom: 4px;
    overflow: hidden;
    color: var(--messenger-text);
    font-size: 1rem;
    font-weight: 800;
    line-height: 1.25;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread-preview {
    overflow: hidden;
    color: var(--messenger-muted);
    font-size: 0.92rem;
    line-height: 1.25;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread-meta {
    align-self: start;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
    padding-top: 9px;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread-time {
    color: var(--messenger-muted);
    font-size: 0.78rem;
    white-space: nowrap;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-unread-dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #047857;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-avatar-wrap {
    position: relative;
    width: max-content;
    flex: 0 0 auto;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-avatar {
    width: 56px;
    height: 56px;
    border: 1px solid rgb(5 5 5 / 0.08);
    background: linear-gradient(135deg, #e5e7eb, #f3f4f6);
    color: #334155;
    font-weight: 800;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-avatar.avatar-sm {
    width: 34px;
    height: 34px;
    font-size: 0.75rem;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-avatar.avatar-lg {
    width: 92px;
    height: 92px;
    font-size: 1.4rem;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-avatar img {
    display: block;
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-status-dot {
    position: absolute;
    right: 0;
    bottom: 2px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: var(--messenger-green);
    border: 3px solid #ffffff;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-avatar-wrap .avatar-lg + .messenger-status-dot {
    width: 21px;
    height: 21px;
    right: 2px;
    bottom: 7px;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-panel {
    display: flex;
    flex-direction: column;
    position: relative;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-empty-chat {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 32px;
    text-align: center;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-empty-icon {
    width: 92px;
    height: 92px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #eef0f3;
    color: #606770;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-empty-chat h2 {
    margin: 6px 0 0;
    color: var(--messenger-text);
    font-size: 1.25rem;
    font-weight: 800;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-empty-chat p {
    margin: 0;
    color: var(--messenger-muted);
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-content {
    height: 100%;
    min-height: 0;
    display: flex;
    flex-direction: column;
    flex: 1 1 auto;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-header {
    min-height: 80px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 14px 18px;
    border-bottom: 1px solid #e5e7eb;
    background: #ffffff;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-person {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    flex: 1 1 auto;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-copy {
    min-width: 0;
    flex: 1 1 auto;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-name {
    overflow: hidden;
    max-width: min(100%, 28rem);
    color: var(--messenger-text);
    font-size: 1.06rem;
    font-weight: 800;
    line-height: 1.22;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-status {
    color: var(--messenger-muted);
    font-size: 0.86rem;
    line-height: 1.2;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-actions {
    display: flex;
    align-items: center;
    gap: 18px;
    color: var(--messenger-purple);
    flex: 0 0 auto;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-action,
  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-compose-tool,
  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-emoji-btn,
  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-send-btn {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
    padding: 0;
    border: 0;
    border-radius: 50%;
    background: transparent;
    color: inherit;
    cursor: pointer;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-action:hover,
  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-compose-tool:hover,
  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-emoji-btn:hover,
  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-send-btn:hover {
    background: #f2f3f5;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-compose-tool.is-recording {
    background: #fee2e2;
    color: #dc2626;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-back {
    display: none;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-messages-area {
    flex: 1;
    min-height: 0;
    display: flex !important;
    flex-direction: column !important;
    gap: 0 !important;
    overflow-y: auto;
    overscroll-behavior: contain;
    padding: 18px 30px;
    background: #ffffff;
    position: relative !important;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-message {
    display: flex !important;
    align-items: flex-end !important;
    gap: 10px !important;
    max-width: min(74%, 720px) !important;
    width: auto !important;
    clear: both !important;
    margin-bottom: 12px !important;
    position: relative !important;
    float: none !important;
    flex-shrink: 0 !important;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-message.sent {
    align-self: flex-end;
    justify-content: flex-end;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-message.received {
    align-self: flex-start;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-bubble-stack {
    min-width: 0;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-bubble {
    width: fit-content;
    max-width: 100%;
    padding: 9px 15px;
    border-radius: 20px;
    color: var(--messenger-text);
    background: #f0f2f5;
    font-size: 0.98rem;
    line-height: 1.38;
    overflow-wrap: anywhere;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-bubble.is-highlighted {
    outline: 3px solid rgb(245 158 11 / 0.38);
    outline-offset: 3px;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-message.received .messenger-bubble {
    border-bottom-left-radius: 6px;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-message.sent .messenger-bubble {
    margin-left: auto;
    border-bottom-right-radius: 6px;
    background: linear-gradient(135deg, #047857 0%, #0f766e 58%, #2563eb 100%);
    color: #ffffff;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-message-time {
    margin-top: 4px;
    color: var(--messenger-muted);
    font-size: 0.72rem;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-message.sent .messenger-message-time {
    text-align: right;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-system-message {
    align-self: center;
    max-width: min(86%, 36rem);
    margin: 0.4rem auto;
    padding: 0.42rem 0.72rem;
    border-radius: 999px;
    background: #eef4f1;
    color: var(--messenger-muted);
    font-size: 0.78rem;
    font-weight: 700;
    line-height: 1.25;
    text-align: center;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-system-time {
    font-size: 0.72rem;
    color: #7b8a84;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-call-log {
    align-self: center;
    width: min(100%, 620px);
    margin: 0.5rem auto;
    display: grid;
    grid-template-columns: 40px minmax(0, 1fr) auto;
    align-items: center;
    gap: 12px;
    padding: 0.9rem 1rem;
    min-height: 56px;
    border-radius: 12px;
    background: #f7fbf9;
    border: 1px solid #d9e7e2;
    color: var(--messenger-text);
    text-align: left;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-call-log .call-log-icon {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #ddf4ea;
    color: #047857;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-call-log .call-log-title {
    font-weight: 800;
    font-size: 0.88rem;
    line-height: 1.1;
    color: var(--messenger-text);
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-call-log .call-log-subtitle {
    font-size: 0.78rem;
    color: var(--messenger-muted);
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-call-log .call-log-meta {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
    font-size: 0.72rem;
    color: var(--messenger-muted);
    white-space: nowrap;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-call-log .call-log-duration {
    padding: 2px 8px;
    border-radius: 999px;
    background: #eef4f1;
    color: #0f3d34;
    font-weight: 700;
  }

  /* Media grid in info panel */
  .media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(84px, 1fr));
    gap: 8px;
    padding: 10px 6px;
  }

  .media-grid .media-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    text-align: center;
    font-size: 0.82rem;
    color: var(--messenger-muted);
  }

  .messenger-customize-panel .form-group { margin-bottom: 8px; }
  .messenger-customize-panel .form-actions { display:flex; gap:8px; justify-content:flex-end; }
  .messenger-privacy-panel .messenger-setting-toggle { display:flex; justify-content:space-between; align-items:center; gap:8px; padding:6px 0; }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-composer {
    min-height: 70px;
    display: flex;
    align-items: flex-end;
    gap: 8px;
    padding: 12px 16px 16px;
    border-top: 1px solid #e5e7eb;
    background: #ffffff;
    color: #047857;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-composer textarea {
    flex: 1;
    min-height: 44px;
    max-height: 124px;
    resize: none;
    border: 0 !important;
    border-radius: 999px !important;
    padding: 12px 46px 12px 16px;
    background: #f0f2f5 !important;
    color: var(--messenger-text) !important;
    box-shadow: none !important;
    font-family: inherit;
    font-size: 0.98rem;
    line-height: 1.28;
    overflow-y: auto;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-composer textarea:focus {
    outline: none;
    box-shadow: inset 0 0 0 2px rgb(8 102 255 / 0.16) !important;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-emoji-btn {
    margin-left: -46px;
    z-index: 1;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-file-input {
    display: none !important;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-emoji-popover {
    position: absolute;
    right: 58px;
    bottom: 72px;
    z-index: 5;
    width: 260px;
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 4px;
    padding: 10px;
    border: 1px solid var(--messenger-border);
    border-radius: 14px;
    background: #ffffff;
    box-shadow: 0 18px 40px -24px rgb(15 23 42 / 0.72);
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-emoji-popover[hidden] {
    display: none !important;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-emoji-choice {
    width: 36px;
    height: 36px;
    border: 0;
    border-radius: 10px;
    background: transparent;
    font-size: 1.2rem;
    cursor: pointer;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-emoji-choice:hover {
    background: #eef4f1;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-attachment-image {
    display: block;
    width: min(320px, 62vw);
    max-height: 340px;
    object-fit: cover;
    border-radius: 16px;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-attachment-file {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: inherit;
    font-weight: 700;
    text-decoration: none;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-attachment-audio {
    width: min(320px, 68vw);
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-panel {
    display: flex;
    flex-direction: column;
    padding: 22px 20px;
    overflow-y: auto;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-profile {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0 12px 22px;
    text-align: center;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-profile h2 {
    margin: 12px 0 4px;
    color: var(--messenger-text);
    font-size: 1.16rem;
    font-weight: 800;
    line-height: 1.25;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-profile p {
    margin: 0;
    color: var(--messenger-muted);
    font-size: 0.92rem;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-encryption-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: 26px;
    padding: 7px 12px;
    border-radius: 999px;
    background: #e4e6eb;
    color: #1c1e21;
    font-size: 0.83rem;
    font-weight: 800;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-actions {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 24px;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-actions button {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    border: 0;
    background: transparent;
    color: var(--messenger-text);
    font-size: 0.88rem;
    cursor: pointer;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-actions span {
    width: 46px;
    height: 46px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #e4e6eb;
    color: #050505;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-actions button:hover span {
    background: #d8dce2;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-actions button.active span {
    background: #ddf4ea;
    color: #047857;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-sections details {
    border-top: 1px solid transparent;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-sections summary {
    min-height: 56px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    color: var(--messenger-text);
    font-size: 0.98rem;
    font-weight: 800;
    cursor: pointer;
    list-style: none;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-sections summary::-webkit-details-marker {
    display: none;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-sections summary::after {
    content: "";
    width: 8px;
    height: 8px;
    border-right: 2px solid currentColor;
    border-bottom: 2px solid currentColor;
    transform: rotate(45deg) translateY(-2px);
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-sections details[open] summary::after {
    transform: rotate(225deg) translateY(-2px);
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-sections p {
    margin: -4px 0 14px;
    color: var(--messenger-muted);
    font-size: 0.88rem;
    line-height: 1.45;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-loading-state,
  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-empty-state,
  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-error-state {
    padding: 40px 20px;
    color: var(--messenger-muted);
    text-align: center;
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-loading-state p,
  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-empty-state p,
  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-error-state p {
    margin: 12px 0 4px;
    color: var(--messenger-muted);
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-spinner {
    width: 34px;
    height: 34px;
    margin: 0 auto;
    border: 4px solid #e4e6eb;
    border-top-color: #0866ff;
    border-radius: 50%;
    animation: messengerSpin 0.8s linear infinite;
  }

  @keyframes messengerSpin {
    to {
      transform: rotate(360deg);
    }
  }

  body[data-app-section="alumni"] .messages-dashboard-shell .messenger-mobile-footer {
    display: none;
  }

  .filters-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 0.75rem;
    margin-top: 1rem;
  }

  .alumni-results {
    max-height: 400px;
    overflow-y: auto;
    margin-top: 1.5rem;
  }

  .alumni-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border-radius: 12px;
    cursor: pointer;
    transition: background 0.2s ease, border-color 0.2s ease;
    border: 1px solid transparent;
    margin-bottom: 0.5rem;
  }

  .alumni-item:hover {
    background: #f0fdf4;
    border-color: #10b981;
  }

  .alumni-item-info {
    flex: 1;
    min-width: 0;
  }

  .alumni-item-name {
    font-weight: 700;
    color: #111827;
    font-size: 1rem;
    margin-bottom: 0.25rem;
  }

  .alumni-item-details {
    font-size: 0.875rem;
    color: #6b7280;
  }

  .alumni-item .btn {
    flex-shrink: 0;
  }

  .message-compose-tabs {
    display: inline-flex;
    gap: 0.25rem;
    padding: 0.25rem;
    border-radius: 999px;
    background: #eef4f1;
    margin-bottom: 1rem;
  }

  .message-compose-tab {
    border: 0;
    border-radius: 999px;
    padding: 0.55rem 0.9rem;
    background: transparent;
    color: #334155;
    font-weight: 800;
    cursor: pointer;
  }

  .message-compose-tab.active {
    background: #ffffff;
    color: #047857;
    box-shadow: 0 1px 2px rgb(15 23 42 / 0.08);
  }

  .group-compose-panel {
    display: grid;
    gap: 0.75rem;
    margin-top: 1rem;
    padding: 1rem;
    border: 1px solid #d9e7e2;
    border-radius: 12px;
    background: #f7fbf9;
  }

  .group-quick-actions,
  .selected-members {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
  }

  .selected-member-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.6rem;
    border-radius: 999px;
    background: #ddf4ea;
    color: #065f46;
    font-size: 0.82rem;
    font-weight: 800;
  }

  .selected-member-chip button {
    width: 1.2rem;
    height: 1.2rem;
    border: 0;
    border-radius: 50%;
    background: rgb(4 120 87 / 0.16);
    color: #065f46;
    cursor: pointer;
  }

  .alumni-item-select {
    width: 22px;
    height: 22px;
    accent-color: #047857;
    flex: 0 0 auto;
  }

  .messenger-info-settings {
    display: grid;
    gap: 12px;
    margin: 6px 0 10px;
  }

  .messenger-setting-row {
    display: grid;
    gap: 6px;
  }

  .messenger-setting-row label {
    color: var(--messenger-text);
    font-size: 0.9rem;
    font-weight: 700;
  }

  .messenger-setting-inline {
    display: flex;
    align-items: center;
    gap: 8px;
  }

  .messenger-setting-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 8px 10px;
    border-radius: 10px;
    background: #f7fbf9;
    border: 1px solid #e1ede7;
    font-weight: 700;
    color: var(--messenger-text);
  }

  .messenger-setting-toggle input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #047857;
  }

  .messenger-setting-preview {
    border: 1px solid #d9e7e2;
    border-radius: 999px;
    padding: 6px 12px;
    background: #ffffff;
    color: #0f3d34;
    font-weight: 700;
    font-size: 0.8rem;
    cursor: pointer;
  }

  .messenger-setting-preview:hover {
    background: #eef4f1;
  }

  .call-overlay {
    position: fixed;
    inset: 0;
    z-index: 2600;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.25rem;
    background: radial-gradient(circle at top, rgb(15 23 42 / 0.4), rgb(15 23 42 / 0.75));
  }

  .call-overlay.hidden {
    display: none !important;
  }

  .call-overlay .hidden {
    display: none !important;
  }

  .call-card {
    width: min(500px, calc(100vw - 1.5rem));
    display: grid;
    gap: 1rem;
    padding: 1rem 1.1rem 1.15rem;
    border-radius: 20px;
    background: #ffffff;
    box-shadow: 0 32px 70px -36px rgb(15 23 42 / 0.88);
  }

  .call-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 1rem;
  }

  .call-heading {
    display: grid;
    gap: 0.25rem;
  }

  .call-card h3 {
    margin: 0.15rem 0 0;
    color: #10201c;
  }

  .call-label,
  .call-status {
    color: #64736e;
    font-size: 0.9rem;
  }

  .call-status-row {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
  }

  .call-timer {
    padding: 2px 8px;
    border-radius: 999px;
    background: #eef4f1;
    color: #0f3d34;
    font-size: 0.8rem;
    font-weight: 800;
  }

  .call-close {
    width: 2.2rem;
    height: 2.2rem;
    border: 0;
    border-radius: 50%;
    background: #eef4f1;
    color: #10201c;
    font-size: 1.4rem;
    line-height: 1;
    cursor: pointer;
  }

  .call-media {
    position: relative;
    border-radius: 16px;
    overflow: hidden;
    min-height: 300px;
    background: #0f172a;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .call-avatar {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e2e8f0;
    color: #0f172a;
    font-weight: 800;
    font-size: 1.4rem;
  }

  #remoteCallVideo {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: none;
  }

  #remoteCallAudio {
    display: none;
  }

  #localCallVideo {
    position: absolute;
    right: 12px;
    bottom: 12px;
    width: 150px;
    height: 106px;
    border-radius: 12px;
    border: 2px solid #ffffff;
    object-fit: cover;
    display: none;
    box-shadow: 0 12px 30px -20px rgb(15 23 42 / 0.8);
  }

  #remoteCallVideo.active,
  #localCallVideo.active {
    display: block;
  }

  .call-actions {
    display: flex;
    gap: 0.75rem;
  }

  .call-actions .btn {
    flex: 1;
    justify-content: center;
  }

  @media (max-width: 1280px) {
    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-surface {
      grid-template-columns: minmax(300px, 360px) minmax(380px, 1fr) minmax(260px, 320px);
      gap: 14px;
      padding: 14px;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-messages-area {
      padding-inline: 20px;
    }
  }

  @media (max-width: 1060px) {
    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-surface {
      grid-template-columns: minmax(300px, 360px) minmax(0, 1fr);
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-info-panel {
      display: none;
    }
  }

  @media (max-width: 900px) {
    body[data-app-section="alumni"] .messages-dashboard-shell .messages-main {
      width: 100% !important;
      max-width: 100vw;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-surface {
      height: 100vh;
      min-height: 0;
      grid-template-columns: 1fr;
      gap: 0;
      padding: 0;
      background: #ffffff;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-list-panel,
    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-panel {
      width: 100%;
      height: 100vh;
      border: 0;
      border-radius: 0;
      box-shadow: none;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-surface[data-view="list"] .messenger-chat-panel,
    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-surface[data-view="list"] .messenger-info-panel {
      display: none;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-surface[data-view="chat"] .messenger-list-panel {
      display: none;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-surface[data-view="chat"] .messenger-chat-panel {
      display: flex;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-list-header {
      padding: 22px 14px 10px 18px;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-title-row {
      margin-bottom: 16px;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-title-row h1 {
      font-size: 1.82rem;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-icon-btn {
      width: 34px;
      height: 34px;
      background: transparent;
      color: #606770;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-icon-btn:hover {
      background: #f2f3f5;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-action-dot {
      top: 1px;
      right: 3px;
      border-color: #ffffff;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-search {
      min-height: 44px;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-tabs {
      gap: 10px;
      margin-top: 14px;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-tab {
      font-size: 1rem;
      padding: 0 12px;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread-list {
      padding: 6px 8px 52px;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread {
      min-height: 86px;
      grid-template-columns: 74px minmax(0, 1fr) auto;
      padding: 8px 10px;
      border-radius: 12px;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread.active {
      background: transparent;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread-name {
      font-size: 1.04rem;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread-preview {
      font-size: 0.94rem;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-avatar {
      width: 58px;
      height: 58px;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-mobile-footer {
      min-height: 46px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex: 0 0 auto;
      border-top: 1px solid #dadde1;
      color: #0064d1;
      background: #ffffff;
      font-weight: 800;
      text-decoration: none;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-back {
      display: inline-flex;
      background: #f0f2f5;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-header {
      min-height: 66px;
      padding: 10px 12px;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-actions {
      gap: 4px;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-messages-area {
      padding: 14px 12px;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-message {
      max-width: 86%;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-composer {
      min-height: 64px;
      padding: 10px;
      gap: 4px;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-compose-extra {
      display: none;
    }
  }

  @media (max-width: 520px) {
    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-desktop-only {
      display: inline-flex;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-title-actions {
      gap: 5px;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-tab {
      min-height: 38px;
      padding-inline: 10px;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread {
      grid-template-columns: 74px minmax(0, 1fr) 30px;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-thread-time {
      display: none;
    }

    body[data-app-section="alumni"] .messages-dashboard-shell .messenger-chat-action {
      width: 30px;
      height: 30px;
    }

    .filters-grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<script>
(async function() {
  const user = API.getUser();
  const currentUserId = Number(user?.id || 0);

  if (!currentUserId) {
    Utils.error('Please log in to access messages');
    window.location.hash = '#/login';
    return;
  }

  if (typeof App !== 'undefined') {
    App._alumniSidebarDesktopCollapsed = true;
  }

  const messengerSurface = Utils.$('#messengerSurface');
  const conversationsList = Utils.$('#conversationsList');
  const messageInput = Utils.$('#messageInput');
  const chatContent = Utils.$('#chatContent');
  const noChatSelected = Utils.$('#noChatSelected');
  const infoPanel = Utils.$('#conversationInfoPanel');

  let conversations = [];
  let currentConversation = null;
  let messages = [];
  let pollingInterval = null;
  let activeFilter = 'all';
  let filtersLoaded = false;
  let composeMode = 'direct';
  let selectedAlumni = new Map();
  let activeCallStream = null;
  let activeCallId = null;
  let activeCallMode = null;
  let activeCallType = 'audio';
  let activeIncomingCall = null;
  let incomingCallInterval = null;
  let activeCallPollInterval = null;
  let callCloseTimer = null;
  let callTimerInterval = null;
  let callTimerStart = null;
  let peerConnection = null;
  let callSignalPollInterval = null;
  let lastCallSignalId = 0;
  let activeRemoteStream = null;
  let pendingIceCandidates = [];
  let mediaRecorder = null;
  let voiceRecordingStream = null;
  let voiceChunks = [];
  let isCleaningUp = false;
  const rtcConfig = {
    iceServers: [
      { urls: 'stun:stun.l.google.com:19302' }
    ]
  };
  const emojiChoices = [
    0x1f600, 0x1f602, 0x1f60a, 0x1f44d, 0x1f44f, 0x1f64f,
    0x1f389, 0x1f525, 0x2764, 0x1f44b, 0x1f914, 0x1f62e,
    0x1f4aa, 0x1f4af, 0x1f60e, 0x1f642, 0x1f622, 0x1f973
  ].map((codePoint) => String.fromCodePoint(codePoint));

  resetCallOverlay();
  await loadConversations();
  renderEmojiPopover();
  startPolling();
  startIncomingCallPolling();
  initNotificationSettings();
  await handlePendingCallAction();

  Utils.$('#newMessageBtn')?.addEventListener('click', openNewMessageModal);
  Utils.$('#sendBtn')?.addEventListener('click', sendMessage);
  Utils.$('#messagesFocusBtn')?.addEventListener('click', () => {
    messengerSurface?.classList.toggle('is-focus-mode');
  });
  Utils.$('#backToChatsBtn')?.addEventListener('click', () => {
    setMessengerView('list');
  });
  Utils.$('#imageUploadBtn')?.addEventListener('click', () => Utils.$('#imageAttachmentInput')?.click());
  Utils.$('#fileUploadBtn')?.addEventListener('click', () => Utils.$('#fileAttachmentInput')?.click());
  Utils.$('#voiceMessageBtn')?.addEventListener('click', toggleVoiceRecording);
  Utils.$('#emojiBtn')?.addEventListener('click', toggleEmojiPopover);
  Utils.$('#createGroupBtn')?.addEventListener('click', createSelectedGroupConversation);
  Utils.$('#conversationInfoBtn')?.addEventListener('click', () => infoPanel?.scrollTo({ top: 0, behavior: 'smooth' }));
  Utils.$('#profileActionBtn')?.addEventListener('click', showProfileAction);
  Utils.$('#muteActionBtn')?.addEventListener('click', toggleMuteAction);
  Utils.$('#searchInConversationBtn')?.addEventListener('click', searchInCurrentConversation);
  Utils.$('#endCallBtn')?.addEventListener('click', handleCallCloseAction);
  Utils.$('#endCallFooterBtn')?.addEventListener('click', handleCallCloseAction);
  Utils.$('#acceptCallBtn')?.addEventListener('click', acceptIncomingCall);
  Utils.$('#declineCallBtn')?.addEventListener('click', declineIncomingCall);

  document.querySelectorAll('[data-call-type]').forEach((button) => {
    button.addEventListener('click', () => startCall(button.dataset.callType || 'audio'));
  });

  Utils.$('#imageAttachmentInput')?.addEventListener('change', (event) => {
    handleAttachment(event.target.files?.[0], 'image');
    event.target.value = '';
  });

  Utils.$('#fileAttachmentInput')?.addEventListener('change', (event) => {
    handleAttachment(event.target.files?.[0], 'file');
    event.target.value = '';
  });

  document.querySelectorAll('[data-compose-mode]').forEach((button) => {
    button.addEventListener('click', () => setComposeMode(button.dataset.composeMode || 'direct'));
  });

  document.querySelectorAll('[data-org-shortcut]').forEach((button) => {
    button.addEventListener('click', () => openOrganizationGroup(button.dataset.orgShortcut));
  });

  conversationsList?.addEventListener('click', (event) => {
    const item = event.target.closest('[data-conversation-id]');
    if (!item) return;
    selectConversation(item.dataset.conversationId);
  });

  Utils.$('#searchConversations')?.addEventListener('input', Utils.debounce(renderFilteredConversations, 180));
  Utils.$('#searchAlumni')?.addEventListener('input', Utils.debounce(searchAlumni, 400));

  document.querySelectorAll('.messenger-tab').forEach((tab) => {
    tab.addEventListener('click', async () => {
      activeFilter = tab.dataset.filter || 'all';
      document.querySelectorAll('.messenger-tab').forEach((item) => {
        item.classList.toggle('active', item === tab);
      });
      renderFilteredConversations();

      if (tab.dataset.orgGroup) {
        await openOrganizationGroup(tab.dataset.orgGroup, { silent: true });
      }
    });
  });

  document.addEventListener('click', (event) => {
    const popover = Utils.$('#emojiPopover');
    if (!popover || popover.hidden) return;
    if (event.target.closest('#emojiPopover, #emojiBtn')) return;
    popover.hidden = true;
  });

  messageInput?.addEventListener('keypress', (event) => {
    if (event.key === 'Enter' && !event.shiftKey) {
      event.preventDefault();
      sendMessage();
    }
  });

  messageInput?.addEventListener('input', (event) => {
    event.target.style.height = 'auto';
    event.target.style.height = Math.min(event.target.scrollHeight, 124) + 'px';
  });

  function setMessengerView(view) {
    if (messengerSurface) {
      messengerSurface.dataset.view = view;
    }
  }

  function initNotificationSettings() {
    const messageSelect = Utils.$('#messageSoundSelect');
    const ringtoneSelect = Utils.$('#ringtoneSoundSelect');
    const desktopToggle = Utils.$('#desktopNotificationsToggle');
    const soundToggle = Utils.$('#soundAlertsToggle');
    const previewMessage = Utils.$('#previewMessageSound');
    const previewCall = Utils.$('#previewCallSound');

    if (
      !messageSelect ||
      !ringtoneSelect ||
      typeof App === 'undefined' ||
      typeof App.getNotificationPreferences !== 'function'
    ) {
      return;
    }

    const options =
      typeof App.getNotificationToneOptions === 'function'
        ? App.getNotificationToneOptions()
        : { message: [], ringtone: [] };

    messageSelect.innerHTML = (options.message || [])
      .map((option) => `<option value="${Utils.escapeHtml(option.value)}">${Utils.escapeHtml(option.label)}</option>`)
      .join('');
    ringtoneSelect.innerHTML = (options.ringtone || [])
      .map((option) => `<option value="${Utils.escapeHtml(option.value)}">${Utils.escapeHtml(option.label)}</option>`)
      .join('');

    const settings = App.getNotificationPreferences();
    messageSelect.value = settings.messageTone || messageSelect.value || 'chime';
    ringtoneSelect.value = settings.ringtoneTone || ringtoneSelect.value || 'pulse';

    if (desktopToggle) {
      desktopToggle.checked = settings.desktopEnabled !== false;
    }

    if (soundToggle) {
      soundToggle.checked = settings.soundEnabled !== false;
    }

    messageSelect.addEventListener('change', () => {
      App.setNotificationPreferences({ messageTone: messageSelect.value });
    });

    ringtoneSelect.addEventListener('change', () => {
      App.setNotificationPreferences({ ringtoneTone: ringtoneSelect.value });
    });

    desktopToggle?.addEventListener('change', () => {
      const enabled = desktopToggle.checked;
      App.setNotificationPreferences({ desktopEnabled: enabled });
      if (enabled && typeof App.requestBrowserNotificationPermission === 'function') {
        App.requestBrowserNotificationPermission(true);
      }
    });

    soundToggle?.addEventListener('change', () => {
      App.setNotificationPreferences({ soundEnabled: soundToggle.checked });
    });

    previewMessage?.addEventListener('click', () => {
      if (typeof App.previewNotificationSound === 'function') {
        App.previewNotificationSound('message', messageSelect.value);
      }
    });

    previewCall?.addEventListener('click', () => {
      if (typeof App.previewNotificationSound === 'function') {
        App.previewNotificationSound('ringtone', ringtoneSelect.value);
      }
    });

    if (typeof App.ensureNotificationAudioUnlock === 'function') {
      App.ensureNotificationAudioUnlock();
    }
  }

  async function loadConversations() {
    try {
      const response = await API.messaging.getConversations();
      conversations = Array.isArray(response?.data) ? response.data : [];

      if (currentConversation) {
        const updated = conversations.find((conv) => String(conv.id) === String(currentConversation.id));
        if (updated) {
          currentConversation = updated;
          updateConversationChrome(updated);
        }
      }

      renderFilteredConversations();
    } catch (error) {
      console.error('Failed to load conversations:', error);
      conversationsList.innerHTML = `
        <div class="messenger-error-state">
          <p>Failed to load conversations</p>
          <small>${Utils.escapeHtml(error.message || 'Please try again')}</small>
        </div>
      `;
    }
  }

  function renderFilteredConversations() {
    const query = (Utils.$('#searchConversations')?.value || '').trim().toLowerCase();
    const filtered = conversations.filter((conv) => {
      const details = getConversationDetails(conv);
      const unread = Number(conv.unread_count || 0);
      const type = String(conv.type || '').toLowerCase();
      const haystack = `${details.name} ${details.preview}`.toLowerCase();

      if (query && !haystack.includes(query)) {
        return false;
      }

      if (activeFilter === 'unread') {
        return unread > 0;
      }

      if (activeFilter === 'groups') {
        return type !== 'personal';
      }

      if (activeFilter === 'section') {
        return type === 'section';
      }

      if (activeFilter === 'college') {
        return type === 'college';
      }

      if (activeFilter === 'batch') {
        return type === 'program' || String(conv.name || '').toLowerCase().includes('batch');
      }

      return true;
    });

    renderConversations(filtered);
  }

  function renderConversations(list) {
    if (!conversationsList) return;

    if (!list.length) {
      conversationsList.innerHTML = `
        <div class="messenger-empty-state">
          <p>No conversations found</p>
          <small>Start a new message to connect with alumni.</small>
        </div>
      `;
      return;
    }

    conversationsList.innerHTML = list.map((conv) => {
      const details = getConversationDetails(conv);
      const displayName = getDisplayConversationName(details.name);
      const isActive = currentConversation && String(currentConversation.id) === String(conv.id);
      const unread = Number(conv.unread_count || 0);

      return `
        <button type="button" class="messenger-thread ${isActive ? 'active' : ''}" data-conversation-id="${Utils.escapeHtml(String(conv.id))}">
          ${avatarMarkup(details.name, details.image, 'avatar-md', details.isOnline)}
          <span class="messenger-thread-main">
            <span class="messenger-thread-name" title="${Utils.escapeHtml(details.name)}">${Utils.escapeHtml(displayName)}</span>
            <span class="messenger-thread-preview">${Utils.escapeHtml(details.preview)}</span>
          </span>
          <span class="messenger-thread-meta">
            ${details.time ? `<span class="messenger-thread-time">${Utils.escapeHtml(details.time)}</span>` : ''}
            ${unread > 0 ? '<span class="messenger-unread-dot" aria-label="Unread conversation"></span>' : ''}
          </span>
        </button>
      `;
    }).join('');
  }

  window.selectConversation = selectConversation;
  async function selectConversation(conversationId) {
    try {
      currentConversation = conversations.find((conv) => String(conv.id) === String(conversationId));
      if (!currentConversation) return;

      setMessengerView('chat');
      noChatSelected?.classList.add('hidden');
      chatContent?.classList.remove('hidden');
      infoPanel?.classList.remove('is-empty');
      updateConversationChrome(currentConversation);

      await loadMessages(currentConversation.id);
      API.messaging.markAsRead(currentConversation.id).catch(() => {});
      currentConversation.unread_count = 0;
      renderFilteredConversations();
    } catch (error) {
      console.error('Failed to select conversation:', error);
      Utils.error('Failed to load conversation');
    }
  }

  async function loadMessages(conversationId) {
    const container = Utils.$('#messagesArea');
    if (!container) return;
    
    // Show loading state
    container.innerHTML = `
      <div class="messenger-loading-state">
        <div class="messenger-spinner"></div>
        <p>Loading messages...</p>
      </div>
    `;
    
    try {
      const response = await API.messaging.getMessages(conversationId);
      console.log('Messages API response:', response); // Debug log
      messages = Array.isArray(response?.data) ? response.data : [];
      console.log('Parsed messages:', messages); // Debug log
      renderMessages();
    } catch (error) {
      console.error('Failed to load messages:', error);
      container.innerHTML = `
        <div class="messenger-error-state">
          <p>Failed to load messages</p>
          <small>${Utils.escapeHtml(error.message || 'Please try again')}</small>
        </div>
      `;
    }
  }

  function renderMessages() {
    const container = Utils.$('#messagesArea');
    if (!container) return;

    if (!messages.length) {
      container.innerHTML = `
        <div class="messenger-empty-state">
          <p>No messages yet. Start the conversation.</p>
        </div>
      `;
      populateMediaFiles();
      return;
    }

    const parts = [];

    for (let i = 0; i < messages.length; i++) {
      const msg = messages[i];
      const messageType = String(msg.message_type || 'text').toLowerCase();

      // Coalesce related call system events into a single entry showing start time and duration
      if (messageType === 'system' && String(msg.content || '').toLowerCase().includes('call')) {
        const startMsg = msg;
        // Look ahead for an ended/answered/missed/declined event within next few messages
        let endMsg = null;
        for (let j = i + 1; j < Math.min(messages.length, i + 10); j++) {
          const m2 = messages[j];
          if (String(m2.message_type || '').toLowerCase() === 'system') {
            const n = String(m2.content || '').toLowerCase();
            if (n.includes('ended') || n.includes('answered') || n.includes('missed') || n.includes('declined')) {
              endMsg = m2;
              i = j; // advance outer loop to skip consumed entries
              break;
            }
          }
        }

        const source = endMsg || startMsg;
        const details = getCallLogDetails(source, String(source.content || '')) || {};
        const time = formatMessageTime(startMsg.created_at);

        parts.push(`
          <div class="messenger-call-log">
            <div class="call-log-icon">${details.icon || ''}</div>
            <div class="call-log-body">
              <div class="call-log-title">${Utils.escapeHtml(details.title || 'Call')}</div>
              <div class="call-log-subtitle">${Utils.escapeHtml(getCallLogSubtitle(endMsg ? 'ended' : 'started', Number(startMsg.sender_id) === currentUserId, currentConversation ? getConversationDetails(currentConversation).name : 'Alumni'))}</div>
            </div>
            <div class="call-log-meta">
              ${time ? `<span class="call-log-time">${Utils.escapeHtml(time)}</span>` : ''}
              ${details.duration ? `<span class="call-log-duration">${Utils.escapeHtml(details.duration)}</span>` : ''}
            </div>
          </div>
        `);

        continue;
      }

      if (messageType === 'system') {
        parts.push(renderSystemMessage(msg));
        continue;
      }

      const isSent = Number(msg.sender_id) === currentUserId;
      const senderName = msg.sender_name || 'Alumni';
      const imageUrl = resolveImageUrl(msg.sender_image || '');

      parts.push(`
        <div class="messenger-message ${isSent ? 'sent' : 'received'}">
          ${!isSent ? avatarMarkup(senderName, imageUrl, 'avatar-sm', false) : ''}
          <div class="messenger-bubble-stack">
            <div class="messenger-bubble">${renderMessageBody(msg)}</div>
            <div class="messenger-message-time">${Utils.escapeHtml(formatMessageTime(msg.created_at))}</div>
          </div>
        </div>
      `);
    }

    container.innerHTML = parts.join('');
    scrollMessagesToBottom(container);
    populateMediaFiles();
  }

  function renderMessageBody(msg) {
    const content = msg.content || '';
    const fileUrl = resolveImageUrl(msg.file_url || msg.attachment_url || '');
    const fileName = msg.file_name || msg.attachment_name || 'Attachment';
    const messageType = String(msg.message_type || 'text').toLowerCase();

    if (messageType === 'system') {
      return Utils.escapeHtml(content);
    }

    if (messageType === 'image' && fileUrl) {
      return `
        <a href="${Utils.escapeHtml(fileUrl)}" target="_blank" rel="noopener">
          <img class="messenger-attachment-image" src="${Utils.escapeHtml(fileUrl)}" alt="${Utils.escapeHtml(fileName)}">
        </a>
        ${content && content !== fileName ? `<div class="mt-sm">${Utils.escapeHtml(content)}</div>` : ''}
      `;
    }

    if (fileUrl && isAudioAttachment(fileName, fileUrl)) {
      return `
        <audio class="messenger-attachment-audio" controls src="${Utils.escapeHtml(fileUrl)}"></audio>
        <div>${Utils.escapeHtml(content || 'Voice message')}</div>
      `;
    }

    if (fileUrl) {
      return `
        <a class="messenger-attachment-file" href="${Utils.escapeHtml(fileUrl)}" target="_blank" rel="noopener">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"></path>
            <path d="M14 2v6h6"></path>
          </svg>
          <span>${Utils.escapeHtml(fileName)}</span>
        </a>
        ${content && content !== fileName ? `<div class="mt-sm">${Utils.escapeHtml(content)}</div>` : ''}
      `;
    }

    return Utils.escapeHtml(content);
  }

  function scrollMessagesToBottom(container) {
    if (!container) {
      return;
    }

    const applyScroll = () => {
      container.scrollTop = container.scrollHeight;
    };

    applyScroll();
    window.requestAnimationFrame(applyScroll);
    setTimeout(applyScroll, 60);

    container.querySelectorAll('img').forEach((img) => {
      img.addEventListener('load', applyScroll, { once: true });
    });

    container.querySelectorAll('audio').forEach((audio) => {
      audio.addEventListener('loadedmetadata', applyScroll, { once: true });
    });
  }

  function renderSystemMessage(msg) {
    const content = String(msg.content || '').trim();
    if (!content) {
      return '';
    }

    const details = getCallLogDetails(msg, content);
    const time = formatMessageTime(msg.created_at);

    if (details) {
      return `
        <div class="messenger-call-log">
          <div class="call-log-icon">
            ${details.icon}
          </div>
          <div class="call-log-body">
            <div class="call-log-title">${Utils.escapeHtml(details.title)}${details.statusLabel ? ` - ${Utils.escapeHtml(details.statusLabel)}` : ''}</div>
            <div class="call-log-subtitle">${Utils.escapeHtml(details.subtitle)}</div>
          </div>
          <div class="call-log-meta">
            ${time ? `<span class="call-log-time">${Utils.escapeHtml(time)}</span>` : ''}
            ${details.duration ? `<span class="call-log-duration">${Utils.escapeHtml(details.duration)}</span>` : ''}
          </div>
        </div>
      `;
    }

    return `
      <div class="messenger-system-message">
        <span>${Utils.escapeHtml(content)}</span>
        ${time ? `<span class="messenger-system-time">${Utils.escapeHtml(time)}</span>` : ''}
      </div>
    `;
  }

  function getCallLogDetails(msg, content) {
    const normalized = content.toLowerCase();
    if (!normalized.includes('call')) {
      return null;
    }

    const callType = normalized.includes('video') ? 'video' : 'audio';
    const event = normalized.includes('missed')
      ? 'missed'
      : normalized.includes('declined')
        ? 'declined'
        : normalized.includes('answered')
          ? 'answered'
          : normalized.includes('started')
            ? 'started'
            : normalized.includes('ended')
              ? 'ended'
              : 'updated';
    const duration = extractCallDuration(content);
    const isSender = Number(msg.sender_id) === currentUserId;
    const otherName = currentConversation
      ? getConversationDetails(currentConversation).name
      : 'Alumni';
    const statusLabelMap = {
      started: 'Started',
      answered: 'Answered',
      declined: 'Declined',
      ended: 'Ended',
      missed: 'Missed',
      updated: 'Updated'
    };

    return {
      title: callType === 'video' ? 'Video call' : 'Audio call',
      statusLabel: statusLabelMap[event] || 'Updated',
      subtitle: getCallLogSubtitle(event, isSender, otherName),
      duration,
      icon: callType === 'video' ? getVideoCallIcon() : getAudioCallIcon()
    };
  }

  function getCallLogSubtitle(event, isSender, otherName) {
    switch (event) {
      case 'started':
        return isSender ? 'You called' : `${otherName} called you`;
      case 'answered':
        return isSender ? 'You answered the call' : `${otherName} answered`;
      case 'declined':
        return isSender ? 'You declined the call' : `${otherName} declined`;
      case 'ended':
        return isSender ? 'You ended the call' : `${otherName} ended the call`;
      case 'missed':
        return isSender ? 'No answer' : 'You missed the call';
      default:
        return 'Call update';
    }
  }

  function extractCallDuration(content) {
    const match = String(content || '').match(/\b(\d{1,2}:\d{2}(?::\d{2})?)\b/);
    return match ? match[1] : '';
  }

  function getAudioCallIcon() {
    return `
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M6.62 10.79c1.44 2.83 3.76 5.15 6.59 6.59l2.2-2.2a1.5 1.5 0 0 1 1.52-.36c1.11.37 2.31.57 3.57.57A1.5 1.5 0 0 1 22 16.89V20.5A1.5 1.5 0 0 1 20.5 22C10.28 22 2 13.72 2 3.5A1.5 1.5 0 0 1 3.5 2H7.1a1.5 1.5 0 0 1 1.5 1.5c0 1.26.2 2.46.57 3.57.16.52.04 1.08-.35 1.47l-2.2 2.25Z" />
      </svg>
    `;
  }

  function getVideoCallIcon() {
    return `
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
        <path d="M4.5 6.5A2.5 2.5 0 0 1 7 4h7a2.5 2.5 0 0 1 2.5 2.5v1.1l3.1-2.1A1.55 1.55 0 0 1 22 6.8v10.4a1.55 1.55 0 0 1-2.4 1.3l-3.1-2.1v1.1A2.5 2.5 0 0 1 14 20H7a2.5 2.5 0 0 1-2.5-2.5v-11Z" />
      </svg>
    `;
  }

  /* Populate media & files list in the info panel */
  function populateMediaFiles() {
    const container = Utils.$('#mediaFilesList');
    if (!container || !Array.isArray(messages)) return;

    const media = messages.filter((m) => {
      const t = String(m.message_type || 'text').toLowerCase();
      return t === 'image' || t === 'file' || (m.attachment_url || m.file_url || m.attachment_name || m.file_name);
    });

    if (!media.length) {
      container.innerHTML = '<div class="p-sm">No shared media yet.</div>';
      return;
    }

    container.innerHTML = media.map((m) => {
      const fileUrl = resolveImageUrl(m.file_url || m.attachment_url || '');
      const fileName = m.file_name || m.attachment_name || (fileUrl ? fileUrl.split('/').pop() : 'file');
      const isImage = String(m.message_type || '').toLowerCase() === 'image' || (fileUrl && /\.(png|jpe?g|gif|webp)$/i.test(fileUrl));
      return `
        <a class="media-item" href="${Utils.escapeHtml(fileUrl)}" target="_blank" rel="noopener">
          ${isImage ? `<img src="${Utils.escapeHtml(fileUrl)}" alt="${Utils.escapeHtml(fileName)}" style="width:72px;height:72px;object-fit:cover;border-radius:6px;border:1px solid #eef4f1">` : `<div class="media-file-icon" style="width:72px;height:72px;display:flex;align-items:center;justify-content:center;border-radius:6px;border:1px solid #eef4f1;background:#fff"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path></svg></div>`}
          <div style="max-width:84px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${Utils.escapeHtml(fileName)}</div>
        </a>
      `;
    }).join('');
  }

  /* Simple customize & privacy controls persisted in localStorage per conversation */
  function setupInfoPanelFunctions() {
    const saveBtn = Utils.$('#saveChatCustomize');
    const resetBtn = Utils.$('#resetChatCustomize');
    const nicknameInput = Utils.$('#chatNicknameInput');
    const themeSelect = Utils.$('#chatThemeSelect');
    const readReceiptsToggle = Utils.$('#readReceiptsToggle');
    const forwardingToggle = Utils.$('#forwardingToggle');
    const reportBtn = Utils.$('#reportConversationBtn');

    function loadSettings() {
      if (!currentConversation) return;
      const key = `chat_custom_${currentConversation.id}`;
      try {
        const raw = localStorage.getItem(key);
        if (!raw) return;
        const data = JSON.parse(raw || '{}');
        nicknameInput && (nicknameInput.value = data.nickname || '');
        themeSelect && (themeSelect.value = data.theme || '');
        readReceiptsToggle && (readReceiptsToggle.checked = !!data.readReceipts);
        forwardingToggle && (forwardingToggle.checked = !!data.forwarding);
      } catch (err) {
        console.warn('Failed to load chat settings', err);
      }
    }

    function saveSettings() {
      if (!currentConversation) return;
      const key = `chat_custom_${currentConversation.id}`;
      const data = {
        nickname: nicknameInput ? nicknameInput.value.trim() : '',
        theme: themeSelect ? themeSelect.value : '',
        readReceipts: readReceiptsToggle ? !!readReceiptsToggle.checked : false,
        forwarding: forwardingToggle ? !!forwardingToggle.checked : false
      };
      try {
        localStorage.setItem(key, JSON.stringify(data));
        Utils.success('Chat settings saved');
        // Apply local UI effects
        applyChatCustomToChrome(currentConversation, data);
      } catch (err) {
        console.error(err);
        Utils.error('Failed to save settings');
      }
    }

    function resetSettings() {
      if (!currentConversation) return;
      const key = `chat_custom_${currentConversation.id}`;
      localStorage.removeItem(key);
      loadSettings();
      applyChatCustomToChrome(currentConversation, {});
      Utils.success('Chat settings reset');
    }

    function applyChatCustomToChrome(conversation, data) {
      if (!conversation) return;
      const nameEl = Utils.$('#chatName');
      const infoNameEl = Utils.$('#infoName');
      if (nameEl && infoNameEl) {
        const base = getConversationDetails(conversation).name || 'Conversation';
        const nick = data.nickname || '';
        const displayName = getDisplayConversationName(nick || base);
        nameEl.textContent = displayName;
        nameEl.title = nick || base;
        infoNameEl.textContent = displayName;
        infoNameEl.title = nick || base;
      }
      // theme: apply accent color to header
      const theme = data.theme || '';
      const header = Utils.$('.messenger-chat-header');
      if (header) {
        header.style.borderColor = '';
        header.style.background = '';
        if (theme === 'green') header.style.background = '#e8f7ee';
        if (theme === 'blue') header.style.background = '#eef6ff';
        if (theme === 'purple') header.style.background = '#f3f0ff';
      }
    }

    saveBtn && saveBtn.addEventListener('click', saveSettings);
    resetBtn && resetBtn.addEventListener('click', resetSettings);
    reportBtn && reportBtn.addEventListener('click', () => {
      Utils.confirm('Report this conversation?', async () => {
        try {
          await API.messaging.reportConversation(currentConversation.id);
          Utils.success('Conversation reported');
        } catch (err) {
          Utils.error('Failed to report conversation');
        }
      });
    });

    // When opening the info panel, refresh media list and load saved settings
    const infoPanelEl = Utils.$('#conversationInfoPanel');
    if (infoPanelEl) {
      infoPanelEl.addEventListener('transitionend', () => {
        populateMediaFiles();
        loadSettings();
      });
    }

    // Also load immediately for current conversation
    loadSettings();
    applyChatCustomToChrome(currentConversation, JSON.parse(localStorage.getItem(`chat_custom_${currentConversation?.id}`) || '{}'));
  }

  async function sendMessage() {
    if (!currentConversation || !messageInput) return;

    const content = messageInput.value.trim();
    if (!content) return;

    messageInput.value = '';
    messageInput.style.height = 'auto';

    try {
      const response = await API.messaging.sendMessage(currentConversation.id, {
        content,
        message_type: 'text'
      });

      if (response?.data) {
        messages.push(response.data);
        renderMessages();
      }

      await loadConversations();
    } catch (error) {
      console.error('Failed to send message:', error);
      Utils.error('Failed to send message');
      messageInput.value = content;
    }
  }

  async function handleAttachment(file, messageType = 'file', options = {}) {
    if (!file) return;

    if (!currentConversation) {
      Utils.error('Select a conversation first');
      return;
    }

    try {
      const response = await API.messaging.sendAttachment(currentConversation.id, file, {
        messageType,
        content: options.content || file.name || 'Attachment'
      });

      if (response?.data) {
        messages.push(response.data);
        renderMessages();
      }

      await loadConversations();
    } catch (error) {
      console.error('Failed to send attachment:', error);
      Utils.error(error.message || 'Failed to send attachment');
    }
  }

  async function toggleVoiceRecording() {
    const button = Utils.$('#voiceMessageBtn');

    if (!currentConversation) {
      Utils.error('Select a conversation first');
      return;
    }

    if (mediaRecorder && mediaRecorder.state === 'recording') {
      mediaRecorder.stop();
      button?.classList.remove('is-recording');
      return;
    }

    if (!navigator.mediaDevices?.getUserMedia || typeof MediaRecorder === 'undefined') {
      Utils.error('Voice recording is not supported by this browser');
      return;
    }

    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      voiceRecordingStream = stream;
      voiceChunks = [];
      mediaRecorder = new MediaRecorder(stream);

      mediaRecorder.addEventListener('dataavailable', (event) => {
        if (event.data?.size) {
          voiceChunks.push(event.data);
        }
      });

      mediaRecorder.addEventListener('stop', async () => {
        stream.getTracks().forEach((track) => track.stop());
        if (voiceRecordingStream === stream) {
          voiceRecordingStream = null;
        }
        const blob = new Blob(voiceChunks, { type: mediaRecorder.mimeType || 'audio/webm' });
        const file = new File([blob], `voice-message-${Date.now()}.webm`, { type: blob.type || 'audio/webm' });
        voiceChunks = [];
        await handleAttachment(file, 'file', { content: 'Voice message' });
      });

      mediaRecorder.start();
      button?.classList.add('is-recording');
      Utils.success('Recording voice message...');
    } catch (error) {
      console.error('Voice recording failed:', error);
      Utils.error('Unable to access microphone');
    }
  }

  async function startCall(type = 'audio') {
    if (!currentConversation) {
      Utils.error('Select a conversation first');
      return;
    }

    if (String(currentConversation.type || 'personal').toLowerCase() !== 'personal') {
      Utils.error('Calls are available for direct conversations right now');
      return;
    }

    if (!navigator.mediaDevices?.getUserMedia) {
      Utils.error('Calls are not supported by this browser');
      return;
    }

    if (typeof RTCPeerConnection === 'undefined') {
      Utils.error('Live calls are not supported by this browser');
      return;
    }

    if (typeof RTCPeerConnection === 'undefined') {
      Utils.error('Live calls are not supported by this browser');
      return;
    }

    await endActiveCall({ notifyServer: true });

    const details = getConversationDetails(currentConversation);
    const wantsVideo = type === 'video';
    activeCallMode = 'outgoing';
    activeCallType = wantsVideo ? 'video' : 'audio';
    activeIncomingCall = null;

    showCallOverlay({
      mode: 'outgoing',
      label: wantsVideo ? 'Video call' : 'Audio call',
      title: details.name,
      status: 'Preparing your device...'
    });

    try {
      await prepareLocalMedia(activeCallType);

      Utils.$('#callStatus').textContent = `Ringing ${details.name}...`;
      const response = await API.messaging.startCall(currentConversation.id, activeCallType);
      activeCallId = response?.data?.id || response?.data?.call_id || null;

      if (!activeCallId) {
        throw new Error('Call invite was not created');
      }

      await refreshCallHistory(currentConversation.id, response?.data?.history_message);

      await setupPeerConnection();
      const offer = await peerConnection.createOffer();
      await peerConnection.setLocalDescription(offer);
      await sendCallSignal('offer', peerConnection.localDescription);
      startCallSignalPolling();

      Utils.$('#callStatus').textContent = `Waiting for ${details.name} to answer...`;
      startActiveCallPolling(details.name);
    } catch (error) {
      console.error('Call failed:', error);
      Utils.$('#callStatus').textContent = error.message || 'Unable to start the call';
      Utils.error(error.message || (wantsVideo ? 'Unable to access camera or microphone' : 'Unable to access microphone'));
      setTimeout(() => endActiveCall({ notifyServer: true }), 1400);
    }
  }

  function startIncomingCallPolling() {
    checkIncomingCalls();
    incomingCallInterval = setInterval(checkIncomingCalls, 4000);
  }

  async function checkIncomingCalls() {
    if (activeCallId || activeCallMode) return;

    try {
      const response = await API.messaging.getIncomingCalls();
      const incoming = Array.isArray(response?.data) ? response.data[0] : null;
      if (incoming) {
        showIncomingCall(incoming);
      }
    } catch (error) {
      console.error('Failed to check incoming calls:', error);
    }
  }

  async function handlePendingCallAction() {
    if (typeof App === 'undefined' || typeof App.consumePendingCallAction !== 'function') {
      return;
    }

    const pending = App.consumePendingCallAction();
    if (!pending || !pending.callId) {
      return;
    }

    try {
      const response = await API.messaging.getCall(pending.callId);
      const call = response?.data;

      if (!call || call.status !== 'ringing') {
        Utils.info('Call is no longer available');
        return;
      }

      showIncomingCall(call);

      if (pending.action === 'accept') {
        await acceptIncomingCall();
      }

      if (pending.action === 'decline') {
        await declineIncomingCall();
      }
    } catch (error) {
      console.error('Failed to handle pending call:', error);
    }
  }

  function showIncomingCall(call) {
    activeCallId = call.id;
    activeCallMode = 'incoming';
    activeCallType = call.call_type === 'video' ? 'video' : 'audio';
    activeIncomingCall = call;

    if (typeof App !== 'undefined' && typeof App.startGlobalRingtone === 'function') {
      App.startGlobalRingtone(activeCallId);
    }

    showCallOverlay({
      mode: 'incoming',
      label: activeCallType === 'video' ? 'Incoming video call' : 'Incoming audio call',
      title: call.caller_name || 'Alumni',
      status: `${call.caller_name || 'An alumni'} is calling you`
    });
    startActiveCallPolling(call.caller_name || 'Alumni');
  }

  async function acceptIncomingCall() {
    if (!activeCallId || !activeIncomingCall) return;

    if (!navigator.mediaDevices?.getUserMedia) {
      Utils.error('Calls are not supported by this browser');
      return;
    }

    if (typeof App !== 'undefined' && typeof App.stopGlobalRingtone === 'function') {
      App.stopGlobalRingtone(activeCallId);
    }

    const callerName = activeIncomingCall.caller_name || 'Alumni';
    setCallButtonMode('connected');
    Utils.$('#callStatus').textContent = 'Connecting...';

    try {
      const response = await API.messaging.respondCall(activeCallId, 'accepted');
      await prepareLocalMedia(activeCallType);
      await setupPeerConnection();
      startCallSignalPolling();
      await pollCallSignals();
      activeCallMode = 'connected';
      Utils.$('#callStatus').textContent = `Connected with ${callerName}`;
      startCallTimer();
      await refreshCallHistory(activeIncomingCall.conversation_id, response?.data?.history_message);
      await openCallConversation(activeIncomingCall.conversation_id);
      startActiveCallPolling(callerName);
    } catch (error) {
      console.error('Failed to accept call:', error);
      Utils.error(error.message || 'Unable to answer this call');
      await endActiveCall({ notifyServer: true });
    }
  }

  async function declineIncomingCall() {
    const callId = activeCallId;
    if (!callId) return;

    if (typeof App !== 'undefined' && typeof App.stopGlobalRingtone === 'function') {
      App.stopGlobalRingtone(callId);
    }

    try {
      const response = await API.messaging.respondCall(callId, 'declined');
      await refreshCallHistory(activeIncomingCall?.conversation_id || currentConversation?.id, response?.data?.history_message);
    } catch (error) {
      console.error('Failed to decline call:', error);
    }

    await endActiveCall({ notifyServer: false });
  }

  async function openCallConversation(conversationId) {
    if (!conversationId) return;

    if (!conversations.some((conv) => String(conv.id) === String(conversationId))) {
      await loadConversations();
    }

    if (conversations.some((conv) => String(conv.id) === String(conversationId))) {
      await selectConversation(conversationId);
    }
  }

  async function prepareLocalMedia(type = 'audio') {
    const wantsVideo = type === 'video';
    stopActiveCallStream();

    activeCallStream = await navigator.mediaDevices.getUserMedia({
      audio: true,
      video: wantsVideo
    });

    const video = Utils.$('#localCallVideo');
    if (video) {
      video.srcObject = wantsVideo ? activeCallStream : null;
      video.classList.toggle('active', wantsVideo);
    }
  }

  async function setupPeerConnection() {
    closePeerConnection();
    lastCallSignalId = 0;
    pendingIceCandidates = [];

    peerConnection = new RTCPeerConnection(rtcConfig);

    peerConnection.addEventListener('icecandidate', (event) => {
      if (!event.candidate || !activeCallId) return;
      sendCallSignal('ice', event.candidate.toJSON ? event.candidate.toJSON() : event.candidate)
        .catch((error) => console.error('Failed to send ICE candidate:', error));
    });

    peerConnection.addEventListener('track', (event) => {
      const stream = event.streams?.[0];
      if (stream) {
        attachRemoteStream(stream);
      }
    });

    peerConnection.addEventListener('connectionstatechange', () => {
      if (!peerConnection) return;
      const state = peerConnection.connectionState;
      if (state === 'connected') {
        Utils.$('#callStatus').textContent = 'Call connected';
      }
      if (state === 'failed' || state === 'disconnected') {
        Utils.$('#callStatus').textContent = 'Call connection interrupted';
      }
    });

    activeCallStream?.getTracks().forEach((track) => {
      peerConnection.addTrack(track, activeCallStream);
    });
  }

  function attachRemoteStream(stream) {
    activeRemoteStream = stream;
    const hasVideo = stream.getVideoTracks().length > 0;
    const remoteVideo = Utils.$('#remoteCallVideo');
    const remoteAudio = Utils.$('#remoteCallAudio');

    if (remoteVideo) {
      remoteVideo.srcObject = hasVideo ? stream : null;
      remoteVideo.classList.toggle('active', hasVideo);
    }

    if (remoteAudio) {
      remoteAudio.srcObject = hasVideo ? null : stream;
    }

    setCallAvatarVisibility(!hasVideo);
  }

  function startCallSignalPolling() {
    if (callSignalPollInterval) {
      clearInterval(callSignalPollInterval);
    }

    pollCallSignals();
    callSignalPollInterval = setInterval(pollCallSignals, 1200);
  }

  async function pollCallSignals() {
    if (!activeCallId || !peerConnection) return;

    try {
      const response = await API.messaging.getCallSignals(activeCallId, lastCallSignalId);
      const signals = Array.isArray(response?.data) ? response.data : [];

      for (const signal of signals) {
        lastCallSignalId = Math.max(lastCallSignalId, Number(signal.id || 0));
        await handleCallSignal(signal);
      }
    } catch (error) {
      console.error('Failed to poll call signals:', error);
    }
  }

  async function handleCallSignal(signal) {
    if (!peerConnection || !signal?.payload) return;

    if (signal.signal_type === 'offer') {
      await peerConnection.setRemoteDescription(new RTCSessionDescription(signal.payload));
      await flushPendingIceCandidates();
      const answer = await peerConnection.createAnswer();
      await peerConnection.setLocalDescription(answer);
      await sendCallSignal('answer', peerConnection.localDescription);
      return;
    }

    if (signal.signal_type === 'answer') {
      if (!peerConnection.currentRemoteDescription) {
        await peerConnection.setRemoteDescription(new RTCSessionDescription(signal.payload));
        await flushPendingIceCandidates();
      }
      return;
    }

    if (signal.signal_type === 'ice') {
      const candidate = new RTCIceCandidate(signal.payload);
      if (peerConnection.remoteDescription?.type) {
        await peerConnection.addIceCandidate(candidate);
      } else {
        pendingIceCandidates.push(candidate);
      }
    }
  }

  async function flushPendingIceCandidates() {
    if (!peerConnection || !pendingIceCandidates.length) return;

    const candidates = [...pendingIceCandidates];
    pendingIceCandidates = [];

    for (const candidate of candidates) {
      await peerConnection.addIceCandidate(candidate);
    }
  }

  async function sendCallSignal(signalType, payload) {
    if (!activeCallId) return;

    const cleanPayload = payload && typeof payload.toJSON === 'function'
      ? payload.toJSON()
      : payload;

    await API.messaging.sendCallSignal(activeCallId, signalType, cleanPayload);
  }

  function startActiveCallPolling(displayName = 'the other alumni') {
    if (activeCallPollInterval) {
      clearInterval(activeCallPollInterval);
    }

    activeCallPollInterval = setInterval(async () => {
      if (!activeCallId) return;

      try {
        const response = await API.messaging.getCall(activeCallId);
        const call = response?.data;
        if (!call) return;

        if (call.status === 'accepted') {
          activeCallMode = 'connected';
          setCallButtonMode('connected');
          Utils.$('#callStatus').textContent = `Connected with ${displayName}`;
          startCallTimer();
          return;
        }

        if (call.status === 'declined') {
          finishCallSoon('Call declined');
          return;
        }

        if (call.status === 'missed') {
          finishCallSoon('No answer');
          return;
        }

        if (call.status === 'ended') {
          finishCallSoon('Call ended');
        }
      } catch (error) {
        console.error('Failed to poll call status:', error);
      }
    }, 2500);
  }

  function finishCallSoon(message) {
    Utils.$('#callStatus').textContent = message;
    stopCallTimer();
    if (callCloseTimer) return;

    callCloseTimer = setTimeout(() => {
      endActiveCall({ notifyServer: false });
    }, 1300);
  }

  function handleCallCloseAction() {
    if (activeCallMode === 'incoming') {
      declineIncomingCall();
      return;
    }

    endActiveCall({ notifyServer: true });
  }

  function showCallOverlay({ mode, label, title, status }) {
    const overlay = Utils.$('#callOverlay');
    Utils.$('#callLabel').textContent = label;
    Utils.$('#callTitle').textContent = title;
    Utils.$('#callStatus').textContent = status;
    updateCallAvatar(title);
    setCallTimerText('--:--');
    setCallButtonMode(mode);
    overlay?.classList.remove('hidden');
    overlay?.setAttribute('aria-hidden', 'false');
  }

  function setCallButtonMode(mode) {
    const isIncoming = mode === 'incoming';
    const showEnd = mode === 'outgoing' || mode === 'connected';
    const endButton = Utils.$('#endCallFooterBtn');

    Utils.$('#acceptCallBtn')?.classList.toggle('hidden', !isIncoming);
    Utils.$('#declineCallBtn')?.classList.toggle('hidden', !isIncoming);
    endButton?.classList.toggle('hidden', !showEnd);

    if (endButton) {
      endButton.textContent = mode === 'outgoing' ? 'Cancel Call' : 'End Call';
    }

    const overlay = Utils.$('#callOverlay');
    if (overlay) {
      overlay.dataset.callMode = mode || 'idle';
    }
  }

  function setCallTimerText(value) {
    const timer = Utils.$('#callTimer');
    if (timer) {
      timer.textContent = value;
    }
  }

  function startCallTimer() {
    if (callTimerInterval) {
      return;
    }

    callTimerStart = Date.now();
    setCallTimerText(formatCallDuration(0));
    callTimerInterval = setInterval(() => {
      if (!callTimerStart) {
        return;
      }
      setCallTimerText(formatCallDuration(Date.now() - callTimerStart));
    }, 1000);
  }

  function stopCallTimer() {
    if (callTimerInterval) {
      clearInterval(callTimerInterval);
      callTimerInterval = null;
    }
    callTimerStart = null;
    setCallTimerText('--:--');
  }

  function formatCallDuration(durationMs) {
    const totalSeconds = Math.max(0, Math.floor(durationMs / 1000));
    const minutes = Math.floor(totalSeconds / 60);
    const seconds = totalSeconds % 60;
    return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
  }

  function updateCallAvatar(name) {
    const avatar = Utils.$('#callAvatar');
    if (!avatar) {
      return;
    }

    avatar.textContent = Utils.getInitials(name || '?') || '?';
    avatar.style.display = 'flex';
  }

  function setCallAvatarVisibility(isVisible) {
    const avatar = Utils.$('#callAvatar');
    if (!avatar) {
      return;
    }

    avatar.style.display = isVisible ? 'flex' : 'none';
  }

  function stopActiveCallStream() {
    if (activeCallStream) {
      activeCallStream.getTracks().forEach((track) => track.stop());
      activeCallStream = null;
    }

    const video = Utils.$('#localCallVideo');
    if (video) {
      video.pause();
      video.srcObject = null;
      video.classList.remove('active');
    }

    setCallAvatarVisibility(true);
  }

  function closePeerConnection() {
    if (callSignalPollInterval) {
      clearInterval(callSignalPollInterval);
      callSignalPollInterval = null;
    }

    if (peerConnection) {
      peerConnection.close();
      peerConnection = null;
    }

    lastCallSignalId = 0;
    pendingIceCandidates = [];
    activeRemoteStream = null;

    const remoteVideo = Utils.$('#remoteCallVideo');
    if (remoteVideo) {
      remoteVideo.pause();
      remoteVideo.srcObject = null;
      remoteVideo.classList.remove('active');
    }

    const remoteAudio = Utils.$('#remoteCallAudio');
    if (remoteAudio) {
      remoteAudio.pause();
      remoteAudio.srcObject = null;
    }
  }

  function resetCallOverlay() {
    if (activeCallPollInterval) {
      clearInterval(activeCallPollInterval);
      activeCallPollInterval = null;
    }

    if (callCloseTimer) {
      clearTimeout(callCloseTimer);
      callCloseTimer = null;
    }

    closePeerConnection();
    stopActiveCallStream();
    stopCallTimer();
    setCallTimerText('--:--');
    setCallAvatarVisibility(true);
    setCallButtonMode('idle');

    const overlay = Utils.$('#callOverlay');
    overlay?.classList.add('hidden');
    overlay?.setAttribute('aria-hidden', 'true');
  }

  async function endActiveCall(options = {}) {
    const notifyServer = options.notifyServer !== false;
    const callId = activeCallId;
    const callConversationId = activeIncomingCall?.conversation_id || currentConversation?.id || null;

    if (typeof App !== 'undefined' && typeof App.stopGlobalRingtone === 'function' && callId) {
      App.stopGlobalRingtone(callId);
    }

    activeCallId = null;
    activeCallMode = null;
    activeIncomingCall = null;
    resetCallOverlay();

    if (notifyServer && callId) {
      try {
        const response = await API.messaging.endCall(callId);
        await refreshCallHistory(callConversationId, response?.data?.history_message);
      } catch (error) {
        console.error('Failed to end call:', error);
      }
    } else if (callId && callConversationId) {
      await refreshCallHistory(callConversationId);
    }
  }

  function updateConversationChrome(conversation) {
    const details = getConversationDetails(conversation);
    const displayName = getDisplayConversationName(details.name);

    Utils.$('#chatName').textContent = displayName;
    Utils.$('#chatName').title = details.name;
    Utils.$('#chatStatus').textContent = details.subtitle;
    Utils.$('#chatAvatar').innerHTML = avatarMarkup(details.name, details.image, 'avatar-md', details.isOnline);

    Utils.$('#infoAvatar').innerHTML = avatarMarkup(details.name, details.image, 'avatar-lg', details.isOnline);
    Utils.$('#infoName').textContent = displayName;
    Utils.$('#infoName').title = details.name;
    Utils.$('#infoStatus').textContent = details.subtitle;
    Utils.$('#infoMeta').textContent = details.meta;
    // Initialize info panel controls for this conversation
    try { setupInfoPanelFunctions(); } catch (err) { /* ignore */ }
  }

  function getConversationDetails(conv) {
    const participants = Array.isArray(conv.participants) ? conv.participants : [];
    const otherParticipant =
      participants.find((participant) => Number(participant.id) !== currentUserId) ||
      participants[0] ||
      {};
    const type = String(conv.type || 'personal').toLowerCase();
    const name = conv.display_name || conv.name || otherParticipant.name || 'Conversation';
    const image = resolveImageUrl(
      conv.participant_image ||
      conv.display_image ||
      otherParticipant.profile_image ||
      ''
    );
    const lastMessage = conv.last_message || 'No messages yet';
    const lastMessageType = String(conv.last_message_type || 'text').toLowerCase();
    const senderPrefix = lastMessageType === 'system'
      ? ''
      : Number(conv.last_sender_id) === currentUserId
      ? 'You: '
      : conv.last_sender_name
        ? `${conv.last_sender_name}: `
        : '';
    const preview = conv.last_message ? `${senderPrefix}${lastMessage}` : lastMessage;
    const participantCount = Number(conv.participant_count || participants.length || 0);
    const subtitle = type === 'personal'
      ? 'Active now'
      : `${participantCount || participants.length || 1} members`;
    const alumniId = otherParticipant.alumni_number || otherParticipant.alumni_id || '';
    const meta = type === 'personal'
      ? alumniId
        ? `Alumni ID: ${alumniId}`
        : 'Personal alumni conversation'
      : `${participantCount || participants.length || 1} people in this group chat`;

    return {
      name,
      image,
      preview,
      subtitle,
      meta,
      isOnline: type === 'personal',
      time: formatRelativeTime(conv.last_message_time || conv.last_message_at || conv.updated_at || conv.created_at)
    };
  }

  function getDisplayConversationName(name) {
    const raw = String(name || 'Conversation').trim();
    if (raw.length <= 36) {
      return raw;
    }

    return `${raw.slice(0, 33).trimEnd()}…`;
  }

  async function refreshCallHistory(conversationId = null, historyMessage = null) {
    const targetId = conversationId || currentConversation?.id || null;

    if (
      historyMessage &&
      currentConversation &&
      String(historyMessage.conversation_id) === String(currentConversation.id) &&
      !messages.some((message) => String(message.id) === String(historyMessage.id))
    ) {
      messages.push(historyMessage);
      renderMessages();
    }

    try {
      await loadConversations();

      if (
        currentConversation &&
        (!targetId || String(currentConversation.id) === String(targetId))
      ) {
        await loadMessages(currentConversation.id);
      }
    } catch (error) {
      console.error('Failed to refresh call history:', error);
    }
  }

  function avatarMarkup(name, imageUrl, sizeClass = 'avatar-md', showStatus = false) {
    const safeName = Utils.escapeHtml(name || 'Conversation');
    const initials = Utils.escapeHtml(Utils.getInitials(name || '?') || '?');
    const image = imageUrl
      ? `<img src="${Utils.escapeHtml(imageUrl)}" alt="${safeName}" onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';"><span style="display:none">${initials}</span>`
      : `<span>${initials}</span>`;

    return `
      <span class="messenger-avatar-wrap">
        <span class="avatar ${sizeClass} messenger-avatar">${image}</span>
        ${showStatus ? '<span class="messenger-status-dot" aria-hidden="true"></span>' : ''}
      </span>
    `;
  }

  function isAudioAttachment(fileName, fileUrl) {
    return /\.(webm|ogg|mp3|wav|m4a|aac)$/i.test(`${fileName} ${fileUrl}`);
  }

  async function openOrganizationGroup(groupType, options = {}) {
    const apiType = groupType === 'batch' ? 'program' : groupType;

    try {
      if (!options.silent) {
        Utils.success('Opening group chat...');
      }

      const response = await API.messaging.joinOrgGroup(apiType);
      const conversationId = response?.data?.id || response?.data?.conversation_id;
      await loadConversations();

      if (conversationId) {
        await selectConversation(conversationId);
      }

      if (!options.silent) {
        Utils.closeModal('#newMessageModal');
      }
    } catch (error) {
      console.error('Failed to open organization group:', error);
      Utils.error(error.message || 'Unable to open that group chat');
    }
  }

  function renderEmojiPopover() {
    const popover = Utils.$('#emojiPopover');
    if (!popover) return;

    popover.innerHTML = emojiChoices.map((emoji) => `
      <button type="button" class="messenger-emoji-choice" data-emoji="${emoji}" aria-label="Insert emoji">${emoji}</button>
    `).join('');

    popover.addEventListener('click', (event) => {
      const button = event.target.closest('[data-emoji]');
      if (!button || !messageInput) return;
      insertAtCursor(messageInput, button.dataset.emoji || '');
      popover.hidden = true;
      messageInput.focus();
    });
  }

  function toggleEmojiPopover() {
    const popover = Utils.$('#emojiPopover');
    if (!popover) return;
    popover.hidden = !popover.hidden;
  }

  function insertAtCursor(input, value) {
    const start = input.selectionStart ?? input.value.length;
    const end = input.selectionEnd ?? input.value.length;
    input.value = `${input.value.slice(0, start)}${value}${input.value.slice(end)}`;
    const nextPosition = start + value.length;
    input.setSelectionRange(nextPosition, nextPosition);
    input.dispatchEvent(new Event('input'));
  }

  function showProfileAction() {
    if (!currentConversation) {
      Utils.error('Select a conversation first');
      return;
    }

    const details = getConversationDetails(currentConversation);
    Utils.success(`${details.name} profile is shown in the details panel`);
  }

  function toggleMuteAction() {
    if (!currentConversation) {
      Utils.error('Select a conversation first');
      return;
    }

    const button = Utils.$('#muteActionBtn');
    const muted = button?.classList.toggle('active');
    Utils.success(muted ? 'Conversation muted' : 'Conversation unmuted');
  }

  function searchInCurrentConversation() {
    if (!messages.length) {
      Utils.error('No messages to search');
      return;
    }

    const query = window.prompt('Search in this conversation');
    if (!query) return;

    const match = Array.from(document.querySelectorAll('.messenger-bubble')).find((bubble) =>
      bubble.textContent.toLowerCase().includes(query.toLowerCase())
    );

    if (!match) {
      Utils.error('No matching message found');
      return;
    }

    document.querySelectorAll('.messenger-bubble.is-highlighted').forEach((bubble) => {
      bubble.classList.remove('is-highlighted');
    });
    match.classList.add('is-highlighted');
    match.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  function formatRelativeTime(value) {
    if (!value) return '';
    try {
      return Utils.timeAgo(value);
    } catch {
      return '';
    }
  }

  function formatMessageTime(value) {
    if (!value) return '';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
      return formatRelativeTime(value);
    }
    return date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
  }

  function setComposeMode(mode) {
    composeMode = mode === 'group' ? 'group' : 'direct';
    document.querySelectorAll('[data-compose-mode]').forEach((button) => {
      button.classList.toggle('active', button.dataset.composeMode === composeMode);
    });
    Utils.$('#groupComposePanel')?.classList.toggle('hidden', composeMode !== 'group');
    renderSelectedGroupMembers();
    searchAlumni();
  }

  window.toggleGroupMember = function(alumniId, alumniName, event) {
    event?.preventDefault();
    const id = Number(alumniId);
    if (!id) return;

    if (selectedAlumni.has(id)) {
      selectedAlumni.delete(id);
    } else {
      selectedAlumni.set(id, alumniName || 'Alumni');
    }

    renderSelectedGroupMembers();
    searchAlumni();
  };

  function renderSelectedGroupMembers() {
    const container = Utils.$('#selectedGroupMembers');
    const createButton = Utils.$('#createGroupBtn');
    if (!container || !createButton) return;

    createButton.disabled = selectedAlumni.size < 2;

    if (selectedAlumni.size === 0) {
      container.innerHTML = '<span class="text-muted text-sm">Select at least two alumni for a custom group chat.</span>';
      return;
    }

    container.innerHTML = Array.from(selectedAlumni.entries()).map(([id, name]) => `
      <span class="selected-member-chip">
        ${Utils.escapeHtml(name)}
        <button type="button" aria-label="Remove ${Utils.escapeHtml(name)}" onclick="toggleGroupMember(${id}, '${Utils.escapeHtml(name).replace(/'/g, "\\'")}')">&times;</button>
      </span>
    `).join('');
  }

  async function createSelectedGroupConversation() {
    if (selectedAlumni.size < 2) {
      Utils.error('Select at least two alumni');
      return;
    }

    const participantIds = Array.from(selectedAlumni.keys());
    const name = Utils.$('#groupNameInput')?.value.trim() ||
      Array.from(selectedAlumni.values()).slice(0, 3).join(', ');

    try {
      const response = await API.messaging.createConversation({
        participant_ids: participantIds,
        name
      });

      Utils.closeModal('#newMessageModal');
      selectedAlumni.clear();
      Utils.$('#groupNameInput').value = '';
      Utils.$('#searchAlumni').value = '';
      Utils.$('#alumniResults').innerHTML = '';
      renderSelectedGroupMembers();
      await loadConversations();

      if (response?.data?.id) {
        await selectConversation(response.data.id);
        Utils.success('Group chat created');
      }
    } catch (error) {
      console.error('Failed to create group chat:', error);
      Utils.error(error.message || 'Failed to create group chat');
    }
  }

  function openNewMessageModal() {
    Utils.openModal('#newMessageModal');
    setComposeMode(composeMode);
    loadFilters();
    setTimeout(() => searchAlumni(), 100);
  }

  async function loadFilters() {
    if (filtersLoaded) return;
    filtersLoaded = true;

    try {
      const currentYear = new Date().getFullYear();
      const batchSelect = Utils.$('#filterBatch');
      batchSelect.innerHTML = '<option value="">All Batches</option>';
      for (let year = currentYear; year >= currentYear - 40; year--) {
        batchSelect.innerHTML += `<option value="${year}">${year}</option>`;
      }

      const collegesResponse = await API.organization.getColleges();
      const colleges = collegesResponse?.data || [];
      const collegeSelect = Utils.$('#filterCollege');
      collegeSelect.innerHTML = '<option value="">All Colleges</option>';
      colleges.forEach((college) => {
        collegeSelect.innerHTML += `<option value="${college.id}">${Utils.escapeHtml(college.name)}</option>`;
      });

      batchSelect.addEventListener('change', searchAlumni);
      collegeSelect.addEventListener('change', async function() {
        const collegeId = this.value;
        const programSelect = Utils.$('#filterProgram');

        if (!collegeId) {
          programSelect.innerHTML = '<option value="">All Programs</option>';
          searchAlumni();
          return;
        }

        try {
          const programsResponse = await API.organization.getPrograms(collegeId);
          const programs = programsResponse?.data || [];
          programSelect.innerHTML = '<option value="">All Programs</option>';
          programs.forEach((program) => {
            programSelect.innerHTML += `<option value="${program.id}">${Utils.escapeHtml(program.name)}</option>`;
          });
          searchAlumni();
        } catch (error) {
          console.error('Error loading programs:', error);
        }
      });

      Utils.$('#filterProgram').addEventListener('change', searchAlumni);
    } catch (error) {
      console.error('Error loading filters:', error);
    }
  }

  async function searchAlumni() {
    const query = Utils.$('#searchAlumni').value.trim();
    const batch = Utils.$('#filterBatch').value;
    const college = Utils.$('#filterCollege').value;
    const program = Utils.$('#filterProgram').value;
    const resultsDiv = Utils.$('#alumniResults');

    resultsDiv.innerHTML = `
      <div class="messenger-loading-state">
        <div class="messenger-spinner"></div>
        <p>Searching alumni...</p>
      </div>
    `;

    try {
      const params = {};
      if (query) params.q = query;
      if (batch) params.batch = batch;
      if (college) params.college = college;
      if (program) params.program = program;

      const response = await API.messaging.searchAlumni(params);
      const alumni = response?.data || [];

      if (alumni.length === 0) {
        resultsDiv.innerHTML = `
          <div class="messenger-empty-state">
            <p>No alumni found</p>
            <small>Try adjusting your search filters.</small>
          </div>
        `;
        return;
      }

      resultsDiv.innerHTML = alumni.map((alumnus) => {
        const id = Number(alumnus.id);
        const name = alumnus.name || 'Alumni';
        const imageUrl = resolveImageUrl(alumnus.profile_image || '');
        const safeNameForHandler = Utils.escapeHtml(name).replace(/'/g, "\\'");
        const selected = selectedAlumni.has(id);
        const actionMarkup = composeMode === 'group'
          ? `<input class="alumni-item-select" type="checkbox" ${selected ? 'checked' : ''} aria-label="Select ${Utils.escapeHtml(name)}">`
          : `<button class="btn btn-sm btn-primary" type="button">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
              </svg>
            </button>`;
        const clickHandler = composeMode === 'group'
          ? `toggleGroupMember(${id}, '${safeNameForHandler}', event)`
          : `startConversation(${id}, '${safeNameForHandler}')`;

        return `
          <div class="alumni-item" onclick="${clickHandler}">
            ${avatarMarkup(name, imageUrl, 'avatar-md', false)}
            <div class="alumni-item-info">
              <div class="alumni-item-name">${Utils.escapeHtml(name)}</div>
              <div class="alumni-item-details">
                ${Utils.escapeHtml(alumnus.college_name || 'N/A')} | ${Utils.escapeHtml(alumnus.program_name || 'N/A')}${alumnus.batch_year ? ' | Batch ' + Utils.escapeHtml(String(alumnus.batch_year)) : ''}
              </div>
            </div>
            ${actionMarkup}
          </div>
        `;
      }).join('');
    } catch (error) {
      console.error('Error searching alumni:', error);
      resultsDiv.innerHTML = `
        <div class="messenger-error-state">
          <p>Error searching alumni</p>
          <small>${Utils.escapeHtml(error.message || 'Please try again')}</small>
        </div>
      `;
    }
  }

  window.startConversation = async function(alumniId, alumniName) {
    try {
      const response = await API.messaging.createConversation({
        participant_ids: [alumniId]
      });

      Utils.closeModal('#newMessageModal');
      Utils.$('#searchAlumni').value = '';
      Utils.$('#alumniResults').innerHTML = '';

      await loadConversations();

      if (response?.data?.id) {
        await selectConversation(response.data.id);
        Utils.success(`Started conversation with ${alumniName}`);
      }
    } catch (error) {
      console.error('Error creating conversation:', error);
      Utils.error(error.message || 'Failed to start conversation');
    }
  };

  function startPolling() {
    pollingInterval = setInterval(async () => {
      await loadConversations();
      if (currentConversation) {
        await loadMessages(currentConversation.id);
      }
    }, 10000);
  }

  async function cleanup() {
    if (isCleaningUp) {
      return;
    }

    isCleaningUp = true;

    if (pollingInterval) {
      clearInterval(pollingInterval);
      pollingInterval = null;
    }

    if (incomingCallInterval) {
      clearInterval(incomingCallInterval);
      incomingCallInterval = null;
    }

    if (mediaRecorder && mediaRecorder.state === 'recording') {
      mediaRecorder.stop();
    }

    if (voiceRecordingStream) {
      voiceRecordingStream.getTracks().forEach((track) => track.stop());
      voiceRecordingStream = null;
    }

    await endActiveCall({ notifyServer: true });
  }

  window.__pageCleanup = cleanup;
  window.addEventListener('hashchange', () => cleanup(), { once: true });
  window.addEventListener('beforeunload', () => cleanup(), { once: true });

  function resolveImageUrl(imageUrl) {
    if (!imageUrl) return '';
    if (API.resolveAssetUrl) {
      return API.resolveAssetUrl(imageUrl);
    }
    if (/^https?:\/\//i.test(imageUrl) || imageUrl.startsWith('data:')) {
      return imageUrl;
    }
    const apiRoot = (API.baseUrl || '').replace(/\/api\/v1\/?$/, '');
    return apiRoot ? `${apiRoot}${imageUrl.startsWith('/') ? '' : '/'}${imageUrl}` : imageUrl;
  }

  // Page cleanup function - called by router when leaving this page
  window.__pageCleanup = async function() {
    console.log('Messages page cleanup: stopping video calls and streams');
    
    // Stop any active video calls
    if (typeof resetCallOverlay === 'function') {
      resetCallOverlay();
    }
    
    // Clear any polling intervals
    if (activeCallPollInterval) {
      clearInterval(activeCallPollInterval);
      activeCallPollInterval = null;
    }
    
    if (conversationPollInterval) {
      clearInterval(conversationPollInterval);
      conversationPollInterval = null;
    }
    
    if (messagesPollInterval) {
      clearInterval(messagesPollInterval);
      messagesPollInterval = null;
    }
  };
})();
</script>
