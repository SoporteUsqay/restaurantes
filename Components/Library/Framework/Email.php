<?php

class Class_Email {

    private $_emailSend = array();
    private $_subject;
    private $_message;
//    private $_headers;
    private $_emailGo;
    private $_nameUser;

    function __construct() {
        
    }

    public function get_emailSend() {
        return $this->_emailSend;
    }

    public function get_subject() {
        return $this->_subject;
    }

    public function get_message() {
        return $this->_message;
    }

//    public function get_headers() {
//        return $this->_headers;
//    }

    public function get_emailGo() {
        return $this->_emailGo;
    }

    public function set_emailSend($_emailSend) {
        $this->_emailSend = $_emailSend;
    }

    public function set_subject($_subject) {
        $this->_subject = $_subject;
    }

    public function set_message($_message) {
        $this->_message = $_message;
    }

//    public function set_headers($_headers) {
//        $this->_headers = 'From:' . $_headers . "\r\n";
//    }

    public function set_emailGo($_emailGo) {
        $this->_emailGo = $_emailGo;
    }

    public function get_nameUser() {
        return $this->_nameUser;
    }

    public function set_nameUser($_nameUser) {
        $this->_nameUser = $_nameUser;
    }

    public function _sendEmail() {
        $emailSends="";
//        die($this->_emailSend);
        foreach ($this->_emailSend as $valor) {

            if (empty($emailSends)==true) {
                $emailSends = $emailSends . $valor;
            } else {
                $emailSends = $emailSends . ', ' . $valor;
            }
           
        }
        $header='From:' . $this->_emailGo . "\r\n";
//         echo $emailSends.$this->_subject.$this->_message.$header;
        if (mail($emailSends, $this->_subject, $this->_message, $header)) {
            echo true;
        } else {
            echo false;
        }
    }

}
