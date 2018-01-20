<?php
/*
 * This file is part of foxverse
 * Copyright (C) 2017 Steph Lockhomes, Billy Humphreys
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

require_once("../lib/Core.php");
require_once("../lib/Database.php");

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

function feelingName($feelingID) {
	$feelingName = "";
	switch ($feelingID) {
		case 0:
			$feelingName = "normal_face";
			break;
		case 1:
			$feelingName = "happy_face";
			break;
		case 2:
			$feelingName = "like_face";
			break;
		case 3:
			$feelingName = "surprised_face";
			break;
		case 4:
			$feelingName = "frustrated_face";
			break;
		case 5:
			$feelingName = "puzzled_face";
			break;
		default:
			$feelingName = "wtf";
			break;
	}
	return $feelingName;
}

$mysqli = $database->connect();

// In the pinnacle of bodging we believe
$uri = $_SERVER["REQUEST_URI"];
$titleid2 = explode("/", substr($uri, 1))[2]; // Community ID

$stmt = $mysqli->prepare("SELECT * FROM `communities` WHERE `community_id` = ?");
if (!$stmt):
	error_log($mysqli->error);
	die($mysqli->error);
endif;

$stmt->bind_param("i", $titleid2);
if (!$stmt->execute()) {
	error_log("Failed to execute $stmt - " . $stmt->error);
	die("Failed to execute $stmt");
}

$community = $database->getResult($stmt)[0];

if (!$community) {
	echo $twig->render("404.twig");
	exit;
}

$stmt = $mysqli->prepare("SELECT * FROM `posts` WHERE `post_community_id` = ? ORDER BY `post_date` DESC");
if (!$stmt):
	error_log($mysqli->error);
	die($mysqli->error);
endif;

$stmt->bind_param("i", $titleid2);
if (!$stmt->execute()) {
	error_log("Failed to execute $stmt - " . $stmt->error);
	die("Failed to execute $stmt");
}

$posts = $database->getResult($stmt);

foreach ($posts as $key => $post) {
	$posts[$key]["post_disabled"] = htmlspecialchars($core->getUserByPID($database, $mysqli, $posts[$key]["post_pid"])["user_disabled"]);
	$posts[$key]["post_username"] = htmlspecialchars($core->getUserByPID($database, $mysqli, $posts[$key]["post_pid"])["user_username"]);
	$posts[$key]["post_display_name"] = htmlspecialchars($core->getUserByPID($database, $mysqli, $posts[$key]["post_pid"])["user_display_name"]);
	if ($core->getUserByPID($database, $mysqli, $posts[$key]["post_pid"])["user_nnid"]) {
		$posts[$key]["post_icon"] = $core->getFeelingImage($core->getUserByPID($database, $mysqli, $posts[$key]["post_pid"])["user_nnid"], feelingName($posts[$key]["post_feeling"]));
	} else {
		$posts[$key]["post_icon"] = "http://res.cloudinary.com/dnhlkobfg/image/upload/v1516125327/no-nnid.png";
	}
	$posts[$key]["post_content"] = htmlspecialchars($posts[$key]["post_content"]);
}

echo $twig->render("titles.twig", ["community" => $community, "posts" => $posts]);