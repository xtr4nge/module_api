<?
/*
    Copyright (C) 2013-2016 xtr4nge [_AT_] gmail.com

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

include "../../../functions.php";

$regex_extra = $regex_extra."/";

// Checking POST & GET variables [regex]...
if ($regex == 1) {
    regex_standard($_POST["api"], "../../../msg.php", $regex_extra);
    regex_standard($_GET["api"], "../../../msg.php", $regex_extra);
	regex_standard($_POST["token"], "../../../msg.php", $regex_extra);
    regex_standard($_GET["token"], "../../../msg.php", $regex_extra);
}

if (isset($_GET["token"])) {
	$token = $_GET["token"];
} else {
	include "../../../login_check.php";
	include "../../../config/config.php";
	$token = $api_token;
}


require("ws.php");
$ws = new WebService($token);
$ws->login();

$api = $_GET["api"];
$api = explode("/", $api);


// INTERFACES
if (sizeof($api) == 2 and $api[1] == "interface")
{
	echo $ws->getAllInterfaces();
}

if (sizeof($api) == 3 and $api[1] == "interface")
{
	echo $ws->getInterface($api[2]);
}

// CONFIG
if (sizeof($api) == 2 and $api[1] == "config")
{
	//echo $ws->getConfig();
}

// CONFIG: GET IN|OUT
if (sizeof($api) == 4 and $api[1] == "config" and $api[2] == "io" and ($api[3] == "in" or $api[3] == "out"))
{
	$io = $api[3]; // in|out
	echo $ws->getConfigInOutAll($io);
}

// CONFIG: GET IN|OUT
if (sizeof($api) == 5 and $api[1] == "config" and $api[2] == "io" and ($api[3] == "in" or $api[3] == "out"))
{
	$io = $api[3]; // in|out
	$prop = $api[4]; // iface|type|ip|mask|gw
	echo $ws->getConfigInOut($io, $prop);
}

// CONFIG: SET IN|OUT
if (sizeof($api) == 6 and $api[1] == "config" and $api[2] == "io" and ($api[3] == "in" or $api[3] == "out"))
{
	$io = $api[3]; // in|out
	$prop = $api[4]; // iface|type|ip|mask|gw
	$value = $api[5]; // value
	echo $ws->setConfigInOut($io, $prop, $value);
}

// CONFIG: GET ACTION [sniff|inject] interface
if (sizeof($api) == 4 and $api[1] == "config" and $api[2] == "io" and $api[3] == "action")
{
	echo $ws->getConfigAction($value);
}

// CONFIG: SET ACTION [sniff|inject] interface
if (sizeof($api) == 5 and $api[1] == "config" and $api[2] == "io" and $api[3] == "action")
{
	$value = $api[4]; // value
	echo $ws->setConfigAction($value);
}

// CONFIG: GET WIRELESS SSID
if (sizeof($api) == 3 and $api[1] == "config" and $api[2] == "wireless")
{
	echo $ws->getConfigWireless($value);
}

// CONFIG: SET WIRELESS SSID
if (sizeof($api) == 4 and $api[1] == "config" and $api[2] == "wireless")
{
	//$value = $api[3]; // value
	//echo $ws->setConfigWireless($value);
}

// MODULES
if (sizeof($api) == 2 and $api[1] == "module")
{
	echo $ws->getModules();
}

if (sizeof($api) == 2 and $api[1] == "modules")
{
	echo $ws->getModulesAll();
}

if (sizeof($api) == 3 and $api[1] == "module")
{
	$module = $api[2];
	echo $ws->getModuleStatus($module);
}

if (sizeof($api) == 4 and $api[1] == "module")
{
	$module = $api[2];
	$action = $api[3];
	echo $ws->setModuleStatus($module, $action);
}

// LOGS
if (sizeof($api) == 3 and $api[1] == "log" and $api[2] == "dhcp")
{
	echo $ws->getLogDHCP();
}

if (sizeof($api) == 3 and $api[1] == "log" and $api[2] == "station")
{
	echo $ws->getLogStation();
}

// STATUS

if (sizeof($api) == 3 and $api[1] == "status" and $api[2] == "cpu")
{
	echo $ws->getStatusCPU();
}

if (sizeof($api) == 3 and $api[1] == "status" and $api[2] == "mem")
{
	echo $ws->getStatusMEM();
}

// CONFIG

if (sizeof($api) == 5 and $api[1] == "config" and $api[2] != "core" and $api[3] != "" and $api[4] != "")
{
	# example: /config/io_mode/1
	echo $ws->setConfigCore($api[3], $api[4]);
}

if (sizeof($api) == 6 and $api[1] == "config" and $api[2] == "module" and $api[3] != "" and $api[4] != "" and $api[5] != "")
{
	# example: /config/module/ap/mod_filter_station/1
	echo $ws->setConfigModule($api[3], $api[4], $api[5]);
}

?>
