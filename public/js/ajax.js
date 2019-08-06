$(document).on('click','.edit-task',function(){
    const url = $(this).data('url');
    console.log(url);
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
                    </script>
                `);
            }
        }
    });
})

$(document).on('click','.edit-promo',function(){
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
                $(el).parent().parent().find('.testt').append(response.render);
                $(el).parent().parent().find('#task-counter').val(counter+response.size);
                if(response.size < 5){
                    $(el).parent().remove();
                }
            }
        }
    });
}