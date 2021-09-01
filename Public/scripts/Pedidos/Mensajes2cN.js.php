<?php require_once '../../../Components/Config.inc.php'; ?>
//<script>
//Variables para pago
var total = 0;
var total_venta = 0;
var por_pagar = 0;
var pagado_efectivo = 0;
var pagado_otros = 0;
var descuento = 0;
var porcentaje_descuento = 0;
var vuelto = 0;
var tipo_pago_final = 0;

var id_pedido = null;
var tabla_pedidos = null;
var tabla_pagos = null;

//Variable para bloquear envio
var enviando = 0;

//Funcion para abortar el proceso de pago
function aborta_pago(){
    total = 0;
    total_venta = 0;
    por_pagar = 0;
    pagado_efectivo = 0;
    pagado_otros = 0;
    descuento = 0;
    porcentaje_descuento = 0;
    vuelto = 0;
    tipo_pago_final = 0;
    actualiza_por_pagar();
    actualiza_vuelto();
    tabla_pagos.clear().draw();
    $("#documento_pago").val("");
    $("#cliente_pago").val("");
    $("#direccion_pago").val("");
    $("#correo_pago").val("");
    //Reseteamos Pago
    $("#medio_pago").val($("#medio_pago option:first").val());
    $("#moneda_pago").val($("#moneda_pago option:first").val());
    $("#moneda_pago").removeAttr("disabled");
    $("#monto_pago").val(0);
    //Reseteamos descuento
    $("#descuento_prefijado_pago").val($("#descuento_prefijado_pago option:first").val());
    $("#porcentaje_descuento_pago").val(0);
    $("#monto_descuento_pago").val(0);
    //Cerramos Modal
    $("#modal_multiple_pago").modal("hide");      
}

//Funcion para Pagar
function paga_final(){
    var documento_pago = $("#documento_pago").val();
    var cliente_pago = $("#cliente_pago").val();
    var direccion_pago = $("#direccion_pago").val();
    var correo_pago = $("#correo_pago").val();

    var parcial = 0;
    var verificacion_cliente = 0;
    var mensaje = null;
    var items = null;
    var pagos = null;
    var pedido = $('#txtCombrobante').val();
    var tipo_impresion = "CUENTA";
    var consumo = 1;
    var sin_pago = 0;

    var total_pagado = pagado_efectivo + pagado_otros;
    total_pagado = my_round(total_pagado,2);
    total = my_round(total,2);

    if((total_pagado === 0) || (total_pagado >= total)){
        if((total_pagado === 0) && ($("#medio_pago").val() != "1|1")){
            alert("¡Recuerda que si no agregas un pago toda la venta sera considerada en efectivo!");
        }else{
            if($('#check_consumo').is(':checked')){
                consumo = 2;
            }

            if($('#check_pago').is(':checked')){
                sin_pago = 1;
            }

            if(parseInt(tipo_pago_final) === 1){
                var tipo_impresion = "BOLETA";

                if(parseFloat(total) > 700){
                    if(documento_pago !== "" && cliente_pago !== "" && direccion_pago !== ""){
                        if(documento_pago.length >= 8){
                            verificacion_cliente = 1;
                        }else{
                            mensaje = "El documento debe ser de al menos 8 digitos";
                            verificacion_cliente = 0;
                        }
                    }else{
                        mensaje = "Para montos mayores a 700 soles la boleta debe llevar datos";
                        verificacion_cliente = 0;
                    }
                }else{
                    if(documento_pago.length > 1){
                        if(documento_pago.length >= 8){
                            verificacion_cliente = 1;
                        }else{
                            mensaje = "El documento debe ser de al menos 8 digitos";
                            verificacion_cliente = 0;
                        }
                    }else{
                        verificacion_cliente = 1;
                    }
                }
            }
            
            if(parseInt(tipo_pago_final) === 2){
                var tipo_impresion = "FACTURA";
                if(documento_pago !== "" && cliente_pago !== "" && direccion_pago !== ""){
                    if(documento_pago.length === 11){
                        verificacion_cliente = 1;
                    }else{
                        mensaje = "El RUC debe ser de 11 digitos";
                        verificacion_cliente = 0;
                    }
                }else{
                    mensaje = "Es obligatorio poner los datos del cliente en una factura";
                    verificacion_cliente = 0;
                }
            }
        
            items = JSON.stringify($('#tabla_pedidos').DataTable().rows().data().toArray());
            
            if(tipo_pago_final === 0){
                verificacion_cliente = 1;
            }

            pagos = JSON.stringify($('#tabla_pagos').DataTable().rows().data().toArray());

            if(verificacion_cliente === 1){
                $("#modal_multiple_pago").modal("hide");
                $('#modal_envio_anim').modal('show');
                var param = {tipo_comprobante: tipo_pago_final, documento: documento_pago, cliente: cliente_pago, direccion: direccion_pago, correo: correo_pago, productos: items, pagos: pagos, total: por_pagar,descuento_monto: descuento, descuento_porcentaje: porcentaje_descuento, vuelto: vuelto, pagado_efectivo: pagado_efectivo, pagado_otros: pagado_otros, pkPediido: pedido, parcial: parcial, mesa: pk_mesa_cookie, consumo: consumo, sin_pago: sin_pago};
                $.ajax({
                    url: "<?php echo Class_config::get('urlApp') ?>/?controller=Comprobante&&action=Comprobante2019",
                    type: 'POST',
                    data: param,
                    cache: false,
                    dataType: "json",
                    beforeSend: function (xhr) {
                        xhr.overrideMimeType("text/plain; charset=x-user-defined");
                    },
                    success: function(data1) {
                        if(parseInt(data1.exito) == 1){
                            //Finalmente ponemos en cola la impresion
                            var param2 = {'pkPedido': data1.id_comprobante, 'terminal': '<?php echo $_COOKIE['t'] ?>', 'tipo': tipo_impresion, 'aux': '<?php echo $_SESSION['id'];?>,'+consumo};
                            $.ajax({
                                url: "<?php echo '//' . $_SERVER['HTTP_HOST'] . '/usqay' ?>/?controller=Pedidos&&action=ImprimeCuenta",
                                type: 'POST',
                                data: param2,
                                success: function() {
                                    window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                                },
                                error: function () {
                                    $("#modal_envio_anim").modal("hide");
                                    alert("¡No se pudo imprimir!. Recuerda que puedes obtener el comprobante en el reporte Ventas por Tipo");
                                    window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                                }
                            });
                        }else{
                            if(parseInt(data1.exito) == 2){
                                alert(data1.mensaje);
                                $('#modal_envio_anim').modal('hide');
                                window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                            }else{
                                alert(data1.mensaje);
                                $('#modal_envio_anim').modal('hide');
                                $("#modal_multiple_pago").modal("show");
                            }
                        }
                    },
                    error: function () {
                        $("#modal_envio_anim").modal("hide");
                        alert("Hubo un error de comunicacion");
                        $("#modal_multiple_pago").modal("show");
                    }
                });
            }else{
                alert("Error: "+mensaje);
            }
        }  
    }else{
        alert("DEBES PAGAR LA CUENTA COMPLETA");
    }
}
    
$(document).ready(function () {
    $.ajaxSetup({ cache: false });

    visualizaTipoUsuario();

    $("#dsc_mon").html(simbolo_nacional);

    $("#check_pago").change(function() {
        if(this.checked) {
            tabla_pagos.clear().draw();
            pagado_efectivo = 0;
            pagado_otros = 0;
            vuelto = 0;
            actualiza_vuelto();
        }
    });

    $('#documento_pago').keypress(function(event) {       
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            $("#datos_cliente_pago").hide(0);
            $("#datos_cliente_busqueda").show(0);

            $("#cliente_pago").val("");
            $("#direccion_pago").val("");
            $("#correo_pago").val("");

            var param = {'document': $('#documento_pago').val()};

            $.ajax({                   
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=ClienteDni",
                type: 'POST',
                data: param,
                cache: false,
                dataType: 'json',
                success: function(data0) {
                    var hay = 0;
                    $.each( data0, function( key0, value0 ) {
                        $("#cliente_pago").val(value0.nombres);
                        $("#direccion_pago").val(value0.direccion);
                        $("#correo_pago").val(value0.email);
                        hay = 1;
                        $("#datos_cliente_pago").show(0);
                        $("#datos_cliente_busqueda").hide(0);
                        return false;
                    });

                    if(hay === 0){
                    $.ajax({
                        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=ClienteRuc",
                        type: 'POST',
                        data: param,
                        cache: false,
                        dataType: 'json',
                        success: function(data1) {
                            $("#datos_cliente_pago").show(0);
                            $("#datos_cliente_busqueda").hide(0);
                            $.each( data1, function( key1, value1 ) {
                                $("#cliente_pago").val(value1.companyName);
                                $("#direccion_pago").val(value1.address);
                                $("#correo_pago").val(value1.email);
                                return false;
                            });
                        }
                    });
                    }
                }
            });
        }
    });

    $('#documento_sin_pago').keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            $("#frm_sin_pago").hide(0);
            $("#busqueda_sin_pago").show(0);

            $("#cliente_sin_pago").val("");
            $("#direccion_sin_pago").val("");

            var param = {'document': $('#documento_sin_pago').val()};

            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=ClienteDni",
                type: 'POST',
                data: param,
                cache: false,
                dataType: 'json',
                success: function(data1) {
                    var hay = 0;
                    $.each( data1, function( key1, value1 ) {
                        $("#cliente_sin_pago").val(value1.nombres);
                        $("#direccion_sin_pago").val(value1.direccion);
                        $("#tipo_cliente_sin_pago").val(1);
                        $('#lbl_documento_sin_pago').html("DNI");
                        $('#lbl_cliente_sin_pago').html("Cliente");
                        hay = 1;
                        $("#frm_sin_pago").show(0);
                        $("#busqueda_sin_pago").hide(0);
                        return false;
                    });

                    if(hay === 0){
                        $.ajax({
                            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=ClienteRuc",
                            type: 'POST',
                            data: param,
                            cache: false,
                            dataType: 'json',
                            success: function(data0) {
                                $("#frm_sin_pago").show(0);
                                $("#busqueda_sin_pago").hide(0);
                                $.each( data0, function( key0, value0 ) {
                                    $("#cliente_sin_pago").val(value0.companyName);
                                    $("#direccion_sin_pago").val(value0.address);            
                                    $("#tipo_cliente_sin_pago").val(2);
                                    $('#lbl_documento_sin_pago').html("RUC");
                                    $('#lbl_cliente_sin_pago').html("Razon Social");
                                    return false;
                                });
                            }
                        });
                    }
                }
            });
        }
    });

});

function openImprimirCuenta() {
    $("#modal_envio_anim").modal("show");
    var param = {'pkPedido': $("#txtCombrobante").val(), 'terminal': $('#terminal').val(), 'tipo':'CUENTA', 'aux': '<?php echo UserLogin::get_id();?>'};
    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ImprimeCuenta",
        type: 'POST',
        data: param,
        cache: false,
        dataType: "json",
        beforeSend: function (xhr) {
            xhr.overrideMimeType("text/plain; charset=x-user-defined");
        },
        success: function(mdata) {
            if(parseInt(mdata.exito) === 1){
                $("#modal_envio_anim").modal("hide");
            }else{
                alert("¡Este pedido ya fue cobrado, no se puede imprimir PRE-CUENTA!");
                window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
            }
        },
        error: function () {
            $("#modal_envio_anim").modal("hide");
            alert("Hubo un error de comunicacion");
        }
    });
}

//Cargamos el pedido
function loadPedidoPk($pkPedido) {
    var param = {'pkPedido': $pkPedido};
    $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListPedidosPk",
        type: 'GET',
        data: param,
        beforeSend: function(xhr) {
        xhr.overrideMimeType("text/plain; charset=x-user-defined");

        },
        dataType: 'json',
        success: function(data) {
        if(Array.isArray(data)){
            var tipo_trabajador = <?php echo UserLogin::get_pkTypeUsernames(); ?>;
            var id_trabajador = <?php echo UserLogin::get_idTrabajador();?>;
            var avanza = 1;

            if(avanza === 1){
                $("#lblnMesa").html(data[0].nmesa);
                $("#lblnSalon").html(data[0].nsalon);
                $("#lblnMoso").html(data[0].ntrabajador);
                $("#txtnPesonas").val(data[0].npersonas);
                $("#TxtPkMesa").val(data[0].pkMesa);
                $("#txtCombrobante").val(data[0].nComprobante);        
                $("#txtDescuento").val(data[0].descuento);
                $("#documentoCliente").val(data[0].documento);
                $("#salon").val(data[0].pkSalon);
                var pkp = data[0].nComprobante;
                id_pedido = pkp;

                tabla_pedidos = $('#tabla_pedidos').DataTable( {
                    dom: 'Blfrtip',
                    "bSort": false,
                    "bFilter": false,
                    "bInfo": false,
                    "ordering": false,
                    "paging": false,
                    "scrollY": '280px',
                    "scrollCollapse": true,
                    "paging":false,
                    "language": {
                    "emptyTable": "Esperando Pedido"
                    },
                    "ajax": {
                        "url": "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListPedidosItem",
                        "data": {
                        "comprobante": id_pedido
                        },
                        "dataSrc": "",
                        error: function (jqXHR, textStatus, errorThrown) {
                            // No hacemos nada
                        }
                    },
                    "columns": [
                        { "data": "pedido" },
                        { "data": "precio" },
                        { "data": "cantidad" },
                        { "data": "importe" },
                        { "data": "Destado" },
                        { "data": "mozo" },
                        { "data": "pkProducto" },
                        { "data": "pkPedido" },
                        { 
                        "data": null,
                        render: function ( data, type, row ) {
                            return "<span id='T"+row.pkPedido+"' hora-inicio='"+row.hora+"'>00:00:00</span>";
                        }
                        },                    
                    ],
                    "columnDefs": [
                    /*{
                        "targets": [ 4 ],
                        "visible": false,
                    },*/
                    {
                        "targets": [ 6 ],
                        "visible": false,
                    },
                    {
                        "targets": [ 7 ],
                        "visible": false,
                    },
                    {
                        "targets": [ 8 ],
                        "visible": false,
                    }
                    ],
                    "initComplete": function(settings, json) {
                        sumaTotal();
                    },
                    "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                        var sDirectionClass = "";
                        if((nRow.cells[4].innerText).search("Anulado") === -1){
                            sDirectionClass = "";
                        }else{
                            sDirectionClass = "anulado";
                        }
                        $(nRow).addClass( sDirectionClass );
                        return nRow;
                    }
                } );

                tabla_pagos = $('#tabla_pagos').DataTable( {
                    dom: 'Blfrtip',
                    "bSort": false,
                    "bFilter": false,
                    "bInfo": false,
                    "ordering": false,
                    "paging": false,               
                    "scrollCollapse": true,
                    "paging":false,
                    "language": {
                    "emptyTable": "Agrega las formas de pago aqui"
                    },
                    "columnDefs": [
                    {
                        "targets": [ 1 ],
                        "visible": false,
                    },
                    {
                        "targets": [ 2 ],
                        "visible": false,
                    },
                    {
                        "targets": [ 3 ],
                        "visible": false,
                    }
                    ],
                } );

                //Funcion para eliminar pagos/propinas
                $('#tabla_pagos tbody').on( 'click', '.eliminar', function () {
                    console.log( tabla_pagos.row($(this).parents('tr')).data() );
                    if(tabla_pagos.row($(this).parents('tr')).data()[0] === 'PAGO'){
                        //Restamos Pagos
                        if(parseInt(tabla_pagos.row($(this).parents('tr')).data()[1]) == 1){
                            //Verificar moneda y tipo cambio
                            if(parseInt(tabla_pagos.row($(this).parents('tr')).data()[2]) > 1){
                                pagado_efectivo = pagado_efectivo - my_round(parseFloat(tabla_pagos.row($(this).parents('tr')).data()[7])*parseFloat(cambios[parseInt(tabla_pagos.row($(this).parents('tr')).data()[2])]),2);
                            }else{
                                pagado_efectivo = pagado_efectivo - parseFloat(tabla_pagos.row($(this).parents('tr')).data()[7]);
                            }
                        }else{                 
                            //Verificar moneda y tipo cambio
                            if(parseInt(tabla_pagos.row($(this).parents('tr')).data()[2]) > 1){
                                pagado_otros = pagado_otros - my_round(parseFloat(tabla_pagos.row($(this).parents('tr')).data()[7])*parseFloat(cambios[parseInt(tabla_pagos.row($(this).parents('tr')).data()[2])]),2);
                            }else{
                                pagado_otros = pagado_otros - parseFloat(tabla_pagos.row($(this).parents('tr')).data()[7]);
                            }
                        }

                        //Calculamos vuelto
                        var total_pagado = pagado_efectivo + pagado_otros;
                        if(total_pagado > por_pagar){
                            var saldo = total_pagado - por_pagar;
                            if(pagado_efectivo >= saldo){
                                vuelto = saldo;
                            }else{
                                vuelto = pagado_efectivo;
                            }
                        }else{
                            vuelto = 0;
                        }

                        actualiza_vuelto();
                    }

                    tabla_pagos
                        .row( $(this).parents('tr') )
                        .remove()
                        .draw();
                } );                  
            }  
        }else{
            alert("¡No se encontro el credito!");
            window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
        }   
        }
    });
}

function loadDetalles() {
    $('#tabla_pedidos').DataTable().ajax.reload(function(){
        sumaTotal();
    },true);
}

function sumaTotal() {
    var items = 0;
    var soles = 0;
    for (var i = 0; i < $('#tabla_pedidos').DataTable().rows().data().toArray().length; i++) {
        if(($('#tabla_pedidos').DataTable().rows().data().toArray()[i].Destado).search("Anulado") === -1){
            items = items + parseFloat($('#tabla_pedidos').DataTable().rows().data().toArray()[i].cantidad);
            soles = soles + parseFloat($('#tabla_pedidos').DataTable().rows().data().toArray()[i].importe);
        }
    }
    items = my_round(items,2);
    soles = my_round(soles,2);
    $("#lblItems2019").html(items);
    $("#lblTotal2019").html(soles);
}

//Muestra botones avanzados solo para Administradores, Gerentes, Cajeros o Anuladores
//Modificado en Agosto 2019 - Gino Lluen
function visualizaTipoUsuario() {
    var type = "<?php echo UserLogin::get_pkTypeUsernames(); ?>";
    type = parseInt(type);
    switch (type) {
        case 1:
        case 2:
        case 8:
        case 9:
        $(".admin").show(0);
        break;

        default:
        $(".admin").hide(0);
        break;
    }
}
  
function agrega_pago(){
    var valor_medio = $("#medio_pago").val();
    var nombre_medio = $("#medio_pago option:selected").text();
    var simbolo_moneda = $("#moneda_pago option:selected").text();
    var monto = my_round(parseFloat($("#monto_pago").val()),2);
    var operacion = $("#operacion_pago").val();

    var mensaje_error = "";
    var pagado_actual = pagado_efectivo+pagado_otros;
    var continuar = 1;
    var saldo = my_round(por_pagar,2) - my_round(pagado_actual,2);
    saldo = my_round(saldo,2);
    if(saldo < 0){
        saldo = 0;
    }

    var propiedades_medio = valor_medio.split("|");
    var id_medio_m = propiedades_medio[0];
    var id_moneda_m = $("#moneda_pago").val();

    //Verficamos si ya no es necesario mas pagos
    if(pagado_actual >= por_pagar){
        continuar = 0;
        mensaje_error = "La venta ya fue pagada completamente";
    }
    //Verificamos si el monto no es 0, o menos
    if(parseFloat(monto) <= 0){
        continuar = 0;
        mensaje_error = "Ingresa un monto valido mayor a 0";
    }
    //Verificamos si se quiere ingresar un monto mayor al restante (cuando no es efectivo)
    if((monto>saldo) && (parseInt(id_medio_m) > 1)){
        continuar = 0;
        mensaje_error = "Ingresa un monto valido menor o igual que el saldo por pagar";
    }

    if(continuar === 1){
        tabla_pagos.row.add( [
            'PAGO',
            id_medio_m,
            id_moneda_m,
            0,
            operacion,
            nombre_medio,
            simbolo_moneda,
            monto,
            '<span style="color:red;" class="glyphicon glyphicon-trash eliminar" aria-hidden="true"></span>'
        ] ).draw( false );

        //Sumamos Pagos
        if(parseInt(id_medio_m) == 1){
            //Verificar moneda y tipo cambio
            if(parseInt(id_moneda_m) > 1){
                pagado_efectivo = pagado_efectivo + my_round(parseFloat(monto)*parseFloat(cambios[parseInt(id_moneda_m)]),2);
            }else{
                pagado_efectivo = pagado_efectivo + parseFloat(monto);
            }
        }else{                 
            //Verificar moneda y tipo cambio
            if(parseInt(id_moneda_m) > 1){
                pagado_otros = pagado_otros + my_round(parseFloat(monto)*parseFloat(cambios[parseInt(id_moneda_m)]),2);
            }else{
                pagado_otros = pagado_otros + parseFloat(monto);
            }
        }

        //Calculamos vuelto
        var total_pagado = pagado_efectivo + pagado_otros;
        if(total_pagado > por_pagar){
            var saldo = total_pagado - por_pagar;
            if(pagado_efectivo >= saldo){
                vuelto = saldo;
            }else{
                vuelto = pagado_efectivo;
            }
        }

        //Reseteamos
        $("#medio_pago").val($("#medio_pago option:first").val());
        $("#moneda_pago").val($("#moneda_pago option:first").val());
        $("#moneda_pago").removeAttr("disabled");
        $("#monto_pago").val(0);
        $("#operacion_pago").attr("disabled", "true");
        $("#operacion_pago").val("");

        actualiza_vuelto();
    }else{
        $("#monto_pago").val(saldo);
        alert(mensaje_error);
    }
}

function actualiza_pago(accion){
    var medio = $("#medio_pago").val();
    var moneda = $("#moneda_pago").val();
    var monto = $("#monto_pago").val();

    var propiedades_medio = medio.split("|");
        var id_medio_m = propiedades_medio[0];
        var id_moneda_m = propiedades_medio[1];

    switch(accion){
        case 1:
            //Validamos cuando se mueve el selector de medio
            switch(parseInt(id_medio_m)){
                case 1:
                    $("#moneda_pago").val(1);
                    $("#moneda_pago").removeAttr("disabled");
                    $("#operacion_pago").val("");
                    $("#operacion_pago").attr("disabled", "true");
                break;
                
                default:
                    $("#moneda_pago").attr("disabled", "true");					
                    $("#moneda_pago").val(id_moneda_m);
                    $("#operacion_pago").removeAttr("disabled");
                break;
            }
        break;
    }
}

function my_round(value, decimals) {
    return Number(Math.round(value+'e'+decimals)+'e-'+decimals);
}

function actualiza_descuento(accion){
    var predeterminado =  $("#descuento_prefijado_pago").val();
    var porcentaje = parseFloat($("#porcentaje_descuento_pago").val()).toFixed(2);
    var monto = parseFloat($("#monto_descuento_pago").val()).toFixed(2);
    var maximo_descuento = my_round((total_venta*0.99),2);
    if(porcentaje >= 99){
        porcentaje = 99;
    }
    if(monto >= maximo_descuento){
        monto = maximo_descuento;
    }
    switch(accion){
        case 1:
            //Acciones en base al combo predeterminado
            if(predeterminado == ""){
                $("#porcentaje_descuento_pago").val(0);
                $("#monto_descuento_pago").val(0);
            }else{
                var propiedades_descuento = predeterminado.split("|");
                var porcentaje_pre = parseFloat(propiedades_descuento[1]).toFixed(2);
                var max_pre = parseFloat(propiedades_descuento[2]).toFixed(2);
                
                $("#porcentaje_descuento_pago").val(porcentaje_pre);
                
                var descuento_calculo = ((total_venta*porcentaje_pre)/100).toFixed(2);
                if(max_pre == 0){
                    $("#porcentaje_descuento_pago").val(porcentaje_pre);
                    $("#monto_descuento_pago").val(descuento_calculo);
                }else{
                    if(Number(descuento_calculo) >= Number(max_pre)){
                        //alert("GG");                      
                        var nuevo_porcentaje = ((max_pre*100)/total_venta).toFixed(2);
                        $("#porcentaje_descuento_pago").val(nuevo_porcentaje);
                        $("#monto_descuento_pago").val(max_pre);
                    }else{
                        $("#porcentaje_descuento_pago").val(porcentaje_pre);
                        $("#monto_descuento_pago").val(descuento_calculo);
                    }
                }		
            }
        break;
        
        case 2:
            //Acciones en base a porcentaje
            $("#descuento_prefijado_pago").val("");
            var descuento_calculo = ((total_venta*porcentaje)/100).toFixed(2);
            $("#porcentaje_descuento_pago").val(porcentaje);
            $("#monto_descuento_pago").val(descuento_calculo);
        break;
        
        case 3:
            //Acciones en base a monto
            $("#descuento_prefijado_pago").val("");
            var nuevo_porcentaje = ((monto*100)/total_venta).toFixed(2);
            $("#porcentaje_descuento_pago").val(nuevo_porcentaje);
            $("#monto_descuento_pago").val(monto);
        break;
    }
}

function aplicar_descuento(){
    descuento = $("#monto_descuento_pago").val();
    porcentaje_descuento = $("#porcentaje_descuento_pago").val();
    por_pagar = total_venta - parseFloat(descuento).toFixed(2);
    por_pagar = parseFloat(por_pagar).toFixed(2);
    total = por_pagar;
    actualiza_por_pagar();
    actualiza_vuelto();
}

function modal_pagar_2019(tipo_pago){
    tipo_pago_final = tipo_pago;
    switch(parseInt(tipo_pago)){
        case 0:
        $("#datos_cliente_pago").hide(0);
        $("#titulo_modal_multiple").html("Generando Ticket");
        break;

        case 1:
        $("#datos_cliente_pago").show(0);
        $("#titulo_modal_multiple").html("Generando Boleta");
        break;

        case 2:
        $("#datos_cliente_pago").show(0);
        $("#titulo_modal_multiple").html("Generando Factura");
        break;
    }

    total_venta = parseFloat($("#lblTotal2019").html());
    total_venta = my_round(total_venta,2);
    total = parseFloat($("#lblTotal2019").html());
    total = my_round(total,2);
    por_pagar = parseFloat($("#lblTotal2019").html());
    por_pagar = my_round(por_pagar,2);

    $("#monto_modal_multiple").html("Total Venta: "+monedas[0]["simbolo"]+$("#lblTotal2019").html());
    
    actualiza_por_pagar();
    actualiza_vuelto();
    $("#modal_multiple_pago").modal("show");
}

function actualiza_por_pagar(){
    var html_por_pagar = "";
    $(monedas).each(function( index, element ) {
    if(parseInt(element['id']) == 1){
        html_por_pagar += "<br/>"+element['simbolo']+""+my_round(por_pagar,2)+"";
    }else{
        html_por_pagar += "<br/>"+element['simbolo']+""+my_round(por_pagar/parseFloat(cambios[parseInt(element['id'])]),2)+"";
    }
    });
    $("#monto_pago").val(my_round(por_pagar,2));
    $("#monto_por_pagar").html(html_por_pagar);
}

function actualiza_vuelto(){
    var html_vuelto = "";
    var html_pagado = "";
    $(monedas).each(function( index, element ) {
    if(parseInt(element['id']) == 1){
        html_vuelto += "<br/>"+element['simbolo']+""+my_round(vuelto,2)+"";
        html_pagado += "<br/>"+element['simbolo']+""+my_round(pagado_efectivo+pagado_otros,2)+"";
    }else{
        html_vuelto += "<br/>"+element['simbolo']+""+my_round(vuelto/parseFloat(cambios[parseInt(element['id'])]),2)+"";
        html_pagado += "<br/>"+element['simbolo']+""+my_round((pagado_efectivo+pagado_otros)/parseFloat(cambios[parseInt(element['id'])]),2)+"";
    }
    });
    var nuevo_monto_pago = my_round(por_pagar,2)-my_round(pagado_efectivo+pagado_otros,2);
    if(nuevo_monto_pago >= 0){
        $("#monto_pago").val(my_round(nuevo_monto_pago,2));
    }else{
        $("#monto_pago").val(0);
    }    
    $("#pagado").html(html_pagado);
    $("#vuelto").html(html_vuelto);
}

var tipo_sin_pago = 0;
//Funciones para cerrar la mesa sin pagos
function sin_pago(tipo_){
    tipo_sin_pago = tipo_;
    if(parseInt(tipo_) === 1){
        $("#titulo_sin_pago").html("Cancelando a Credito");
    }else{
        $("#titulo_sin_pago").html("Cancelando por Consumo");
    }
    $("#modal_sin_pago").modal("show");
}

//Cambio de etiquetas segun tipo de cliente
function cambia_tipo_cliente_sin_pago() {
    var tipo_ = $("#tipo_cliente_sin_pago").val();
    if (parseInt(tipo_) === 1) {
        $('#lbl_documento_sin_pago').html("DNI");
        $('#lbl_cliente_sin_pago').html("Cliente");
    }
    else {
        $('#lbl_documento_sin_pago').html("RUC");
        $('#lbl_cliente_sin_pago').html("Razon Social");
    }
}

//Cancelamos cierre sin pago
function aborta_sin_pago(){
    var tipo_sin_pago = 0;
    $("#tipo_cliente_sin_pago").val(1);
    $('#lbl_documento_sin_pago').html("DNI");
    $('#lbl_cliente_sin_pago').html("Cliente");
    $('#documento_sin_pago').val("");
    $("#cliente_sin_pago").val("");
    $("#direccion_sin_pago").val("");
    $("#modal_sin_pago").modal("hide");
}

//Cerramos cuenta sin pago
function cancela_sin_pago() {
    //Primero enviamos pedidos pendientes
    var url_final = "";
    var tipo_impresion = "";
    var tipo_ = $("#tipo_cliente_sin_pago").val();
    if (tipo_sin_pago === 1) {
        url_final = "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=CancelaPedidoCredito";
        tipo_impresion = "CREDITO";
    }
    else {
        url_final = "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=CancelaPedidoACuenta";
        tipo_impresion = "CONSUMO";
    }

    if($('#documento_sin_pago').val() != "" && $('#cliente_sin_pago').val() != "" && $('#direccion_sin_pago').val() != ""){
        if(enviando === 0){
            enviando = 1;
            $("#modal_sin_pago").modal("hide");
            $('#modal_envio_anim').modal('show');
            var param1 = {'pkPedido': $('#txtCombrobante').val(), documento: $('#documento_sin_pago').val(),
            tipo_cliente: tipo_, valor1: $('#cliente_sin_pago').val(), valor2: $('#direccion_sin_pago').val(),total: parseFloat($("#lblTotal2019").html())};
            $.ajax({
                url: url_final,
                type: 'POST',
                data: param1,
                cache: false,
                dataType: "json",
                beforeSend: function (xhr) {
                    xhr.overrideMimeType("text/plain; charset=x-user-defined");
                },
                success: function (data1) {
                    if(parseInt(data1.exito) == 1){
                        //Finalmente ponemos en cola la impresion
                        var param2 = {'pkPedido': $("#txtCombrobante").val(), 'terminal': $('#terminal').val(), 'tipo': tipo_impresion, 'aux': '<?php echo UserLogin::get_id();?>'};
                            $.ajax({
                            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ImprimeCuenta",
                            type: 'POST',
                            data: param2,
                            success: function() {
                                window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                            },
                            error: function () {
                                $("#modal_envio_anim").modal("hide");
                                alert("¡No se pudo imprimir!. Recuerda que puedes obtener el comprobante en el reporte Ventas por Tipo");
                                window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                            }
                        });  
                    }else{
                        $("#modal_envio_anim").modal("hide");
                        alert("¡Este pedido ya finalizo!");
                        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                    }                   
                },
                error: function () {
                    $("#modal_envio_anim").modal("hide");
                    alert("Hubo un error de comunicacion");
                }
            });
        }
    }else{
        alert("Debes llenar todos los datos");
    }
}
