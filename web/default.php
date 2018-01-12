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

require_once("../lib/AltoRouter.php");
require_once("../lib/Core.php");
session_start();

$core = new Core();
$twig = $core->initTwig();
$router = new AltoRouter();

$router->addRoutes(array(
	array("GET", "/titles/show", "show.php", "Titles-show"),
	array("GET", "/", "activity-feed.php", "Activity-feed"),
	array("GET", "/communities", "communities.php", "Communities-list"),
	array("GET", "/titles/[i:id]", "titles.php", "Titles"),
	array("GET", "/check_update.json", "check_update.php", "Check-update")
));

$match = $router->match(urldecode($_SERVER["REQUEST_URI"]));
if ($match) {
	foreach($match["params"] as &$param) {
		${key($match["params"])} = $param;
	}
	require_once $match["target"];
} else {
	http_response_code(404);
	echo $twig->render("404.twig");
	exit;
}