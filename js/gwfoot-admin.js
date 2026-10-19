/* Progressive shortcode copying; manual fields remain usable without JavaScript. */
(() => {
 'use strict';
 document.querySelectorAll('.gwfoot-copy').forEach(button => {
  button.hidden = false;
  button.addEventListener('click', async () => {
   const field = document.getElementById(button.dataset.gwfootCopy);
   const status = document.getElementById('gwfoot-copy-status');
   if (!(field instanceof HTMLInputElement) || !status) return;
   try {
    if (!navigator.clipboard?.writeText) throw new Error('Clipboard unavailable');
    await navigator.clipboard.writeText(field.value);
    status.textContent = gwfootCopy.success;
   } catch (_) {
    field.focus(); field.select();
    status.textContent = gwfootCopy.manual;
   }
  });
 });
})();
