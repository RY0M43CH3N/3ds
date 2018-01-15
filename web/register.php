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
$database = new Database();
$mysqli = $database->connect();

function registerError($str) {
	header("Location: " . $_SERVER["REQUEST_URI"] . "?err=" . $str);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	//registerError("Registration is currently disabled.");
	//exit;
	echo $twig->render("registerSuccess.twig");
	exit;
}

if (isset($_GET["err"]) && !empty($_GET["err"])) {
	echo $twig->render("register.twig", ["err" => htmlspecialchars($_GET["err"])]);
} else {
	echo $twig->render("register.twig");
}