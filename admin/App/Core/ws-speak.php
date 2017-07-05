<?
	########################################################################
	# IMPORTAMAMOS A CLASSE INTERNA 
	########################################################################
	$r = $_SERVER["DOCUMENT_ROOT"];$_SERVER["DOCUMENT_ROOT"] = (substr($r, -1) == '/') ? substr($r, 0, -1) : $r;
	include_once ($_SERVER["DOCUMENT_ROOT"].'/admin/App/Lib/class-ws-v1.php');
	session_name('_WS_');
	session_regenerate_id();
	session_id($_COOKIE['ws_session']);
    session_start();
	if(isset($_GET['session'])){print_r($_SESSION);	exit;}

	########################################################################
	# FUNÇÕES PRE DEFINIDAS 
	########################################################################
	$artigos = Array("o","ao","aos","à","às","da","das","de","do","dos","a","as","na","nas","os","no","nos","num","nuns","em","numa","numas","um","uns","dum","duns","uma","umas","duma","dumas");


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

	function search_in_google(){
		$url 	= 'https://www.googleapis.com/customsearch/v1?q='.urlencode($_POST['termo']).'&hl=pt&alt=json&key=AIzaSyDB2CL5jO7KZ51ibfJ_hPM0uBCI_SsMiJA&cx=007902768968091743701:mbho57c0nru';
		$url 	= file_get_contents($url);
		$result = json_decode($url,TRUE);
		return $result['items'];
	}

	function speak($string,$open=true,$close=true) {
		$open 	= ($open) 	? "true" : "false";
		$close 	= ($close) 	? "true" : "false";
		return 'wsAssistent.speak("'.addslashes($string).'",'.$open.','.$close.');';
	}
	function zeraTudo(){
		$_SESSION['speakDolly'] = null;
	}
	function meaning(){
		speak("funciona!",true,true);
	}


	########################################################################
	# CASO NÃO SEJA UMA CONVERSA, SEJA APENAS UMA FUNÇÃO A SER EXECUTADA
	########################################################################
	if(isset($_POST['function'])){
		_exec(@$_POST['function']);
		exit;
	}

	########################################################################
	# DEFINE SE AINDA ESTÁ ESCUTANDO OU NÃO 
	########################################################################
	$_SESSION['speakDolly']['listen']	=(empty($_SESSION['speakDolly']['listen'])) 	? null 	: $_SESSION['speakDolly']['listen'];
	$_SESSION['speakDolly']['continue']	=(empty($_SESSION['speakDolly']['continue'])) 	? null 	: $_SESSION['speakDolly']['continue'];


	$search = str_replace(array('.',',',';')," ",$_POST['search']);
	$text 	= explode(" ",$search);


	########################################################################
	# CASO SEJA CONTINUAÇÃO
	########################################################################
	if($_SESSION['speakDolly']['continue']==true){goto continueDolly;}

	########################################################################
	# INICIA A SESSÃO COM A DOLLY
	########################################################################
	$init 		=	array("bore","bori","dale","dolly","dori","dolly","dolly","dolly","dolly","dolly","italy","dolly" ,"idole","dali","idade","darling","dog","vale","lei");
	$response 	=	array("sim?","o que?","olá?","pois não?","estou ouvindo","diga?");

	if(in_array($search,$init) && $_SESSION['speakDolly']['listen']==null){
		$_SESSION['speakDolly']['listen'] = true;
		$total = array_rand($response,1);
		echo speak($response[$total],true,false);
		exit;	
	}


	if(in_array($search,$init) && $_SESSION['speakDolly']['listen']==true){
		echo speak("EU JÁ ESTOU TE ESCUTANDO.",true,false);
		echo speak("CONTINUE.",true,false);
		exit;	
	}


	########################################################################
	# CASO ESTEJA OUVINDO, INICIA A PESQUISA
	########################################################################

	if($_SESSION['speakDolly']['listen']==true){
		$searchDolly= new MySQL();
		$searchDolly->set_table(PREFIX_TABLES.'ws_dolly_fn');
		$wheres = array();
		foreach ($text as $key => $value) {
			if(!in_array($value,$artigos) && $value!=""){
				$wheres[]=("(acao like '%".$value."%' OR local like '%".$value."%')");
			}
		}
		$searchDolly->set_where(implode($wheres,"OR"));
		$searchDolly->select();
		echo "/*".$searchDolly->query."*/".PHP_EOL.PHP_EOL.PHP_EOL;

		/*
			sites sobre*
			Dolly, pesquise para mim sites sobre (sair do sistema)
			Dolly, pesquise para mim sites sobre sair do sistema

		*/

		if($searchDolly->_num_rows==0){
			echo speak("Desculpe, não entendi sua pergunta.",true,false);
		}elseif($searchDolly->_num_rows==1){
			$_SESSION['speakDolly']['mysql'] = $searchDolly->fetch_array[0];
			$confirma = $searchDolly->fetch_array[0]['confirma']; 
			if($confirma!=""){
				echo speak($confirma,true,false);
				$_SESSION['speakDolly']['continue'] = true;
				exit;
				continueDolly:
				if($search=="sim"){
					echo $_SESSION['speakDolly']['mysql']['cod'];
					zeraTudo();
				}elseif($search=="não"){
					echo speak("ok",true,false);
					zeraTudo();
				}else{
					echo speak("Não entendi, diga sim ou não.",true,false);
				}
				exit;
			}else{

				echo "/*";



				###################################### separamos a ação do comando 
				$_LOCAL = array();
				$local 	= explode(" ",$searchDolly->fetch_array[0]['local']); 
				foreach ($text as $itemLocal) {
					 if(in_array(trim($itemLocal),$local)){
					 	$_LOCAL[] = $itemLocal;
					 }
				}



				if(count($_LOCAL)==0){

					$_ACAO = array();
					$acao 	= explode(" ",$searchDolly->fetch_array[0]['acao']); 
					foreach ($text as $itemAcao) {
						 if(in_array(trim($itemAcao),$acao)){
						 	$_ACAO[] = $itemAcao;
						 }
					}

					$newAction = trim(substr(strstr($search,$_ACAO[0]),strlen($_ACAO[0])));


				}else{
					$newAction = trim(substr(strstr($search,$_LOCAL[0]),strlen($_LOCAL[0])));
				}

				echo $newAction;
				// echo $text;
				echo "*/";
				//echo $_SESSION['speakDolly']['mysql']['codigo'];




			}
		}elseif($searchDolly->_num_rows>1){




// ================== TUDO ANTES DO LOCAL DELETA ========= google :   **** *** *** localize        *** *** ***        



				foreach ($searchDolly->fetch_array as $key => $value) {


						//print_r($searchDolly->fetch_array);


				}


				// $fn 					= new MySQL();
				// $fn->set_table(PREFIX_TABLES.'ws_dolly_fn');
				// $fn->set_where('id="'.$searchDolly->fetch_array[0]['id_fn'].'"');
				// $fn->select();
				// foreach ($searchDolly->fetch_array as $value) {
				// 		print_r($value);
				// }






		}
	}


/*




	########################################################################
	# SISTEMAS DE BUSCA 
	########################################################################

	$searchSystem = array("wikipedia", "google", "dicionario");




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




				// $_SESSION['speakDolly']['continue'] = null;
				// $_SESSION['speakDolly']['listen']   = 0;

				// if(!in_array($engineSearch, $searchSystem)){
				// 	echo speak("Desculpe, não sei ainda fazer pesquisas no ".$engineSearch,true,false);
				// 	$_SESSION['speakDolly']['continue'] = null;
				// 	$_SESSION['speakDolly']['listen']   = 0;
				// }else{
				// 	echo speak("ok, procurarei ".$_SESSION['speakDolly']['term']." no ".$engineSearch,true,false);
				// 	$_SESSION['speakDolly']['continue'] = null;
				// 	$_SESSION['speakDolly']['listen']   = 0;
				// 	echo speak("aguarde",true,false);
				// }
			

				break;
		}





}
*/

