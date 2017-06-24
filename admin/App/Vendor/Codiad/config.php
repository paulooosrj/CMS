<?php


/*
*  pegamos os paths padrões do sistema
*/
$r 		= $_SERVER["DOCUMENT_ROOT"];
$root 	=  ((substr($r, -1) == '/') ? substr($r, 0, -1) : $r);
include_once($root.'/admin/App/Lib/class-ws-v1.php');


/*
*  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
*  as-is and without warranty under the MIT License. See
*  [root]/license.txt for more. This information must remain intact.
*/

//////////////////////////////////////////////////////////////////
// CONFIG
//////////////////////////////////////////////////////////////////

// PATH TO CODIAD
define("BASE_PATH", $root."/admin/App/Vendor/Codiad");

// BASE URL TO CODIAD (without trailing slash)
define("BASE_URL", $root."/admin/App/Vendor/Codiad");

// THEME : default, modern or clear (look at /themes)
define("THEME", "default");

// ABSOLUTE PATH
define("WHITEPATHS", BASE_PATH);

// SESSIONS (e.g. 7200)
$cookie_lifetime = "0";

// TIMEZONE
date_default_timezone_set("America/Sao_Paulo");


define("AUTH_PATH", BASE_PATH."/data/ws-user.php");

//////////////////////////////////////////////////////////////////
// ** DO NOT EDIT CONFIG BELOW **
//////////////////////////////////////////////////////////////////

// PATHS
define("COMPONENTS", 	BASE_PATH . "/components");
define("PLUGINS", 		BASE_PATH . "/plugins");
define("THEMES", 		BASE_PATH . "/themes");
define("DATA", 			BASE_PATH . "/data");
define("WORKSPACE", 	ROOT_DOCUMENT);
define("WSURL", 		ROOT_DOCUMENT);

// Marketplace
//define("MARKETURL", "http://market.codiad.com/json");
// Update Check
//define("UPDATEURL", "http://update.codiad.com/?v={VER}&o={OS}&p={PHP}&w={WEB}&a={ACT}");
//define("ARCHIVEURL", "https://github.com/Codiad/Codiad/archive/master.zip");
//define("COMMITURL", "https://api.github.com/repos/Codiad/Codiad/commits");
