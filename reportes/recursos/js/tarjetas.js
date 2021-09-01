jQuery.fn.reset = function () {
    $(this).each(function () {
        this.reset();
    });
};


$(document).ready(function () {

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

$('#fecha_inicio').datepicker({dateFormat: 'yy-mm-dd',
changeMonth: true,
changeYear: true
});

$('#fecha_fin').datepicker({dateFormat: 'yy-mm-dd',
changeMonth: true,
changeYear: true
});

$("#fecha_inicio").change(function(){
    $("#fecha_fin").val($("#fecha_inicio").val());
});

});