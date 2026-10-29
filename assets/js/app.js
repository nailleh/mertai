(() => {
  const $ = s => document.querySelector(s);
  const log = $('#log'); if (!log) return;
  const spacer = document.createElement('div'); spacer.style.height = '100px'; log.appendChild(spacer);
  const form = $('#form'), input = $('#input'), send = $('#send'), empty = $('#empty'), list = $('#list'), menu = $('#menu');
  const KEY = 'mert.chats', S = n => `<svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">${n}</svg>`;
  const IC = { cp: S('<rect x="9" y="9" width="11" height="11" rx="2"/><path d="M5 15V6a2 2 0 0 1 2-2h8"/>'), up: S('<path d="M7 11v9H4v-9zM7 11l4-7c1.5 0 2.5 1 2 3l-.5 3H19a2 2 0 0 1 2 2.3l-1 6A2 2 0 0 1 18 20H7"/>'),
    dn: S('<path d="M7 13V4H4v9zM7 13l4 7c1.5 0 2.5-1 2-3l-.5-3H19a2 2 0 0 0 2-2.3l-1-6A2 2 0 0 0 18 4H7"/>'), rg: S('<path d="M20 11a8 8 0 0 0-14-4L4 9M4 4v5h5M4 13a8 8 0 0 0 14 4l2-2M20 20v-5h-5"/>') };
  let chats = []; try { chats = JSON.parse(localStorage.getItem(KEY)) || []; } catch {}
  let cur = null, busy = false;
  const save = () => { try { localStorage.setItem(KEY, JSON.stringify(chats)); } catch {} };
  const esc = s => s.replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));

  function md(t) {
    const bl = [];
    t = esc(t).replace(/```(\w*)\n?([\s\S]*?)(```|$)/g, (_, l, c) => (bl.push(`<pre><button class="cp" type="button">Kopyala</button><code>${c.replace(/\n$/, '')}</code></pre>`), `\u0000${bl.length - 1}\u0000`));
    t = t.replace(/`([^`\n]+)`/g, '<code>$1</code>').replace(/\*\*([^*\n]+)\*\*/g, '<strong>$1</strong>');
    t = t.split(/\n{2,}/).map(p => {
      if (/^\s*([-*]|\d+\.)\s/.test(p)) { const o = /^\s*\d+\./.test(p) ? 'ol' : 'ul'; return `<${o}>` + p.split('\n').map(l => `<li>${l.replace(/^\s*([-*]|\d+\.)\s/, '')}</li>`).join('') + `</${o}>`; }
      return /^\u0000\d+\u0000$/.test(p.trim()) ? p : `<p>${p.replace(/\n/g, '<br>')}</p>`;
    }).join('');
    return t.replace(/\u0000(\d+)\u0000/g, (_, i) => bl[i]);
  }

  const down = () => { log.scrollTop = log.scrollHeight; };
  function add(role, text, loading) {
    const m = document.createElement('div'); m.className = 'msg ' + role;
    const b = document.createElement('div'); b.className = 'b';
    b.innerHTML = loading ? '<span class="dots"><i></i><i></i><i></i></span>' : role === 'ai' ? md(text) : esc(text).replace(/\n/g, '<br>');
    m.appendChild(b);
    log.insertBefore(m, spacer);
    if (role === 'ai') b.insertAdjacentHTML('beforeend', `<div class="acts"><button data-a="cp" aria-label="Kopyala">${IC.cp}</button><button data-a="up" aria-label="Beğen">${IC.up}</button><button data-a="dn" aria-label="Beğenme">${IC.dn}</button><button data-a="rg" aria-label="Yeniden üret">${IC.rg}</button></div>`);
    down(); return b;
  }

  function renderList() {
    const q = $('#q').value.toLowerCase();
    list.innerHTML = '';
    chats.filter(c => c.title.toLowerCase().includes(q)).sort((a, b) => (b.pin | 0) - (a.pin | 0)).forEach(c => {
      const b = document.createElement('button'); b.className = 'item' + (c === cur ? ' on' : '');
      b.textContent = (c.pin ? '📌 ' : '') + c.title; b.onclick = () => open(c); list.appendChild(b);
    });
  }
  function reset() { log.querySelectorAll('.msg').forEach(m => m.remove()); }
  function open(c) {
    cur = c; reset(); empty.hidden = true;
    c.msgs.forEach(m => add(m.r, m.t)); renderList(); if (innerWidth < 800) document.body.classList.add('closed'); down();
  }
  function fresh() { cur = null; reset(); empty.hidden = false; renderList(); input.focus(); if (innerWidth < 800) document.body.classList.add('closed'); }

  async function typeOut(b, full) {
    const step = Math.max(3, Math.ceil(full.length / 90));
    for (let i = step; i < full.length; i += step) { b.innerHTML = md(full.slice(0, i)); down(); await new Promise(r => setTimeout(r, 14)); }
    b.innerHTML = md(full); down();
  }

  async function ask(text, regen) {
    if (!cur) { cur = { id: crypto.randomUUID(), title: text.slice(0, 36), msgs: [], pin: false, started: false }; chats.unshift(cur); }
    busy = true; empty.hidden = true; sync();
    if (!regen) { cur.msgs.push({ r: 'user', t: text }); add('user', text); }
    const b = add('ai', '', true); let acc = '';
    try {
      const history = cur.msgs.slice(0, -1).map(m => ({ role: m.r === 'user' ? 'user' : 'assistant', content: m.t }));
      const r = await fetch('api/chat.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ message: text, history: history }) });
      if (!r.ok || !r.body) throw 0;
      const rd = r.body.getReader(), dec = new TextDecoder(); let buf = '';
      for (;;) {
        const { done, value } = await rd.read(); if (done) break;
        buf += dec.decode(value, { stream: true });
        let idx;
        while ((idx = buf.indexOf('\n\n')) >= 0) {
          const chunk = buf.slice(0, idx);
          buf = buf.slice(idx + 2);
          const k = chunk.indexOf('data:');
          if (k < 0) continue;
          const trimmed = chunk.slice(k);
          try {
            const d = JSON.parse(trimmed.slice(5));
            if (d.e) throw 0;
            if (d.t) acc += d.t;
          } catch (e) { console.error('Parse error:', e, 'Chunk:', chunk); }
        }
      }
      if (!acc) throw 0;
      await typeOut(b, acc);
      cur.started = true; cur.msgs.push({ r: 'ai', t: acc });
      b.parentNode.remove(); add('ai', acc);
    } catch (e) { console.error('API Error:', e); b.innerHTML = (acc ? md(acc) : '') + '<p class="err">Bağlantı hatası: ' + (e.message || 'API yanıt vermedi') + '</p>'; }
    busy = false; save(); renderList(); sync();
  }

  const sync = () => send.disabled = busy || !input.value.trim();
  const fit = () => { input.style.height = 'auto'; input.style.height = Math.min(input.scrollHeight, 160) + 'px'; };
  const flash = (el, t) => { const o = el.innerHTML; el.textContent = t; setTimeout(() => el.innerHTML = o, 1400); };

  input.addEventListener('input', sync);
  input.addEventListener('keydown', e => { if (e.key === 'Enter' && !e.shiftKey && !e.isComposing) { e.preventDefault(); form.requestSubmit(); } });
  form.addEventListener('submit', e => { e.preventDefault(); const t = input.value.trim(); if (!t || busy) return; input.value = ''; ask(t); });
  $('#new').onclick = () => !busy && fresh();
  $('#q').oninput = renderList;
  document.querySelectorAll('[data-toggle]').forEach(b => b.onclick = () => document.body.classList.toggle('closed'));
  if (innerWidth < 800) document.body.classList.add('closed');

  if ($('#more')) $('#more').onclick = e => { e.stopPropagation(); menu.hidden = !menu.hidden; $('#pin span').textContent = cur && cur.pin ? 'Sabitlemeyi kaldır' : 'Sohbeti sabitle'; };
  if (menu) document.addEventListener('click', () => menu.hidden = true);
  if ($('#pin')) $('#pin').onclick = () => { if (cur) { cur.pin = !cur.pin; save(); renderList(); } };
  if ($('#del')) $('#del').onclick = () => { if (cur && !busy) { chats = chats.filter(c => c !== cur); save(); fresh(); } };

  log.addEventListener('click', e => {
    const c = e.target.closest('.cp');
    if (c) return navigator.clipboard.writeText(c.nextElementSibling.textContent).then(() => { c.textContent = 'Kopyalandı'; setTimeout(() => c.textContent = 'Kopyala', 1400); });
    const a = e.target.closest('[data-a]'); if (!a) return;
    const k = a.dataset.a, msg = a.closest('.msg');
    if (k === 'cp') navigator.clipboard.writeText(msg.querySelector('.b').innerText);
    else if (k === 'rg' && !busy && cur && msg === log.lastElementChild) {
      const last = [...cur.msgs].reverse().find(m => m.r === 'user'); if (!last) return;
      cur.msgs.pop(); msg.remove(); ask(last.t, true);
    } else { a.classList.toggle('on'); }
  });

  renderList();
})();
