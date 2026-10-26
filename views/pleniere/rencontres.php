<?php use App\Services\Signs; $qq = $questions[$q]; ?>
<div class="pl-wrap">
  <div class="pl-head">
    <span style="background:#fff;border-radius:50%;padding:10px;display:inline-flex"><?= Signs::svg($driver, 70) ?></span>
    <div><span class="kicker">La conduite accompagnée · <?= e($drivers[$driver]['nom']) ?></span><h1><?= $qq['icone'] ?> <?= e($qq['titre']) ?></h1></div>
    <div class="counter"><b><?= (int) $count ?></b>rencontres</div>
  </div>
  <p style="font-size:20px;opacity:.85"><?= fr('Ce que disent les « ' . e($drivers[$driver]['nom']) . ' » de la salle (réponses anonymes, tirées au hasard).') ?></p>
  <?php if (!$quotes): ?><p style="font-size:22px">Pas encore de réponses pour ce driver.</p><?php endif; ?>
  <div class="proj-quotes">
    <?php foreach ($quotes as $quote): ?><div>« <?= e($quote) ?> »</div><?php endforeach; ?>
  </div>
</div>
