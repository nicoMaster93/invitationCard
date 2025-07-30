<?php 
include("conexion/dev.ini");
$invitados = json_decode( utf8_encode( file_get_contents("https://web-html.com/baby-shower-meet/back/db/guests.json") ) , true);
$confirmados = [];
$companies = [];
$detailCompanies = [];
$sinConfirmar = [];
$noAsistir = [];
foreach ($invitados as $key) {
    if($key["confirm_assistance"]){
        $confirmados[] = $key;
    }
    if(count($key["companions"]) > 0 ){
        $companies[] = $key;
        $detailCompanies = array_merge($detailCompanies, array_column($key["companions"], "guest") );
    }
    if(is_null($key["confirm_assistance"])){
        $sinConfirmar[] = $key;
    }else if(!$key["confirm_assistance"]){
        $noAsistir[] = $key;
    }
}

$result = [
    "totalConfirmados" => count($confirmados),
    "detailConfirmados" => array_column($confirmados, "guest"),
    "totalSinConfirmar" => count($sinConfirmar),
    "detailSinConfirmados" => array_column($sinConfirmar, "guest"),
    "totalSinAsistir" => count($noAsistir),
    "detailSinAsistir" => array_column($noAsistir, "guest"),
    "totalInvitados" => count($invitados),
    "totalAcompanantes" => count($companies),
    "detailAcompanantes" => $companies,
    "detailAcompanantesNames" => $detailCompanies,
];

if(file_exists(BASE . "result.json")){
    unlink(BASE . "result.json");
}
file_put_contents(BASE . "result.json", utf8_encode(json_encode($result, JSON_PRETTY_PRINT)));
chmod(BASE . "result.json", 0777);
echo "Se actualizó el resultado de las invitaciones";

?>
