<?

/*###############################################################################################################################################################  
*
*
*	Para melhor manutenção, acumulei todos os scripts de upload em um só arquivo
*
*
###############################################################################################################################################################*/

	##########################################################################################################  
	# IMPORTAMOS A CLASSE PADRÃO DO SISTEMA
	##########################################################################################################
		include_once(__DIR__.'/../Lib/class-ws-v1.php');

	##########################################################################################################  
	# CRIA SESSÃO
	##########################################################################################################  
		$session = new session();

	##########################################################################################################  
	# Limpa as informações em cache sobre arquivos
	##########################################################################################################
		clearstatcache();
	##########################################################################################################  
	# ERROS DE UPLOAD (inativo temporariamente)
	##########################################################################################################			
		$errorUpload 	= Array();
		$errorUpload[1] = 'UPLOAD_ERR_INI_SIZE: The uploaded file exceeds the upload_max_filesize directive in php.ini.';
		$errorUpload[2] = 'UPLOAD_ERR_FORM_SIZE: The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.';
		$errorUpload[3] = 'UPLOAD_ERR_PARTIAL: The uploaded file was only partially uploaded.';
		$errorUpload[4] = 'UPLOAD_ERR_NO_FILE: No file was uploaded.';
		$errorUpload[5] = 'UPLOAD_ERR_NO_TMP_DIR: Missing a temporary folder. Introduced in PHP 5.0.3.';
		$errorUpload[6] = 'UPLOAD_ERR_CANT_WRITE: Failed to write file to disk. Introduced in PHP 5.1.0.';
		$errorUpload[7] = 'UPLOAD_ERR_EXTENSION: A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help.';
		$errorUpload[8] = 'UPLOAD_ERR_NO_FILES: No attachments are uploaded.';
	##########################################################################################################  
	# CASO NÃO EXISTA O DIRETÓRIO PADRÃO PARA UPLOAD, CRIAMOS
	##########################################################################################################			
		if(!file_exists(ROOT_WEBSITE.'/assets')){				mkdir(ROOT_WEBSITE.'/assets');				}		
		if(!file_exists(ROOT_WEBSITE.'/assets/upload-files')){	mkdir(ROOT_WEBSITE.'/assets/upload-files');	}		
		define("UPLOAD_DIR",ROOT_WEBSITE.'/assets/upload-files');
	##########################################################################################################  
	# VERIFICAMOS SE O DIRETÓRIO EXISTE E ESTÁ EM CONDIÇÕES DE RECEBER UPLOADS   
	##########################################################################################################			
		if(is_dir(UPLOAD_DIR) && is_writable(UPLOAD_DIR)) {

			##########################################################################################################  
			# VERIFICAMOS SE EXISTE ARQUIVOS ANEXADOS  
			##########################################################################################################
			if(count($_FILES)>=1){

				##########################################################################################################  
				# MONTAMOS UM ARRAY QUE IRÁ RECEBER TODOS OS DADOS DE RETORNO  
				##########################################################################################################
				 $_RETURN_FILES = Array();

				##########################################################################################################  
				# VARREMOS OS ARQUIVOS  
				##########################################################################################################
				foreach ($_FILES as $key => $__FILE__) {
					##########################################################################################################  
					# CASO SEJA MULTIPLOS UIPLOADS  
					##########################################################################################################
					if(is_array($__FILE__['name'])){
						for ($i=0; $i < count($__FILE__['name']); $i++) { 

							$tmp_name 	= $__FILE__["tmp_name"][$i];
							$size 		= $__FILE__["size"][$i];
							$type		= $__FILE__["type"][$i];
							$nome 		= url_amigavel_filename($__FILE__["name"][$i]);
							$ext		= strtolower(substr($nome,(strripos($nome,'.')+1)));
							$ext		= str_replace(array("jpeg"),array("jpg"),$ext);
							$token 		= md5(uniqid(rand(), true));
							##########################################################################################################  
							# Retorna TRUE se o arquivo com o nome filename foi enviado por POST HTTP  
							# Isto é útil para ter certeza que um usuário malicioso não está tentando levar o script a trabalhar 
							# em arquivos que não deve estar trabalhando --- por exemplo, /etc/passwd.
							##########################################################################################################
							if(is_uploaded_file($__FILE__["tmp_name"][$i])){

								##########################################################################################################  
								# MOVEMOS O ARQUIVO PARA O SERVIDOR
								##########################################################################################################  
							 	if(move_uploaded_file( $tmp_name ,UPLOAD_DIR.'/'.$token.'.'.$ext)){

									##########################################################################################################  
							 		# GUARDAMOS AS VARIÁVEIS DO ARQUIVO UPADO NA ARRAY
									##########################################################################################################  
									$_RETURN_FILES[] = array(
										'status'=>'sucesso',
										'response'=>'Upload efetuado com sucesso!',
										'error'=>0,
										'file'=>array(
											'size'		=>$size,
											'type'		=>$type,
											'name'		=>$nome,
											'newName'	=>$token.'.'.$ext,
											'ext'		=>$ext,
											'token'		=>$token
										)
									);
								 }						
								
							}else{
								$_RETURN_FILES[] = json_encode(array('status'=>'falha','response'=>'Esse arquivo não pode ser upado', 'error'=>'is_uploaded_file','file'=>'null'));
							}
						}
					}else{
					##########################################################################################################  
					# CASO SEJA UM ÚNICO UIPLOAD  
					##########################################################################################################

			        	$tmp_name 	= $__FILE__["tmp_name"];
			        	$size 		= $__FILE__["size"];
			        	$type		= $__FILE__["type"];
						$nome 		= url_amigavel_filename($__FILE__["name"]);
						$ext		= strtolower(substr($nome,(strripos($nome,'.')+1)));
						$ext		= str_replace(array("jpeg"),array("jpg"),$ext);
						$token 		= md5(uniqid(rand(), true));

						##########################################################################################################  
						# "is_uploaded_file" Retorna TRUE se o arquivo com o nome filename foi enviado por POST HTTP  
						# Isto é útil para ter certeza que um usuário malicioso não está tentando levar o script a trabalhar 
						# em arquivos que não deve estar trabalhando --- por exemplo, /etc/passwd.
						##########################################################################################################
						if(is_uploaded_file($tmp_name)){
							if(move_uploaded_file( $tmp_name ,UPLOAD_DIR.'/'.$token.'.'.$ext)){
								##########################################################################################################  
						 		# GUARDAMOS AS VARIÁVEIS DO ARQUIVO UPADO NA ARRAY
								##########################################################################################################
								$_RETURN_FILES[] = array(
									'status'=>'sucesso',
									'response'=>'Upload efetuado com sucesso!',
									'error'=>0,
									'file'=>array(
										'size'		=>$size,
										'type'		=>$type,
										'name'		=>$nome,
										'newName'	=>$token.'.'.$ext,
										'ext'		=>$ext,
										'token'		=>$token
									)
								);
							}
						}else{
							$_RETURN_FILES[] = json_encode(array('status'=>'falha','response'=>'Esse arquivo não pode ser upado', 'error'=>'is_uploaded_file','file'=>'null'));
						}
					}
				}
			}else{
				$_RETURN_FILES[] = json_encode(array('status'=>'falha','response'=>'Não existem arquivos anexados', 'error'=>$errorUpload[8],'file'=>'null'));
			}
		}else{
			$_RETURN_FILES[] = json_encode(array('status'=>'falha','response'=>'Diretório inexistente ou não permite esta ação', 'error'=>'is not writable (is_writable)','file'=>'null'));
		}

/*##########################################################################################################  
*
*
*
*
*
*	DEPOIS DE EFETUADO O UPLOAD DOS ARQUIVOS, VAMOS TRATAR OS DADOS DA BASE
*
*
*
*
*
##########################################################################################################*/

##########################################################################################################
#  FUNÇÃO QUE ADICIONA O ARQUIVO A BIBLIOTECA
##########################################################################################################			
	function AddBiblioteca($FILE,$POST){
		if(empty($POST['token']) 		|| $POST['token']=="")			$POST['token'] 			= _token(PREFIX_TABLES . 'ws_biblioteca', 'token');
		if(empty($POST['token_group']) 	|| $POST['token_group']=="")	$POST['token_group'] 	= _token(PREFIX_TABLES . 'ws_biblioteca', 'token_group');

		$_biblioteca_ = new MySQL();
		$_biblioteca_->set_table(PREFIX_TABLES.'ws_biblioteca');
		$_biblioteca_->set_insert('filename',		$FILE['file']['name']);
		$_biblioteca_->set_insert('file',			$FILE['file']['newName']);
		$_biblioteca_->set_insert('token',			$FILE['file']['token']);
		$_biblioteca_->set_insert('type',			$FILE['file']['type']);
		$_biblioteca_->set_insert('upload_size',	$FILE['file']['size']);
		$_biblioteca_->set_insert('token_group',	$POST['token_group']);
		$_biblioteca_->set_insert('tokenFile',		$POST['token']);
		if(isset($POST['download']) && $POST['download']==1){
			$_biblioteca_->set_insert('download','1'); 
		}
		$_biblioteca_->insert();
	}
##########################################################################################################
#  VARREMOS OS ARQUIVOS ANEXADOS
##########################################################################################################
foreach($_RETURN_FILES AS $FILE){
	#################################################################################################
	# CADA MÓDULO POSSÚI UM CAMPO HIDDEN COM O NAME="TYPE" QUE VAI NOS DIZER QUE MÓDULO ELE PERTENCE
	#  VERIFICAMOS QUAL É O CAMPO E MANIPULAMOS A BASE DE DADOS CONFORME O ITEM INDICADO
	#################################################################################################
    if($_POST['type']=='item_detail_file'){		

		AddBiblioteca($FILE,$_POST);

		#------------------------------------------------------------------------------	
		$Old				= new MySQL();
		$Old->set_table(PREFIX_TABLES.'_model_files');
		$Old->set_where('token="'.$_POST['token'].'" ');
		$Old->set_where('AND painel="1" ');
		$Old->set_where('AND ws_draft="1"');
		$Old->set_where('AND id_item="'.$_POST['id_item'].'" ');
		$Old->set_where('AND ws_id_draft="'.$_POST['id_item'].'"');
		$Old->select();

		#------------------------------------------------------------------------------				
		if($Old->_num_rows==0){
			$up					= new MySQL();
			$up->set_table(PREFIX_TABLES.'_model_files');
			$up->set_insert('ws_id_ferramenta' 	,$_POST['ws_id_ferramenta']);
			$up->set_insert('painel'			,'1');
			$up->set_insert('ws_draft'			,'1');
			$up->set_insert('ws_id_draft'		,$_POST['id_item']);
			$up->set_insert('id_item'			,$_POST['id_item']);
			$up->set_insert('token'				,$_POST['token']);
			$up->set_insert('size_file'			,$FILE['file']['size']);
			$up->set_insert('file'				,$FILE['file']['newName']);
			$up->set_insert('filename'			,$FILE['file']['name']);
			if($_POST['download']==1){$up->set_insert('download',1);}
			$up->insert();
		}else{
			$U= new MySQL();
			$U->set_table(PREFIX_TABLES.'_model_files');
			$U->set_where('token			="'.$_POST['token'].'"'); 
			$U->set_where('AND ws_draft 	="1"');
			$U->set_where('AND painel		="1"');
			$U->set_where('AND ws_id_draft	="'.$_POST['id_item'].'"');
			$U->set_where('AND id_item		="'.$_POST['id_item'].'"');
			$U->set_update('file'			,$FILE['file']['newName']);
			$U->set_update('filename'		,$FILE['file']['name']);
			$U->set_update('size_file'		,$FILE['file']['size']);
			if($_POST['download']==1){$U->set_update('download',1); }else{$U->set_update('download',0); }
			$U->salvar();
		}

		##########################################################################################################
		#  VERIFICAMOS SE O ÍTEM TEM UM RASCUINHO
		##########################################################################################################				
		$verify_produto= new MySQL();
		$verify_produto->set_table(PREFIX_TABLES.'_model_item');
		$verify_produto->set_where('ws_draft="1"');
		$verify_produto->set_where('AND ws_id_draft="'.$_POST['id_item'].'"');
		$verify_produto->select();

		##########################################################################################################
		#  CASO NÃO TENHA, CRIA UM 
		##########################################################################################################
		if($verify_produto->_num_rows < 1) { criaRascunho($_POST['ws_id_ferramenta'],$_POST['id_item']);}

		##########################################################################################################
		#  SALVA NA BASE
		##########################################################################################################
		$U= new MySQL();
		$U->set_table(PREFIX_TABLES.'_model_item');
		$U->set_where('ws_draft="1"');
		$U->set_where('AND ws_id_draft="'.$_POST['id_item'].'"');
		$U->set_update($_POST['mysql'],$FILE['file']['newName']);
		$U->salvar();
		echo json_encode(array('status'=>'sucesso','original'=>$FILE['file']['name'], 'filename'=>$FILE['file']['newName']));

		exit;
	}elseif($_POST['type']=='item_detail_thumbnail'){	

		AddBiblioteca($FILE,$_POST);
		criaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item']);
		$U = new MySQL();
		$U->set_table(PREFIX_TABLES . '_model_item');
		$U->set_where('ws_draft="1"');
		$U->set_where('AND ws_id_draft="' . $_POST['id_item'] . '"');
		$U->set_update($_POST['mysql'], 	$FILE['file']['newName']);
		$U->salvar();
		$U = new MySQL();
		$U->set_table(PREFIX_TABLES .'_model_img');
		$U->set_where('token="' 	.$_POST['token'] . '"');
		$U->set_update('filename',	$FILE['file']['newName']);
		$U->set_update('painel', '1');
		$U->salvar();
		##################################################################################################
		echo json_encode(array(
				'nome' => $FILE['file']['newName'],
				'thumb' => '/ws-img/' . $_POST['newwidth'] . '/' . $_POST['newheight'] . '/100/' . $FILE['file']['newName']
		));
	}elseif($_POST['type']=='avatar_galeria'){			

		#######################################################################################
		# SELECIONA O AVATAR ATUAL
		#######################################################################################
			$Old				= new MySQL();
			$Old->set_table(PREFIX_TABLES.'_model_img_gal');
			$Old->set_where('id_galeria="'.$_POST['id_gal'].'"');
			$Old->set_where('AND id_item="'.$_POST['id_item'].'"');
			$Old->set_where('AND avatar="1"');
			$Old->select();

		#######################################################################################
		# CASO JÁ TENHA UM AVATAR RETIRA O REGISTRO "AVATAR" DEIXANDO COMO 0
		#######################################################################################
			$Old				= new MySQL();
			$Old->set_table(PREFIX_TABLES.'_model_img_gal');
			$Old->set_where('id_galeria="'.$_POST['id_gal'].'"');
			$Old->set_where('AND avatar="1"');
			$Old->set_update('avatar','0');
			$Old->salvar();

		#######################################################################################
		# E ADICIONAMOS OUTRO COM O PARAMETRO "AVATAR" = 1
		#######################################################################################
			$_insert_tab_img_					= new MySQL();
			$_insert_tab_img_->set_table(PREFIX_TABLES.'_model_img_gal');
			$_insert_tab_img_->set_insert('ws_id_ferramenta' ,	$_POST['ws_id_ferramenta']);
			$_insert_tab_img_->set_insert('id_galeria',			$_POST['id_gal']);
			$_insert_tab_img_->set_insert('id_item',			$_POST['id_item']);
			$_insert_tab_img_->set_insert('imagem',				$FILE['file']['newName']);
			$_insert_tab_img_->set_insert('filename',			$FILE['file']['name']);
			$_insert_tab_img_->set_insert('token',				$FILE['file']['token']);
			$_insert_tab_img_->set_insert('avatar','1');
			$_insert_tab_img_->insert();

		#######################################################################################
		# ATUALIZA O REGISTRO DO AVATAR NA GALERIA
		#######################################################################################
			$_update_gal_					= new MySQL();
			$_update_gal_->set_table(PREFIX_TABLES.'_model_gal');
			$_update_gal_->set_where('id="'.$_POST['id_gal'].'"');
			$_update_gal_->set_update('avatar',$FILE['file']['newName']);
			$_update_gal_->salvar();

		#######################################################################################
		# RETORNA THUMBNAIL
		#######################################################################################
			echo '/ws-img/155/128/100/'.$FILE['file']['newName'];
			exit;
	}elseif($_POST['type']=='img_galeria'){				

		#################################################################################################
		# ADICIONA A BIBLIOTECA
		#################################################################################################
		AddBiblioteca($FILE,$_POST);
		$up					= new MySQL();
		$up->set_table(PREFIX_TABLES.'_model_img_gal');
		$up->set_insert('ws_draft'				,'1');
		$up->set_insert('titulo'				,'');
		$up->set_insert('texto'					,'');
		$up->set_insert('file'					,$FILE['file']['newName']);
		$up->set_insert('filename'				,$FILE['file']['name']);
		$up->set_insert('token'					,$FILE['file']['token']);
		$up->set_insert('ws_id_ferramenta' 		,$_POST['ws_id_ferramenta']);
		$up->set_insert('ws_id_draft'			,$_POST['id_item']);
		$up->set_insert('id_galeria'			,$_POST['id_galeria']);
		$up->set_insert('id_item'				,$_POST['id_item']);
		$up->insert();

		#################################################################################################
		# SELECIONAMOS O ARQUIVO PARA PEGAR OS ID's
		#################################################################################################
		$get_ID					= new MySQL();
		$get_ID->set_table(PREFIX_TABLES.'_model_img_gal');
		$get_ID->set_where('file="'.$FILE['file']['newName'].'"');
		$get_ID->select();

		#################################################################################################
		# RETORNA A STRING DA GALERIA
		#################################################################################################
		echo "<li id='".$get_ID->fetch_array[0]['id']."'>	
				<div id='combo'>
					<div id='detalhes_img' class='bg02'>
					<spam ><img class='editar' 	legenda='Editar Informações'	src='./App/Templates/img/websheep/layer--pencil.png'></spam>   
					<spam ><img class='excluir'	legenda='Excluir Imagem'		src='./App/Templates/img/websheep/cross-button.png'></spam>   
				</div>
					<img class='thumb_exibicao' src='/ws-img/155/155/100/".$FILE['file']['newName']."'>
				</div>
			</li>";
	}elseif($_POST['type']=='ckEditor'){				
			AddBiblioteca($FILE,$_POST);
			echo '/ws-img/0/0/100/'.$FILE['file']['newName'];
	}elseif($_POST['type']=='leadCapture'){				

			AddBiblioteca($FILE,$_POST);
			$U = new MySQL();
			$U->set_table(PREFIX_TABLES . 'ws_list_leads');
			$U->set_where('token="' . $_POST['token'] . '"');
			if ($_POST['datatype'] == 'topo_email') {
					$U->set_update('header_email', $FILE['file']['newName']);
			} elseif ($_POST['datatype'] == 'footer_email') {
					$U->set_update('footer_email', $FILE['file']['newName']);
			}
			$U->salvar();
			echo json_encode(array(
					'nome' => $FILE['file']['newName'],
					'thumb' => '/ws-img/'.$_POST['width'].'/'.$_POST['height'].'/100/'.$FILE['file']['newName']
			));
	}elseif($_POST['type']=='avatar_categoria'){		

				AddBiblioteca($FILE,$_POST);

				$Old				= new MySQL();
				$Old->set_table(PREFIX_TABLES.'_model_img');
				$Old->set_where('id_cat="'.$_POST['id'].'"');
				$Old->set_where('AND avatar="1"');
				$Old->exclui();

				$up					= new MySQL();
				$up->set_table(PREFIX_TABLES.'_model_img');
				$up->set_insert('ws_id_ferramenta' ,$_POST['ws_id_ferramenta']);
				$up->set_insert('avatar',			'1');
				$up->set_insert('imagem'			,$FILE['file']['newName']);
				$up->set_insert('id_cat'			,$_POST['id']);
				$up->set_insert('filename'			,$FILE['file']['name']);
				$up->set_insert('token'				,$FILE['file']['token']);
				$up->insert();


				$up					= new MySQL();
				$up->set_table(PREFIX_TABLES.'_model_cat');
				$up->set_where('id="'.$_POST['id'].'"');
				$up->set_update('avatar',$FILE['file']['newName']);
				$up->salvar();
				echo '/ws-img/281/0/100/'.$FILE['file']['newName'];
	}elseif($_POST['type']=='lista_arquivos_item'){		
		
			AddBiblioteca($FILE,$_POST);
			$up					= new MySQL();
			$up->set_table(PREFIX_TABLES.'_model_files');
			$up->set_insert('ws_id_ferramenta' ,$_POST['ws_id_ferramenta']);
			$up->set_insert('id_item',$_POST['id_item']);
			$up->set_insert('file',$FILE['file']['newName']);
			$up->set_insert('filename',$FILE['file']['name']);
			$up->set_insert('ws_draft',"1");
			$up->set_insert('ws_id_draft',$_POST['id_item']);			
			$up->set_insert('token',$token);
			$up->set_insert('size_file',$FILE['file']['size']);
			$up->insert();


	}elseif($_POST['type']=='img_inner_item'){		
			criaRascunho($_POST['ws_id_ferramenta'], $_POST['id_item'],true);	
			AddBiblioteca($FILE,$_POST);
			$_insert_tab_img_					= new MySQL();
			$_insert_tab_img_->set_table(PREFIX_TABLES.'_model_img');
			$_insert_tab_img_->set_insert('ws_id_ferramenta' ,	$_POST['ws_id_ferramenta']);
			$_insert_tab_img_->set_insert('id_item',			$_POST['id_item']);
			$_insert_tab_img_->set_insert('imagem',				$FILE['file']['newName']);
			$_insert_tab_img_->set_insert('filename',			$FILE['file']['name']);
			$_insert_tab_img_->set_insert('token',				$FILE['file']['token']);
			$_insert_tab_img_->set_insert('ws_draft','1');
			$_insert_tab_img_->set_insert('ws_id_draft',$_POST['id_item']);
			$_insert_tab_img_->insert();
			$file					= new MySQL();
			$file->set_table(PREFIX_TABLES.'_model_img');
			$file->set_where('imagem="'.$FILE['file']['newName'].'"');
			$file->select();
			echo "<li id='".$file->fetch_array[0]['id']."'>	
					<div id='combo'>
						<div id='detalhes_img' class='bg02'><spam class='editar' legenda='Editar Informações'>✎</spam>   <spam class='excluir' legenda='Excluir Imagem'>✖</spam></div>
						<img src='/ws-img/155/155/".$FILE['file']['newName']."'>
					</div>
				</li>";
	}elseif($_POST['type']=='splashScreen'){			
		AddBiblioteca($FILE,$_POST);
		$U				= new MySQL();
		$U->set_table(PREFIX_TABLES.'setupdata');
		$U->set_where('id="1"');
		$U->set_update('splash_img',$FILE['file']['newName']);
		$U->salvar();
		echo json_encode(array(
            'nome' =>  $FILE['file']['name'],
            'thumb' => $FILE['file']['newName']
        ));
	}elseif($_POST['type']==''){	
	}elseif($_POST['type']==''){		
	}elseif($_POST['type']==''){		
	}elseif($_POST['type']==''){}
}