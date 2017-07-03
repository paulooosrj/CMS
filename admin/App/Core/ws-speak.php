<?

/*
	gerenciar ferramenta
	acessar ferramenta
*/
	########################################################################
	# IMPORTAMAMOS A CLASSE INTERNA 
	########################################################################
	$r = $_SERVER["DOCUMENT_ROOT"];$_SERVER["DOCUMENT_ROOT"] = (substr($r, -1) == '/') ? substr($r, 0, -1) : $r;
	include_once ($_SERVER["DOCUMENT_ROOT"].'/admin/App/Lib/class-ws-v1.php');
	session_name('_WS_');
	session_regenerate_id();
	session_id($_COOKIE['ws_session']);
    session_start();

	########################################################################
	# FUNÇÕES PRE DEFINIDAS 
	########################################################################
	function search_in_Wiki($str){
		$url='http://pt.wikipedia.org/w/api.php?action=query&prop=extracts|info&exintro&titles='.urlencode($str).'&format=json&explaintext&redirects&inprop=url';
		$json = file_get_contents($url);
		$json = json_decode($json, TRUE);
		$result = array();
			foreach($json['query']['pages'] as $page){
				$result["title"]	=  (empty($page['title']))?"Sem titulo..":$page['title'];
				$result["extract"]	= (empty($page['extract']))?"Sem descrição..":$page['extract'];
				$result["fullurl"]	= @$page['fullurl'];
				break;
			};
		 return $result;
	}

	function search_in_google($str){
		$url 	= 'https://www.googleapis.com/customsearch/v1?start=1&alt=json&key=AIzaSyDB2CL5jO7KZ51ibfJ_hPM0uBCI_SsMiJA&cx=017576662512468239146:omuauf_lfve&q='.urlencode($str);
		$url 	= 'https://www.googleapis.com/customsearch/v1?q='.urlencode($str).'&hl=pt&alt=json&key=AIzaSyDB2CL5jO7KZ51ibfJ_hPM0uBCI_SsMiJA&cx=007902768968091743701:mbho57c0nru';
		$url 	= file_get_contents($url);
		$result = json_decode($url,TRUE);
		return $result['items'];
	}


	function ordenaArray($a, $b) {
		$cmp = strlen($a) - strlen($b);
		if ($cmp === 0)
			$cmp = strcmp($a, $b);
		return $cmp;
	}

	function prefix_in_array($array, $string) {
			foreach ($array as $key=>$comm) {
				if(substr($string,0,strlen($comm))==$comm){
					return true;
					break;
				}
			}
			return false;
	}
	function get_prefix_in_array($array, $string) {
			foreach ($array as $key=>$comm) {
				if(substr($string,0,strlen($comm))==$comm){
					$req = substr($string,strlen($comm),strlen($string));
					return $req;
					break;
				}
			}
			return false;
	}
	function get_type_prefix_in_array($array, $string) {
			foreach ($array as $key=>$comm) {
				if(substr($string,0,strlen($comm))==$comm){
					$req = substr($string,0,strlen($comm));
					return $req;
					break;
				}
			}
			return false;
	}
	function speak($string,$open='true',$close='true') {
		$open 	= ($open) 	? "true" : "false";
		$close 	= ($close) 	? "true" : "false";
		return 'wsAssistent.speak("'.addslashes($string).'",'.$open.','.$close.');';
	}
	function zeraTudo(){
		$_SESSION['speakDolly'] = null;
	}


	########################################################################
	# DEFINE SE AINDA ESTÁ ESCUTANDO OU NÃO 
	########################################################################
	$_SESSION['speakDolly']['listen']	=(empty($_SESSION['speakDolly']['listen'])) 	? 0 	: $_SESSION['speakDolly']['listen'];
	$_SESSION['speakDolly']['continue']	=(empty($_SESSION['speakDolly']['continue'])) 	? null 	: $_SESSION['speakDolly']['continue'];

	if(isset($_GET['session'])){
		print_r($_SESSION);
		exit;
	}
	$text = $_POST['search'];

	########################################################################
	# SISTEMAS DE BUSCA 
	########################################################################

	$searchSystem = array(
		"wikipedia",
		"google",
		"dicionario",
		);

	########################################################################
	# COMANDOS NATIVOS DA VERSÃO 
	########################################################################
	$basic_commands = (object) Array();
	$basic_commands->commands 	=	Array();
	$basic_commands->commands 	= 	array(
										"logout"=>array(
											"index"			=>array("sair do web chip","sair do websheep","sair","fazer logout","desligar"),
										),
										"corrigir"=>array(
											"index"			=>array("não","cancelar","cancela","corrigindo","parar","pare","tchau"),
										),
										"okDolly"=>array(
											"index"			=>array("bore","bori","dale","dolly","dori","olá dolly","oi dolly","ok dolly","hey dolly","ok dolly","ok italy","ok e dolly" ,"ok idole","ok e dali","ok idade","ok darling","ok dog","ok vale","ok da lei"),
											"response"		=>array("sim?","o que?","olá?","pois não?","estou ouvindo","diga?")
										),
										"search"=>array(
											"index"			=>array("procure por sites","pesquise por sites","procure sites sobre","pesquise sites sobre", "pesquise no google sobre","pesquisa no google sobre","procure no google sobre","pesquise no google","pesquisa no google","procure no google","pesquisa sobre","pesquise sobre","qual é a definição de","qual a definição de","o que é","o que e","o que são","o que sao","o que significa","o que quer dizer","pesquisa para","pesquise","pesquise por","pesquisar","pesquisar por","procure","procure por"),
											"action"		=>'wsAssistent.speak("Aonde procuro?",true,true);'
										),
										"loadToll"=>array(
											"index"			=>array("abrir ferramenta","abra ferramenta","acessar ferramenta","acesse a ferramenta","procure","ache"),
											"action"		=>'wsAssistent.speak("Você quer saber sobre {text}",true,true);'
										)
									);



$listen = $_SESSION['speakDolly']['listen'];

foreach($basic_commands->commands as $key1=>$commands) {
		// ORDENAMOS A ARRAY
		uasort($commands['index'], 'ordenaArray');
		$commands['index'] 	= array_reverse($commands['index']);
		// se estiver cancelado, não faz mais nada.
		if( $key1=="corrigir" &&  in_array($text,$commands['index'])){
			zeraTudo();
			echo speak("Ok, cancelado ",true,true);
			exit;
		}


		if($_SESSION['speakDolly']['continue']!=null){
			// infelizmente a função 'goto' não aceita variáveis
			// então tive que fazer "no braço" mesmo os direcionamentos
			 if($_SESSION['speakDolly']['continue']=='SearchLerOuEscutar'){	goto SearchLerOuEscutar;}
			 if($_SESSION['speakDolly']['continue']=='confirmLogout'){		goto confirmLogout;}
			exit;
		}

		if( $key1=="okDolly" &&  in_array($text,$commands['index']) &&  $listen==0){
			$total = array_rand($commands['response'],1);
			echo speak($commands['response'][$total],true,false);
			$_SESSION['speakDolly']['listen']=1;
			exit;
		}elseif( $key1=="okDolly" &&  in_array($text,$commands['index']) &&  $listen==1){
			echo speak("Já estava te ouvindo.",true,false);
			echo speak("Continue",true,false);
			exit;
		}

		if( $key1=="logout" &&  in_array($text,$commands['index']) &&  $listen==1){
			echo speak("Você quer sair do web chip? Diga sim ou não.",true,false);
			$_SESSION['speakDolly']['continue'] = "confirmLogout";
			exit;
			confirmLogout:
			if($text=="sim"){
				echo speak("Até mais!",false,true);
				echo 'wsAssistent.functions.logout();';
				zeraTudo();
			}else{
				echo speak("ok",false,true);
				zeraTudo();
			}
			exit;
		}


		if( $key1=="search" &&  prefix_in_array($commands['index'],$text) && $listen==1){
			search:
				$palavra = get_prefix_in_array($commands['index'],$text);
				$tipo = get_type_prefix_in_array($commands['index'],$text);
				echo speak("Aguarde ",false,false);
				echo speak("você gostaria de ler, ou escutar a definição de ".$palavra,false,false);
				$_SESSION['speakDolly']['continue'] = "SearchLerOuEscutar";
				$_SESSION['speakDolly']['term'] 	= $palavra;

			exit;
			SearchLerOuEscutar:
				// $result 			= search_in_Wiki($_SESSION['speakDolly']['term']);
				// $extractSpeak 	= str_replace(array(PHP_EOL,"\n","\r","."),"",$result['extract']);
				// $extractText 	= str_replace(array(PHP_EOL,"\n","\r","."),"<br>",addslashes($result['extract']));
				// echo speak("Pessquisa com o seguinte título, ".$result['title'],false,false);
				$termo = trim($_SESSION['speakDolly']['term']);
				$result = search_in_google($termo);
				$modal = '<div style="position: relative; overflow: auto; height: 400px; margin-bottom: -55px; ">';
				foreach ($result as $key => $value) {
					$modal .= '<div><a href="'.$value['link'].'" target="_blank" style="text-decoration: none; ">';
					$modal .='	<div class="w1" style="text-align: left; font-weight: 700; padding: 4px 10px; font-size: 18px; ">'.$value['title'].'</div>';
					$modal .='	<div class="w1" style="text-align: left;font-weight: 300;padding: 4px 10px;font-size: 15px;border-bottom: solid 1px rgba(70, 121, 185, 0.25);">'.$value['snippet'].'</div>';
					$modal .= "</a></div>";
				}
				$modal .= "</div>";
				$modal =  str_replace(array(PHP_EOL,"\n","\r"),"<br>",addslashes($modal));



				 echo speak("Foram encontraos ".count($result)." resultados para ".$termo,false,false);

				echo 'ws.confirm({conteudo:"'.$modal.'"});';
			 	zeraTudo();
				exit;

				 echo speak("Pessquisa com o seguinte título, ".$result['title'],false,false);
				// echo speak($extract,false,false);




/*				$_SESSION['speakDolly']['continue'] = null;
				$_SESSION['speakDolly']['listen']   = 0;

				if(!in_array($engineSearch, $searchSystem)){
					echo speak("Desculpe, não sei ainda fazer pesquisas no ".$engineSearch,true,false);
					$_SESSION['speakDolly']['continue'] = null;
					$_SESSION['speakDolly']['listen']   = 0;
				}else{
					echo speak("ok, procurarei ".$_SESSION['speakDolly']['term']." no ".$engineSearch,true,false);
					$_SESSION['speakDolly']['continue'] = null;
					$_SESSION['speakDolly']['listen']   = 0;
					echo speak("aguarde",true,false);
				}
*/			break;
		}






}

