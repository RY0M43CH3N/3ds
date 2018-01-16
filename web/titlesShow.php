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

/// Start session.
session_start();

/// Initialize our classes.
$core = new Core();
$database = new Database();

/// Connect to MySQL.
$mysqli = $database->connect();
if (!$mysqli) {
	error_log("Failed to connect to MySQL - " . $database->mysqli->error);
	die("Failed to connect to MySQL");
}

/// Let's get our console data, parse it and shove it into our session!
$data = explode("\\", base64_decode($_SERVER["HTTP_X_NINTENDO_PARAMPACK"]));
array_shift($data);
array_pop($data);

for ($i = 0; $i < count($data); $i += 2) {
    $_SESSION["console"]["ParamData"][$data[$i]] = $data[$i + 1];
}

/// Unset console data. (don't worry this still keep's it in session)
unset($data);

/*
 * Language Codes:
 * 0 - Japanese
 * 1 - English
 * 2 - German
 * 3 - French
 * 4 - Spanish
 * 5 - Italian
 * 6 - Dutch
 */
/// Translation soon

/// Grab's console data from transferable_id in our session
$console = $core->getConsole($database, $mysqli, $_SESSION["console"]["ParamData"]["transferable_id"]);

/// Console doesn't exist in database, let's redirect them to setup!
if (!$console) {
    header("Location: /welcome");
    exit;
}

/// Set our user session to the user the console is linked to and redirect to communities.
$_SESSION["user"] = $core->getUserByPID($database, $mysqli, $console["linked_pid"]);
header("Location: /communities");