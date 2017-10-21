<? 
	############################################
	#	DEFINE CHARSET
	############################################
	 header("Content-Type: text/html; charset=utf-8",true);

	############################################
	#	CARREGA SESSION
	############################################
	$user = new session();


	##########################################################################################
	#  VERSÃO DO SISTEMA   
	##########################################################################################
	$localVersion  = json_decode(file_get_contents(ROOT_ADMIN.'/App/Templates/json/ws-update.json'));

	##########################################################################################
	#  PUXAMOS OS DADOS BÁSICOS DA INSTALAÇÃO
	##########################################################################################
	$setupdata 					= new MySQL();
	$setupdata->set_table(PREFIX_TABLES.'setupdata');
	$setupdata->set_order('id','DESC');
	$setupdata->set_limit(1);
	$setupdata->debug(0);
	$setupdata->select();
	$setupdata = $setupdata->fetch_array[0];

	##########################################################################################
	#  VERIFICAMOS AS PERMIÇÕES DE ACESSO A FERRAMENTA DO USUÁRIO 
	##########################################################################################
	$_permissao_user_ 					= new MySQL();
	$_permissao_user_->set_table(PREFIX_TABLES.'ws_user_link_ferramenta');

	##########################################################################################
	#  CASO O PAINEL SEJA INICIADO NO MODO 'INSECURE'  IGNORA AS PERMISSÕES E LIBERA TUDO
	##########################################################################################
	if (SECURE!=FALSE && $user->verify()){
		$_permissao_user_->set_where('id_user="'.$user->get('id').'"');
	}
	$_permissao_user_->select();

	##########################################################################################
	#  GUARDAMOS TODAS AS PERMIÇÕES EM UMA ARRAY
	##########################################################################################
	$permTool = array();
	foreach ($_permissao_user_->fetch_array as $tool) {$permTool[] = $tool['id_ferramenta'];}

	##########################################################################################
	#  GRAVA UM JSON COM OS PLUGINS ATIVOS NO SITE
	##########################################################################################
	refreshJsonPluginsList();
	$string 		= file_get_contents(ROOT_ADMIN.'/App/Templates/json/ws-plugin-list.json');
	$jsonPlugins 	= json_decode($string);

	##########################################################################################
	#  INICIAMOS A CLASSE TEMPLATE
	##########################################################################################
	$TEMPLATE = new Template(ROOT_ADMIN . "/App/Templates/html/ws-dashboard-template.html", true);

	##########################################################################################
	#  CASO NÃO TENHA NENHUM SPLASH CADASTRADO, PUXAMOS O PADRÃO DO SISTEMA
	##########################################################################################
	if($setupdata['splash_img']==""){
			$TEMPLATE->block("NOSPLASH");
	}else{
			$TEMPLATE->SPLASHSCREEN_IMG = $setupdata['splash_img'];
			$TEMPLATE->block("SPLASHSCREEN");
	}

	##########################################################################################
	#  AQUI VERIFICAMOS APENAS PARA AVISAR QUE ESTÁ NO MODO 'INSECURE'   
	##########################################################################################

		if (SECURE!=FALSE && @$log_session->get('ws_log')==1){
			$name = $user->get('nome');
		}else{
			$name = "<span style='color:#f00;font-weight:bold;'>INSECURE</span>";
		}

	##########################################################################################
	#  DEFINE A SAUDAÇÃO   
	##########################################################################################
		$TEMPLATE->SAUDACAO =  ws::getlang('dashboard>welcome',array('[avatar]','[username]'),array('',$name));


	##########################################################################################
	#  LISTANDO OS PLUGINS DE TOPO   
	##########################################################################################
		foreach ($jsonPlugins as $plugin) {
			$contents 				=	$plugin;
			$_menu_existe  	 		= isset($contents->menu);
			$_painel_existe  		= (isset($contents->painel) && $contents->painel!="");
			$_menuArrayAndTopo   	= (is_array(@$contents->menu) && in_array("topo",$contents->menu));
			$_menuStringAndTopo   	= (is_string(@$contents->menu) && @$contents->menu =="topo");
			$is_loadType   			= isset($contents->loadType) && is_array($contents->loadType);

			##########################################################################################
			#  CASO ELE ESTEJA CONFIGURADO PARA APARECER NO MENU DE TOPO   
			##########################################################################################
				if(($_menu_existe  && $_painel_existe ) && ($_menuArrayAndTopo	||	$_menuStringAndTopo)){
					if($is_loadType){
						$dataType 	= @$contents->loadType[0];
						$dataW 		= @$contents->loadType[1];
						$dataH 		= @$contents->loadType[2];
					}else{
						$dataType 	= $contents->loadType;
						$dataW 		= 500;
						$dataH 		= 500;
					}

					##########################################################################################
					#  SEPARA AS VARIÁVEIS DA LISTAGEM   
					##########################################################################################
						$TEMPLATE->LI_W 	= @$dataW;
						$TEMPLATE->LI_H 	= @$dataH;
						$TEMPLATE->LI_TYPE 	= @$dataType;

						if(@$dataType!="iframe"){
							$TEMPLATE->LI_PATH 	= str_replace('/'.$setupdata['url_plugin'].'/',"", $contents->realPath);
							$TEMPLATE->LI_FILE 	= $contents->painel;
						}else{
							$TEMPLATE->LI_PATH 	= $contents->realPath;
							$TEMPLATE->LI_FILE 	= $contents->realPath.'/'.$contents->painel;
							
						}


						if(isset($contents->icon) &&$contents->icon!="" &&file_exists(ROOT_WEBSITE.$contents->realPath.'/'.$contents->icon)){
							$TEMPLATE->LI_ICON 	= '<img src="'.$contents->realPath.'/'.$contents->icon.'" width="15px"/>';
						}else{
							$TEMPLATE->clear("LI_ICON");
						}
						$TEMPLATE->LI_NAME 	= $contents->pluginName;

					##########################################################################################
					#  RETORNA A STRING DO PLUGIN   
					##########################################################################################
						$TEMPLATE->block('TOP_ICONS_PLUGINS');
				}
		}

	##########################################################################################
	#  MENSAGEM COPYRIGHT DO RODAPÉ   
	##########################################################################################
	$TEMPLATE->copyright 	= ws::getlang('dashboard>footerCopyright',array('[name]','[system_version]'),array($setupdata['client_name'],$localVersion->version));


	##########################################################################################
	#     
	##########################################################################################



	##########################################################################################
	#  VERIFICA SE EXISTE 2° PATH NA URL
	##########################################################################################
	$keyAccess = ws::urlPath(2,false);
 	if($keyAccess){
		##########################################################################################
		#  CASO O 2° PATH SEJA UM ACESSO DIRETO, PUXAMOS DA BASE A CHAVE
		##########################################################################################
		$ws_direct_access 				= new MySQL();
		$ws_direct_access->set_table(PREFIX_TABLES.'ws_direct_access');
		$ws_direct_access->set_where('keyaccess="'.$keyAccess.'"');
		$ws_direct_access->select();
		$_num_rows 						= $ws_direct_access->_num_rows;
		$ws_direct_access 				= $ws_direct_access->fetch_array;
		##########################################################################################
		#  CASO EXISTA O SERIALKEY RETORNA 
		##########################################################################################
		if($_num_rows>0){
			$TEMPLATE->type_obj 		= $ws_direct_access[0]['type_obj'];
			$TEMPLATE->id_tool 			= $ws_direct_access[0]['id_tool'];
			$TEMPLATE->id_item 			= $ws_direct_access[0]['id_item'];
			$TEMPLATE->id_gal 			= $ws_direct_access[0]['id_gal'];
			$TEMPLATE->block('DIRECTACCESS');
		}else{
			$TEMPLATE->clear('DIRECTACCESS');
		}
 	}		
 	$TEMPLATE->classHTMLtypeAcess 	= ( isset($_num_rows) && $_num_rows>0 ) ? "IframeModel" : "";
	##########################################################################################
	#  RETORNA O HTML MONTADO   
	##########################################################################################
	$TEMPLATE->block("DASHBOARD");

	##########################################################################################
	#  COMPILA O JAVASCRIPT CASO NAO TENHA SIDO COMPILADO   
	##########################################################################################
		if(
			(!file_exists(ROOT_ADMIN.'/App/Templates/js/websheep/funcionalidades.min.js') || !file_exists(ROOT_ADMIN.'/App/Templates/js/websheep/functionsws.min.js')) 	||
			(filemtime(ROOT_ADMIN.'/App/Templates/js/websheep/funcionalidades.js') 	> filemtime(ROOT_ADMIN.'/App/Templates/js/websheep/funcionalidades.min.js')) 		||
			(filemtime(ROOT_ADMIN.'/App/Templates/js/websheep/functionsws.js') 		> filemtime(ROOT_ADMIN.'/App/Templates/js/websheep/functionsws.min.js')) 
		){
			ws::compileJS();
		}

	##########################################################################################
	#  PRINTA RESULTADO   
	##########################################################################################
	$TEMPLATE->show();

