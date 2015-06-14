//Visualización de búsqueda por categoría

$('#categorySearch').click(function(event) {
    event.preventDefault();

    $('#studySelect').hide();
    $('#customSelect').hide();

    $('#categorySelect').show();
});

//Acción de eliminación de empresa

$('a.remove-link').click(function(event) {
    event.preventDefault();
    if(confirm("¿Estás seguro de eliminar esta empresa?")){
        $.ajax({
            type: 'DELETE',
            url: $(this).attr('href'),
            beforeSend: function() {
                $(this).html($('img').src('../img/load.gif'));
            },
            success: function() {
                $('#companyRemoved').show();
                $(this).parent().parent().remove();
            },
            error: function() {
                $('#error').show();
            }
        });
    }
});
