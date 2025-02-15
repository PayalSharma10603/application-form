
        document.getElementById('add-row').addEventListener('click', function () {
            const tableBody = document.querySelector('#seaman-records-table tbody');
            const newRow = document.querySelector('.record-row').cloneNode(true);
            newRow.querySelectorAll('input').forEach(input => input.value = '');
            tableBody.appendChild(newRow);
        });

        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-row')) {
                e.target.closest('tr').remove();
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const table = document.getElementById('sea-service-table').getElementsByTagName('tbody')[0];
            const addRowButton = document.getElementById('add-row-second');

            addRowButton.addEventListener('click', function () {
                const newRow = table.rows[0].cloneNode(true);
                newRow.querySelectorAll('input').forEach(input => input.value = '');
                table.appendChild(newRow);
            });

            table.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-row-second')) {
                    const row = e.target.closest('tr');
                    if (table.rows.length > 1) {
                        row.remove();
                    }
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function () {
            const table = document.getElementById('employers-table').querySelector('tbody');
            const addRowButton = document.getElementById('add-row-third');

            addRowButton.addEventListener('click', function () {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td><input type="text" name="company[]" placeholder="Company"></td>
                    <td><input type="text" name="person_in_charge[]" placeholder="Person in Charge"></td>
                    <td><input type="text" name="contact_details[]" placeholder="Contact Details"></td>
                    <td><button type="button" class="remove-row">Remove</button></td>
                `;
                table.appendChild(newRow);
            });

            table.addEventListener('click', function (event) {
                if (event.target.classList.contains('remove-row-third')) {
                    const row = event.target.closest('tr');
                    table.removeChild(row);
                }
            });
        });
        
        // document.addEventListener("DOMContentLoaded", function () {
        //     const form = document.querySelector("form");
        //     const inputs = form.querySelectorAll("input, select, textarea");
        
        //     // Load data from cookies
        //     inputs.forEach((input) => {
        //         const savedValue = getCookie(input.name);
        //         if (savedValue) {
        //             input.value = savedValue;
        //         }
        
        //         // Save data to cookies on input change
        //         input.addEventListener("input", () => {
        //             setCookie(input.name, input.value, 3); // Save cookie for 1 minute
        //         });
        //     });
        
        //     // Utility to set a cookie
        //     function setCookie(name, value, minutes) {
        //         const date = new Date();
        //         date.setTime(date.getTime() + minutes * 60 * 1000); // Convert minutes to milliseconds
        //         document.cookie = `${name}=${encodeURIComponent(value)}; expires=${date.toUTCString()}; path=/`;
        //     }
        
        //     // Utility to get a cookie
        //     function getCookie(name) {
        //         const nameEQ = name + "=";
        //         const cookies = document.cookie.split("; ");
        //         for (let i = 0; i < cookies.length; i++) {
        //             const cookie = cookies[i];
        //             if (cookie.startsWith(nameEQ)) {
        //                 return decodeURIComponent(cookie.substring(nameEQ.length));
        //             }
        //         }
        //         return null;
        //     }
        
        //     form.addEventListener("submit", () => {
        //         inputs.forEach((input) => {
        //             document.cookie = `${input.name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
        //         });
        //     });
        // });

        document.addEventListener("DOMContentLoaded", function () {
            const form = document.querySelector("#applicationForm"); // Use the ID to select the form
            const inputs = form.querySelectorAll("input, select, textarea");
        
            // Load data from cookies
            inputs.forEach((input) => {
                const savedValue = getCookie(input.name);
                if (savedValue) {
                    input.value = savedValue;
                }
        
                // Save data to cookies on input change
                input.addEventListener("input", () => {
                    setCookie(input.name, input.value, 3); // Save cookie for 3 minutes
                });
            });
        
            // Utility to set a cookie
            function setCookie(name, value, minutes) {
                const date = new Date();
                date.setTime(date.getTime() + minutes * 60 * 1000); // Convert minutes to milliseconds
                document.cookie = `${name}=${encodeURIComponent(value)}; expires=${date.toUTCString()}; path=/`;
            }
        
            // Utility to get a cookie
            function getCookie(name) {
                const nameEQ = name + "=";
                const cookies = document.cookie.split("; ");
                for (let i = 0; i < cookies.length; i++) {
                    const cookie = cookies[i];
                    if (cookie.startsWith(nameEQ)) {
                        return decodeURIComponent(cookie.substring(nameEQ.length));
                    }
                }
                return null;
            }
        
            form.addEventListener("submit", () => {
                inputs.forEach((input) => {
                    document.cookie = `${input.name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
                });
            });
        });
        
        
        jQuery(document).ready(function ($) {
            let isEmailValid = false;
        
            $('#email').on('input', function () {
                $('#email-validation-message').text('');
                isEmailValid = false; // Reset validation state when the user types
            });
        
            $('#email').on('blur', function () {
                const email = $(this).val();
                const validationMessage = $('#email-validation-message');
        
                if (email === '') {
                    validationMessage.text('Email cannot be empty.');
                    return;
                }
        
                // AJAX request
                $.ajax({
                    url: wpmsf_ajax.ajax_url,
                    method: 'POST',
                    data: {
                        action: 'validate_email',
                        email: email,
                    },
                    success: function (response) {
                        if (response.success) {
                            validationMessage.text(response.data).css('color', 'green');
                            isEmailValid = true;
                        } else {
                            validationMessage.text(response.data).css('color', 'red');
                            isEmailValid = false;
                        }
                    },
                    error: function () {
                        validationMessage.text('Error validating email. Please try again.').css('color', 'red');
                        isEmailValid = false;
                    },
                });
            });
        
            $('form').on('submit', function (e) {
                if (!isEmailValid) {
                    e.preventDefault(); // Prevent form submission if email is invalid
                    $('#email-validation-message').text('Please use a valid email.').css('color', 'red');
                }
            });
        });
        
        document.getElementById('applicationForm').addEventListener('submit', function (event) {
            let requiredFields = [
                'surname', 'name', 'father_name', 'mother_name', 'email', 'password',
                'maritime_college', 'education_from', 'department', 'education_till',
                'date_signed', 'name_sign', 'position_applied', 'date_of_readiness', 'date_of_birth', 'nationality', 'place_of_birth', 'home_address', 'home_zip', 'contact_phone', 
                'next_kin', 'relation', 'next_kin_address', 'next_kin_phone', 'height', 'weight', 'size_overall', 'eye_color', 'hair_color', 'shoes',
                'maritime_college', 'education_from', 'department', 'education_till'
            ];

            let isValid = true;

            // Clear previous error messages
            document.querySelectorAll('.error-message').forEach(function (element) {
                element.textContent = '';
            });

            requiredFields.forEach(function (field) {
                let input = document.querySelector('[name="' + field + '"]');
                if (input && !input.value.trim()) {
                    isValid = false;
                    document.getElementById(field + '_error').textContent = field.replace('_', ' ') + " is required.";
                }
            });

            if (!document.getElementById('confirmationCheckbox').checked) {
                isValid = false;
                document.getElementById('confirmation_checkbox_error').textContent = "You must confirm the information is true.";
            }

            if (!isValid) {
                event.preventDefault();
            }
        });