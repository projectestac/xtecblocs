/**
 * Form of the widget: chooses the image in the media library or removes it. The form is in the page of the widgets,
 * where it can be added at any moment, and in the customizer, so the events are listened in the whole document.
 */
(function () {
    'use strict';

    function setImage(box, url) {
        const input = box.querySelector('input');
        input.value = url;
        box.querySelector('img').src = url;
        box.querySelector('img').hidden = url === '';
        box.querySelector('.xtec-wdu-remove').hidden = url === '';
        // The page of the widgets and the customizer save the widget when its fields change
        input.dispatchEvent(new Event('change', {bubbles: true}));
    }

    document.addEventListener('click', function (event) {
        const remove = event.target.closest('.xtec-wdu-remove');
        if (remove) {
            event.preventDefault();
            setImage(remove.closest('.xtec-wdu-image'), '');
            return;
        }

        const select = event.target.closest('.xtec-wdu-select');
        if (!select) {
            return;
        }
        event.preventDefault();

        const box = select.closest('.xtec-wdu-image');
        const frame = wp.media({
            title: xtecWidgetDataUsers.title,
            button: {text: xtecWidgetDataUsers.button},
            library: {type: 'image'},
            multiple: false
        });
        frame.on('select', function () {
            setImage(box, frame.state().get('selection').first().toJSON().url);
        });
        frame.open();
    });
}());
