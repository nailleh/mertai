<?php
$logo = '<img class="logo" src="assets/img/tontişmert.jpeg" alt="" width="28" height="28" onerror="this.replaceWith(Object.assign(document.createElement(\'span\'),{className:\'logo mono\',textContent:\'M\'}))">';
$i = fn($d) => '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $d . '</svg>';
?>
<aside class="side glass" id="side">
  <div class="side-head"><?= $logo ?>
    <button class="ib" data-toggle aria-label="Kenar çubuğu"><?= $i('<rect x="3" y="4" width="18" height="16" rx="3"/><path d="M9 4v16"/>') ?></button></div>
  <nav>
    <button class="nav" id="new"><?= $i('<path d="M12 4H7a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-5"/><path d="M17.5 3.5a2.1 2.1 0 0 1 3 3L12 15l-4 1 1-4z"/>') ?>Yeni sohbet</button>
    <label class="nav search"><?= $i('<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>') ?><input id="q" placeholder="Sohbetlerde ara" autocomplete="off"></label>
    <a class="nav" href="?p=about"><?= $i('<circle cx="12" cy="12" r="9"/><path d="M12 8v.01M12 11v5"/>') ?>Hakkında</a>
  </nav>
  <div class="label">Sohbetler</div>
  <div id="list"></div>
  <a class="credit" href="?p=about">Developed by Eren Fındıkcı</a>
</aside>

<header class="top">
  <button class="ib glass" data-toggle id="open" aria-label="Kenar çubuğu"><?= $i('<rect x="3" y="4" width="18" height="16" rx="3"/><path d="M9 4v16"/>') ?></button>
  <span class="chip glass">Mert AI</span>
  <span class="grow"></span>
  <div class="rel">
    <button class="ib glass" id="more" aria-label="Diğer"><?= $i('<circle cx="5" cy="12" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="19" cy="12" r="1"/>') ?></button>
    <div class="menu glass" id="menu" hidden>
      <button id="pin"><?= $i('<path d="M9 4h6l-1 6 3 3H7l3-3zM12 13v8"/>') ?><span>Sohbeti sabitle</span></button>
      <button id="del"><?= $i('<path d="M4 7h16M10 11v6M14 11v6M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12M9 7V4h6v3"/>') ?><span>Sil</span></button>
    </div>
  </div>
</header>

<main id="log">
  <section id="empty"><?= str_replace('width="28" height="28"', 'width="64" height="64"', $logo) ?><h1>Merhaba, ben Mert AI</h1></section>
</main>

<form id="form" class="dock" autocomplete="off">
  <div class="field glass">
    <textarea id="input" rows="1" maxlength="4000" placeholder="Bir şey sor…" aria-label="Mesaj"></textarea>
    <button id="send" type="submit" aria-label="Gönder" disabled><?= $i('<path d="M12 19V5M5 12l7-7 7 7"/>') ?></button>
  </div>
  <small>Mert AI hata yapabilir. Önemli bilgileri doğrula.</small>
</form>
