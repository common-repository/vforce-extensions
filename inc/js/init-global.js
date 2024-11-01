if (typeof webToCaseInputId !== 'undefined') {
    if (jQuery(webToCaseInputId)) {
        jQuery(webToCaseInputId).val(associationId)
    } else {
        console.log('No form with title of Association ID found on this page')
    }
}
if (typeof reviewHeader !== 'undefined') {
    if (jQuery('.reviewHeader')) {
        jQuery('.reviewHeader').html(reviewHeader)
    }
}