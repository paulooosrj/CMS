<?
	require_once( COMPONENTS . "/user/class.user.php" );

// #####################################################  
// # FORMATA O CAMINHO ROOT
// #####################################################


	$_SESSION['lang'] 		= 'pt';
	$_SESSION['theme'] 		= 'default';
	$_SESSION['project'] 	= 'website';
	$User = new User();
	
	$User->username = @$_SESSION['user']['usuario'];
	if ($User->CheckDuplicate()) {
		$User->users[] = array( 'username' => $_SESSION['user']['usuario'], 'password' => null, 'project' => "website" );
		saveJSON( "users.php", $User->users );
 	}


