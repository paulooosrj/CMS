<?
#############################################################################
#	IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
#############################################################################
	ob_start();
	include_once(__DIR__.'/../Lib/class-ws-v1.php');
	ob_end_clean();

#############################################################################
#	SETAMOS O HEADER PARA JSON
#############################################################################
	header("Content-type:application/json");

#############################################################################
#	TRATAMOS O JSON DOS COMMITS E RETORNAMOS
#############################################################################
	$json = json_decode(ws::get_github_commits());
	echo json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
?>