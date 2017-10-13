<?php

    /*
    *  Copyright (c) Codiad & Kent Safranski (codiad.com), distributed
    *  as-is and without warranty under the MIT License. See
    *  [root]/license.txt for more. This information must remain intact.
    */
    $r = $_SERVER["DOCUMENT_ROOT"];
    $_SERVER["DOCUMENT_ROOT"] = (substr($r, -1) == '/') ? substr($r, 0, -1) : $r;

    ############################################################################################################################
    # IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
    ############################################################################################################################
    include_once ($_SERVER["DOCUMENT_ROOT"].'/admin/App/Lib/class-ws-v1.php');
    

    require_once('../../common.php');
    require_once('class.active.php');

    $Active = new Active();

    //////////////////////////////////////////////////////////////////
    // Verify Session or Key
    //////////////////////////////////////////////////////////////////

    checkSession();
    $user = new session();

    //////////////////////////////////////////////////////////////////
    // Get user's active files
    //////////////////////////////////////////////////////////////////

if ($_GET['action']=='list') {
    $Active->username =  $user->get('usuario');
    $Active->ListActive();
}

    //////////////////////////////////////////////////////////////////
    // Add active record
    //////////////////////////////////////////////////////////////////

if ($_GET['action']=='add') {
    $Active->username = $user->get('usuario');
    $Active->path = $_GET['path'];
    $Active->Add();
}

    //////////////////////////////////////////////////////////////////
    // Rename
    //////////////////////////////////////////////////////////////////

if ($_GET['action']=='rename') {
    $Active->username = $user->get('usuario');
    $Active->path = $_GET['old_path'];
    $Active->new_path = $_GET['new_path'];
    $Active->Rename();
}

    //////////////////////////////////////////////////////////////////
    // Check if file is active
    //////////////////////////////////////////////////////////////////

if ($_GET['action']=='check') {
    $Active->username = $user->get('usuario');
    $Active->path = $_GET['path'];
    $Active->Check();
}

    //////////////////////////////////////////////////////////////////
    // Remove active record
    //////////////////////////////////////////////////////////////////

if ($_GET['action']=='remove') {
    $Active->username = $user->get('usuario');
    $Active->path = $_GET['path'];
    $Active->Remove();
}
    
    //////////////////////////////////////////////////////////////////
    // Remove all active record
    //////////////////////////////////////////////////////////////////

if ($_GET['action']=='removeall') {
    $Active->username = $user->get('usuario');
    $Active->RemoveAll();
}
    
    //////////////////////////////////////////////////////////////////
    // Mark file as focused
    //////////////////////////////////////////////////////////////////

if ($_GET['action']=='focused') {
    $Active->username = $user->get('usuario');
    $Active->path = $_GET['path'];
    $Active->MarkFileAsFocused();
}
