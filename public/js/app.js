/**
 * @file Maneja la lógica de los formularios de la aplicación, incluyendo la integración
 * con reCAPTCHA v3 y el envío de datos mediante AJAX.
 * @author Jean Carlo Garcia
 * @version 1.0
 */

document.addEventListener('DOMContentLoaded', function() {
  console.log('app.js loaded');

  /**
   * Obtiene la Site Key de reCAPTCHA v3 analizando la URL del script de Google.
   * @returns {string|null} La Site Key si se encuentra, o null si no.
   */
  function getRecaptchaSiteKey() {
    const script = document.querySelector('script[src*="recaptcha/api.js"]');
    if (script) {
      try {
        const url = new URL(script.src);
        return url.searchParams.get('render');
      } catch (e) {
        console.error("Error al analizar la URL del script de reCAPTCHA:", e);
        return null;
      }
    }
    return null;
  }

  const siteKey = getRecaptchaSiteKey();
  if (!siteKey) {
    console.error('La Site Key de reCAPTCHA v3 no fue encontrada. Asegúrate de que el script de Google se cargue correctamente en la vista.');
  }

  /**
   * Mejora un formulario HTML para que se envíe vía AJAX con validación de reCAPTCHA v3.
   * Previene el envío por defecto, obtiene un token de reCAPTCHA, y maneja la respuesta del servidor.
   * @param {string} formId - El ID del elemento del formulario a mejorar (ej. 'registrationForm').
   * @param {string} successUrl - La URL a la que se redirigirá al usuario si el envío es exitoso.
   * @param {string} recaptchaAction - El nombre de la acción para la ejecución de reCAPTCHA (ej. 'register' o 'login').
   * @returns {void} No devuelve ningún valor.
   */
  function ajaxifyForm(formId, successUrl, recaptchaAction) {
    const form = document.getElementById(formId);
    if (!form) {
      // No es un error, simplemente el formulario no está en esta página.
      return;
    }

    form.addEventListener('submit', function(e) {
      e.preventDefault();

      if (typeof grecaptcha === 'undefined' || !siteKey) {
        console.error('grecaptcha no está listo o falta la Site Key. Abortando envío.');
        displayFormErrors({ general: ['Error de configuración de seguridad. No se puede enviar el formulario.'] });
        return;
      }

      grecaptcha.ready(() => {
        grecaptcha.execute(siteKey, { action: recaptchaAction }).then((token) => {
          const recaptchaInput = document.getElementById('g-recaptcha-response');
          if (recaptchaInput) {
            recaptchaInput.value = token;
          }

          const formData = new FormData(form);

          fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
              'Accept': 'application/json',
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              if (data.redirect) {
                window.location.href = data.redirect;
              } else {
                window.location.href = successUrl;
              }
            } else {
              displayFormErrors(data.errors || { general: ['Ha ocurrido un error inesperado.'] });
            }
          })
          .catch(error => {
            console.error('Error en la solicitud Fetch:', error);
            displayFormErrors({ general: ['Ocurrió un error de red. Por favor, inténtalo de nuevo.'] });
          });
        });
      });
    });
  }

  /**
   * Muestra los errores de validación del formulario en la interfaz de usuario.
   * Limpia los errores anteriores y renderiza los nuevos dentro de un contenedor de alerta.
   * @param {Object.<string, string[]>} errors - Un objeto donde las claves son los nombres de los campos
   * y los valores son arrays de mensajes de error. Un campo 'general' puede usarse para errores no asociados a un input.
   * @returns {void}
   */
  function displayFormErrors(errors) {
    const errorContainer = document.getElementById('error-container');
    if (!errorContainer) {
        console.error("El contenedor de errores con ID 'error-container' no fue encontrado en el DOM.");
        return;
    }

    // Limpia errores anteriores
    errorContainer.innerHTML = '';
    document.querySelectorAll('.js-error').forEach(el => el.remove());

    const errorList = document.createElement('ul');
    errorList.className = 'list-disc list-inside mt-2 text-red-700';
    
    // Aplica estilos de alerta de Tailwind
    errorContainer.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4';
    errorContainer.setAttribute('role', 'alert');
    errorContainer.innerHTML = '<strong class="font-bold">¡Error!</strong><span class="block sm:inline"> Por favor, corrige los siguientes errores:</span>';
    
    Object.entries(errors).forEach(([field, messages]) => {
      messages.forEach(msg => {
        const li = document.createElement('li');
        li.textContent = msg;
        errorList.appendChild(li);

        // Opcional: Muestra el error también debajo del campo específico
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'js-error text-red-600 text-sm mt-1';
            errorDiv.textContent = msg;
            // Inserta el error después del input
            input.parentElement.appendChild(errorDiv);
        }
      });
    });

    if (errorList.hasChildNodes()) {
        errorContainer.appendChild(errorList);
    }
  }

  // --- INICIALIZACIÓN ---
  // Se asigna la lógica AJAX a los formularios que puedan existir en la página.
  ajaxifyForm('registrationForm', '/login', 'register');
  ajaxifyForm('loginForm', '/dashboard', 'login');
});