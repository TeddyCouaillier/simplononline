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