//Visualización de formularios para listados por criterio

$('#knowledgeSearch').click(function(event) {
    event.preventDefault();

    $('#studySelect').hide();
    $('#customSelect').hide();

    $('#knowledgeSelect').show();
});

$('#studySearch').click(function(event) {
    event.preventDefault();

    $('#knowledgeSelect').hide();
    $('#customSelect').hide();

    $('#studySelect').show();
});

$('#customSearch').click(function(event) {
    event.preventDefault();

    $('#knowledgeSelect').hide();
    $('#studySelect').hide();

    $('#customSelect').show();
});

$('#categorySearch').click(function(event) {
    event.preventDefault();

    $('#studySelect').hide();
    $('#customSelect').hide();

    $('#categorySelect').show();
});

/*** Acciones de adición */

//Estudio

$('a.study-add-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de añadir este estudio?")){
        $.ajax({
            type: 'GET',
            url: link.attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().append($('img').attr({
                    src: '../../assets/img/load.gif',
                    id: 'loadImg'
                }));
            },
            success: function() {
                $('#studyAdded').show();
                link.parent().parent().remove();
            },
            error: function() {
                $('#error').show();
                $('#loadImg').remove();
            }
        });
    }
});

//Conocimiento

$('a.knowledge-add-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de añadir este conocmiento?")){
        $.ajax({
            type: 'GET',
            url: link.attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().append($('img').attr({
                    src: '../../assets/img/load.gif',
                    id: 'loadImg'
                }));
            },
            success: function() {
                $('#knowledgeAdded').show();
                link.parent().parent().remove();
            },
            error: function() {
                $('#error').show();
                $('#loadImg').remove();
            }
        });
    }
});

//Empresa

$('a.company-add-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de añadir esta empresa?")){
        $.ajax({
            type: 'GET',
            url: link.attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().append($('img').attr({
                    src: '../../assets/img/load.gif',
                    id: 'loadImg'
                }));
            },
            success: function() {
                $('#companyAdded').show();
                link.parent().parent().remove();
            },
            error: function() {
                $('#error').show();
                $('#loadImg').remove();
            }
        });
    }
});

/*** Acciones de eliminación */

//Alumno

$('a.student-remove-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de eliminar este alumno?")){
        $.ajax({
            type: 'DELETE',
            url: link.attr('href'),
            beforeSend: function() {
                link.html('');
                link.append($('img').attr({
                    src: '../../assets/img/load.gif',
                    id: 'loadImg'
                }));
            },
            success: function() {
                window.location.replace('http://pidam.local/students/lists')
            },
            error: function() {
                $('#error').show();
                $('#loadImg').remove();
                link.html('<i class="fi-trash small-icon"></i>Borrar');
            }
        });
    }
});

//Estudio

$('a.study-remove-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de eliminar este estudio?")){
        $.ajax({
            type: 'DELETE',
            url: link.attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().append($('img').attr({
                    src: '../../assets/img/load.gif',
                    id: 'loadImg'
                }));
            },
            success: function() {
                $('#studyRemoved').show();
                link.parent().parent().remove();
            },
            error: function() {
                $('#error').show();
                $('#loadImg').remove();
            }
        });
    }
});

//Conocimiento

$('a.knowledge-remove-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de eliminar este conocimiento?")){
        $.ajax({
            type: 'DELETE',
            url: link.attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().append($('img').attr({
                    src: '../../assets/img/load.gif',
                    id: 'loadImg'
                }));
            },
            success: function() {
                $('#knowledgeRemoved').show();
                link.parent().parent().remove();
            },
            error: function() {
                $('#error').show();
                $('#loadImg').remove();
            }
        });
    }
});

//Empresa

$('a.company-remove-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de eliminar esta empresa?")){
        $.ajax({
            type: 'DELETE',
            url: link.attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().append($('img').attr({
                    src: '../../assets/img/load.gif',
                    id: 'loadImg'
                }));
            },
            success: function() {
                $('#companyRemoved').show();
                link.parent().parent().remove();
            },
            error: function() {
                $('#error').show();
                $('#loadImg').remove();
            }
        });
    }
});