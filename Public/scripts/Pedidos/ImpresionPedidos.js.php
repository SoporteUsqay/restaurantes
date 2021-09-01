<?php require_once '../../../Components/Config.inc.php'; ?>
//<script>
    var apellidos = "Castillo";
    var nombres = "Freicia";
    var myWindow = window.open('', '', 'width=1,height=1');
    myWindow.document.write("<table style='font-size:smaller; font-family:Comic Sans MS, cursive, sans-serif;'><tbody>");
    myWindow.document.write("<tr><center><h3>Almuerzo</h3></center></tr>");
    myWindow.document.write("<tr><center><h2>" + today + "</h2></center></tr>");
    myWindow.document.write("<tr><center><h1>" + numero + "</h1></center></tr>");
    myWindow.document.write("<tr><center><h2>" + apellidos
            + " " + nombres + "</h2></center></tr>");
    myWindow.document.write("</tbody></table>");
    //myWindow.document.write(today);
    //myWindow.document.write($("#trabajadorNombre").val());
    //myWindow.document.write("HOLA");

//   myWindow.document.close(); //missing code**
    //alert("despues de abrir");
    myWindow.focus();
    myWindow.print();
    myWindow.close(); 