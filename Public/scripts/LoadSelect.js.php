<?php require_once '../../Components/Config.inc.php'; ?>
//<script>
    $(window).load(function() {

        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=TypeUser&&action=List', function(data) {
            for (var i = 0; i < data.length; i++) {

                $('#cmbRegisterTypeUser').append("<option value=\"" + data[i].pkTypeUser + "\">" + data[i].description + "</option>")
                $('#txtModifyUserType').append("<option value=\"" + data[i].pkTypeUser + "\">" + data[i].description + "</option>")
                $('#cmbTypeUserModifyPermissions').append("<option value=\"" + data[i].pkTypeUser + "\">" + data[i].description + "</option>")
            }

        });
        _listUser();

    });
    function _listUser() {
        $('#tableListUser tbody th').remove();
        $.getJSON('<?php echo Class_config::get('urlApp') ?>/?controller=User&&action=List', function(data) {

            var $objTabla = $('#tableListUser'),
                    //contamos la cantidad de columnas que tiene la tabla
                    iTotalColumnasExistentes = $('#tblTabla thead tr th').length;
            var result = '';
//                setTimeout("document.location=document.location", 9000);
            for (var i = 0; i < data.length; i++) {
                var namesUser = data[i].lastName + ' ' + data[i].names;

                $('<tr>').html('<th>' + namesUser + '</th>' +
                        '<th>' + data[i].user + '</th>' +
                        '<th>' + data[i].description + '</th>' +
                        '<th><a href="javascript:_openModifyUser(' + data[i].id + ',\'' + namesUser + '\', \'' + data[i].user + '\',' + data[i].pkTypeUser + ')"><span class="ui-icon ui-icon-trash"></span> </a></th>' +
                        '<th> <a href="javascript:_deleteUser(' + data[i].id + ')"><span class="ui-icon ui-icon-trash"></span></a></th>'
                        ).appendTo($objTabla.find('tbody'));

            }

        });

    }

    function  _onLoadPage(name, url) {
//        $('#loadBody').html('<img src="<?php echo Class_config::get('urlApp') ?>/Public/images/preload.gif" />procesando...');
//        setTimeout(function() {
            var url1 = "<?php echo Class_config::get('urlApp') ?>/?controller=" + name + "&&action=Show" + url;
            $("#contentIndex").load(url1);
//            _listUser();
//            $('#loadBody').html("");
//        }, 2)



//        console.log(name+url);
    }



