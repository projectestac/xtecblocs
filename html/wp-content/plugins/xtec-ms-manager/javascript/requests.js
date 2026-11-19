
// Shows and populates the form for the request
function change_select(option) {
    for (var i = 0; i < dataResults.length; i++) {
        if (dataResults[i]['id'] == option) {
            jQuery('#form-details').css('display', 'block');
            jQuery('#request-info').text(dataResults[i]['description']);
            jQuery('#comments-text').text(dataResults[i]['comments_text']);
            jQuery('#request-id').val(dataResults[i]['id']);
            break;
        }
    }
}

// Captures onchange event in new request page
jQuery(document).ready(function () {
    jQuery('#select-request').on('change', function (event) {
        change_select(jQuery('#' + event.target.id + ' option:selected').val());
    });
});

function confirm_deletion($id) {
    if (confirm("Esteu segur/a de voler esborrar l'element amb id " + $id)) {
        jQuery('#xmm-del-link-'+$id).attr('href', jQuery('#xmm-del-link-'+$id).attr('href')+'&delete=1');
    }
}
