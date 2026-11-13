<?php
use App\Services\Signs;
$limits = content('cas');
$maxR = (int) $limits['max_redaction'];
$max = $maxR + (int) $limits['bonus_driver'];
$pct = fn ($v) => $v === null ? '–' : round(100 * (float) $v) . ' %';
include ROOT . '/views/admin/_nav.php';
?>
<h1>L’épreuve de conduite</h1>

<h2>Débrief par situation</h2>
<p class="small">Statistiques anonymes. Réussite par critère : part des réponses où l’IA a validé le critère.</p>
<div class="table-wrap">
<table class="data">
  <tr><th>Situation</th><th>Driver</th><th>Réponses</th><th>Moyenne /<?= $max ?></th><th>Driver identifié</th><th>Accueil</th><th>Valorisation</th><th>Permission</th><th>Cadre</th><th>Sans piège</th></tr>
  <?php foreach ($cfg as $no => $c): $st = $stats[$no] ?? null; ?>
    <tr>
      <td><strong><?= $no ?> · <?= e($c['titre']) ?></strong></td>
      <td><?= Signs::svg($c['driver'], 22) ?> <?= e($drivers[$c['driver']]['nom']) ?></td>
      <td><?= (int) ($st['n'] ?? 0) ?></td>
      <td><?= $st ? number_format((float) $st['moyenne'], 1, ',', ' ') : '–' ?></td>
      <td><?= $st ? $pct($st['driver']) : '–' ?></td>
      <?php foreach (['accueil', 'valorise', 'permission', 'cadre', 'piege'] as $k): ?><td><?= $st ? $pct($st[$k]) : '–' ?></td><?php endforeach; ?>
    </tr>
  <?php endforeach; ?>
</table>
</div>

<h2>Les réponses</h2>
<p>
  <a class="btn small <?= $filter ? 'secondary' : '' ?>" href="<?= url('/admin/cas') ?>">Toutes</a>
  <?php foreach ($cfg as $no => $c): ?>
    <a class="btn small <?= $filter === $no ? '' : 'secondary' ?>" href="<?= url('/admin/cas?cas=' . $no) ?>"><?= $no ?> · <?= e($c['titre']) ?></a>
  <?php endforeach; ?>
  <a class="btn small corail" target="_blank" href="<?= url('/admin/projection/cas') ?>">Projeter la sélection ↗</a>
  <a class="btn small secondary" href="<?= url('/admin/export/cas.csv') ?>">Exporter (CSV)</a>
</p>
<p class="small"><?= fr('Triées par note. Cochez « Projeter » pour les réponses à montrer au débrief. La note de l’animateur (sur ' . $maxR . ', pour la réponse rédigée), si elle est saisie, remplace celle de l’IA ; le point du driver s’ajoute automatiquement.') ?></p>

<?php foreach ($subs as $s): $no = (int) $s['case_no']; $t = (int) $s['team_id']; $c = $cfg[$no] ?? ['titre' => '?', 'driver' => '']; ?>
  <div class="card" style="<?= $s['projected'] ? 'box-shadow:0 0 0 3px var(--corail)' : '' ?>">
    <div style="display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap">
      <div><span class="kicker">Situation <?= $no ?> · <?= e($c['titre']) ?></span><h3 style="margin-top:4px"><?= e($s['team_name']) ?></h3>
        <span class="small">Driver choisi : <strong><?= e($drivers[$s['driver_guess']]['nom'] ?? '–') ?></strong> <?= $s['driver_ok'] ? '✓ +1' : '✗' ?></span></div>
      <div class="right"><span class="score-big" style="font-size:32px"><?= (int) $s['final_score'] ?></span>/<?= $max ?><br>
        <span class="small"><?php
          if ($s['score_override'] !== null) echo 'note animateur ' . (int) $s['score_override'] . '/' . $maxR . ' (IA : ' . ($s['ai_score'] ?? '–') . ')';
          elseif ($s['ai_status'] === 'done' && $s['crit_accueil'] !== null) echo 'IA ' . (int) $s['ai_score'] . '/' . $maxR . ' · accueil ' . (int) $s['crit_accueil'] . ' · valorisation ' . (int) $s['crit_valorise'] . ' · permission ' . (int) $s['crit_permission'] . ' · cadre ' . (int) $s['crit_cadre'] . ' · sans piège ' . (int) $s['crit_piege'];
          elseif ($s['ai_status'] === 'done') echo 'IA ' . (int) $s['ai_score'];
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
        <input type="number" name="note" min="0" max="<?= $maxR ?>" value="<?= e((string) ($s['score_override'] ?? '')) ?>" placeholder="/ <?= $maxR ?>" style="width:80px;padding:6px">
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
