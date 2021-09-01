<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

Class Application_Views_ConfigurationsView {

    public function __construct() {
        
    }

    public function showBuckups() {
        include_once 'Configurations/Buckups.php';
    }
    public function showUser() {
        include_once 'Configurations/User.php';
    }

    public function showReportarProblema(){
        include_once 'Configurations/ReportarProblemas.php';
    }
    public function showEmpresa(){
        include_once 'Configurations/Empresa.php';
    }
    public function showSistema(){
        include_once 'Configurations/Sistema.php';
    }
    public function showInformacion(){
        include_once 'Configurations/Informacion.php';
    }
    public function showTipos(){
        include_once 'Configurations/ShowTipos.php';
    }
    public function showCategoria(){
        include_once 'Configurations/ShowTipos.php';
    }
    public function showImpresion(){
        include_once 'Configurations/ShowTipos.php';
    }
    public function showAdminSalon(){
        include_once 'Configurations/Salones.php';
    }
    public function showAdminMensajes(){
        include_once 'Configurations/Mensajes.php';
    }
    public function showMesas(){
        include_once 'Configurations/ShowMesas.php';
    }     
    public function showMotivosAnulacion(){
        include_once 'Configurations/MotivoAnulacion.php';
    }     

}
