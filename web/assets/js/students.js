//Visualización de formularios para listados por criterio

$('#companySearch').click(function(event) {
    event.preventDefault();

    $('#studySelect').hide();
    $('#customSelect').hide();
    $('#knowledgeSelect').hide();

    $('#companySelect').show();
});

$('#knowledgeSearch').click(function(event) {
    event.preventDefault();

    $('#studySelect').hide();
    $('#customSelect').hide();
    $('#companySelect').hide();

    $('#knowledgeSelect').show();
});

$('#studySearch').click(function(event) {
    event.preventDefault();

    $('#knowledgeSelect').hide();
    $('#customSelect').hide();
    $('#companySelect').hide();

    $('#studySelect').show();
});

$('#customSearch').click(function(event) {
    event.preventDefault();

    $('#knowledgeSelect').hide();
    $('#studySelect').hide();
    $('#companySelect').hide();

    $('#customSelect').show();
});

$('#categorySearch').click(function(event) {
    event.preventDefault();

    $('#studySelect').hide();
    $('#customSelect').hide();
    $('#companySelect').hide();

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
                link.parent().prepend('<img id="loadImg"  src="/assets/img/load.gif"/>');
            },
            success: function() {
                $('#studyAdded').show();
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

$('a.categorized-study-add-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de añadir este estudio?")){
        $.ajax({
            type: 'GET',
            url: link.attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().prepend('<img id="loadImg"  src="/assets/img/load.gif"/>');
            },
            success: function() {
                $('#studyAdded').show();
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
                link.parent().prepend('<img id="loadImg"  src="/assets/img/load.gif"/>');
            },
            success: function() {
                $('#knowledgeAdded').show();
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

$('a.categorized-knowledge-add-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de añadir este conocmiento?")){
        $.ajax({
            type: 'GET',
            url: link.attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().prepend('<img id="loadImg"  src="/assets/img/load.gif"/>');
            },
            success: function() {
                $('#knowledgeAdded').show();
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

$('a.company-add-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de añadir esta empresa?")){
        $.ajax({
            type: 'GET',
            url: link.attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().prepend('<img id="loadImg"  src="/assets/img/load.gif"/>');
            },
            success: function() {
                $('#companyAdded').show();
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

$('a.categorized-company-add-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de añadir esta empresa?")){
        $.ajax({
            type: 'GET',
            url: link.attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().prepend('<img id="loadImg"  src="/assets/img/load.gif"/>');
            },
            success: function() {
                $('#companyAdded').show();
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
                link.parent().append('<img id="loadImg"  src="/assets/img/load.gif"/>');
            },
            success: function() {
                window.location.replace('http://'+ document.domain + '/students/lists')
            },
            error: function() {
                $('#error').show();
                $('#loadImg').remove();
                link.show();
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
                link.parent().append('<img id="loadImg"  src="/assets/img/load.gif"/>');
            },
            success: function() {
                $('#studyRemoved').show();
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
                link.parent().append('<img id="loadImg"  src="/assets/img/load.gif"/>');
            },
            success: function() {
                $('#knowledgeRemoved').show();
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

$('a.company-remove-link').click(function(event) {
    event.preventDefault();
    var link = $(this);
    if(confirm("¿Estás seguro de eliminar esta empresa?")){
        $.ajax({
            type: 'DELETE',
            url: link.attr('href'),
            beforeSend: function() {
                link.hide();
                link.parent().append('<img id="loadImg"  src="/assets/img/load.gif"/>');
            },
            success: function() {
                $('#companyRemoved').show();
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