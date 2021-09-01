<?php

class Application_Controllers_EmpresaController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {
            case 'ListAction':
                $this->_ListarEmpresa();
                break;
            case 'EditAction':
                $this->_Edit();
                break;
            case 'CloudAction':
                $this->_EnvioCloud();
                break;
        }
    }

    private function _ListarEmpresa() {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                $objView = new Application_Models_EmpresaModel();
                $objView->_ListEmpresa();
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

    private function _EnvioCloud(){
        error_reporting(E_ALL);
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {
                //Me La sudó
            }
        }
    }

    private function _Edit() {
        error_reporting(E_ALL);
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                if (isset($_FILES["img"])) {
                    $key = $_FILES["img"];
                    $tipo = 0;
                    $ruta = "Public/images/";
                    $tipo_imagen = $key['type'];
                    if (strpos($tipo_imagen, "gif")) {
                        $tipo = 1;
                    } else {
                        if (strpos($tipo_imagen, "jpeg")) {
                            $tipo = 2;
                        } else {
                            if (strpos($tipo_imagen, "jpg")) {
                                $tipo = 2;
                            } else {
                                if (strpos($tipo_imagen, "png")) {
                                    $tipo = 3;
                                } else {
                                    $tipo = 0;
                                }
                            }
                        }
                    }
                    if ($tipo > 0) {
                        if (file_exists($ruta ."logo_empresa.png")) {
                            unlink($ruta ."logo_empresa.png");
                        }
                        $exito = 1;
                        $img_original = 0;
                        switch ($tipo) {
                            case 1:
                                $img_original = imagecreatefromgif($key["tmp_name"]);
                                break;
    
                            case 2:
                                $img_original = imagecreatefromjpeg($key["tmp_name"]);
                                break;
    
                            case 3:
                                $img_original = imagecreatefrompng($key["tmp_name"]);
                                break;
                        }
                        $ancho = imagesx($img_original);
                        $alto = imagesy($img_original);
                        //Se define el maximo ancho o alto que tendra la imagen final
                        $max_ancho = 450;
                        $max_alto = 450;
    
                        //Se calcula ancho y alto de la imagen final
                        $x_ratio = $max_ancho / $ancho;
                        $y_ratio = $max_alto / $alto;
    
                        //Si el ancho y el alto de la imagen no superan los maximos, 
                        //ancho final y alto final son los que tiene actualmente
                        if (($ancho <= $max_ancho) && ($alto <= $max_alto)) {//Si ancho
                            $ancho_final = $ancho;
                            $alto_final = $alto;
                        } /*
                         * si proporcion horizontal*alto mayor que el alto maximo,
                         * alto final es alto por la proporcion horizontal
                         * es decir, le quitamos al alto, la misma proporcion que 
                         * le quitamos al alto
                         * 
                        */
                        elseif (($x_ratio * $alto) < $max_alto) {
                            $alto_final = ceil($x_ratio * $alto);
                            $ancho_final = $max_ancho;
                        } /*
                         * Igual que antes pero a la inversa
                        */
                        else {
                            $ancho_final = ceil($y_ratio * $ancho);
                            $alto_final = $max_alto;
                        }
    
                        //Creamos una imagen en blanco de tamaño $ancho_final  por $alto_final .
                        $tmp = imagecreatetruecolor($ancho_final, $alto_final);
    
                        //Copiamos $img_original sobre la imagen que acabamos de crear en blanco ($tmp)
                        imagecopyresampled($tmp, $img_original, 0, 0, 0, 0, $ancho_final, $alto_final, $ancho, $alto);
    
                        //Se destruye variable $img_original para liberar memoria
                        imagedestroy($img_original);
    
                        //Se crea la imagen final en el directorio indicado
                        $ruta = $ruta ."logo_empresa.png";
                        imagepng($tmp, $ruta);
                    }
                }

                $_razonsocial=$_REQUEST['razonsocial'];
                $_nombre=$_REQUEST['nombre'];
                $_direccion=$_REQUEST['direccion'];
                $_ciudad=$_REQUEST['ciudad'];
                $_telefono=$_REQUEST['telefono'];
                $_ruc=$_REQUEST['ruc'];
                $_correos=$_REQUEST['correos'];
                $obj = new Application_Models_EmpresaModel();
                $obj->updateEmpresa($_razonsocial,$_nombre,$_direccion,$_ciudad,$_telefono,$_ruc,$_correos);
            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }

}
