<?php

class Application_Models_TestConexionModel {

    private $abierta;
    private $host;
    private $usuario;
    private $contra;
    private $dbCreate;
    
    public function __construct()
    {
        $this->host = Class_config::get('hostNameDataBase');
        $this->usuario = Class_config::get('userDataBase');
        $this->contra = Class_config::get('passwordDataBase');
        $this->dbCreate = Class_config::get('nameDataBase');
    }

    public function getConnection()
    {
        return $this->abierta;
    }

    // Connect function for database access
    function conexion_cotos($usr,$pw,$host) {
        try {
            $this->abierta = new mysqli($this->host,$this->usuario,trim($this->contra));            
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }

    public function test_create(){
        try{
            $this->conexion_cotos($this->usuario,$this->contra,$this->host);
            // aqui verificare si existe la base de datos y hate you mdfk            
            $res = $this->isset_bd();
            if($res===100){
                // echo("existe la base de datos");
                // echo(100);
                return 100;
            } else if($res===-100){
                echo("no existe la base de datos, espere a que la BD se cree.");
                echo("Ejecutando....");
                $createDatabase = $this->createDatabase();
                if($createDatabase===-101){
                    //echo(-101);
                    echo("no se pudo crear la base de datos");
                    return -101;
                }
                if($createDatabase===-102){
                    //echo(-102); // SE ENCONTRO UN ERROR AL EJECUTAR EL SCRIPT                    
                    echo("SE ENCONTRÓ UN ERROR AL EJECUTAR EL SCRIPT");
                    return -102;
                }
                if($createDatabase===1){
                    echo "<script>location.reload();</script>";
                    return 1;  // SE EJECUTO CORRECTAMENTE
                    //return -101;
                }
                
            }
        } catch (Exception $e) {            
            return 0; // LA CONEXION NO SE PUDO ESTABLECER
            //return 0;
        }
    }

    public function isset_bd(){
        $sql = "SELECT IF('$this->dbCreate' IN(SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA), 1, 0) AS found";
        $isset = $this->consulta_arreglo($sql);
        $find=$isset[0];
        if($find==1){
            return 100;
        }else{
            // return $this->createDatabase();
            return -100;
        }        
    }

    public function createDatabase(){
        $sql = 'CREATE DATABASE '.$this->dbCreate.' ';
        if ($this->abierta->query($sql)) {
            // echo "La base de datos ".$this->dbCreate." se creó correctamente";
            $runScript = $this->runScript();
            if($runScript===-1){
                return -102; // SE ENCONTRO UN ERROR AL EJECUTAR EL SCRIPT
            }
            if($runScript===1){
                return 1;
            }
        } else {
            return -101; // NO SE PUDO CREAR LA BASE DE DATOS
        }
    }

    public function runScript(){
        // Aquí tendrás que poner tus datos de conexión a la base de datos y la tabla en cuestión.
        $conx = mysqli_connect($this->host, $this->usuario, $this->contra, "$this->dbCreate") or die('Error al conectar');
        //  $conx = $this->abierta;    
        $fichero = 'restaurantes_back.sql'; // Ruta al fichero que vas a cargar.
        
        //$fichero = file_get_contents('./pos_prueba.sql', FILE_USE_INCLUDE_PATH);

        // Linea donde vamos montando la sentencia actual
        $temp = '';

        // Flag para controlar los comentarios multi-linea
        $comentario_multilinea = false;

        // Leemos el fichero SQL al completo
        $lineas = file($fichero);

        // Procesamos el fichero linea a linea
        foreach ($lineas as $linea) {

            $linea = trim($linea); // Quitamos espacios/tabuladores por delante y por detrás

            // Si es una linea en blanco o tiene un comentario nos la saltamos
            if ( (substr($linea, 0, 2) == '--') or (substr($linea, 0, 1) == '#') or ($linea == '') )
                continue;

            // Saltamos los comentarios multilinea /* texto */ Se detecta cuando empiezan y cuando acaban mediante estos dos ifs
            if ( substr($linea, 0, 2) == '/*' ) $comentario_multilinea = true;

            if ( $comentario_multilinea ) {
            if ( (substr($linea, -2, 2) == '*/') or (substr($linea, -3, 3) == '*/;') ) $comentario_multilinea = false;
            continue;
            }

            // Añadimos la linea actual a la sentencia en la que estamos trabajando 
            $temp .= $linea;

            // Si la linea acaba en ; hemos encontrado el final de la sentencia
            if (substr($linea, -1, 1) == ';') {
                // Ejecutamos la consulta
                // mysqli_query($conx, $temp) or print('<strong>Error en la consulta</strong> \'' . $temp . '\' - ' . mysqli_error($conx) . "<br /><br />\n");
                $res=mysqli_query($conx, $temp);
                if(!$res){
                    return -1;
                }
                /* $res=mysqli_query($conx, $temp);
                if(!$res){

                    return -1;
                } */
                // Limpiamos sentencia temporal
                $temp = '';
            }
        }
        return 1;
    }

    public function consulta_arreglo($consulta)
    {
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

}