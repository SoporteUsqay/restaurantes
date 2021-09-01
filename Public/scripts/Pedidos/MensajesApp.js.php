<?php require_once '../../../Components/Config.inc.php'; ?>
//<script>
    function nobackbutton() {

        window.location.hash = "no-back-button";
        window.location.hash = "Again-No-back-button" //chrome
        window.onhashchange = function () {
            window.location.hash = "no-back-button";
        }
    }

    var pageInicial = 0;
    var sizeInicial = 12;
    function CargarPrincipal() {
        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
    }
    
    function topFunction() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
    }
    
    function regresacancelaPedidoCredito() {
        topFunction();
        $("#panelOperativo").show(0);
        $("#panelPagoCredito").hide(0);  
    }
    
    function cancelaPedidoCredito() {
        //Primero enviamos pedidos pendientes
        if($('#txtDocumentoPC').val() != "" && $('#txtValor1PC').val() != "" && $('#txtValor2PC').val() != ""){
            if(enviando === 0){
                enviando = 1;
                $('#modal_envio_anim').modal('show');
                var param = {'array': JSON.stringify(array_table_envio()), 'terminal': $('#terminal').val()};
                $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=EnviaPedido",
                    type: 'POST',
                    data: param,
                    success: function() {
                        //Luego generamos el comprobante
                        var param1 = {'pkPedido': $('#txtCombrobante').val(), documento: $('#txtDocumentoPC').val(),
                        tipo_cliente: $('#cmbTipoClienteCredito').val(), valor1: $('#txtValor1PC').val(), valor2: $('#txtValor2PC').val(),
                        total: calculaTotal()};
                        $.ajax({
                        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=CancelaPedidoCredito",
                        type: 'POST',
                        data: param1,
                        success: function () {              
                            //Finalmente ponemos en cola la impresion
                            var param2 = {'pkPedido': $("#txtCombrobante").val(), 'terminal': $('#terminal').val(), 'tipo': 'CREDITO', 'aux': '<?php echo UserLogin::get_id();?>'};
                            $.ajax({
                                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ImprimeCuenta",
                                    type: 'POST',
                                    data: param2,
                                    success: function() {
                                        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                                    }
                            });          
                        }
                        });
                    }
                });
            }
        }else{
            alert("Debes llenar todos los datos");
        }
    }
    
    function regresacancelaPedidoCACuenta() {
        topFunction();
        $("#panelOperativo").show(0);
        $("#panelPagoConsumo").hide(0);  
    }
    
    function cancelaPedidoCACuenta() {
        if($('#txtDocumentoPCuenta').val() != "" && $('#txtValor1PCuenta').val() != "" && $('#txtValor2PCuenta').val() != ""){
            //Primero enviamos pedidos pendientes
            if(enviando === 0){
                enviando = 1;
                $('#modal_envio_anim').modal('show');
                var param = {'array': JSON.stringify(array_table_envio()), 'terminal': $('#terminal').val()};
                $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=EnviaPedido",
                    type: 'POST',
                    data: param,
                    success: function() {
                        //Luego generamos el comprobante
                        var param1 = {'pkPedido': $('#txtCombrobante').val(), documento: $('#txtDocumentoPCuenta').val(),tipo_cliente: $('#cmbTipoClienteCuenta').val(), valor1: $('#txtValor1PCuenta').val(), valor2: $('#txtValor2PCuenta').val(), total: calculaTotal()};
                        $.ajax({
                        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=CancelaPedidoACuenta",
                        type: 'POST',
                        data: param1,
                        success: function () {              
                            //Finalmente ponemos en cola la impresion
                            var param2 = {'pkPedido': $("#txtCombrobante").val(), 'terminal': $('#terminal').val(), 'tipo': 'CONSUMO', 'aux': '<?php echo UserLogin::get_id();?>'};
                            $.ajax({
                                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ImprimeCuenta",
                                    type: 'POST',
                                    data: param2,
                                    success: function() {
                                        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                                    }
                            });          
                        }
                        });
                    }
                });
            }
        }else{
            alert("Debes llenar todos los datos");
        }
    }
    
    function openMesa($mesa) {
        loadPedido($mesa);
        $("#txtMesaApertura").val($mesa);
        visualizaTipoUsuario();
        $("#TxtPkMesa").val($mesa);
    }
    
    function CargarMesa() {
        var param = {pkMesa: $("#txtMesaApertura").val()};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=AperturarMesa",
            type: 'POST',
            data: param,
            beforeSend: function (xhr) {
                xhr.overrideMimeType("text/plain; charset=x-user-defined");
                $.messager.progress({
                    title: 'Por Favor Espere',
                    msg: 'Abriendo mesa...'
                });
            },
            success: function (data) {
                $.messager.progress('close');
                $('#modaConfirmAperturaMesa').modal('hide');
                openMesa($("#txtMesaApertura").val());
                var mesa = $("#txtMesaApertura").val();
                $('#btnMesa' + mesa).attr('class', 'mesas btn btn-lg btn-danger');
                $('#btnMesa' + mesa).removeAttr('onclick');
                $('#btnMesa' + mesa).click(function () {
                    openMesa(mesa);
                });
            }

        });
        $('#tblProductos').datagrid({
            url: '<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListAllProduct'});
    }
    
    function sumNumber($valor, $id) {
        $valor = parseInt($valor);
        var $total = parseInt($('#' + $id).val());
        $('#' + $id).val(($total + $valor));
    }
    
    function minNumber($valor, $id) {
        $valor = parseInt($valor);
        var $total = parseInt($('#' + $id).val());
        if ($total > 1) {
            $('#' + $id).val(($total - $valor));
        }
    }
    
    $(document).ready(function () {
        $(document).on( 'shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
            $('#tblcomprobante').datagrid();
        });
        $('#txtDescuento').numeric({negative: false});
        $("#txtCantidadSend").numeric({negative: false});
        $("#txtnPesonas").numeric({negative: false});
        loadItemsTipoPlato('42');
        $('#TxtdescripcionProduct').keypress(function (event) {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if (keycode == '13') {
                doBuscarMenu($('#TxtdescripcionProduct').val());
            }
        });
    });
    
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
        $("#d_precio_plato").html("S/ "+($precio).toFixed(2));
        $("#cartaSys").hide(0);
        $("#tecladoCantidad").show(0);
        contador = 0;
    }
    
    function regresarP(){
        topFunction();
        $("#cartaSys").show(0);
        $("#tecladoCantidad").hide(0);
    }
    
    /**
     * Actualiza los detalles de venta
     * */
     
    function loadDetalles() {
        $('#tblcomprobante').datagrid('load', {
            comprobante: $("#txtCombrobante").val()
        });
        console.log("carga");
    }

    /**
     * Cargar Los Items Segun el tipo de plato
     * */
     
    function regresarTipos(){     
        topFunction();
        loadItemsTipoPlato('42');
    }
    
    function loadItemsTipoPlato($pkCategoria) {
        $("#navegacionActual").html("Cargando...");
        var param = {'pkCategoria': $pkCategoria};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Tipos&&action=List",
            type: 'POST',
            data: param,
            dataType: 'json',
            success: function (data1) {
                $("#navegacionActual").html("Categorías");
                $div = $('#navegacionMenu');
                $div.empty();
                topFunction();
                $('<button id="btnMenus" hfre="#" onclick="mostrar_menus()" class="usqay-btn btn btn-lg">MENUS (SISTEMA)</button>').appendTo($div);
                for (var i = 0; i < data1.length; i++) {
                    $('<button id="btnTipo' + data1[i].pkTipoPlato + '" href="#" onclick="buscaTipo('+data1[i].pkTipoPlato+',\''+data1[i].descripcion+'\')" class="usqay-btn btn btn-lg">'+data1[i].descripcion+'</button>').appendTo($div);
                }               
            }
        });
    }
    
    function buscaTipo(pkTipo,des){
        var vactual = $("#navegacionActual").html();
        if(vactual == "Resultados de búsqueda"){
            doBuscarMenu($('#TxtdescripcionProduct').val());
        }else{
            if(des == "--BACK"){
                des = $("#navegacionActual").html();
            }
            $("#navegacionActual").html("Cargando...");
            $("#contenTipoPlato button").removeClass("usqay-btn-red");
            $("#btnTipo"+pkTipo).addClass("usqay-btn-red");
            $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListAllProduct&tipo=' + pkTipo,
                function (data) {
                    $("#navegacionActual").html(des);
                    var div = $('#navegacionMenu');
                    div.empty();
                    topFunction();
                    $('<button id="btnPlato" href="#" onclick="regresarTipos()" class="usqay-btn btn btn-lg"><b><span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span></b></button>').appendTo(div);
                    for (var i = 0; i < data.length; i++) {
                        if(parseInt(data[i].stock)<0){
                            $('<button id="btnPlato' + data[i].id + '" href="#" onclick="mostraralerta(' + data[i].precio + ',\'' + data[i].id + '\',\'' + data[i].tpedido + '\',\''+data[i].descripcion+'\')" class="usqay-btn btn btn-lg">'+data[i].descripcion+'<br/><b>S/. ' + data[i].precio + '</b></button>').appendTo(div);
                        }else{
                            $('<button id="btnPlato' + data[i].id + '" href="#" onclick="mostraralerta(' + data[i].precio + ',\'' + data[i].id + '\',\'' + data[i].tpedido + '\',\''+data[i].descripcion+'\')" class="usqay-btn btn btn-lg">'+data[i].descripcion+'<br/><b>S/. ' + data[i].precio + '</b><br/><span style="color:red !important;"><strong>STOCK:'+data[i].stock+'</span></button>').appendTo(div);
                        }
                    }

                }, 'json'
            );
        }
    }
    
    
    function mostrar_menus() {
        $("#navegacionActual").html("Cargando...");
        $("#contenTipoPlato button").removeClass("usqay-btn-red");
        $("#btnMenus").addClass("usqay-btn-red");
        $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=TiposMenus',
            function (data) {
                $("#navegacionActual").html("Menus");
                topFunction();
                var div = $('#navegacionMenu');
                div.empty();
                $('<button id="btnPlato" href="#" onclick="regresarTipos()" class="usqay-btn btn btn-lg"><b><span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span></b></button>').appendTo(div);
                for (var i = 0; i < data.length; i++) {
                    $('<button id="btnMenu' + data[i].id + '" hfre="#" onclick="modal_menu('+ data[i].id +')" class="usqay-btn btn btn-lg">' + data[i].nombre + '<br/><strong>S/. ' + data[i].precio + '</strong></button>').appendTo(div);
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
                body += "<div class='row' style='margin:0px !important;border-bottom: solid 1px #ddd;padding-bottom: 10px !important;margin-bottom: 10px !important;'>";
                body += "<div class='col-xs-12 col-sm-12 col-md-2'><b>"+value.nombre+"</b></div><div class='row col-xs-12 col-sm-12 col-md-7' style='margin: 0px !important;'>";
                $.each(value.platos, function (key, value1) {
                    body += '<div class="btn btn-success col-xs-12 col-sm-12 col-md-12" style="margin:3px;">';
                    body += '<b>' + value1.descripcion + '</b><br/>';
                    body += 'S/ ' + value1.precio +'<br/>';
                    body += '<span style="color:red;font-weight:bold;">Quedan: ' + value1.stock +'</span><br/>';
                    body += '<center>';
                    body += '<div class="input-group">';
                    body += '<span class="input-group-addon" onclick="res_plato(\''+value1.pkPlato+'\',\''+value1.precio+'\','+it+')">-</span>';
                    body += '<input id="cant'+value1.pkPlato+'" type="number" class="form-control" value="0" min="0" max="'+value1.stock+'" style="text-align: center;" readonly>';
                    body += '<span class="input-group-addon" onclick="sum_plato(\''+value1.pkPlato+'\',\''+value1.precio+'\','+it+','+value1.stock+')">+</span>';
                    body += '</div>';
                    body += '</center>';
                    body += '</div>';
                });
                body += "</div><div class='col-xs-12 col-sm-12 col-md-3'>";
                body += "<center><b>Total "+value.nombre+"</b></center>";
                body += "<center><b>S/<span id='tt"+it+"'>0.00</span></b></center>";
                body += "</div>";
                body += "</div>";
                it = it + 1;
            });
            $("#componente").html(body);
            }, 'json');
        $("#cartaSys").hide(0);
        $("#contenidoMenu").show(0);
    }
    
    function sum_plato(pk_plato,precio,postipo,stock){
        var cantidad_actual = $("#cant"+pk_plato).val();
        var nueva_c = parseInt(cantidad_actual) + 1;
        
        if(nueva_c <= parseInt(stock)){
            var tmp = {};
            tmp["cantidad"] = nueva_c;
            tmp["precio"] = precio;
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
    
    function res_plato(pk_plato,precio,postipo){
        var cantidad_actual = $("#cant"+pk_plato).val();
        var nueva_c = parseInt(cantidad_actual) - 1;
        
        if(nueva_c >= 0){
            var tmp = {};
            tmp["cantidad"] = nueva_c;
            tmp["precio"] = precio;
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
        $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=AddMenu',
        {
            platos: JSON.stringify(menus),
            pkComprobante: $('#txtCombrobante').val(),
            tipo: 'pkPlato'
        },
        function(){
            topFunction();
            loadDetalles();
            $("#total_menu").html("0.00");
            $("#cartaSys").show(0);
            $("#contenidoMenu").hide(0);
        });   
    }
    
    function regresa_menu(){
            topFunction();
            $("#total_menu").html("0.00");
            $("#cartaSys").show(0);
            $("#contenidoMenu").hide(0);
    }
    
    /**
     * Mostrar el los datos segun el tipo de pedido
     * 
     * */
    
    function doBuscarMenu($decripcion) {
        $("#navegacionActual").html("Cargando...");
        $.post('<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ListProductDescripcion&descripcion='+$decripcion ,
                function (data) {
                    $("#navegacionActual").html("Resultados de búsqueda");
                    var div = $('#navegacionMenu');
                    div.empty();
                    topFunction();
                    $('<button id="btnPlato" href="#" onclick="regresarTipos()" class="usqay-btn btn btn-lg"><b><span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span></b></button>').appendTo(div);
                    for (var i = 0; i < data.length; i++) {
                        if(parseInt(data[i].stock)<0){
                            $('<button id="btnPlato' + data[i].id + '" href="#" onclick="mostraralerta(' + data[i].precio + ',\'' + data[i].id + '\',\'' + data[i].tpedido + '\',\''+data[i].descripcion+'\')" class="usqay-btn btn btn-lg">'+data[i].descripcion+'<br/><b>S/. ' + data[i].precio + '</b></button>').appendTo(div);
                        }else{
                            $('<button id="btnPlato' + data[i].id + '" href="#" onclick="mostraralerta(' + data[i].precio + ',\'' + data[i].id + '\',\'' + data[i].tpedido + '\',\''+data[i].descripcion+'\')" class="usqay-btn btn btn-lg">'+data[i].descripcion+'<br/><b>S/. ' + data[i].precio + '</b><br/><span style="color:red !important;"><strong>STOCK:'+data[i].stock+'</span></button>').appendTo(div);
                        }
                    }

                }, 'json'
                );
    }
    

    function saveMessagePedido($mensaje) {
        var param = {pkPedido: $("#txtIdPedido").val(),
            message: $mensaje};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=SaveMessagePedido",
            type: 'POST',
            data: param,
            success: function (data) {
                $('#textAreaMessage').val("");
                loadDetalles();
                topFunction();
                $("#panelMensajes").hide(0);
                $("#panelOperativo").show(0);
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
    
    function regresarMensajes() {
       $('#textAreaMessage').val("");
       topFunction();
       $("#panelMensajes").hide(0);
       $("#panelOperativo").show(0);
    }

    /**
     * ============================================
     * Agregar un pedido nuevo
     * =============================================
     * @function Agregar Pedido
     *  */
   //Variable global para bloquear envio doble
   var enviando = 0;
   
    function addPedido() {
        if(enviando === 0){
            $('#modal_envio_anim').modal('show');
            enviando = 1; 
            var param = {cantidad: $('#txtCantidadSend').val(),
                precio_venta: $("#txtPrecioSend").val(),
                fkPedido: $('#txtPkPedido').val(),
                pkComprobante: $('#txtCombrobante').val(),
                tipo: $("#txtTipoSend").val()};
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=AddPedido",
                type: 'POST',
                data: param,
                success: function (mdata) {
                    loadDetalles();
                    buscaTipo(mdata,'--BACK');
                    $("#cartaSys").show(0);
                    $("#tecladoCantidad").hide(0);
                    enviando = 0;
                    $('#modal_envio_anim').modal('hide');
                }
            });
        }
    }
    
    /**
     * ============================================
     * Actualizar el numero de personas
     * =============================================
     * @function update n Personas
     *  */
    function updateNPersonas() {
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
    
    function sumaTotal() {
        var total = 0;
        var ss = [];
        var rows = $('#tblcomprobante').datagrid('getRows');
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i];
            ss.push('<span>' + row.precio + ":" + row.importe + ":" + row.pedido + '</span>');
            total = total + parseFloat(row.importe);
        }
        $("#lblSubTotal").html("S/ " + subTotalIgv(total));
        var descuento_sin_igv = (parseInt($("#txtDescuento").val())/1.18).toFixed(2);
        $("#lblDesC").html("S/ "+descuento_sin_igv);
        var subtotal_c_descuento = (subTotalIgv(total) - descuento_sin_igv).toFixed(2);
        $("#lblSubTotalD").html("S/ "+subtotal_c_descuento);
        total = total - $("#txtDescuento").val();
        $("#lblIgv").html("S/ " + (total - subTotalIgv(total)).toFixed(2));
        $("#lblTotal").html("S/ " + total.toFixed(2));
        var param = {dsc:$("#txtDescuento").val(),pk:$('#txtCombrobante').val()};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=Descuento",
            type: 'POST',
            data: param,
            success: function () {
                //Ni Madres
            }

        });
    }

    function subTotalIgv($total) {
        var igv = (<?php echo Class_config::get('igv') ?> + 100) / 100;
        var total = ($total / igv).toFixed(2);
        return total;
    }

    function totaIgv($subTotal) {
        var igv =<?php echo Class_config::get('igv') ?> * 1 / 100;
        return ($subTotal * igv).toFixed(2);
    }

    /*Cargando mensaje del pedido*/
    function loadMessage(index, field) {
        var row = $('#tblcomprobante').datagrid('getSelected');
        if (row) {
            $("#TxtMensaje").val(row.mensaje);
            $("#txtNameMozo").val(row.mozo);
        }
    }
    /**
     * ============================================
     * Cancela un Pedido
     * =============================================
     * @function update n Personas
     *  */
    function regresaCancelaPedido(){
        topFunction();
        $("#panelOperativo").show(0);
        $("#panelCancelCuenta").hide(0);
    }
    
    function CancelaPedido() {
        //Primero enviamos pedidos pendientes
        if(enviando === 0){
            enviando = 1;
            var totalf = $('#lblTotal').html();
            totalf = totalf.split(" ");
            totalf = totalf[1];
            $('#modal_envio_anim').modal('show');
            var param = {'array': JSON.stringify(array_table_envio()), 'terminal': $('#terminal').val()};
            $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=EnviaPedido",
                type: 'POST',
                data: param,
                cache: false,
                success: function() {
                    //Luego generamos el comprobante
                    var param1 = {descuento: $('#txtDescuento').val(),
                        total_venta: totalf,
                        pkMesa: $("#TxtPkMesa").val(),
                        pkComprobante: $('#txtCombrobante').val(),
                        efectivo: $("#inputPago").val()
                    };
                    $.ajax({
                        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Comprobante&&action=CancelarPedido",
                        type: 'POST',
                        data: param1,
                        success: function () {
                            //Finalmente ponemos en cola la impresion
                            var param2 = {'pkPedido': $("#txtCombrobante").val(), 'terminal': $('#terminal').val(), 'tipo': 'EFECTIVO', 'aux': '<?php echo UserLogin::get_id();?>'};
                            $.ajax({
                                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ImprimeCuenta",
                                    type: 'POST',
                                    data: param2,
                                    cache: false,
                                    success: function() {
                                        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                                    }
                            });
                        }
                    });
                }
            });
        }
    }
    
    function checkedTarjeta() {

        if ($('#chkpagoEfectivoTarjeta').prop('checked') == false) {

            $('#txtMontoCTarjeta').attr('disabled', true);
        }
        else {
            $('#txtMontoCTarjeta').attr('disabled', false);
            $('#txtMontoCTarjeta').focus();
        }
    }
    function checkedTarjeta2($id, $idMonto) {
        if ($('#' + $id).prop('checked') == false) {
            $('#' + $idMonto).attr('disabled', true);
            $('#' + $idMonto).val("");
            if($id == "chkpagoEfectivoTarjetaBoleta"){
                $("#txtMontoEfectivo").val("");
            }else{
                $("#txtMontoEfectivoFactura").val("");
            }
        }
        else {
            $('#' + $idMonto).attr('disabled', false);
            $('#' + $idMonto).focus();
        }
    }
    
    function restarNumeros() {
        var total = $("#lblTotalCancelCuenta").html();
        var tarjeta = $('#txtMontoCTarjeta').val();
        if(tarjeta == ""){
            $("#lblMontoEfectivo").html("");
        }else{
            var total_final = parseFloat(total) - parseFloat(tarjeta);
            if(total_final >= 0){
                $("#lblMontoEfectivo").html(total_final);
            }else{
                $('#txtMontoCTarjeta').val(total);
                $("#lblMontoEfectivo").html("");
            }
        }
    }
    
    function restarNumeros2($idTotal, $idMontoEfectivo, $idTarjeta) {
        var total = parseFloat($("#" + $idTotal).html());
        var tarjeta = $('#' + $idTarjeta).val();
        if(tarjeta == ""){
            $("#" + $idMontoEfectivo).val("");
        }else{
            var total_final = parseFloat(total) - parseFloat(tarjeta);
            if(total_final >= 0){
                $("#" + $idMontoEfectivo).val(total_final);
            }else{
                $('#' + $idTarjeta).val(total);
                $("#" + $idMontoEfectivo).val("");
            }
        }
    }
    /**
     * ============================================
     * Cancela un Pedido Con Tarjeta
     * =============================================
     * @function update n Personas
     *  */
     
    function regresaCancelaPedidoConTarjeta(){
        topFunction();
        $("#panelOperativo").show(0);
        $("#panelCancelCuentaCTarjeta").hide(0);
    }
    
    function CancelaPedidoConTarjeta() {
        //Primero enviamos pedidos pendientes
        if(enviando === 0){
            enviando = 1;
            var totalf = $('#lblTotalCancelCuenta').html();
            $('#modal_envio_anim').modal('show');
            var param = {'array': JSON.stringify(array_table_envio()), 'terminal': $('#terminal').val()};
            $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=EnviaPedido",
                type: 'POST',
                data: param,
                cache: false,
                success: function() {
                    //Luego generamos el comprobante
                    var param1 = null;
                    if ($('#chkpagoEfectivoTarjeta').prop('checked') == false) {
                        param1 = {descuento: $('#txtDescuento').val(),
                            total_venta: totalf,
                            total_tarjeta: totalf,
                            total_efectivo: 0,
                            array: JSON.stringify($('#tblcomprobante').datagrid('getSelections')),
                            tipo_tarjeta: $('#cmbTipoTarjeta').val(),
                            pkMesa: $("#TxtPkMesa").val(),
                            pkComprobante: $('#txtCombrobante').val()};
                    } else {
                        param1 = {descuento: $('#txtDescuento').val(),
                            total_venta: totalf,
                            total_tarjeta: $('#txtMontoCTarjeta').val(),
                            array: JSON.stringify($('#tblcomprobante').datagrid('getSelections')),
                            total_efectivo: $('#lblMontoEfectivo').html(),
                            tipo_tarjeta: $('#cmbTipoTarjeta').val(),
                            pkMesa: $("#TxtPkMesa").val(),
                            pkComprobante: $('#txtCombrobante').val()
                        };
                    }
                    $.ajax({
                        url: "<?php echo Class_config::get('urlApp') ?>/?controller=Comprobante&&action=CancelarPedidoTarjeta",
                        type: 'POST',
                        data: param1,
                        success: function () {
                            //Finalmente ponemos en cola la impresion
                            var param2 = {'pkPedido': $("#txtCombrobante").val(), 'terminal': $('#terminal').val(), 'tipo': 'TARJETA', 'aux': '<?php echo UserLogin::get_id();?>'};
                            $.ajax({
                                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ImprimeCuenta",
                                    type: 'POST',
                                    data: param2,
                                    cache: false,
                                    success: function() {
                                        window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                                    }
                            });
                        }
                    });
                }
            });
        }
    }
    
    /**
     * Mostrar Mensaje para enviar un mensaje a cocina
     * 
     * */
    function showMessages() {
        var row = $('#tblcomprobante').datagrid('getSelected');
        if (row) {
            $("#nombre_producto").html(row.pedido);
            $("#mensaje_producto").html(row.mensaje);
            $("#txtIdPedido").val(row.pkPedido);
            $("#panelOperativo").hide(0);
            $("#panelMensajes").show(0);
        }
        else {
            $.messager.alert('Alerta', "Debe Seleccionar un Pedido", 'warning');
        }
    }


    //Con esto se paga en efetivo
    function openModal() {
        $('#lblTotalCancel').html(calculaTotal());
        $('#inputPago').focus();
        $("#panelOperativo").hide(0);
        $("#panelCancelCuenta").show(0);
    }
    
    function openModalCancelCuentaTarjeta() {
        $('#lblTotalCancelCuenta').html(calculaTotal());
        $('#lblMontoEfectivo').html(calculaTotal());
        $("#panelOperativo").hide(0);
        $("#panelCancelCuentaCTarjeta").show(0);
    }
    
    function openCredito(){
        $("#panelOperativo").hide(0);
        $("#panelPagoCredito").show(0);  
    }
    
    function openConsumo(){
        $("#panelOperativo").hide(0);
        $("#panelPagoConsumo").show(0);  
    }
    
    function openModalGenerarComprobante() {
        $("#panelOperativo").hide(0);
        $("#panelBoleta").show(0); 
        $('#txtMontoEfectivo').focus();
        $('#lblTotalCancelCuentafrmPagoBoleta').html(calculaTotal());
    }
    
    function openModalGenerarComprobanteFactura() {
        $("#panelOperativo").hide(0);
        $("#panelFactura").show(0); 
        $('#lblTotalCancelCuentafrmPagoFactura').html(calculaTotal());
    }
    
    function openModalChangeMesa($mesa) {
        $("#panelOperativo").hide(0);
        $("#panelCambioMesa").show(0);  
        $('#contenidoCambioMesa').html("Cambiando de mesa");
        $('#contenidoCambioMesa').empty();
        $('#contenidoCambioMesa').html('Cargando...');
        $("#contenidoCambioMesa").load('<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ShowMesas&mesaAnterior=' + $mesa + '&pkPedido=' + $('#txtCombrobante').val());
    }
    
    function regresaCambioMesa(){
        topFunction();
        $("#panelOperativo").show(0);
        $("#panelCambioMesa").hide(0);  
    }

    function calculaVuelto() {
        var pago = parseFloat($("#inputPago").val());
        if(isNaN(pago)){
            pago = 0;
            $("#lblVuelto").html("00.00");
        }
        var totalf = $('#lblTotal').html();
        totalf = totalf.split(" ");
        totalf = totalf[1];
        var total = parseFloat(totalf);     
        if (pago >= total){
            $("#lblVuelto").html((pago - total));
        }else{
            $("#lblVuelto").html("00.00");
        }
    }
    
    function calculaVuelto2($totalPago, $totalVenta, $idVuelto) {
        var pago = parseFloat($("#" + $totalPago).val());
        var totalf = $("#" + $totalVenta).html();
        totalf = totalf.split(" ");
        totalf = totalf[1];
        var total = parseFloat(totalf);
        if (pago >= total)
            $("#" + $idVuelto).val((pago - total));
    }

    var contador = 0;
    $('#inputPago').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            contador++;
            switch (contador) {
                case 1:
                    calculaVuelto();
                    break;
                case 2:
                    break;
                case 3:
                    CancelaPedido();
                    contador = 0;
                    break;
            }
        }
    });
    $('#txtMontoEfectivoFactura').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            calculaVuelto2('txtMontoEfectivoFactura', 'lblTotal', 'txtvueltoFactura');
        }
    });
    
    $('#txtMontoEfectivo').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            calculaVuelto2('txtMontoEfectivo', 'lblTotal', 'txtvueltoBoleta');
        }
    });


    function _anulaPedido($nameGrid) {
        var row = $('#' + $nameGrid).datagrid('getSelected');
        if (row) {  
            var param = {'pkDetallepedido': row.pkPedido, tipo: 1};
            $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=DeletePedido",
                type: 'GET',
                data: param,
                dataType: 'html',
                success: function (data) {
                    loadDetalles();
                }
            });
        }
        else {
            $.messager.alert('Error', '¡Debe Seleecionar un Item del Listado de Pedidos!', 'error');
        }
    }

    //guarda el mensaje 
    function _saveMensaje() {
        var param = {'descripcion': $("#textAreaMessage").val()};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Mensaje&&action=Save",
            type: 'POST',
            data: param,
            cache: true,
            dataType: 'json',
            success: function (data) {
                console.log(data);
            }
        });
    }
    
    function openImprimirCuenta() {
        var param = {'pkPedido': $("#txtCombrobante").val(), 'terminal': $('#terminal').val(), 'tipo': 'CUENTA', 'aux': '<?php echo UserLogin::get_id();?>'};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=ImprimeCuenta",
                type: 'POST',
                data: param,
                cache: false,
                dataType: 'json',
                success: function() {
                    ///Nada
                }
        });
    }

    function checkEfectivoTarjeta($id, $idDiv) {
        var value2 = $('input:radio[name=' + $id + ']:checked').val();
        if (value2 === "1") {
            $('#' + $idDiv).hide();
        }
        else {
            $('#' + $idDiv).show();
        }
    }

    $('#txtRucFactura').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            console.log("presiono enteeer");
            searchCustomerRuc($('#txtRucFactura').val(), "txtRazonSocialFactura", "txtDireccionFactura");
        }
    });
    
    $('#txtDniCliente').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            searchCustomerDNI($('#txtDniCliente').val(), "txtNombres", "txtApellidos");
        }
    });
    function searchCustomerRuc($document, $idRazon, $idDireccion) {
        $("#" + $idRazon).val("");
        $("#" + $idDireccion).val("");
        var param = {'document': $document
        };
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=ClienteRuc",
            type: 'POST',
            data: param,
            cache: true,
            dataType: 'json',
            success: function (data) {
                $("#" + $idRazon).val(data[0].companyName);
                $("#" + $idDireccion).val(data[0].address);
            }
        });
    }
    ;
    
    function searchCustomerDNI($document, $nombres, $apellidos) {
        $("#" + $nombres).val("");
        $("#" + $apellidos).val("");
        var param = {'document': $document
        };
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Cliente&&action=ClienteDni",
            type: 'POST',
            data: param,
            cache: true,
            dataType: 'json',
            success: function (data) {
                $("#" + $nombres).val(data[0].nombres);
                $("#" + $apellidos).val(data[0].apellidos);
            }
        });
    }
    ;
    
    function onClickCell(index, field) {
        console.log(field);
        if (endEditing() && field == "pedido") {
            $('#tblcomprobante').datagrid('selectRow', index)
                    .datagrid('editCell', {index: index, field: field});
            editIndex = index;
        }
    }
    
    function saveChanges(index, data, changes) {
        var param = {'pkPedido': data.pkPedido,
            cantidad: data.cantidad, precio: data.precio};
        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=UpdatePedido",
            type: 'POST',
            data: param,
            success: function (data2) {
                loadDetalles();
            }

        });
    }

    function confirmCancelaMesa() {
        $.messager.confirm('Liberando Mesa ', '¿Esta seguro que desea liberar esta mesa?. Los pedidos registrados se anularán', function (r) {
            if (r) {
                //Anulamos pedidos
                var param = {'array': JSON.stringify($('#tblcomprobante').datagrid('getRows')), 'terminal': $('#terminal').val()};
                $.ajax({
                url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=DeletePedido",
                    type: 'POST',
                    data: param,
                    success: function() {
                        //Liberamos mesa
                        var param2 = {'pkPedido': $('#txtCombrobante').val()};
                        $.ajax({
                            url: "<?php echo Class_config::get('urlApp') ?>/?controller=Pedidos&&action=CancelaMesa",
                            type: 'POST',
                            data: param2,
                            dataType: 'html',
                            success: function () {
                                window.location.href = "<?php echo Class_config::get('urlApp') ?>/?controller=Index&&action=ShowHome";
                            }
                        });
                    }
                });
            }
        });
    }

    function calculaTotal() {
        /*var $total = 0;
        if ($('#tblcomprobante').datagrid('getSelections').length > 0) {
            for (var i = 0; i < $('#tblcomprobante').datagrid('getSelections').length; i++) {
                $total = $total + parseFloat($('#tblcomprobante').datagrid('getSelections')[i].importe);
            }
            $total=$total-$('#txtDescuento').val();

        } else {
            
            $total = $("#lblTotal").html();
        }*/
        var totalf = $('#lblTotal').html();
        totalf = totalf.split(" ");
        totalf = totalf[1];
        return totalf;
    }


    /**LUIGUI*/
    function openModalGenerarComprobante2() {
        $('#modalCancelGnerarComprobante2').modal('show');
        $('#lblTotalCancelCuentaBoleta').html(calculaTotal());
    }

    /**JEison*/
    
    /** Par de Putos **/


    function SumaBilletesMonto($cantidad, $idMonto) {
        var total = parseFloat($('#' + $idMonto).val());
        if(isNaN(total)){
            $('#' + $idMonto).val($cantidad);
        }else{
            $('#' + $idMonto).val(total + $cantidad);
        }
        calculaVuelto();
    }