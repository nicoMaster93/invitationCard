<?php 
        /**
         * Automatically generated script 
         * Creation Date: 2023-04-05 14:00:20 
         * Autor: Nicolas Hernandez 
         * version:1
         
         * Informacion: 
         * Esta clase contiene los servicios que se van a utilizar, inicialmente la clase contiene un servicio (mtodo) de ejemplo; 
         * el cual ayuda a entender el desarrollo de un servicio autodocumentado facilitando luego el uso del mismo.
         **/
        
        class GuestsController extends Controller{
            
            function __construct($postData){
                try{
                    parent::__construct($postData);
                    $this->table = "guests";
                }catch(Exception $e) {
                    return $this->response([],500,$e->getMessage());
                }
            }

            /**
             * Inicio de métodos privados
             * Los metodos privados sirven para realizar condiciones, consultas, etc;  que validen acciones internas
             * los métodos privados no se ven en la lista de servicios
             */
            private function saveDB($data){
                try{
                    /* Los metodos privados se ocultan de la lista del api */
                    /* Realizó la copia  */
                    copy(BASE . "db/guests.json", BASE . "db/guests-".date("Y-m-d").".json");
                    chmod(BASE . "db/guests-".date("Y-m-d").".json", 0777);
                    $newData = json_encode($data, JSON_PRETTY_PRINT);
                    unlink(BASE . "db/guests.json");
                    file_put_contents(BASE . "db/guests.json", $newData);
                    chmod(BASE . "db/guests.json", 0777);
                    return [true, "Se actualizó la db"];
                }catch(Exception $e) {
                    $erno = ["errorDB" => $e->getMessage()];
                    $this->logs(print_r($erno,1));
                    return [false, $erno];
                }
            }
            private function sendMail($data){
                try{
                    // Destinatario
                    $to = 'destinatario@example.com';
                    // Asunto
                    $subject = 'Correo electrónico con archivo adjunto';
                    // Mensaje
                    $message = 'Este es un correo electrónico con un archivo adjunto generado desde PHP.';
                    // Línea de separación
                    $semi_rand = md5(time());
                    // Cabeceras
                    $headers = 'From: remitente@example.com' . "\r\n" .
                            'Reply-To: remitente@example.com' . "\r\n" .
                            'Content-Type: multipart/mixed; boundary="PHP-mixed-'.$semi_rand.'"';
                    $mime_boundary = '==Multipart_Boundary_x'.$semi_rand.'x';
                    // Encabezado para el mensaje
                    $message = "This is a multi-part message in MIME format.\n\n".
                            "--$mime_boundary\n".
                            "Content-Type: text/plain; charset=us-ascii\n".
                            "Content-Transfer-Encoding: 7bit\n\n".
                            $message."\n\n";

                    // Adjuntamos el PDF al mensaje
                    $message .= "--$mime_boundary\n".
                                "Content-Type: application/pdf; name=\"reporte.pdf\"\n".
                                "Content-Disposition: attachment; filename=\"reporte.pdf\"\n".
                                "Content-Transfer-Encoding: base64\n\n".
                                chunk_split(base64_encode($pdf_data))."\n".
                                "--$mime_boundary--\n";

                    // Enviamos el correo electrónico
                    mail($to, $subject, $message, $headers);

                }catch(Exception $e) {
                    $erno = ["errorDB" => $e->getMessage()];
                    $this->logs(print_r($erno,1));
                    return [false, $erno];
                }
            }
            
            /**
             * Fin de los métodos privados
             */
        
            /**
             * Inicio de los métodos públicos
             * Los métodos públicos representan cada servicio al que pueden acceder desde el api
             */

            /**
            * @method getAllGuests Obtiene toda la informacion base de guests
            **/
            public function getAllGuests($select="t.*"){ 
                try{
                    /* Se agregan al arreglo los parametros requeridos para el funcionamiento del metodo */
                    /* optional_vars => Campos por lo cual se desea filtrar */
                    $paramRequired = array(
                        "optional_vars" => array(
                            "id" => "Id Guests"
                        )
                    );
                    $validMethods = ["GET"];
                    $erno = self::validParameters("getAllGuests",$paramRequired,"Obtiene el listado de Guests");
                    // validacion de parametros
                    $success = false;
                    if($erno["error"]){
                        throw new Exception($erno["msj"], 1);
                    }elseif(!in_array($this->params["requestMethod"],$validMethods)){
                        throw new Exception("El Método HTTP es incorrecto \nMétodos http request admitidos [" . implode(",", $validMethods) ."]", 1);
                    }else{
                        $post = array();
                        $data = file_get_contents(BASE . "db/guests.json", false, stream_context_create(['http' => ['header' => 'Connection: close']]));
                        $data = json_decode($data, true);
                        if(count($data)>0){
                            /* Valido si viene id  */
                            if(array_key_exists('id',$this->params) ){
                                if(isset($data[(int)$this->params['id']])){
                                    $data = $data[(int)$this->params['id']];
                                }else{
                                    return $this->response($data,400,"No hay registros");
                                }
                            }
                            /* Ordeno de manera ascendente  */
                            if(!array_key_exists('id',$this->params) ){
                                // usort($data, function ($a, $b) {
                                //     return strcmp($a["guest"], $b["guest"]);
                                // });
                            }

                            return $this->response($data,200);
                        }else{
                            return $this->response($data,400,"No hay registros");
                        }
                    }

                }catch(Exception $e) {
                    return $this->response([],500,$e->getMessage());
                }
            }
            /**
            * @method getAllGuests Obtiene toda la informacion base de guests
            **/
            public function createCompanioFromGuests(){ 
                try{
                    /* Se agregan al arreglo los parametros requeridos para el funcionamiento del metodo */
                    /* optional_vars => Campos por lo cual se desea filtrar */
                    $paramRequired = array(
                        "name" => "Name Companion",
                        "id" => "Id Guests"
                    );
                    $validMethods = ["POST"];
                    $erno = self::validParameters("getAllGuests",$paramRequired,"Crea un acompañante por invitado");
                    // validacion de parametros
                    $success = false;
                    if($erno["error"]){
                        throw new Exception($erno["msj"], 1);
                    }elseif(!in_array($this->params["requestMethod"],$validMethods)){
                        throw new Exception("El Método HTTP es incorrecto \nMétodos http request admitidos [" . implode(",", $validMethods) ."]", 1);
                    }else{
                        $post = array();
                        $data = file_get_contents(BASE . "db/guests.json", false, stream_context_create(['http' => ['header' => 'Connection: close']]));
                        $guests = json_decode($data, true);
                        if(count($guests)>0){
                            $replace = array(
                                'á' => 'a',
                                'é' => 'e',
                                'í' => 'i',
                                'ó' => 'o',
                                'ú' => 'u',
                                // Agrega aquí más caracteres especiales que quieras reemplazar
                            );
                            /* valido que tenga minimo 2 nombres */
                            if(count( explode(" ", trim($this->params['name']) ) ) < 2){
                                throw new Exception("Debes poner mínimo un nombre y un apellido o el nombre y complemento, para poder identificarlo mejor", 1);
                            }
                            /* Valido si existe el nombre dado */
                            $searchPattern = "/" .strtr($this->params['name'], $replace). "/i"; // Expresión regular para buscar los nombres que contengan "Garcia"
                            $filteredGuests = array_filter($guests, function($guest) use ($searchPattern, $replace) {
                                $guest['guest'] = strtr($guest['guest'], $replace);
                                $resp = preg_match($searchPattern, $guest['guest']);
                                if(!$resp){
                                    $resp = array_filter($guest['companions'], function($companions) use ($searchPattern, $replace) {
                                        $companions['guest'] = strtr($companions['guest'], $replace);
                                        return preg_match($searchPattern, $companions['guest']);
                                    });
                                }
                                return $resp;
                            });

                            if($filteredGuests){
                                foreach ($filteredGuests as $key => $value) {
                                    throw new Exception("Tu acompañante ya esta registrado con {$value['guest']}", 1);
                                }
                            }
                            /* En este punto ya puedo registrar el nuevo acompañante */
                            $companions = $guests[ (int) $this->params["id"] ]["companions"];
                            array_push($companions, ["guest" => $this->params['name'] ]);
                            /* Actualizo el json  */
                            $guests[ (int) $this->params["id"] ]["companions"] = $companions;
                            $this->saveDB($guests);
                            return $this->response([],200,"Tu acompañante Se almacenó correctamente");
                            
                        }else{
                            return $this->response($data,400,"No hay registros");
                        }
                    }

                }catch(Exception $e) {
                    return $this->response([],500,$e->getMessage());
                }
            }
            /**
            * @method getAllGuests Obtiene toda la informacion base de guests
            **/
            public function createMessageFromNosAssitance(){ 
                try{
                    /* Se agregan al arreglo los parametros requeridos para el funcionamiento del metodo */
                    /* optional_vars => Campos por lo cual se desea filtrar */
                    $paramRequired = array(
                        "message" => "Mensaje",
                        "id" => "Id Guests"
                    );
                    $validMethods = ["POST"];
                    $erno = self::validParameters("createMessageFromNosAssitance",$paramRequired,"Crear un mensaje");
                    // validacion de parametros
                    $success = false;
                    if($erno["error"]){
                        throw new Exception($erno["msj"], 1);
                    }elseif(!in_array($this->params["requestMethod"],$validMethods)){
                        throw new Exception("El Método HTTP es incorrecto \nMétodos http request admitidos [" . implode(",", $validMethods) ."]", 1);
                    }else{
                        $post = array();
                        $data = file_get_contents(BASE . "db/guests.json", false, stream_context_create(['http' => ['header' => 'Connection: close']]));
                        $data = json_decode($data, true);
                        if(count($data)>0){
                            /* Valido si viene id  */
                            $data[(int)$this->params['id']]['confirm_assistance'] = false;
                            $data[(int)$this->params['id']]['message_not_assistance'] = $this->params['message'];
                            $this->saveDB($data);
                            return $this->response([],200,"Gracias <b>" . $data[(int)$this->params['id']]['guest'] . "</b> por avisarnos.");
                        }else{
                            return $this->response($data,400,"No hay registros");
                        }
                    }

                }catch(Exception $e) {
                    return $this->response([],500,$e->getMessage());
                }
            }
            /**
            * @method deleteCompanion Obtiene toda la informacion base de guests
            **/
            public function deleteCompanion(){ 
                try{
                    /* Se agregan al arreglo los parametros requeridos para el funcionamiento del metodo */
                    /* optional_vars => Campos por lo cual se desea filtrar */
                    $paramRequired = array(
                        "id_guest" => "Id Invitado",
                        "id_compa" => "Id acompañamte"
                    );
                    $validMethods = ["DELETE"];
                    $erno = self::validParameters("deleteCompanion",$paramRequired,"Elimina el acompañante");
                    // validacion de parametros
                    $success = false;
                    if($erno["error"]){
                        throw new Exception($erno["msj"], 1);
                    }elseif(!in_array($this->params["requestMethod"],$validMethods)){
                        throw new Exception("El Método HTTP es incorrecto \nMétodos http request admitidos [" . implode(",", $validMethods) ."]", 1);
                    }else{
                        $post = array();
                        $data = file_get_contents(BASE . "db/guests.json", false, stream_context_create(['http' => ['header' => 'Connection: close']]));
                        $data = json_decode($data, true);
                        if(count($data)>0){
                            /* Valido si viene id  */
                            $dataCompanion = $data[(int)$this->params['id_guest']]["companions"];
                            if(isset($dataCompanion[(int)$this->params['id_compa']])){
                                unset($dataCompanion[(int)$this->params['id_compa']]);
                            }else{
                                throw new Exception("Hay un error con el acompañante que vas a eliminar, al parecer ya no existe", 1);
                            }
                            $dataCompanion = array_values($dataCompanion);
                            $data[(int)$this->params['id_guest']]["companions"] = $dataCompanion;
                            
                            $this->saveDB($data);
                            return $this->response("Se eliminó correctamente ",200);
                        }else{
                            return $this->response($data,400,"No hay registros");
                        }
                    }

                }catch(Exception $e) {
                    return $this->response([],500,$e->getMessage());
                }
            }
            /**
            * @method confirmAssistance Obtiene toda la informacion base de guests
            **/
            public function confirmAssistance(){ 
                try{
                    /* Se agregan al arreglo los parametros requeridos para el funcionamiento del metodo */
                    /* optional_vars => Campos por lo cual se desea filtrar */
                    $paramRequired = array(
                        "id" => "Id Invitado"
                    );
                    $validMethods = ["POST"];
                    $erno = self::validParameters("confirmAssistance",$paramRequired,"Confirmar Asistencia");
                    // validacion de parametros
                    $success = false;
                    if($erno["error"]){
                        throw new Exception($erno["msj"], 1);
                    }elseif(!in_array($this->params["requestMethod"],$validMethods)){
                        throw new Exception("El Método HTTP es incorrecto \nMétodos http request admitidos [" . implode(",", $validMethods) ."]", 1);
                    }else{
                        $post = array();
                        $data = file_get_contents(BASE . "db/guests.json", false, stream_context_create(['http' => ['header' => 'Connection: close']]));
                        $data = json_decode($data, true);
                        if(count($data)>0){
                            /* Valido si viene id  */
                            $data[(int)$this->params['id']]['confirm_assistance'] = true;
                            $this->saveDB($data);
                            return $this->response([],200,"Gracias <b>" . $data[(int)$this->params['id']]['guest'] . "</b> por confirmar.<br>Para nosotros es muy importante tu asistencia");
                        }else{
                            return $this->response($data,400,"No hay registros");
                        }
                    }

                }catch(Exception $e) {
                    return $this->response([],500,$e->getMessage());
                }
            }
            /**
            * Fin de los métodos públicos
            */
        
        
        }
         ?>