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
    if (submit.dataset.busy) submit.textContent = submit.dataset.busy;
  }
}, true);
