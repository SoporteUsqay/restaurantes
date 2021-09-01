<?php
Class Class_message{
    public static function get($key){
        
     global $message;
        
        if (!empty($key) and !empty($message[$key])){
            return $message[$key];           
                    }
        else 
            die ("Error, Este Mensaje o existe ".$key);
    }
    
    
}


















/*
 * 
 */


//Class Class_Message {
//
//    public static function get($key) {
//        global $messages;
//        if (!empty($key) and !empty($messages[$key])) {
//
//            return $messages[$key];
//        }
//        else
//            die( $messages[$key]. 'El Mensaje no Existe');
//    }

//}