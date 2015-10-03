$('a.remove-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de eliminar esta categoría/actividad?")){
        $.ajax({
            type: 'DELETE',
            url: link.attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().append('<img id="loadImg" class="img-responsive" src="/assets/img/load.gif"/>');
            },
            success: function() {
                window.location.replace('http://'+ document.domain + '/categories/lists')
            },
            error: function() {
                $('#error').show();
                $('#loadImg').remove();
                link.show();
            }
        });
    }
});