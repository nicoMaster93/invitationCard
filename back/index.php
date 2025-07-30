<?php

class LoadService {
    protected $model;
    protected $headers;
    protected $mode;
    function __construct(){
        date_default_timezone_set('America/Bogota');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: *');
        header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
        header('content-type: application/json; charset=utf-8');
        require_once('conexion/conexion.php');
        $this->headers = apache_request_headers();
        if($this->autentication()){
            $this->ini();
        }
    }
    protected function writeLog($msj){
        $msj = $msj = "\n[".date("Y-m-d H:i:s")."] Headers recibidos\n " . print_r($msj,1);
        $fileName = BASE . 'logs/logService-'.date("Y-m-d").'.log';
        error_log(print_r($msj,1), 3, $fileName );
        chmod($fileName, 0664);
    }
    protected function autentication(){
        /* Valido los headers */
        try {
            $au = true;
            if(empty($au)){
                throw new Exception("El [token] o el [mode_api] es inválido. Consulte con el administrador", 1);
            }else{
                return true;
            }
        } catch (\Throwable $e) {
            die($this->response([],500,$e->getMessage()));
        }
    }
    protected function ini(){
        try {
            $endPoint = $_GET['endpoint'];
            $action = $_GET['action'];
            $folder = BASE . FOLDER_SERVICE . DIRECTORY_SEPARATOR . $endPoint;
            $controller = BASE . FOLDER_SERVICE . DIRECTORY_SEPARATOR . "Controller.php";
            $route = $folder . DIRECTORY_SEPARATOR . "{$endPoint}Controller.php";
            /* validamos la ruta del servicio */
            if(!is_dir($folder) || !file_exists($route)){
                throw new Exception("El servicio solicitado no existe [$route]", 1);
            }else{
                require_once($controller);
                require_once($route);
                $requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'post';
                $postData = $_REQUEST;
                $postData['requestMethod'] = strtoupper($requestMethod);
                $classname = "{$endPoint}Controller";
                // valido si se envia un $_FILES
                if(count($_FILES) > 0){
                    foreach ($_FILES as $key => $value) {
                        $postData[$key] = $value;
                    }
                }

                if(class_exists($classname)){
                    $api = new $classname($postData);
                    if(method_exists($api,$action)){
                        $resp = $api->$action();
                        return $this->response( $resp['result'], $resp['code'], $resp['message']);
                    }else{
                        throw new Exception("La acción solicitada no existe [$action]", 1);
                    }
                }
            }

            echo $route;
        } catch (\Throwable $e) {
            die($this->response([],500,$e->getMessage()));
        }

    }
    protected function response($data,$code,$msj=''){
        // http_response_code($code);
        $resp = json_encode([
            'code' => $code,
            'message' => $msj,
            'result' => $data,
        ]);
        // echo "hola";
        
        echo $resp;
    }
}(new LoadService());


?>