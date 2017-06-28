<?php
	ob_start();
	include($_SERVER['DOCUMENT_ROOT'] . '/admin/App/Lib/class-ws-v1.php');
	_session();
	clearstatcache();
	if (empty($_GET['id_cat'])) {
			$_GET['id_cat'] = '0';
	}
	if (empty($_GET['ws_nivel'])) {
			$_GET['ws_nivel'] = '0';
	}

	if (empty($_GET['ws_id_ferramenta'])) {
		die("Empty _GET['ws_id_ferramenta'] ");
	}

	if(empty($_GET['LIMIT']))	{$_GET['LIMIT']="50";}
	if(empty($_GET['PAGE']))	{$_GET['PAGE']="1";}




	$FERRAMENTA              = new MySQL();
	$FERRAMENTA->set_table(PREFIX_TABLES . 'ws_ferramentas');
	$FERRAMENTA->set_order('posicao', 'ASC');
	$FERRAMENTA->set_where('id="' . $_GET['ws_id_ferramenta'] . '"');
	$FERRAMENTA->debug(0);
	$FERRAMENTA->select();
	$_FERRAMENTA_ = $FERRAMENTA->fetch_array[0];
	$_TIT_PAI_    = new MySQL();
	$_TIT_PAI_->set_table(PREFIX_TABLES . '_model_cat');
	$_TIT_PAI_->set_where('id="' . $_GET['id_cat'] . '"');
	$_TIT_PAI_->debug(0);
	$_TIT_PAI_->select();
	ob_end_clean();

	define("PATH", 'App/Modulos/_modulo_');
	define("ID_FERRAMENTA", $_GET['ws_id_ferramenta']);
	define("WS_NIVEL", $_GET['ws_nivel']);
	$_SET_TEMPLATE_INPUT                							= new Template(ROOT_ADMIN . "/App/Templates/html/Modulos/ws-tool-itens-template.html", true);
	$_SET_TEMPLATE_INPUT->ID_CAT        							= 0;
	$_SET_TEMPLATE_INPUT->PATH          							= PATH;
	$_SET_TEMPLATE_INPUT->ID_FERRAMENTA 							= ID_FERRAMENTA;
	$_SET_TEMPLATE_INPUT->WS_NIVEL      							= WS_NIVEL;
	$_SET_TEMPLATE_INPUT->TOKEN_GROUP   							= $_GET['token_group'];
	$_SET_TEMPLATE_INPUT->modulo_itens_buttons_back 				= ws::getLang("modulo>itens>buttons>back");
	$_SET_TEMPLATE_INPUT->modulo_itens_modal_back_content 			= ws::getLang("modulo>itens>modal>back>content");
	$_SET_TEMPLATE_INPUT->modulo_itens_buttons_category 			= ws::getLang("modulo>itens>buttons>category");
	$_SET_TEMPLATE_INPUT->modulo_itens_buttons_addItem 				= ws::getLang("modulo>itens>buttons>addItem");
	$_SET_TEMPLATE_INPUT->modulo_itens_modal_loading_content 		= ws::getLang("modulo>itens>modal>loading>content");
	$_SET_TEMPLATE_INPUT->modulo_itens_modal_addItem_content 		= ws::getLang("modulo>itens>modal>addItem>content");
	$_SET_TEMPLATE_INPUT->modulo_itens_modal_delete_content 		= ws::getLang("modulo>itens>modal>delete>content");
	$_SET_TEMPLATE_INPUT->modulo_itens_modal_delete_bot1 			= ws::getLang("modulo>itens>modal>delete>bot1");
	$_SET_TEMPLATE_INPUT->modulo_itens_modal_delete_bot2 			= ws::getLang("modulo>itens>modal>delete>bot2");
	$_SET_TEMPLATE_INPUT->modulo_itens_modal_delete_beforeContent 	= ws::getLang("modulo>itens>modal>delete>beforeContent");
	$_SET_TEMPLATE_INPUT->modulo_itens_topAlert_sucessDelete 		= ws::getLang("modulo>itens>topAlert>sucessDelete");
	$_SET_TEMPLATE_INPUT->TITULO_TOOL   							= $_FERRAMENTA_['_tit_topo_'];
	$_SET_TEMPLATE_INPUT->LIMIT 									= $_GET['LIMIT'];
	$_SET_TEMPLATE_INPUT->PAGE 										= $_GET['PAGE'];

	#######################################################################
	# CASO TENHA ALGUMA PALAVRA CHAVE DE PESQUISA, ESCONDE OS OUTROS BOTÕES
	#######################################################################
	if(isset($_GET['keywork'])){
			$link_back = "./".PATH."/itens.php?PAGE=".$_GET['PAGE']."&LIMIT=".$_GET['LIMIT']."&token_group=".$_GET['token_group']."&ws_id_ferramenta=".ID_FERRAMENTA.'&LIMIT='.$_GET['LIMIT'];
			$_SET_TEMPLATE_INPUT->LINK_BACK = $link_back;
			$_SET_TEMPLATE_INPUT->KEYWORK 	=	' - "'.$_GET['keywork'].'"';
			$_SET_TEMPLATE_INPUT->clear("BT_CAT");
			$_SET_TEMPLATE_INPUT->clear("BT_ADD");
			$_SET_TEMPLATE_INPUT->block("BT_BACK");
			$_SET_TEMPLATE_INPUT->clear("FILTER_KEYUP");
	} else {
			$_SET_TEMPLATE_INPUT->block("FORM_SEARCH");
			$_SET_TEMPLATE_INPUT->clear("LINK_BACK");
			$_SET_TEMPLATE_INPUT->clear("KEYWORK");
			$_SET_TEMPLATE_INPUT->block("BT_ADD");
			$_SET_TEMPLATE_INPUT->block("FILTER_KEYUP");
	}

	####################################################################################
	# CASO A PAGINAÇÃO SEJA "ALL" E Ñ TENHA PESQUISA, ELE MOSTRA O BOTÃO DE MOVER O ÍTEM
	####################################################################################
	$_SET_TEMPLATE_INPUT->STYLEMOVESHOW = (empty($_GET['keywork']) && isset($_GET['LIMIT']) && $_GET['LIMIT']=="All") ? "display:initial" : "display:none";
	$_SET_TEMPLATE_INPUT->LIMIT			= $_GET['LIMIT'];


	if ($_FERRAMENTA_['_niveis_'] >= 1) {
			$_SET_TEMPLATE_INPUT->block("BT_CAT");
	}
	$s = new MySQL();
	$s->set_table(PREFIX_TABLES . "ws_ferramentas");
	$s->set_where('id="' . $_GET['ws_id_ferramenta'] . '"');
	$s->select();
	$colunas = @$s->fetch_array[0]['det_listagem_item'];
	if ($colunas == ""){$colunas = "id";}

	$itens = new MySQL();
	$itens->url('decode');
	$itens->set_table(PREFIX_TABLES . "_model_item ");
	$itens->set_where(' ws_id_draft="0" ');
	// $itens->set_where(' AND id_cat="' . $_GET['id_cat'] . '" ');
	$itens->set_where(' AND ws_id_ferramenta="' . $_GET['ws_id_ferramenta'] . '" ');
	$itens->set_colum('id');
	$rows = explode(',', $colunas);
	foreach ($rows as $value) {
			$campo = new MySQL();
			$campo->set_table(PREFIX_TABLES . "_model_campos");
			$campo->set_where('coluna_mysql="' . $value . '"');
			$campo->select();
			if ($colunas == "id") {
					// $_SET_TEMPLATE_INPUT->CLASS     = "bg05";
					$_SET_TEMPLATE_INPUT->NAMECOLUM = "ID";
			} elseif ($value == "posicao") {
					// $_SET_TEMPLATE_INPUT->CLASS     = "bg05 data-posicao";
					$_SET_TEMPLATE_INPUT->NAMECOLUM = "Posição";
			} elseif (@$campo->fetch_array[0]['type'] == 'thumbmail') {
					// $_SET_TEMPLATE_INPUT->CLASS     = "nosort bg01";
					$_SET_TEMPLATE_INPUT->NAMECOLUM = @$campo->fetch_array[0]['listaTabela'];
			} else {
					// $_SET_TEMPLATE_INPUT->CLASS     = "bg05";
					$_SET_TEMPLATE_INPUT->NAMECOLUM = @$campo->fetch_array[0]['listaTabela'];
			}
			$_SET_TEMPLATE_INPUT->block("THEAD");
			$itens->set_colum($value);
	}

		########################################################################
		# LIMIT DOS RESULTADOS
		########################################################################
		if($_GET['LIMIT']!="All" && empty($_GET['keywork'])){
			$_atual_page_ = $_GET['PAGE'];
			$_max_posts_  = $_GET['LIMIT'];
			$_set_limit   = ($_atual_page_ * $_max_posts_) - $_max_posts_;
			if ($_atual_page_ == 0 || $_atual_page_ == 1) {
				$_set_limit = $_max_posts_;
			} else {
				$_set_limit = $_set_limit . ',' . $_max_posts_;
			}
			$itens->set_limit($_set_limit);
		}

		########################################################################
		# PAGINAÇÃO
		########################################################################


		if($_GET['LIMIT']!="All" && empty($_GET['keywork'])){
			$totalResults = new MySQL();
			$totalResults->set_table(PREFIX_TABLES . "_model_item ");
			$totalResults->set_where(' ws_id_draft="0" ');
			$totalResults->set_where(' AND ws_id_ferramenta="' . $_GET['ws_id_ferramenta'] . '" ');
			$totalResults->set_colum('id');
			$totalResults->select();
			$_total_posts_ 		= $totalResults->_num_rows;
			$_total_paginas_	= ceil($_total_posts_ / $_max_posts_); 
			for($i=1; $i<=$_total_paginas_;++$i){
				$_SET_TEMPLATE_INPUT->COUNT = $i;
				$_SET_TEMPLATE_INPUT->ACTIVE = ($i==$_GET['PAGE'])? "active":"";
				$_SET_TEMPLATE_INPUT->block("COUNTPAGE");
			}
		}

	########################################################################
	# LIKE SEARCH 
	########################################################################

	if(isset($_GET['keywork'])){
		$campo = new MySQL();
		$campo->set_table(PREFIX_TABLES . "_model_campos");
		$campo->set_where('ws_id_ferramenta="'.$_GET['ws_id_ferramenta']. '"');
		$campo->select();
		foreach ($campo->fetch_array as $item) {
			if($item['coluna_mysql']!=""){
				$itens->like($item['coluna_mysql'],'%'.strtolower(trim($_GET['keywork'])).'%');
			}
		}
	}

	########################################################################
	# INICIAMOS O FOREACH PARA EXIBIR O RESULTADO 
	########################################################################
	$itens->set_order("posicao","ASC");
	$itens->select();
	foreach ($itens->fetch_array as $item) {
			$_SET_TEMPLATE_INPUT->ID_ITEM = $item['id'];
			foreach ($rows as $value) {
					$campo = new MySQL();
					$campo->set_table(PREFIX_TABLES . "_model_campos");
					$campo->set_where('coluna_mysql="' . $value . '"');
					$campo->select();
					$type     = @$campo->fetch_array[0]['type'];
					$multiple = @$campo->fetch_array[0]['multiple'];
					if ($type == 'multiple_select') {
							$values                          = explode('[-]', $item[$value]);
							$_SET_TEMPLATE_INPUT->DATA_NAME  = $value;
							// $_SET_TEMPLATE_INPUT->STYLE      = "";
							$_SET_TEMPLATE_INPUT->LABEL_ITEM = implode($values, ' , ');
							$_SET_TEMPLATE_INPUT->block("TD_ITEM");
					} elseif ($type == 'linkTool' && @$campo->fetch_array[0]['filtro'] == "item") {
							$files = new MySQL();
							$files->set_table(PREFIX_TABLES . "_model_item");
							$files->set_where('id="' . $item[$value] . '"');
							$files->select();
							$newColum = Array();
							foreach ($files->fetch_array as $value) {
									$newColum[] = $value[@$campo->fetch_array[0]['referencia']];
							}
							$_SET_TEMPLATE_INPUT->DATA_NAME  = $campo->fetch_array[0]['coluna_mysql'];
							// $_SET_TEMPLATE_INPUT->STYLE      = "";
							$_SET_TEMPLATE_INPUT->LABEL_ITEM = implode($newColum, ',');
							$_SET_TEMPLATE_INPUT->block("TD_ITEM");
					} elseif ($type == 'textarea') {
							$strn = strip_tags($item[$value]);
							if (strlen($strn) > 70) {
									$strn = substr($strn, 0, 70) . '...';
							}
							$_SET_TEMPLATE_INPUT->DATA_NAME  = $value;
							// $_SET_TEMPLATE_INPUT->STYLE      = "";
							$_SET_TEMPLATE_INPUT->LABEL_ITEM = $strn;
							$_SET_TEMPLATE_INPUT->block("TD_ITEM");
					} elseif ($type == 'thumbmail') {
							$_SET_TEMPLATE_INPUT->DATA_NAME  = $value;
							// $_SET_TEMPLATE_INPUT->STYLE      = "text-align:center;";
							$_SET_TEMPLATE_INPUT->LABEL_ITEM = "<img src='/ws-img/100/40/100/" . $item[$value] . "'/>";
							$_SET_TEMPLATE_INPUT->block("TD_ITEM");
					} elseif ($type == 'colorpicker') {
							$_SET_TEMPLATE_INPUT->DATA_NAME  = $value;
							// $_SET_TEMPLATE_INPUT->STYLE      = "text-align: center;color: #FFFFFF;text-shadow: 2px 0px 1px #000,-2px 0px 1px #000,0px 2px 1px #000,0px -2px 1px #000;background-color:" . $item[$value] . ";color:#FFF";
							$_SET_TEMPLATE_INPUT->LABEL_ITEM = $item[$value];
							$_SET_TEMPLATE_INPUT->block("TD_ITEM");
					} elseif ($type == 'file') {

							$files = new MySQL();
							$files->set_table(PREFIX_TABLES . "ws_biblioteca");
							$files->set_where('file="' . $item[$value] . '"');
							$files->select();
							
							$file                            = ''.@$files->fetch_array[0]['filename'];
							$_SET_TEMPLATE_INPUT->DATA_NAME  = $value;
							// $_SET_TEMPLATE_INPUT->STYLE      = "";
							$_SET_TEMPLATE_INPUT->LABEL_ITEM = $file;
							$_SET_TEMPLATE_INPUT->block("TD_ITEM");
					} else {
							$strn = strip_tags($item[$value]);
							if (strlen($strn) > 70) {
									$strn = substr($strn, 0, 70) . '...';
							}
							$_SET_TEMPLATE_INPUT->DATA_NAME  = $value;
							// $_SET_TEMPLATE_INPUT->STYLE      = "";
							$_SET_TEMPLATE_INPUT->LABEL_ITEM = $strn;
							$_SET_TEMPLATE_INPUT->block("TD_ITEM");
					}
			}
			$_SET_TEMPLATE_INPUT->block("TR_ITEM");
	}
	$_SET_TEMPLATE_INPUT->block("ITEM_LIST");
	$_SET_TEMPLATE_INPUT->show();
?>
