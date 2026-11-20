// Shows the form of the request with the description and the text of the comments of the chosen request type
function change_select(option) {
    const requestType = dataResults.find((type) => String(type.id) === option);
    if (requestType) {
        jQuery('#form-details').css('display', 'block');
        jQuery('#request-info').text(requestType.description);
        jQuery('#comments-text').text(requestType.comments_text);
    }
}

jQuery(document).ready(function () {
    jQuery('#select-request').on('change', function () {
        change_select(jQuery(this).val());
    });
});

// Adds the parameter delete to the link of the deletion if the user confirms it
function confirm_deletion(id) {
    if (confirm(xmmRequests.confirmDeletion.replace('%s', id))) {
        const link = jQuery('#xmm-del-link-' + id);
        link.attr('href', link.attr('href') + '&delete=1');
    }
}
