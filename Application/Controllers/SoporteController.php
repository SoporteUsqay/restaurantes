<?php

class Application_Controllers_SoporteController {

    private static $session;

    public function __construct($action) {
        self::$session = Factory::buildObjectClass('Start_sesion');
        switch ($action) {

            case 'ShowAction':
                require_once 'Application/Views/Soporte/TicketSoporte.php';
                break;

            case 'SendTicketAction':
                $this->SendTicket();
                break;
        }
    }

    public function SendTicket()
    {
        if (self::$session->validateStartSesion()) {
            if (!self::$session->validateSesion()) {

                $trabajador_id = UserLogin::get_id();

                $db = new SuperDataBase();

                $query = "SELECT * FROM cloud_config";

                $res = $db->executeQueryEx($query);

                $configuracion = [];

                while ($row = $db->fecth_array($res)) {
                    $configuracion[$row['parametro']] = $row['valor'];
                }

                # Fill in the data for the new ticket, this will likely come from $_POST.
                $config = array(
                    'url'=> $configuracion['url_os_ticket'],
                    'key'=> $configuracion['key_os_ticket']); 
                    
                $data = array(
                        'name'      =>      $_POST['phone'] . " - " .$_POST['name'],
                        'email'     =>      $_POST['email'],
                        'subject'   =>      'RES - '.$_POST['subject'],
                        'message'   =>      $_POST['message'],
                        'ip'        =>      $configuracion['ip_publica_cliente_os_ticket'],                    
                        'attachments' => array(),
                );

                #pre-checks
                function_exists('curl_version') or die('CURL support required');
                function_exists('json_encode') or die('JSON support required');
                
                #set timeout
                set_time_limit(30);
                
                #curl post
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $config['url']);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_USERAGENT, 'osTicket API Client v1.7');
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Expect:', 'X-API-Key: '.$config['key']));
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                $result=curl_exec($ch);
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($code != 201){
                    // die('Unable to create ticket: '.$result);
                    echo(0);

                }else{
                    // $objSoporteTicket->setVar('id', NULL);
                    // $objSoporteTicket->setVar('email', $_POST['email']);
                    // $objSoporteTicket->setVar('name', $_POST['name']);
                    // $objSoporteTicket->setVar('phone', $_POST['phone']);
                    // $objSoporteTicket->setVar('topicId', $_POST['topicId']);
                    // $objSoporteTicket->setVar('subject', $_POST['subject']);
                    // $objSoporteTicket->setVar('message', $_POST['message']);
                    // $objSoporteTicket->setVar('id_usuario', $_COOKIE['id_usuario']);
                    // $objSoporteTicket->setVar('fecha_cierre', $configuracion['fecha_cierre']);
                    // $objSoporteTicket->setVar('fecha', date('Y-m-d H:i:s'));
                    // $objSoporteTicket->setVar('id_caja',$_COOKIE['id_caja']);            
                    // $objSoporteTicket->setVar('priorityId',1);
                    // $objSoporteTicket->setVar('estado_atencion', 0);
                    // $objSoporteTicket->setVar('estado_fila', 1);
                    // $objSoporteTicket->setVar('numero_ticket', "".(int) $result."");                
                    // $ids = $objSoporteTicket->insertDB();           
                    // //echo json_encode($ids);
                    $ticket_id = (int) $result;
                    // echo json_encode($ticket_id);

                    $query = "insert into ticket_soporte (email, fecha, nombre, problema, detalle_problema, trabajador_id, numero_ticket, telefono) values ";

                    $query .= "('${_POST['email']}', now(), '${_POST['name']}', '${_POST['subject']}', '${_POST['message']}', $trabajador_id, '$ticket_id', '${_POST['phone']}')";

                    $res = $db->executeQueryEx($query);

                    echo(1);
                }


            } else {
                self::$session->redirect();
            }
        } else {
            self::$session->redirect();
        }
    }
}