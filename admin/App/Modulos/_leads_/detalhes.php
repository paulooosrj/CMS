<?php
	#####################################################  
	# FORMATA O CAMINHO ROOT
	#####################################################
	$r                        = $_SERVER["DOCUMENT_ROOT"];
	$_SERVER["DOCUMENT_ROOT"] = (substr($r, -1) == '/') ? substr($r, 0, -1) : $r;

	#####################################################  
	# DEFINE O PATH DO MÓDULO 
	#####################################################
	define("PATH", 'App/Modulos/_leads_');
		
	#####################################################  
	# LIMPA O CACHE INTERNO
	#####################################################
	clearstatcache();
	
	#####################################################  
	# CONTROLA O CACHE
	#####################################################
	header("Expires: Mon, 26 Jul 1990 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	
	#####################################################  
	# IMPORTA A CLASSE PADRÃO DO SISTEMA
	#####################################################
	ob_start();
	include($_SERVER['DOCUMENT_ROOT'] . '/admin/App/Lib/class-ws-v1.php');
	
	#####################################################  
	# CRIA SESSÃO
	#####################################################  
	_session();

	#####################################################  
	# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
	#####################################################
	verifyUserLogin();
	
	#####################################################  
	# GERA O TOKEN GROUP PARA UPLOAD
	#####################################################
	if(empty($_SESSION['token_group'])){
		$_SESSION['token_group'] = _crypt();
	}
	$s = new MySQL();
	$s->set_table(PREFIX_TABLES.'ws_list_leads');
	$s->set_where('token="'.$_GET['lead'].'"');
	$s->select();
	$lead = $s->obj[0];

	$_COLUNAS_ = new MySQL();
	$_COLUNAS_->set_table(strtolower(PREFIX_TABLES.'wslead_'.$_GET['lead']));
	$_COLUNAS_->show_columns();

	#####################################################  
	# DEFINE O LINK DO TEMPLATE DESTE MÓDULO 
	#####################################################  
	define("TEMPLATE_LINK", ROOT_ADMIN . "/App/Templates/html/Modulos/_leads_/ws-tool-leads-detalhes.html");
	$template           				= new Template(TEMPLATE_LINK, true);

	#####################################################  
	# DEFINIMOS AS VARIÁVEIS NECESSÁRIAS PARA O MÓDULO 
	#####################################################
	$template->PATH 					= 'App/Modulos/_leads_';
	$template->DOMINIO 					= ws::protocolURL().DOMINIO;
	$template->lead_token			 	= strtolower($lead->token);
	$template->lead_title				= $lead->title;
	$template->lead_content			 	= $lead->content;
	$template->lead_SMTPSecure			= $lead->SMTPSecure;
	$template->lead_host				= $lead->host;
	$template->lead_resposta_ao_usuario = ($lead->resposta_ao_usuario 	==1)	? 'checked="true"': "";
	$template->lead_smtp_local 			= ($lead->smtp_local 			==1)	? 'checked="true"': "";
	$template->lead_server_ssl  		= ($lead->server_ssl 			==1)	? 'checked="true"': "";
	$template->lead_email_envio			= $lead->email_envio;
	$template->lead_pass			 	= $lead->pass;
	$template->lead_remetente_name		= $lead->remetente_name;
	$template->lead_remetente			= $lead->remetente;
	$template->lead_assunto			 	= $lead->assunto;
	$template->lead_assunto_clt			= $lead->assunto_clt;
	$template->lead_msng_resp_user		= $lead->msng_resp_user;
	$template->lead_url_sucess			= $lead->url_sucess;
	$template->lead_url_error			= $lead->url_error;
	$template->lead_header_email		= $lead->header_email;
	$template->lead_footer_email		= $lead->footer_email;
	$template->lead_camp_mail_clt		= $lead->camp_mail_clt;
	$template->lead_finalidade			= $lead->finalidade;
	$template->lead_msng_resp		 	= $lead->msng_resp;
	$template->lead_port		 		= $lead->port;
	$template->lead_id			 		= $lead->id;
	$template->token_group 				= (isset($_SESSION['token_group'])) ? $_SESSION['token_group']:_crypt();
 
	############################################################### 
	# LISTA AS COLUNAS CADASTRADAS PARA DEFINIR O CAMPO DE EMAIL 
	###############################################################
	foreach($_COLUNAS_->fetch_array as $coluna){
		if($coluna['Field']!="id"){
			$template->OPT_COLUMN = $coluna['Field'];
			$template->block("COLUMNS"); 
		}
	};

	##################################################################
	# RETORNAMOS AS COLUNAS NOVAMENTE PARA INSERÇÃO NO EMAIL AO ADMIN 
	##################################################################
	foreach($_COLUNAS_->fetch_array as $coluna){
		if($coluna['Field']!="id"){
			$template->A_FIELD = $coluna['Field'];
			$template->block("FIELD_MSN_ADM"); 
		}
	};

	#####################################################################
	# RETORNAMOS AS COLUNAS NOVAMENTE PARA INSERÇÃO NO EMAIL AO USUÁRIO 
	#####################################################################
	foreach($_COLUNAS_->fetch_array as $coluna){
		if($coluna['Field']!="id"){
			$template->A_FIELD = $coluna['Field'];
			$template->block("FIELD_MSN_USER"); 
		}
	};

	#####################################################################
	# FINALIZAMOS O ARQUIVO, SETAMOS O BLOCO E RETORNAMOS O HTML  
	#####################################################################
	$template->block("DETALHES_LEAD"); 
	$template->show(); 
