<!DOCTYPE html>
<html lang="es">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../../../Public/Bootstrap/css/bootstrap.css" rel="stylesheet">
        <script type="text/javascript" src="../../../Public/jquery-ui-1.10.4.custom/js/jquery-1.10.2.js"></script>
        <script type="text/javascript" src="../../../Public/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.js"></script>

        <script  type="text/javascript" src="../../../Public/Bootstrap/js/bootstrap.min.js"></script>
        <link rel="stylesheet" type="text/css" href="../../../Public/css/style.css">
        <!--            <link rel="stylesheet" href="../Public/Bootstrap/css/bootstrap.min.css">
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
                    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>-->
    </head>
    <body>
        <div class=" container">
            <form class="prom row">

                <div class=" col-md-12">  </div>  
                <div class=" row">
                    <div class=" col-md-6">
                        <div class="col-md-3">
                            <label>NOMBRE PROMOCION</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="inpus">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="col-md-3">
                            <label>PRECIO</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text">
                        </div>
                    </div>
                </div>

            </form>
            <form class="row">
                <br >
                <div class="col-md-12">  </div>  
                <div class="row">
                    <div class=" col-md-6">
                        <div class="col-md-3">
                            <label>BUSCAR PRODUCTO</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" list="BuscaProducto"/>
                            <datalist id="BuscaProducto">
                                <option>Carlos</option>
                                <option>Manuel</option>
                                <option>Fernando</option>
                                <option>Carmona</option>
                                <option>Feria</option>
                            </datalist>
                        </div>
                    </div>
                    <div class="col-md-6">

                        <button> AGREGAR</button>
                    </div>
                </div>
            </form>
            <form class="row">
                <br>
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tr>
                            <th>Id</th>
                            <th>Descripcion</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>Pollito</td>
                            <td>55.00</td>
                            <td>1</td>
                        </tr>
                    </table>
                </div>  
            </form>

        </div>
    </body>
</html>
