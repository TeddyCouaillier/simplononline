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

$(document).mouseup(function(e)
{
    var container = $(".nav-dropdown");
    if (!container.is(e.target) && container.has(e.target).length === 0)
    {
        container.fadeOut(500);
    }
});


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
    if ($(event.target).attr('id') == $('.modal-weather').attr('id')) {

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
    updateCounterUser();
    const index = +$('#widgets-counter').val() + 1;
    const tmpl = $('#edit_promotion_users').data('prototype').replace(/__name__/g, index);
    $('#edit_promotion_users').append(tmpl);
    handleDeleteButtons();
});

function handleDeleteButtons() {
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounterUser() {
    const last = $('#edit_promotion_users div.promo-add-users').last();
    var lastIndex = $(last).attr('id');
    lastIndex = parseInt(lastIndex.replace('block_edit_promotion_users_', ''));
    $('#widgets-counter').val(lastIndex);
}
handleDeleteButtons();

/* ------------- FILE USERS SELECTED ------------ */
$(function() {
    $('.multipleselect').multipleSelect({
        filter: true,
        formatSelectAll () {
        return 'Tout sélectionner'
        },
        formatAllSelected () {
        return 'Tous les apprenants'
        },
        formatCountSelected (count, total) {
        return count + ' sur ' + total + ' apprennants'
        },
        formatNoMatchesFound () {
        return 'Aucun apprenant'
        }
    })
})

/* ------------- FILE USERS SELECTED ------------ */
$(function() {
    $('.multipleselectlang').multipleSelect({
        filter: true,
        formatSelectAll () {
        return 'Tout sélectionner'
        },
        formatAllSelected () {
        return 'Tous les langages'
        },
        formatCountSelected (count, total) {
        return count + ' sur ' + total + ' langages'
        },
        formatNoMatchesFound () {
        return 'Aucun langage'
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

/* ----------------- CAT DISPLAY ----------------- */
$('.help-all_cats span').click(function() {
    var category = $(this).data('badge');

    $('.help-all_cats span').addClass('badge-link-inactive').removeClass('badge-link-active');
    $(this).removeClass('badge-link-inactive')
    $(this).addClass('badge-link-active');

    if (category === "Tout")
    {
        var elems = $('.help-all .help-link');
        Array.from(elems).forEach(function(elem) {
            $(elem).show();
        });
    }
    else
    {
        var elems = $('.help-all .help-link');

        Array.from(elems).forEach(function(elem) {
            var category_ = $(elem).data('badge');

            $(elem).hide();

            if (category_ === category)
            {
                $(elem).show();
            }
        });
    }

});

/* ----------------- Trainings ------------------ */
$('#add-training').click(function (){
    const index = +$('#widgets-counter').val();
    const tmpl = $('#training_course_user_trainingCourse').data('prototype').replace(/__name__/g, index);
    $('#training_course_user_trainingCourse').append(tmpl);
    $('#widgets-counter').val(index + 1);
    handleDeleteButtonsTraining();

    $('.training-project-toggle').off('click');
    $('.training-project-toggle').on('click',function(){
        $(this).parent().parent().find('.training-project').fadeToggle(500);
        $(this).parent().find('.chevron').toggleClass('down');
    })
});

function handleDeleteButtonsTraining() {
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounterTraining() {
    const count = +$('#training_course_user_trainingCourse div.trainings').length;
    $('#widgets-counter').val(count);
}
updateCounterTraining();
handleDeleteButtonsTraining();

function changeStatus(el){
    var cl =  $(el).attr("class").split(" ");
    var newcl =[];
    for(var i=0;i<cl.length;i++){
        r = cl[i].search(/edit-status-+/);
        if(r)newcl[newcl.length] = cl[i];
    }
    switch ($(el).val()) {
        case "Intéressé(e)" :
            $(el).removeClass().addClass(newcl.join(" "));
            $(el).addClass("edit-status-yellow");
            break;
        case "En attente de réponse" :
            $(el).removeClass().addClass(newcl.join(" "));
            $(el).addClass("edit-status-blue");
            break;
        case "Entretien" :
            $(el).removeClass().addClass(newcl.join(" "));
            $(el).addClass("edit-status-purple");
            break;
        case "Réponse positive" :
            $(el).removeClass().addClass(newcl.join(" "));
            $(el).addClass("edit-status-green");
            break;
        case "Réponse négative" :
            $(el).removeClass().addClass(newcl.join(" "));
            $(el).addClass("edit-status-red");
            break;
        default:
            $(el).removeClass().addClass(newcl.join(" "));
    }
}

$('.training-project-toggle').on('click',function(){
    $(this).parent().parent().find('.training-project').fadeToggle(500);
    $(this).parent().find('.chevron').toggleClass('down');

})

$('#add-training-admin').click(function (){
    const index = +$('#widgets-counter').val();
    const tmpl = $('#admin_training_course_user_trainingCourse').data('prototype').replace(/__name__/g, index);
    $('#admin_training_course_user_trainingCourse').append(tmpl);
    $('#widgets-counter').val(index + 1);
    handleDeleteButtonsTrainingAdmin();

    $('.training-project-toggle').off('click');
    $('.training-project-toggle').on('click',function(){
        $(this).parent().parent().find('.training-project').fadeToggle(500);
        $(this).parent().find('.chevron').toggleClass('down');
    })
});

function handleDeleteButtonsTrainingAdmin() {
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounterTrainingAdmin() {
    const count = +$('#admin_training_course_user_trainingCourse div.trainings').length;
    $('#widgets-counter').val(count);
}
updateCounterTrainingAdmin();
handleDeleteButtonsTrainingAdmin();

function projectPanel(el){
    $('.project-nav').removeClass('active');
    $(el).addClass('active');
    const pan = $(el).data('project');
    $('.project-content').hide(750);
    $(`.project-content#${pan}`).show(750);
}

/* ------------- SUBTASK COLLECTION ------------ */
$('#add-subtask').click(function (){
    const index = +$('#widgets-counter').val();
    const tmpl = $('#task_subtasks').data('prototype').replace(/__name__/g, index);
    $('#task_subtasks').append(tmpl);
    $('#widgets-counter').val(index + 1);
    handleDeleteButtonsTask();
});

function handleDeleteButtonsTask() {
    $('button[data-action="delete"]').click(function(){
        const target = this.dataset.target;
        $(target).remove();
    });
}

function updateCounterTask() {
    const count = +$('#task_subtasks div.task-add-subtask').length;
    $('#widgets-counter').val(count);
}
updateCounterTask();
handleDeleteButtonsTask();
