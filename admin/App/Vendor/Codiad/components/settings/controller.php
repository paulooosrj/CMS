<?php

    /*
    *  Copyright (c) Codiad & Andr3as, distributed
    *  as-is and without warranty under the MIT License. See
    *  [root]/license.txt for more. This information must remain intact.
    */
    $r = $_SERVER["DOCUMENT_ROOT"];
    $_SERVER["DOCUMENT_ROOT"] = (substr($r, -1) == '/') ? substr($r, 0, -1) : $r;

    require_once('../../common.php');
    require_once('class.settings.php');
    include_once ($_SERVER["DOCUMENT_ROOT"].'/admin/App/Lib/class-ws-v1.php');



if (!isset($_GET['action'])) {
    die(formatJSEND("error", "Missing parameter"));
}
    
    //////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////

    checkSession();

    $Settings = new Settings();

    //////////////////////////////////////////////////////////////////
    // Save User Settings
    //////////////////////////////////////////////////////////////////
    $user = new session();
    if ($_GET['action']=='save') {
        if (!isset($_POST['settings'])) {
            die(formatJSEND("error", "Missing settings"));
        }

        $Settings->username = $user->get('usuario');
        $Settings->settings = json_decode($_POST['settings'], true);
        $Settings->Save();
    }

    //////////////////////////////////////////////////////////////////
    // Load User Settings
    //////////////////////////////////////////////////////////////////

if ($_GET['action']=='load') {
    $Settings->username = $user->get('usuario');
    $Settings->Load();
}
