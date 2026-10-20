/**
 * Page to manage the plugins: activates or deactivates a plugin in all the blogs. The blogs are processed in batches,
 * with a request for each one, so that a single request doesn't have to process all of them.
 */
(function () {
    'use strict';

    const l10n = window.xtecPluginManager;
    const result = document.getElementById('xtec-plugin-manager-result');
    const buttons = document.querySelectorAll('.xtec-plugin-manager-mass');

    function format(text, values) {
        let i = 0;

        return text.replace(/%(\d\$)?[sd]/g, function (match, position) {
            return String(values[position ? parseInt(position, 10) - 1 : i++]);
        });
    }

    function notice(type, text) {
        const div = document.createElement('div');
        const p = document.createElement('p');
        div.className = 'notice notice-' + type + ' inline';
        p.textContent = text;
        div.appendChild(p);
        result.replaceChildren(div);
    }

    async function batch(plugin, mode, offset) {
        const response = await fetch(l10n.ajaxUrl, {
            method: 'POST',
            credentials: 'same-origin',
            body: new URLSearchParams({
                action: 'xtec_plugin_manager_mass',
                _ajax_nonce: l10n.nonce,
                plugin: plugin,
                mode: mode,
                offset: String(offset)
            })
        });
        const json = await response.json();
        if (!response.ok || !json.success) {
            throw new Error('xtec-plugin-manager');
        }

        return json.data;
    }

    async function run(button) {
        const plugin = button.dataset.plugin;
        const name = button.dataset.name;
        const mode = button.dataset.mode;
        const confirmText = mode === 'activate' ? l10n.confirmActivate : l10n.confirmDeactivate;
        if (!window.confirm(format(confirmText, [name]))) {
            return;
        }

        buttons.forEach(function (b) {
            b.disabled = true;
        });

        let offset = 0;
        let failed = 0;
        let total = 0;
        try {
            do {
                const data = await batch(plugin, mode, offset);
                offset = data.next;
                total = data.total;
                failed += data.failed;
                notice('info', format(l10n.progress, [offset, total]));
            } while (offset < total);

            let text = format(mode === 'activate' ? l10n.activated : l10n.deactivated, [name, total - failed]);
            if (failed > 0) {
                text += ' ' + format(l10n.failed, [failed]);
            }
            notice(failed > 0 ? 'warning' : 'success', text);
        } catch (e) {
            notice('error', l10n.error);
        }

        buttons.forEach(function (b) {
            b.disabled = false;
        });
    }

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            run(button);
        });
    });
}());
