<?php
ob_start();
###################################################################
# IMPORTA A CLASSE PADRÃO DO SISTEMA
###################################################################
$_SERVER["DOCUMENT_ROOT"] = (substr($_SERVER["DOCUMENT_ROOT"], -1) == '/') ? substr($_SERVER["DOCUMENT_ROOT"], 0, -1) : $_SERVER["DOCUMENT_ROOT"];
include_once($_SERVER['DOCUMENT_ROOT'].'/ws-config.php');

#####################################################################
# 	RETORNA ARQUIVOS CONFORMA O PATERN SETADO
#####################################################################
include_once(ROOT_ADMIN.'/App/Lib/ws-get-typeof.php');

###################################################################
# 	PEGAMOS A URL QUE FOI SETADA
###################################################################
	$REQUEST_URI	= explode('/',$_SERVER["REQUEST_URI"]);
	$BASE_FILE		= array_slice($REQUEST_URI,2);
###################################################################
# VERIFICAMOS DE ONDE ELE DEVE PUXAR O CONTEUDO: ADMIN OU WEBSITE
###################################################################
	if(isset($_GET['type']) && $_GET['type']=='website'){
		$BASE_FILE = ROOT_WEBSITE.'/'.implode('/',$BASE_FILE);
	}else{
		$BASE_FILE = ROOT_ADMIN.'/'.implode('/',$BASE_FILE);
	}

###################################################################
# LIMPA BUFFER PARA SETAR O NOVO HEADER
###################################################################
ob_end_clean();

###################################################################
# VERIFICAMOS DE ONDE ELE DEVE PUXAR O CONTEUDO: ADMIN OU WEBSITE
###################################################################
	@header('Content-Type:'.get_content_type($BASE_FILE));

	if (file_exists($BASE_FILE.'.gz') && @filemtime($BASE_FILE.'.gz') > @filemtime($BASE_FILE)) {
		echo gzdecode(file_get_contents($BASE_FILE.'.gz'));
	}else{
		$content = file_get_contents($BASE_FILE);
		file_put_contents($BASE_FILE.'.gz', gzencode($content));
		echo $content;
	};

exit;




