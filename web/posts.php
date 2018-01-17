<?php
require_once("../lib/Database.php");
require_once("../lib/Core.php");
session_start();

$core = new Core();
$database = new Database();
$mysqli = $database->connect();

if ($_SESSION["user"]) {
	if ($_SERVER["REQUEST_METHOD"] === "POST") {
		$title_id = $_POST["olive_title_id"];
		$id = $_POST["olive_community_id"];
		$feeling = $_POST["feeling_id"];
		$type = $_POST["_post_type"];
		if ($type == "body") {
			$body = $_POST["body"];
			if (strlen($body) < 2) {
				header("Location: /titles/" . $title_id . "/" . $id . "/posts");
				exit;
			}
			$postID = $core->getContentID();
			$stmt = $mysqli->prepare("INSERT INTO `posts` (`post_id`, `post_community_id`, `post_pid`, `post_content`, `post_feeling`) VALUES (?, ?, ?, ?, ?)");
			if (!$stmt):
				error_log($mysqli->error);
				die($mysqli->error);
			endif;

			$stmt->bind_param("ssiisi", $postID, $id, $_SESSION["user"]["user_pid"], $body, $feeling);
			if (!$stmt->execute()) {
				error_log("Failed to execute $stmt - " . $stmt->error);
				die("Failed to execute $stmt");
			}
			header("Location: /titles/" . $title_id . "/" . $id);
		} else {
			//not implemented
			header("Location: /titles/" . $title_id . "/" . $id);
		}
	}
} else {
	header("Location: /titles/" . $title_id . "/" . $id);
}