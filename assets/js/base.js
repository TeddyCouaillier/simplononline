

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

$(function() {
    const messages = $("body").find('.message-flash');

    messages.each(function(index, el){
        var type = "success";
        if($(this).hasClass('message-flash-success')){
            type = "success";
        } else if($(this).hasClass('message-flash-warning')){
            type = "warning"
        } else {
            type = "danger"
        }
    });
})

function removeNotify(interval, elem) {
    clearInterval(interval);
    $(elem).slideUp(250);
    setTimeout(() => {
        $(elem).remove();
    }, 250);
}

function Notify(message,type) {
    var classM = "";
    if(type == "success"){
        classM = '<i class="fas fa-check text-success"></i>';
    } else if(type == "warning"){
        classM = '<i class="fas fa-times text-warning"></i>'
    } else {
        classM = '<i class="fas fa-times text-red"></i>'
    }
    const content = `
        <div class="toast-message animated slideInLeft" >
            <div class="progress-message progress-${type}"></div>
            <p class="content">
                ${message}
            </p>
            ${classM}
        </div>
    `;

    const elem = $(content).appendTo('.toast-content');

    const interval = setInterval(() => {
        var prog  = $(elem).find(`.progress-message`);
        var width = $(prog).css("width");
        width = parseInt(width.slice(0, width.length - 2)) - 1;
        $(prog).css("width", width);

        if (width <= 0) {
            removeNotify(interval, elem)
        }
    }, 15);
}

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
$(document).on('click','.copyToClipboard', function(){ 
    const id = $(this).data('link-id');
    const el = $(`.data-link-${id}`);

    var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(el).text()).select();
    document.execCommand("copy");
    $temp.remove();
});

/* ----------------- CAT DISPLAY ----------------- */
// Quand on clique sur le bouton du filtre
$('.help-all_cats span').click(function() {
    // On recupere la categorie
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

$(document).on('click','.training-project-toggle', function(){
    $(this).parent().parent().find('.training-project').fadeToggle(500);
    $(this).parent().find('.chevron').toggleClass('down');

})

$(document).on('click','.project-nav', function(){
    $('.project-nav').each(function(){
        $(this).removeClass('active');
    });

    $(this).addClass('active');
    const pan = $(this).data('project');
    $('.project-content').fadeOut(750);
    $(`.project-content#${pan}`).fadeIn(750);
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

$(document).on('click','.backtotop', function(e) {
    e.preventDefault();
    $('html,body').animate({
        scrollTop: 0
    }, 700);
})