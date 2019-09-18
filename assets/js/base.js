

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
    if (!container.is(e.target) && (container.has(e.target).length === 0 || tooltip.has(e.target).length === 0))
    {
        container.fadeOut(500);
    }
});

$(document).mouseup(function(e)
{
    var container = $(".notif");
    var tooltip = $(".user-notif");
    if (!container.is(e.target) && (container.has(e.target).length === 0 || tooltip.has(e.target).length === 0))
    {
        container.removeClass('text-black');
        tooltip.fadeOut(500);
    }
});

$(document).mouseup(function(e)
{
    var container = $(".dots");
    var tooltip = $(".user-subnav");
    if (!container.is(e.target) && (container.has(e.target).length === 0 || tooltip.has(e.target).length === 0))
    {
        container.removeClass('active');
        tooltip.fadeOut(500);
    }
});

/* ---------------- CLOSE -------------------- */
$(".close-message").click(function() {
    $(this).parent().parent().fadeOut(500);
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

$(document).on('click','.project-nav', function(){
    $('.project-nav').each(function(){
        $(this).removeClass('active');
    });

    $(this).addClass('active');
    const pan = $(this).data('project');
    $('.project-content').hide(750);
    $(`.project-content#${pan}`).show(750);
})

$(document).on('click','.notif',function() {
    if($(this).parent().parent().find('.user-notif').css('display') == 'none'){
        $(this).parent().parent().find('.user-notif').fadeIn();
        $(this).addClass('text-black');
    } else {
        $(this).parent().parent().find('.user-notif').fadeOut();
        $(this).removeClass('text-black');
    }
})

$(document).on('click','.dots',function() {
    if($(this).parent().parent().find('.user-subnav').css('display') == 'none'){
        $(this).parent().parent().find('.user-subnav').fadeIn();
        $(this).addClass('active');
    } else {
        $(this).parent().parent().find('.user-subnav').fadeOut();
        $(this).removeClass('active');
    }
})


$(document).on('click','.nav-left-button', function() {
    const navleft = $(this).parent().find('.nav-left');
    if($(this).hasClass('active')){
        $(navleft).removeClass('active');
        $(this).removeClass('active');
    } else {
        $(navleft).addClass('active');
        $(this).addClass('active');
    }
})

$(document).on('click', '.learn', function() {
    $('.hire-container').slideUp();
    $('.heart-container').slideUp();
    $('.learn-container').slideDown();
    $([document.documentElement, document.body]).animate({
        scrollTop: $(".learn-container").offset().top
    }, 1000);
})
$(document).on('click', '.hire', function() {
    $('.learn-container').fadeOut(function(){
        $('.heart-container').fadeOut();
        $('.hire-container').fadeIn();
        $([document.documentElement, document.body]).animate({
            scrollTop: $(".hire-container").offset().top
        }, 1000);

    });
})
$(document).on('click', '.heart', function() {
    $('.hire-container').slideUp();
    $('.learn-container').slideUp();
    $('.heart-container').slideDown();
    $([document.documentElement, document.body]).animate({
        scrollTop: $(".heart-container").offset().top
    }, 1000);
})

$(document).on('click', '.promo-show', function() {
    $('.promo-show').each(function(){
        let promos = $(this).data('promo');
        let containers = $('body').find(promos);
        $(this).removeClass('active');
        $(containers).fadeOut();
    });
    const promo = $(this).data('promo');
    const container = $('body').find(promo);
    const promoid = $(container).attr('id');
    console.log(promoid);
    $(this).addClass('active');
    $(container).fadeIn(function (){
        $([document.documentElement, document.body]).animate({
            scrollTop: $(`#${promoid}`).offset().top
        }, 1000);
    });
})

$(document).on('click','.project-info', function() {
    $('.project-info-container').fadeToggle();
})

$(document).on('click','.navleft-toggle', function() {
    $('.navleft-container').slideToggle(750);
    if($(this).hasClass('shadow-small')){
        $(this).removeClass('shadow-small');
        $(this).html('<i class="fal fa-times text-xlarge"></i>');
    } else {
        $(this).addClass('shadow-small');
        $(this).html('<i class="fal fa-bars text-large"></i>');
    }
})

$('.task').collection({
    'up': '<a href="#" class="mx-1" aria-label="Remonter"><i class="fal fa-chevron-circle-up"></i></a>',
    'down': '<a href="#" class="mx-1" aria-label="Redescendre"><i class="fal fa-chevron-circle-down"></i></a>',
    'remove': '<a href="#" class="mx-1 text-red" aria-label="Enlever"><i class="fal fa-times-circle"></i></a>',
    'add': '<button class="button-edit my-2 mx-auto d-block" aria-label="Ajouter"><i class="fal fa-plus"></i></button>',
    'add_at_the_end': true
});

$(document).on('click','.edit-weather', function() {
    $('.weather-choice').slideToggle();
})