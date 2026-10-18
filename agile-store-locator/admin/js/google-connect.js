(function () {
  'use strict';
  const root = document.getElementById('asl-google-connect');
  if (!root || typeof aslGoogleConnect === 'undefined') return;
  const manual = document.getElementById('asl-connect-manual');
  const manualPanel = document.getElementById('asl-manual-key-panel');
  if (manual && manualPanel) {
    manualPanel.hidden = true;
    manual.addEventListener('click', () => {
      manualPanel.hidden = !manualPanel.hidden;
      manual.setAttribute('aria-expanded', String(!manualPanel.hidden));
      if (!manualPanel.hidden) document.getElementById('asl-dashboard-maps-key').focus();
    });
  }
  const config = aslGoogleConnect;
  const start = document.getElementById('asl-google-connect-start');
  const check = document.getElementById('asl-google-connect-check');
  const launch = document.getElementById('asl-google-connect-launch');
  const status = document.getElementById('asl-google-connect-status');
  const reload = document.getElementById('asl-google-connect-reload');
  const verify = document.getElementById('asl-maps-verify');
  const providers = document.querySelectorAll('input[name="asl-dashboard-map-provider"]');
  let busy = false;
  let saved = false;
  check.hidden = !config.pending;

  function feedback(message, state) {
    status.textContent = message;
    status.className = 'asl-maps-feedback is-' + state;
  }
  // Reloading after connection must show the fresh key, not the previous input value.
  function reopenAfterConnection() {
    if (new URLSearchParams(window.location.search).get('asl-google-connected') !== '1') return;
    const google = document.querySelector('input[name="asl-dashboard-map-provider"][value="google"]');
    const opener = document.querySelector('[data-bs-toggle="sl_offcanvas"][href="#asl-map-setup"]');
    if (google) { google.checked = true; google.dispatchEvent(new Event('change', {bubbles: true})); }
    if (opener) opener.click();
    const url = new URL(window.location.href);
    url.searchParams.delete('asl-google-connected');
    window.history.replaceState(null, '', url.toString());
    if (verify) verify.focus();
  }
  if (document.readyState === 'complete') reopenAfterConnection();
  else window.addEventListener('load', reopenAfterConnection, {once: true});

  async function request(action, extra) {
    const body = new URLSearchParams(Object.assign({action, nonce: config.nonce}, extra));
    const response = await fetch(config.url, {method: 'POST', credentials: 'same-origin', body});
    let data;
    try { data = await response.json(); } catch (_) { throw new Error(config.text.failed); }
    if (!response.ok || !data.success) throw new Error(data.data && data.data.message || config.text.failed);
    return data.data;
  }
  async function run(fn) {
    if (busy || saved || (verify && verify.disabled)) return;
    busy = true; start.disabled = true; check.disabled = true;
    if (verify) verify.disabled = true;
    providers.forEach(field => { field.disabled = true; });
    root.setAttribute('aria-busy', 'true');
    try { await fn(); } catch (e) { start.hidden = false; feedback(e.message, 'error'); }
    finally {
      busy = false; start.disabled = saved; check.disabled = saved;
      if (verify) verify.disabled = saved;
      providers.forEach(field => { field.disabled = saved; });
      root.setAttribute('aria-busy', 'false');
    }
  }
  start.addEventListener('click', () => run(async () => {
    feedback(config.text.starting, 'loading');
    launch.hidden = true;
    const result = await request('asl_google_connect_start', {subdomains: document.getElementById('asl-google-connect-subdomains').checked ? '1' : '0'});
    launch.href = result.url; launch.textContent = config.text.continue; launch.hidden = false;
    start.hidden = true; check.hidden = false;
    feedback(config.text.waiting, 'loading');
    launch.focus();
  }));
  check.addEventListener('click', () => run(async () => {
    feedback(config.text.checking, 'loading');
    const result = await request('asl_google_connect_check');
    if (result.status === 'saved') {
      saved = true;
      feedback(config.text.saved, 'success');
      reload.hidden = false; check.hidden = true; launch.hidden = true; start.hidden = true;
      const keyField = document.getElementById('asl-dashboard-maps-key');
      if (keyField) keyField.disabled = true;
      reload.focus();
    } else feedback(result.message || config.text.waiting, 'loading');
  }));
})();
