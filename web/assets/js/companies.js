//Visualización de búsqueda por categoría

$('#categorySearch').click(function(event) {
    event.preventDefault();

    $('#customSelect').hide();

    $('#categorySelect').show();
});

/*** Acciones de adición */

//Actividad

$('a.category-add-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de añadir esta actividad?")){
        $.ajax({
            type: 'GET',
            url: link.attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().prepend('<img id="loadImg" src="/assets/img/load.gif"/>');
            },
            success: function() {
                $('#activityAdded').show();
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

/*** Acciones de eliminación */

//Categoría

$('a.category-remove-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de eliminar esta categoría?")){
        $.ajax({
            type: 'DELETE',
            url: link.attr('href'),
            beforeSend: function() {
                link.parent().append('<img id="loadImg" src="/assets/img/load.gif"/>');
            },
            success: function() {
                $('#activityAdded').show();
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

//Empresa

$('a.remove-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de eliminar esta empresa?")){
        $.ajax({
            type: 'DELETE',
            url: link.attr('href'),
            beforeSend: function() {
                link.parent().append('<img id="loadImg" src="/assets/img/load.gif"/>');
            },
            success: function() {
                $('#activityAdded').show();
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
