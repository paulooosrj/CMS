<?
	
	###############################################################################################################################
	#  HABILITANDO E DESABILITANDO PLUGINS
	#  Todos os plugins ativos tem um arquivo em seu diretorio chamado "active"  
	#  sem extenção nem nada, apenas o valor true dentro dele
	#  quando excluído, o plugin não é exibido 
	###############################################################################################################################
	function disEnabledPlugin() {
		$key        = ROOT_WEBSITE . '/' . $_REQUEST['pathPlugin'] . '/active';
		$jsonConfig = ROOT_WEBSITE . '/' . $_REQUEST['pathPlugin'] . '/plugin.config.json';
		$phpConfig  = ROOT_WEBSITE . '/' . $_REQUEST['pathPlugin'] . '/plugin.config.php';
		if (file_exists($phpConfig)) {
			ob_start();
			@include($phpConfig);
			$jsonRanderizado = ob_get_clean();
			$contents        = $plugin;
		} elseif (file_exists($jsonConfig)) {
			$contents = json_decode(file_get_contents($jsonConfig));
		}
		if (file_exists($key)) {
			unlink($key);
			echo "off";
			exit;
		} else {
			file_put_contents($key, "true");
			echo "on";
			exit;
		}
	}
	
	###############################################################################################################################
	#  EXCLUÍNDO PLUGINS
	#  Ao excluir o plugin, todos os arquivos dentro dele também serão excluídos 
	###############################################################################################################################
	function excluiPlugin() {
		function ExcluiDir($Dir) {
			$dd = opendir($Dir);
			while (false !== ($Arq = readdir($dd))) {
				if ($Arq != "." && $Arq != "..") {
					$Path = "$Dir/$Arq";
					if (is_dir($Path)) {
						ExcluiDir($Path);
					} elseif (is_file($Path)) {
						if (!unlink($Path)) {
							_erro("ops, houve um erro!" . __LINE__);
						}
					}
				}
			}
			closedir($dd);
			chmod($Dir, 0777);
			if (!rmdir($Dir)) {
				_erro("ops, houve um erro!" . __LINE__);
			}
		}
		ExcluiDir(ROOT_WEBSITE . '/' . $_REQUEST['path']);
	}
	
	###############################################################################################################################
	#  ADICIONANDO PLUGINS
	#  Ao adicionar um plugin, ele copia de dentro do painel um plugin básico de início para pasta padrão   
	###############################################################################################################################
	function AddPlugin() {
		$getPath = new MySQL();
		$getPath->set_table(PREFIX_TABLES . 'setupdata');
		$getPath->set_limit(1);
		$getPath->select();
		$getPath = $getPath->fetch_array[0]['url_plugin'];
		CopiaDir(ROOT_ADMIN . '/App/Modulos/plugins/padrao', ROOT_WEBSITE . '/' . $getPath . '/padrao_' . date('d-m-Y_H-i'));
		exit;
	}
	
	###############################################################################################################################
	# VINCULANDO UM ÍTEM A OUTROS ÍTENS OU CATEGORIAS
	# Função padrão para o campo interno do ítem 
	###############################################################################################################################
	function vinculaItemOuCategorias() {
		$variaveis = array();
		parse_str($_POST['categorias'], $variaveis);
		$CAMPO_DATA = new MySQL();
		$CAMPO_DATA->set_table(PREFIX_TABLES . '_model_campos');
		$CAMPO_DATA->set_where('id_campo="' . $_POST['idCampo'] . '"');
		$CAMPO_DATA->select();
		$CAMPO_DATA = $CAMPO_DATA->fetch_array[0];
		$categorias = new MySQL();
		$categorias->set_table(PREFIX_TABLES . 'ws_link_itens');
		$categorias->set_where(' id_item="' . $_POST['id_item'] . '"');
		$categorias->set_where(' AND ws_draft="1" ');
		$categorias->set_where(' AND ws_id_draft="' . $_POST['id_item'] . '"');
		$categorias->exclui();
		$output = array();
		if ($CAMPO_DATA['filtro'] == 'item') {
			foreach ($variaveis as $key => $value) {
				$new_key           = str_replace('_cat_', '', $key);
				$output[]          = $new_key;
				$insert_categorias = new MySQL();
				$insert_categorias->set_table(PREFIX_TABLES . 'ws_link_itens');
				$insert_categorias->set_insert('id_item', $_POST['id_item']);
				$insert_categorias->set_insert('id_item_link', $new_key);
				$insert_categorias->set_insert('ws_draft', '1');
				$insert_categorias->set_insert('ws_id_draft', $_POST['id_item']);
				$insert_categorias->insert();
			}
		}
		if ($CAMPO_DATA['filtro'] == 'cat') {
			foreach ($variaveis as $key => $value) {
				$new_key           = str_replace('_cat_', '', $key);
				$output[]          = $new_key;
				$insert_categorias = new MySQL();
				$insert_categorias->set_table(PREFIX_TABLES . 'ws_link_itens');
				$insert_categorias->set_insert('id_item', $_POST['id_item']);
				$insert_categorias->set_insert('id_cat_link', $new_key);
				$insert_categorias->set_insert('ws_draft', '1');
				$insert_categorias->set_insert('ws_id_draft', $_POST['id_item']);
				$insert_categorias->insert();
			}
		}
		$insert_categorias = new MySQL();
		$insert_categorias->set_table(PREFIX_TABLES . '_model_item');
		$insert_categorias->set_where('id="' . $_POST['id_item'] . '"');
		$insert_categorias->set_where(' AND ws_draft="1"');
		$insert_categorias->set_where(' AND ws_id_draft="' . $_POST['id_item'] . '"');
		$insert_categorias->set_update($CAMPO_DATA['coluna_mysql'], implode($output, ','));
		$insert_categorias->salvar();
		echo "sucesso";
		exit;
	}
	
	###############################################################################################################################
	# RETORNANDO OS ÍTENS OU CATEGORIAS PARA SEREM VINCULADOS AO ÍTEM EM QUESTÃO
	# Função padrão para o campo interno do ítem 
	###############################################################################################################################
	function returnItensVinculados() {
		if (criaRascunho($_REQUEST['ws_id_ferramenta'], $_POST['id_item'])) {
			echo '
					<script>
						TopAlert({
							mensagem:"<i class=\'fa fa-info-circle\'></i> Para que você possa editar este conteúdo de forma segura, foi gerado um rascunho do ítem.<br>Para aplicar as alterações, será necessário salvar e aplicar o rascunho nos ítens.",
							clickclose:true,
							height:40,
							timer:10000,
							type:1,
						})
					</script>
		';
		}
		$CAMPO_DATA = new MySQL();
		$CAMPO_DATA->set_table(PREFIX_TABLES . '_model_campos');
		$CAMPO_DATA->set_where('id_campo="' . $_POST['idCampo'] . '"');
		$CAMPO_DATA->select();
		$CAMPO_DATA = $CAMPO_DATA->fetch_array[0];
		if ($CAMPO_DATA['filtro'] == 'item') {
			$LINKPRODCAT = new MySQL();
			$LINKPRODCAT->set_table(PREFIX_TABLES . '_model_item as tabela_modelo');
			$LINKPRODCAT->set_where('tabela_modelo.ws_id_ferramenta="' . $CAMPO_DATA['values_opt'] . '"');
			$LINKPRODCAT->set_where('AND tabela_modelo.ws_id_draft=0');
			if ($CAMPO_DATA['cat_referencia'] != '') {
				$LINKPRODCAT->join(" INNER ", PREFIX_TABLES . '_model_link_prod_cat as linkCat ', ' tabela_modelo.id=linkCat.id_item AND linkCat.id_cat="' . $CAMPO_DATA['cat_referencia'] . '"');
			}
			$LINKPRODCAT->select();
			foreach ($LINKPRODCAT->fetch_array as $item) {
				$verify = new MySQL();
				$verify->set_table(PREFIX_TABLES . 'ws_link_itens');
				$verify->set_where('id_item="' . $_POST['id_item'] . '"');
				$verify->set_where(' AND id_item_link="' . $item['id'] . '"');
				$verify->set_where(' AND ws_draft=1');
				$verify->set_where(' AND ws_id_draft="' . $_POST['id_item'] . '"');
				$verify->select();
				if ($verify->_num_rows >= 1) {
					$select = "checked";
				} else {
					$select = "";
				}
				echo '<label class="categoria">' . $item[$CAMPO_DATA['referencia']];
				echo '<input name="' . $item['id'] . '" type="checkbox" ' . $select . '/></label>' . PHP_EOL;
			}
		}
		if ($CAMPO_DATA['filtro'] == 'cat') {
			function foreachCat($categoria, $listCat,$ws_id_ferramenta) {
				$cat_foreach = new MySQL();
				$cat_foreach->set_table(PREFIX_TABLES . '_model_cat');
				$cat_foreach->set_order('titulo', 'ASC');
				$cat_foreach->set_where('id_cat="' . $categoria . '"');
				$cat_foreach->set_where('AND ws_id_ferramenta="' . $ws_id_ferramenta . '"');
				$cat_foreach->select();
				foreach ($cat_foreach->fetch_array as $cat) {
					$verify = new MySQL();
					$verify->set_table(PREFIX_TABLES . 'ws_link_itens');
					$verify->set_where('id_item="' . $_POST['id_item'] . '"');
					$verify->set_where('AND id_cat_link="' . $cat['id'] . '"');
					$verify->set_where(' AND ws_draft=1');
					$verify->set_where(' AND ws_id_draft="' . $_POST['id_item'] . '"');
					$verify->select();
					if ($verify->_num_rows >= 1) {
						$select = "checked";
					} else {
						$select = "";
					}
					$listCat[] = $cat['titulo'];
					echo '<label class="categoria">' . implode($listCat, ' > ') . '<input name="_cat_' . $cat['id'] . '" type="checkbox" ' . $select . '/></label>' . PHP_EOL;
					foreachCat($cat['id'], $listCat);
					$listCat = array();
				}
			}
			$categorias = new MySQL();
			$categorias->set_table(PREFIX_TABLES . '_model_cat');
			$categorias->set_order('titulo', 'ASC');
			$categorias->set_where('ws_id_ferramenta="' . $_POST['ws_id_ferramenta'] . '"');
			if ($CAMPO_DATA['cat_referencia'] != "") {
				$categorias->set_where('AND id_cat="' . $CAMPO_DATA['cat_referencia'] . '"');
			} else {
				$categorias->set_where('AND id_cat="0"');
			}
			$categorias->select();
			foreach ($categorias->fetch_array as $cat) {
				$select = "";
				$verify = new MySQL();
				$verify->set_table(PREFIX_TABLES . 'ws_link_itens');
				$verify->set_where('id_item="' . $_POST['id_item'] . '"');
				$verify->set_where('AND id_cat_link="' . $cat['id'] . '"');
				$verify->set_where(' AND ws_draft=1');
				$verify->set_where(' AND ws_id_draft="' . $_POST['id_item'] . '"');
				$verify->select();
				if ($verify->_num_rows >= 1) {
					$select = "checked";
				} else {
					$select = "";
				}
				echo '<label class="categoria">' . $cat['titulo'] . ' <input name="_cat_' . $cat['id'] . '" type="checkbox" ' . $select . '/></label>' . PHP_EOL;
				$listCat[] = $cat['titulo'];
				foreachCat($cat['id'], $listCat);
				$listCat = array();
			}
		}
	}
	
	###############################################################################################################################
	# FUNÇÃO DESATIVADA TEMPORARIAMENTE PARA FUTURAS MELHORIAS
	# Função padrão para o campo interno do ítem 
	###############################################################################################################################
	function SaveLiveEditor() {
		global $_conectMySQLi_;
		$sucesso          = false;
		$_POST['content'] = mysqli_real_escape_string($_conectMySQLi_, urldecode($_POST['content']));
		$linkVideo        = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . '_model_item');
		$linkVideo->set_where('token="' . $_POST['token'] . '"');
		$linkVideo->set_update($_POST['colum'], $_POST['content']);
		$linkVideo->debug(0);
		if ($linkVideo->salvar()) {
			$sucesso = true;
		}
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . '_model_cat');
		$linkVideo->set_where('token="' . $_POST['token'] . '"');
		$linkVideo->debug(0);
		$linkVideo->set_update($_POST['colum'], $_POST['content']);
		if ($linkVideo->salvar()) {
			$sucesso = true;
		}
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . '_model_files');
		$linkVideo->set_where('token="' . $_POST['token'] . '"');
		$linkVideo->set_update($_POST['colum'], $_POST['content']);
		$linkVideo->debug(0);
		if ($linkVideo->salvar()) {
			$sucesso = true;
		}
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . '_model_gal');
		$linkVideo->set_where('token="' . $_POST['token'] . '"');
		$linkVideo->set_update($_POST['colum'], $_POST['content']);
		$linkVideo->debug(0);
		if ($linkVideo->salvar()) {
			$sucesso = true;
		}
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . '_model_img');
		$linkVideo->set_where('token="' . $_POST['token'] . '"');
		$linkVideo->set_update($_POST['colum'], $_POST['content']);
		$linkVideo->debug(0);
		if ($linkVideo->salvar()) {
			$sucesso = true;
		}
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . '_model_img_gal');
		$linkVideo->set_where('token="' . $_POST['token'] . '"');
		$linkVideo->set_update($_POST['colum'], $_POST['content']);
		$linkVideo->debug(0);
		if ($linkVideo->salvar()) {
			$sucesso = true;
		}
		echo $sucesso;
		exit;
	}
	
	###############################################################################################################################
	# SALVA A URL FORMATADA DO VÍDEO DO VÍMEO OU YOUTUBE 
	# Função padrão para o campo interno do ítem 
	###############################################################################################################################
	function saveURLvideo() {
		criaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item']);
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . '_model_item');
		//	$linkVideo->set_where('id="'.$_POST['id_item'].'"');
		$linkVideo->set_where('ws_draft="1"');
		$linkVideo->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
		$linkVideo->set_update($_POST['coluna'], $_POST['urlVideo']);
		if ($linkVideo->salvar()) {
			$urlVideo   = $_POST['urlVideo'];
			$urlExplode = parse_url($urlVideo);
			$query      = array();
			parse_str(@$urlExplode['query'], $query);
			$urlExplode['query'] = $query;
			if ($urlExplode['host'] == "youtube.com" || $urlExplode['host'] == "www.youtube.com") {
				$urlThumb = "http://img.youtube.com/vi/" . $urlExplode['query']['v'] . "/hqdefault.jpg";
			} elseif ($urlExplode['host'] == "vimeo.com" || $urlExplode['host'] == "www.vimeo.com") {
				$url      = 'http://vimeo.com/api/v2/video' . $urlExplode['path'] . '.php';
				$contents = @file_get_contents($url);
				$array    = @unserialize(trim($contents));
				$urlThumb = $array[0]['thumbnail_large'];
			}
			echo $urlThumb;
			exit;
		} else {
			echo "falha!";
			exit;
		}
	}
	
	###############################################################################################################################
	# SALVA A URL FORMATADA DO MP3 E RETORNA O TEMPLATE DO PLAYER 
	# Função padrão para o campo interno do ítem 
	###############################################################################################################################
	function saveURLmp3() {
		#####################################################################################
		# CRIA O RASCUNHO CASO NAO TENHA
		#####################################################################################
		criaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item']);
		#####################################################################################
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . '_model_item');
		$linkVideo->set_where('ws_draft="1"');
		$linkVideo->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
		$linkVideo->set_update($_POST['coluna'], $_POST['urlMP3']);
		if ($linkVideo->salvar()) {
			$urlParsed = parse_url($_POST['urlMP3']);
			$options   = array(
				CURLOPT_SSL_VERIFYHOST 	=> false,
				CURLOPT_SSL_VERIFYPEER 	=> false,
				CURLOPT_RETURNTRANSFER 	=> true, // return web page
				CURLOPT_HEADER 			=> false, // don't return headers
				CURLOPT_FOLLOWLOCATION 	=> true, // follow redirects
				CURLOPT_ENCODING 		=> "", // handle all encodings
				CURLOPT_USERAGENT 		=> "WebSheep", // who am i
				CURLOPT_AUTOREFERER 	=> true, // set referer on redirect
				CURLOPT_CONNECTTIMEOUT 	=> 120, // timeout on connect
				CURLOPT_TIMEOUT 		=> 120, // timeout on response
				CURLOPT_MAXREDIRS 		=> 10 // stop after 10 redirects
			);
			$ch        = curl_init($_POST['urlMP3']);
			curl_setopt_array($ch, $options);
			$content = curl_exec($ch);
			$err     = curl_errno($ch);
			$errmsg  = curl_error($ch);
			$header  = curl_getinfo($ch);
			curl_close($ch);
			$header['errno']   = $err;
			$header['errmsg']  = $errmsg;
			$header['content'] = $content;
			$html              = new DOMDocument();
			@$html->loadHTML($content);
			foreach ($html->getElementsByTagName('meta') as $meta) {
				if ($urlParsed['host'] == 'mixcloud.com' || $urlParsed['host'] == 'www.mixcloud.com') {
					if ($meta->getAttribute('name') == 'twitter:player') {
						$template = str_replace('', '', $meta->getAttribute('content'));
						break;
					}
				} elseif ($urlParsed['host'] == 'soundcloud.com' || $urlParsed['host'] == 'www.soundcloud.com') {
					if ($meta->getAttribute('property') == 'twitter:player') {
						$template = str_replace('visual=true', 'visual=false', $meta->getAttribute('content'));
						break;
					}
				}
			}
			echo $template;
			exit;
		} else {
			echo "falha!";
			exit;
		}
	}
	
	###############################################################################################################################
	# RETORNA O MODAL COM O FORMULARIO PARA DEFINIÇÃO DA URL DO SOUNDCLOUD OU  MIXCLOUD
	# Função padrão para o campo interno do ítem 
	###############################################################################################################################
	function getURLmp3() {
		$token = $_POST['token'];
		$campo = new MySQL();
		$campo->set_table(PREFIX_TABLES . '_model_campos');
		$campo->set_where('token="' . $token . '"');
		$campo->select();
		$coluna    = $campo->fetch_array[0]['coluna_mysql'];
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . '_model_item');
		$linkVideo->set_where('ws_draft="1"');
		$linkVideo->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
		//		$linkVideo->set_where('id="'.$_POST['id_item'].'"');
		$linkVideo->select();
		echo "<div>
			<div>Digite a URL da sua música (SoundCloud ou MixCloud):</div>
			<form id='formLinkVideo'>
				<input name='ws_id_ferramenta' value='" . $_POST['ws_id_ferramenta'] . "' type='hidden'/>
				<input name='coluna' value='" . $coluna . "' type='hidden'/>
				<input name='id_item' value='" . $_POST['id_item'] . "' type='hidden'/>
				<input id='urlPath' class='inputText' name='urlMP3' value='" . @$linkVideo->fetch_array[0][$coluna] . "' style='padding:10px 20px;width:calc(100% - 20px);margin-top: 13px;'/>
			<form>
		</div>";
	}
	
	###############################################################################################################################
	# RETORNA O MODAL COM O FORMULARIO PARA DEFINIÇÃO DA URL DO YOUTUBE OU VIMEO
	# Função padrão para o campo interno do ítem 
	###############################################################################################################################	
	function getURLvideo() {
		if (criaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item'])) {
			echo '
				<script>
					TopAlert({
						mensagem:"<i class=\'fa fa-info-circle\'></i> Para que você possa editar este conteúdo de forma segura, foi gerado um rascunho do ítem.<br>Para aplicar as alterações, será necessário salvar e aplicar o rascunho nos ítens.",
						clickclose:true,
						height:40,
						timer:10000,
						type:1,
					})
				</script>
				';
		}
		$token = $_POST['token'];
		$campo = new MySQL();
		$campo->set_table(PREFIX_TABLES . '_model_campos');
		$campo->set_where('token="' . $token . '"');
		$campo->select();
		$coluna    = $campo->fetch_array[0]['coluna_mysql'];
		$linkVideo = new MySQL();
		$linkVideo->set_table(PREFIX_TABLES . '_model_item');
		//$linkVideo->set_where('id="'.$_POST['id_item'].'"');
		$linkVideo->set_where('ws_draft="1"');
		$linkVideo->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
		$linkVideo->select();
		echo "<div>
			<div>Digite a URL do seu video (Youtube ou Vimeo):</div>
			<form id='formLinkVideo'>
				<input name='coluna' value='" . $coluna . "' type='hidden'/>
				<input name='id_item' value='" . $_POST['id_item'] . "' type='hidden'/>
				<input name='ws_id_ferramenta' value='" . $_POST['ws_id_ferramenta'] . "' type='hidden'/>
				<input class='inputText' name='urlVideo' value='" . $linkVideo->fetch_array[0][$coluna] . "' style='padding:10px 20px;width:calc(100% - 20px);margin-top: 13px;'/>
			<form>
			<div>";
	}
	
	###############################################################################################################################
	# SALVANDO O TÍTULO PADRÃO DA PÁGINA   
	###############################################################################################################################
	function salvaTitlePage() {
		global $_conectMySQLi_;
		$titulo  = mysqli_real_escape_string($_conectMySQLi_, $_POST['titulo']);
		$id_page = $_POST['id_page'];
		if ($id_page == '0') {
			$Salva_Titulo = new MySQL();
			$Salva_Titulo->set_table(PREFIX_TABLES . 'setupdata');
			$Salva_Titulo->set_where('id="1"');
			$Salva_Titulo->set_update('title_root', $titulo);
			if ($Salva_Titulo->Salvar()) {
				echo "sucesso";
			}
		} else {
			$Salva_Titulo = new MySQL();
			$Salva_Titulo->set_table(PREFIX_TABLES . 'ws_pages');
			$Salva_Titulo->set_where('id="' . $id_page . '"');
			$Salva_Titulo->set_update('title_page', $titulo);
			if ($Salva_Titulo->Salvar()) {
				echo "sucesso";
			}
		}
	}
	
	###############################################################################################################################
	# EXCLUINDO UMA META TAG   
	###############################################################################################################################
	function exclMetaTag() {
		$FERRA = new MySQL();
		$FERRA->set_table(PREFIX_TABLES . 'meta_tags');
		$FERRA->set_where('id="' . $_POST['idMeta'] . '"');
		if ($FERRA->exclui()) {
			echo "sucesso";
			exit;
		}
	}
	
	###############################################################################################################################
	# EXCLUINDO VÁRIAS META TAG'S   
	###############################################################################################################################
	function exclMultiMetaTag() {
		$FERRA = new MySQL();
		$FERRA->set_table(PREFIX_TABLES . 'meta_tags');
		$FERRA->set_where('id<>"" AND (id="' . implode($_POST['metas'], '" OR id="') . '")');
		if ($FERRA->exclui()) {
			echo "sucesso";
			exit;
		}
	}
	
	###############################################################################################################################
	# SALVA VÁRIAS META TAG'S   
	###############################################################################################################################
	function salvaMetaTag() {
		$FERRA = new MySQL();
		$FERRA->set_table(PREFIX_TABLES . 'meta_tags');
		$FERRA->set_where('id="' . $_POST['idMeta'] . '"');
		$FERRA->set_update('type', $_POST['type']);
		$FERRA->set_update('type_content', $_POST['type_content']);
		$FERRA->set_update('content', $_POST['content']);
		if ($FERRA->Salvar()) {
			echo "sucesso";
			exit;
		}
	}
	
	###############################################################################################################################
	# ADICIONANDO META TAG'S A UMA PÁGINA   
	###############################################################################################################################
	function addMetaTag() {
		$twitter   = array();
		$twitter[] = "twitter:card";
		$twitter[] = "twitter:site";
		$twitter[] = "twitter:creator";
		$twitter[] = "twitter:title";
		$twitter[] = "twitter:description";
		$twitter[] = "twitter:image";
		$basic     = array();
		$basic[]   = "pragma";
		$basic[]   = "author";
		$basic[]   = "robots";
		$basic[]   = "language";
		$basic[]   = "description";
		$basic[]   = "keywords";
		$og        = array();
		$og[]      = "og:title";
		$og[]      = "og:type";
		$og[]      = "og:url";
		$og[]      = "og:description";
		$og[]      = "og:image";
		$og[]      = "og:site_name";
		$og[]      = "fb:app_id";
		$og[]      = "og:video";
		$og[]      = "og:locale";
		$og[]      = "og:audio";
		$blank     = array();
		$blank[]   = "";
		if ($_POST['TypeMedia'] == "blank") {
			$insert_categorias = new MySQL();
			$insert_categorias->set_table(PREFIX_TABLES . 'meta_tags');
			$insert_categorias->set_insert('id_page', $_POST['id_page']);
			if (!$insert_categorias->insert()) {
				echo "falha";
				exit;
			}
			echo "sucesso";
			exit;
		} elseif ($_POST['TypeMedia'] == "basic") {
			foreach ($basic as $basic) {
				$insert_categorias = new MySQL();
				$insert_categorias->set_table(PREFIX_TABLES . 'meta_tags');
				$insert_categorias->set_insert('id_page', $_POST['id_page']);
				$insert_categorias->set_insert('tag', 'meta');
				$insert_categorias->set_insert('type', 'name');
				$insert_categorias->set_insert('type_content', $basic);
				if (!$insert_categorias->insert()) {
					echo "falha";
					exit;
				}
			}
			echo "sucesso";
			exit;
		} elseif ($_POST['TypeMedia'] == "og") {
			foreach ($og as $og) {
				$insert_categorias = new MySQL();
				$insert_categorias->set_table(PREFIX_TABLES . 'meta_tags');
				$insert_categorias->set_insert('id_page', $_POST['id_page']);
				$insert_categorias->set_insert('tag', 'meta');
				$insert_categorias->set_insert('type', 'property');
				$insert_categorias->set_insert('type_content', $og);
				if (!$insert_categorias->insert()) {
					echo "falha";
					exit;
				}
			}
			echo "sucesso";
			exit;
		} elseif ($_POST['TypeMedia'] == "twitter") {
			foreach ($twitter as $twitter) {
				$insert_categorias = new MySQL();
				$insert_categorias->set_table(PREFIX_TABLES . 'meta_tags');
				$insert_categorias->set_insert('id_page', $_POST['id_page']);
				$insert_categorias->set_insert('tag', 'meta');
				$insert_categorias->set_insert('type', 'name');
				$insert_categorias->set_insert('type_content', $twitter);
				if (!$insert_categorias->insert()) {
					echo "falha";
					exit;
				}
			}
			echo "sucesso";
			exit;
		}
	}
	
	###############################################################################################################################
	# ADICIONANDO META TAG'S A UMA PÁGINA   
	###############################################################################################################################
	function returnCategorias() {
		if (criaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item'])) {
			echo '
			<script>
				TopAlert({
					mensagem:"<i class=\'fa fa-info-circle\'></i> Para que você possa editar este conteúdo de forma segura, foi gerado um rascunho do ítem.<br>Para aplicar as alterações, será necessário salvar e aplicar o rascunho nos ítens.",
					clickclose:true,
					height:40,
					timer:10000,
					type:1,
				})
			</script>
			';
		}
		function foreachCat($categoria, $listCat) {
			$cat_foreach = new MySQL();
			$cat_foreach->set_table(PREFIX_TABLES . '_model_cat');
			$cat_foreach->set_order('titulo', 'ASC');
			$cat_foreach->set_where('id_cat="' . $categoria . '"');
			$cat_foreach->set_where('AND ws_id_ferramenta="' . $_POST['ws_id_ferramenta'] . '"');
			$cat_foreach->select();
			foreach ($cat_foreach->fetch_array as $cat) {
				$verify = new MySQL();
				$verify->set_table(PREFIX_TABLES . '_model_link_prod_cat');
				$verify->set_where('id_cat="' . $cat['id'] . '"');
				$verify->set_where('AND id_item="' . $_POST['id_item'] . '"');
				$verify->set_where('AND ws_id_ferramenta="' . $_POST['ws_id_ferramenta'] . '"');
				$verify->set_where('AND ws_draft="1"');
				$verify->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
				$verify->select();
				if ($verify->_num_rows >= 1) {
					$select = "checked";
				} else {
					$select = "";
				}
				$listCat[] = $cat['titulo'];
				echo '<label class="categoria">' . implode($listCat, ' > ') . '<input name="' . $cat['ws_nivel'] . '_cat_' . $cat['id'] . '" type="checkbox" ' . $select . '/></label>' . PHP_EOL;
				foreachCat($cat['id'], $listCat);
				$listCat = array();
			}
		}
		$LINKPRODCAT = new MySQL();
		$LINKPRODCAT->set_table(PREFIX_TABLES . '_model_link_prod_cat');
		//	$LINKPRODCAT->set_where('id_item="'.$_POST['id_item'].'"');
		$LINKPRODCAT->set_where('ws_draft="1"');
		$LINKPRODCAT->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
		$LINKPRODCAT->select();
		$listCat    = array();
		$categorias = new MySQL();
		$categorias->set_table(PREFIX_TABLES . '_model_cat');
		$categorias->set_order('titulo', 'ASC');
		$categorias->set_where('id_cat="0"');
		$categorias->set_where('AND ws_id_ferramenta="' . $_POST['ws_id_ferramenta'] . '"');
		$categorias->select();
		$verify = new MySQL();
		$verify->set_table(PREFIX_TABLES . '_model_link_prod_cat');
		$verify->set_where('id_cat="0"');
		$verify->set_where('AND id_item="' . $_POST['id_item'] . '"');
		$verify->set_where('AND ws_id_ferramenta="' . $_POST['ws_id_ferramenta'] . '"');
		$verify->set_where('AND ws_draft="1"');
		$verify->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
		$verify->select();
		if ($verify->_num_rows >= 1) {
			$select = "checked";
		} else {
			$select = "";
		}
		echo '<label class="categoria">Nivel zero <input name="0_cat_0" type="checkbox" ' . $select . '/></label>' . PHP_EOL;
		foreach ($categorias->fetch_array as $cat) {
			$verify = new MySQL();
			$verify->set_table(PREFIX_TABLES . '_model_link_prod_cat');
			$verify->set_where('id_cat="' . $cat['id'] . '"');
			$verify->set_where('AND id_item="' . $_POST['id_item'] . '"');
			$verify->set_where('AND ws_id_ferramenta="' . $_POST['ws_id_ferramenta'] . '"');
			$verify->set_where('AND ws_draft="1"');
			$verify->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
			$verify->select();
			if ($verify->_num_rows >= 1) {
				$select = "checked";
			} else {
				$select = "";
			}
			echo '<label class="categoria">#' . $cat['id'] . ' - ' . urldecode($cat['titulo']) . ' <input name="' . $cat['ws_nivel'] . '_cat_' . $cat['id'] . '" type="checkbox" ' . $select . '/></label>' . PHP_EOL;
			$listCat[] = $cat['titulo'];
			foreachCat($cat['id'], $listCat,$_POST['ws_id_ferramenta']);
			$listCat = array();
		}
	}
	
	
	
	###############################################################################################################################
	# VINCULA UM ÍTEM A UMA OU MAIS  CATEGORIA   
	# Função padrão para o campo interno do ítem
	###############################################################################################################################
	function vinculaCategorias() {
		$variaveis = array();
		parse_str($_POST['categorias'], $variaveis);
		$categorias = new MySQL();
		$categorias->set_table(PREFIX_TABLES . '_model_link_prod_cat');
		$categorias->set_where('id_item="' . $_POST['id_item'] . '"');
		$categorias->set_where('AND ws_id_ferramenta="' . $_POST['ws_id_ferramenta'] . '"');
		$categorias->set_where('AND ws_draft="1"');
		$categorias->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
		$categorias->exclui();
		foreach ($variaveis as $key => $value) {
			$explode           = explode('_', $key);
			$insert_categorias = new MySQL();
			$insert_categorias->set_table(PREFIX_TABLES . '_model_link_prod_cat');
			$insert_categorias->set_insert('id_item', $_POST['id_item']);
			$insert_categorias->set_insert('ws_nivel', $explode[0]);
			$insert_categorias->set_insert('id_cat', $explode[2]);
			$insert_categorias->set_insert('ws_id_ferramenta', $_POST['ws_id_ferramenta']);
			$insert_categorias->set_insert('ws_draft', "1");
			$insert_categorias->set_insert('ws_id_draft', $_POST['id_item']);
			$insert_categorias->insert();
		}
		echo "sucesso";
		exit;
	}
	
	###############################################################################################################################
	# RETORNA O COMBO PARA UPLOAD DE IMAGENS NO EDITOR DE TEXTO   
	# Função padrão para o campo interno do ítem
	
	###############################################################################################################################
	function ReturnUploadCKEditor() {
		echo '<form id="formUploadCKeditor" action="/admin/App/Core/ws-upload-files.php" method="post" enctype="multipart/form-data" name="formUpload">
					<input name="type" type="hidden" value="ckEditor">
					<input name="token_group" 	type="hidden" value="'.$_POST['token_group'].'">
					<input name="token" 		type="hidden" value="'._token(PREFIX_TABLES . 'ws_biblioteca', 'token').'">

					<input id="inputFile" input name="arquivo" accept="image/jpg,image/png,image/jpeg,image/gif" id="myfileCKEditor" type="file"  style="display:none">
					<button type="submit" id="enviar_arquivos" style="dispaly:none;"></button>
				</form>
				<div id="btSelectFile" class="botao" style="position: relative;float: left;padding: 10px 50px;left:50%;transform:translateX(-50%);">Selecionar imagem</div>
				<div id="uploadBarContent" class="bg01" style="height: 18px;position: relative;float: left;padding: 2px 20px;width: calc(100% - 60px);left: 50%;transform: translateX(-50%);margin-top: 18px;margin-bottom: -57px;">
					<div id="uploadBar" class="bg05" style="color:#FFF;text-shadow:none;width:0%;overflow:hidden;"></div>
			</div>';
	}
	
	###############################################################################################################################
	# SALVA ESTILOS INTERNOS DO EDITOR CKEDITOR    
	# Função padrão para o campo interno do ítem
	###############################################################################################################################
	function salvaEstilo() {
		$SETUP = new MySQL();
		$SETUP->set_table(PREFIX_TABLES . 'setupdata');
		$SETUP->set_where('id="1"');
		$SETUP->set_update('stylejson', urlencode($_REQUEST['json']));
		if ($SETUP->salvar()) {
			echo "sucesso";
			exit;
		}
	}
	
	###############################################################################################################################
	# RETORNA ESTILOS INTERNOS DO EDITOR CKEDITOR  
	# Função padrão para o campo interno do ítem
	###############################################################################################################################
	function ReturnEstiloPadrao() {
		$SETUP = new MySQL();
		$SETUP->set_table(PREFIX_TABLES . 'setupdata');
		$SETUP->set_where('id="1"');
		$SETUP->select();
		$SETUP = $SETUP->fetch_array[0];
		echo '<style>' . '.ps-container > .ps-scrollbar-x-rail,.ps-container > .ps-scrollbar-y-rail {opacity: 0.8;}' . '</style>' . '<textarea id="stylesCSS" style="display:none;">' . urldecode($SETUP['stylejson']) . '</textarea>' . '<div id="ace_stylesCSS" style="text-shadow: none; text-align: left; height: 100%;left: 20px;width: calc(100% - 50px);0% - 13px);margin-top: 0px; font-size: 17px;float: left;"></div>' . '';
	}
	
	###############################################################################################################################
	# SALVA CSS INTERNO DO EDITOR CKEDITOR    
	# Função padrão para o campo interno do ítem
	###############################################################################################################################
	function salvaCss() {
		$SETUP = new MySQL();
		$SETUP->set_table(PREFIX_TABLES . 'setupdata');
		$SETUP->set_where('id="1"');
		$SETUP->set_update('stylecss', urlencode($_REQUEST['css']));
		if ($SETUP->salvar()) {
			echo "sucesso";
			exit;
		}
	}
	###############################################################################################################################
	# RETORNA CSS INTERNO DO EDITOR CKEDITOR    
	# Função padrão para o campo interno do ítem
	###############################################################################################################################
	function ReturnCSSPadrao() {
		$SETUP = new MySQL();
		$SETUP->set_table(PREFIX_TABLES . 'setupdata');
		$SETUP->set_where('id="1"');
		$SETUP->select();
		$SETUP = $SETUP->fetch_array[0];
		echo '<style>' . '.ps-container > .ps-scrollbar-x-rail,.ps-container > .ps-scrollbar-y-rail {opacity: 0.8;}' . '</style>' . '<textarea id="stylesCSS" name="stylecss" style="display:none;">' . retira_acentos(urldecode($SETUP['stylecss'])) . '</textarea>' . '<div id="ace_stylesCSS" style="text-shadow: none; text-align: left; height: 100%;left: 20px;width: calc(100% - 50px);0% - 13px);margin-top: 0px; font-size: 17px;float: left;"></div>' . '';
	}
	
	
	###############################################################################################################################
	# VERIFICA SE O ARQUIVO DO PLUGIN EXISTE OU NÃO PARA SER EXECUTADO    
	###############################################################################################################################
	
	function loadInfosPlugin() {
		if (file_exists(ROOT_WEBSITE . $_REQUEST['dataFile'])) {
			echo 'true';
		} else {
			echo 'false';
		}
		exit;
	}
	
	###############################################################################################################################
	# ATUALIZA O AVATAR DE UMA CATEGORIA    
	###############################################################################################################################
	function reloadThumbCategoria() {
		$U = new MySQL();
		$U->set_table(PREFIX_TABLES . '_model_cat');
		$U->set_where('id="' . $_REQUEST['idCat'] . '"');
		$U->set_update('avatar', $_REQUEST['avatar']);
		if ($U->salvar()) {
			echo '/ws-img/281/0/100/' . $_REQUEST['avatar'];
		}
	}
	
	###############################################################################################################################
	# ATUALIZA O AVATAR DE UMA GALERIA    
	###############################################################################################################################
	function substituiThumbGaleria() {
		if (is_array($_REQUEST['img'])) {
			$_REQUEST['img'] = $_REQUEST['img'][0];
		}
		$U = new MySQL();
		$U->set_table(PREFIX_TABLES . '_model_gal');
		$U->set_where('id="' . $_REQUEST['idItem'] . '"');
		$U->set_update($_REQUEST['coluna'], $_REQUEST['img']);
		if ($U->salvar()) {
			echo $_REQUEST['img'];
		}
	}
	
	###############################################################################################################################
	# ATUALIZA UMA IMAGEM DE UM ÍTEM
	# Função padrão para o campo interno do ítem    
	###############################################################################################################################
	function substituiThumb() {

		criaRascunho($_POST['ws_id_ferramenta'], $_POST['idItem']);
		if (is_array($_POST['img'])) {
			$_POST['img'] = $_POST['img'][0];
		}
		$U = new MySQL();
		$U->set_table(PREFIX_TABLES . '_model_item');
		//$U->set_where('id="'.$_REQUEST['idItem'].'"');
		$U->set_where('ws_draft="1"');
		$U->set_where('AND ws_id_draft="' . $_POST['idItem'] . '"');
		$U->set_update($_POST['coluna'], $_POST['img']);
		if ($U->salvar()) {
			echo $_POST['img'];
		}
	}
	
	###############################################################################################################################
	# EXCLUI UMA IMAGEM DE UM ÍTEM
	# Função padrão para o campo interno do ítem    
	###############################################################################################################################
	function excluiThumb() {
		criaRascunho($_POST['ws_id_ferramenta'], $_POST['idItem']);
		$U = new MySQL();
		$U->set_table(PREFIX_TABLES . '_model_item');
		$U->set_where('ws_draft="1"');
		$U->set_where('AND ws_id_draft="' . $_POST['idItem'] . '"');
		$U->set_update($_POST['coluna'], '');
		if ($U->salvar()) {
			echo true;
		}
	}
	###############################################################################################################################
	# ADICIONANDO IMAGENS AO ÍTEM PUXANDO DE DENTRO DA BIBLIOTECA
	# Função padrão para o campo interno do ítem    
	###############################################################################################################################
	function addImagensBibliotecaItem() {
		criaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item'],true);
		$up = new MySQL();
		$up->set_table(PREFIX_TABLES . 'ws_biblioteca');
		$up->set_where('file=""');
		foreach ($_REQUEST['img'] as $imagem) {
			$up->set_where('OR file="' . $imagem . '"');
		}
		$up->select();
		foreach ($up->fetch_array as $imagem) {
			$up = new MySQL();
			$up->set_table(PREFIX_TABLES . '_model_img');
			$up->set_insert('ws_draft', '1');
			$up->set_insert('ws_id_ferramenta', 	$_POST['ws_id_ferramenta']);
			$up->set_insert('ws_id_draft', 			$_POST['id_item']);
			$up->set_insert('id_item',			 	$_POST['id_item']);
			$up->set_insert('ws_tool_id', 			$_POST['id_item']);
			$up->set_insert('ws_tool_item', 		$_POST['id_item']);
			$up->set_insert('id_cat', 				$_POST['id_cat']);
			$up->set_insert('imagem', $imagem['file']);
			$up->set_insert('filename', $imagem['filename']);
			$up->set_insert('token', $imagem['token']);
			$up->insert();
		}
		echo true;
		exit;
	}
	
	###############################################################################################################################
	# ADICIONANDO IMAGENS A UMA GALERIA DE FOTOS PUXANDO DE DENTRO DA BIBLIOTECA
	# Função padrão para o campo interno do ítem    
	###############################################################################################################################
	function addImagensBibliotecaGaleriaInterna() {
		$up = new MySQL();
		$up->set_table(PREFIX_TABLES . 'ws_biblioteca');
		$up->set_colum('file');
		$up->set_colum('token');
		$up->set_colum('filename');
		$up->set_where('file=""');
		$up->distinct();
		foreach ($_REQUEST['img'] as $imagem) {
			$up->set_where('OR file="' . $imagem . '"');
		}
		$up->select();
		foreach ($up->fetch_array as $imagem) {
			$up = new MySQL();
			$up->set_table(PREFIX_TABLES . '_model_img_gal');
			$up->set_insert('ws_id_ferramenta', $_REQUEST['ws_id_ferramenta']);
			$up->set_insert('file', '' . $imagem['file']);
			$up->set_insert('filename', $imagem['filename']);
			$up->set_insert('token', $imagem['token']);
			$up->set_insert('id_item', $_REQUEST['id_item']);
			$up->set_insert('id_galeria', $_REQUEST['id_galeria']);
			$up->set_insert('ws_draft', '1');
			$up->set_insert('ws_id_draft', $_REQUEST['id_item']);
			if ($up->insert()) {
				$file = new MySQL();
				$file->set_table(PREFIX_TABLES . '_model_img_gal');
				$file->set_where('file="' . $imagem['file'] . '"');
				$file->select();
				echo "<li id='" . $file->fetch_array[0]['id'] . "'>	
						<div id='combo'>
							<div id='detalhes_img' class='bg02'>
							<span><img class='editar' 	legenda='Editar Informações'	src='/admin/App/Templates/img/websheep/layer--pencil.png'></span>   
							<span><img class='excluir'	legenda='Excluir Imagem'		src='/admin/App/Templates/img/websheep/cross-button.png'></span>   
							</div>
							<img class='thumb_exibicao' src='/ws-img/155/155/100/" . $file->fetch_array[0]['file'] . "'>
						</div>
					</li>";
			}
		}
	}
	
	
	###############################################################################################################################
	# RETORNA COMBO PARA EDIÇÃO DE UM SELECTBOX 
	# Função padrão para o campo interno do ítem    
	###############################################################################################################################
	function edita_selectbox() {
		$campos = new MySQL();
		$campos->set_table(PREFIX_TABLES . '_model_campos');
		$campos->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$campos->select();
		echo '<div id="campos_BG">
				<form id="form_dados">
						<input  name="cargo" id="cargoInput" value=""/>
				</form>
				<div id="salvar" class="w1">>Salvar</div>
				<div id="excluir" class="w1">>Excluir</div>
		</div>';
		exit;
	}
	
	###############################################################################################################################
	# ORGANIZA A ORDEM DOS ARQUIVOS INTERNOS     
	###############################################################################################################################
	function ordena_files() {
		$array_id = explode(',', $_REQUEST['ids']);
		$i        = 1;
		foreach ($array_id as $id) {
			$Salva = new MySQL();
			$Salva->set_table(PREFIX_TABLES . '_model_files');
			$Salva->set_where('id="' . $id . '"');
			$Salva->set_update('posicao', $i);
			if ($Salva->salvar()) {
				++$i;
			}
		}
	}
	
	###############################################################################################################################
	# ORGANIZA A ORDEM DAS IMAGENS INTERNAS     
	###############################################################################################################################	
	function ordena_fotos_imgs() {

		$array_id = explode(',', $_REQUEST['ids']);
		$i        = 1;
		foreach ($array_id as $id) {
			$Salva = new MySQL();
			$Salva->set_table(PREFIX_TABLES . '_model_img');
			$Salva->set_where('id="' . $id . '"');
			$Salva->set_update('posicao', $i);
			if ($Salva->salvar()) {
				++$i;
			}
		}
	}
	
	
	###############################################################################################################################
	# RETORNA COMBO PARA EDIÇÃO DAS INFORMAÇÕES DE UM ARQUIVO INTERNO DO ITEM  
	###############################################################################################################################
	function dados_file() {
		$idFile = $_REQUEST['idFile'];
		$token  = $_REQUEST['token'];
		$Dados  = new MySQL();
		$Dados->set_table(PREFIX_TABLES . '_model_files');
		$Dados->set_where('id=' . $idFile);
		$Dados->select();
		$titulo    = $Dados->fetch_array[0]['titulo'];
		$descricao = $Dados->fetch_array[0]['texto'];
		$url       = $Dados->fetch_array[0]['url'];
		$download  = $Dados->fetch_array[0]['download'];
		echo '<form id="form-img" id-img="' . $idFile . '" style="padding: 0 20px;" >
			<input 		id="titulo" 	name="titulo" 		class="inputText" value="' . $titulo . '" placeholder="Titulo da imagem">
			<textarea 	id="textAreaInput" 	name="descricao" 	class="inputText">' . stripslashes(urldecode($descricao)) . '</textarea>
			<input 		id="url" 			name="url" 				class="inputText" value="' . $url . '"	placeholder="Link de Direcionamento"style="margin-top: 10px;" >
			<input 		id="token" 			name="token" 		type="hidden" value="' . $token . '" >
			<label>';
		if ($download == 1) {
			$download = 'checked="true"';
		}
		echo '<input id="download" name="download" type="checkbox" ' . $download . ' style="position:relative;width:fit-content;">
				Habilitar para download
			</label>
			</form>';
	}
	
	
	###############################################################################################################################
	# SALVA DADOS DE UM ARQUIVO INTERNO DO ITEM  
	###############################################################################################################################
	function SalvarDadosFiles() {
		$Salva = new MySQL();
		$Salva->set_table(PREFIX_TABLES . '_model_files');
		$Salva->set_where('id=' . $_POST['idFile']);
		$Salva->set_update('titulo', $_POST['titulo']);
		$Salva->set_update('texto', urlencode($_POST['texto']));
		$Salva->set_update('url', urlencode($_POST['url']));
		$Salva->set_update('download', $_POST['download']);
		$SalvaBiblio = new MySQL();
		$SalvaBiblio->set_table(PREFIX_TABLES . 'ws_biblioteca');
		$SalvaBiblio->set_where('tokenFile="' . $_POST['token'] . '"');
		$SalvaBiblio->set_update('download', $_POST['download']);
		$SalvaBiblio->salvar();
		if ($Salva->salvar()) {
			echo "Ítem salvo com sucesso!";
			exit;
		} else {
			_erro("Falha ao salvar arquivo.");
			exit;
		}
	}
	
	###############################################################################################################################
	# EXCLUI UM ARQUIVO INTERNO DO ITEM  
	###############################################################################################################################
	function ExcluiFile() {
		$iDimg = $_REQUEST['iDimg'];
		$Dados = new MySQL();
		$Dados->set_table(PREFIX_TABLES . '_model_files');
		$Dados->set_where('id=' . $iDimg);
		$Dados->select();
		$arquivo    = $Dados->obj[0]->file;
		$token      = $Dados->obj[0]->token;
		$Biblioteca = new MySQL();
		$Biblioteca->set_table(PREFIX_TABLES . 'ws_biblioteca');
		$Biblioteca->set_where('tokenFile="' . $token . '"');
		$Biblioteca->exclui();
		$ws_keyfile = new MySQL();
		$ws_keyfile->set_table(PREFIX_TABLES . 'ws_keyfile');
		$ws_keyfile->set_where('tokenFile="' . $token . '"');
		$ws_keyfile->exclui();
		$model_files = new MySQL();
		$model_files->set_table(PREFIX_TABLES . '_model_files');
		$model_files->set_where('token="' . $token . '"');
		$model_files->exclui();
		@unlink('./uploads/' . $arquivo);
		@unlink('./../../../website/assets/upload-files/' . $arquivo);
		echo "Sucesso em excluir o arquivo";
		exit;
	}
	
	###############################################################################################################################
	# RETORNA COMBO PARA EDIÇÃO DAS INFORMAÇÕES DE UMA IMAGEM INTERNA DO ITEM  
	###############################################################################################################################
	function dados_img() {
		$iDimg = $_REQUEST['iDimg'];
		$Dados = new MySQL();
		$Dados->set_table(PREFIX_TABLES . '_model_img');
		$Dados->set_where('id=' . $iDimg);
		$Dados->url('decode');
		$Dados->select();
		$titulo    = $Dados->fetch_array[0]['titulo'];
		$descricao = $Dados->fetch_array[0]['texto'];
		$url       = $Dados->fetch_array[0]['url'];
		$avatar    = $Dados->fetch_array[0]['avatar'];
		if ($avatar == '1') {
			$avatar = 'checked';
		} else {
			$avatar = '';
		}
		echo '<form id="form-img" id-img="' . $iDimg . '" >
			<input 		id="titulo" 		name="titulo" 			class="inputText" value="' . $titulo . '" placeholder="Titulo da imagem" style="width: 488px;left: 0;padding: 10px;margin-bottom: 20px;">
			<textarea 	id="textarea" 		name="descricao" 		class="inputText">' . stripslashes(urldecode($descricao)) . '</textarea>
			<input 		id="url" 			name="url" 				class="inputText" value="' . $url . '"	placeholder="Link de Direcionamento" style="width: 488px;left: 0;padding: 10px;margin-top: 20px;">
			</form>
			<script>
					CKEDITOR.replace( "textarea", {forcePasteAsPlainText	:true,fillEmptyBlocks:false,basicEntities:false,entities_greek:false, entities_latin:false, entities_additional:"",toolbarGroups: [{ name: "basicstyles" },{ name: "links" }]});
					setTimeout(function(){$(".cke_button[title=\"Add ShortCode\"] .cke_button_label").show();},500)
					reloadFunctions();
					$("#checkbox_avatar").LegendaOver();
			</script>';
	}
	
	###############################################################################################################################
	# SALVA INFORMAÇÕES DE UMA IMAGEM INTERNA DO ITEM  
	###############################################################################################################################
	function SalvarDadosImg() {
		if ($_REQUEST['avatar'] == 'on') {
			$_REQUEST['avatar'] = '1';
		} else {
			$_REQUEST['avatar'] = '0';
		}
		$Salva = new MySQL();
		$Salva->set_table(PREFIX_TABLES . '_model_img');
		//$Salva->set_where('ws_draft="1" AND id_item="' . $_REQUEST['idImg'] . '"');
		$Salva->set_where('id="' . $_REQUEST['iDimg'] . '"');
		$Salva->set_update('titulo', $_REQUEST['titulo']);
		$Salva->set_update('texto', urlencode($_REQUEST['texto']));
		$Salva->set_update('url', urlencode($_REQUEST['url']));
		$Salva->set_update('avatar', $_REQUEST['avatar']);
		if ($Salva->salvar()) {
			echo "sucesso";
			exit;
		} else {
			_erro("Falha ao salvar imagem, erro:" . __LINE__);
		}
	}
	
	###############################################################################################################################
	# EXCLUI UMA IMAGEM INTERNA DO ITEM  
	###############################################################################################################################
	function ExcluiImgm() {
		$iDimg = $_REQUEST['iDimg'];
		$D     = new MySQL();
		$D->set_table(PREFIX_TABLES . '_model_img');
		$D->set_where('id=' . $iDimg);
		$D->exclui();
	}
	
	###############################################################################################################################
	# ADICIONA UMA CATEGORIA A FERRAMENTA  
	###############################################################################################################################
	function addCategoria() {
		$token = _token(PREFIX_TABLES . '_model_cat', 'token');
		$I     = new MySQL();
		$I->set_table(PREFIX_TABLES . '_model_cat');
		$I->set_insert('token', $token);
		$I->set_insert('ws_id_ferramenta', $_REQUEST['ws_id_ferramenta']);
		$I->set_insert('id_cat', $_REQUEST['id_cat']);
		$I->set_insert('titulo', 'Nova categoria');
		if ($I->insert()) {
			echo json_encode(array(
				'resposta' => 'sucesso'
			));
		} else {
			echo json_encode(array(
				'resposta' => 'falha'
			));
		}
	}
	
	###############################################################################################################################
	# REORGANIZA O POSICIONAMENTO DAS CATEGORIAS   
	###############################################################################################################################
	function OrdenaCategoria() {
		$array_id = explode(',', $_REQUEST['ids']);
		$i        = 1;
		foreach ($array_id as $id) {
			$Salva = new MySQL();
			$Salva->set_table(PREFIX_TABLES . '_model_cat');
			$Salva->set_where('id="' . $id . '"');
			$Salva->set_update('posicao', $i);
			if ($Salva->salvar()) {
				++$i;
			}
		}
	}
	
	###############################################################################################################################
	# SALVA INFORMAÇÕES DE UMA CATEGORIA  
	###############################################################################################################################
	function SalvarDadosCategoria() {
		$Salva = new MySQL();
		$Salva->set_table(PREFIX_TABLES . '_model_cat');
		$Salva->set_where('id=' . $_POST['iCat']);
		$Salva->set_update('id_cat', stripslashes(strip_tags(urldecode($_POST['categoryTop']))));
		$Salva->set_update('titulo', stripslashes(strip_tags(urldecode($_POST['titulo']))));
		$Salva->set_update('texto', stripslashes(strip_tags(urldecode($_POST['texto']))));
		$Salva->set_update('ws_protect', stripslashes(strip_tags(urldecode($_POST['ws_protect']))));
		$Salva->set_update('url', urlencode($_POST['url']));
		if ($Salva->salvar()) {
			echo "sucesso!";
		} else {
			echo "Falha!";
		}
	}
	
	###############################################################################################################################
	###############################################################################################################################
	###############################################################################################################################  EXCLUSÃO DE DADOS
	###############################################################################################################################
	###############################################################################################################################
	
	function excl_arquivos($id) {
		$geral = new MySQL();
		$geral->set_table(PREFIX_TABLES . '_model_campos');
		$geral->set_where('type="file" OR type="bt_files"');
		$geral->select();
		if ($geral->_num_rows >= 1) {
			$mysql_files = new MySQL();
			$mysql_files->set_table(PREFIX_TABLES . '_model_files');
			$mysql_files->set_where('id_item="' . $id . '"');
			$mysql_files->select();
			foreach ($mysql_files->fetch_array as $file) {
				$exclui = new MySQL();
				$exclui->set_table(PREFIX_TABLES . '_model_files');
				$exclui->set_where('id="' . $file['id'] . '"');
				if ($exclui->exclui()) {
				}
			}
		}
	}
	function excl_img($id) {
		@ob_start();
		$geral = new MySQL();
		$geral->set_table(PREFIX_TABLES . '_model_campos');
		$geral->set_where('type="thumbmail" OR type="bt_fotos"');
		$geral->select();
		if ($geral->_num_rows >= 1) {
			$img_prod = new MySQL();
			$img_prod->set_table(PREFIX_TABLES . '_model_img');
			$img_prod->set_where('id_item="' . $id . '"');
			$img_prod->set_where('OR ws_id_draft="' . $id . '"');
			$img_prod->select();
			foreach ($img_prod->fetch_array as $imgp) {
				$exclui = new MySQL();
				$exclui->set_table(PREFIX_TABLES . '_model_img');
				$exclui->set_where('id="' . $imgp['id'] . '"');
				if ($exclui->exclui()) {
					@ob_end_clean();
				}
			}
		}
	}
	function excl_img_gal($id) {
		@ob_start();
		$geral = new MySQL();
		$geral->set_table(PREFIX_TABLES . '_model_campos');
		$geral->set_where('type="bt_galerias"');
		$geral->select();
		if ($geral->_num_rows >= 1) {
			$img_prod = new MySQL();
			$img_prod->set_table(PREFIX_TABLES . '_model_img_gal');
			$img_prod->set_where('id_galeria="' . $id . '"');
			$img_prod->select();
			foreach ($img_prod->fetch_array as $imgp) {
				$exclui = new MySQL();
				$exclui->set_table(PREFIX_TABLES . '_model_img_gal');
				$exclui->set_where('id="' . $imgp['id'] . '"');
				if ($exclui->exclui()) {
					@ob_end_clean();
				}
			}
		}
	}
	function excl_img_cat($id) {
		@ob_start();
		$geral = new MySQL();
		$geral->set_table(PREFIX_TABLES . '_model_campos');
		$geral->set_where('type="thumbmail" OR type="bt_fotos"');
		$geral->select();
		if ($geral->_num_rows >= 1) {
			$img_prod = new MySQL();
			$img_prod->set_table(PREFIX_TABLES . '_model_img');
			$img_prod->set_where('id_cat="' . $id . '"');
			$img_prod->set_where('AND avatar="1"');
			$img_prod->select();
			foreach ($img_prod->fetch_array as $imgp) {
				//@unlink('./uploads/'.$imgp['imagem']);
				$exclui = new MySQL();
				$exclui->set_table(PREFIX_TABLES . '_model_img');
				$exclui->set_where('id="' . $imgp['id'] . '"');
				if ($exclui->exclui()) {
					@ob_end_clean();
				}
			}
		}
	}
	function excl_gal($id) {
		@ob_start();
		$geral = new MySQL();
		$geral->set_table(PREFIX_TABLES . '_model_campos');
		$geral->set_where('type="bt_galerias"');
		$geral->select();
		if ($geral->_num_rows >= 1) {
			$img_prod = new MySQL();
			$img_prod->set_table(PREFIX_TABLES . '_model_gal');
			$img_prod->set_where('id_item="' . $id . '"');
			$img_prod->set_where('OR ws_id_draft="' . $id . '"');
			$img_prod->select();
			foreach ($img_prod->fetch_array as $imgp) {
				//if(@unlink('./uploads/'.$imgp['avatar'])){}
				excl_img_gal($imgp['id']);
				$exclui = new MySQL();
				$exclui->set_table(PREFIX_TABLES . '_model_gal');
				$exclui->set_where('id="' . $imgp['id'] . '"');
				if ($exclui->exclui()) {
					@ob_end_clean();
				}
			}
		}
	}
	function excl_prod($id) {
		@ob_start();
		$produto = new MySQL();
		$produto->set_table(PREFIX_TABLES . '_model_item');
		$produto->set_where('id_cat="' . $id . '"');
		$produto->set_order('posicao', 'ASC');
		$produto->select();
		//excl_links($id);
		foreach ($produto->fetch_array as $prod) {
			excl_img($prod['id']);
			excl_gal($prod['id']);
			excl_arquivos($prod['id']);
			if ($_SESSION['_NIVEIS_'] >= 1) {
				$exclui = new MySQL();
				$exclui->set_table(PREFIX_TABLES . '_model_link_prod_cat');
				$exclui->set_where('id_item="' . $id . '"');
				$exclui->set_where('OR ws_id_draft="' . $id . '"');
				$exclui->exclui();
			}
			$exclui = new MySQL();
			$exclui->set_table(PREFIX_TABLES . '_model_item');
			$exclui->set_where('id="' . $prod['id'] . '"');
			$exclui->set_where('OR ws_id_draft="' . $prod['id'] . '"');
			if ($exclui->exclui()) {
				@ob_end_clean();
			}
		}
	}
	function excl_links($id) {
		$categorias = new MySQL();
		$categorias->set_table(PREFIX_TABLES . 'ws_link_itens');
		$categorias->set_where('id_item="' . $id . '"');
		$categorias->set_where('OR id_item_link="' . $id . '"');
		$categorias->set_where('OR ws_id_draft="' . $id . '"');
		$categorias->exclui();
	}
	function excl_links_cats($id) {
		$categorias = new MySQL();
		$categorias->set_table(PREFIX_TABLES . 'ws_link_itens');
		$categorias->set_where('id_item="' . $id . '"');
		$categorias->set_where('OR id_cat_link="' . $id . '"');
		$categorias->set_where('OR ws_id_draft="' . $id . '"');
		$categorias->exclui();
	}
	function excl_produto() {
		excl_img($_REQUEST['id_item']);
		excl_gal($_REQUEST['id_item']);
		excl_arquivos($_REQUEST['id_item']);
		//excl_links($_REQUEST['id_item']);
		if ($_SESSION['_NIVEIS_'] >= 1) {
			$excluiNIVEIS = new MySQL();
			$excluiNIVEIS->set_table(PREFIX_TABLES . '_model_link_prod_cat');
			$excluiNIVEIS->set_where('id_item="' . $_REQUEST['id_item'] . '"');
			$excluiNIVEIS->set_where('OR ws_id_draft="' . $_REQUEST['id_item'] . '"');
			$excluiNIVEIS->exclui();
		}
		$excluiITEM = new MySQL();
		$excluiITEM->set_table(PREFIX_TABLES . '_model_item');
		$excluiITEM->set_where('id="' . $_REQUEST['id_item'] . '"');
		$excluiITEM->set_where('OR ws_id_draft="' . $_REQUEST['id_item'] . '"');
		if ($excluiITEM->exclui()) {
			echo "sucesso";
			exit;
		} else {
			echo "falha!";
			exit;
		}
	}
	function excl_cat($id) {
		@ob_start();
		$categoria = new MySQL();
		$categoria->set_table(PREFIX_TABLES . '_model_cat');
		$categoria->set_where('id="' . $id . '"');
		$categoria->select();
		excl_links_cats($id);
		$id_cat = new MySQL();
		$id_cat->set_table(PREFIX_TABLES . '_model_cat');
		$id_cat->set_where('id_cat="' . $id . '"');
		$id_cat->debug(0);
		$id_cat->select();
		// verifica as sub categorias  e aplica a função nelas
		foreach ($id_cat->fetch_array as $gal) {
			excl_cat($gal['id']);
		}
		// exclui relações da categoria com os produtos
		$exclui = new MySQL();
		$exclui->set_table(PREFIX_TABLES . '_model_link_prod_cat');
		$exclui->set_where('id_cat="' . $id . '"');
		if ($exclui->exclui()) {
			@ob_end_clean();
		}
		// exclui as imagens da categoria e vamos pro produto
		excl_img_cat($id);
		excl_prod($id);
		// exclui o registro
		$exclui = new MySQL();
		$exclui->set_table(PREFIX_TABLES . '_model_cat');
		$exclui->set_where('id="' . $id . '"');
		if ($exclui->exclui()) {
			@ob_end_clean();
		}
	}
	function ExcluiCategoria() {
		$id_cat = $_REQUEST['id_cat'];
		excl_cat($id_cat);
		@ob_end_clean();
		exit;
	}
	
	###############################################################################################################################
	#	ADICIONA UM ÍTEM A FERRAMENTA
	###############################################################################################################################
	function addItem() {
		$token = _token(PREFIX_TABLES . '_model_item', 'token');
		$I     = new MySQL();
		$I->set_table(PREFIX_TABLES . '_model_item');
		$I->set_insert('token', $token);
		$I->set_insert('ws_id_ferramenta', 	$_POST['ws_id_ferramenta']);
		$I->set_insert('id_cat', 			$_POST['id_cat']);
		$I->set_insert('ws_nivel', 			$_POST['ws_nivel']);
		$I->set_insert('ws_author', 		$_SESSION['user']['id']);


		if ($I->insert()) {
			$I = new MySQL();
			$I->set_table(PREFIX_TABLES . '_model_item');
			$I->set_where('token="' . $token . '"');
			$I->select();
			$id = $I->fetch_array[0]['id'];
			echo json_encode(array(
				'resposta' => 'sucesso',
				'id' => $id
			));
		} else {
			echo json_encode(array(
				'resposta' => 'falha'
			));
		}
	}
	
	###############################################################################################################################
	#	REPOSICIONA A ORDEM DOS ÍTENS (desabilitado temporariamente)
	###############################################################################################################################
	function OrdenaItem() {
		$array_pos      = $_REQUEST['posicoes'];
		$array_id       = $_REQUEST['ids'];
		$i              = 0;
		sort($array_pos,SORT_NUMERIC);
		 foreach ($array_id as $id) {
			$Salva = new MySQL();
			$Salva->set_table(PREFIX_TABLES . '_model_item');
			$Salva->set_where('id="' . $array_id[$i] . '"');
			$Salva->set_update('posicao',$array_pos[$i]);
			$Salva->salvar();
		 	++$i;
		}
	}
	
	###############################################################################################################################
	#	ADICIONA UMA GALERTIA DE IMAGENS AO ÍTEM
	###############################################################################################################################
	function addGaleria() {
		criaRascunho($_REQUEST['ws_id_ferramenta'], $_REQUEST['id_item']);
		$token = _token(PREFIX_TABLES . '_model_gal', 'token');
		$I     = new MySQL();
		$I->set_table(PREFIX_TABLES . '_model_gal');
		$I->set_insert('token', $token);
		$I->set_insert('ws_id_ferramenta', $_REQUEST['ws_id_ferramenta']);
		$I->set_insert('id_item', $_REQUEST['id_item']);
		$I->set_insert('id_cat', $_REQUEST['id_cat']);
		$I->set_insert('ws_draft', "1");
		$I->set_insert('ws_id_draft', $_REQUEST['id_item']);
		if ($I->insert()) {
			$I = new MySQL();
			$I->set_table(PREFIX_TABLES . '_model_gal');
			$I->set_where('token="' . $token . '"');
			$I->select();
			if ($I->fetch_array[0]['avatar'] == '') {
				$I->fetch_array[0]['avatar'] = '/admin/App/Templates/img/websheep/avatar.png';
			}
		}
	}
	
	###############################################################################################################################
	#	ORGANIZA E REPOSICIONA AS GALERIAS DE UM ÍTEM
	###############################################################################################################################
	function OrdenaGaleria() {
		$array_id = explode(',', $_REQUEST['ids']);
		$i        = 1;
		foreach ($array_id as $id) {
			$Salva = new MySQL();
			$Salva->set_table(PREFIX_TABLES . '_model_gal');
			$Salva->set_where('id="' . $id . '"');
			$Salva->set_update('posicao', $i);
			if ($Salva->salvar()) {
				++$i;
			}
		}
	}
	
	###############################################################################################################################
	#	RETORNA COMBO PARA EDIÇÃO DAS INFORMAÇÕES DE UMA GALERIA
	###############################################################################################################################
	function dados_galeria() {
		$iDimg = $_REQUEST['iDimg'];
		$Dados = new MySQL();
		$Dados->set_table(PREFIX_TABLES . '_model_gal');
		$Dados->set_where('id=' . $iDimg);
		$Dados->select();
		$titulo    = $Dados->fetch_array[0]['titulo'];
		$descricao = $Dados->fetch_array[0]['texto'];
		$url       = $Dados->fetch_array[0]['url'];
		$avatar    = $Dados->fetch_array[0]['avatar'];
		
		echo '	<form id="form-img" id-img="' . $iDimg . '" >
				<img src="/ws-img/320/375/100/' . $avatar . '" style="width:320px; height:375px; float: left;margin-left: 20px;border: solid 1px rgba(0, 0, 0, 0.09);">
				<input 		id="titulo" 	name="titulo" 		class="inputText" value="' . $titulo . '" placeholder="Titulo da imagem" style="padding:10px;width:444px;">
				<textarea 	id="textarea" 	name="descricao" 	class="inputText">' . stripslashes(urldecode($descricao)) . '</textarea>
				<input 		id="url" 		name="url" 			class="inputText" value="' . $url . '" placeholder="Url:" style="padding:10px;width:440px;margin-top: 10px;">
				</form>
				<script>
					CKEDITOR.replace( "textarea", {forcePasteAsPlainText	:true,fillEmptyBlocks:false,basicEntities:false,entities_greek:false, entities_latin:false, entities_additional:"",toolbarGroups: [{ name: "basicstyles" },{ name: "links" }]});
					setTimeout(function(){$(".cke_button[title=\"Add ShortCode\"] .cke_button_label").show();},500)
					reloadFunctions();
				</script>';
	}
	
	###############################################################################################################################
	#	SALVA OS DADOS DE UMA GALERIA
	###############################################################################################################################
	function SalvarDadosGalerias() {
		$Salva = new MySQL();
		$Salva->set_table(PREFIX_TABLES . '_model_gal');
		$Salva->set_where('id=' . $_REQUEST['iDimg']);
		$Salva->set_update('titulo', $_REQUEST['titulo']);
		$Salva->set_update('texto', urlencode($_REQUEST['texto']));
		$Salva->set_update('url', urlencode($_REQUEST['url']));
		if ($Salva->salvar()) {
			$dado = new MySQL();
			$dado->set_table(PREFIX_TABLES . '_model_gal');
			$dado->set_where('id=' . $_REQUEST['iDimg']);
			$dado->select();
			if ($dado->fetch_array[0]['avatar'] == '') {
				$avatar = '/admin/App/Templates/img/websheep/avatar.png';
			} else {
				$avatar = $dado->fetch_array[0]['avatar'];
			}
			echo "<img class='avatar' src='/ws-img/40/40/60/" . $avatar . "'>
		<span class='titulo_item w1'>" . stripslashes(substr(strip_tags(urldecode($dado->fetch_array[0]['titulo'])), 0, 100)) . "</span>
		<span class='desc_item w2'>" . stripslashes(substr(strip_tags(urldecode($dado->fetch_array[0]['texto'])), 0, 100)) . "...</span>
		<div id='combo'>
		<div id='detalhes_img' class='bg02'>
			<span><img class='mover_item' 		src='/admin/App/Templates/img/websheep/arrow-move.png'>	</span>
			<span><img class='galeria'			src='/admin/App/Templates/img/websheep/images.png'>		</span>
			<span><img class='editar' 			src='/admin/App/Templates/img/websheep/layer--pencil.png'>	</span>
			<span><img class='excluir'			src='/admin/App/Templates/img/websheep/cross-button.png'>	</span>
		</div>
			<form name='formUpload' class='formUploadGaleria' action='./" . $_REQUEST['path'] . "/upload_files.php' method='post' enctype='multipart/form-data' name='formUpload'>
				<input name='arquivo' id='myfile' type='file' style='display:none'/>
				<input name='_c_' hidden='true' value='" . $dado->fetch_array[0]['id'] . "'/>
				<input name='_t_' hidden='true' value='".PREFIX_TABLES."_model_gal'/>
				<button type='submit' class='enviar_arquivos' style='display:none'></button>
			</form>
		</div>";
		} else {
			_erro("Falha ao salvar estado personalizado.");
		}
	}
	
	###############################################################################################################################
	#	EXCLUI UMA GALERIA
	###############################################################################################################################
	function ExcluiGaleria() {
		$iDgaleria = $_REQUEST['iDgaleria'];
		$IMG       = new MySQL();
		$IMG->set_table(PREFIX_TABLES . '_model_img_gal');
		$IMG->set_where('id_galeria="' . $iDgaleria . '"');
		$IMG->select();
		foreach ($IMG->fetch_array as $img) {
			$D = new MySQL();
			$D->set_table(PREFIX_TABLES . '_model_img_gal');
			$D->set_where('id=' . $img['id']);
			if ($D->exclui()) {
				// unlink('./uploads/'.$img['imagem']);
			}
		}
		$Gal = new MySQL();
		$Gal->set_table(PREFIX_TABLES . '_model_gal');
		$Gal->set_where('id=' . $iDgaleria);
		$Gal_thb = new MySQL();
		$Gal_thb->set_table(PREFIX_TABLES . '_model_gal');
		$Gal_thb->set_where('id=' . $iDgaleria);
		$Gal_thb->select();
		$Gal->exclui();
		//unlink('./uploads/'.$Gal_thb->fetch_array[0]['avatar']);
	}
	
	###############################################################################################################################
	#	RETORNA COMBO PARA EDIÇÃO DAS INFORMAÇÕES DE UMA IMAGEM INTERNA DE UMA GALERIA
	###############################################################################################################################
	function dados_img_gal() {
		$iDimg = $_REQUEST['iDimg'];
		$Dados = new MySQL();
		$Dados->set_table(PREFIX_TABLES . '_model_img_gal');
		$Dados->set_where('id=' . $iDimg);
		$Dados->select();
		$titulo    = $Dados->fetch_array[0]['titulo'];
		$descricao = $Dados->fetch_array[0]['texto'];
		$imagem    = $Dados->fetch_array[0]['file'];
		$url       = $Dados->fetch_array[0]['url'];
		echo '<form id="form-img" id-img="' . $iDimg . '" >
			<img src="/ws-img/320/320/100/' . $imagem . '" style="width:320px; height:320px; float: left;margin-left: 20px;border: solid 1px rgba(0, 0, 0, 0.09);">
			<input 		id="titulo" 	name="titulo" 		class="inputText" value="' . $titulo . '" placeholder="Titulo da imagem">
			<textarea 	id="textarea" 	name="descricao" 	class="inputText"style="width:320px;">' . stripslashes(urldecode($descricao)) . '</textarea>
			</form>
			<script>
				CKEDITOR.replace( "textarea", {
					forcePasteAsPlainText	:true,
					fillEmptyBlocks:false,
					basicEntities:false,
					entities_greek:false, 
					entities_latin:false, 
					entities_additional:"",
					toolbarGroups: [{ name: "basicstyles" },{ name: "links" }]});
				setTimeout(function(){$(".cke_button[title=\"Add ShortCode\"] .cke_button_label").show();},500)
				reloadFunctions();
			</script>';
	}
	
	###############################################################################################################################
	#	SALVA AS INFORMAÇÕES DE UMA IMAGEM INTERNA DE GALERIA
	###############################################################################################################################
	function SalvarDados() {
		$Salva = new MySQL();
		$Salva->set_table(PREFIX_TABLES . '_model_img_gal');
		$Salva->set_where('id=' . $_REQUEST['idImg']);
		$Salva->set_update('titulo', $_REQUEST['titulo']);
		$Salva->set_update('texto', urlencode($_REQUEST['texto']));
		$Salva->set_update('url', urlencode($_REQUEST['url']));
		if ($Salva->salvar()) {
			echo 'sucesso';
			exit;
		} else {
			_erro("Falha ao salvar estado personalizado.");
		}
	}
	
	###############################################################################################################################
	#	EXCLUI UMA IMAGEM INTERNA DE UMA GALERIA
	###############################################################################################################################
	function ExcluiImagem_gal() {
		$iDimg = $_REQUEST['iDimg'];
		$D     = new MySQL();
		$D->set_table(PREFIX_TABLES . '_model_img_gal');
		$D->set_where('id=' . $iDimg);
		$D->exclui();
		exit;
	}
	
	###############################################################################################################################
	#	POSICIONA E REORGANIZA AS FOTOS INTERNAS DE UMA GALERIA
	###############################################################################################################################
	function ordena_fotos() {
		$array_id = explode(',', $_REQUEST['ids']);
		$i        = 1;
		foreach ($array_id as $id) {
			$Salva = new MySQL();
			$Salva->set_table(PREFIX_TABLES . '_model_img_gal');
			$Salva->set_where('id="' . $id . '"');
			$Salva->set_update('posicao', $i);
			if ($Salva->salvar()) {
				++$i;
			}
		}
	}
	
	###############################################################################################################################
	#	CASO A FERRAMENTA NÃO TENHA NÍVEIS E SEJA APENAS UMA GALERIA DE IMAGENS, AO INVEZ DE SALVAR O ÍTEM, ELE PUBLICA AS IMAGENS
	###############################################################################################################################
	function PublicaRascunhoImagens() {
		$vars = $_POST;
		if (aplicaRascunho($vars['ws_id_ferramenta'], $vars['id_item'], true)) {
			echo "Ítem salvo com sucesso!";
		}
	}
	
	##########################################################################################################
	# 	PUBLICA UM RASCUNHO DE UM ÍTEM
	##########################################################################################################
	function PublicaRascunho() {
		$vars = $_POST;
		if (SalvaDetalhes($_POST)) {
			if (aplicaRascunho($vars['ws_id_ferramenta'], $vars['id_item'])) {
				echo "Ítem salvo com sucesso!";
			} else {
				echo "Falha em publicar rascunho!";
			}
		} else {
			echo "Falha em salvar rascunho!";
		}
	}
	
	##########################################################################################################
	# SALVA RASCUINHO DE UM ÍTEM
	##########################################################################################################
	function SalvaDetalhes($vars = null) {
		if ($vars != null) { $_POST = $vars;}


		global $_conectMySQLi_;

		$id_item 			= @$_POST['id_item'];
		$function 			= @$_POST['function'];
		$ws_session			= @$_POST['ws_session'];
		$ws_id_ferramenta 	= @$_POST['ws_id_ferramenta'];
		$id_item       		= @$_POST['id_item'];
		$function 			= @$_POST['function'];
		$_link_opt_cat_ 	= @$_POST['_link_opt_cat_'];

		unset($_POST['ws_log']);
		unset($_POST['ws_session']);
		unset($_POST['ws_id_ferramenta']);
		unset($_POST['id_item']);
		unset($_POST['function']);
		unset($_POST['_link_opt_cat_']);

		##########################################################################################################
		# PEGAMOS A FERRAMENTA
		##########################################################################################################
		$FERRAMENTA = new MySQL();
		$FERRAMENTA->set_table(PREFIX_TABLES . 'ws_ferramentas');
		$FERRAMENTA->set_where('id="' . $ws_id_ferramenta . '"');
		$FERRAMENTA->select();
		$FERRAMENTA = @$FERRAMENTA->fetch_array[0];

		##########################################################################################################
		# SEPARAMOS OS CAMPOS DESTE ÍTEM
		##########################################################################################################
		$campos     = new MySQL();
		$campos->set_table(PREFIX_TABLES . '_model_campos');
		$campos->set_order("posicao", "ASC");
		$campos->set_where('ws_id_ferramenta="' . $ws_id_ferramenta . '"');
		$campos->select();

		##########################################################################################################
		# CRIAMOS O RASCUNHO CASO NÃO TENHA
		##########################################################################################################
		criaRascunho($ws_id_ferramenta, $id_item);

		##########################################################################################################
		# SELECIONA AGORA O RASCUNHO QUE SERÁ COLOCADO AS INFORMAÇÕES 
		##########################################################################################################
		$_SALVAR_ = new MySQL();
		$_SALVAR_->set_table(PREFIX_TABLES . '_model_item');
		$_SALVAR_->set_where('ws_id_draft="' . $id_item . '"');
		$_SALVAR_->set_where('AND ws_id_ferramenta="' . $ws_id_ferramenta . '"');
		foreach ($_POST as $KEY => $POST) {
			$campos = new MySQL();
			$campos->set_table(PREFIX_TABLES . '_model_campos');
			$campos->set_where('coluna_mysql="' . $KEY . '"');
			$campos->select();
			$TYPE = @$campos->fetch_array[0]['type'];
			if ($TYPE == 'key_works') {
				if (is_array($POST) == 1) {
					$_SALVAR_->set_update($KEY, implode($POST, ","));
				} else {
					$_SALVAR_->set_update($KEY, $POST);
				}
			} elseif ($TYPE == 'multiple_select') {
				if (is_array($POST)) {
					$VALOR = implode($POST, "[-]");
					$_SALVAR_->set_update($KEY, $VALOR);
				} else {
					$_SALVAR_->set_update($KEY, $POST);
				}
			} elseif ($TYPE == "keyworks") {
				if (is_array($POST)) {
					$_SALVAR_->set_update($KEY, implode($POST, ","));
				} else {
					$_SALVAR_->set_update($KEY, $POST);
				}
			} else {
				$_SALVAR_->set_update($KEY, mysqli_real_escape_string($_conectMySQLi_, urldecode($POST)));
			}
		}

		if ($vars != null) {

			if (count($_POST) >= 1) {
				if ($_SALVAR_->salvar()) {
					return true;
					exit;
				} else {
					return false;
					exit;
				}
			} else {
				return true;
				exit;
			}
		} else {
			if (count($_POST) >= 1) {
				if ($_SALVAR_->salvar()) {
					echo 'Ítem salvo com sucesso!';
					exit;
				} else {
					echo "Ops! Houve um arro ao salvar";
					exit;
				}
			} else {
				echo 'Ítem salvo com sucesso!';
				exit;
			}
		}
	}
	
	##########################################################################################################
	# RETORNA MODAL COM FORMULARIO DE EDIÇÃO DE UM SELECTBOX
	##########################################################################################################
	function editaCamposSelect() {
		$keyarray      = array();
		$KeyAfinidades = new MySQL();
		$KeyAfinidades->set_table(PREFIX_TABLES . '_model_campos');
		$KeyAfinidades->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$KeyAfinidades->select();
		$opcoes = explode("|", $KeyAfinidades->fetch_array[0]['values_opt']);
		foreach ($opcoes as $op) {
			$orderarray[] = $op;
		}
		//sort($orderarray);
		$opcoes = implode($orderarray, "|");
		echo '
		Aperte a tecla "Enter" para cadastrar as palavras separadamente:
		<div class="c"></div>
		<textarea id="textarea" rows="1" style="position: absolute; width: 489px; left: 0px;"></textarea>
	   <script type="text/javascript">
	    var Added	=	false;
	    setTimeout(function(){
		    $("#textarea").textext({
		            plugins : "tags",
		            tagsItems: ["';
		echo str_replace("|", '","', $opcoes);
		echo '"],
		             ext: {
			            tags: {
			            		onEnterKeyPress: function(tags){
			            			Added=true
						            $.fn.textext.TextExtTags.prototype.onEnterKeyPress.apply(this, arguments);
				                },
			            		addTags: function(tags){
						            $.fn.textext.TextExtTags.prototype.addTags.apply(this, arguments);
						            var teste = arguments;

						            if(Added==true){
					                   functions({
											patch:"' . $_REQUEST['path'] . '",
											funcao:"add_key_Select_Options",
											vars:"tags="+tags+"&id_campo=' . $_REQUEST['id_campo'] . '&id_item=' . $_REQUEST['id_item'] . '",
											Sucess:function(e){
												if(tags!=""){
													 var newOption = $(e);
													$("#' . $_REQUEST['id_campo'] . '").empty();
													$("#' . $_REQUEST['id_campo'] . '").append(newOption);
												}
											}
										})
						            }
				                },

				            	removeTag:function(tags){
				                   	$.fn.textext.TextExtTags.prototype.removeTag.apply(this, arguments);
				                    functions({
										patch:"' . $_REQUEST['path'] . '",
										funcao:"remove_key_Select_Options",
										vars:"tags="+tags[0].innerText+"&id_campo=' . $_REQUEST['id_campo'] . '&id_item=' . $_REQUEST['id_item'] . '",
										Sucess:function(e){
												var newOption = $(e);
												$("#' . $_REQUEST['id_campo'] . '").empty();
												$("#' . $_REQUEST['id_campo'] . '").append(newOption);
										}
									})
				            	}
							}
						}

			}).bind("removeTag", function(e,tag,value){
		        alert(tag.data);
		    })
		},500);
		</script>';
	}
	
	##########################################################################################################
	# FUNÇÃO QUE REMOVE UMA OPÇÃO E DÁ UPDATE AO SELECTOBOX NO ÍTEM
	##########################################################################################################
	function remove_key_Select_Options() {
		$D = new MySQL();
		$D->set_table(PREFIX_TABLES . '_model_campos');
		$D->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$D->select();
		$opcoes = $D->fetch_array[0]['values_opt'];
		$opcoes = explode("|", $opcoes);
		sort($opcoes);
		foreach ($opcoes as $op) {
			if ($op != $_REQUEST['tags']) {
				$novasOpcoes[] = $op;
			}
		}
		$opcoes = implode($novasOpcoes, "|");
		$S      = new MySQL();
		$S->set_table(PREFIX_TABLES . '_model_campos');
		$S->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$S->set_update('values_opt', $opcoes);
		if ($S->salvar()) {
			$O = new MySQL();
			$O->set_table(PREFIX_TABLES . '_model_campos');
			$O->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
			$O->select();
			$_values = $O->fetch_array[0]['values_opt'];
			$opcoes  = explode('|', $_values);
			sort($opcoes);
			foreach ($opcoes as $op) {
				$R = new MySQL();
				$R->set_table(PREFIX_TABLES . '_model_item');
				$R->set_where('id="' . $_REQUEST['id_item'] . '"');
				$R->select();
				if (urldecode($R->fetch_array[0][$O->fetch_array[0]['coluna_mysql']]) == $op) {
					$chek = 'selected';
				} else {
					$chek = '';
				}
				if ($op != "") {
					echo '<option name="' . $O->fetch_array[0]['name'] . '" value="' . $op . '" >' . $op . '</option>';
				}
			}
		}
	}
	
	##########################################################################################################
	# FUNÇÃO QUE ADICIONA UMA OPÇÃO E DÁ UPDATE AO SELECTOBOX NO ÍTEM
	##########################################################################################################
	function add_key_Select_Options() {
		$O = new MySQL();
		$O->set_table(PREFIX_TABLES . '_model_campos');
		$O->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$O->select();
		$_values       = $O->fetch_array[0]['values_opt'];
		$opcoes_values = explode('|', $_values);
		sort($opcoes_values);
		$S = new MySQL();
		$S->set_table(PREFIX_TABLES . '_model_campos');
		$S->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$opcoes_atuais[] = $_REQUEST['tags'];
		sort($opcoes_atuais);
		foreach ($opcoes_values as $opv) {
			if ($opv != "") {
				$opcoes_atuais[] = $opv;
			}
		}
		if ($_REQUEST['tags'] != "") {
			$S->set_update('values_opt', implode($opcoes_atuais, "|"));
		}
		if ($S->salvar()) {
			$O = new MySQL();
			$O->set_table(PREFIX_TABLES . '_model_campos');
			$O->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
			$O->select();
			$_values = $O->fetch_array[0]['values_opt'];
			$opcoes  = explode('|', $_values);
			sort($opcoes);
			foreach ($opcoes as $op) {
				$R = new MySQL();
				$R->set_table(PREFIX_TABLES . '_model_item');
				$R->set_where('id="' . $_REQUEST['id_item'] . '"');
				$R->select();
				if (urldecode($R->fetch_array[0][$O->fetch_array[0]['coluna_mysql']]) == $op) {
					$chek = 'selected';
				} else {
					$chek = '';
				}
				echo '<option name="' . $O->fetch_array[0]['name'] . '" value="' . $op . '" >' . $op . '</option>';
			}
		}
	}
	
	##########################################################################################################
	# RETORNA MODAL COM FORMULARIO DE EDIÇÃO DE UM MULTIPLE SELECTBOX
	##########################################################################################################
	function edita_select_box_multiple() {
		$multiple = array();
		$multiple = new MySQL();
		$multiple->set_table(PREFIX_TABLES . '_model_op_multiple');
		$multiple->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$multiple->select();
		echo '
			Aperte a tecla "Enter" para cadastrar as palavras separadamente:
			<div class="c"></div>
			<textarea id="textarea" rows="1" style="position: absolute; width: 489px; left: 0px;"></textarea>
		   <script type="text/javascript">
		    var Added	=	false;
		    setTimeout(function(){
			    $("#textarea").textext({
			            plugins : "tags",
			            tagsItems: ["';
		foreach ($multiple->fetch_array as $op) {
			$orderarray[] = @$op['label'];
		}
		echo @implode($orderarray, '","');
		echo '"],
	             ext: {
		            tags: {
		            		onEnterKeyPress: function(tags){
		            			Added=true
					            $.fn.textext.TextExtTags.prototype.onEnterKeyPress.apply(this, arguments);
			                },
		            		addTags: function(tags){
					            $.fn.textext.TextExtTags.prototype.addTags.apply(this, arguments);
					            var teste = arguments;
					            if(Added==true){
				                	   functions({
										patch:"' . $_REQUEST['path'] . '",
										funcao:"add_opt_Select_Options_multiple",
										vars:"tags="+tags+"&ws_id_ferramenta=' . $_REQUEST['ws_id_ferramenta'] . '&id_campo=' . $_REQUEST['id_campo'] . '&id_item=' . $_REQUEST['id_item'] . '",
										Sucess:function(e){
											if(tags!=""){
												var newOption = $(e);
												$("#' . $_REQUEST['id_campo'] . '").empty();
												$("#' . $_REQUEST['id_campo'] . '").prepend(newOption);
												setTimeout(function(){$("#' . $_REQUEST['id_campo'] . '").trigger("chosen:updated");},500);
											}
										}
									})
					            }
			                },

			            	removeTag:function(tags){
			                   	$.fn.textext.TextExtTags.prototype.removeTag.apply(this, arguments);

			                    functions({
									patch:"'.$_REQUEST['path'].'",
									funcao:"remove_opt_Select_Options_multiple",
									vars:"path='.$_REQUEST['path'].'&tags="+tags[0].innerText+"&id_campo=' . $_REQUEST['id_campo'] . '&id_item=' . $_REQUEST['id_item'] . '",
									Sucess:function(e){
											var newOption = $(e);
											$("#' . $_REQUEST['id_campo'] . '").empty();
											$("#' . $_REQUEST['id_campo'] . '").prepend(newOption);
											setTimeout(function(){$("#' . $_REQUEST['id_campo'] . '").trigger("chosen:updated");},500);

									}
								})
			            	}
						}
					}

		}).bind("removeTag", function(e,tag,value){
	        alert(tag.data);
	    })
			},500);


		</script>';
	}
	
	##########################################################################################################
	# FUNÇÃO QUE ADICIONA UMA OPÇÃO E DÁ UPDATE AO MULTIPLE SELECTOBOX NO ÍTEM
	##########################################################################################################
	function add_opt_Select_Options_multiple() {
		$I = new MySQL();
		$I->set_table(PREFIX_TABLES . '_model_op_multiple');
		$I->set_insert('ws_id_ferramenta', $_REQUEST['ws_id_ferramenta']);
		$I->set_insert('id_ferramenta', $_REQUEST['ws_id_ferramenta']);
		$I->set_insert('id_item', $_REQUEST['id_item']);
		$I->set_insert('id_campo', $_REQUEST['id_campo']);
		$I->set_insert('label', $_REQUEST['tags']);
		$I->insert();
		$labels = array();
		$labels = new MySQL();
		$labels->set_table(PREFIX_TABLES . '_model_op_multiple');
		$labels->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$labels->select();
		foreach ($labels->fetch_array as $op) {
			$selected = new MySQL();
			$selected->set_table(PREFIX_TABLES . '_model_link_op_multiple');
			$selected->set_where('id_opt="' . $op['id'] . '"');
			$selected->set_where('AND id_campo="' . $_REQUEST['id_campo'] . '"');
			$selected->set_where('AND id_item="' . $_REQUEST['id_item'] . '"');
			$selected->set_where('AND id_ferramenta="' . $_REQUEST['ws_id_ferramenta'] . '"');
			$selected->set_insert('AND ws_draft', '1');
			$selected->select();
			if ($selected->_num_rows >= 1) {
				$chek = "selected";
			} else {
				$chek = "";
			}
			echo '<option id="' . $op['id'] . '" value="' . $op['label'] . '" ' . $chek . '>' . $op['label'] . '</option>';
		}
	}
	
	##########################################################################################################
	# FUNÇÃO QUE REMOVE UMA OPÇÃO E DÁ UPDATE AO MULTIPLE SELECTOBOX NO ÍTEM
	##########################################################################################################
	function remove_opt_Select_Options_multiple() {
		$labels = array();
		$labels = new MySQL();
		$labels->set_table(PREFIX_TABLES . '_model_op_multiple');
		$labels->set_where('label="' . $_REQUEST['tags'] . '"');
		$labels->select();
		foreach ($labels->fetch_array as $op) {
			$linkLabels = new MySQL();
			$linkLabels->set_table(PREFIX_TABLES . '_model_link_op_multiple');
			$linkLabels->set_where('id_opt="' . $op['id'] . '"');
			$linkLabels->set_where('AND ws_draft="1"');
			$linkLabels->exclui();
			$excl_label = new MySQL();
			$excl_label->set_table(PREFIX_TABLES . '_model_op_multiple');
			$excl_label->set_where('id="' . $op['id'] . '"');
			$excl_label->exclui();
		}
		$labels = array();
		$labels = new MySQL();
		$labels->set_table(PREFIX_TABLES . '_model_op_multiple');
		$labels->set_where('id_campo="' . $_REQUEST['id_campo'] . '"');
		$labels->select();
		foreach ($labels->fetch_array as $op) {
			$selected = new MySQL();
			$selected->set_table(PREFIX_TABLES . '_model_link_op_multiple');
			$selected->set_where('id_opt="' . $op['id'] . '"');
			$selected->set_where('AND id_campo="' . $_REQUEST['id_campo'] . '"');
			$selected->set_where('AND id_item="' . $_REQUEST['id_item'] . '"');
			$selected->set_where('AND id_ferramenta="' . $_REQUEST['ws_id_ferramenta'] . '"');
			$selected->set_where('AND ws_draft="1"');
			$selected->select();
			if ($selected->_num_rows >= 1) {
				$chek = "selected";
			} else {
				$chek = "";
			}
			echo '<option id="' . $op['id'] . '" value="' . $op['label'] . '" ' . $chek . '>' . $op['label'] . '</option>';
		}
	}
	
	##########################################################################################################
	# AGORA QUE TODAS AS FUNÇÕES JÁ FORAM DEFINIDAS, DAMOS START AO BUFFER E INCLUÍMOS A FUNÇÃO BASE DO SISTEMA
	##########################################################################################################
	clearstatcache();
	ob_start();
	include_once($_SERVER['DOCUMENT_ROOT'] . '/admin/App/Lib/class-ws-v1.php');
	
	##########################################################################################################
	# INICIA A SESSÃO
	##########################################################################################################
	_session();
	
	##########################################################################################################
	# EXECUTA A FUNÇÃO REQUERIDA VIA AJAX
	##########################################################################################################
	_exec(@$_REQUEST['function']);
?>