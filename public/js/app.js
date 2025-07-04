// public/js/app.js

document.addEventListener('DOMContentLoaded', function() {
    // Function to handle form submission via AJAX
    function submitForm(formSelector, successCallback, errorCallback) {
        const form = document.querySelector(formSelector);

        if (!form) {
            console.error('Form not found:', formSelector);
            return;
        }

        form.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(form);

            fetch(form.action, {
                method: form.method,
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json(); // Assuming your server returns JSON
            })
            .then(data => {
                if (data.success) {
                    successCallback(data);
                } else {
                    errorCallback(data);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                // Generic error handling
                errorCallback({ general: ['An unexpected error occurred.'] });
            });
        });
    }

    // Example usage for the registration form
    submitForm('#registrationForm',
        function(data) {
            // Success: Redirect or display a success message
            window.location.href = '/login'; // Redirect to login page
        },
        function(data) {
            // Error: Display errors in the form
            displayFormErrors('#registrationForm', data.errors);
        }
    );

    // Example usage for the login form
    submitForm('#loginForm',
        function(data) {
            // Success: Redirect to dashboard
            window.location.href = '/dashboard';
        },
        function(data) {
            // Error: Display errors in the form
            displayFormErrors('#loginForm', data.errors);
        }
    );

    // Function to display form errors
    function displayFormErrors(formSelector, errors) {
        const form = document.querySelector(formSelector);
        if (!form) {
            console.error('Form not found:', formSelector);
            return;
        }

        // Clear previous errors
        const errorDivs = form.querySelectorAll('.error-message');
        errorDivs.forEach(div => div.remove());

        // Display new errors
        for (const field in errors) {
            if (errors.hasOwnProperty(field)) {
                const errorMessages = errors[field];
                const inputField = form.querySelector(`[name="${field}"]`);

                if (inputField) {
                    // Create and insert error message(s)
                    errorMessages.forEach(message => {
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'error-message text-red-500 text-sm italic mt-1';
                        errorDiv.textContent = message;
                        inputField.parentNode.insertBefore(errorDiv, inputField.nextSibling);
                    });
                } else {
                    console.warn('Input field not found:', field);
                }
            }
        }
    }

    // Function to handle reCAPTCHA token and send it as hidden field
    window.handleRecaptcha = function(token) {
      document.getElementById("g-recaptcha-response").value = token;
    }

});