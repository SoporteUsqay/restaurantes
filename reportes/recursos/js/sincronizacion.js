jQuery.fn.reset = function () {
    $(this).each(function () {
        this.reset();
    });
};


$(document).ready(function () {
    history.pushState(null, "", 'sincronizacion_almacen.php');
    $('#tb').dataTable();
});
