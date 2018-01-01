<?php
require_once("../lib/Core.php");
session_start();
set_time_limit(0);

$roomId = isset($_GET["roomId"]) ? (int)$_GET["roomId"] : null;
if ($roomId) {
    $roomIdLower = strtolower($roomId)[0];
    if (!file_exists($roomIdLower . ".csv")) {
        echo("[@] Room ID valid. Welcome to Room " . strtoupper($roomIdLower)) . ", user!";
    } else {
        die("[!] Invalid room ID, closing connection.");
    }
} else {
    die("[!] Room ID is not set, closing connection.");
}

while (true) {
    $lastAjaxCall = isset($_GET["timestamp"]) ? (int)$_GET["timestamp"] : null;

    if (!$lastAjaxCall) {
        die("[!] Timestamp not set, closing connection.");
    }

    clearstatcache();
    $lastFileChange = filemtime($roomIdLower . ".csv");

    if ($lastAjaxCall == null || $lastFileChange > $lastAjaxCall) {
        $data = file($roomIdLower . ".csv");
        $dataLine = $data[count($data) - 1];
        $dataParsed = explode($dataLine, ",");

        $result = array(
            "username" => $dataParsed[0],
            "message" => $dataParsed[1],
            "timestamp" => $lastFileChange
        );

        $json = json_encode($result);
        echo($json);
        break;
    } else {
        sleep(1);
        continue;
    }
}