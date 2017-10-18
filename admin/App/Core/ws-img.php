<?
	set_time_limit(0); 
	################################################################################
	# IMPORTAMOS A CLASSE THUMB CANVAS
	################################################################################
	ob_start();
	include_once(__DIR__.'/../Lib/class-canvas.class.php');
	ob_clean();
	################################################################################
	# FUNÇÃO PARA MANIPULAR A URL
	################################################################################
	function urlPath($node = null, $debug = true) {
		if (substr($_SERVER['REQUEST_URI'], 0, 1) == '/')
			$_SERVER['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'], 1, strlen($_SERVER['REQUEST_URI']));
		if (is_string($node)) {
			echo "Erro: Isso não é um número ->	urlPath('" . $node . "')";
			exit;
		} elseif ($node == null && $node == 0) {
			$REQUEST_URL = explode('?', $_SERVER['REQUEST_URI']);
			$url         = $REQUEST_URL[0];
			return $url;
			exit;
		} else {
			$REQUEST_URL = explode('?', $_SERVER['REQUEST_URI']);
			$url         = $REQUEST_URL[0];
			if (substr($url, -1) == '/') {
				$url = substr($url, 0, -1);
			}
			$GET = explode('/', $url);
			if ($node > count($GET)) {
				if ($debug == true) {
					echo "Erro: Não existe path nesta posição ->	urlPath(" . $node . ")";
					exit;
				} else {
					return false;
				}
			} else {
				return $GET[($node - 1)];
			}
		}
	}
	
	
	################################################################################
	# EXPLODIMOS A URL DA IMAGEM
	################################################################################
	$URL = explode('/', urlPath());
	
	################################################################################
	# AGORA TRATAMOS A ARRAY COM OS DADOS NECESSÁRIOS
	################################################################################
	if (count($URL) >= 5) {
		$vars['largura'] = urlPath(2, false);
		$vars['altura']  = urlPath(3, false);
		$vars['q']       = urlPath(4, false);
		$vars['imagem']  = urlPath(5, false);
	} elseif (count($URL) == 4) {
		$vars['largura'] = urlPath(2, false);
		$vars['altura']  = urlPath(3, false);
		if (is_numeric(urlPath(4, false))) {
			$vars['q']      = urlPath(4, false);
			$vars['imagem'] = null;
		} else {
			$vars['q']      = null;
			$vars['imagem'] = urlPath(4, false);
		}
	} elseif (count($URL) == 3) {
		$vars['q']       = null;
		$vars['largura'] = urlPath(2, false);
		if (is_numeric(urlPath(3, false))) {
			$vars['altura'] = urlPath(3, false);
			$vars['imagem'] = null;
		} else {
			$vars['altura'] = 0;
			$vars['imagem'] = urlPath(3, false);
		}
	} elseif (count($URL) == 2) {
		if (is_numeric(urlPath(2, false))) {
			$vars['largura'] = urlPath(2, false);
			$vars['altura']  = 0;
			$vars['imagem']  = null;
		} else {
			$vars['imagem']  = urlPath(2, false);
			$vars['altura']  = 0;
			$vars['largura'] = 0;
		}
	} else {
		$vars['imagem']  = null;
		$vars['altura']  = 0;
		$vars['largura'] = 0;
		$vars['q']       = null;
	}
	
	################################################################################
	# RETIRAMOS O @2X PARA NÃO DAR CONFLITO COM TEMPLATES RESPONSIVOS
	################################################################################
	$vars['imagem'] = str_replace("@2x", "", $vars['imagem']);
	
	################################################################################
	# DEFINE O PATH QUE IRÁ BUSCAR AS IMAGENS
	################################################################################
	$pathUpload = $_SERVER["DOCUMENT_ROOT"] . '/website/assets/upload-files/';
	
	################################################################################
	# CASO NÃO EXISTA A IMAGEM, SUBSTITUIMOS PELA IMAGEM PADRÃO DO SISTEMA
	################################################################################
	if ($vars['imagem'] == null || !file_exists($pathUpload . $vars['imagem'])) {
		$OriginalExists=false;
		$vars['imagem'] = $_SERVER["DOCUMENT_ROOT"] . '/admin/App/Templates/img/websheep/no-img.png';
	} else {
		$OriginalExists=true;
		$vars['imagem'] = $pathUpload . $vars['imagem'];
	}
	
	################################################################################
	# DEFINE O TAMANHO DA IMAGEM
	################################################################################
	$filesize = getimagesize($vars['imagem']);
	if ($vars['altura'] == 0 && $vars['largura'] == 0) {
		$vars['largura'] = $filesize[0];
		$vars['altura']  = $filesize[1];
	}
	
	################################################################################
	# VERIFICA A EXTENÇÃO DO ARQUIVO
	################################################################################
	$extencao = substr($vars['imagem'], -3);
	
	################################################################################
	# DEFINE A QUALIDADE DA IMAGEM
	################################################################################	
	if ($extencao == 'jpg') {
		if (empty($vars['q'])) {
			$vars['q'] = '100';
		}
	}
	if ($extencao == 'png') {
		if (empty($vars['q'])) {
			$vars['q'] = '9';
		}
	}
	if ($extencao == 'gif') {
		if (empty($vars['q'])) {
			$vars['q'] = '100';
		}
	}
	
	################################################################################
	# CAMINHO ROOT DO ARQUIVO TRATADO
	################################################################################

	$_root = $_SERVER["DOCUMENT_ROOT"] . '/website/';
	$local = $_root.'/assets/upload-files/thumbnail';


	if(!file_exists($local)){@mkdir($local);}
	$newName = $local.'/'.$vars['largura'] . '-' . $vars['altura'] . '-' . $vars['q'] . '-' . basename($vars['imagem']);

	################################################################################
	# VERIFICA SE A THUMB NÃO EXISTE  
	################################################################################
	if (!file_exists($newName)) {
		
		################################################################################
		# CRIA THUMB
		################################################################################
		if($OriginalExists==false){
			################################################################################
			# EXIBE A THUMB PADRÃO DO SISTEMA (SEM GRAVAR)
			################################################################################

				###########################################################################################
				# CASO QUEIRA DEIXAR PADRÃO A IMAGEM, DESCOMENTE ESSA LINHA E COMENTE AS PRÓXIMAS 2 LINHAS
				###########################################################################################
				$img = new canvas();
				$img->carrega($vars['imagem'])->redimensiona($vars['largura'], $vars['altura'], 'crop')->exibe($vars['imagem']);exit;
				//header('Content-type: image/jpeg');
   				//readfile('http://placehold.it/'.$vars['largura'].'x'.$vars['altura']);
		}else{
			$img = new canvas();
			 if ($img->carrega($vars['imagem'])->redimensiona($vars['largura'], $vars['altura'], 'crop')->grava($newName, $vars['q'])) {
			 	################################################################################
			 	# MONTA O HEADER
			 	################################################################################
					 if ($extencao == 'jpg') {
					 	header("Content-type: image/jpeg");
					 } elseif ($extencao == 'png') {
					 	header("Content-type: image/png");
					 } elseif ($extencao == 'gif') {
					 	header("Content-type: image/gif");
					 }
				################################################################################
				# RETTORNA A IMAGEM
				################################################################################
				 	$handle = fopen($newName, "rb");
					echo stream_get_contents($handle);
					fclose($handle);
			 }
		}
	} else {
		################################################################################
		# MONTAMOS O HEADER
		################################################################################
		 if ($extencao == 'jpg') {
		 	header("Content-type: image/jpeg");
		 } elseif ($extencao == 'png') {
		 	header("Content-type: image/png");
		 } elseif ($extencao == 'gif') {
		 	header("Content-type: image/gif");
		 }
		################################################################################
		# RETTORNA A IMAGEM
		################################################################################
		 	$handle = fopen($newName, "rb");
			echo stream_get_contents($handle);
			fclose($handle);
		################################################################################
	}
	exit(0);