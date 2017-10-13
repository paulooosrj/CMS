<?
	require_once( COMPONENTS . "/user/class.user.php" );
	$r = $_SERVER["DOCUMENT_ROOT"];
	$_SERVER["DOCUMENT_ROOT"] = (substr($r, -1) == '/') ? substr($r, 0, -1) : $r;

	include_once ($_SERVER["DOCUMENT_ROOT"].'/admin/App/Lib/class-ws-v1.php');
	$sessionUser = new session();
// #####################################################  
// # FORMATA O CAMINHO ROOT
// #####################################################


	$_SESSION['lang'] 		= 'pt';
	$_SESSION['theme'] 		= 'default';
	$_SESSION['project'] 	= 'website';
	$User = new User();
	
	$User->username = $sessionUser->get('usuario');
	if ($User->CheckDuplicate()) {
		$User->users[] = array( 'username' => $sessionUser->get('usuario'), 'password' => null, 'project' => "website" );
		saveJSON( "users.php", $User->users );
 	}


