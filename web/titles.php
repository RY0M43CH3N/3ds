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

$mysqli = $database->connect();

$stmt = $mysqli->prepare("SELECT * FROM `communities` WHERE `community_id` = ?");
if (!$stmt):
	error_log($mysqli->error);
	die($mysqli->error);
endif;

$titleid2 = 1;

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

$stmt = $mysqli->prepare("SELECT * FROM `posts` WHERE `post_community_id` = ?");
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

for ($i = 0; $i < count($posts); $i++) {
    $posts["post_username"][$data[$i]] = htmlspecialchars($core->getUserByPID($database, $mysqli, $posts["post_pid"][$data[$i]])["user_username"]);
    $posts["post_icon"][$data[$i]] = $core->getFeelingImage($core->getUserByPID($database, $mysqli, $posts["post_pid"][$data[$i]])["user_nnid"]);
}

echo $twig->render("titles.twig", ["community" => $community, "posts" => $posts]);