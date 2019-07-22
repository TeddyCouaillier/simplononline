$(function () {
    $('[data-toggle="popover"]').popover()
})

$('.popover-dismiss').popover({
    trigger: 'focus'
})

/* ---------------- MODAL -------------------- */
// When the user clicks the button, open the modal
if($(".modal-button") != null){
    $(".modal-button").on('click', function() {
        var modalid = $(this).data('modalid');
        $(`.modal[data-modalid='${modalid}'`).fadeIn();
    });
}

// When the user clicks on <span> (x), close the modal
if($('.close-modal') != null){
    $('.close-modal').click(function() {
      $(".modal").fadeOut(250);
    });
}

// When the user clicks anywhere outside of the modal, close it
$(window).click(function(event) {
    if ($(event.target).attr('id') == $('.modal').attr('id')) {
        $(".modal").fadeOut(250);
    }

    $('.popover-body .modal-button').on('click', function() {
        var id = $(this).parent().parent().attr('id');
        var popoverParent = $(`i[aria-describedby='${id}']`)
        var modalid = $(popoverParent).data('modalid');
        $(`.modal[data-modalid='${modalid}'`).fadeIn();
    });
});


/* ---------------- CLOSE -------------------- */
$(".close-message").click(function() {
    $(this).parent().fadeOut(500);
});

/* ------------- USERS COLLECTION ------------ */
$('#add-user').click(function (){
    // Get futures fields created
    const index = +$('#widgets-counter').val();

    // Get entry prototype
    const tmpl = $('#edit_promotion_users').data('prototype').replace(/__name__/g, index);

    // Adding prototype in div
    $('#edit_promotion_users').append(tmpl);

    $('#widgets-counter').val(index + 1);

    // Delete buttons action
    handleDeleteButtons();
});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounter() {
    const count = +$('#edit_promotion_users div.promo-add-users').length;

    $('#widgets-counter').val(count);
}
updateCounter();
handleDeleteButtons();