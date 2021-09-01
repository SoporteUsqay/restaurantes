  
<!--<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">-->
<h1>Bienvenido al Sistema Integrado Cueva Beach</h1>    
<!--<d></div>-->

<div class="row"> 
    <?php
    $db = new SuperDataBase();
    $objUserSystem = new UserLogin();
    $query_ = "SELECT pkModule, nameModule,url, rutaImagen from module m inner join accesmodule a on a.fkModule=m.pkModule where a.fkTypeUser=" . $objUserSystem->get_pkTypeUsernames();
    $resultListModule = $db->executeQuery($query_);
    while ($row = $db->fecth_array($resultListModule)) {
        echo "<div class='col-md-2'>";
//                echo '<li class="expandable"><div class="hitarea expandable-hitarea"></div>'
        echo '<a href="javascript:_onLoadPage(\'' . $row['url'] . '\',\'Index\')">';
        echo "<img src='Public/images/" . $row['rutaImagen'] . "' style='width: 150px; height: 150px;'>";
        echo "<br><center>".$row['nameModule']."</center>";
        echo "</a>";

        echo "</div>";

//                
//                if ($a == 1) {
//                    echo"</ul>";
//                }
//                echo '</li>';
    }
    ?>
</div>
<!--<h3><?php echo Class_message::get('TxtPerrmisionsForModule') ?></h3>-->
<!--    <table class="table table-striped">
    <thead> 
        <tr>
            <th style="width: 100px">
                Modulo 
            </th>
            <th style="width: 100px">
<?php echo Class_message::get('BtnList') ?>
            </th>
            <th style="width: 100px">
<?php echo Class_message::get('BtnRegister') ?>
            </th>
            <th style="width: 100px">
<?php echo Class_message::get('BtnModify') ?>
            </th>
            <th style="width: 100px">
                Eliminar
            </th>
        </tr>
    </thead>
    <tbody> 
<?php
$objUserSystem = new UserLogin();

$db = new SuperDataBase();

$query = "CALL sp_listModule(" . $objUserSystem->get_pkTypeUsernames() . ");";
//            echo $query;
$resul = $db->executeQuery($query);

while ($row = $db->fecth_array($resul)) {

    echo "<tr>";
    echo "<th>" . $row['nameModule'] . "</th>";
    if ($row[2] == "0") {
        $result = "<img src='Public/images/uncheck.jpg'>";
    } else if ($row[2] == "1") {
        $result = "<img src='./././Public/images/check.png'>";
    }
    if ($row[3] == "0") {
        $result2 = "<img src='./././Public/images/uncheck.jpg'>";
    } else if ($row[3] == "1") {
        $result2 = "<img src='./././Public/images/check.png'>";
    }

    if ($row[4] == "0") {
        $result3 = "<img src='./././Public/images/uncheck.jpg'>";
    } else if ($row[4] == "1") {
        $result3 = "<img src='./././Public/images/check.png'>";
    }
    if ($row[5] == "0") {
        $result4 = "<img src='./././Public/images/uncheck.jpg'>";
    } else if ($row[5] == "1") {
        $result4 = "<img src='./././Public/images/check.png'>";
    }
    echo "<th>" . $result . "</th>";
    echo "<th>" . $result2 . "</th>";
    echo "<th>" . $result3 . "</th>";
    echo "<th>" . $result4 . "</th>";
    echo "</tr>";
}
?>
    </tbody>
</table>-->

<!--</div>-->