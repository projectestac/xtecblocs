let xtecDescriptorsAutocompRequest = 0;

/**
 * Shows the descriptors that start with the value entered by the user as suggestions.
 */
function xtecDescriptorsAutocomplete(value) {
    const suggestions = document.getElementById('autocompletediv');
    const request = ++xtecDescriptorsAutocompRequest;

    suggestions.style.visibility = 'visible';

    fetch(xtecDescriptorsAutocompUrl + '&sstring=' + encodeURIComponent(value), {credentials: 'same-origin'})
        .then((response) => (response.ok ? response.text() : ''))
        .then((html) => {
            // Ignore the responses of the previous requests that arrive late
            if (request === xtecDescriptorsAutocompRequest) {
                suggestions.innerHTML = html;
            }
        });
}

/**
 * Sets the suggestion chosen by the user as the descriptor and hides the suggestions.
 */
function setvalue(value) {
    document.getElementById('autocompletediv').style.visibility = 'hidden';
    document.getElementById('descriptor').value = value;
}
