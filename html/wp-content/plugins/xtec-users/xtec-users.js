/**
 * Page to add users: checks the username through AJAX while it is typed and shows the result, which contains the
 * fields to add the user. It also shows the strength of the password of new users and can generate another one.
 */
(function () {
    'use strict';

    const form = document.getElementById('xtec-users-form');
    if (!form) {
        return;
    }

    const username = document.getElementById('xtec-users-username');
    const result = document.getElementById('xtec-users-result');
    let timer = null;
    let lastUsername = username.value.trim().toLowerCase();
    let sequence = 0;

    // The check is done automatically, so the button is only needed without JavaScript. It is removed so that pressing
    // Enter in the other fields uses the button of the result.
    form.querySelector('[name="xtec_users_check"]').remove();

    function check() {
        const value = username.value.trim().toLowerCase();
        if (value === lastUsername) {
            return;
        }
        lastUsername = value;

        // Only the response of the last request is shown
        const current = ++sequence;

        if (value === '') {
            result.innerHTML = '';
            return;
        }

        const body = new FormData();
        body.append('action', 'xtec_users_check');
        body.append('_wpnonce', form.elements._wpnonce.value);
        body.append('username', value);

        fetch(window.ajaxurl, {method: 'POST', body: body, credentials: 'same-origin'})
            .then(function (response) {
                return response.json();
            })
            .then(function (json) {
                if (current === sequence && json.success) {
                    result.innerHTML = json.data.html;
                    initPassword();
                }
            })
            .catch(function () {
                if (current === sequence) {
                    result.innerHTML = '<div class="notice notice-error inline"><p>No s\'ha pogut comprovar el nom ' +
                        'd\'usuari. Torneu-ho a provar.</p></div>';
                    lastUsername = null;
                }
            });
    }

    function generatePassword() {
        // Without characters that can be confused, as the password is given to the user by hand
        const chars = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        const values = new Uint32Array(12);
        window.crypto.getRandomValues(values);

        return Array.from(values, function (n) {
            return chars[n % chars.length];
        }).join('');
    }

    function initPassword() {
        const password = document.getElementById('xtec-users-password');
        if (!password) {
            return;
        }

        const strength = document.getElementById('xtec-users-strength');

        // Same levels as the profile of the user in WordPress
        function update() {
            if (!window.wp || !wp.passwordStrength || !window.pwsL10n) {
                return;
            }
            const score = wp.passwordStrength.meter(password.value, wp.passwordStrength.userInputDisallowedList(), password.value);
            const level = {2: 'bad', 3: 'good', 4: 'strong'}[score] || (score < 0 ? '' : 'short');
            strength.className = level;
            strength.textContent = level ? pwsL10n[level] : '';
        }

        password.addEventListener('input', update);
        document.getElementById('xtec-users-generate').addEventListener('click', function () {
            password.value = generatePassword();
            update();
        });

        // The library that measures the strength (zxcvbn) is loaded asynchronously after the page, so the strength is
        // shown when it is available
        let tries = 0;
        (function waitForLibrary() {
            if (typeof window.zxcvbn !== 'undefined') {
                update();
            } else if (++tries < 50) {
                setTimeout(waitForLibrary, 200);
            }
        }());
    }

    username.addEventListener('input', function () {
        clearTimeout(timer);
        timer = setTimeout(check, 400);
    });

    username.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            clearTimeout(timer);
            check();
        }
    });

    initPassword();
}());
