// Confirmation avant les saisies d'équipe, et protection contre le double envoi.
// La confirmation est une fenêtre intégrée à la page : les navigateurs peuvent bloquer
// window.confirm() après plusieurs fenêtres successives, ce qui annulait l'envoi sans rien afficher.
function confirmBox(message, onYes) {
  var box = document.createElement('div');
  box.className = 'confirm-overlay';
  box.innerHTML = '<div class="confirm-box" role="dialog" aria-modal="true"><p></p>' +
    '<div class="confirm-actions"><button type="button" class="btn secondary" data-no>Annuler</button>' +
    '<button type="button" class="btn" data-yes>Confirmer</button></div></div>';
  box.querySelector('p').textContent = message;
  function close() { document.removeEventListener('keydown', onKey); box.remove(); }
  function onKey(e) { if (e.key === 'Escape') close(); }
  box.addEventListener('click', function (e) {
    if (e.target === box || e.target.hasAttribute('data-no')) close();
    if (e.target.hasAttribute('data-yes')) { close(); onYes(); }
  });
  document.addEventListener('keydown', onKey);
  document.body.appendChild(box);
  box.querySelector('[data-yes]').focus();
}

document.addEventListener('submit', function (ev) {
  var form = ev.target;
  var btn = form.querySelector('[data-confirm]');
  if (btn && form.dataset.confirmed !== '1') {
    ev.preventDefault();
    confirmBox(btn.getAttribute('data-confirm'), function () {
      form.dataset.confirmed = '1';
      if (form.requestSubmit) form.requestSubmit(btn); else btn.click();
    });
    return;
  }
  if (form.dataset.sent === '1') {
    ev.preventDefault();
    return;
  }
  form.dataset.sent = '1';
  var submit = form.querySelector('button:not([type=button])');
  if (submit) {
    // Désactivé juste après l'envoi, pour ne pas retirer le bouton des données envoyées
    setTimeout(function () { submit.disabled = true; }, 0);
    if (submit.dataset.busy) {
      submit.dataset.label = submit.textContent;
      submit.textContent = submit.dataset.busy;
    }
  }
}, true);

// Retour arrière (cache du navigateur) : réactiver les formulaires restés en « envoi en cours ».
window.addEventListener('pageshow', function (ev) {
  if (!ev.persisted) return;
  document.querySelectorAll('form[data-sent="1"]').forEach(function (form) {
    delete form.dataset.sent;
    delete form.dataset.confirmed;
    var submit = form.querySelector('button:not([type=button])');
    if (submit) {
      submit.disabled = false;
      if (submit.dataset.label) submit.textContent = submit.dataset.label;
    }
  });
});
