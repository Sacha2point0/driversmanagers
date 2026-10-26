<div class="pl-wrap">
  <div class="pl-head">
    <img src="<?= asset('img/cachet.svg') ?>" alt="">
    <div><span class="kicker">Le permis de manager</span><h1>Classement des équipes</h1></div>
    <div class="counter"><b id="enc">0</b>rencontres</div>
  </div>
  <div class="board" id="board"></div>
</div>
<script>
(function () {
  var api = <?= json_encode(url('/api/pleniere')) ?>, every = <?= (int) $refresh ?> * 1000;
  function esc(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
  function load() {
    fetch(api, { cache: 'no-store' }).then(function (r) { return r.json(); }).then(function (d) {
      document.getElementById('enc').textContent = d.encounters;
      var half = Math.ceil(d.teams.length / 2), rows = [];
      // Remplissage en colonnes : 1 à N/2 à gauche, la suite à droite
      for (var i = 0; i < half; i++) {
        [i, i + half].forEach(function (k) {
          var t = d.teams[k];
          rows.push(t ? '<div class="row podium-' + (k + 1) + '"><span class="rk">' + (k + 1) + '</span><span>' + esc(t.name) + '</span><span class="pts">' + t.total + '</span></div>' : '<div></div>');
        });
      }
      document.getElementById('board').innerHTML = rows.join('');
    }).catch(function () {});
  }
  load(); setInterval(load, every);
})();
</script>
