//Visualización de búsqueda por categoría

$('#categorySearch').click(function(event) {
    event.preventDefault();

    $('#studySelect').hide();
    $('#customSelect').hide();

    $('#categorySelect').show();
});

/*** Acciones de adición */

//Categoría

$('a.category-add-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de añadir esta categoría?")){
        $.ajax({
            type: 'GET',
            url: link.attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().prepend('<img id="loadImg" class="img-responsive" src="/assets/img/load.gif"/>');
            },
            success: function() {
                $('#categoryAdded').show();
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

//Alumno

$('a.student-add-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de añadir este alumno?")){
        $.ajax({
            type: 'GET',
            url: link.attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().prepend('<img id="loadImg" class="img-responsive" src="/assets/img/load.gif"/>');
            },
            success: function() {
                $('#studentAdded').show();
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
                link.hide();
                link.parent().append('<img id="loadImg" class="img-responsive" src="/assets/img/load.gif"/>');
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

//Alumno

$('a.student-remove-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de eliminar este alumno?")){
        $.ajax({
            type: 'DELETE',
            url: link.attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().append('<img id="loadImg" class="img-responsive" src="/assets/img/load.gif"/>');
            },
            success: function() {
                $('#studentRemoved').show();
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

//Acción de eliminación de estudio

$('a.remove-link').click(function(event) {
    event.preventDefault();
    if(confirm("¿Estás seguro de eliminar este estudio?")){
        $.ajax({
            type: 'DELETE',
            url: $(this).attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().append('<img id="loadImg" class="img-responsive" src="/assets/img/load.gif"/>');
            },
            success: function() {
                window.location.replace('http://'+ document.domain + '/studies/lists')
            },
            error: function() {
                $('#error').show();
                $('#loadImg').remove();
                link.show();
            }
        });
    }
});