<?
	ob_start();
	
	include_once(__DIR__.'/../../Lib/class-ws-v1.php');
	ob_end_clean();
	function SalvaPaths() {
		$inputs = array();
		parse_str($_REQUEST['Formulario'], $inputs);
		$setupdata = new MySQL();
		$setupdata->set_table(PREFIX_TABLES . 'setupdata');
		$setupdata->set_where('id="1"');
		$setupdata->set_update('url_initPath', $inputs['url_initPath']);
		$setupdata->set_update('url_setRoot', $inputs['url_setRoot']);
		$setupdata->set_update('url_set404', $inputs['url_set404']);
		$setupdata->set_update('url_plugin', $inputs['url_plugin']);
		if (isset($inputs['url_ignore_add']) && $inputs['url_ignore_add'] == "0") {
			$setupdata->set_update('url_ignore_add', '1');
		} else {
			$setupdata->set_update('url_ignore_add', '0');
		}
		if ($setupdata->salvar()) {
			echo "sucesso";
			exit;
		}
	}
	function createHTACCESS() {
		$includes = new MySQL();
		$includes->set_table(PREFIX_TABLES . "ws_pages");
		$includes->set_where("(type='system' OR type='custom') AND path<>'' AND file<>''");
		$includes->select();
		$htaccess = '';
		foreach ($includes->fetch_array as $item) {
			$htaccess .= 'RewriteRule ' . $item['path'] . '	' . $item['file'] . ' [L]' . PHP_EOL;
		}
		file_put_contents(ROOT_ADMIN . './../.htaccess', str_replace(array(
			'{{INCLUDES}}',
			'{{COUNT}}'
		), array(
			$htaccess,
			($includes->_num_rows + 1)
		), file_get_contents(ROOT_ADMIN . '/App/Templates/txt/ws-model-htaccess.txt')));
	}
	
	function addRules($urlPath = "", $nameFile = "") {
		$NewPage = new MySQL();
		$NewPage->set_table(PREFIX_TABLES . 'ws_pages');
		if ($urlPath != "" && $nameFile != "") {
			$NewPage->set_Insert('path', $urlPath);
			$NewPage->set_Insert('file', $nameFile);
		}
		$NewPage->set_Insert('type', 'custom');
		$NewPage->insert();
	}

	function salvaRules() {
		$idPath   = $_REQUEST['idPath'];
		$urlPath  = $_REQUEST['urlPath'];
		$nameFile = $_REQUEST['nameFile'];
		$type     = $_REQUEST['type'];
		$NewPage  = new MySQL();
		$NewPage->set_table(PREFIX_TABLES . 'ws_pages');
		$NewPage->set_where('id="' . $idPath . '"');
		$NewPage->set_update('path', $urlPath);
		if ($type == "custom") {
			$NewPage->set_update('file', $nameFile);
		}
		$NewPage->salvar();
		createHTACCESS();
		exit;
	}
	function addFile() {
		$token = _token(PREFIX_TABLES . 'ws_pages', 'token');
		$I     = new MySQL();
		$I->set_table(PREFIX_TABLES . 'ws_pages');
		$I->set_insert('token', $token);
		$I->set_insert('type', 'include');
		if ($I->insert()) {
			echo "sucesso";
		}
	}
	function excluiRegistro() {
		$I = new MySQL();
		$I->set_table(PREFIX_TABLES . 'ws_pages');
		$I->set_where('id="' . $_REQUEST['idPath'] . '"');
		$I->exclui();
		createHTACCESS();
	}
	
	
	
	
	
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	//####################################################################################################################
	_session();
	_exec($_REQUEST['function']);
?>

