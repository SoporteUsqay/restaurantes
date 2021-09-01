jQuery.fn.reset = function () {
    $(this).each(function () {
        this.reset();
    });
};


$(document).ready(function () {
    
$('#tipo_plato option').prop('selected', true);


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
    $("#fecha_fin").val($("#fecha_inicio").val());
    $("#dfin").show("fast");
    get_cortes($("#fecha_inicio").val(),'');
    $("#corte").show("fast");
});

$("#fecha_fin").change(function(){
    if($("#fecha_inicio").val() === $("#fecha_fin").val()){
        get_cortes($("#fecha_inicio").val(),'');
        $("#corte").show("fast");
    }else{
        $("#corte").hide("fast");
    }
});

$("#caja").change(function(){
    if($("#caja option:selected").val() === ""){
        $("#corte").hide("fast");
        $("#corte_busqueda").html("");
        $("#corte_busqueda").append('<option value="ALL" selected>Todo el Día</option>');
    }else{
        get_cortes($("#fecha_inicio").val(),'');
        $("#corte").show("fast");
    }
});


$("#tipo_busqueda_fecha").change(function(){
   switch($("#tipo_busqueda_fecha").val()){
       case "d":
           $("#dinicio").show("fast");
           $("#corte").show("fast");
           $("#dfin").hide("fast");
           $("#mes").hide("fast");
           $("#ano").hide("fast");
       break;
       
       case "m":
           $("#dinicio").hide("fast");
           $("#dfin").hide("fast");
           $("#mes").show("fast");
           $("#ano").show("fast");
           $("#corte").hide("fast");
       break;
       
       case "a":
           $("#dinicio").hide("fast");
           $("#dfin").hide("fast");
           $("#mes").hide("fast");
           $("#ano").show("fast");
           $("#corte").hide("fast");
       break;
   } 
});


});

function filtrar(){
    location.href = "consolidado.php?s="+$("#id_sucursal").val()+"&tpf="+$("#tipo_busqueda_fecha").val()+"&fi="+$("#fecha_inicio").val()+"&ff="+$("#fecha_fin").val()+"&mes="+$("#mes_busqueda").val()+"&ano="+$("#ano_busqueda").val()+"&tp="+$("#tipo_plato").val()+"&cc="+$("#corte_busqueda").val()+"&caja="+$("#caja option:selected").val();
}

function get_cortes(fecha_in, gato){
      var caja_a = $("#caja option:selected").val();
      $.post( "../?controller=Caja&&action=ListCortesDia", {fecha:fecha_in, caja:caja_a}, function(data) {
        if(data.length > 0){
          $("#corte_busqueda").html("");          
          $.each( data, function( key, value ) {
            if(value === gato){
              $("#corte_busqueda").append('<option value="'+value+'" selected>'+value+'</option>');
            }else{
              $("#corte_busqueda").append('<option value="'+value+'">'+value+'</option>');
            }
          });
          if(gato === "ALL"){
            $("#corte_busqueda").append('<option value="ALL" selected>Todo el Día</option>');
          }else{
            $("#corte_busqueda").append('<option value="ALL">Todo el Día</option>');
          }
        }else{
          $("#corte_busqueda").html("");
          if(gato === "ALL"){
            $("#corte_busqueda").append('<option value="ALL" selected>Todo el Día</option>');
          }else{
            $("#corte_busqueda").append('<option value="ALL">Todo el Día</option>');
          }
        }
        if(gato === "INI"){
          //filtrar();
        }
      }, "json");
}