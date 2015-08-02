//Visualización de búsqueda por categoría

$('#categorySearch').click(function(event) {
    event.preventDefault();

    $('#studySelect').hide();
    $('#customSelect').hide();

    $('#categorySelect').show();
});

//Acción de eliminación de estudio

$('a.remove-link').click(function(event) {
    event.preventDefault();
    if(confirm("¿Estás seguro de eliminar este estudio?")){
        $.ajax({
            type: 'DELETE',
            url: $(this).attr('href'),
            beforeSend: function() {
                $(this).html($('img').src('../img/load.gif'));
            },
            success: function() {
                $('#knowledgeRemoved').show();
                $(this).parent().parent().remove();
            },
            error: function() {
                $('#error').show();
            }
        });
    }
});