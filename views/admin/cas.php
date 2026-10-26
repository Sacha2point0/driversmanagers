<?php include ROOT . '/views/admin/_nav.php'; ?>
<h1>La conduite : réponses aux cas</h1>
<p>
  <a class="btn small <?= $filter ? 'secondary' : '' ?>" href="<?= url('/admin/cas') ?>">Tous</a>
  <?php foreach ($cfg as $no => $c): ?>
    <a class="btn small <?= $filter === $no ? '' : 'secondary' ?>" href="<?= url('/admin/cas?cas=' . $no) ?>">Cas <?= $no ?> · <?= e($c['titre']) ?></a>
  <?php endforeach; ?>
  <a class="btn small corail" target="_blank" href="<?= url('/admin/projection/cas') ?>">Projeter la sélection ↗</a>
  <a class="btn small secondary" href="<?= url('/admin/export/cas.csv') ?>">Exporter (CSV)</a>
</p>
<p class="small">Triées par note. Cochez « Projeter » pour les réponses à montrer au débrief. La note de l’animateur, si elle est saisie, remplace celle de l’IA.</p>

<?php foreach ($subs as $s): $no = (int) $s['case_no']; $t = (int) $s['team_id']; ?>
  <div class="card" style="<?= $s['projected'] ? 'box-shadow:0 0 0 3px var(--corail)' : '' ?>">
    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap">
      <div><span class="kicker">Cas <?= $no ?> · <?= e($cfg[$no]['titre']) ?></span><h3 style="margin-top:4px"><?= e($s['team_name']) ?></h3></div>
      <div class="right"><span class="score-big" style="font-size:32px"><?= (int) $s['final_score'] ?></span>/10<br>
        <span class="small"><?php
          if ($s['score_override'] !== null) echo 'note animateur (IA : ' . ($s['ai_score'] ?? '–') . ')';
          elseif ($s['ai_status'] === 'done') echo 'note IA · ' . (int) $s['crit_feu_vert'] . '+' . (int) $s['crit_stop'] . '+' . (int) $s['crit_demande'] . '+' . (int) $s['crit_ton'];
          else echo '<span style="color:#c0392b;font-weight:800">' . ($s['ai_status'] === 'error' ? 'échec IA' : 'en attente') . '</span>';
        ?></span>
      </div>
    </div>
    <p style="white-space:pre-line"><?= e($s['answer']) ?></p>
    <?php if ($s['alert']): ?><div class="flash error">Alerte IA : <?= e($s['alert']) ?></div><?php endif; ?>
    <?php if ($s['feedback_ok'] || $s['feedback_more']): ?>
      <p class="small mt0">🟢 <?= e($s['feedback_ok']) ?><br>➜ <?= e($s['feedback_more']) ?></p>
    <?php endif; ?>
    <?php if ($s['ai_status'] === 'error'): ?><p class="small muted">Erreur : <?= e(mb_substr((string) $s['ai_raw'], 0, 200)) ?></p><?php endif; ?>
    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
      <form method="post" action="<?= url("/admin/cas/$t/$no/note") ?>" style="display:flex;gap:6px;align-items:center">
        <?= csrf_field() ?><input type="hidden" name="filtre" value="<?= (int) $filter ?>">
        <input type="number" name="note" min="0" max="10" value="<?= e((string) ($s['score_override'] ?? '')) ?>" placeholder="note" style="width:80px;padding:6px">
        <button class="btn small secondary">Corriger</button>
      </form>
      <?php if ($s['ai_status'] !== 'done'): ?>
        <form method="post" action="<?= url("/admin/cas/$t/$no/relancer") ?>"><?= csrf_field() ?><input type="hidden" name="filtre" value="<?= (int) $filter ?>"><button class="btn small secondary">Relancer l’IA</button></form>
      <?php endif; ?>
      <form method="post" action="<?= url("/admin/cas/$t/$no/projeter") ?>"><?= csrf_field() ?><input type="hidden" name="filtre" value="<?= (int) $filter ?>"><button class="btn small <?= $s['projected'] ? 'corail' : 'secondary' ?>"><?= $s['projected'] ? '✓ Projetée' : 'Projeter' ?></button></form>
    </div>
  </div>
<?php endforeach; ?>
<?php if (!$subs): ?><p class="muted">Aucune réponse pour l’instant.</p><?php endif; ?>
