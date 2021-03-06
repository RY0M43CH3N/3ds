<?php
require_once("../lib/Database.php");
require_once("../lib/Core.php");

$core = new Core();
$database = new Database();
$twig = $core->initTwig();

// Hold up, we're in foxverse 2!
if (!$_SESSION["user"]) {
	header("Location: /titles/show");
	exit;
} elseif ($_SESSION["user"]["user_disabled"] == 1){
	echo $twig->render("disabled.twig");
	exit;
}

$mysqli = $database->connect();

// spaghetti code below
if ($_SESSION["user"]) {
	if ($_SERVER["REQUEST_METHOD"] === "POST") {
		$title_id = $_POST["olive_title_id"];
		$id = $_POST["olive_community_id"];
		$feeling = $_POST["feeling_id"];
		$type = $_POST["_post_type"];
		$stmt = $mysqli->prepare("SELECT * FROM `communities` WHERE `community_id` = ?");
		if (!$stmt):
			error_log($mysqli->error);
			die($mysqli->error);
		endif;

		$stmt->bind_param("i", $id);
		if (!$stmt->execute()) {
			error_log("Failed to execute $stmt - " . $stmt->error);
			die("Failed to execute $stmt");
		}

		$community = $database->getResult($stmt)[0];
		if ($_SESSION["user"]["user_permission"] == $community["community_permission"] || $community["community_permission"] == 0) {
		} else {
			exit;
		}
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

			$stmt->bind_param("siisi", $postID, $id, $_SESSION["user"]["user_pid"], $body, $feeling);
			if (!$stmt->execute()) {
				error_log("Failed to execute $stmt - " . $stmt->error);
				die("Failed to execute $stmt");
			}
			header("Location: /titles/" . $title_id . "/" . $id);
		} else {
			$painting = $_POST["painting"];
			$stmt = $mysqli->prepare("SELECT * FROM cloudinary_keys ORDER BY RAND() LIMIT 1");
			if (!$stmt):
				error_log($mysqli->error);
				die($mysqli->error);
			endif;

			if (!$stmt->execute()) {
				error_log("Failed to execute $stmt - " . $stmt->error);
				die("Failed to execute $stmt");
			}

			$key = $database->getResult($stmt)[0];

			// this part was taken from cedar, im too lazy to add cloudinarys shitty php to core
			$pvars = array(
				"file" => "data:image/png;base64," . $painting,
				"api_key" => $key["api_key"],
				"upload_preset" => $key["upload_preset"]
			);

			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, "https://api.cloudinary.com/v1_1/" . $key["cloud_key"] . "/auto/upload");
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $pvars);
			$out = curl_exec($curl);
			curl_close($curl);
			$pms = json_decode($out, true);

			//var_dump($pms);

			if (@$image = $pms["secure_url"]) {
				//echo($image);
				$image_http = str_replace("https://", "http://", $image);
				//echo($image_http);
				$postID = $core->getContentID();
				$stmt = $mysqli->prepare("INSERT INTO `posts` (`post_id`, `post_community_id`, `post_pid`, `post_image`, `post_feeling`) VALUES (?, ?, ?, ?, ?)");
				if (!$stmt):
					error_log($mysqli->error);
					die($mysqli->error);
				endif;

				$stmt->bind_param("siisi", $postID, $id, $_SESSION["user"]["user_pid"], $image_http, $feeling);
				if (!$stmt->execute()) {
					error_log("Failed to execute $stmt - " . $stmt->error);
					die("Failed to execute $stmt");
				}
				header("Location: /titles/" . $title_id . "/" . $id);
			} else {
				header("Location: /titles/" . $title_id . "/" . $id);
			}
		}
	}
} else {
	header("Location: /titles/" . $title_id . "/" . $id);
}