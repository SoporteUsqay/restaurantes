<!--<script src="Public/scripts/WorkEmployed.js.php"></script>
<script src="Public/scripts/LoadSelect.js.php"></script>-->
<!--<section class="three fourths padded  bounceInDown animated  align-bottom-desktop" onload="">-->
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
   <h3><?php echo Class_message::get('titleList')?></h3>
    <!--        <div id="toolbar" class="ui-widget-header ui-corner-all">-->
    <!--    <button onclick="_onLoadPage('User', 'Register')">Agregar</button> 
        <button onclick="_onLoadPage('User', 'Register')">Exportar</button> 
        <button onclick="_onLoadPage('User', 'Register')">Imprimir</button> -->
    <p><a href="javascript:void(0)" id="delete"><?php echo Class_message::get('BtnDelete')?></a></p>
    <p><a href="javascript:void(0)" id=""><?php echo Class_message::get('BtnModify')?></a></p>
    <!--</div>-->
    <table  border="1" class="table table-striped table-bordered" cellspacing="0" id="example" width="100%">
        <thead>


            <tr>
                <th style="width: 250px">
                    ID
                </th>
                <th style="width: 250px">
                    <?php echo Class_message::get('TxtNameAndLastNames') ?>  
                </th>
                <th style="width: 250px">
                    <?php echo Class_message::get('TxtUserName') ?>
                </th>
                <th style="width: 150px">
                    <?php echo Class_message::get('TxtTyeUser') ?>
                </th>
                <th style="width: 100px">
                    <?php echo Class_message::get('msgDocument')?>
                </th>
                <th style="width: 100px">
                    <?php echo Class_message::get('msgEmail')?>
                </th>

            </tr>
        </thead>

        <div id="dialog-form" title="Modificando Usuario" style="display: none">

            <tbody> 
                <?php
                $db = new SuperDataBase();
                $query = "SELECT idusuarios, user,   concat(names,' ', lastNames) as nombres, document, email,   description as typeUser FROM users u inner join workpeople w on w.pkWorkPeople=u.fkWorkPeople inner join typeuser tu on tu.pkTypeUser= u.fkTypeUser;";
                $result = $db->executeQuery($query);

                while ($row = $db->fecth_array($result)) {
                    echo"<tr >";
                    echo "<td>" . $row['idusuarios'] . "</td>";
                    echo "<td>" . $row['nombres'] . "</td>";
//                    echo "<td><input style='width:80px' class='form-control' type='text' id='row-1-position' name='row-1-position' value='" . $row['stock'] . "'/></td>";
                    echo "<td>" . $row['user'] . "</td>";
                    echo "<td>" . $row['typeUser'] . "</td>";
                    echo "<td>" . $row['document'] . "</td>";
                    echo "<td>" . $row['email'] . "</td>";
//                    echo "<td>" . $row[''] . "</td>";
//                    echo "<td>" . $row['category'] . "</td>";
//                    echo "<td width='15%'><input style='width:100px' class='form-control' type='number' id='row-1-position' name='row-1-position' value='" . $row['pricenEnd'] . "'/></td>";
//                    echo "<td width='15%'> " . $row['priceGo'] . "</td>";
                    echo"</tr>";
                }
                ?>
            </tbody>
    </table>
</form>
</div>
<?php // require 'Register.tpl.php';?>
<script type="text/javascript">
    function _openRegister() {
        $("#dialoRegister").dialog("open");
    }

    //              _listUser();
    $(window).load(function() {
        //                 _listUser();
        alert("_laist")
    });



    // id,name,user,typeUser
    function _openModifyUser(id, name, user, typeUser) {
        //                console.log(id + " " + name + " " + user);
        $('#txtModifyUser_id').val(id);
        $('#txtModifyUserName').val(name);
        $('#txtModifyUser').val(user);
        $('#txtModifyUserType').val(typeUser);
        $("#dialog-form").dialog("open");


    }
    function _openRegisterUser() {

        $("#dialoRegister").dialog("open");


    }

    function _deleteUser(id) {
        $.post(
                "<?php echo Class_config::get('urlApp') ?>/?controller=User&&action=Delete",
                {pkUser: id},
        function(data) {
            alert("<?php echo Class_message::get('msgDeleteOk')?>");
            _listUser();
        }

        );
    }

    $("#dialog-form").dialog({
        //                resizable: false,
        height: 300,
        width: 500,
        autoOpen: false,
        modal: true,
        buttons: {
            "Modificar": function() {

                $.post(
                        "<?php echo Class_config::get('urlApp') ?>/?controller=User&&action=Update",
                        {pkUser: $('#txtModifyUser_id').val(), fkTypeUser: $('#txtModifyUserType').val()},
                function(data) {

                    $("#dialog-form").dialog("close");

                    _listUser();

                }

                );

            },
            "Cancelar": function() {
                $(this).dialog("close");
            }
        }
    });
    //            $("#dialoRegister").dialog({
    ////                resizable: false,
    //                height: 300,
    //                width: 500,
    //                autoOpen: false,
    //                modal: true,
    //                buttons: {
    //                    "Cancelar": function() {
    //                        $(this).dialog("close");
    //                    }
    //                }
    //            });

    $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=TypeUser&&action=List', function(data) {
        $('#txtModifyUserType option').remove();
        for (var i = 0; i < data.length; i++) {

            //                    $('#cmbRegisterTypeUser').append("<option value=\"" + data[i].pkTypeUser + "\">" + data[i].description + "</option>")
            $('#txtModifyUserType').append("<option value=\"" + data[i].pkTypeUser + "\">" + data[i].description + "</option>")
            //                    $('#cmbTypeUserModifyPermissions').append("<option value=\"" + data[i].pkTypeUser + "\">" + data[i].description + "</option>")
        }

    });

</script>
<script>
    $(function() {
        $("#beginning").button({
            text: false,
            icons: {
                primary: "ui-icon-disk"
            }
        });
        $("#rewind").button({
            text: false,
            icons: {
                primary: "ui-icon-seek-prev"
            }
        });
        $("#play").button({
            text: false,
            icons: {
                primary: "ui-icon-play"
            }
        })
                .click(function() {
                    var options;
                    if ($(this).text() === "play") {
                        options = {
                            label: "pause",
                            icons: {
                                primary: "ui-icon-pause"
                            }
                        };
                    } else {
                        options = {
                            label: "play",
                            icons: {
                                primary: "ui-icon-play"
                            }
                        };
                    }
                    $(this).button("option", options);
                });
        $("#stop").button({
            text: false,
            icons: {
                primary: "ui-icon-stop"
            }
        })
                .click(function() {
                    $("#play").button("option", {
                        label: "play",
                        icons: {
                            primary: "ui-icon-play"
                        }
                    });
                });
        $("#forward").button({
            text: false,
            icons: {
                primary: "ui-icon-seek-next"
            }
        });
        $("#end").button({
            text: false,
            icons: {
                primary: "ui-icon-seek-end"
            }
        });
        $("#shuffle").button();
        $("#repeat").buttonset();
    });
</script>


<!--</section>-->

