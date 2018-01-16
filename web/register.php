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

function errorRedirect($str) {
	header("Location: " . $_SERVER["REQUEST_URI"] . "?err=" . $str);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	if (isset($_POST["display_name"]) && !empty($_POST["display_name"])) {
		if (strlen($_POST["display_name"]) < 6 || strlen($_POST["display_name"]) > 24) {
			errorRedirect("Display Name length must be between 6-24.");
			exit;
		}
	} else {
		errorRedirect("Please enter a display name.");
		exit;
	}

	if (isset($_POST["username"]) && !empty($_POST["username"])) {
		if (strlen($_POST["username"]) < 6 || strlen($_POST["username"]) > 16) {
			errorRedirect("Username length must be between 6-16.");
			exit;
		}
	} else {
		errorRedirect("Please enter a username.");
		exit;
	}

	if (isset($_POST["password"]) && !empty($_POST["password"])) {
		if (strlen($_POST["password"]) < 6) {
			errorRedirect("Password length must be above 6.");
			exit;
		}
	} else {
		errorRedirect("Please enter a password.");
		exit;
	}

	if (isset($_POST["email"]) && !empty($_POST["email"])) {
		if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
			errorRedirect("Invalid email, please enter a valid email.");
			exit;
		}
	} else {
		errorRedirect("Please enter a email.");
		exit;
	}

	$nnid = null;

	if (isset($_POST["nnid"]) && !empty($_POST["nnid"])) {
		if (strlen($_POST["nnid"]) < 6 || strlen($_POST["nnid"]) > 16) {
			errorRedirect("Nintendo Network ID length must be between 6-16.");
			exit;
		}

		$nnid = $_POST["nnid"];
	}
	echo $twig->render("registerSuccess.twig");
	exit;
}

if (isset($_GET["err"]) && !empty($_GET["err"])) {
	echo $twig->render("register.twig", ["err" => htmlspecialchars($_GET["err"])]);
} else {
	echo $twig->render("register.twig");
}