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
session_start();

$core = new Core();
$database = new Database();
$twig = $core->initTwig();

// In the pinnacle of bodging we believe
$uri = $_SERVER["REQUEST_URI"];
$titleid2 = explode("/", substr($uri, 1))[1]; // Community ID

$mysqli = $database->connect();
if (!$mysqli) {
	error_log("Failed to connect to MySQL - " . $database->mysqli->error);
	die("Failed to connect to MySQL");
}

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

/*$feelings = array();
$feelings["normal_face"] = $core->getFeelingImage($_SESSION["user"]["user_nnid"]);
$feelings["happy_face"] = $core->getFeelingImage($_SESSION["user"]["user_nnid"], "happy_face");
$feelings["like_face"] = $core->getFeelingImage($_SESSION["user"]["user_nnid"], "like_face");
$feelings["surprised_face"] = $core->getFeelingImage($_SESSION["user"]["user_nnid"], "surprised_face");
$feelings["frustrated_face"] = $core->getFeelingImage($_SESSION["user"]["user_nnid"], "frustrated_face");
$feelings["puzzled_face"] = $core->getFeelingImage($_SESSION["user"]["user_nnid"], "puzzled_face");*/

$feelings = array();
$feelings["normal_face"] = "http://res.cloudinary.com/dnhlkobfg/image/upload/v1516125327/no-nnid.png";
$feelings["happy_face"] = "http://res.cloudinary.com/dnhlkobfg/image/upload/v1516125327/no-nnid.png";
$feelings["like_face"] = "http://res.cloudinary.com/dnhlkobfg/image/upload/v1516125327/no-nnid.png";
$feelings["surprised_face"] = "http://res.cloudinary.com/dnhlkobfg/image/upload/v1516125327/no-nnid.png";
$feelings["frustrated_face"] = "http://res.cloudinary.com/dnhlkobfg/image/upload/v1516125327/no-nnid.png";
$feelings["puzzled_face"] = "http://res.cloudinary.com/dnhlkobfg/image/upload/v1516125327/no-nnid.png";

echo $twig->render("postForm.twig", ["community" => $community, "feelings" => $feelings]);