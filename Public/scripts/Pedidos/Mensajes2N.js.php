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

    //Variable para timeout en dispositivos moviles
    var timeout_idle = null;

    //Variable para bloquear envio
    var enviando = 0;

    //Script teclado agregar pedido
    var contador = 0;
    function Cantidad2($val, $id) {
        if (contador === 0) {
            $('#' + $id).val("");
        }

        var $text = $('#' + $id).val();
        $('#' + $id).val($text + $val);
        contador++;
    }

    function resetP(){
        $("#txtCantidadSend").val(1);
        $("#txt_mensaje_send").val("");
        contador = 0;
    }

    function nobackbutton() {
        window.location.hash = "no-back-button";
        window.location.hash = "Again-No-back-button"; //chrome
        window.onhashchange = function () {
            window.location.hash = "no-back-button";
        };
    }

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
        //Reseteamos Propina
        $("#trabajador_propina_pago").val($("#trabajador_propina_pago option:first").val());
        $('#trabajador_propina_pago').trigger('change');
        $("#medio_propina_pago").val($("#medio_propina_pago option:first").val());
        $("#moneda_propina_pago").val($("#moneda_propina_pago option:first").val());
        $("#monto_propina_pago").val(0);
        $("#moneda_propina_pago").removeAttr("disabled");
        //Reseteamos descuento
        $("#descuento_prefijado_pago").val($("#descuento_prefijado_pago option:first").val());
        $("#porcentaje_descuento_pago").val(0);
        $("#monto_descuento_pago").val(0);
        //Cerramos Modal
        $("#modal_multiple_pago").modal("hide");      
    }

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
        var fpropina = 0;

        var total_pagado = pagado_efectivo + pagado_otros;
        total_pagado = my_round(total_pagado,2);
        total = my_round(total,2);

        if((total_pagado === 0) || (total_pagado + total_detraccion >= total)){
            //Validamos si no ha agragado un medio de pago
            if((total_pagado === 0) && ($("#medio_pago").val() != "1|1")){
                alert("¡Recuerda que si no agregas un pago toda la venta sera considerada en efectivo!");
            }else{               
                if($('#check_consumo').is(':checked')){
                    consumo = 2;
                }

                if($('#check_propina').is(':checked')){
                    fpropina = 1;
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
            
                var row = $('#tabla_pedidos').DataTable().rows('.selected').data().toArray()[0];
                if (row) {
                    parcial = 1;
                }

                if(parcial === 1){
                    items = JSON.stringify($('#tabla_pedidos').DataTable().rows('.selected').data().toArray());
                }else{
                    items = JSON.stringify($('#tabla_pedidos').DataTable().rows().data().toArray());
                }

                if(tipo_pago_final === 0){
                    verificacion_cliente = 1;
                }

                pagos = JSON.stringify($('#tabla_pagos').DataTable().rows().data().toArray());

                if(verificacion_cliente === 1){
                    $("#modal_multiple_pago").modal("hide");
                    $('#modal_envio_anim').modal('show');
                    var param0 = {'pkPedido': $('#txtCombrobante').val(), 'terminal': $('#terminal').val()};
                    $.ajax({
                    url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=EnviaPedido",
                    type: 'POST',
                    data: param0,
                    cache: false,
                    dataType: "json",
                    beforeSend: function (xhr) {
                        xhr.overrideMimeType("text/plain; charset=x-user-defined");
                    },
                    success: function(data0) {
                        if(parseInt(data0.exito) === 1){
                            var param = {tipo_comprobante: tipo_pago_final, documento: documento_pago, cliente: cliente_pago, direccion: direccion_pago, correo: correo_pago, productos: items, pagos: pagos, total: (por_pagar + total_detraccion), descuento_monto: descuento, descuento_porcentaje: porcentaje_descuento, vuelto: vuelto, pagado_efectivo: pagado_efectivo, pagado_otros: pagado_otros, pkPediido: pedido, parcial: parcial, mesa: pk_mesa_cookie, consumo: consumo, propina: fpropina, total_detraccion: total_detraccion};
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
                                            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ImprimeCuenta",
                                                type: 'POST',
                                                data: param2,
                                                success: function() {
                                                    if(parcial === 1){
                                                        window.location.reload();
                                                    }else{
                                                        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                                                    }
                                                },
                                                error: function () {
                                                    if(parcial === 1){
                                                        $("#modal_envio_anim").modal("hide");
                                                        alert("¡No se pudo imprimir!. Recuerda que puedes obtener el comprobante en el reporte Ventas por Tipo");
                                                        window.location.reload();
                                                    }else{
                                                        $("#modal_envio_anim").modal("hide");
                                                        alert("¡No se pudo imprimir!. Recuerda que puedes obtener el comprobante en el reporte Ventas por Tipo");
                                                        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                                                    }
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
                            $("#modal_envio_anim").modal("hide");
                            alert("¡El pedido ya finalizo, no se puede cobrar!");
                            window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                        } 
                    },
                    error: function () {
                        $("#modal_envio_anim").modal("hide");
                        alert("Hubo un error de comunicacion");
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

    var total_detraccion = 0;

    //$(document).ready(function () {
    $(function() {
        $('#modal_envio_anim').modal('show');
        nobackbutton();
        visualizaTipoUsuario();
        
        $.ajaxSetup({ cache: false });

        if(app == 1){
            timeout_idle = setTimeout(muestra_bloqueo,60000);
        }

        $("#btn_desbloquear").click(function(event){
            location.reload();
        });

        $("body").click(function(event){
            if(app == 1){
                clearTimeout(timeout_idle);
                timeout_idle = setTimeout(muestra_bloqueo,60000);
            }
        });

        $("#dsc_mon").html(simbolo_nacional);

        $('#pkch').select2({dropdownParent: $('#modalPlatoCambio')});
        $('#trabajador_propina_pago').select2({dropdownParent: $('#modal_multiple_pago')}); 

        $('#TxtdescripcionProduct').keypress(function (event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                doBuscarMenu($('#TxtdescripcionProduct').val());
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

                if ($('#documento_pago').val().length == 8) {
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
                            
                            }
                        }
                    });
                } else {
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

        $('#documento_sin_pago').keypress(function(event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                $("#frm_sin_pago").hide(0);
                $("#busqueda_sin_pago").show(0);

                $("#cliente_sin_pago").val("");
                $("#direccion_sin_pago").val("");

                var param = {'document': $('#documento_sin_pago').val()};

                if ($('#documento_sin_pago').val().length == 8) {
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
                            
                            }
                        }
                    });
                } else {
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

        //Cargamos info
        loadPedido(pk_mesa_cookie);

        $('#check_detraccion').on('change', () => {

            let isChecked = $('#check_detraccion').prop('checked');

            if(isChecked){
                total_detraccion = my_round(parseFloat($("#lblTotal2019").html()) * porcentaje_detraccion / 100, 2);
                por_pagar -= total_detraccion
                $('#totalDetraccion').html(total_detraccion)
                $('#check_detraccion').hide(0)
                actualiza_por_pagar();
                actualiza_vuelto(); 
            } else {
                total_detraccion = 0;
                $('#totalDetraccion').html(0)
            }
        })
    });

    //Funcion que inicializa la pantalla
    function loadPedido($mesa) {
        var param = {'mesa': $mesa};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListPedidos",
            type: 'GET',
            data: param,
            beforeSend: function(xhr) {
                xhr.overrideMimeType("text/plain; charset=x-user-defined");
            },
            dataType: 'json',
            success: function(data) {
                $('#modal_envio_anim').modal('hide');
                if(Array.isArray(data)){
                    var tipo_trabajador = <?php echo UserLogin::get_pkTypeUsernames(); ?>;
                    var id_trabajador = <?php echo UserLogin::get_idTrabajador();?>;
                    var avanza = 0;
                    if(parseInt(tipo_trabajador) === 4){
                        if(parseInt(id_trabajador) === parseInt(data[0].pktrabajador)){
                            avanza = 1;
                        }else{
                        //Expectoramos
                        alert("¡Esta mesa ya fue tomada por otro usuario!");
                            window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                        }
                    }else{
                        avanza = 1;
                    }

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
                        CargaCliente(pkp);

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
                            "bDestroy": true,
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
                            {
                                "targets": [ 6 ],
                                "visible": false,
                            },
                            {
                                "targets": [ 7 ],
                                "visible": false,
                            }
                            ],
                            "initComplete": function(settings, json) {                              
                                inicializa_contadores();
                                sumaTotal();
                                loadItemsTipoPlato();
                                $('#modal_envio_anim').modal('hide');
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

                        $('#tabla_pedidos tbody').on( 'click', 'tr', function () {
                            if(!$(this).hasClass('anulado')){
                                $(this).toggleClass('selected');
                            }                          
                        } );
                        
                        $('#trabajador_propina_pago').val(data[0].pktrabajador);
                        $('#trabajador_propina_pago').trigger('change');                                
                    }  
                }else{
                    $('#modal_envio_anim').modal('hide'); 
                    alert("¡No hay un pedido asignado a la mesa!");
                    window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                }   
            },
            error: function () {
                $('#modal_envio_anim').modal('hide'); 
                alert("Hubo un error de comunicacion");
                window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
            }
        });
    }

    //Variables y funciones para conteo de tiempo de pedido
    var intervals = [];

    function inicializa_contadores(){
        for (var i = 0; i < $('#tabla_pedidos').DataTable().rows().data().toArray().length; i++) {
                setInterval(counterstrike, 1000,"T"+$('#tabla_pedidos').DataTable().rows().data().toArray()[i].pkPedido);
        }    
    }

    function counterstrike(id){
        var startDateTime = new Date($("#"+id).attr("hora-inicio"));
        var startStamp = startDateTime.getTime();

        var newDate = new Date();
        var newStamp = newDate.getTime();

        newDate = new Date();
        newStamp = newDate.getTime();
        var diff = Math.round((newStamp-startStamp)/1000);
        
        var d = Math.floor(diff/(24*60*60));
        diff = diff-(d*24*60*60);
        var h = Math.floor(diff/(60*60));
        diff = diff-(h*60*60);
        var m = Math.floor(diff/(60));
        diff = diff-(m*60);
        var s = diff;

        h = h + d*24;
        
        $("#"+id).html(h.toString().padStart(2,"0")+":"+m.toString().padStart(2,"0")+":"+s.toString().padStart(2,"0"));
    }

    function loadDetalles() {
        $('#tabla_pedidos').DataTable().ajax.reload(function(){
            sumaTotal();
            inicializa_contadores();
        },true);
    }

    function reDibuja(){
        tabla_pedidos.columns.adjust();
    }

    //Funcion para cargar el cliente asociado al pedido
    function CargaCliente(pkComprobante){
        var param = {'pedido': pkComprobante};
        $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=getPedido",
        type: 'POST',
        dataType: 'json',
        data: param,
        success: function(data) {
            if(data != null){
                if(parseInt(data.tipop) == 1){
                    $("#lblNombreCliente").html(data.cliente);
                    $("#tipoPedidoC").val(data.tipop);
                    $("#idClienteC").val(data.id);
                    //Guardamos Data
                    $("#nombresClienteC").val(data.cliente);
                }else{
                    $("#lblNombreCliente").html(data.nombres+" - "+data.direccion+" - "+data.telefono);
                    $("#tipoPedidoC").val(data.tipop);
                    $("#idClienteC").val(data.id);
                    //Guardamos Data
                    $("#documentoClienteC").val(data.documento);
                    $("#nombresClienteC").val(data.nombres);
                    $("#telefonoClienteC").val(data.telefono);
                    $("#direccionClienteC").val(data.direccion);       
                }
            }
        }
        });
    }

    function editaClienteP(){
        var tipop = $("#tipoPedidoC").val();
        if(parseInt(tipop) == 1){
            $("#txtNameCliente").val($("#nombresClienteC").val());
            $("#rowNombre").show(0);
            $("#rowTelefono").hide(0);
            $("#rowDocumento").hide(0);
            $("#rowDireccion").hide(0);
        }else{
            $("#txtNameCliente").val($("#nombresClienteC").val());
            $("#txtPhoneCliente").val($("#telefonoClienteC").val());
            $("#txtDocCliente").val($("#documentoClienteC").val());
            $("#txtDireccionCliente").val($("#direccionClienteC").val());
            $("#rowNombre").show(0);
            $("#rowTelefono").show(0);
            $("#rowDocumento").show(0);
            $("#rowDireccion").show(0);
        }
        $("#modDatosCliente").modal("show");
    }

    function guardaCliente(){
        var tipop = $("#tipoPedidoC").val();
        if(parseInt(tipop) == 1){       
            $("#nombresClienteC").val($("#txtNameCliente").val());
            $("#lblNombreCliente").html($("#nombresClienteC").val());
        }else{
            $("#nombresClienteC").val($("#txtNameCliente").val());
            $("#telefonoClienteC").val($("#txtPhoneCliente").val());
            $("#documentoClienteC").val($("#txtDocCliente").val());
            $("#direccionClienteC").val($("#txtDireccionCliente").val());
            $("#lblNombreCliente").html($("#nombresClienteC").val()+" - "+$("#direccionClienteC").val()+" - "+$("#telefonoClienteC").val());     
        }
        
        var param = {'pedido': $('#txtCombrobante').val(), 'tipop': tipop , 'nombres': $("#txtNameCliente").val(), 'telefono': $("#txtPhoneCliente").val(), 'documento' : $("#txtDocCliente").val(), 'direccion' : $("#txtDireccionCliente").val()}; 
    
        $.ajax({
        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=updatePedido",
        type: 'POST',
        dataType: 'json',
        data: param,
        success: function() {
            //Nada wey
        }
        });
        $("#modDatosCliente").modal("hide");
    }

    var type_anulacion = '';

    function _showAnulaPedido() {
        var row = $('#tabla_pedidos').DataTable().rows('.selected').data().toArray()[0];
        if (row) {
            type_anulacion = 'anula-pedido'

            if (row.Destado.includes('Solicitar')) {
                _resolverAnulacion();
            } else {
                $('#modMotivoAnulacionPedido').modal('show')
            }
        }
    }

    function _showAnulaPedido1() {
        var row = $('#tabla_pedidos').DataTable().rows('.selected').data().toArray()[0];
        if (row) {
            type_anulacion = 'anula-pedido1'

            if (row.Destado.includes('Solicitar')) {
                _resolverAnulacion();
            } else {
                $('#modMotivoAnulacionPedido').modal('show')
            }
        }
    }

    function _showLiberarMesa() {

        var r = confirm("¿Desea Liberar esta mesa?. Esta acción no se puede deshacer y los pedidos registrados se anularán");
        if (r) {
            type_anulacion = 'liberar-mesa'

            // console.log($('#tabla_pedidos').DataTable().rows().data().toArray().length)

            if ($('#tabla_pedidos').DataTable().rows().data().toArray().length > 0) {
                $('#modMotivoAnulacionPedido').modal('show')
            } else {
                _resolverAnulacion();
            }
        }
    }

    function _resolverAnulacion() {
        
        switch (type_anulacion) {
            case 'anula-pedido' :
                _anulaPedido()
                break
            case 'anula-pedido1' :
                _anulaPedido1()
                break
            case 'liberar-mesa' :
                confirmCancelaMesa()
                break
        }
    }

    //Las funciones de anulacion ahora revisa si el pedido ya fue anulado, de igual manera los detalles
    //Asi evitamos acciones dobles y descuadres
    function _anulaPedido() {
        var row = $('#tabla_pedidos').DataTable().rows('.selected').data().toArray()[0];
        if (row) {
            // var razon = prompt("Describa el motivo:", "");
            var razon = $('#usqay-input-razon-anulacion').val();

            if(verificacion == 1){
                $('#modal_envio_anim').modal('show');
            }
            var param = {'array': JSON.stringify($('#tabla_pedidos').DataTable().rows('.selected').data().toArray()), 'terminal': $('#terminal').val(), 'idPedido': $('#txtCombrobante').val(), 'razon':razon};
            $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=DeletePedido",
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
                        loadDetalles();
                    }else{
                        if(parseInt(mdata.exito) === 2){
                            $("#modal_envio_anim").modal("hide");
                            alert("¡Uno de los detalles seleccionados ya se habia anulado!");
                            loadDetalles();
                        }else{
                            $("#modal_envio_anim").modal("hide");
                            alert("¡No se puede anular los detalles, este pedido ya terminó!");
                            window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                        }
                    }
                    $('#modMotivoAnulacionPedido').modal('hide')
                },
                error: function () {
                    $("#modal_envio_anim").modal("hide");
                    alert("Hubo un error de comunicacion");
                }
            });
        }
        else {
            alert('¡Debe Seleecionar al menos un Item del Listado de Pedidos!');
        }
    }

    function _anulaPedido1() {
        var row = $('#tabla_pedidos').DataTable().rows('.selected').data().toArray()[0];
        if (row) {
            // var razon = prompt("Describa el motivo:", "");
            var razon = $('#usqay-input-razon-anulacion').val();

            if(verificacion == 1){
                $('#modal_envio_anim').modal('show');
            }

            var param = {'array': JSON.stringify($('#tabla_pedidos').DataTable().rows('.selected').data().toArray()), 'terminal': $('#terminal').val(), 'idPedido':$("#txtCombrobante").val(), 'razon':razon};
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=DeletePedido1",
                data: param,
                cache: false,
                dataType: "json",
                beforeSend: function (xhr) {
                    xhr.overrideMimeType("text/plain; charset=x-user-defined");
                },
                success: function(mdata) {
                    if(parseInt(mdata.exito) === 1){
                        $("#modal_envio_anim").modal("hide");
                        loadDetalles();
                    }else{
                        if(parseInt(mdata.exito) === 2){
                            $("#modal_envio_anim").modal("hide");
                            alert("¡Uno de los detalles seleccionados ya se habia anulado!");
                            loadDetalles();
                        }else{
                            $("#modal_envio_anim").modal("hide");
                            alert("¡No se puede anular los detalles, este pedido ya terminó!");
                            window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                        }
                    }
                    $('#modMotivoAnulacionPedido').modal('hide')
                },
                error: function () {
                    $("#modal_envio_anim").modal("hide");
                    alert("Hubo un error de comunicacion");
                }
            });
        }
        else {
            alert('¡Debe Seleecionar al menos un Item del Listado de Pedidos!');
        }
    }

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
                    alert("¡Este pedido ya finalizo, no se puede imprimir PRE-CUENTA!");
                    window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                }
            },
            error: function () {
                $("#modal_envio_anim").modal("hide");
                alert("Hubo un error de comunicacion");
            }
        });
    }

    //Funcion para enviar pedidos
    //Modificada Agosto 2019 - Gino Lluen
    //Ahora verificamos si la mesa sigue abierta
    //Ahora enviamos la lista de pendientes desde el servidor
    function confirImpresion($tipo) {
        if(enviando === 0){
            if(verificacion == 1){
                $('#modal_envio_anim').modal('show');
            }
            enviando = 1;
            var param = {'pkPedido': $('#txtCombrobante').val(), 'terminal': $('#terminal').val()};
            $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=EnviaPedido",
                type: 'POST',
                data: param,
                cache: false,
                dataType: "json",
                beforeSend: function (xhr) {
                    xhr.overrideMimeType("text/plain; charset=x-user-defined");
                },
                success: function(mdata) {
                    if(parseInt(mdata.exito) === 1){
                        if ("<?php echo UserLogin::get_pkTypeUsernames(); ?>" !== '4') {
                            enviando = 0;
                            $('#modal_envio_anim').modal('hide');
                            loadDetalles();
                        }
                        else
                        {
                            if(app === 1){
                                enviando = 0;
                                $('#modal_envio_anim').modal('hide');
                                loadDetalles();
                            }else{
                                window.location.href = "<?php echo Class_config::get('urlApp') ?>/";
                            }
                        }
                    }else{
                        $("#modal_envio_anim").modal("hide");
                        enviando = 0;
                        alert("¡No se puede enviar los items, este pedido ya terminó!");
                        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                    }                    
                },
                error: function () {
                    enviando = 0;
                    $("#modal_envio_anim").modal("hide");
                    alert("Hubo un error de comunicacion");
                }
            });
        }
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
            // $(".admin").show(0);
            break;

            default:
            // $(".admin").hide(0);
            break;
        }
    }

    //Funcion para llevar Pedido o detalles a mesa elegida
    //Actualizada para detectar si el pedido ya acabo
    //Gino Lluen - 2019
    function concreta_cambio_mesa($pkSalon, $pkMesa, $estado) {
        $('#ModalChangeMesa').modal('hide');
        $("#modal_envio_anim").modal("show");

        var parcial = 0;
        var row = $('#tabla_pedidos').DataTable().rows('.selected').data().toArray()[0];
        if (row) {
            parcial = 1;
        }

        var detalles = 0;
        if(parcial === 1){
            detalles = JSON.stringify($('#tabla_pedidos').DataTable().rows('.selected').data().toArray());
        }

        var param = {'mesaAnterior':pk_mesa_cookie, 'pkPedido':$('#txtCombrobante').val(), 'mesaActual':$pkMesa, 'estado':$estado, 'array': detalles, 'pkSalon': $pkSalon};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ChangeMesa",
            type: 'POST',
            data: param,
            cache: false,
            dataType: "json",
            beforeSend: function (xhr) {
                xhr.overrideMimeType("text/plain; charset=x-user-defined");
            },
            success: function(mdata) {
                if(parseInt(mdata.exito) === 1){
                    if(parcial === 0){
                        window.location.href="<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ShowPedidos2&pkMesa="+mdata.destino;
                    }else{
                        $('#tabla_pedidos').DataTable().ajax.reload(function(){
                            sumaTotal();
                            inicializa_contadores();
                            var total_nuevo = parseFloat($("#lblTotal2019").html());
                            if(total_nuevo === 0){
                                window.location.href="<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ShowPedidos2&pkMesa="+mdata.destino;
                            }else{
                                window.location.reload();
                            }
                        },true);            
                    }
                }else{
                    alert("¡Este pedido ya finalizo, no se puede cambiar de mesa!");
                    window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                }
            },
            error: function () {
                $("#modal_envio_anim").modal("hide");
                alert("Hubo un error de comunicacion");
            }
        });
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
        if(monto <= 0){
            continuar = 0;
            mensaje_error = "Ingresa un monto valido mayor a 0";
        }
        //Verificamos si se quiere ingresar un monto mayor al restante (cuando no es efectivo)
        if((monto>saldo) && (parseInt(id_medio_m) > 1)){
            //alert("Monto: "+monto+" - Saldo:"+saldo);
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

    function agregar_propina(){
        var trabajador = $("#trabajador_propina_pago").val();
        var valor_medio = $("#medio_propina_pago").val();
        var nombre_medio = $("#medio_propina_pago option:selected").text();
        var simbolo_moneda = $("#moneda_propina_pago option:selected").text();
        var monto = $("#monto_propina_pago").val();

        var propiedades_medio = valor_medio.split("|");
        var id_medio_m = propiedades_medio[0];
        var id_moneda_m = $("#moneda_propina_pago").val();

        if(parseFloat(monto)>0){
            tabla_pagos.row.add( [
                'PROPINA',
                id_medio_m,
                id_moneda_m,
                trabajador,
                '',
                nombre_medio,
                simbolo_moneda,
                monto,
                '<span style="color:red;" class="glyphicon glyphicon-trash eliminar" aria-hidden="true"></span>'
            ] ).draw( false );

            $("#trabajador_propina_pago").val($("#trabajador_propina_pago option:first").val());
            $('#trabajador_propina_pago').trigger('change');
            $("#medio_propina_pago").val($("#medio_propina_pago option:first").val());
            $("#moneda_propina_pago").val($("#moneda_propina_pago option:first").val());
            $("#moneda_propina_pago").removeAttr("disabled");
            $("#monto_propina_pago").val(0);      
        }
    }

    function actualiza_propina(accion){
        var trabajador = $("#trabajador_propina_pago").val();
        var medio = $("#medio_propina_pago").val();
        var moneda = $("#moneda_propina_pago").val();
        var monto = $("#monto_propina_pago").val();
        
        var propiedades_medio = medio.split("|");
        var id_medio_m = propiedades_medio[0];
        var id_moneda_m = propiedades_medio[1];
        
        switch(accion){
            case 2:
                //Validamos cuando se mueve el selector de medio
                switch(parseInt(id_medio_m)){
                    case 1:
                        $("#moneda_propina_pago").val(1);
                        $("#moneda_propina_pago").removeAttr("disabled");
                    break;
                    
                    default:
                        $("#moneda_propina_pago").attr("disabled", "true");					
                        $("#moneda_propina_pago").val(id_moneda_m);
                    break;
                }
            break;
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
                        $("#operacion_pago").removeAttr("disabled");				
                        $("#moneda_pago").val(id_moneda_m);
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
                        //alert(descuento_calculo+" > "+max_pre);
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
        for (var i = 0; i < $('#tabla_pedidos').DataTable().rows().data().toArray().length; i++) {
            if(($('#tabla_pedidos').DataTable().rows().data().toArray()[i].Destado).search("Solicitar") > -1) {
                return alert('Tiene pedidos sin enviar aun, envie todo el pedido antes de poder cobrar por favor.');
            }
        }
        $('#check_detraccion').prop('checked', false)
        $('#totalDetraccion').html(0)
        $('#check_detraccion').show(0)
        var continuar = 1;
        var parcial = 0;
        var row = $('#tabla_pedidos').DataTable().rows('.selected').data().toArray()[0];
        if (row) {
            if (confirm('SE PAGARAN SOLO LOS ITEMS SELECCIONADOS, ¿Continuar?')) {
                parcial = 1;
                continuar = 1;
            } else {
                continuar = 0;
            }
        }

        if(continuar === 1){
            tipo_pago_final = tipo_pago;
            switch(parseInt(tipo_pago)){
                case 0:
                $("#datos_cliente_pago").hide(0);
                $("#table_detraccion").hide(0);
                $("#titulo_modal_multiple").html("Generando Ticket");
                break;

                case 1:
                $("#datos_cliente_pago").show(0);
                $("#table_detraccion").show(0);
                $("#titulo_modal_multiple").html("Generando Boleta");
                break;

                case 2:
                $("#datos_cliente_pago").show(0);
                $("#table_detraccion").show(0);
                $("#titulo_modal_multiple").html("Generando Factura");
                break;
            }

            if(parcial === 0){            
                var total_actual = parseFloat($("#lblTotal2019").html());
                var total_nuevo = 0;
                if(verificacion == 1){
                    $('#modal_envio_anim').modal('show');
                    $('#tabla_pedidos').DataTable().ajax.reload(function(){
                        sumaTotal();
                        inicializa_contadores();
                        total_nuevo = parseFloat($("#lblTotal2019").html());
                        total_actual = my_round(total_actual,2);
                        total_nuevo = my_round(total_nuevo,2);
                        if(total_actual !== total_nuevo){
                            $('#modal_envio_anim').modal('hide');
                            alert("El pedido en pantalla es distinto del obtenido del servidor. ¡Verifique antes de pagar!")
                        }else{                          
                            total_venta = parseFloat($("#lblTotal2019").html());
                            total_venta = my_round(total_venta,2);
                            total = parseFloat($("#lblTotal2019").html());
                            total = my_round(total,2);
                            por_pagar = parseFloat($("#lblTotal2019").html());
                            por_pagar = my_round(por_pagar,2);
                            $("#monto_modal_multiple").html("Total Venta: "+monedas[0]["simbolo"]+$("#lblTotal2019").html());
                            $("#monto_descuento_pago").attr("max",(total*0.99).toFixed(2));

                            actualiza_por_pagar();
                            actualiza_vuelto();
                            $('#modal_envio_anim').modal('hide');
                            $("#modal_multiple_pago").modal("show");
                        }
                    },true);
                }else{
                    total_venta = parseFloat($("#lblTotal2019").html());
                    total_venta = my_round(total_venta,2);
                    total = parseFloat($("#lblTotal2019").html());
                    total = my_round(total,2);
                    por_pagar = parseFloat($("#lblTotal2019").html());
                    por_pagar = my_round(por_pagar,2);
                    $("#monto_modal_multiple").html("Total Venta: "+monedas[0]["simbolo"]+$("#lblTotal2019").html());
                    $("#monto_descuento_pago").attr("max",(total*0.99).toFixed(2));

                    actualiza_por_pagar();
                    actualiza_vuelto();
                    $("#modal_multiple_pago").modal("show");
                }
            }else{
                var soles = 0;
                for (var i = 0; i < $('#tabla_pedidos').DataTable().rows('.selected').data().toArray().length; i++) {
                    soles = soles + parseFloat($('#tabla_pedidos').DataTable().rows('.selected').data().toArray()[i].importe);
                }

                total_venta = parseFloat(soles);
                total_venta = my_round(total_venta,2);
                total = parseFloat(soles);
                total = my_round(total,2);
                por_pagar = parseFloat(soles);
                por_pagar = my_round(por_pagar,2);
                $("#monto_descuento_pago").attr("max",(total*0.99).toFixed(2));
                $("#monto_modal_multiple").html("Total Venta: "+monedas[0]["simbolo"]+soles);

                actualiza_por_pagar();
                actualiza_vuelto();
                $("#modal_multiple_pago").modal("show");
            }
        }
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
        $("#monto_pago").attr("max",my_round(por_pagar,2));
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

    var pageInicial = 0;
    var sizeInicial = 12;
    
    function CargarPrincipal() {
        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
    }
    
    var tipo_sin_pago = 0;
    //Funciones para cerrar la mesa sin pagos
    function sin_pago(tipo_){
        for (var i = 0; i < $('#tabla_pedidos').DataTable().rows().data().toArray().length; i++) {
            if(($('#tabla_pedidos').DataTable().rows().data().toArray()[i].Destado).search("Solicitar") > -1) {
                return alert('Tiene pedidos sin enviar aun, envie todo el pedido antes de poder cobrar por favor.');
            }
        }
        $('#modal_envio_anim').modal('show');
        var total_actual = parseFloat($("#lblTotal2019").html());
        var total_nuevo = 0;
        $('#tabla_pedidos').DataTable().ajax.reload(function(){
            sumaTotal();
            inicializa_contadores();
            total_nuevo = parseFloat($("#lblTotal2019").html());
            if(total_actual !== total_nuevo){
                $('#modal_envio_anim').modal('hide');
                alert("El pedido en pantalla es distinto del obtenido del servidor. ¡Verifique antes de cerrar el pedido!")
            }else{
                tipo_sin_pago = tipo_;
                if(parseInt(tipo_) === 1){
                    $("#titulo_sin_pago").html("Cancelando a Credito");
                }else{
                    $("#titulo_sin_pago").html("Cancelando por Consumo");
                }
                $('#modal_envio_anim').modal('hide');
                $("#modal_sin_pago").modal("show");
            }
        },true);
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
                var param = {'pkPedido': $('#txtCombrobante').val(), 'terminal': $('#terminal').val()};
                $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=EnviaPedido",
                type: 'POST',
                data: param,
                cache: false,
                dataType: "json",
                beforeSend: function (xhr) {
                    xhr.overrideMimeType("text/plain; charset=x-user-defined");
                },
                success: function(data0) {
                    if(parseInt(data0.exito) === 1){
                        //Luego generamos el comprobante
                        var param1 = {'pkPedido': $('#txtCombrobante').val(), documento: $('#documento_sin_pago').val(),
                        tipo_cliente: tipo_, valor1: $('#cliente_sin_pago').val(), valor2: $('#direccion_sin_pago').val(), comentario: $('#comentario_sin_pago').val(),total: parseFloat($("#lblTotal2019").html())};
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
    

    function openMesa($mesa) {
        
    }
    
    /**
     * Mostrar la alerta para agregar productos
     * 
     *  */
    function mostraralerta($precio, $id, $tipoPedido,$nombre) {
        $("#txtPkPedido").val($id);
        $("#txtCantidadSend").val(1);
        $("#txtPrecioSend").val($precio);
        $("#txtTipoSend").val($tipoPedido);
        $("#d_nombre_plato").html($nombre);
        $("#d_precio_plato").html(simbolo_nacional+$precio);
        $('#ModalCantidadPedir').modal('show');
        contador = 0;
    }

    function cancela_alerta(){
        contador = 0;
        $("#txtCantidadSend").val(1);
        $("#txt_mensaje_send").val("");
        $('#ModalCantidadPedir').modal('hide');
    }
    
    /**
     * Cargar Los Items Segun el tipo de plato
     * */

    function loadItemsTipoPlato() {
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Tipos&&action=List",
            type: 'POST',
            dataType: 'json',
            success: function (data1) {

                $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=TiposMenus',
                    function (data) {

                        // $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListProductAccesoRapido',
                        //     function (data2) {
                                
                                $div = $('#contenTipoPlato');
                        
                                // if (data2.length > 0) {
                                    $('<button id="btnAccesoRapido" hfre="#" onclick="mostrar_acceso_rapido()" class="usqay-btn btn btn-lg">ACCÉSO RÁPIDO</button>').appendTo($div);
                                // }

                                if (data.length > 0) {
                                    $('<button id="btnMenus" hfre="#" onclick="mostrar_menus()" class="usqay-btn btn btn-lg">MENUS DEL DÍA</button>').appendTo($div);
                                }
                                
                                for (var i = 0; i < data1.length; i++) {
                                    $('<button id="btnTipo' + data1[i].pkTipoPlato + '" href="#" onclick="buscaTipo('+data1[i].pkTipoPlato+')" class="usqay-btn btn btn-lg">'+data1[i].descripcion+'</button>').appendTo($div);
                                }   

                                //mostrar_acceso_rapido();

                        //     }, 'json'
                        // );
                    }, 'json'
                );
            }
        });
    }
    
    function buscaTipo(pkTipo){
        $("#contenTipoPlato button").removeClass("usqay-btn-red");
        $("#btnTipo"+pkTipo).addClass("usqay-btn-red");
        $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListAllProduct&tipo=' + pkTipo,
            function (data) {
                if(app === 1){
                    $("#contenedor_tipos").hide(0);
                    $("#contenedor_platos").show(0);
                }else{
                    $('html, body').animate({
                        scrollTop: $("#divProductos").offset().top
                    }, 500);
                }
                var div = $('#divProductos');
                div.empty();
                if(app === 1){
                    $('<button id="btnPlato" href="#" onclick="regresarTipos()" class="usqay-btn btn btn-lg"><b><span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span></b></button>').appendTo(div);
                }
                for (var i = 0; i < data.length; i++) {
                    if(parseInt(data[i].stock)<0){
                        $('<button id="btnPlato' + data[i].id + '" href="#" onclick="mostraralerta(' + data[i].precio + ',\'' + data[i].id + '\',\'' + data[i].tpedido + '\',\''+data[i].descripcion+'\')" class="usqay-btn btn btn-lg">'+data[i].descripcion+'<br/><b>'+simbolo_nacional+data[i].precio+'</b></button>').appendTo(div);
                    }else{
                        $('<button id="btnPlato' + data[i].id + '" href="#" onclick="mostraralerta(' + data[i].precio + ',\'' + data[i].id + '\',\'' + data[i].tpedido + '\',\''+data[i].descripcion+'\')" class="usqay-btn btn btn-lg">'+data[i].descripcion+'<br/><b>'+simbolo_nacional+data[i].precio+ '</b><br/><span style="color:red !important;"><strong>STOCK:'+data[i].stock+'</span></button>').appendTo(div);
                    }
                }

            }, 'json'
        );
    }

    function regresarTipos(){
        $("#contenedor_platos").hide(0);
        $("#contenedor_tipos").show(0);
    }
    
    function mostrar_acceso_rapido() {
        $("#contenTipoPlato button").removeClass("usqay-btn-red");
        $("#btnAccesoRapido").addClass("usqay-btn-red");
        $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListProductAccesoRapido',
            function (data) {
                if(app === 1){
                    $("#contenedor_tipos").hide(0);
                    $("#contenedor_platos").show(0);
                }else{
                    $('html, body').animate({
                        scrollTop: $("#divProductos").offset().top
                    }, 500);
                }
                var div = $('#divProductos');
                div.empty();
                if(app === 1){
                    $('<button id="btnPlato" href="#" onclick="regresarTipos()" class="usqay-btn btn btn-lg"><b><span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span></b></button>').appendTo(div);
                }
                for (var i = 0; i < data.length; i++) {
                    if(parseInt(data[i].stock)<0){
                        $('<button id="btnPlato' + data[i].id + '" href="#" onclick="mostraralerta(' + data[i].precio + ',\'' + data[i].id + '\',\'' + data[i].tpedido + '\',\''+data[i].descripcion+'\')" class="usqay-btn btn btn-lg">'+data[i].descripcion+'<br/><b>'+simbolo_nacional+data[i].precio+'</b></button>').appendTo(div);
                    }else{
                        $('<button id="btnPlato' + data[i].id + '" href="#" onclick="mostraralerta(' + data[i].precio + ',\'' + data[i].id + '\',\'' + data[i].tpedido + '\',\''+data[i].descripcion+'\')" class="usqay-btn btn btn-lg">'+data[i].descripcion+'<br/><b>'+simbolo_nacional+data[i].precio+ '</b><br/><span style="color:red !important;"><strong>STOCK:'+data[i].stock+'</span></button>').appendTo(div);
                    }
                }

            }, 'json'
        );
    }
    
    function mostrar_menus() {
        $("#contenTipoPlato button").removeClass("usqay-btn-red");
        $("#btnMenus").addClass("usqay-btn-red");
        $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=TiposMenus',
            function (data) {
                if(app === 1){
                    $("#contenedor_tipos").hide(0);
                    $("#contenedor_platos").show(0);
                }else{
                    $('html, body').animate({
                        scrollTop: $("#divProductos").offset().top
                    }, 500);
                }
                var div = $('#divProductos');
                div.empty();
                if(app === 1){
                    $('<button id="btnPlato" href="#" onclick="regresarTipos()" class="usqay-btn btn btn-lg"><b><span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span></b></button>').appendTo(div);
                }
                for (var i = 0; i < data.length; i++) {
                    $('<button id="btnMenu' + data[i].id + '" hfre="#" onclick="modal_menu('+ data[i].id +')" class="usqay-btn btn btn-lg">' + data[i].nombre + '<br/><strong>'+simbolo_nacional+data[i].precio+'</strong></button>').appendTo(div);
                }

            }, 'json'
        );
    }
    
     var menus = {};
    
    function modal_menu(idTipo){
        menus = {};
    
        $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ComponentesPorTipo&idt='+idTipo,
            function (data) {
            var body = "";
            $("#componente").html("");
            var it = 1;
            $.each(data, function (key, value) {
                body += "<tr>";
                body += "<td><b>"+value.nombre+"</b></td><td class='row'>";
                $.each(value.platos, function (key, value1) {
                    body += '<div class="btn btn-success col-xs-12 col-sm-12 col-md-12" style="margin:3px;">';
                    body += '<b>' + value1.descripcion + '</b><br/>';
                    body += simbolo_nacional+value1.precio +'<br/>';
                    body += '<span style="color:red;font-weight:bold;">Quedan: ' + value1.stock +'</span><br/>';
                    body += '<center>';
                    body += '<div class="input-group">';
                    body += '<span class="input-group-addon" onclick="res_plato(\''+value1.pkPlato+'\',\''+value1.precio+'\','+it+','+value1.id+')">-</span>';
                    body += '<input id="cant'+value1.pkPlato+'" type="number" class="form-control" value="0" min="0" max="'+value1.stock+'" style="text-align: center;" readonly>';
                    body += '<span class="input-group-addon" onclick="sum_plato(\''+value1.pkPlato+'\',\''+value1.precio+'\','+it+','+value1.stock+','+value1.id+')">+</span>';
                    body += '</div>';
                    body += '</center>';
                    body += '</div>';
                });
                body += "</td><td>";
                body += "<center><b>Total "+value.nombre+"</b></center>";
                body += "<center><b>"+simbolo_nacional+"<span id='tt"+it+"'>0.00</span></b></center>";
                body += "</td>";
                body += "</tr>";
                it = it + 1;
            });
            $("#componente").html(body);
            }, 'json');
        $("#modal_contenido_menu").modal("show");
    }
    
    function sum_plato(pk_plato,precio,postipo,stock,id_componente){
        var cantidad_actual = $("#cant"+pk_plato).val();
        var nueva_c = parseInt(cantidad_actual) + 1;
        
        if(nueva_c <= parseInt(stock)){
            var tmp = {};
            tmp["cantidad"] = nueva_c;
            tmp["precio"] = precio;
            tmp["id_componente"] = id_componente;
            menus[pk_plato] = tmp;
            $("#cant"+pk_plato).val(nueva_c);

            var total_menu = $("#total_menu").html();
            var total_actual = $("#tt"+postipo).html();
            var nuevo_t = parseFloat(total_actual) + (parseFloat(precio));
            var nuevo_tm = parseFloat(total_menu) + (parseFloat(precio));
            $("#tt"+postipo).html(nuevo_t);
            $("#total_menu").html(nuevo_tm);
        }
    }
    
    function res_plato(pk_plato,precio,postipo,id_componente){
        var cantidad_actual = $("#cant"+pk_plato).val();
        var nueva_c = parseInt(cantidad_actual) - 1;
        
        if(nueva_c >= 0){
            var tmp = {};
            tmp["cantidad"] = nueva_c;
            tmp["precio"] = precio;
            tmp["id_componente"] = id_componente;
            menus[pk_plato] = tmp;
            $("#cant"+pk_plato).val(nueva_c);

            var total_menu = $("#total_menu").html();
            var total_actual = $("#tt"+postipo).html();
            var nuevo_t = parseFloat(total_actual) - (parseFloat(precio));
            var nuevo_tm = parseFloat(total_menu) - (parseFloat(precio));
            $("#tt"+postipo).html(nuevo_t);
            $("#total_menu").html(nuevo_tm);
        }
    }
    
    function agrega_menu(){
        if(enviando === 0){
            $("#modal_contenido_menu").modal("hide");
            /*if(verificacion == 1){
                $('#modal_envio_anim').modal('show');
            }*/
            //Para descartar error de red en esta accion siempre mostramos modal envio
            $('#modal_envio_anim').modal('show');
            enviando = 1;            
            var param = {platos: JSON.stringify(menus),pkComprobante: $('#txtCombrobante').val(),tipo: 'pkPlato'};
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=AddMenu",
                type: 'POST',
                data: param,
                cache: false,
                dataType: "json",
                beforeSend: function (xhr) {
                    xhr.overrideMimeType("text/plain; charset=x-user-defined");
                },
                success: function (mdata) {
                    if(parseInt(mdata.exito) === 1){
                        enviando = 0;
                        loadDetalles();
                        $("#total_menu").html("0.00");
                        $("#modal_envio_anim").modal("hide");
                    }else{
                        $("#modal_envio_anim").modal("hide");
                        enviando = 0;
                        alert("¡No puedes agregar mas items, este pedido ya terminó!");
                        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                    }                  
                },
                error: function () {
                    enviando = 0;
                    alert("Hubo un error de comunicacion");
                    $("#modal_envio_anim").modal("hide");
                    $("#modal_contenido_menu").modal("show");  
                }
            });
        }   
    }
    
    /**
     * Mostrar el los datos segun el tipo de pedido
     * 
     * */
    var urlTipo = "";
    function doSearchTipo($tipo, $page, $size) {
        urlTipo = $tipo;
        $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListAllProduct&tipo=' + $tipo + '&page=' + $page + '&size=' + $size,
            function (data) {
                var div = $('#divProductos');
                div.empty();
                for (var i = 0; i < data.length; i++) {
                    if(parseInt(data[i].stock)<0){
                        $('<button class="tipos btn btn-categoria btn-group-lg " onclick="mostraralerta(' + data[i].precio + ',\'' + data[i].id + '\',\'' + data[i].tpedido + '\',\''+data[i].descripcion+'\')"><small> <br>' + data[i].descripcion + '<br><strong>'+simbolo_nacional+data[i].precio + '</strong></small></button>').appendTo(div);
                    }else{
                        $('<button class="tipos btn btn-categoria btn-group-lg " onclick="mostraralerta(' + data[i].precio + ',\'' + data[i].id + '\',\'' + data[i].tpedido + '\',\''+data[i].descripcion+'\')"><small> <br>' + data[i].descripcion + '<br><strong>'+simbolo_nacional+data[i].precio + '</strong></small><br><small style="color:red !important;"><strong>STOCK:'+data[i].stock+'</strong></small></button>').appendTo(div);
                    }
                }

            }, 'json'
        );
    }
    
    function doBuscarMenu($decripcion) {
        $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListProductDescripcion&descripcion='+$decripcion ,
                function (data) {
                    if(app === 1){
                        $("#contenedor_tipos").hide(0);
                        $("#contenedor_platos").show(0);
                    }else{
                        $('html, body').animate({
                            scrollTop: $("#divProductos").offset().top
                        }, 500);
                    }
                    var div = $('#divProductos');
                    div.empty();
                    if(app === 1){
                        $('<button id="btnPlato" href="#" onclick="regresarTipos()" class="usqay-btn btn btn-lg"><b><span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span></b></button>').appendTo(div);
                    }
                    for (var i = 0; i < data.length; i++) {
                        if(parseInt(data[i].stock)<0){
                            $('<button id="btnPlato' + data[i].id + '" href="#" onclick="mostraralerta(' + data[i].precio + ',\'' + data[i].id + '\',\'' + data[i].tpedido + '\',\''+data[i].descripcion+'\')" class="usqay-btn btn btn-lg">'+data[i].descripcion+'<br/><b>'+simbolo_nacional+data[i].precio + '</b></button>').appendTo(div);
                        }else{
                            $('<button id="btnPlato' + data[i].id + '" href="#" onclick="mostraralerta(' + data[i].precio + ',\'' + data[i].id + '\',\'' + data[i].tpedido + '\',\''+data[i].descripcion+'\')" class="usqay-btn btn btn-lg">'+data[i].descripcion+'<br/><b>'+simbolo_nacional+data[i].precio + '</b><br/><span style="color:red !important;"><strong>STOCK:'+data[i].stock+'</span></button>').appendTo(div);
                        }
                    }

                }, 'json'
                );
    }
    
    /**
     * Funcion para guradar un mensaje a un pedido
     * */
    function saveMessagePedido($mensaje) {
        var param = {pkPedido: $("#txtIdPedido").val(),
            message: $mensaje};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=SaveMessagePedido",
            type: 'POST',
            data: param,
            success: function (data) {
                loadDetalles();
                $('#modalMensaje').modal('hide');
                $('#textAreaMessage').val("");
            }

        });
    }
    
    function addMessagePedido($mensaje) {
        if($("#textAreaMessage").val() === ""){
            $('#textAreaMessage').val($mensaje);
        }else{
            $('#textAreaMessage').val($("#textAreaMessage").val()+"+"+$mensaje);
        }
    }

    function limpiar_mensaje_s(){
        $('#txt_mensaje_send').val("");
    }

    function limpiar_mensaje(){
        $('#textAreaMessage').val("");
    }

    function addMessagePedidoS($mensaje) {
        if($("#txt_mensaje_send").val() === ""){
            $('#txt_mensaje_send').val($mensaje);
        }else{
            $('#txt_mensaje_send').val($("#txt_mensaje_send").val()+"+"+$mensaje);
        }
    }
    
    function saveMessagePedidoCualquiera($mensaje) {
        var param = {pkPedido: $("#txtIdPedido").val(),
            message: $("#textAreaMessage").val()};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=SaveMessagePedido",
            type: 'POST',
            data: param,
            success: function (data) {
                loadDetalles();
                $('#modalMensaje').dialog('close');
            }

        });
    }

    //Modificado Agosto 2019 - Gino Lluen
    //Ya no podemos agregar detalles a un pedido finalizado   
    function addPedido() {
        $('#ModalCantidadPedir').modal('hide');
        if(enviando === 0){
            if(parseInt($('#txtCantidadSend').val()) > 0){
                /*if(verificacion == 1){
                    $('#modal_envio_anim').modal('show');
                }*/
                //Para descartar error de red en esta accion siempre mostramos modal envio
                $('#modal_envio_anim').modal('show');
                enviando = 1;            
                var param = {
                    cantidad: $('#txtCantidadSend').val(),
                    precio_venta: $("#txtPrecioSend").val(),
                    fkPedido: $('#txtPkPedido').val(),
                    pkComprobante: $('#txtCombrobante').val(),
                    tipo: $("#txtTipoSend").val(),
                    mensaje : $("#txt_mensaje_send").val()
                };
                $.ajax({
                    url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=AddPedido",
                    type: 'POST',
                    data: param,
                    cache: false,
                    dataType: "json",
                    beforeSend: function (xhr) {
                        xhr.overrideMimeType("text/plain; charset=x-user-defined");
                    },
                    success: function (mdata) {
                        if(parseInt(mdata.exito) === 1){
                            contador = 0;
                            $("#txtCantidadSend").val(1);
                            $("#txt_mensaje_send").val("");
                            $("#modal_envio_anim").modal("hide");
                            enviando = 0;
                            loadDetalles();
                            buscaTipo(mdata.pktipo);
                        }else{
                            $("#modal_envio_anim").modal("hide");
                            enviando = 0;
                            alert("¡No puedes agregar mas items, este pedido ya terminó!");
                            window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                        }                  
                    },
                    error: function () {
                        enviando = 0;
                        alert("Hubo un error de comunicacion");                    
                        $("#modal_envio_anim").modal("hide");
                        $('#ModalCantidadPedir').modal('show');
                    }

                });
            }
        }
    }
    
    function updateNPersonas() {
       if ($('#txtnPesonas').val()>15){
alert("No puede colocar esta cantidad de personas en una mesa");
       }else{ 
        var param = {npersonas: $('#txtnPesonas').val(),
            pkComprobante: $('#txtCombrobante').val()
        };
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=UpdateNPersonas",
            type: 'POST',
            data: param,
            success: function (data) {
                //Ni Madres
            }

        });
         }
    }

    function sumaTotal() {
        var items = 0;
        var soles = 0;
        for (var i = 0; i < $('#tabla_pedidos').DataTable().rows().data().toArray().length; i++) {

            if(($('#tabla_pedidos').DataTable().rows().data().toArray()[i].Destado).search("Anulado") === -1){
                 if(($('#tabla_pedidos').DataTable().rows().data().toArray()[i].Destado).search("Solicitar") === -1) {
                    items = items + parseFloat($('#tabla_pedidos').DataTable().rows().data().toArray()[i].cantidad);
                    soles = soles + parseFloat($('#tabla_pedidos').DataTable().rows().data().toArray()[i].importe);
                 }
            }       
        }
        items = my_round(items,2);
        soles = my_round(soles,2);
        $("#lblItems2019").html(items);
        $("#lblTotal2019").html(soles);
    }
    
    /**
     * Mostrar Mensaje para enviar un mensaje a cocina
     * 
     * */
    function showMessages() {
        var row = $('#tabla_pedidos').DataTable().rows('.selected').data().toArray()[0];
        if (row) {
            $("#textAreaMessage").val(row.mensaje);
            $('#modalMensaje').modal('show');
            $("#txtIdPedido").val(row.pkPedido);
        }else {
            $.messager.alert('Alerta', "Debe Seleccionar un Pedido", 'warning');
        }
    }
    
    function cambiaPlato(){
        var row = $('#tabla_pedidos').DataTable().rows('.selected').data().toArray()[0];
        if (row) {
            if((row.pkPedido).search("C") < 0){
                if((row.pedido).substr(0, 3) !== "(*)"){
                    if(row.estado == 1 || row.estado == 2){
                        $("#porg").html("Plato: "+row.pedido);
                        $("#corg").html("Cantidad: "+row.cantidad);
                        $("#morg").html("Precio: "+simbolo_nacional+row.precio);
                        $("#torg").html(row.importe);
                        $("#txtIdPedidoCP").val(row.pkPedido);
                        $("#tabla_cambio").html("");
                        $('#modalPlatoCambio').modal('show');
                    }
                }
            }
        }
    }
    
    function editaPrecio(){
        var row = $('#tabla_pedidos').DataTable().rows('.selected').data().toArray()[0];
        if (row) {
            if((row.pkPedido).search("C") < 0){
                var n_precio = prompt("Ingresa el nuevo precio para "+row.pedido, row.precio);
                if(parseFloat(n_precio)>=0){
                    var param = {pk_pedido: $('#txtCombrobante').val(), pk_detalle: row.pkPedido, precio_anterior: row.precio, precio_nuevo: n_precio, cantidad: row.cantidad};
                    $.ajax({
                        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=cambiaPrecio",
                        type: 'POST',
                        data: param,
                        success: function () {
                            loadDetalles();
                        }
                    });
                    
                }
            }
        }
    }
    
    function carga_items_cambio(){
        var total = 0;
        var id_detalle = $("#txtIdPedidoCP").val();
        var param = {pkDet: id_detalle};
        // $.ajax({
        // url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=getDetallesFE",
        // type: 'POST',
        // data: param,
        // dataType: 'json',
        // success: function (data) {
            $("#tabla_cambio").html("");
            var html_detalles = "";
            $.each( array_cambio_platos, function( key, value ) {
                value.total = value.preN * value.caN;
                html_detalles += "<tr><td>"+(key+1)+
                "</td><td>"+value.descripcion+"</td><td>"+
                value.preN+"</td><td>"+
                value.caN+"</td><td>"+
                (value.total)+"</td><td>"+
                "<a href='#' onclick='quita_item_cambio("+(key)+")'><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span></a></td></tr>";
        
                total = total + parseFloat(value.total);
            });
            $("#tabla_cambio").html(html_detalles);
            $("#tchan").html(total);
        // }
        // });
    }
    
    function quita_item_cambio(id_in){
        // var param = {pkCambio: id_in};
        // $.ajax({
        // url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=delDetalleFE",
        // type: 'POST',
        // data: param,
        // dataType: 'html',
        // success: function () {
            $("#modalPlatoCambio").animate({ scrollTop: 0 }, 0);
            array_cambio_platos.splice(id_in, 1)
            carga_items_cambio();
        // }
        // });
    }
    
    function termina_magia(){
        var id_detalle = $("#txtIdPedidoCP").val();
        var param = {pkDet: id_detalle};
        // $.ajax({
        // url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=cleanMagiaFE",
        // type: 'POST',
        // data: param,
        // dataType: 'html',
        // success: function () {
            array_cambio_platos = [];
            carga_items_cambio();
            $('#modalPlatoCambio').modal('hide');
        // }
        // });
    }
    
    function confirma_magia(){
        var total_original = $("#torg").html();
        var total_nuevo = $("#tchan").html();
        if(parseFloat(total_original) === parseFloat(total_nuevo)){

            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=saveCambioDePlato",
                type: 'POST',
                data: {platos: array_cambio_platos},
                dataType: 'json',
                success: function () {
                    termina_magia(); 
                    $("#tchan").html("0.00");
                    loadDetalles();
                    $('#modalPlatoCambio').modal('hide');
                }
            });

            
        }else{
            alert("¡El monto debe ser igual al pedido elegido!");
        }
    }

    var array_cambio_platos = [];
    
    function hacer_magia_cambio(){
        var total_original = $("#torg").html();
        var total_nuevo = $("#tchan").html();
        var precio = $("#pkch option:selected").text();
        precio = precio.split(simbolo_nacional);
        precio = precio[1];
        var nombre_plato = $("#pkch option:selected").text().split(simbolo_nacional)[0];
        var cantidad = $("#cach").val();
        var total_parcial = parseFloat(precio)*parseFloat(cantidad);

        if(parseFloat(total_original) >= (parseFloat(total_nuevo)+total_parcial)){
            var param = {pkDet: $("#txtIdPedidoCP").val(), pkpN: $("#pkch option:selected").val(), caN: cantidad,pkP: $("#txtCombrobante").val(),preN: precio, agrupar:$('#agrch').is(":checked")};
            
            param.descripcion = nombre_plato;

            if (param.agrupar) {
                array_cambio_platos.push(param);
            } else {
                let cantidad_temp = param.caN;
                for (let i=0; i<cantidad_temp;i++) {
                    param.caN = 1;
                    array_cambio_platos.push(param);
                }
            }

            $("#cach").val("1");
            $("#modalPlatoCambio").animate({ scrollTop: 0 }, 0);
            carga_items_cambio();

            // $.ajax({
            // url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=magiaDetalleFE",
            // type: 'POST',
            // data: param,
            // dataType: 'html',
            // success: function () {
            //     $("#cach").val("1");
            //     $("#modalPlatoCambio").animate({ scrollTop: 0 }, 0);
            //     carga_items_cambio();
            // }
            // });
        }else{
            alert("¡El total no puede exceder el original!");
        }    
    }

    function openModalChangeMesa($mesa) {
        $('#ModalChangeMesa').modal('show');
        $('#lblTitleCambioMesa').html("Cambiando de mesa");
        $('#contenBodyMesas').empty();
        $('#contenBodyMesas').html('content');
        $("#contenBodyMesas").load('<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ShowMesas&mesaAnterior=' + $mesa + '&pkPedido=' + $('#txtCombrobante').val());
    }

    //Funcion para liberar mesa
    //Modificada Agosto 2019 - Gino Lluen
    //Ahora verificamos si el pedido no ha cambiado
    //Si el pedido ha cambiado no liberamos y prevenimos perdida de pedidos por descoordinacion
    //Tambien verificamos si el pedido sigue abierto si liberamos un pedido cerrado se crea descuadre en caja
    function confirmCancelaMesa() {
        $('#modal_envio_anim').modal('show');
        var total_actual = parseFloat($("#lblTotal2019").html());
        var total_nuevo = 0;
        $('#tabla_pedidos').DataTable().ajax.reload(function(){
            sumaTotal();
            inicializa_contadores();
            total_nuevo = parseFloat($("#lblTotal2019").html());
            if(total_actual !== total_nuevo){
                $('#modal_envio_anim').modal('hide');
                alert("El pedido en pantalla es distinto del obtenido del servidor. ¡Verifique antes de liberar!")
            }else{
                $('#modal_envio_anim').modal('hide');
                //Si el pedido no ha cambiado, preguntamos si se debe liberar
                // var r = confirm("¿Desea Liberar esta mesa?. Esta acción no se puede deshacer y los pedidos registrados se anularán");
                // if (r == true) {
                    // var razon = prompt("Describa el motivo:", "");
                    var razon = $('#usqay-input-razon-anulacion').val();

                    $('#modal_envio_anim').modal('show');
                    //Liberamos mesa
                    var param = {'pkPedido': $('#txtCombrobante').val(),'terminal': $('#terminal').val(), 'pkMesa': pk_mesa_cookie, 'razon': razon};
                    $.ajax({
                        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=CancelaMesa",
                        type: 'POST',
                        data: param,
                        cache: false,
                        dataType: "json",
                        beforeSend: function (xhr) {
                            xhr.overrideMimeType("text/plain; charset=x-user-defined");
                        },
                        success: function (mdata) {
                            if(parseInt(mdata.exito) === 1){
                                window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                            }else{
                                alert("¡Este pedido ya finalizo, no se puede liberar!");
                                window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                            } 
                        },
                        error: function () {
                            $("#modal_envio_anim").modal("hide");
                            alert("Hubo un error de comunicacion");
                        }
                    });
                // }
            }
        },true);
    }

    //Funciones para el timeout si es dispositivo movil
    function muestra_bloqueo(){
        /*$('.modal').modal('hide');
        $("#modal_idle").modal("show");*/
        //Parche rapido para la kermes
        location.reload();
    }
