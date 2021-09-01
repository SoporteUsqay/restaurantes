<?php

class WebService {

    public function get_precioDolar() {
        $moneda1 = array("nombre" => "Euros", "registro" => "eur"); //creo este array para que te des cuenta, el euro en este ws se denomina eur 
        $moneda2 = array("nombre" => "Dolares americanos", "registro" => "usd"); //dolar = usd 
        $cuanto = 1; //Para el ejemplo, un euro cuantos USD ?¿? 
        require('lib/nusoap.php'); //cargamos la librería nusoap 
        $cliente = new soapclient('http://xurrency.com/api.wsdl', 'wsdl'); //conexion con el ws del que queremos consumir 
        $err = $cliente->getError();
        if ($err)
            exit();
        $proxy = $cliente->getProxy();
        $resultado = $proxy->getValue($cuanto, $moneda1['registro'], $moneda2['registro']); //Por lo que he visto este es el método que hace la conversión 
        if (!$cliente->getError())
            echo $cuanto . " " . $moneda1['nombre'] . " son " . $resultado . " " . $moneda2['nombre']; //Lo mostramos 
        else
            echo $cliente->getError();
    }

}
