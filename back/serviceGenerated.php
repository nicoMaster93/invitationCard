<?php 
    include('conexion/.ini');
    $helpTerm ="\nError en la creacion del servicio\nParametros para la creacion de un servicio\n"; 
    $helpTerm.="-n:: Nombre del nuevo servicio \n"; 
    $helpTerm.="-c:: cliente default (opcional) \n"; 
    $helpTerm.="-t:: nombre de la tabla principal (opcional) \n"; 

    $options = array();
    $shortopts = "";
    
    if(!is_null($argv)){
        $shortopts .= "n::";
        $shortopts .= "c::";
        $shortopts .= "t::";
        $options = getopt($shortopts);
        if(isset($options['n']) && !empty($options['n'])){
            $directorio = BASE . FOLDER_SERVICE;
            $service = ucfirst($options['n']);
            $fileName = "{$service}Controller.php";
            if(is_dir($directorio . DIRECTORY_SEPARATOR .$service)){
                error_log("El servicio ".$options['n']." ya existe");
            }else{
                /* Creo el folder de servicios */
                if(!is_dir($directorio)){
                    if(mkdir($directorio, 0777, true)){
                        error_log("Se creo el folder " . $directorio);  
                    };
                }
                /* Creo el controlador del servicio */
                $controller_file = "Controller.php";
                if (!file_exists($directorio . DIRECTORY_SEPARATOR . $controller_file)) {
                    $pathClass = $directorio . DIRECTORY_SEPARATOR . $controller_file;
                    $contenxt = '<?php '.contextController().' ?>';
                    if(file_put_contents($pathClass,$contenxt)){
                        error_log("Se creo el controlador de los servicios correctamente.\nRuta Archivo <$pathClass>");
                    }
                }
                /* Creo el directorio con el nombre del servicio */
                if (mkdir($directorio . DIRECTORY_SEPARATOR . $service, 0777, true)) {
                    $pathClass = $directorio . DIRECTORY_SEPARATOR . $service .DIRECTORY_SEPARATOR. $fileName;
                    $contenxt = '<?php '.contextService().' ?>';
                    if(file_put_contents($pathClass,$contenxt)){
                        error_log("Se creo el servicio correctamente.\nRuta Archivo <$pathClass>");
                    }
                }
            }
        }else{
            error_log($helpTerm);
        }
    }else{
        error_log($helpTerm);
    }

    function contextController(){
        global $options;
        $client = (isset($options['c']) && !empty($options['c']) ? $options['c'] : 1);
        $service = ucfirst($options['n']);
        $class = "Controller";
        $table = (isset($options['t']) && !empty($options['t']) ? $options['t'] : $options['n']);
        $newService = '
        /**
         * Automatically generated script 
         * Creation Date: '.date("Y-m-d H:i:s").' 
         * Autor: Nicolas Hernandez 
         * version:1
         '.($client!=1 ? '* Cliente:'.$client:'').'
         * Informacion: 
         * Esta clase contiene los metodos que se van a utilizar en los servicios que se generan, con el fin de validar parametros, logs y respuestas http.
         **/
        
         class '.$class.'{
            protected $param;
            protected $model;
            protected $table;
            protected $jsonBody;
            function __construct($postData){
                try{
                    include_once(BASE."models/classBD.php");
                    $this->model = (new classBD);
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
        ';
        return $newService;
    }
    function contextService(){
        global $options;
        $client = (isset($options['c']) && !empty($options['c']) ? $options['c'] : 1);
        $service = ucfirst($options['n']);
        $class = "{$service}Controller";
        $table = (isset($options['t']) && !empty($options['t']) ? $options['t'] : $options['n']);
        $newService = '
        /**
         * Automatically generated script 
         * Creation Date: '.date("Y-m-d H:i:s").' 
         * Autor: Nicolas Hernandez 
         * version:1
         '.($client!=1 ? '* Cliente:'.$client:'').'
         * Informacion: 
         * Esta clase contiene los servicios que se van a utilizar, inicialmente la clase contiene un servicio (mtodo) de ejemplo; 
         * el cual ayuda a entender el desarrollo de un servicio autodocumentado facilitando luego el uso del mismo.
         **/
        
        class '.$class.' extends Controller{
            
            function __construct($postData){
                try{
                    parent::__construct($postData);
                    $this->table = "'.$table.'";
                }catch(Exception $e) {
                    return $this->response([],500,$e->getMessage());
                }
            }

            /**
             * Inicio de métodos privados
             * Los metodos privados sirven para realizar condiciones, consultas, etc;  que validen acciones internas
             * los métodos privados no se ven en la lista de servicios
             */
            private function method_private_example($success,$data,$infoAdd=null){
                try{
                    /* Los metodos privados se ocultan de la lista del api */
                    return true;
                }catch(Exception $e) {
                    return ["error" => $e->getMessage()];
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
            * @method getAll'.$service.' Obtiene toda la informacion base de '.$table.'
            **/
            public function getAll'.$service.'($select="t.*"){ 
                try{
                    /* Se agregan al arreglo los parametros requeridos para el funcionamiento del metodo */
                    /* optional_vars => Campos por lo cual se desea filtrar */
                    $paramRequired = array(
                        "optional_vars" => array(
                            "id" => "Id '.$service.'",
                            "status" => "Id status",
                        )
                    );
                    $validMethods = ["GET"];
                    $erno = self::validParameters("getAll'.$service.'",$paramRequired,"Obtiene el listado de '.$service.'");
                    // validacion de parametros
                    $success = false;
                    if($erno["error"]){
                        throw new Exception($erno["msj"], 1);
                    }elseif(!in_array($this->params["requestMethod"],$validMethods)){
                        throw new Exception("El Método HTTP es incorrecto \nMétodos http request admitidos [" . implode(",", $validMethods) ."]", 1);
                    }else{
                        $post = array();
                        $tabla = "$this->table t";
                        $condicion = [
                            "selects" => $select,
                            "condition" => [""]
                        ];
                        if(array_key_exists("id",$this->params)){
                            $condicion["condition"] = ["id = ?", $this->params["id"]];
                        }
                        if(array_key_exists("status",$this->params)){
                            $condicion["condition"][0] = (!empty($condicion["condition"][0]) ? $condicion["condition"][0] . " and " : "" ) . "status in (?)";
                            $condicion["condition"][] = $this->params["status"];
                        }
                        $data = $this->model->getDataTable($tabla,$condicion);
                        if(count($data)>0){
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
             * @method create '.$service.'
            **/
            public function create'.$service.'(){ 
                try{
                    /* Se agregan al arreglo los parametros requeridos para el funcionamiento del metodo */
                    /* optional_vars => es una llave reservada para identificar valores opcionales */
                    $paramRequired = array(
                        "user" => "Id del usuario",
                        "name" => "nombre de la categoría",
                    );
                    $validMethods = ["POST"];
                    $erno = self::validParameters("create'.$service.'",$paramRequired,"METHOD:POST || de '.$service.'");
                    // validacion de parametros
                    $success = false;
                    if($erno["error"]){
                        throw new Exception($erno["msj"], 1);
                    }elseif(!in_array($this->params["requestMethod"],$validMethods)){
                        throw new Exception("El Método HTTP es incorrecto \nMétodos http request admitidos [" . implode(",", $validMethods) ."]", 1);
                    }else{
                        $time = date("Y-m-d H:i:s");
                        $post = [
                            "create_at" => $time,
                            "update_at" => $time,
                            "status" => 1,
                        ];
                        /* validamos si existe antes de crear el registro */
                        $e = $this->model->getDataTable("'.$table.'",["condition" => ["CAMPO=?",  $this->params[CAMPO_CONDITIONAL]]]);
                        if(count($e)>0){
                            throw new Exception("El registro que intentas crear ya existe", 1);
                        }else{
                            $data = ["msj" => "Error al insertar el nuevo registro"];
                            if((array_key_exists("debugAction",$this->params) && $this->params["debugAction"]==true)){
                                $save = true;
                            }elseif( (array_key_exists("debugAction",$this->params) && $this->params["debugAction"]==false)  || (!array_key_exists("debugAction",$this->params)) ){
                                $save = $this->model->saveTable($this->table,$post);
                            }
                            if($save){
                                $code=true;
                                return $this->response([],200,"Se insertó correctamente");
                            }
                            /* 
                            // Este metodo valida si se envia parametros opcionales y los agrega en el arreglo $post
                            $post = $this->addOptionalVarsData($post,"PARAM_OPTIONAL");
                            */
                        }
                    }
        
                }catch(Exception $e) {
                    return $this->response([],500,$e->getMessage());
                }
            }
            /**
             * @method update '.$service.' 
            **/
            public function update'.$service.'(){ 
                try{
                    /* Se agregan al arreglo los parametros requeridos para el funcionamiento del metodo */
                    /* optional_vars => es una llave reservada para identificar valores opcionales */
                    $paramRequired = array(
                        "id" => "Id '.$service.'",
                        "name" => "nombre del campo",
                    );
                    $validMethods = ["PUT"];
                    $erno = self::validParameters("update'.$service.'",$paramRequired,"METHOD:PUT || UPDATE de '.$service.'");
                    // validacion de parametros
                    $success = false;
                    if($erno["error"]){
                        throw new Exception($erno["msj"], 1);
                    }elseif(!in_array($this->params["requestMethod"],$validMethods)){
                        throw new Exception("El Método HTTP es incorrecto \nMétodos http request admitidos [" . implode(",", $validMethods) ."]", 1);
                    }else{
                        $time = date("Y-m-d H:i:s");
                        $post = [
                            "name" => $this->params["name"],
                            "update_at" => $time,
                        ];
                        $data = ["msj" => "Error al modificar el registro"];
                        if((array_key_exists("debugAction",$this->params) && $this->params["debugAction"]==true)){
                            $save = true;
                        }elseif( (array_key_exists("debugAction",$this->params) && $this->params["debugAction"]==false)  || (!array_key_exists("debugAction",$this->params)) ){
                            $save = $this->model->saveTable($this->table,$post,"WHERE id = {$this->params["id"]}");
                        }
                        if($save){
                            return $this->response([],200,"Se actualizó correctamente");
                        }
                        /* 
                        // Este metodo valida si se envia parametros opcionales y los agrega en el arreglo $post
                            $post = $this->addOptionalVarsData($post,"PARAM_OPTIONAL");
                        */
                    }
        
                }catch(Exception $e) {
                    return $this->response([],500,$e->getMessage());
                }
            }
            /**
             * @method delete '.$service.' 
            **/
            public function delete'.$service.'(){ 
                try{
                    /* Se agregan al arreglo los parametros requeridos para el funcionamiento del metodo */
                    /* optional_vars => es una llave reservada para identificar valores opcionales */
                    $paramRequired = array(
                        "id" => "Id '.$service.'",
                    );
                    $validMethods = ["DELETE"];
                    $erno = self::validParameters("delete'.$service.'",$paramRequired,"METHOD:DELETE || Eliminar registro de '.$service.'");
                    // validacion de parametros
                    $success = false;
                    if($erno["error"]){
                        throw new Exception($erno["msj"], 1);
                    }elseif(!in_array($this->params["requestMethod"],$validMethods)){
                        throw new Exception("El Método HTTP es incorrecto \nMétodos http request admitidos [" . implode(",", $validMethods) ."]", 1);
                    }else{
                        $time = date("Y-m-d H:i:s");
                        $post = [
                            "status" => 0,
                            "update_at" => $time,
                        ];
                        $data = ["msj" => "Error al eliminar el registro"];
                        if((array_key_exists("debugAction",$this->params) && $this->params["debugAction"]==true)){
                            $save = true;
                        }elseif( (array_key_exists("debugAction",$this->params) && $this->params["debugAction"]==false)  || (!array_key_exists("debugAction",$this->params)) ){
                            $save = $this->model->saveTable($this->table,$post,"WHERE id = {$this->params["id"]}");
                        }
                        if($save){
                            return $this->response([],200,"Se eliminó el registro correctamente");
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
        ';
        return $newService;
    }

?>