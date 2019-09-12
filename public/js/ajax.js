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
                        $('.edit-task').collection({
                            'up': '<a href="#" class="mx-1"><i class="fal fa-chevron-circle-up"></i></a>',
                            'down': '<a href="#" class="mx-1"><i class="fal fa-chevron-circle-down"></i></a>',
                            'remove': '<a href="#" class="mx-1 text-red"><i class="fal fa-times-circle"></i></a>',
                            'add': '<button class="button-edit my-2 mx-auto d-block"><i class="fal fa-plus"></i></button>',
                            'add_at_the_end': true
                        });
                        $('.task-user-selected').selectpicker();
                    </script>
                `);
            }
        }
    });
})

$(document).on('click','.promo-edit',function(){
    const url = $(this).data('url');
    modal = $(this).data('target');
    $.ajax({
        url: url,
        type: "POST",

        success: function(response){
            if (url !== undefined)
            {
                $('.modal[id*="modal-promo-edit-"] .modal-body').html("");
                $(modal).find('.modal-body').append(response.render + `
                    <script>
                        $('.promo-mod-selected').selectpicker();
                    </script>
                `);
            }
        }
    });
})


function seeMore(el){
    counter = parseInt($(el).parent().parent().find('#task-counter').val());
    const url  = $(el).data('url');
    const type = $(el).data('type');

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
                $(el).parent().parent().find('.tasks').append(response.render);
                $(el).parent().parent().find('#task-counter').val(counter+response.size);
                if(response.size < 5){
                    $(el).parent().remove();
                }
            }
        }
    });
}

$(document).on('click','.promocurrent', function() {
    const check = $(this);
    $('.promocurrent').each(function(){
        $(this).html('activer');
        $(this).removeClass('bg-red');
        $(this).removeClass('text-white');
        $(this).addClass('text-grey');
    });

    const url = $(this).data('promourl');

    $.ajax({
        url: url,
        type: "POST",

        success: function(){
            $(check).html('en cours');
            $(check).removeClass('text-grey');
            $(check).addClass('bg-red');
            $(check).addClass('text-white');
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
            }
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
            }
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
            }
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
            }
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
            }
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
            }
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
            }
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
            console.log('Ah non !');
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
                    $(container).parent().html('<div class="flex just-center align-center text-center text-medium text-greyl" style="height: 100px"><p><i class="fal fa-times-hexagon text-large"></i><br>Aucune deadline</p></div>');
                }, 500);
            }
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
        }
    });
})