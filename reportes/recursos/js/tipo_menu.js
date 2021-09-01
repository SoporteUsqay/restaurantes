jQuery.fn.reset = function () {
    $(this).each(function () {
        this.reset();
    });
};


$(document).ready(function () {
    history.pushState(null, "", 'tipo_menu.php');
});
