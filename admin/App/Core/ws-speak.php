<?
	$text = "pesquise por bolas de gude";//$_POST['search'];
	$basic_commands = (object) Array();
	$basic_commands->commands 	=	Array();
	$basic_commands->commands 	= 	array(
										"teste"=>array(
													"index"		=>array("a a q q","aa","aaaa","aaa","aaaaaaaaa","aaaa"),
													"response"	=>array("sim","o que","olá","pois não","estou ouvindo","diga"),
													"action" 	=>'wsAssistent.speak("Você me cumprimentou com "+e.trim(),true,true);'
												),
										"okDolly"=>array(
													"index"		=>array("dolly","olá dolly","oi dolly","ok dolly","hey dolly","ok dolly","ok italy","ok e dolly" ,"ok idole","ok e dali","ok idade","ok darling","ok dog","ok vale","ok da lei"),
													"response"	=>array("sim","o que","olá","pois não","estou ouvindo","diga"),
													"action" 	=>'wsAssistent.speak("Você me cumprimentou com "+e.trim(),true,true);'
												),
										"search"=>array(
													"index"=>array("pesquise","pesquise por","pesquisar","pesquisar por","procure","ache"),
													"response"=>array("ok, pesquisando","certo, procurando","aguarde um momento"),
													"action"=>'wsAssistent.speak("Você quer saber sobre "+e.trim(),true,true);'
										)
									);

########################################################################
# varre os comandos básicos
########################################################################
function sortByLengthReverse($a, $b){return strlen($b) - strlen($a);}

function mycmp($a, $b) {
    $cmp = strlen($a) - strlen($b);
    if ($cmp === 0)
        $cmp = strcmp($a, $b);
    return $cmp;
}

foreach($basic_commands->commands as $key1=>$commands) {

		uasort($commands['index'], 'mycmp');
		$commands['index'] = array_reverse($commands['index']);

		if(in_array($text,$commands['index'])){
			echo $commands['action'];
			break;
		}else{
			foreach ($commands['index'] as $comm) {

				if(substr($text,0,strlen($comm))==$comm){
					echo $commands['action'];
					break;
				}
			}
		};

}

