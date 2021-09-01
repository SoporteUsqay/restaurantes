<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="../Public/Bootstrap/css/bootstrap.min.css">
    <style>
        * {
        font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
    }
    </style>
</head>
<body>
    <?php
        require_once '../Components/Config.inc.php';

        $db = new SuperDataBase();
         
    ?>

    <div class="container-fluid">

        <h1 class="text-center">Menú</h1>

        <?php
            $query = "select * from tipos where estado = 0 ";
            
            $res = $db->executeQueryEx($query);

            while($row = $db->fecth_array($res)) :
        ?>  

            <h3><?php echo $row['descripcion'] ?></h3>

            <?php 

                $query = "select * from plato where pktipo = ${row['pkTipo']}";

                $res1 = $db->executeQueryEx($query);

                while($row1 = $db->fecth_array($res1)) :
            ?>
                <p><?php echo $row1['descripcion'] ?></p>
                <p><?php echo number_format($row1['precio_venta'], 2) ?></p>
            <?php  endwhile ?>
        <?php  endwhile ?>
    </div>
</body>
</html>