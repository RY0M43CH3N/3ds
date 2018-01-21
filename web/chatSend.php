<?php
if ($_SESSION["user"]) {
    if (isset($_GET["message"])) {
    	$tmpChat = fopen("../tmp/chat", "w") or die("fopen failed");
    	fwrite($tmpChat, $_SESSION["user"]["user_pid"] . "|" . $_GET["message"] . "|" . time());
    	fclose($tmpChat);
        echo "ok";
    } else {
    	echo "error";
    }
} else {
	echo "error";
}