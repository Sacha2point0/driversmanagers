<?php include ROOT . '/views/admin/_nav.php'; ?>
<h1>Données et confidentialité</h1>

<div class="card">
  <h2 class="mt0">Exports</h2>
  <p>
    <a class="btn small secondary" href="<?= url('/admin/export/participants.csv') ?>">Participants</a>
    <a class="btn small secondary" href="<?= url('/admin/export/scores-questionnaire.csv') ?>">Scores du questionnaire</a>
    <a class="btn small secondary" href="<?= url('/admin/export/classement.csv') ?>">Classement</a>
    <a class="btn small secondary" href="<?= url('/admin/export/cas.csv') ?>">Réponses aux cas</a>
    <a class="btn small secondary" href="<?= url('/admin/export/rencontres-anonymes.csv') ?>">Rencontres (anonymisées)</a>
  </p>
  <p class="help">Les résultats individuels du questionnaire et les rencontres nominatives ne doivent pas être transmis au client : seules les statistiques anonymes peuvent l’être.</p>
</div>

<div class="card" style="box-shadow:0 0 0 2px var(--corail)">
  <h2 class="mt0">Supprimer les données</h2>
  <form method="post" action="<?= url('/admin/purge') ?>">
    <?= csrf_field() ?>
    <label class="switch-row"><span>Épreuves seulement (code, rencontres, manœuvres, cas). Participants et questionnaires conservés.</span><input type="radio" name="scope" value="epreuves" checked></label>
    <label class="switch-row"><span>Tout : participants, équipes, questionnaires et épreuves. À faire après le séminaire.</span><input type="radio" name="scope" value="tout"></label>
    <div class="field"><label for="confirm">Tapez SUPPRIMER pour confirmer</label><input id="confirm" name="confirm" type="text" autocomplete="off"></div>
    <button class="btn corail" data-confirm="Cette suppression est définitive. Continuer ?">Supprimer définitivement</button>
  </form>
</div>
