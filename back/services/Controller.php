<?php 
        /**
         * Automatically generated script 
         * Creation Date: 2023-04-05 14:00:20 
         * Autor: Nicolas Hernandez 
         * version:1
         
         * Informacion: 
         * Esta clase contiene los metodos que se van a utilizar en los servicios que se generan, con el fin de validar parametros, logs y respuestas http.
         **/
        
         class Controller{
            protected $param;
            protected $model;
            protected $table;
            protected $jsonBody;
            function __construct($postData){
                try{
                    $this->params = $postData;
                    $jsonBody = json_decode(file_get_contents("php://input"),true);
                    if(is_array($jsonBody)){
                        if(count($jsonBody) > 0 ){
                            $this->params = array_merge($this->params, $jsonBody);
                        }
                    }
                    $this->logs(print_r($this->params,1));   
                }catch(Exception $e) {
                    return $this->response([],500,$e->getMessage());
                }
            }
            protected function validParameters($accion="",$parametrosValidos = array(),$description=""){
                $req = array();
                if(!empty($description)){
                    $description = "\n\n<h4>Descripción de la acción</h4>\n\n<p>$description</p>";
                }
                $ernoTitle = $description."\n\n<h4>Parametros de la acción <b>[ $accion ]</b></h4><p>Estos son los parametros requeridos</p>\n";
                $error = false;
                
                foreach ($parametrosValidos as $key => $value) {
                    if(is_array($value) && array_key_exists("optional_vars",$parametrosValidos)){
                        $errorParam = "\n\n<h4> Parametros Opcionales  <b>[ $accion ]</b> </h4>\n";
                        $errorParam .= "\n<li> @Param [debugAction] => boolean (true/false) || Testea el servicio en ambiente de pruebas, aplica unicamente para los servicios que: [Inserten, Actualicen o Eliminen]. </li>";
                        array_push($req,$errorParam);
                        foreach ($value as $ko => $vo) {
                            $errorParam = "\n<li> @Param [$ko] =>  $vo . </li>";
                            array_push($req,$errorParam);
                        }
                    }else{
                        if(!array_key_exists($key,$this->params)){
                            $error = true;
                        }
                        if(empty($value)){
                            $value = "Campo Obligatorio";
                        }
                        $errorParam = "\n<li> @Param [$key] => $value . </li>";
                        array_push($req,$errorParam);
                        
                    }
                }
                if($error){
                    $resp = array("error" => $error, "msj" => $ernoTitle.implode("",$req));
                }else{
                    $resp = array("error" => $error);
                }
                return $resp;
            }
            protected function addOptionalVarsData($post,$key){
                try{
                    /**
                     * agrego los valores que vienen como opcional y devuelvo el arreglo con la posicion nueva
                     */
                    $keyParam = $keyPost = $key;
                    if(is_array($key)){
                        $keyParam = $key[0];
                        $keyPost = $key[1];
                    }
                    if(array_key_exists($keyParam,$this->params) && !empty($this->params[$keyParam]) ){
                        $post[$keyPost] = $this->params[$keyParam];
                    }
                    return $post;
                }catch(Exception $e) {
                    return $this->response([],500,$e->getMessage());
                }
            }
            protected function sendDataCurl($body,$headers=array()){
                try{
                    /**
                     * Envia datos por curl el @param body es un array [url => "", data => ""]
                     */
                    if(count($headers) == 0){
                        $headers = array(
                            "Content-Type: application/json"
                        );
                    }
                    $ch = curl_init();
                    curl_setopt( $ch,CURLOPT_URL, $body["url"]);
                    curl_setopt( $ch,CURLOPT_POST, true );
                    curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
                    curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
                    curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
                    curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $body["data"] ) );
                    $result = curl_exec($ch );
                    curl_close( $ch );
                    return json_decode($result,true);
                }catch(Exception $e) {
                    return $this->response([],500,$e->getMessage());
                }
            }
            protected function logs($msj=""){
                if(empty($msj)){
                    $msj = "[".date("Y-m-d H:i")."] Parametros recibidos\n" . print_r($this->params,1);
                    $msj .= "\n[".date("Y-m-d H:i")."] Headers recibidos\n" . print_r(getallheaders(),1);
                }else{
                    $msj = "\n[".date("Y-m-d H:i")."] {$msj}";
                }
                $fileName = BASE . "logs/logService-" . date("Y-m-d").".log";
                error_log($msj, 3, $fileName );
                chmod($fileName, 0664);
            }
            protected function response($data,$code,$msj=""){
                $resp = array(
                    "code" => $code,
                    "message" => $msj,
                    "result" => $data,
                );            
                $this->logs(print_r($resp,1));     
                return $resp;
            }
            /**
             * Fin de los métodos internos de la clase 
             */
            
        }
         ?>