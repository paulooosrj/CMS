<?php 
	/*
		Este arquivo é parte do Websheep CMS
		Websheep é um software livre; você pode redistribuí-lo e/ou 
		modificá-lo dentro dos termos da Licença Pública Geral GNU como 
		publicada pela Fundação do Software Livre (FSF); na versão 3 da 
		Licença, ou qualquer versão posterior.

		Este programa é distribuído na esperança de que possa ser  útil, 
		mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO
		a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. 
		
		Veja a Licença Pública Geral GNU para maiores detalhes.
		Você deve ter recebido uma cópia da Licença Pública Geral GNU junto
		com este programa, Se não, veja <http://www.gnu.org/licenses/>.
	*/

	ob_start();	
	$r = $_SERVER["DOCUMENT_ROOT"];
	$_SERVER["DOCUMENT_ROOT"] = (substr($r, -1) == '/') ? substr($r, 0, -1) : $r;


	############################################################################################################################
	# CASO NÃO TENHA SIDO VERIFICADO OU SEJA UMA NOVA INSTALAÇÃO/UPDATE IMPORTA VERIFICAÇÃO DO SERVIDOR
	############################################################################################################################
	if(!file_exists($_SERVER["DOCUMENT_ROOT"].'/admin/App/Config/ws-server-ok') || file_exists($_SERVER["DOCUMENT_ROOT"].'/admin/App/Config/firstacess')){
		include($_SERVER["DOCUMENT_ROOT"].'/admin/App/Config/ws-verify-server.php');
	}

	############################################################################################################################
	# CASO NÃO EXISTA O 'ws-config.php' IMPORTA A TELA DE SETUP
	############################################################################################################################
	if(!file_exists($_SERVER["DOCUMENT_ROOT"].'/ws-config.php')) {
		include('./App/Core/ws-setup.php');
		exit;
	}

	############################################################################################################################
	# IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
	############################################################################################################################
	include_once ($_SERVER["DOCUMENT_ROOT"].'/admin/App/Lib/class-ws-v1.php');

	############################################################################################################################
	#	CASO SEJA O 1° ACESSO, IMPORTA A TELA DE INSTALAÇÃO
	############################################################################################################################
	if(file_exists(ROOT_ADMIN.'/App/Config/firstacess') && file_get_contents(ROOT_ADMIN.'/App/Config/firstacess')=='true'){
		include(ROOT_ADMIN.'/App/Core/ws-install.php');exit;
	}

	############################################################################################################################
	#	CRIAMOS A 1° SESSÃO
	############################################################################################################################
		_session();

	############################################################################################################################
	#	CASO ESTEJA LOGADO DIRETAMENTE COM ACCESSKEY
	############################################################################################################################
		if(ws::urlPath(2,false)){
			$keyAccess = ws::getTokenRest(ws::urlPath(2,false),false);
		}else{
			$keyAccess = false;
		}

	############################################################################################################################
	#	CASO ESTEJA LOGADO IMPORTAMOS O DESKTOP
	############################################################################################################################	
		if( SECURE==FALSE || (isset($keyAccess) && $keyAccess==true) || (!empty($_COOKIE['ws_log']) && $_COOKIE['ws_log']=='true') && (!empty($_SESSION) && @$_SESSION['ws_log']==true)){	
			include(ROOT_ADMIN.'/App/Core/ws-dashboard.php');exit;
		}

	############################################################################################################################
	#	CASO ESTEJA OFFLINE JÁ DIRECIONA PRO LOGIN
	############################################################################################################################
	include(ROOT_ADMIN.'/App/Modulos/login/index.php');
	exit;