<?php
/**
 * @author: Miguel Angel Vásquez Jiménez
 * @copyright: JEMASoft
 */
Class FrontController{

    private static $_instance;
    
    private $_controller;
    private $_action;


    /**
     * Constructor que define el controlador y la accion  que se ejecutaran
     */
    public function  __construct() {
        $this->_controller = $this->getController();
        $this->_action = $this->getAction();
    }

    /**
     * Retorna una instancia de la clase 
     * @return Class_FrontController
     */
    public static function getInstance(){
        if(!isset(self::$_instance)){
            self::$_instance = new self();
        }
        else
            self::$_instance->__construct();
        
        return self::$_instance;
    }

    /**
     * Retorna el nombre de la clase controladora
     * @return string
     */
    public function getController(){
        if(isset($_GET['controller']))
            $controller = 'Application_Controllers_'.$_GET['controller'].'Controller';
        else
            $controller = 'Application_Controllers_IndexController';
        
        return $controller;
    }

    /**
     * Retorna la accion a ejecutar de la clase controladora
     * @return string 
     */
    public function getAction(){
        if(isset($_GET['action']))
            $action = $_GET['action'].'Action';
        else
            $action = 'ShowLoginAction';
        
        return $action;
    }

    /**
     * Rutea la direccion URL, es decir crea una instancia de la clase controladora
     * y le pasa la accion como parametro
     */
    public function route(){
        if(class_exists($this->_controller)){
            $objController = new $this->_controller($this->_action);
        }
            
        else
            Class_Exception::messageException('Undefined: ' + $this->_controller, 8);
    }

    /**
     * Identifica que tipo de URL es la utilizada
     * de acuerdo al tipo asigna el cotrolador y la accion asi como los parametros
     */
    
    private function _parseURL(){       
        $stringURL = $_SERVER['REQUEST_URI'];

        $flag = strpos($stringURL, '?');
        $flag = strpos($stringURL, '&');
        $flag = strpos($stringURL, '=');

        if(empty($flag)){
            $hay = 0;
                 
            $arrayToken = str_getcsv($stringURL, '/');

            if(!empty($arrayToken[3])){
                Class_SendVars::setVarOnArrayGET('controller', $arrayToken[3]);
                Class_SendVars::setVarOnArrayGET('action', $arrayToken[4]);
            }

            foreach ($arrayToken as $key => $var){
                if($key >= 5 and ($key % 2) <> 0){
                    Class_SendVars::setVarOnArrayGET($var, $arrayToken[$key + 1]);
                }
            }
        }
    }
}
