$(document).ready(function() {
    $('.level').each(function() {
        var color = "level-"+$(this).val();
        $(this).addClass(color);
    })
});

$('.level').change(function() {
    var color = "level-"+$(this).val();
    for(var i = 0 ; i < 4 ; i++){
        $(this).removeClass("level-"+i);
    }
    $(this).addClass(color);
});

$('.circle-level').click(function() {
    var id = $(this).attr('id');
    var color = "level-"+id;
    var input = $(this).parent().parent().find('.level');
    input.val(id);
    for(var i = 0 ; i < 4 ; i++){
        input.removeClass("level-"+i);
    }
    input.addClass(color);
});