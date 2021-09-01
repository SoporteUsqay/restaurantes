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
        <h2>
            Copia y restauracion de base de datos

        </h2>
        <div class="row">
            <div class="col-lg-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Importar</h3>
                    </div>
                    <div class="panel-body">
                        <input type="radio" > Importar un archivo<br>
                        Busca en su ordenador: <input type="file">
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Exportar</h3>

                    </div>
                    <div class="panel-body">
                        <form>
                            <input name="export" checked="true" type="radio" > Guardar como archivo<br>
                            <input name="export" type="radio" > Guardar y enviar a un email<br>
                        </form>
                        <a href="buckup.php" target="_blank" class="btn btn-danger" >Continuar</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script type="text/javascript" src="Application/Views/Configurations/js/buckups.js.php" ></script>
    <?php
    $objViewMenu = new Application_Views_IndexView();
    $objViewMenu->showFooter();
    ?>

</body>
</html>
