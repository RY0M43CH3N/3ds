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

$mysqli = $database->connect();

$stmt = $mysqli->prepare("SELECT * FROM `users` WHERE `user_username` = ?");
if (!$stmt):
	error_log($mysqli->error);
	die($mysqli->error);
endif;

$stmt->bind_param("s", $id);
if (!$stmt->execute()) {
	error_log("Failed to execute $stmt - " . $stmt->error);
	die("Failed to execute $stmt");
}

$user = $database->getResult($stmt)[0];
unset($user["user_password"], $user["user_email"], $user["user_ip"]);
if ($user["user_nnid"] != null) {
	$user["user_icon"] = $core->getFeelingImage($user["user_nnid"]);
} else {
	$user["user_icon"] = "http://res.cloudinary.com/dnhlkobfg/image/upload/v1516125327/no-nnid.png";
}
$user["user_username"] = htmlspecialchars($user["user_username"]);

echo $twig->render("users.twig", ["user" => $user]);