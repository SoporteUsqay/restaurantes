<?php

/* Clase Conexion Maestra
 * Por Gino Lluen 05 de julio del 2012
 * Usada en KamiCRUD desde 12 de mayo del 2013
 * Actualizada a Mysqli (al fin) 08 de diciembre del 2016
 */

$email_user = "reportes@usqay-cloud.com";
$email_password = "xclvidizybcaywvw";

class MasterConexion {

    private $abierta;
    private $host = "localhost";
    private $usuario = "root";
    private $contra = "";
    private $base = "restaurantes";
    
    public function getConnection(){
        return $this->abierta;
    }

    public function __construct()
    {
        $this->abierta = new mysqli($this->host,$this->usuario,$this->contra,$this->base);
        if ($this->abierta->connect_errno) {
            die("Mysql error csm : (" . $this->abierta->mysqli_connect_errno() . ") " . $this->abierta->mysqli_connect_error());
        }
    }

    public function consulta_simple($consulta) {
        if ($this->abierta->query($consulta) === TRUE) {
            return 1;
        } else {
            return 0;
        }
    }

    public function consulta_id($consulta) {
        if ($this->abierta->query($consulta) === TRUE) {
            $mah_id = $this->abierta->insert_id;
            return $mah_id;
        } else {
            return 0;
        }
    }

    public function consulta_afectados($consulta) {
        if ($this->abierta->query($consulta) === TRUE) {
            $mah_opens = $$this->abierta->affected_rows;
            return $mah_opens;
        } else {
            $this->abierta->close();
            return 0;
        }
    }

    public function consulta_cantidad($consulta) {
        if ($resultado = $this->abierta->query($consulta)) {
            $mah_result = $resultado;
            $resultado->free();
            return $mah_result->num_rows;
        } else {
            return 0;
        }
    }

    public function consulta_arreglo($consulta) {
        $resultado = $this->abierta->query($consulta);
        if ($resultado) {
            if ($deshilachado = $resultado->fetch_array(MYSQLI_BOTH)) {
                $resultado->free();
                return array_map('utf8_decode', $deshilachado);
            } else {
                $resultado->free();
                return 0;
            }
        } else {
//            $this->abierta->close();
            return 0;
        }
    }

    public function consulta_arreglo_ex($consulta) {
        $resultado = $this->abierta->query($consulta);
        if ($resultado) {
            if ($deshilachado = $resultado->fetch_array(MYSQLI_BOTH)) {
                $resultado->free();
                return array_map('utf8_encode', $deshilachado);
            } else {
                $resultado->free();
                return 0;
            }
        } else {
//            $this->abierta->close();
            return 0;
        }
    }

    public function consulta_matriz($consulta) {
        $matriz = array();
        $resultado = $this->abierta->query($consulta);
        if ($deshilachar = $resultado->fetch_array(MYSQLI_BOTH)) {
            $matriz[0] = array_map('utf8_decode', $deshilachar);
            $i = 1;
            while ($deshilachar = $resultado->fetch_array(MYSQLI_BOTH)) {
                $matriz[$i] = array_map('utf8_decode', $deshilachar);
                $i = $i + 1;
            }
            $resultado->free();
            return $matriz;
        } else {
            return 0;
        }
    }

    public function consulta_matriz_ex($consulta) {
        $matriz = array();
        $resultado = $this->abierta->query($consulta);
        if ($deshilachar = $resultado->fetch_array(MYSQLI_BOTH)) {
            $matriz[0] = array_map('utf8_encode', $deshilachar);
            $i = 1;
            while ($deshilachar = $resultado->fetch_array(MYSQLI_BOTH)) {
                $matriz[$i] = array_map('utf8_encode', $deshilachar);
                $i = $i + 1;
            }
            $resultado->free();
            return $matriz;
        } else {
            return 0;
        }
    }

    function __destruct() {
        $this->abierta->close();
    }

}
