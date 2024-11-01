if (jQuery('.ballot-container').val()) {
    let ballotStatus = jQuery('input[title="Ballot Status"]').val();
    let formContainer = jQuery('.wFormContainer')
    let ballotCastMessage

    (vforce_helper.ballotCastMessage) ? ballotCastMessage = vforce_helper.ballotCastMessage : ballotCastMessage = '<h2>This ballot has been closed.  Thank you for your response.</h2>'
    if (ballotStatus.toLowerCase() === 'cast') {
        jQuery('.ballot-container').removeClass('d-none')
        jQuery('.wFormContainer').html(ballotCastMessage)
    } else {
        jQuery('.ballot-container').removeClass('d-none')
    }
}
/* Old code to add directly to form assembly */
window.addEventListener('load', function () {
    var ballotStatus = document.querySelector("input[title='Ballot Status']");
    if (ballotStatus.value.toLowerCase() === 'cast') {// If ballot has been cast, hide form and show message.
        document.querySelector('form').style.display = 'none';
        document.querySelector('form').parentNode.innerHTML += '<h2>' + document.querySelector("input[title='Ballot Cast Message']").value + '</h2>';
    } else {// If ballot has not yet been cast, then hide the input containing the message as well as it's label.
        document.querySelector("input[title='Ballot Cast Message']").parentNode.parentElement.style.display = 'none'
    }
})