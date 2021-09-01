<style>
    .notification-bubble{
        display: inline; background: #db4437; height: 16px;
        width: 16px;
        position: absolute;
        top: -15px;
        left: 5px;
        color: #fff;
        text-align: center;
        font-size: 8px;
        line-height: 14px;
        border-radius: 20px;
        font-weight: bold;
        cursor: pointer;
    }
    
    .navbar-inverse .navbar-nav > li > a {
        color: #fff !important;
    }
    
    @media (max-width:1360px) and (min-width:700px){
        .navbar-inverse .navbar-nav > li > a {
            font-size:0px !important;

        }
        .navbar-inverse .navbar-nav > li > a .glyphicon{
            font-size: 12px !important;
        }
    }
</style>
<?php
if(!isset($p_reportes)){ 
    require_once 'Components/Config.inc.php';
}
$urlapp = Class_config::get('urlApp');

//error_reporting(E_ALL);              
$db = new SuperDataBase();
$objUserSystem = new UserLogin();
//Obtenemos id del usuario
$id_usuario = $objUserSystem->get_idTrabajador();
$tipo_actual = intval($objUserSystem->get_pkTypeUsernames());
?>
<div class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="background-color: #00395a !important; border-color: #00395a !important; border-bottom: #ef6a00 solid 5px !important;">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a style="width:110px;margin-top: -2px;padding:15px 5px;" class="navbar-brand" href="<?php echo $urlapp;?>/?controller=Index&&action=ShowHome"><img src="<?php echo Class_config::get('urlApp') ?>/Public/images/usqay-large-inverse.svg" style="width:100%;"/></a>
        </div>
        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav btn-xs" >
               
                <?php if (in_array($tipo_actual, [1, 2])): ?>
                <li class="">
                    <a class="nav-link" href="<?php echo $urlapp;?>/?controller=Dashboard&&action=Show">
                        <span class="glyphicon glyphicon-home" aria-hidden="true"></span>
                        <!-- Dashboard -->
                    </a>
                </li>
                <?php endif ?>
                <?php
                
                $query_ = "";
                if($tipo_actual > 7 && $tipo_actual < 11){
                    $query_ = "Select * from module where estadoModule = '0'";
                }else{
                    $query_ = "SELECT pkModule, nameModule,url from module m inner join accesmodule a on a.fkModule=m.pkModule where estadoModule=0 and a.fkTypeUser=" . $objUserSystem->get_pkTypeUsernames();
                }
                //echo $query_;
                $resultListModule = $db->executeQuery($query_);
                while ($row = $db->fecth_array($resultListModule)) {
                    //Verificamos si es usuario acreedor a filtro
                   
                    $merece = 1;
                    if($tipo_actual > 7 && $tipo_actual < 11){
                        //Verificamos si el usuario se merece el modulo
                        $query_merece = "Select * from trabajador_modulo where id_trabajador = '".$id_usuario."' AND id_modulo = '".$row["pkModule"]."'";
                        //echo $query_merece;
                        $rmerece = $db->executeQuery($query_merece);
                        if($rw = $db->fecth_array($rmerece)){
                            //Nada papu
                        }else{
                            //pasa el zelda 
                            //RIP ":v" 2/6/2016 :(
                            $merece = 0;
                        }
                    
                    }
                    
                    if($merece == 1){
                        echo '<li class="dropdown"><a  class="dropdown-toggle"  data-toggle="dropdown" href="">';
                        echo utf8_encode($row['nameModule']);
                        echo "<b class='caret'></b></a>";

                        $_query = "SELECT pkSubModule, fkModule, nameSubModule, status,url FROM submodule s where fkModule=" . $row['pkModule'] . " and status in (0, 2)";
                        $resultSubModule = $db->executeQuery($_query);

                        $a = 0;
                        
                        while ($ro2 = $db->fecth_array($resultSubModule)) {
                            $merece_sub = 1;
                            if($tipo_actual > 7 && $tipo_actual < 11){
                                //Verificamos si el usuario se merece el modulo
                                $query_merece1 = "Select * from trabajador_submodulo where id_trabajador = '".$id_usuario."' AND id_submodulo = '".$ro2["pkSubModule"]."'";
                                $rmerece1 = $db->executeQuery($query_merece1);
                                if($rw = $db->fecth_array($rmerece1)){
                                    //Nada papu
                                }else{
                                    //pasa el zelda 
                                    //RIP :v 2/6/2016 :(
                                    $merece_sub = 0;
                                }
                            }
                            if($merece_sub == 1){
                                if ($a == 0) {
                                    echo '<ul  class="dropdown-menu">';
                                }
                                if ($ro2['status'] == 2) {
                                    echo '<li class="divider"></li>';
                                }
                                echo '<li ><a  href="'.$urlapp.'/?controller=' . $row['url'] . '&action=' . $ro2['url'] . '">';
                                echo utf8_encode($ro2['nameSubModule']);
                                echo '</a></li>';
                                $a = 1;
                            }
                        }
                        if ($a == 1) {
                            echo"</ul>";
                        }

                        echo '</li>';
                    }
                }
                ?>
                <li class="dropdown"><a  class="dropdown-toggle"  data-toggle="dropdown" href="">
                       <span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span> Ayuda <b class='caret'></b></a>
                    <ul  class="dropdown-menu">
                        <li > <a  href="support/manual.pdf" target="__blank">
                                Manual de Usuario
                            </a>
                        </li>
                    </ul>
                    </a>

                </li>
            </ul>
            <ul class="nav navbar-nav navbar-right btn-xs">
                <li class="dropdown">
                    <a  class="dropdown-toggle"  data-toggle="dropdown" href="">
                        <span class="glyphicon glyphicon-user" aria-hidden="true"></span><?php
                        $user = new UserLogin();
                        echo" " . $user->get_names()
                        ?> <b class='caret'></b>
                    </a>
                    <ul  class="dropdown-menu">
                        <li > 
                            <a  onclick="openModalContrasena()">
                                Cambiar Contraseña
                            </a>
                        </li>
                        <li > 
                            <a  href="<?php echo $urlapp;?>/?controller=Soporte&&action=Show">
                                Añadir Ticket para Soporte
                            </a>
                        </li>
                    </ul>
                    </a>

                </li>
                <li class="active"><a style="background-color: #00395a !important;" href="<?php echo Class_config::get('urlApp') ?>/?controller=User&action=CloseSession"><span class="glyphicon glyphicon-off"></span> Salir</a></li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>

<div id="modalCambiarContrasena" class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><label id="tituloModalDetalleMas">Cambiar Acceso</label></h4>
            </div>
            <div class="modal-body">
                
                <form id="frmCon">
                    <div class="form-group">
                        <label for="">Contraseña actual</label>
                        <input type="text" name="password_actual" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="">Nueva Contraseña</label>
                        <input type="text" name="password_nuevo" class="form-control">
                    </div>
                </form>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="sendChangePass()">Aceptar</button>
            </div>
        </div>
    </div>
</div>

<script>

    function openModalContrasena() {
        $('#modalCambiarContrasena').modal('show')
    }

    function openModalSoporte() {
        window.location.href = "<?php echo Class_config::get('urlApp') ?>/reportes/view_soporte_ticket.php"
    }

    function sendChangePass() {

        $.ajax({
            url: "<?php echo Class_config::get('urlApp') ?>/?controller=User&&action=ChangePass",
            type: 'POST',
            data: $('#frmCon').serialize(),
            dataType: 'json',
            success: function(data) {
                
                alert(data.message);
                if (data.ok) {
                    $('#modalCambiarContrasena').modal('hide');
                    location.reload();
                }
            }

        });
    }   

    
</script>