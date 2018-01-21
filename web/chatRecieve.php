<?php
require_once("../lib/Core.php");
require_once("../lib/Database.php");

$core = new Core();
$database = new Database();
$mysqli = $database->connect();

set_time_limit(0);
$tmpChat = "../tmp/chat";

while (true) {
    $lastAjax = isset($_GET["timestamp"]) ? (int)$_GET["timestamp"] : null;
    clearstatcache();
    $lastChange = filemtime($tmpChat);
    
    if ($lastAjax == null || $lastChange > $lastAjax) {
        $data = file_get_contents($tmpChat);
        $dataExplode = explode("|", $data);

        $user = $core->getUserByPID($database, $mysqli, intval($dataExplode[0]));
        
        $result = array(
            "username" => $user["user_display_name"],
            "message" => $dataExplode[1],
            "time_stamp" => intval($dataExplode[2]),
            "timestamp" => $lastChange
        );
        
        $json = json_encode($result);
        echo $json;
        break;
    } else {
        sleep(1);
        clearstatcache();
        continue;
    }
}
?>