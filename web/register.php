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

require_once("../lib/Database.php");
require_once("../lib/Core.php");
session_start();

$core = new Core();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$database = new Database();
	$mysqli = $database->connect();

	$stmt = $mysqli->prepare("SELECT * FROM `users`");
	if (!$stmt):
		error_log($mysqli->error);
		die($mysqli->error);
	endif;

	if (!$stmt->execute()) {
		error_log("Failed to execute $stmt - " . $stmt->error);
		die("Failed to execute $stmt");
	}

	$pid = 1799999999 - $stmt->affected_rows;

	$stmt = $mysqli->prepare("INSERT INTO `users` (`user_pid`, `user_creation_date`, `user_ip`, `user_display_name`, `user_username`, `user_password`, `user_nnid`, `user_email`, `user_country_id`, `user_systems_owned`) 
		VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?, ?)");
	if (!$stmt):
		error_log($mysqli->error);
		die($mysqli->error);
	endif;

	$systems_owned = 1;

	$stmt->bind_param("issssssii", $pid, $_SERVER["REMOTE_ADDR"], $_POST["display_name"], $_POST["username"], password_hash($_POST["password"], PASSWORD_DEFAULT), $_POST["nnid"], $_POST["email"], $_SESSION["console"]["ParamData"], $systems_owned);
	if (!$stmt->execute()) {
		error_log("Failed to execute $stmt - " . $stmt->error);
		die("Failed to execute $stmt");
	}

	echo("Please re-open Miiverse to start using foxverse!");
}

echo $twig->render("register.twig");