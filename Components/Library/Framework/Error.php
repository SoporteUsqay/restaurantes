<?php 
Class Class_Error{
    
    public static function messageException($message, $file){
        die( $message . " Archivo: ". $file);
        
    }
    
    
}