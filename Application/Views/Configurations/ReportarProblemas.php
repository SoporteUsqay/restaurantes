<?php include 'Application/Views/template/header.php'; ?>
<body>

    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showContent();
    ?>    
    <div class="container">
        <!--<div class="jumbotron">-->

        <br>
        <br>
        <br>

        <div class="panel panel-default" >
            <div class="panel-heading">
                <h3 class="panel-title"> <span class="glyphicon glyphicon-calendar"> </span> Contactanos</h3>
            </div>
            <div class="panel-body" style="font-size: 14px">
                <form class="form-horizontal" id="FormReportProblem" action="ReportProblemas.php" method="get">
                    
                    
                    <div class="form-group">
                        <label for="txtIdModulo" class="col-sm-2 control-label">Modulo</label>
                        <div class="col-sm-9">
                             <select name="txtIdModulo" class="form-control" id="cmbModulo" onclick="_listModulos('cmbModulo','cmbSubModulo')"  >

                             </select>
                        </div> 
                       
                    </div>
                    
                    <div class="form-group">
                        <label for="txtSubModulo" class="col-sm-2 control-label">SubModulo</label>
                        <div class="col-sm-9">
                            <select name="txtSubModulo"  class="form-control" id="cmbSubModulo" >

                            </select>
                        </div> 
                    </div>


                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Asunto</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="txtproblem" name="txtproblem" placeholder="Asunto del Problema">
                        </div> 
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">Mensaje</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="txtdescripcionpro" name="txtdescripcionpro" placeholder="Describa aquí el problema"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-9">
                            <button type="button" class="btn btn-success" onclick="enviarEmail()">Enviar Informe</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <input for="inputEmail3" class="col-sm-2 control-label" name="sesion" value="<?php echo $_SESSION['id'] ?>" hidden="true">
                    </div>
                </form>
            </div>
        </div>
    </div>
<center>


    <h4>
        Escribanos algun mensaje, inconveniente, oportunidad de mejora
    </h4>

</center>
<script>
   
    $(document).ready(function () {
 
        ListComboModulos();

    });
   
    function ListComboModulos(){
        
        var $id = "cmbModulo";
        $('#' + $id + ' option').remove();
        $('#' + $id).append("<option value=\"0\">Seleccione una opcion</option>")

        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Config&action=ListModulos', function (data) {

            for (var i = 0; i < data.length; i++) {

                $('#' + $id).append("<option value=\"" + data[i].idmodule + "\">" + data[i].nombre_Module + "</option>")

            }
            $('#cmbModulo').val(getpkSalon());

        });
    }
    
    
    function _listModulos($idmodulo,$idsubmodulo) {
        $('#'+$idsubmodulo+' option').remove();
        var pro= $('#'+$idmodulo).val();
        $('#'+$idsubmodulo).append("<option value=\"0\">Seleccione una opcion</option>")
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=Config&action=LisSubModulos&pkIdModulo='+pro, function(data) {

            for (var i = 0; i < data.length; i++) {

                $('#'+$idsubmodulo).append("<option value=\"" + data[i].id + "\">" + data[i].nombre_SubModule + "</option>")
//            $('#txtModifyUserType').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
//            $('#cmbWorkStationModifyPermissions').append("<option value=\"" + data[i].pkWorkStation + "\">" + data[i].description + "</option>")
            }

        });
    }
    
    
    
    function enviarEmail() {
        $.ajax({
            type: "GET",
            url: "<?php echo Class_config::get('urlApp') ?>/ReportProblemas.php",
            data: $('#FormReportProblem').serialize(), //$("#frmCategoria").serialize(), // Adjuntar los campos del formulario enviado.
            dataType: 'html',
            success: function (data)

            {

                $.messager.show({
                    title: 'estado',
                    msg: data
                });

            }
        });
    }
</script>