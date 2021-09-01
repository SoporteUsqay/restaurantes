jQuery.fn.reset = function () {
    $(this).each(function () {
        this.reset();
    });
};


$(document).ready(function () {
    history.pushState(null, "", 'configuracion_impresion.php');
    $('#tb').DataTable();
});
