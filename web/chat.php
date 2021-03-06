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

$core = new Core();
$twig = $core->initTwig();

// Hold up, we're in foxverse 2!
if (!$_SESSION["user"]) {
	header("Location: /titles/show");
	exit;
} elseif ($_SESSION["user"]["user_disabled"] == 1){
	echo $twig->render("disabled.twig");
	exit;
}

echo $twig->render("chat.twig");