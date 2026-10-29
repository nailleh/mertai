<?php $i = fn($d) => '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">' . $d . '</svg>'; ?>
<main class="about">
  <a class="pill glass back" href="./"><?= $i('<path d="m15 5-7 7 7 7"/>') ?><span>Sohbete dön</span></a>
  <section class="glass hero-card">
    <img class="logo big" src="assets/img/tontişmert.jpeg" alt="" width="84" height="84" onerror="this.replaceWith(Object.assign(document.createElement('span'),{className:'logo big mono',textContent:'M'}))">
    <h1>Mert AI</h1>
    <p>Hızlı, sade ve cam efektli bir yapay zeka sohbet deneyimi. Sorularını yaz, cevaplar anında akarak gelsin.</p>
  </section>
  <section class="grid">
    <div class="glass card"><?= $i('<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>') ?><h3>Canlı akış</h3><p>Cevaplar yazılırken ekrana kelime kelime akar, beklemezsin.</p></div>
    <div class="glass card"><?= $i('<path d="M4 5h16v11H9l-5 4z"/>') ?><h3>Sohbet geçmişi</h3><p>Sohbetlerin tarayıcında saklanır; arayabilir, sabitleyebilir ve silebilirsin.</p></div>
    <div class="glass card"><?= $i('<path d="m8 8-4 4 4 4M16 8l4 4-4 4M13.5 5l-3 14"/>') ?><h3>Kod desteği</h3><p>Kod blokları düzgün biçimlenir ve tek tıkla kopyalanır.</p></div>
    <div class="glass card"><?= $i('<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/>') ?><h3>Gizli sohbet</h3><p>Sohbetler özel olarak açılır, geçmişin yalnızca senin cihazında durur.</p></div>
  </section>
  <section class="glass dev">
    <span class="label">Geliştirici</span>
    <h2>Eren Fındıkcı</h2>
    <p>Mert AI, Eren Fındıkcı tarafından geliştirildi.</p>
  </section>
  <footer>© <?= date('Y') ?> Mert AI · Developed by Eren Fındıkcı</footer>
</main>
