/**
 * Messagerie support web — AJAX + Pusher (sans rechargement de page).
 */
(function () {
  const cfg = window.DOMINI_SUPPORT_MESSENGER;
  if (!cfg) return;

  const app = document.getElementById('msgApp');
  if (!app) return;

  const csrf = cfg.csrf;
  const apiBase = cfg.apiBase;
  const accent = cfg.accent || '#FF0000';
  const staffBubble = cfg.staffBubble || '#111';

  function getXsrfCookie() {
    const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
    if (!match?.[1]) return null;
    try {
      return decodeURIComponent(match[1]);
    } catch (_) {
      return match[1];
    }
  }

  function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta?.content) return meta.content;
    return csrf;
  }

  function apiFetch(url, options = {}) {
    const token = getCsrfToken();
    const xsrfCookie = getXsrfCookie();
    const headers = Object.assign(
      {
        Accept: 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': token,
      },
      options.headers || {}
    );
    if (xsrfCookie) {
      headers['X-XSRF-TOKEN'] = xsrfCookie;
    }
    return fetch(url, Object.assign({ credentials: 'same-origin', headers }, options));
  }

  function httpErrorMessage(res, json, rawText) {
    if (res.status === 419) {
      return (json && json.message) || 'Session expirée. Rechargez la page (F5) puis réessayez.';
    }
    if (res.status === 401 || res.status === 403) {
      return (json && json.message) || 'Accès refusé. Reconnectez-vous.';
    }
    if (json && json.message) return json.message;
    const snippet = (rawText || '').replace(/\s+/g, ' ').trim().slice(0, 120);
    return (
      'Réponse serveur invalide (attendu JSON). Vérifiez la connexion et réessayez.' +
      (snippet ? ' Début : ' + snippet : '')
    );
  }

  let activeThreadId = cfg.activeThreadId || null;
  let messageIds = new Set();
  let pusher = null;
  let threadChannel = null;

  const elList = document.getElementById('msgThreadList');
  const elMain = document.getElementById('msgMainPanel');
  const elBody = document.getElementById('msgBody');
  const elComposer = document.getElementById('msgComposer');
  const elComposerForm = document.getElementById('msgComposerForm');
  const elComposerInput = document.getElementById('msgComposerInput');
  const elUnreadPill = document.getElementById('msgUnreadPill');
  const elHeaderName = document.getElementById('msgHeaderName');
  const elHeaderMeta = document.getElementById('msgHeaderMeta');
  const elHeaderAvatar = document.getElementById('msgHeaderAvatar');
  const elStatusBtn = document.getElementById('msgStatusBtn');

  function escapeHtml(s) {
    const d = document.createElement('div');
    d.textContent = s;
    return d.innerHTML;
  }

  function formatTime(iso) {
    if (!iso) return '';
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) return '';
    return d.toLocaleString('fr-FR', { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' });
  }

  function formatTimeShort(iso) {
    if (!iso) return '';
    const d = new Date(iso);
    if (Number.isNaN(d.getTime())) return '';
    return d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
  }

  function initial(name) {
    return (name || '?').trim().charAt(0).toUpperCase();
  }

  function scrollBody() {
    if (!elBody) return;
    requestAnimationFrame(() => {
      elBody.scrollTop = elBody.scrollHeight;
    });
  }

  function cleanDisplayBody(body) {
    if (!body) return '';
    return String(body).replace(/^\[[^\]]+\]\s*/, '').trim() || String(body);
  }

  function messageLabel(msg) {
    const out = msg.sender === 'admin';
    const name = msg.sender_name || (out ? 'Domini' : 'Client');
    const role = msg.sender_label || '';
    if (out) return 'Vous' + (role ? ' · ' + role : '');
    return name + (role ? ' · ' + role : '');
  }

  function renderBubble(msg) {
    const out = msg.sender === 'admin';
    const wrap = document.createElement('div');
    wrap.className = 'msg-bubble-wrap ' + (out ? 'out' : 'in');
    wrap.dataset.messageId = String(msg.id);

    const label = document.createElement('div');
    label.className = 'msg-bubble-label';
    label.textContent = messageLabel(msg);

    const div = document.createElement('div');
    div.className = 'msg-bubble ' + (out ? 'out' : 'in');
    const text = msg.display_body || cleanDisplayBody(msg.body);
    div.innerHTML =
      escapeHtml(text) +
      '<div class="msg-bubble-time">' +
      escapeHtml(formatTime(msg.created_at)) +
      '</div>';

    wrap.appendChild(label);
    wrap.appendChild(div);
    return wrap;
  }

  function appendMessage(msg, skipDup) {
    if (!elBody || !msg || !msg.id) return;
    if (skipDup && messageIds.has(msg.id)) return;
    messageIds.add(msg.id);
    elBody.appendChild(renderBubble(msg));
    scrollBody();
  }

  function clearMessages() {
    messageIds.clear();
    if (elBody) elBody.innerHTML = '';
  }

  function updateThreadListItem(threadId, data) {
    const item = elList?.querySelector('[data-thread-id="' + threadId + '"]');
    if (!item || !data) return;
    const preview = item.querySelector('.msg-thread-preview');
    const time = item.querySelector('.msg-thread-time');
    const badge = item.querySelector('.msg-badge');
    const msg = data.message;
    if (msg && preview) {
      const prefix = msg.sender === 'admin' ? 'Vous : ' : '';
      const text = msg.display_body || cleanDisplayBody(msg.body || '');
      preview.textContent = prefix + text.substring(0, 60);
    }
    if (data.thread?.last_message_at && time) {
      time.textContent = formatTimeShort(data.thread.last_message_at);
    }
    const unread = data.thread?.unread_admin ?? 0;
    if (unread > 0) {
      item.classList.add('unread');
      if (badge) badge.textContent = String(unread);
      else {
        const meta = item.querySelector('.msg-thread-meta');
        if (meta) {
          const b = document.createElement('span');
          b.className = 'msg-badge';
          b.textContent = String(unread);
          meta.appendChild(b);
        }
      }
    } else {
      item.classList.remove('unread');
      badge?.remove();
    }
    if (item.parentNode && item.parentNode.firstChild !== item) {
      item.parentNode.prepend(item);
    }
  }

  function setActiveListItem(threadId) {
    elList?.querySelectorAll('.msg-thread-item').forEach((el) => {
      el.classList.toggle('active', String(el.dataset.threadId) === String(threadId));
    });
  }

  function showChatPanel(show) {
    if (!elMain) return;
    elMain.querySelector('.msg-main-empty')?.classList.toggle('hidden', show);
    elMain.querySelector('.msg-chat-active')?.classList.toggle('hidden', !show);
  }

  function renderHeader(user, status) {
    if (!user) return;
    if (elHeaderName) elHeaderName.textContent = user.name || 'Utilisateur';
    if (elHeaderMeta) {
      elHeaderMeta.textContent =
        [user.email, user.telephone, user.role].filter(Boolean).join(' · ');
    }
    if (elHeaderAvatar) elHeaderAvatar.textContent = initial(user.name);
    if (elStatusBtn) {
      elStatusBtn.textContent = status === 'closed' ? 'Rouvrir' : 'Fermer';
      elStatusBtn.dataset.action = status === 'closed' ? 'reopen' : 'close';
    }
  }

  function renderMessages(messages) {
    clearMessages();
    if (!messages?.length) {
      if (elBody) {
        elBody.innerHTML =
          '<p style="text-align:center;color:#888;margin:auto;">En attente du premier message…</p>';
      }
      return;
    }
    messages.forEach((m) => appendMessage(m, false));
  }

  function subscribeThreadChannel(threadId) {
    if (!pusher || !threadId) return;
    if (threadChannel) {
      pusher.unsubscribe(threadChannel);
      threadChannel = null;
    }
    threadChannel = 'support.thread.' + threadId;
    const ch = pusher.subscribe(threadChannel);
    ch.bind('support.message', onPusherMessage);
    ch.bind('support.thread.updated', onPusherThreadUpdated);
  }

  function onPusherMessage(data) {
    if (!data || !data.message) return;
    updateThreadListItem(data.thread_id, data);
    if (String(data.thread_id) === String(activeThreadId)) {
      appendMessage(data.message, true);
      if (data.message.sender === 'user' && elUnreadPill) {
        // lu côté serveur au chargement ; pas d’incrément local
      }
    }
    refreshUnreadTotal();
  }

  function onPusherThreadUpdated(data) {
    if (!data || String(data.thread_id) !== String(activeThreadId)) return;
    if (elStatusBtn && data.thread) {
      elStatusBtn.textContent = data.thread.status === 'closed' ? 'Rouvrir' : 'Fermer';
      elStatusBtn.dataset.action = data.thread.status === 'closed' ? 'reopen' : 'close';
    }
  }

  function refreshUnreadTotal() {
    if (!elList || !elUnreadPill) return;
    let total = 0;
    elList.querySelectorAll('.msg-badge').forEach((b) => {
      total += parseInt(b.textContent, 10) || 0;
    });
    if (total > 0) {
      elUnreadPill.textContent = total + ' non lu(s)';
      elUnreadPill.style.display = 'inline-block';
    } else {
      elUnreadPill.style.display = 'none';
    }
  }

  async function loadThread(threadId) {
    activeThreadId = threadId;
    setActiveListItem(threadId);
    showChatPanel(true);
    if (elBody) elBody.innerHTML = '<p style="text-align:center;color:#888;margin:2rem auto;">Chargement…</p>';
    clearMessages();

    const url = new URL(window.location.href);
    url.searchParams.set('conversation', threadId);
    window.history.replaceState({}, '', url);

    subscribeThreadChannel(threadId);

    try {
      const res = await apiFetch(apiBase + '/threads/' + threadId);
      const json = await parseJsonResponse(res);
      if (!json.success || !json.data) throw new Error(json.message || 'Erreur');
      const user = json.data.user;
      renderHeader(user, json.data.thread?.status);
      renderMessages(json.data.messages);
      scrollBody();
    } catch (e) {
      if (elBody) {
        elBody.innerHTML =
          '<p style="text-align:center;color:#c00;">' + escapeHtml(e.message || 'Chargement impossible') + '</p>';
      }
    }
  }

  async function parseJsonResponse(res) {
    const text = await res.text();
    let json = null;
    try {
      json = JSON.parse(text);
    } catch (_) {
      if (!res.ok) {
        throw new Error(httpErrorMessage(res, null, text));
      }
      throw new Error(httpErrorMessage(res, null, text));
    }
    if (!res.ok) {
      throw new Error(httpErrorMessage(res, json, text));
    }
    return json;
  }

  async function sendMessage(body) {
    if (!activeThreadId || !body.trim()) return;
    const res = await apiFetch(apiBase + '/threads/' + activeThreadId + '/messages', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ body: body.trim() }),
    });
    const json = await parseJsonResponse(res);
    if (!json.success) throw new Error(json.message || 'Envoi impossible');
    if (json.data?.messages) {
      const last = json.data.messages[json.data.messages.length - 1];
      if (last) appendMessage(last, true);
    }
    updateThreadListItem(activeThreadId, {
      thread_id: activeThreadId,
      message: json.data?.messages?.slice(-1)[0],
      thread: json.data?.thread,
    });
  }

  async function toggleStatus() {
    if (!activeThreadId || !elStatusBtn) return;
    const action = elStatusBtn.dataset.action === 'reopen' ? 'reopen' : 'close';
    const res = await apiFetch(apiBase + '/threads/' + activeThreadId + '/' + action, {
      method: 'POST',
    });
    const json = await parseJsonResponse(res);
    if (!json.success) throw new Error(json.message || 'Erreur');
    const status = json.data?.status || (action === 'close' ? 'closed' : 'open');
    elStatusBtn.textContent = status === 'closed' ? 'Rouvrir' : 'Fermer';
    elStatusBtn.dataset.action = status === 'closed' ? 'reopen' : 'close';
  }

  function initPusher() {
    if (!cfg.pusherKey || !window.Pusher) return;
    pusher = new Pusher(cfg.pusherKey, {
      cluster: cfg.pusherCluster || 'mt1',
      forceTLS: true,
    });
    const staffCh = pusher.subscribe('support.staff');
    staffCh.bind('support.message', onPusherMessage);
    staffCh.bind('support.thread.updated', onPusherThreadUpdated);
    if (activeThreadId) subscribeThreadChannel(activeThreadId);
  }

  elList?.addEventListener('click', (e) => {
    const item = e.target.closest('.msg-thread-item');
    if (!item) return;
    e.preventDefault();
    const id = item.dataset.threadId;
    if (id && String(id) !== String(activeThreadId)) loadThread(id);
  });

  elComposerForm?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const text = elComposerInput?.value || '';
    if (!text.trim() || elComposerForm.dataset.sending === '1') return;
    elComposerForm.dataset.sending = '1';
    try {
      await sendMessage(text);
      if (elComposerInput) elComposerInput.value = '';
    } catch (err) {
      alert(err.message || 'Envoi impossible');
    } finally {
      elComposerForm.dataset.sending = '0';
    }
  });

  elComposerInput?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      elComposerForm?.requestSubmit();
    }
  });

  elStatusBtn?.addEventListener('click', async () => {
    try {
      await toggleStatus();
    } catch (err) {
      alert(err.message || 'Erreur');
    }
  });

  if (cfg.initialMessages?.length) {
    cfg.initialMessages.forEach((m) => appendMessage(m, false));
    scrollBody();
  }

  if (activeThreadId) {
    showChatPanel(true);
    subscribeThreadChannel(activeThreadId);
  } else {
    showChatPanel(false);
  }

  initPusher();
  refreshUnreadTotal();
})();
