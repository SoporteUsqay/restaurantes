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
    "oTableTools": {
            "sSwfPath": "recursos/swf/copy_csv_xls_pdf.swf",
            "aButtons": [
            {
                "sExtends": "csv"
            },
            {
                "sExtends": "pdf",
                "sPdfOrientation": "landscape",
                "title": "miau",
                "sPdfMessage": $("#mensaje").val()
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

$('#fecha_inicio').datepicker({dateFormat: 'yy-mm-dd',
changeMonth: true,
changeYear: true
});

$('#fecha_fin').datepicker({dateFormat: 'yy-mm-dd',
changeMonth: true,
changeYear: true
});

//eventos filtrado de datos

$("#fecha_inicio").change(function(){
    $("#dfin").show("fast");
});

$("#tipo_busqueda_fecha").change(function(){
   switch($("#tipo_busqueda_fecha").val()){
       case "d":
           $("#dinicio").show("fast");
           $("#dfin").hide("fast");
           $("#mes").hide("fast");
           $("#ano").hide("fast");
       break;
       
       case "m":
           $("#dinicio").hide("fast");
           $("#dfin").hide("fast");
           $("#mes").show("fast");
           $("#ano").show("fast");
       break;
       
       case "a":
           $("#dinicio").hide("fast");
           $("#dfin").hide("fast");
           $("#mes").hide("fast");
           $("#ano").show("fast");
       break;
   } 
});

$("#tipo_busqueda_dato").change(function(){
   switch($("#tipo_busqueda_dato").val()){
       case "to":
           $("#platos").hide("fast");
           $("#productos").hide("fast");
       break;
       
       case "pl":
           $("#platos").show("fast");
           $("#productos").hide("fast");
       break;
       
       case "pr":
           $("#platos").hide("fast");
           $("#productos").show("fast");
       break;
   } 
});
});

function filtrar(){
    location.href = "consolidado.php?s="+$("#id_sucursal").val()+"&tpf="+$("#tipo_busqueda_fecha").val()+"&fi="+$("#fecha_inicio").val()+"&ff="+$("#fecha_fin").val()+"&mes="+$("#mes_busqueda").val()+"&ano="+$("#ano_busqueda").val()+"&tpd="+$("#tipo_busqueda_dato").val()+"&plato="+$("#plato_busqueda").val()+"&producto="+$("#producto_busqueda").val();
}




//function pdf_consolidado() {
//    
//    var url = location.href = "pdf_consolidadoVentas.php?sucursal="+$("#id_sucursal").val()+"&fecha="+"2015-08-09";   
////  var url = "<?php echo Class_config::get('urlApp') ?>/pdf_consolidadoVentas.php?sucursal=" + <?php echo $sucursal ?> + "&fecha=" + <?php echo $ffecha?>;
//   window.open(url, '_blank');
//}