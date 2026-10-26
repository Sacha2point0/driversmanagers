<div class="pl-wrap">
  <div class="pl-head">
    <img src="<?= asset('img/cachet.svg') ?>" alt="">
    <div><span class="kicker">L’épreuve de conduite</span><h1>Les plus belles répliques</h1></div>
  </div>
  <?php if (!$rows): ?>
    <p style="font-size:22px;margin-top:30px">Aucune réponse sélectionnée : cochez « Projeter » dans l’espace animateur.</p>
  <?php endif; ?>
  <?php foreach ($rows as $r): ?>
    <div class="proj-answer">
      <div class="meta">Cas <?= (int) $r['case_no'] ?> · <?= e($cfg[(int) $r['case_no']]['titre']) ?> · <?= e($r['team_name']) ?> · <?= (int) $r['final_score'] ?>/10</div>
      <div style="white-space:pre-line"><?= e($r['answer']) ?></div>
      <?php if ($r['feedback_ok']): ?><p style="font-size:18px;margin-bottom:0">🟢 <?= fr(e($r['feedback_ok'])) ?></p><?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>
