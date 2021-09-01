<?php

/**
 * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>
 * @access public
 * @version 1.0
 * @copyright (c) 2014, Jeison Cruz Yesan
 * * */class Class_App {

    /**
     * @access public getIp
     * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>
     * @return string * 
     */
    public static function getIp() {
        $ip = getenv('REMOTE_ADDR');
        if($ip=="::1"){
            $ip="localhost";
        }
        return $ip;
    }

    /**
     * @access public getNameComputer
     * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>
     * @return string * 
     */
    public static function getNameComputer() {
        $nameComputer = gethostbyaddr($_SERVER['REMOTE_ADDR']);
        return $nameComputer;
    }

    /**
     * @access public getAuthor
     * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>
     * @return string  
     */
    public static function getAuthor() {
        return "Jeison Cruz";
    }

    /**
     * @access public getModule
     * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>
     * @return array * 
     */
    public static function getModule() {
        $db = new SuperDataBase();
        $query = "call sp_listModule()";
        $result2 = $db->executeQuery($query);
        $_array = array();
        while ($row = $db->fecth_array($result2)) {
            $_array = array(
                $row[1],
                $row[2],
                $row[3],
                $row[4],
                $row[5],
            );
        }
        return $_array;
    }

    /**
     * @access public generatePassword
     * @author Jeison Cruz Yesan <jcruzyesan@gmail.com>
     * @return String password* 
     */
    public static function generatePassword() {

        $pass = substr(sha1(microtime()), 1, 15);
        return $pass;
    }

    public static function setUser($user) {
        
    }

}
