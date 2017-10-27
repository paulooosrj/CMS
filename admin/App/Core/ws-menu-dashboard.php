<?
	#####################################################  
	# FUNÇÕES DO MODULO
	#####################################################  
	// _session();
	$user = new Session();

	#####################################################  
	# GET SETUP DATA
	#####################################################
	$setupdata = new MySQL();
	$setupdata->set_table(PREFIX_TABLES . 'setupdata');
	$setupdata->set_order('id', 'DESC');
	$setupdata->set_limit(1);
	$setupdata->debug(0);
	$setupdata->select();
	$setupdata = $setupdata->fetch_array[0];
	
	#####################################################  
	# DEFINE PATH DOS PLUGINS
	#####################################################
	define("PLUGIN_PATH", ROOT_WEBSITE . '/' . $setupdata['url_plugin']);
	
	#####################################################  
	# IMPORTA CLASS DO TEMPLATE
	#####################################################
	$menu_dashboard = new Template(ROOT_ADMIN . '/App/Templates/html/ws-dashboard-menu.html', true);
	

	##########################################################################################
	#  ANTES DE MONTAR O MENU, VERIFICAMOS A VERSÃO O PAINEL   
	##########################################################################################
	$remoteVersion = json_decode(@file_get_contents("https://raw.githubusercontent.com/websheep/cms/master/admin/App/Templates/json/ws-update.json"));
	$localVersion  = json_decode(file_get_contents(ROOT_ADMIN.'/App/Templates/json/ws-update.json'));

	 if($remoteVersion && ws::version_compare($localVersion->version,$remoteVersion->version)==-1){
	 	$menu_dashboard->newVersion 		= $remoteVersion->version;
	 	$menu_dashboard->newVersionContent 	= implode($remoteVersion->features,"<br>");	
	 	$menu_dashboard->block('NEW_VERSION');
	 } 


	###############################################################################################################  
	# PLUGINS:  Varre os diretórios dos plugins, separa a configuração de cada um deles, e printa a opção no menu 
	###############################################################################################################	
	if (is_dir(PLUGIN_PATH)) {
		$dh = opendir(PLUGIN_PATH);
		while ($diretorio = readdir($dh)) {
			if ($diretorio != '..' && $diretorio != '.' && $diretorio != '.htaccess') {
				if (file_exists(PLUGIN_PATH . '/' . $diretorio . '/active')) {
					$phpConfig = PLUGIN_PATH . '/' . $diretorio . '/plugin.config.php';
					if (file_exists($phpConfig)) {
						ob_start();
						@include($phpConfig);
						$jsonRanderizado = ob_get_clean();
						$contents        = $plugin;
					}
					if (isset($contents)) {
						if ((isset($contents->menu) && ((is_array($contents->menu) && in_array("lateral", $contents->menu)) || $contents->menu == "lateral")) && isset($contents->painel) && $contents->painel != "") {
							if (isset($contents->loadType) && is_array($contents->loadType)) {
								$dataType = $contents->loadType[0];
								$dataW    = $contents->loadType[1];
								$dataH    = $contents->loadType[2];
							} else {
								$dataType = "";
								$dataW    = "";
								$dataH    = "";
							}
							if (filter_var($contents->painel, FILTER_VALIDATE_URL) === FALSE) {
								$link = '/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $contents->painel;
							} else {
								$link = $contents->painel;
							}
							
							########################################################################################################  
							# GUARDA AS INFORMAÇÕES NO TEMPLATE QUE SERÁ UTILIZADO LOGO A FRENTE
							########################################################################################################
							$menu_dashboard->PATH  = $link;
							$menu_dashboard->W     = @$dataW;
							$menu_dashboard->H     = @$dataH;
							$menu_dashboard->TYPE  = @$dataType;
							$menu_dashboard->ICON  = '/' . $setupdata['url_plugin'] . '/' . $diretorio . '/' . $contents->icon;
							$menu_dashboard->LABEL = $contents->pluginName;
							$menu_dashboard->block("PLUGIN");
						}
					}
				}
			}
		}
	}
		
	########################################################################################################  
	# FERRAMENTAS
	########################################################################################################
	$_WS_TOOL_ = new MySQL();
	$_WS_TOOL_->set_table(PREFIX_TABLES . 'ws_ferramentas as tools');
	$_WS_TOOL_->set_colum('tools.id as id_tool');
	$_WS_TOOL_->set_colum('tools._tit_topo_ as titulo');
	$_WS_TOOL_->set_order('posicao', 'ASC');
	$_WS_TOOL_->set_where('_plugin_="0"');
	$_WS_TOOL_->set_where('AND App_Type="1"');
	



	if (SECURE!=FALSE && @$user->get('admin') ==0) {
		$_WS_TOOL_->join(' INNER ', PREFIX_TABLES . 'ws_user_link_ferramenta as link', 'link.id_ferramenta=tools.id AND link.id_user="' . $user->get('id') . '"');
	}
	$_WS_TOOL_->select();
	

	########################################################################################################  
	# FOREACH NAS FERRAMENTAS CRIADAS E GUARDAMOS OS DADOS NO TEMPLATE 
	########################################################################################################
	foreach ($_WS_TOOL_->fetch_array as $inner_menu) {
		$menu_dashboard->ID    = $inner_menu['id_tool'];
		$menu_dashboard->LABEL = $inner_menu['titulo'];
		$menu_dashboard->block("TOOL");
	}

	if ( SECURE==FALSE || $user->get('admin')==1) {
		$menu_dashboard->block("ADMIN");
	}





	$menu_dashboard->label_newversion 				= ws::getlang('dashboard>NewVersion');
	$menu_dashboard->label_paginas 					= ws::getlang('dashboard>lateralMenu>ManagePages');
	$menu_dashboard->label_ferramentas 				= ws::getlang('dashboard>lateralMenu>MyTools>main');
	$menu_dashboard->label_gerenciar_ferramentas 	= ws::getlang('dashboard>lateralMenu>MyTools>manage');
	$menu_dashboard->label_links			 		= ws::getlang('dashboard>lateralMenu>URLsIncludes>main');
	$menu_dashboard->label_url_includes	 			= ws::getlang('dashboard>lateralMenu>URLsIncludes>navigation');
	$menu_dashboard->label_url_htaccess	 			= ws::getlang('dashboard>lateralMenu>URLsIncludes>htaccess');
	$menu_dashboard->label_include_css	 			= ws::getlang('dashboard>lateralMenu>URLsIncludes>css');
	$menu_dashboard->label_include_js	 			= ws::getlang('dashboard>lateralMenu>URLsIncludes>js');
	$menu_dashboard->label_plugin 					= ws::getlang('dashboard>lateralMenu>Plugins>main');
	$menu_dashboard->label_gerenciar_plugin 		= ws::getlang('dashboard>lateralMenu>Plugins>manage');
	$menu_dashboard->label_plugin_instalado 		= ws::getlang('dashboard>lateralMenu>Plugins>installedPlugins');
	$menu_dashboard->label_editor	 				= ws::getlang('dashboard>lateralMenu>CodeEditor');
	$menu_dashboard->label_Conf_Painel				= ws::getlang('dashboard>lateralMenu>PanelConfiguration');
	$menu_dashboard->label_Download_Senha			= ws::getlang('dashboard>lateralMenu>DownloadPassword');
	$menu_dashboard->label_Cadastros				= ws::getlang('dashboard>lateralMenu>AdditionalRegistrations');
	$menu_dashboard->label_BKP	 					= ws::getlang('dashboard>lateralMenu>BKPCentral');
	$menu_dashboard->label_Usuarios					= ws::getlang('dashboard>lateralMenu>Users');
	$menu_dashboard->label_Biblioteca				= ws::getlang('dashboard>lateralMenu>ImageLibrary');
	$menu_dashboard->label_hd 						= ws::getlang('dashboard>lateralMenu>HDManagement');
	$menu_dashboard->logRecords 					= ws::getlang('dashboard>lateralMenu>logRecords');
	$menu_dashboard->label_logout					= ws::getlang('dashboard>lateralMenu>Logout');
	$menu_dashboard->label_reportBugs				= ws::getlang('dashboard>lateralMenu>reportBugs');
	$menu_dashboard->dashboard_modal_logOut_content	= ws::getlang('dashboard>modal>logOut>content');
	$menu_dashboard->dashboard_modal_logOut_bot1	= ws::getlang('dashboard>modal>logOut>bot1');
	$menu_dashboard->dashboard_modal_logOut_bot2	= ws::getlang('dashboard>modal>logOut>bot2');


	$menu_dashboard->label_logout_loading			= ws::getlang('dashboard>modal>logOut>loading');

	$menu_dashboard->block("MENU_DASHBOARD");
	$menu_dashboard->show();