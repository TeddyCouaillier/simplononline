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

$(document).on('click','.subnav',function() {
    $(this).find('.subnav-tooltip').fadeToggle();
})

/* ----------------- NAV --------------------- */
$(function () {
    const modal = $('#modal-weather');
    const update = $(modal).data('update');
    if(parseInt(update) == 0){
        $(modal).modal('show');
    }
})

/* ----------------- NAV --------------------- */
$('.logout').click(function(){
    if($('.nav-dropdown').css('display') == 'none'){
        $('.nav-dropdown').fadeIn(500);
    } else {
        $('.nav-dropdown').fadeOut(500);
    }
})

$(document).mouseup(function(e)
{
    var container = $(".nav-dropdown");
    var tooltip = $(".subnav-tooltip");
    if (!container.is(e.target) && container.has(e.target).length === 0)
    {
        container.fadeOut(500);
    }
    if(!container.is(e.target) && tooltip.has(e.target).length === 0)
    {
        tooltip.fadeOut(500);
    }
});

/* ---------------- CLOSE -------------------- */
$(".close-message").click(function() {
    $(this).parent().parent().fadeOut(500);
});

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

function projectPanel(el){
    $('.project-nav').removeClass('active');
    $(el).addClass('active');
    const pan = $(el).data('project');
    $('.project-content').hide(750);
    $(`.project-content#${pan}`).show(750);
}


$(document).on('click','.notif',function() {
    $(this).parent().parent().find('.user-notif').fadeToggle();
    // $(this).find('.subnav-tooltip').fadeToggle();
})

$(document).on('click','.dots',function() {
    $(this).parent().parent().find('.user-subnav').fadeToggle();
    // $(this).find('.subnav-tooltip').fadeToggle();
})