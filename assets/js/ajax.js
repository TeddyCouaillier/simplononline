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
        Notify($(this).html(), type);
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
    }, 10);
}

$(document).on('click','.edit-task',function(){
    const url = $(this).data('url');
    modal = $(this).data('target');

    $.ajax({
        url: url,
        type: "POST",

        success: function(response){
            if (url !== undefined)
            {
                $('.modal[id*="modal-edit-"] .modal-body').html("");
                $(modal).find('.modal-body').append(response.render + `
                    <script>
                        $('.subtask').collection({
                            'up': '<a href="#" class="mx-1"><i class="fal fa-chevron-circle-up"></i></a>',
                            'down': '<a href="#" class="mx-1"><i class="fal fa-chevron-circle-down"></i></a>',
                            'remove': '<a href="#" class="mx-1 text-red"><i class="fal fa-times-circle"></i></a>',
                            'add': '<button class="button-edit my-2 mx-auto d-block"><i class="fal fa-plus"></i></button>',
                            'add_at_the_end': true
                        });
                        $('.task-user-selected').selectpicker();
                    </script>
                `);
            } else {
                $('.modal[id*="modal-edit-"] .modal-body').html("");
                $(modal).find('.modal-body').append('Access denied');
            }
        },
        error: function(){
            console.log('AJAX task error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.weather-type', function(){
    const type = parseInt($(this).data('type'));
    if(type >= 1 && type <= 5){
        $.ajax({
            url: '/meteo/edit',
            data: {
                type: type
            },
            type: "POST",

            success: function(response){
                if(response.update){
                    switch(type) {
                        case 1:
                            $('.current-weather').html('<img src="/img/weather/sun.svg" class="img-md" alt="sun">')
                            break;
                        case 2:
                            $('.current-weather').html('<img src="/img/weather/rain.svg" class="img-md" alt="rain">')
                            break;
                        case 3:
                            $('.current-weather').html('<img src="/img/weather/clouds.svg" class="img-md" alt="clouds">')
                            break;
                        case 4:
                            $('.current-weather').html('<img src="/img/weather/thunder.svg" class="img-md" alt="thunder">')
                            break;
                        case 5:
                            $('.current-weather').html('<img src="/img/weather/suncloud.svg" class="img-md" alt="suncloud">')
                            break;
                        default:
                            $('.current-weather').html('<p class="icon-lg"> - </p>')
                    }
                    $('.weather-choice').slideUp();
                    Notify("La météo a bien été mise à jour","success");
                } else {
                    Notify("Problème technique, veuillez réessayer","warning");
                }
            },
            error: function(){
                console.log('AJAX weather error');
                Notify("Problème technique, veuillez réessayer","danger");
            }
        });
    }

});

$(document).on('click','.promo-edit',function(){
    const url = $(this).data('url');
    modal = $(this).data('target');
    $.ajax({
        url: url,
        type: "POST",

        success: function(response){
            if (url !== undefined)
            {
                console.log(response);
                $('.modal[id*="modal-promo-edit-"] .modal-body').html("");
                $(modal).find('.modal-body').append(response.render + `
                    <script>
                        $('.promo-mod-selected').selectpicker();
                    </script>
                `);
            } else {
                Notify("Lien mort","warning");
            }
        },
        error: function(){
            console.log('AJAX promotion error');
            Notify("Problème technique.","danger");
        }
    });
})


$(document).on('click','.see-more-task', function(){
    counter = parseInt($(this).parent().parent().find('#task-counter').val());
    const url  = $(this).data('url');
    const type = $(this).data('type');
    const qqch = $(this);

    $.ajax({
        url: url,
        type: "POST",
        data:{
            type: type,
            offset: counter
        },
        success: function(response){
            if (url !== undefined)
            {
                $(qqch).parent().parent().find('.tasks').append(response.render);
                $(qqch).parent().parent().find('.task-hidden').fadeIn(400);
                $(qqch).parent().parent().find('#task-counter').val(counter+response.size);
                if(response.size < 5){
                    $(qqch).parent().remove();
                }
            } else {
                Notify("Lien mort","warning");
            }
        },
        error: function(){
            console.log('AJAX seemore error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.promocurrent', function() {
    const check = $(this);
    $('.promocurrent').each(function(){
        $(this).html('Activer');
        $(this).addClass('inactive');
    });

    const url = $(this).data('promourl');

    $.ajax({
        url: url,
        type: "POST",

        success: function(){
            $(check).html('En cours');
            $(check).removeClass('inactive');
        },
        error: function(){
            console.log('AJAX promo current error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.edit-data',function(){
    const url = $(this).data('url');
    modal = $(this).data('target');
    $.ajax({
        url: url,
        type: "POST",

        success: function(response){
            if (url !== undefined)
            {
                $('.modal[id*="edit-data-"] .modal-body').html("");
                $(modal).find('.modal-body').append(response.render);
            } else {
                Notify("Lien mort","warning");
            }
        },
        error: function(){
            console.log('AJAX data error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.edit-skill',function(){
    const url = $(this).data('url');
    modal = $(this).data('target');
    $.ajax({
        url: url,
        type: "POST",

        success: function(response){
            if (url !== undefined)
            {
                $('.modal[id*="edit-skill-"] .modal-body').html("");
                $(modal).find('.modal-body').append(response.render);
            } else {
                Notify("Lien mort","warning");
            }
        },
        error: function(){
            console.log('AJAX skill error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.add-project-correction',function(){
    const url = $(this).data('url');
    modal = $(this).data('target');
    $.ajax({
        url: url,
        type: "POST",

        success: function(response){
            if (url !== undefined)
            {
                $('.modal[id*="add-project-correction"] .modal-body').html("");
                $(modal).find('.modal-body').append(response.render);
            } else {
                Notify("Lien mort","warning");
            }
        },
        error: function(){
            console.log('AJAX correction error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.add-project-task',function(){
    const url = $(this).data('url');
    modal = $(this).data('target');
    $.ajax({
        url: url,
        type: "POST",

        success: function(response){
            if (url !== undefined)
            {
                $('.modal[id*="add-project-task"] .modal-body').html("");
                $(modal).find('.modal-body').append(response.render + `
                <script>
                    $('.task').collection({
                        'up': '<a href="#" class="mx-1"><i class="fal fa-chevron-circle-up"></i></a>',
                        'down': '<a href="#" class="mx-1"><i class="fal fa-chevron-circle-down"></i></a>',
                        'remove': '<a href="#" class="mx-1 text-red"><i class="fal fa-times-circle"></i></a>',
                        'add': '<button class="button-edit my-2 mx-auto d-block"><i class="fal fa-plus"></i></button>',
                        'add_at_the_end': true
                    });
                    $('.task-user-selected').selectpicker();
                </script>
                `);
            } else {
                Notify("Lien mort","warning");
            }
        },
        error: function(){
            console.log('AJAX project task error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.edit-project',function(){
    const url = $(this).data('url');
    modal = $(this).data('target');
    $.ajax({
        url: url,
        type: "POST",

        success: function(response){
            if (url !== undefined)
            {
                $('.modal[id*="edit-project"] .modal-body').html("");
                $(modal).find('.modal-body').append(response.render + `
                    <script>
                    $('.project-user-selected').selectpicker();
                    $('.language-selected').selectpicker();
                    </script>
                `);
            } else {
                Notify("Lien mort","warning");
            }
        },
        error: function(){
            console.log('AJAX project error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.edit-correction',function(){
    const url = $(this).data('url');
    modal = $(this).data('target');
    $.ajax({
        url: url,
        type: "POST",

        success: function(response){
            if (url !== undefined)
            {
                $('.modal[id*="edit-correction"] .modal-body').html("");
                $(modal).find('.modal-body').append(response.render);
            } else {
                Notify("Lien mort","warning");
            }
        },
        error: function(){
            console.log('AJAX edit correction error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.remove-language',function(){
    const url = $(this).data('url');
    const pill = $(this);
    $.ajax({
        url: url,
        type: "POST",

        success: function(){
            $(pill).parent().fadeOut(500);
        },
        error: function(){
            console.log('AJAX language error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.remove-file', function() {
    const url = $(this).data('url');
    const file = $(this).parent().parent().parent().parent();
    const nofiles = $('.no-files');
    let counterContainer = $('.file-counter');
    let counter = $('.file-counter').data('counter');

    $.ajax({
        url: url,
        type: "POST",

        success: function(){
            let counterFinal = counter - 1;
            $(counterContainer).data('counter',counterFinal);
            $(file).fadeOut();
            if($('.file-counter').data('counter') <= 0){
                setTimeout(function(){
                    $(nofiles).fadeIn();
                },300);
            }
        },
        error: function(){
            console.log('AJAX file error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.remove-important', function() {
    const url = $(this).data('url');
    const classe = $(this).data('target');
    let filestate = $(`.${classe}`);

    $.ajax({
        url: url,
        type: "POST",

        success: function(){
            $(filestate).removeClass('bg-red');
        },
        error: function(){
            console.log('AJAX remove important error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.game-edit',function(){
    const url = $(this).data('url');
    modal = $(this).data('target');
    $.ajax({
        url: url,
        type: "POST",

        success: function(response){
            if (url !== undefined)
            {
                $('.modal[id*="modal-edit-"] .modal-body').html("");
                $(modal).find('.modal-body').append(response.render);
            } else {
                Notify("Lien mort","warning");
            }
        },
        error: function(){
            console.log('AJAX game error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.vote',function(){
    const button  = $(this);
    const url     = $(this).data('url');
    const content = $(this).data('content');
    const likeBtn    = $(this).parent().find('[data-content=like]');
    const dislikeBtn = $(this).parent().find('[data-content=dislike]');

    if(content == 'like'){
        if($(button).hasClass('text-like')){
            $(this).removeClass('text-like');
        } else {
            $(button).addClass('text-like');
        }

        if ($(dislikeBtn).hasClass('text-dislike'))
        {
            $(dislikeBtn).removeClass('text-dislike');
        }
    } else {
        if(!($(button).hasClass('text-dislike'))){
            $(button).addClass('text-dislike');
        } else {
            $(this).removeClass('text-dislike');
        }

        if ($(likeBtn).hasClass('text-like'))
        {
            $(likeBtn).removeClass('text-like');
        }
    }

    $.ajax({
        url: url,
        data : {
            content: content
        },
        type: "POST",

        success: function(response){
            if(content == 'like'){
                $(button).find('.count').html(response.countLike);
                $(dislikeBtn).find('.count').html(response.countDislike);
            } else {
                $(button).find('.count').html(response.countDislike);
                $(likeBtn).find('.count').html(response.countLike);
            }
        },
        error: function(){
            console.log('AJAX vote error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.active-deadline',function(){
    const url = $(this).data('url');
    const container = $(this).parent();
    let counter = parseInt($('.deadline-counter').html());

    $.ajax({
        url: url,
        type: "POST",

        success: function(){
            $(container).fadeOut(750);
            counter -= 1;
            $('.deadline-counter').html(counter);
            if(counter == 0){
                setTimeout(function(){
                    $(container).parent().html('<p class="text-center text-grey">Aucune deadline</p>');
                }, 750);
            }
        },
        error: function(){
            console.log('AJAX deadline active error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.deadline-state',function(){
    const url = $(this).data('url');
    let validate = 0;
    if($(this).find('span').hasClass('text-red')){
        validate = 1;
    }
    const icon = $(this).find('span');

    $.ajax({
        url: url,
        data : {
            validate: validate
        },
        type: "POST",

        success: function(){
            if(validate){
                $(icon).removeClass('text-red');
            } else {
                $(icon).addClass('text-red');
            }
        },
        error: function(){
            console.log('AJAX deadline state error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.deadline-delete',function(){
    const url = $(this).data('url');
    const container = $(this).parent().parent().parent();
    let counter = parseInt($('.deadline-counter').html());

    $.ajax({
        url: url,
        type: "POST",

        success: function(){
            $(container).fadeOut(500);
            counter -= 1;
            $('.deadline-counter').html(counter);
            if(counter == 0){
                setTimeout(function(){
                    $(container).parent().html('<div class="flex just-center align-center text-center text-medium text-grey" style="height: 100px"><p><i class="fal fa-times-hexagon text-large"></i><br>Aucune deadline</p></div>');
                }, 500);
            }
        },
        error: function(){
            console.log('AJAX deadline delete error');
            Notify("Problème technique.","danger");
        }
    });
})

$(document).on('click','.active-user', function() {
    const check = $(this);

    const url = $(this).data('userurl');
    $.ajax({
        url: url,
        type: "POST",

        success: function(response){
            if($(check).is(':checked')){
                $(check).prop("checked",true);
            } else {
                $(check).prop("checked",false);
            }
        },
        error: function(){
            console.log('AJAX active user error');
            Notify("Problème technique.","danger");
        }
    });
})