<?php
/**
 * @author: Miguel Angel Vásquez Jiménez
 * @copyright: JEMASoft
 */
Class db_Mysql implements InterfaceDataBase{

    private $_connection;

    /**
     * Devuelve una conexion MySQL, con los parametro del archivo config
     * @return Connection
     */
    public function getConnection() {
        $host = Class_config::get('hostNameDataBase');
        $pasword = Class_config::get('passwordDataBase');
        $user = Class_config::get('userDataBase');
        $dataBase = Class_config::get('nameDataBase');
        
        $this->_connection = new mysqli($host, $user, trim($pasword), $dataBase);
//        die('llego a conectar');
        if(mysqli_connect_errno()){
            die('Error al conectar a la base de datos');
        }else{
            mysqli_set_charset($this->_connection, 'utf8'); // <- add this too
            mysqli_query($this->_connection, "SET NAMES 'utf8';");
            mysqli_query($this->_connection, "SET CHARACTER SET 'utf8';");
            mysqli_query($this->_connection, "SET COLLATION_CONNECTION = 'utf8_spanish2_ci';");
        }
        
        return $this->_connection;
    }

    /**
     * Devuelve la cantidad de registros devueltas por una consulta SQL de lectura
     * @return int
     */
    public function num_rows() {
        return $this->_connection->affected_rows;
    }

    /**
     * Devuelve el número de registros afectados por una consulta SQL de escritura
     * @return int
     */
    public function affected_rows() {
        return $this->_connection->affected_rows;
    }

    /**
     * Devuelve el número de campos devueltos por la consulta SQL de lectura
     * @return int
     */
    public function field_count(){
        return $this->_connection->field_count;
    }

        /**
        * Navega el Resulset devuelto por la consulta SQL
        * @param Array $result
        * @return Array
        */
    public function fetch_array($result) {
        return $result->fetch_array();
    }

    /**
     * Ejecuta una consulta SQL
     * @param String $query
     * @return resulSet
     */
    public function executeQuery($query) {
        return $this->_connection->query($query);
    }
    
    /**
     * Cierra la conexion MySQL
     */
    public function close_connection() {
       $this->_connection->close();
    }
    public function getId(){
        return $this->_connection->insert_id;
    }

    /**
     * Devulve una consulta
     * @param int $case
     * @param String $nameProcedure
     * @param Array $arrayParameters
     * @return string
     */
    public function concatScriptSQL($case = 1, $nameProcedure = null, $arrayParameters = null){
        if($case == 1)
            $sql = 'CALL ';
        else
            $sql = 'SELECT ';

        $sql =  $sql . Class_Configuration::get('prefixDB') . '_' . $nameProcedure . '(';

        if(!is_null($arrayParameters)){
            foreach ($arrayParameters as $parameter)
                $sql = $sql . "'$parameter'" . ',';
            $sql = substr($sql, 0, strlen($sql) - 1);
        }
        $sql = $sql . ')';
        return $sql;
    }
}
