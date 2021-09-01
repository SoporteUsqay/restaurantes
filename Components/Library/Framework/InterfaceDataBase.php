<?php

/**
 * @author Jeison Smith Cruz Yesan
 * @copyright Ucv - Student
 */
interface InterfaceDataBase {

    /**
     * Metodo para Devolver una Conexion a Base de Datos
     */
    public function getConnection();

    /**
     * Devuelve el número de registros leidos por una consulta SQL
     */
    public function num_rows();

    /**
     * Devuelve el número de registros Afectados por una consulta SQL
     */
    public function affected_rows();

    /**
     * Devuelve el número de campos leidos por una consulta SQL
     */
    public function field_count();

    /**
     * Devuelve un registro como un array asociativo
     */
    public function fetch_array($result);

    /**
     * Ejecuta cualquier consulta SQL
     */
    public function executeQuery($query);

    /**
     * Cierra la conexion a la base de datos
     */
    public function close_connection();
}

?>
