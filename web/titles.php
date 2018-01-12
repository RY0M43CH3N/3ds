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

$stmt = $mysqli->prepare("SELECT * FROM `communities` WHERE id = ? LIMIT 1");
if (!$stmt):
	error_log($mysqli->error);
	die($mysqli->error);
endif;
$id = 1;
$stmt->bind_param("i", $id);
if (!$stmt->execute()) {
	error_log("Failed to execute $stmt - " . $stmt->error);
	die("Failed to execute $stmt");
}

echo $twig->render("titles.twig", ["communities" => $database->getResult($stmt)]);