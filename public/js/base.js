/* --------------- BOOTSTRAP ---------------- */
$(function () {
    $('[data-toggle="popover"]').popover()
})

$('.popover-dismiss').popover({
    trigger: 'focus'
})

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
})

/* ----------------- NAV --------------------- */
$('.logout').click(function(){
    $('.nav-dropdown').fadeToggle(500);
})


/* ---------------- MODAL -------------------- */
// When the user clicks the button, open the modal
if($(".modal-button") != null){
    $(".modal-button").on('click', function() {
        var docHeight = 100+$(window).scrollTop();
        docHeight += "px";
        var modalid = $(this).data('modalid');
        $(`.modal[data-modalid='${modalid}'`).fadeIn();
        $(`.modal[data-modalid='${modalid}'`).css("padding-top",docHeight);
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
        var docHeight = 150+$(window).scrollTop();
        docHeight += "px";
        $('.modal').css("padding-top", docHeight);
        var id = $(this).parent().parent().attr('id');
        var tag = event.target.tagName.toLowerCase();
        var popoverParent = $(`${tag}[aria-describedby='${id}']`)
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

/* ------------- FILE USERS SELECTED ------------ */
$(function() {
    $('select').multipleSelect({
        filter: true,
        formatSelectAll () {
        return 'Tout sélectionner'
        },
        formatAllSelected () {
        return 'Tout les utilisateurs'
        },
        formatCountSelected (count, total) {
        return count + ' sur ' + total + ' utilisateurs'
        },
        formatNoMatchesFound () {
        return 'Aucun utilisateur'
        }
    })
})

/* --------------- REVEAL CONTENTS -------------- */
const ratio = .1
const options = {
    root: null,
    rootMargin: '0px',
    threshold: ratio
  }
const handleIntersect = function (entries, observer) {
    entries.forEach(function (entry) {
        if(entry.intersectionRatio > ratio) {
            entry.target.classList.add('reveal-visible')
            observer.unobserve(entry.target)
        }
    })
}
const observer = new IntersectionObserver(handleIntersect, options);
document.querySelectorAll('.reveal').forEach(function (r) {
    observer.observe(r)
})

$(document).ready(function(){
    $(".main-content").fadeIn(500)
});

/* ----------------- COPY LINKS ----------------- */
function copyToClipboard(element) {
    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
}