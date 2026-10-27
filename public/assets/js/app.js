// Confirmation avant les saisies d'équipe, et protection contre le double envoi.
document.addEventListener('submit', function (ev) {
  var form = ev.target;
  var btn = form.querySelector('[data-confirm]');
  if (btn && !window.confirm(btn.getAttribute('data-confirm'))) {
    ev.preventDefault();
    return;
  }
  if (form.dataset.sent === '1') {
    ev.preventDefault();
    return;
  }
  form.dataset.sent = '1';
  var submit = form.querySelector('button:not([type=button])');
  if (submit) {
    submit.disabled = true;
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
    var submit = form.querySelector('button:not([type=button])');
    if (submit) {
      submit.disabled = false;
      if (submit.dataset.label) submit.textContent = submit.dataset.label;
    }
  });
});
