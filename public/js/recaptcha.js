// public/js/recaptcha.js
document.addEventListener('DOMContentLoaded', function() {
  // Create a hidden input to store the recaptcha response.

  const registerForm = document.querySelector("#registrationForm");
  if (registerForm != null){
      const recaptchaInput = document.createElement("input");
      recaptchaInput.type = "hidden";
      recaptchaInput.name = "g-recaptcha-response";
      recaptchaInput.id = "g-recaptcha-response";
      registerForm.appendChild(recaptchaInput);
  }
  const loginForm = document.querySelector("#loginForm");
  if (loginForm != null){
      const recaptchaInput = document.createElement("input");
      recaptchaInput.type = "hidden";
      recaptchaInput.name = "g-recaptcha-response";
      recaptchaInput.id = "g-recaptcha-response";
      loginForm.appendChild(recaptchaInput);
  }

  window.handleRecaptcha = function(token) {
      document.getElementById("g-recaptcha-response").value = token;
  }
});