function consulta_cp() {
    var cp = $('#cp').val();
    $.ajax({
        async: true,
        cache: false,
        type: "POST",
        datatype: "html",
        contentType: "application/x-www-form-urlencoded",
        url: '/Alumnos/buscacp',
        data: "cp=" + cp,
        beforeSend: function() {
            $("#contenedor_cp").text('Cargando...');
        },
        success: function(datos) {
            $("#contenedor_cp").html(datos);
        },
        timeout: 4000,
        error: function(xhr, ajaxOptions, thrownError) {
            alert('ESTATUS: ' + xhr.status + '' + 'ERROR: ' + thrownError);
        }
    });
}
$(function() {
    $('#N_ID_PAIS').combobox({
        url: '/Alumnos/consultapais',
        valueField: 'Code',
        textField: 'Name',
        required: true
    });
	$('#N_TURNO').combobox({
        panelHeight: 'auto',
        selectOnNavigation: false,
        valueField: 'id',
        textField: 'text',
        editable: false,
        required: true,
        data: [{"id": "1", "text": "Matutino"}, {"id": "2", "text": "Vespertino"},{"id": "3", "text": "Diurna"}]
    });
    $('#N_ID_ESTATUS').combobox({
        panelHeight: 'auto',
        selectOnNavigation: false,
        valueField: 'id',
        textField: 'text',
        editable: false,
        required: true,
        data: [{"id": "1", "text": "Activo"}, {"id": "2", "text": "Inactivo"}]
    });
    $('#D_FECHA_INGRESO').datebox({
        required: true,
        formatter: function(date) {
            return date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear();
        }
    });

    $('#D_FECHA_NACIMIENTO').datebox({
        required: true,
        formatter: function(date) {
            return date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear();
        }
    });
    $('#S_SEXO').combobox({
        panelHeight: 'auto',
        selectOnNavigation: false,
        valueField: 'id',
        textField: 'text',
        editable: false,
        required: true,
        data: [{"id": "1", "text": "Masculino"}, {"id": "2", "text": "Femenino"}]
    });


});
