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

exit; // DISABLED BECUS ME ANGERY >:(

if (getenv("CREATION_PASSWORD") != $_GET["password"]) {
	die("Invalid password");
}

require_once("../lib/Database.php");
require_once("../lib/Snowflake.php");

$database = new Database();
$snowflake = new Snowflake(1, 1);
$mysqli = $database->connect();

if (!$mysqli)
	die("Error while init'ing the database");

$type = intval($_GET["type"]);
$permission_level = intval($_GET["permission_level"]);
$hidden = intval($_GET["hidden"]);

$stmt = $mysqli->prepare("INSERT INTO `communities` (`id`, `name`, `description`) VALUES (?, ?, ?)");
if (!$stmt):
	error_log($mysqli->error);
	die($mysqli->error);
endif;

$stmt->bind_param("iss", $snowflake->generateID(), $_GET["name"], $_GET["description"]);
if (!$stmt->execute()) {
	error_log("Failed to execute $stmt - " . $stmt->error);
	die("Failed to execute $stmt");
}

echo("Created community, " . $_GET["name"]);