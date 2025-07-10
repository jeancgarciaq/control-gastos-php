document.addEventListener('DOMContentLoaded', function() {
  console.log('app.js loaded');
  const script = document.querySelector('script[src*="api.js"]');
  const siteKey = script?.src.split('render=')[1] || '';

  /**
   * Attaches AJAX submit with reCAPTCHA v3, manual redirect handling and error rendering.
   *
   * @param {string} formSelector       - Selector del formulario a “ajaxificar”.
   * @param {string} successRedirect    - Ruta a la que redirigir en caso de éxito.
   * @param {string} actionName         - Nombre de la acción para grecaptcha.execute().
   * @returns {void}
   */
  function ajaxifyForm(formSelector, successRedirect, actionName) {
    const form = document.querySelector(formSelector);
    if (!form) {
      console.warn('Form no encontrado:', formSelector);
      return;
    }

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      if (typeof grecaptcha !== 'undefined' && siteKey) {
        grecaptcha.ready(() => {
          grecaptcha
            .execute(siteKey, { action: actionName })
            .then((token) => {
              form.querySelector('input[name="g-recaptcha-response"]').value = token;
              const formData = new FormData(form);
              fetch(form.action, {
                method: form.method,
                body: formData,
                credentials: 'same-origin',
                headers: {
                  'Accept': 'application/json',
                  'X-Requested-With': 'XMLHttpRequest'
                },
                redirect: 'manual'
              })
                .then((res) => {
                  if (res.status === 302) {
                    window.location.href = res.headers.get('Location');
                    return;
                  }
                  const ct = res.headers.get('Content-Type') || '';
                  if (ct.includes('application/json')) {
                    return res.json().then((data) => {
                      if (data.success) {
                        window.location.href = successRedirect;
                      } else {
                        displayFormErrors(formSelector, data.errors || {});
                      }
                    });
                  }
                  if (ct.includes('text/html')) {
                    return res.text().then((text) => {
                      const doc = new DOMParser().parseFromString(text, 'text/html');
                      const newForm = doc.querySelector(formSelector);
                      if (newForm) {
                        form.replaceWith(newForm);
                      } else {
                        const loc = res.headers.get('Location');
                        if (loc) window.location.href = loc;
                      }
                    });
                  }
                  console.warn('Unexpected response type:', ct);
                })
                .catch((err) => {
                  console.error('Fetch failed:', err);
                  form.submit();
                });
            });
        });
      } else {
        form.submit();
      }
    });
  }

  /**
   * Inserta mensajes de error bajo los campos del formulario.
   *
   * @param {string} formSelector             - Selector del formulario.
   * @param {Object.<string, string[]>} errors - Mapa campo → array de mensajes.
   * @returns {void}
   */
  function displayFormErrors(formSelector, errors) {
    const form = document.querySelector(formSelector);
    if (!form) return;
    form.querySelectorAll('.js-error').forEach(el => el.remove());

    Object.entries(errors).forEach(([field, msgs]) => {
      const input = form.querySelector(`[name="${field}"]`);
      if (!input) return;
      msgs.forEach(msg => {
        const div = document.createElement('div');
        div.className = 'js-error text-red-600 text-sm mt-1';
        div.textContent = msg;
        input.insertAdjacentElement('afterend', div);
      });
    });
  }

  // Inicializar AJAX en el formulario de registro
  ajaxifyForm('#registrationForm', '/login', 'register');
  ajaxifyForm('#loginForm', '/dashboard', 'login');
});