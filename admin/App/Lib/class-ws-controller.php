<?
class controller{
	static function GetDebugError($dados,$plus="") {return ("<br>".$plus."<br><br><hr style='border-bottom: dashed 1px;'><br><b>Arquivo:</b>".@$dados[0]['file'].'<br><b>Linha</b>: '.$dados[0]['line'].' <br><b>Função:</b>'.$dados[0]['class'].$dados[0]['type'].$dados[0]['function'].'("'.implode($dados[0]['args'],',').'")<br><br><hr style="border-bottom: dashed 1px;"><br>Consulte:	controller::help();<br><hr>' );}
	protected static function _erro($error){echo '<pre style="position: relative;color: #F00;background-color: #FFCB00;font-weight: bold;padding: 10px;">! -- Erro interno da Classe -- ! '.PHP_EOL.$error.PHP_EOL."</pre>";}
	public function __construct(){
		$this->createCache 	= 1;
		$this->init 		= array("inicio","");
		$this->paths 		= array();
		$this->erro404 		= null;
		$this->checkURL		= 0;
		$this->fileInclude	= null;
		$this->files 		= array();
		$this->URI 			= "";
		$this->urlsIncludes	= array();
		$this->ignoreAdd	= false;
		$this->nocache		= false;
		$this->Root 		= ROOT_WEBSITE;
	}

	public function returnRealPath(){
		self::get_URI();
		$setupdata = new MySQL();
		$setupdata->set_table(PREFIX_TABLES.'setupdata');
		$setupdata->select();
		$ws_pages = new MySQL();
		$ws_pages->set_table(PREFIX_TABLES.'ws_pages');
		$ws_pages->set_where('type="path"');
		$ws_pages->select();
		$this->init = $setupdata->fetch_array[0]['url_initPath'];
		foreach($ws_pages->fetch_array as $path){ array_push($this->paths,$path['path']);}
		$i 		= 0;
		$lister = 1;
		// primeiro verifica se esta vazia 
		if(ws::urlPath()==""){
			//agora verifica se existe caminho default pra ele
				if(!in_array($this->init, $this->paths)){
					self::_erro('Por favor, o caminho Default nao está definido ou não existe no sistema.');
				}else{
					//se tiver caminho default, faz um foreach nos paths
					foreach($this->paths as $path){
						if($path==$this->init){return  $path; break;};
						$i++;
					}				
				}
			// caso nao esteja vaziu
		}else{
			// foreach nas urls setadas na classe
			foreach($this->paths as $path){
				$url 			= $path;
				$urlTratada 	= $url;
				$urlExplode 	= explode('/',$path);
				$uriExplode 	= explode('/',$this->URI);
				$a 				= 0;
				foreach ($urlExplode as $pasta){ 
					if(substr(@$urlExplode[$a],0,1)=='^' && substr(@$urlExplode[$a],1)!= @$uriExplode[$a]){
						$urlTratada 	= explode("/",$urlTratada);
						$urlTratada[$a] = @$uriExplode[$a];
						$urlTratada 	= implode($urlTratada,"/");
					}elseif(stripos($pasta,'|')){
						if(in_array(@$uriExplode[$a],explode('|',$pasta))){
							$urlTratada = str_replace($pasta, @$uriExplode[$a], $urlTratada);
						}else{
							$lister=0;
						}
					}elseif($pasta=='*'){
						$urlTratada = explode("/",$urlTratada);
						$urlTratada[$a] = @$uriExplode[$a];
						$urlTratada = implode($urlTratada,"/");
					}else{
						if($pasta!=@$uriExplode[$a]){$lister=0;}
					}
					$a++;
				};
				if($urlTratada==$this->URI){
					return  $path; break;
				}else{
					$i++;
				}
			};
		}
	}
	public function verifyURL(){
		$i 		= 0;
		$lister = 1;
		// primeiro verifica se esta vazia 
		if(ws::urlPath()==""){

			//agora verifica se existe caminho default pra ele
			if(!in_array($this->init, $this->paths)){
				self::_erro('Por favor, o caminho Default nao está definido ou não existe no sistema.');
			}else{
				//se tiver caminho default, faz um foreach nos paths
				foreach($this->paths as $path){
					if($path==$this->init){
						//se achar dá include e BOA!
						ws::wsInclude($this->Root.'/'.$this->files[$i]);
						break;
					};
					$i++;
				}				
			}
		// caso nao esteja vaziu
		}else{
			// foreach nas urls setadas na classe
			foreach($this->paths as $path){
					$url 			= $path;
					$urlTratada 	= $url;
					$urlExplode 	= explode('/',$path);
					$uriExplode 	= explode('/',$this->URI);
					$a 				= 0;
					foreach ($urlExplode as $pasta){ 
						if(substr(@$urlExplode[$a],0,1)=='^' && substr(@$urlExplode[$a],1)!= @$uriExplode[$a]){
							$urlTratada 	= explode("/",$urlTratada);
							$urlTratada[$a] = @$uriExplode[$a];
							$urlTratada 	= implode($urlTratada,"/");
						}elseif(stripos($pasta,'|')){
							if(in_array(@$uriExplode[$a],explode('|',$pasta))){
								$urlTratada = str_replace($pasta, @$uriExplode[$a], $urlTratada);
							}else{
								$lister=0;
							}
						}elseif($pasta=='*'){
							$urlTratada = explode("/",$urlTratada);
							$urlTratada[$a] = @$uriExplode[$a];
							$urlTratada = implode($urlTratada,"/");
						}else{
							if($pasta!=@$uriExplode[$a]){
								$lister=0;
							}
						}
						$a++;
					};

					if($this->ignoreAdd==1){
						$url_verify = substr($this->URI,0,strlen($urlTratada));
						if($url_verify==$urlTratada){
							$this->urlsIncludes = array_merge($this->urlsIncludes, array($urlTratada=>$this->files[$i]));
							goto processa;
						}
					}else{
						if($urlTratada==$this->URI){
							$this->urlsIncludes = array_merge($this->urlsIncludes, array($urlTratada=>$this->files[$i]));
						}
					}
				processa:
				$i++;
			};


			if(count($this->urlsIncludes)==0){
				ws::wsInclude($this->Root.'/'.$this->erro404);
				$this->createCache 	= 0;
			}else{
				$filePath = end($this->urlsIncludes);
				ws::wsInclude($this->Root.'/'.$filePath);
			}
		}
	}
	public function setRoot($root=null){
		if($root==null){
			self::_erro(self::GetDebugError(debug_backtrace(),'Por favor, o caminho $root nao está definido.'));
		}else{
			if(substr($root,-1)=='/'){$root = substr($root,0,-1);}
			$this->Root=ROOT_WEBSITE.'/'.$root;
		}
	}
	public function ignoreAdd(){ $this->ignoreAdd = true;}
	public function get_URI(){
		if(substr($_SERVER['REQUEST_URI'],0,1)=="/"){
			$this->URI = urldecode(substr($_SERVER['REQUEST_URI'],1));
		}else{
			$this->URI = $_SERVER['REQUEST_URI'];
		}
	}
	public function initPath($path=null){
		if($path=="" || !is_string($path) || $path==null){
			self::_erro(self::GetDebugError(debug_backtrace(),'Por favor, coloque uma URL válida de inicio ->initPath(string)'));
		}else{
			$this->init = $path;
		}
	}
	
	public function set404($page=null){
		if($page==null){self::_erro(self::GetDebugError(debug_backtrace(),'Por favor, coloque uma URL válida de como erro404'));}
		if(!file_exists($this->Root.'/'.$page)){
			self::_erro(self::GetDebugError(debug_backtrace(),'Este arquivo não existe: '.$this->Root.'/'.$page));
		}
		$this->erro404=$page;
	}
	public function returnInc(){
		return count($this->urlsIncludes);
	}
	public function error404(){
		if($this->erro404==null){
			self::_erro(self::GetDebugError(debug_backtrace(),'Por favor, coloque uma URL válida de como erro404'));
		}else{
			ws::wsInclude($this->Root.'/'.$this->erro404);
			$this->createCache 	= 0;

		}
	}
	public function trataURL(){
		$REQUEST_URL 	= 	explode('?',$this->URI);
		if(isset($REQUEST_URL[1])){
			$urlGet	=	''.$REQUEST_URL[1];
			parse_str($urlGet, $parse);
			array_merge($_GET,$parse);
			if(substr($REQUEST_URL[0],-1)=='/'){
				header("Location:/".substr($REQUEST_URL[0],0,-1).'?'.$REQUEST_URL[1]);
			}
		}else{
			if(substr($REQUEST_URL[0],-1)=='/'){
				header("Location:/".substr($this->URI,0,-1));
			}
		}
		$this->URI = urldecode($REQUEST_URL[0]);
	}

	static function getPath($node=null){
		if(is_string($node)){		self::_erro(self::GetDebugError(debug_backtrace(),"Erro: Isso não é um número ->	self::getPath('".$node."')"));}
		elseif($node==null && $node==0){
			if(substr( $_SERVER['REQUEST_URI'],0,1)=='/') $_SERVER['REQUEST_URI']=substr($_SERVER['REQUEST_URI'],1,strlen($_SERVER['REQUEST_URI']));
			$REQUEST_URL 	= 	explode('?',$_SERVER['REQUEST_URI']);
			$url 			=	$REQUEST_URL[0];
			return $url;
			exit;
		}else{
			$REQUEST_URL	= 	substr( $_SERVER['REQUEST_URI'], strlen('/'));
			$REQUEST_URL 	= 	explode('?',$REQUEST_URL);
			$url 			=	$REQUEST_URL[0];
			$GET=explode( '/', $url);
			if($node>count($GET)){
				self::_erro(self::GetDebugError(debug_backtrace(),"Erro: Não existe path nesta posição ->	self::getPath(".$node.")"));
			}else{
				return $GET[($node-1)];
			};
		}
	} 

	public function includeFile($file=null,$first=""){
		if(substr($file,0,1)=='/'){$file = substr($file,1);}
		if($file==null || $file==""){
			self::_erro(self::GetDebugError(debug_backtrace(),"Erro: Por favor, insira o nome de um arquivo -> ".$file));
		}elseif(!file_exists(ROOT_WEBSITE.'/'.$file)){
			self::_erro(self::GetDebugError(debug_backtrace(),"Erro: 1 Este arquivo não existe -> ".$file));
		}elseif(is_string($file)){
			ws::wsInclude(ROOT_WEBSITE.'/'.$file);
		}
	}

	public function setPath($file=null,$path=null){
		if($file==null || $file==""){
			self::_erro(self::GetDebugError(debug_backtrace(),"Erro: Por favor, insira o nome de um arquivo"));
		}elseif(!file_exists($this->Root.'/'.$file)){
			self::_erro(self::GetDebugError(debug_backtrace(),"Erro: Este arquivo não existe -> ".$this->Root.'/'.$file));
		}elseif(is_string($path)){
			array_push($this->paths,$path);
			array_push($this->files,$file);
		}else{
			self::_erro(controller::GetDebugError(debug_backtrace(),"Erro: Por favor, apenas strings -> ".$path));
		}
	}
	public function go(){
		$this->get_URI();
		$this->verifyURL();
		$this->trataURL();
	}
	
}


	// // 	Inicia a classe
	// $controller = new controller();
	// // 	includeFile dá um include direto, sem passar pelo processo de seleção
	// $controller->includeFile("header.php");
	// //	Dizemos o diretorio raiz que devemos buscar
	// $controller->setRoot(__DIR__);
	// // caso não exista a URL puxa o arquivo setado (ñ é obrigatorio)
	// $controller->set404("erro404.php");
	// // caso nao tenha URL nenhuma direciona para setada
	// $controller->initPath("inicio");
	// // 1° arquivo que deverá ser incluido
	// // 2° PATH da url, trabalha com variáveis ex: (a|b)  quer dizer q é "a" OU "b"
	// $controller->setPath("arquivo.php","pasta1/pasta2/pasta3/pasta4");

	// // Quando o path tiver o símbolo de * qualquer valor setado será válido
	// $controller->setPath("arquivo.php","pasta1/*/pasta3");

	// // Quando o path tiver o símbolo de ^ o valor precisa ser diferente do que está setado
	// $controller->setPath("arquivo.php","pasta1/^pasta2/pasta3");

	// //Caso tenha paths adicionais sobrando no final da URL, puxa da mesma forma o arquivo
	// // Por exemplo: pasta1/pasta2/pasta3/blablabla 
	// // ele levara em conta apenas o pasta1/pasta2/pasta3
	// // se nao estiver habilitado, só puxará o arquivo caso a URL seja identica
	// $controller->ignoreAdd();
	// // inicia o processo
	// $controller->go();
	// // inclui mais um arquivo  
	// $controller->includeFile("footer.php");


?>