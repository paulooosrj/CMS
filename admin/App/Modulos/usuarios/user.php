<?php
	#####################################################  
	# FORMATA O CAMINHO ROOT
	#####################################################
	$r                        = $_SERVER["DOCUMENT_ROOT"];
	$_SERVER["DOCUMENT_ROOT"] = (substr($r, -1) == '/') ? substr($r, 0, -1) : $r;

	#####################################################  
	# DEFINE O PATH DO MÓDULO 
	#####################################################
	define("PATH", 'App/Modulos/usuarios');
		
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
	include(__DIR__.'/../../Lib/class-ws-v1.php');
	
	#####################################################  
	# VERIFICA SE ESTAMOS USANDO O MODO "INSECURE"  
	#####################################################
	if(SECURE===FALSE) die(_erro(" NÃO É POSSÍVEL ACESSAR O GERENCIADOR DE USUÁRIOS NO MODO 'INSECURE'"));

	#####################################################  
	# CRIA SESSÃO
	#####################################################  
	$user = new session();
	#####################################################  
	# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
	#####################################################
	verifyUserLogin();
	
	#####################################################  
	# PUXAMOS OS DADOS DA BASE 
	#####################################################  
	$USUARIO = new MySQL();
	$USUARIO->set_table(PREFIX_TABLES.'ws_usuarios');
	$USUARIO->set_where('id="'.$_GET['id_user'].'"');
	$USUARIO->select();
	$USUARIO = $USUARIO->fetch_array[0];


	################################################################################
	# MONTAMOS A CLASSE DOS TEMPLATES 
	################################################################################
	$template           										= new Template(ROOT_ADMIN.'/App/Templates/html/Modulos/usuarios/ws-tool-usuarios-details.html', true);
	$template->PATH 											= 'App/Modulos/usuarios';
	$template->USER_ID 											= $USUARIO['id'];
	$template->USER_NAME 										= $USUARIO['nome'];
	$template->USER_SOBRENOME 									= $USUARIO['sobrenome'];
	$template->USER_EMAIL 										= $USUARIO['email'];
	$template->USER_LOGIN 										= $USUARIO['login'];
	$template->USER_RG 											= $USUARIO['RG'];
	$template->USER_CPF 										= $USUARIO['CPF'];
	$template->userManager_details_back 						= ws::getLang("userManager>details>back");
	$template->userManager_details_save 						= ws::getLang("userManager>details>save");
	$template->userManager_details_userData 					= ws::getLang("userManager>details>userData");
	$template->userManager_details_labels_name 					= ws::getLang("userManager>details>labels>name");
	$template->userManager_details_labels_lastName 				= ws::getLang("userManager>details>labels>lastName");
	$template->userManager_details_labels_name 					= ws::getLang("userManager>details>labels>name");
	$template->userManager_details_labels_lastName 				= ws::getLang("userManager>details>labels>lastName");
	$template->userManager_details_labels_email 				= ws::getLang("userManager>details>labels>email");
	$template->userManager_details_labels_login 				= ws::getLang("userManager>details>labels>login");
	$template->userManager_details_labels_pass 					= ws::getLang("userManager>details>labels>pass");
	$template->userManager_details_labels_login 				= ws::getLang("userManager>details>labels>login");
	$template->userManager_details_labels_pass 					= ws::getLang("userManager>details>labels>pass");
	$template->userManager_details_labels_rg 					= ws::getLang("userManager>details>labels>rg");
	$template->userManager_details_labels_cpf 					= ws::getLang("userManager>details>labels>cpf");
	$template->userManager_details_labels_rg 					= ws::getLang("userManager>details>labels>rg");
	$template->userManager_details_labels_cpf 					= ws::getLang("userManager>details>labels>cpf");
	$template->userManager_details_labels_basicData 			= ws::getLang("userManager>details>labels>basicData");
	$template->userManager_details_labels_readOnly 				= ws::getLang("userManager>details>labels>readOnly");
	$template->userManager_details_labels_administrator 		= ws::getLang("userManager>details>labels>administrator");
	$template->userManager_details_labels_canAddAndEditUsers 	= ws::getLang("userManager>details>labels>canAddAndEditUsers");
	$template->userManager_details_labels_edit_only_own			= ws::getLang("userManager>details>labels>editOnlyOwn");
	$template->userManager_details_labels_panelStatus 			= ws::getLang("userManager>details>labels>panelStatus");
	$template->userManager_details_labels_normal 				= ws::getLang("userManager>details>labels>normal");
	$template->userManager_details_labels_invalidAccess 		= ws::getLang("userManager>details>labels>invalidAccess");
	$template->userManager_details_labels_inactiveDashboard 	= ws::getLang("userManager>details>labels>inactiveDashboard");
	$template->userManager_details_labels_evaluation 			= ws::getLang("userManager>details>labels>evaluation");
	$template->userManager_details_labels_blocked 				= ws::getLang("userManager>details>labels>blocked");
	$template->userManager_details_labels_levelsOfAccess 		= ws::getLang("userManager>details>labels>levelsOfAccess");
	$template->ADMIN_CHECK 										= ($USUARIO['admin']=='1') 			?	'checked="true"' : "";
	$template->CHECK_ADD_USER									= ($USUARIO['add_user']=='1') 		?	'checked="true"' : "";
	$template->CHECK_EDIT_ONLY_OWN								= ($USUARIO['edit_only_own']=='1')	?	'checked="true"' : "";
	$template->CHECK_READING									= ($USUARIO['leitura']=='1') 		?	'checked="true"' : "";
	$template->block('ADMIN_USER');


	if($_GET['id_user']!=$user->get('id')){
		$innerTool = new MySQL();
		$innerTool->set_table(PREFIX_TABLES.'ws_ferramentas');
		$innerTool->set_where('App_Type="1"');
		$innerTool->select();
		foreach($innerTool->fetch_array as $tool){
			$AccessTool = new MySQL();
			$AccessTool->set_table(PREFIX_TABLES.'ws_user_link_ferramenta');
			$AccessTool->set_where('id_user="'.$_GET['id_user'].'" AND id_ferramenta="'.$tool['id'].'"');
			$AccessTool->select();

			$template->LI_TOOL_ID 		= $tool['id'];
			$template->LI_TOOL_TITLE 	= $tool['_tit_menu_'];
			$template->BG 				= 'bg01';

			if($USUARIO['admin']=='1'){ 
				$template->BG 				= 'bg04';
				$template->LI_TOOL_CHECK 	= 'checked="checked"';
				$template->LI_TOOL_DISABLE 	= 'disabled="disabled"';

			}elseif($AccessTool->_num_rows==1){
				$template->BG 				= 'bg04';
				$template->LI_TOOL_CHECK 	= 'checked="checked"';
				$template->clear("LI_TOOL_DISABLE");
			}else{
				$template->clear("LI_TOOL_CHECK");
				$template->clear("LI_TOOL_DISABLE");
			}
			$template->block('LI_TOOLS');
		}
	}


	$template->block('BLOCKUSER');
	$template->show();