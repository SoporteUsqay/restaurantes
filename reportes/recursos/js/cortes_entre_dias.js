jQuery.fn.reset = function () {
    $(this).each(function () {
        this.reset();
    });
};


$(document).ready(function () {
    $('#tb').dataTable({
        "dom": 'T<"clear">lfrtip',
        "bSort": false,
        "bFilter": false,
        "bInfo": false,
        "ordering": false,
        "oTableTools": {
                "sSwfPath": "recursos/swf/copy_csv_xls_pdf.swf",
                "aButtons": [
                {
                    "sExtends": "csv"
                },
                {
                    "sExtends": "pdf",
                    "sPdfOrientation": "landscape",
                    "title": "Cortes entre dias"
                }
            ]
        }
    });

    $.datepicker.regional['es'] = 
    {
        closeText: 'Cerrar', 
        prevText: 'Previo', 
        nextText: 'Próximo',

        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
        'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
        monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
        'Jul','Ago','Sep','Oct','Nov','Dic'],
        monthStatus: 'Ver otro mes', yearStatus: 'Ver otro año',
        dayNames: ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'],
        dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sáb'],
        dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
        dateFormat: 'yy-mm-dd', firstDay: 0, 
        initStatus: 'Selecciona la fecha', isRTL: false};
        $.datepicker.setDefaults($.datepicker.regional['es']);

        $('#inicio').datepicker({dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
        });

        $('#fin').datepicker({dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
        });

});
