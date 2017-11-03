<?php 

	############################################################################################################################
	# IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
	############################################################################################################################
	include_once ('./../admin/App/Lib/class-ws-v1.php');

	############################################################################################################################
	# SEPARAMOS O TOKEN DE AUTENTICAÇÃO
	############################################################################################################################
	$_URL_TOKEN = ws::urlPath(2,false);

	###################################################################################
	# EXCLUI QUALQUER TOKEN QUE ESTEJA EXPIRADO NA TABELA DE TEMPLATES
	###################################################################################
	$_EXCL_ = new MySQL();
	$_EXCL_->set_table(PREFIX_TABLES . 'ws_auth_template');
	$_EXCL_->set_where('NOW() > expire');
	$_EXCL_->exclui();
	
	###################################################################################
	# CASO EXISTA UM TOKEN DE ACESSO
	###################################################################################
	if(null != $_URL_TOKEN ){
		$_VERIFY_ = new MySQL();
		$_VERIFY_->set_table(PREFIX_TABLES . 'ws_auth_template');
		$_VERIFY_->set_where('token="'.$_URL_TOKEN.'"');
		$_VERIFY_->select();

		########################################################################################
		# E VERIFICAMOS SE ELE É VÁLIDO NO SISTEMA E SE O TOKEN EXISTE NA TABELA DOS ARQUIVOS
		########################################################################################
		if(ws::getTokenRest($_URL_TOKEN,false,false) && $_VERIFY_->_num_rows == 1){
			$file_url = './'.$_VERIFY_->fetch_array[0]['filename'];

			###################################################################################
			# LIBERA O ARQUIVO PARA DOWNLOAD
			###################################################################################
			header('Content-Description: File Transfer');
			header('Content-Type: application/zip');
			header('Content-Disposition: attachment; filename="'.basename($file_url).'"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			header('Content-Length: '.filesize($file_url));
			readfile($file_url); 
		}else{
			###################################################################################
			# RETORNA O ERRO
			###################################################################################
			header("HTTP/1.0 500 Internal Server Error");
			die();
		}
	}else{
		###################################################################################
		# RETORNA O ERRO
		###################################################################################
			header("HTTP/1.0 500 Internal Server Error");
			die();
	}


















