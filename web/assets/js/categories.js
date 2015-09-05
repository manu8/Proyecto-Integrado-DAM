$('a.remove-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de eliminar esta categoría/actividad?")){
        $.ajax({
            type: 'DELETE',
            url: link.attr('href'),
            beforeSend: function() {
                link.parent().append('<img id="loadImg" src="/assets/img/load.gif"/>');
            },
            success: function() {
                $('#categoryRemoved').show();
                link.parent().parent().remove();
            },
            error: function() {
                $('#error').show();
                $('#loadImg').remove();
                link.show();
            }
        });
    }
});