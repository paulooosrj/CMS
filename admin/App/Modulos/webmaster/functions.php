<?

include_once($_SERVER['DOCUMENT_ROOT'].'/admin/App/Lib/class-ws-v1.php');
_session();
function InsertPagination(){
	echo '
	<style>
	@media screen and (max-width: 1030px) {
		.comboShortCode label div{    
			width: calc(50% - 20px)!important;
			padding: 10px 0!important;
			margin: 10px 10px!important;
		}
		#shortcodes{
			position: relative;
			margin: 20px 0;
			width: 100%!important;
		}
	}
	@media screen and (min-width: 1031px) {
		.comboShortCode label div{    
			width: calc(33% - 20px)!important;
			padding: 10px 0!important;
			margin: 10px 10px!important;
		}
		#shortcodes{
			position: relative;
			margin: 20px 0;
			width: 100%!important;
		}
	}
	</style>
	<div class="comboShortCode" style="overflow: auto;height: 100%;">
		<form id="formTags" style="width: calc(100% - 10px);left: 0px;position: relative;">
			<div style="font-size: 30px;font-weight: bold;padding-bottom: 12px;">Adicionar paginação</div>
			<div class="descricao">Selecione o que você quer, e uma ferramenta:</div>
			<div class="c"></div>
			<div style="padding: 20px;margin-bottom: -24px;">
				<div style="position: relative;font-size: 20px;margin-top: 20px;font-weight: 700;float: left;text-align: center;width: 100%;" class="w1">Selecione uma ferramenta</div>
				<select id="shortcodes" name="id_tool" style="width:560px;padding: 10px;border: none;color: #3A639A;-moz-border-radius: 7px;-webkit-border-radius: 7px;border-radius: 7px;"><option value="">Selecione uma popção</option>';
						$ws_ferramentas 				= new MySQL();
						$ws_ferramentas->set_table(PREFIX_TABLES.'ws_ferramentas');
						$ws_ferramentas->set_where('App_Type="1"');
						$ws_ferramentas->select();
						foreach ($ws_ferramentas->fetch_array as $tool) {
							echo '<option value="'.$tool['id'].'">'.$tool['_tit_menu_'].'</option>';
						}

				echo '</select>
			</div>
		<div style="padding: 0 20px;margin-bottom: -24px;width: calc(100% - 40px);">
			<div style="position: relative;font-size: 20px;font-weight: 700;" class="w1">O que você quer paginar?</div>
			<div class="c"></div>
			<label>
				<div style="margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 70px;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
					Item: 
					<input name="type" value="item" type="radio"/>
				</div>
			</label>
			<label>
				<div style="margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 50px;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
					Galerias: 
					<input name="type" value="gal" type="radio"/>
				</div>
			</label>
			<label>
				<div style="margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 30px;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
					Img. de galerias: 
					<input name="type" value="img_gal" type="radio"/>
				</div>
			</label>
			<label>
				<div style="margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 57px;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
					Imagens: 
					<input name="type" value="img" type="radio"/>
				</div>
			</label>
			<label>
				<div style="margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 41px;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
					Categorias: 
					<input name="type" value="cat" type="radio"/>
				</div>
			</label>
			<label>
				<div style="margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 52px;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
					Arquivos: 
					<input name="type" value="file" type="radio"/>
				</div>
			</label>
		</div>
			<div class="c"></div>
			<div style="position: relative;font-size: 20px;margin-top: 20px;font-weight: 700;" class="w1">Corpo HTML</div>
			<div id="editorHTML" style="text-align:left;margin-top: 20px;font-size: 15px;text-shadow: none;height: 280px;"></div>
			<textarea name="editorHTML" id="textarea_html" style="display:none">'.rawurlencode("<div class='combo' >
	<div class='primeira'>
		<a href='/?page={{first}}'>Primeira</a>
	</div>
	<div class='anterior'>
		<a href='/?page={{prev}}'>Anterior</a>
	</div>
	{{pages}}
	<div class='primeira'>
		<a href='/?page={{next}}'>Próxima</a>
	</div>
	<div class='anterior'>
		<a href='/?page={{last}}'>Último</a>
	</div>
	</div>").'</textarea>


		<div style="position: relative;font-size: 20px;margin-top: 20px;font-weight: 700;" class="w1">Contador</div>
		<div id="editorCOUNT" style="text-align:left;margin-top: 20px;font-size: 15px;text-shadow: none;height: 180px;"> </div>
		<textarea name="editorCOUNT" style="display:none">'.rawurlencode("<li>
		<a href='/blog/{{i}}'>
			{{i}}
		</a>
	</li>").'</textarea>


		<div style="position: relative;font-size: 20px;margin-top: 20px;font-weight: 700;" class="w1">Contador Ativo (pag. atual)</div>
		<div id="editorCOUNTactive" style="text-align:left;margin-top: 20px;font-size: 15px;text-shadow: none;height: 180px;"></div>
		<textarea name="editorCOUNTactive" style="display:none">'.rawurlencode("<li>
		<div class='active'>
			{{i}}
		</div>
	</li>").'</textarea>
			</form>
		</div>';
	exit;
}

function InsertPaginationCampos(){
	$ws_ferramentas 				= new MySQL();
	$ws_ferramentas->set_table(PREFIX_TABLES.'ws_ferramentas');
	$ws_ferramentas->set_where('id="'.$_REQUEST['id_tool'].'"');
	$ws_ferramentas->select();
	$isso = array('"',"	",PHP_EOL,"\r","\n");
	$porisso = array("'","","","","");
	$output  =  '	<!--'."\n\n";
	$output .=  '		-------------------------------LEGENDA:--------------------------------------'."\n\n";
	$output .=  '		max: Quantos ítens listará por página	'."\n";
	$output .=  '		atual: Qual é a página atual, pode-se usar url:1,url:2,url:3 etc para setar uma variavel do topo ou utilizar a classe ws::urlPath(0)'."\n";
	$output .=  '		html: Código html da paginação'."\n";
	$output .=  '		number: <li> onde ficará o n° de cada pág'."\n";
	$output .=  '		active: Página atual'."\n\n";
	$output .=  '		------------------- OUTRAS TAGS DISPONÍIVEIS PARA SELECT:----------------------------'."\n\n";
	$output .=  '		distinct=""	'."\n";
	$output .=  '		category=""	'."\n";
	$output .=  '		galery=""	'."\n";
	$output .=  '		item="" 	'."\n";
	$output .=  '		where=""	'."\n";
	$output .=  '		innerItem="" '."\n\n";
	$output .=  '	-->'."\n";
	$output .=  '<ws-paginate slug="'.$ws_ferramentas->fetch_array[0]['slug'].'" type="'.$_REQUEST['type'].'" max="5" atual="url:2" ';
	$output .=  'html="'.(str_replace($isso,$porisso,$_REQUEST['editorHTML'])).'" '; 
	$output .=  'number="'.(str_replace($isso,$porisso,$_REQUEST['editorCOUNT'])).'" ';
	$output .=  'active="'.(str_replace($isso,$porisso,$_REQUEST['editorCOUNTactive'])).'">';
	$output .=  '</ws-paginate>'."\n";
	echo ($output);
	exit;
}
function InsertCodeForm(){
	echo '<div class="comboShortCode">
		<form id="formTags" style="height: 200px;">
			<div style="font-size: 20px;font-weight: bold;padding-bottom: 12px;">Adicionar um formulário de cadastro</div>
			<div class="descricao">Selecione qual cadastro será esse formulário:</div>
			<div class="c"></div>
			<div style="padding: 20px;margin-top: 7px;">
				<select id="shortcodes" name="id_tool" style="width:450px;padding: 10px;border: none;color: #3A639A;-moz-border-radius: 7px;-webkit-border-radius: 7px;border-radius: 7px;"><option value="">Selecione uma popção</option>';
					$fullPages 				= new MySQL();
					$fullPages->set_table(PREFIX_TABLES.'ws_list_leads');
					$fullPages->select();
					foreach ($fullPages->fetch_array as $value) {echo '<option value="'.$value['token'].'">'.$value['title'].'</option>'; }
				echo '</select>
				</div>
			<div class="descricao" style="margin-bottom: -20px;">Como você prefere a forma de envio?</div>
			<label>
				<div style="width: 200px;margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 41px;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
					HTML Normal: <input name="typeCode" value="html" type="radio"/>
				</div>
			</label>
			<label>
				<div style="width: 170px;margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 52px;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
					Jquery AJAX: <input name="typeCode" value="ajax" type="radio"/>
				</div>
			</label>


			</form>
		</div>';
	exit;
}
function InsertCodeFormCampos(){

	if($_REQUEST['typeCode']=='html'){
			echo '<form action="/ws-leads/'.strtolower($_REQUEST['id_tool']).'" method="post">'.PHP_EOL;
					$local = new MySQL();
					$local->set_table(PREFIX_TABLES.'wslead_'.strtolower($_REQUEST['id_tool']));
					$local->show_columns();
			echo '		<input type="hidden" name="typeSend" value="html">'.PHP_EOL;
					foreach($local->fetch_array as $coluna){
						if($coluna['Field']!="id"){
							echo '		<input type="text" name="'.$coluna['Field'].'" value="">'.PHP_EOL;
						}
					};
			echo '		<input type="submit" value="Submit">'.PHP_EOL;
			echo '</form>';
			exit;    	
	}elseif($_REQUEST['typeCode']=='ajax'){
			echo '<form id="ws_send">'.PHP_EOL;
					$local = new MySQL();
					$local->set_table(PREFIX_TABLES.'wslead_'.strtolower($_REQUEST['id_tool']));
					$local->show_columns();
					echo '		<input type="hidden" name="typeSend" value="ajax">'.PHP_EOL;
					foreach($local->fetch_array as $coluna){
						if($coluna['Field']!="id"){
							echo '		<input type="text" name="'.$coluna['Field'].'" value="">'.PHP_EOL;
						}
					};
			echo '		<input type="submit" value="Submit">'.PHP_EOL;
			echo '</form>'.PHP_EOL;
			echo '<div id="ws_response"></div>'.PHP_EOL.PHP_EOL;
			echo '<script>'.PHP_EOL;
				echo '	$("#ws_send").submit(function(e){
					e.preventDefault();
					$.ajax({
						type: "POST",
						url:"/ws-leads/'.strtolower($_REQUEST['id_tool']).'",
						data: {form:$("#ws_send").serialize()},
						async: true,
						beforeSend: function(data) {	console.log("beforeSend");	},
						ajaxSend: function(data) {		console.log("ajaxSend");	},
						success: function(data) {		console.log("success");		},
						error: function(data) {			console.log("error");		},
						complete: function(data) {		console.log("complete");	}
					}).done(function(data) {	
						console.log(data);
						$("#ws_response").prepend(data);	
					});
					return false;
				})
			</script>';

			exit;    	
	}
}
function InsertCode(){
	echo '<div class="comboShortCode">
		<form id="formTags" style="height: 313px;">
			<div style="font-size: 20px;font-weight: bold;padding-bottom: 12px;">Adicionar conteúdo</div>
			<div class="descricao">Selecione o que você quer, e uma ferramenta:</div>
			<div class="c"></div>
			<label>
				<div style="margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 70px;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
					Item: 
					<input name="type" value="item" type="radio"/>
				</div>
			</label>
			<label>
				<div style="margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 50px;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
					Galerias: 
					<input name="type" value="gal" type="radio"/>
				</div>
			</label>
			<label>
				<div style="margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 30px;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
					Img. de galerias: 
					<input name="type" value="img_gal" type="radio"/>
				</div>
			</label>
			<label>
				<div style="margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 57px;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
					Imagens: 
					<input name="type" value="img" type="radio"/>
				</div>
			</label>
			<label>
				<div style="margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 41px;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
					Categorias: 
					<input name="type" value="cat" type="radio"/>
				</div>
			</label>
			<label>
				<div style="margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 52px;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
					Arquivos: 
					<input name="type" value="file" type="radio"/>
				</div>
			</label>
			<div class="c"></div>
			<div style="padding: 20px;margin-top: 7px;margin-bottom: -10px;">
				<select id="shortcodes" name="id_tool" style="width:450px;padding: 10px;border: none;color: #3A639A;-moz-border-radius: 7px;-webkit-border-radius: 7px;border-radius: 7px;"><option value="">Selecione uma popção</option>';
					$ws_ferramentas 				= new MySQL();
					$ws_ferramentas->set_table(PREFIX_TABLES.'ws_ferramentas');
					$ws_ferramentas->set_where('App_Type="1"');
					$ws_ferramentas->select();
					foreach ($ws_ferramentas->fetch_array as $tool) {
						echo '<option value="'.$tool['id'].'">'.$tool['_tit_menu_'].'</option>'; 
					}
					$fullPages 				= new MySQL();
					$fullPages->set_table(PREFIX_TABLES.'ws_ferramentas');
					$fullPages->set_where('_plugin_="1"');
					$fullPages->set_order('posicao','ASC');
					$fullPages->select();
					foreach ($fullPages->fetch_array as $value) {echo '<option value="'.$value['id'].'">Plugins -> '.$value['_tit_menu_'].'</option>'; }
					
				echo '</select>
				</div>
			<div class="descricao">Você quer a classe em PHP ou uma TAG HTML5?</div>
			<label>
				<div style="width: 290px;margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 0;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
					Classe PHP: 
					<input name="typeCode" value="classe" type="radio"/>
				</div>
			</label>
			<label>
				<div style="width: 290px;margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 0;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
					Tag HTML5: 
					<input name="typeCode" value="tag" type="radio"/>
				</div>
			</label>
			<!-- EM BREVE -->
			<!--
				<label>
					<div style="width: 200px;margin: 10px;cursor:pointer;position: relative;float: left;padding: 10px 0;background: rgba(255, 255, 255, 0.58);top: 16px;left: 0px;">
						API RESTful: 
						<input name="typeCode" value="rest" type="radio"/>
					</div>
				</label>
			-->

			</form>
		</div>';
	exit;
}

function InsertCodeCampos(){
	$ws_ferramentas 				= new MySQL();
	$ws_ferramentas->set_table(PREFIX_TABLES.'ws_ferramentas');
	$ws_ferramentas->set_where('id="'.$_REQUEST['id_tool'].'"');
	$ws_ferramentas->select();
	$Ferramenta = $ws_ferramentas->fetch_array[0];
	$prefix 	= $Ferramenta['_prefix_'];

	if(isset($_REQUEST['typeCode']) && $_REQUEST['typeCode']=="classe"){
		$output="\n";
		$output.= '################################### CLASSE PHP ###################################'."\n";
		$output.= '	//Caso utilize template, utilize as variáveis da mesma forma que as tag HTML5;'."\n";
		$output.= '	$template="<div>{{coluna}}</div>";'."\n\n";
		$output.= '	// Chamamos a classe WS'."\n";
		$output.= '	$Tool= new WS();'."\n";
		$output.= '	$pesquisa = $Tool->slug("'.$ws_ferramentas->fetch_array[0]['slug'].'")->type("'.$_REQUEST['type'].'")'."\n\n";
		$output.= '##############################################################################'."\n";
		$output.= '//	Aqui são outras variáveis de pesquisa, para utilizar basta descomentar'."\n";
		$output.= '##############################################################################'."\n";
		$output.= '	//->limit()'."\n";
		$output.= '	//->innerCategory(2)'."\n";
		$output.= '	//->innerItem(1)'."\n";
		$output.= '	//->item(1)'."\n";
		$output.= '	//Apenas se tiver um template'."\n";
		$output.= '	//->setTemplate($template)'."\n";
		$output.= '	//->innerGalery()'."\n";
		$output.= '	//->where("")'."\n";
		$output.= '	->go();'."\n";
		$output.= "\n";
		$output.= '################################### RESULTADO ###################################'."\n";
		$output.= '	//A classe retorna um array com todos os dados da pesquisa'."\n";
		$output.= '	//Caso tenha um template cadastrado, não retornará um array, e sim a saída formatada já.'."\n";
		$output.= '	print_r($pesquisa->result);'."\n";
		$output.= '	//Retorno em objeto.'."\n";
		$output.= '	print_r($pesquisa->obj);'."\n";
		$output.= '	//Também a quantidade de resultados'."\n";
		$output.= '	print_r($pesquisa->_num_rows);'."\n";
		$output.= '	//Se qusier, também pode consultar diretamente a base, consultado a saída '."\n";
		$output.= '	print_r($pesquisa->sql);'."\n";
		$output.= '/*################################### LIVE EDITOR ###################################'."\n";
		$output.= '		Agora você pode editar o seu site, dentro do próprio site, '."\n";
		$output.= '		basta inserir da DIV a tag "data-live-editor"'."\n";
		$output.= '		Por exemplo, o campo que você quer trazer é "titulo_blog",'."\n";
		$output.= '		Você deverá inserir em sua div isso: data-live-editor="<?=$pesquisa->obj[0]->titulo_blog_editor?>".'."\n";
		$output.= '		ATENÇÃO, COLOQUE APENAS NA DIV QUE TIVER O CONTEÚDO COMPLETO, POIS O QUE TIVER NA DIV SERÁ SALVO NA BASE DE DADOS.".'."\n";
		$output.= '		Por exemplo, se uma div tiver apenas uma prévia do texto, e você salvar, todo conteudo será trocado pela prévia.'."\n";
		$output.= '*/#################################################################################'."\n";
		$output.= '	//Listando os resultados '."\n";
		$output.= '	foreach($pesquisa->obj as $data){'."\n";
		$fullPages 				= new MySQL();
		$fullPages->set_table(PREFIX_TABLES.'_model_campos');
		$fullPages->set_where('ws_id_ferramenta="'.$_REQUEST['id_tool'].'"');
		$fullPages->set_where('AND coluna_mysql<>""');
		$fullPages->select();


		if(isset($_REQUEST['type']) && $_REQUEST['type']=='item'){
			if($fullPages->_num_rows==0){$output .= '	//Nenhum campos específico adicionado'."\n";}
			foreach ($fullPages->fetch_array as $tool) {

				if($prefix!="" && substr($tool['coluna_mysql'],0,strlen($prefix))==$prefix) {$tool['coluna_mysql'] = substr($tool['coluna_mysql'],strlen($prefix));}

				if( $tool['type']=="playerVideo"){
					$output .=	"		//Function:  url=null,type=player,w=null,h=null\n";
					$output .= '		//Retorno: site,url,title,description,image ou player'."\n";
					$output .= '		//Default: div ou mensagem que retornará caso não tenha URL especificada'."\n";
					$output .= '		//AutoPlay: Habilita o AutoPlay no vídeo. Valores 1 - 0 '."\n";
					$output .= '		echo "<div>".ws::videoData($data->'.$tool['coluna_mysql'].',"Retorno","200","200","Default","AutoPlay")."</div>";'."\n\n\n";
				
					$output .= '		//Caso queira a URL do MP4 diretamente'."\n";
					$output .= '		//Secury=true retorna um link seguro, impossível de salvar '."\n";
					$output .= '		//Secury=false retorna o link do arquivo do MP4 diretamente do youtube ou Vimeo '."\n";
					$output .= '		echo \'<video width="200" height="200" preload="auto" type="video/mp4" src="\'.ws::getVimeoYoutubeDirectLink($data->'.$tool['coluna_mysql'].',Secury).\'" controls="true" poster=""></video>\';'."\n\n";


				}elseif( $tool['type']=="playerMP3"){
					$output .=	"		// Function:  url=null,type=player,w=null,h=null,size,theme \n";
					$output .=	"		// Retorno: 	player, site, title, description, height, width, image \n";
					$output .=	"		// Size: 		widget,classic,minimo,list \n";
					$output .=	"		// Theme: 	light, dark \n";
					$output .=	"		// Auto_play: 	true, false \n";
					$output .=	"		// Default: 	div ou mensagem que retornará caso não tenha URL especificada \n";
					$output .= '		echo "<div>".ws::audioData('.$tool['coluna_mysql'].',"Retorno","w","h","Size","Theme","Auto_play","Default")."</div>";'."\n";

				}elseif( $tool['type']=="thumbmail"){
					$output.= '		//retorno da imagem:  ( path/largura/altura/imagem ) '."\n";
					$output .= "		echo '<img src=\"/ws-img/0/0/'.\$data->".$tool['coluna_mysql'].".'\"/>';\n";
				}else{
					$output .= '		echo \'<div>\'.$data->'.$tool['coluna_mysql'].'.\'</div>\';'."\n";
				}
			}
		}

		if(isset($_REQUEST['type']) && $_REQUEST['type']=='gal'){
			$output .= '		echo \'<div>\'.$data->img_count.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->titulo.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->texto.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->url.\'</div>\';'."\n";
			$output .= '		echo \'<img src="/ws-img/0/0/\'.$data->avatar.\'"/>\';'."\n";
		}

		if(isset($_REQUEST['type']) && $_REQUEST['type']=='img_gal'){
			$output .= '		echo \'<div>\'.$data->titulo.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->texto.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->url.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->token.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->filename.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->posicao.\'</div>\';'."\n";
			$output .= '		echo \'<img src="/ws-img/0/0/\'.$data->file.\'"/>\';'."\n";
		}
		if(isset($_REQUEST['type']) && $_REQUEST['type']=='img'){
			$output .= '		echo \'<div>\'.$data->titulo.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->texto.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->url.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->filename.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->token.\'</div>\';'."\n";
			$output .= '		echo \'<img src="/ws-img/0/0/\'.$data->imagem.\'"/>\';'."\n";
		}

		if(isset($_REQUEST['type']) && $_REQUEST['type']=='cat'){
			$output .= '		echo \'<div>\'.$data->titulo.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->texto.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->avatar.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->token.\'</div>\';'."\n";
			$output .= '		echo \'<img src="/ws-img/0/0/\'.$data->avatar.\'"/>\';'."\n";
		}
		if(isset($_REQUEST['type']) && $_REQUEST['type']=='file'){
			$output .= '		echo \'<div>\'.$data->posicao.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->uploaded.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->titulo.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->url.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->texto.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->file.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->filename.\'</div>\';'."\n";
			$output .= '		echo \'<div>\'.$data->token.\'</div>\';'."\n";
		}
	$output.= '	}'."\n";
	echo ($output);
	exit;
	}
	if(isset($_REQUEST['typeCode']) && $_REQUEST['typeCode']=="tag"){
		$output =  "";
		$output .= '<ws-tool slug="'.$ws_ferramentas->fetch_array[0]['slug'].'" type="'.$_REQUEST['type'].'">'."\n";
		$output .=  '	<!--'."\n";
		$output .=  '		OUTRAS TAGS DISPONÍIVEIS:'."\n";
		$output .=  '		colum=""	'."\n";
		$output .=  '		distinct=""	'."\n";
		$output .=  '		utf8=""	'."\n";
		$output .=  '		url=""	'."\n";	
		$output .=  '		order=""	'."\n";
		$output .=  '		category=""	'."\n";
		$output .=  '		galery=""	'."\n";
		$output .=  '		item="" 	'."\n";
		$output .=  '		where=""	'."\n";
		$output .=  '		innerItem="" '."\n";
		$output .=  '		filter=""'."\n";
		$output .=  '		Paginação:'."\n";
		$output .=  '		|	paginate="1,2"'."\n";
		$output .=  '		|	1:max por página, 2:página atual'."\n";
		$output .=  '		|	pode ser usado parâmetros da URL ex:  paginate="url:1,url:2"'."\n\n";
		// $output.= '################################### LIVE EDITOR ###################################'."\n";
		// $output.= '		Agora você pode editar o seu site, dentro do próprio site, '."\n";
		// $output.= '		basta inserir da DIV a tag "live-editor"'."\n";
		// $output.= '		Por exemplo, o campo que você quer trazer é "titulo_blog",'."\n";
		// $output.= '		Você deverá inserir em sua div isso: live-editor="{{titulo_blog_editor}}".'."\n";
		// $output.= '		ATENÇÃO, COLOQUE APENAS NA DIV QUE TIVER O CONTEÚDO COMPLETO, POIS O QUE TIVER NA DIV SERÁ SALVO NA BASE DE DADOS.".'."\n";
		// $output.= '		Por exemplo, se uma div tiver apenas uma prévia do texto, e você salvar, todo conteudo será trocado pela prévia.'."\n";
		// $output.= '#################################################################################'."\n";

		$output .=  '	-->'."\n";

		$fullPages 				= new MySQL();
		$fullPages->set_table(PREFIX_TABLES.'_model_campos');
		$fullPages->set_where('ws_id_ferramenta="'.$_REQUEST['id_tool'].'"');
		$fullPages->set_where('AND coluna_mysql<>""');
		$fullPages->select();

		if(isset($_REQUEST['type']) && $_REQUEST['type']=='item'){
			if($fullPages->_num_rows==0){$output .= '	<!-- Nenhum campos específico adicionado -->'."\n";}
			foreach ($fullPages->fetch_array as $tool) {
				if($prefix!="" && substr($tool['coluna_mysql'],0,strlen($prefix))==$prefix) {$tool['coluna_mysql'] = substr($tool['coluna_mysql'],strlen($prefix));}
				if( $tool['type']=="playerVideo"){
					$output .=	"	<!-- Function:  url=null,type=player,w=null,h=null -->\n";
					$output .= '	<!-- Retorno: site,url,title,description,image ou player -->'."\n";
					$output .= '	{{'.$tool['coluna_mysql'].",ws::videoData,(this),Retorno,200,200}}"."\n";
				}elseif( $tool['type']=="playerMP3"){
					$output .=	"	<!-- Function:  url=null,type=player,w=null,h=null,size,theme -->\n";
					$output .=	"	<!-- Retorno: 	player, site, title, description, height, width, image -->\n";
					$output .=	"	<!-- Size: 		widget,classic,minimo,list -->\n";
					$output .=	"	<!-- Theme: 	light, dark -->\n";
					$output .= '	{{'.$tool['coluna_mysql'].',ws::audioData,(this),Retorno,w,h,Size,Theme}}'."\n";
				}elseif( $tool['type']=="thumbmail"){
					$output .= '	<img src="/ws-img/0/0/{{'.$tool['coluna_mysql'].'}}"/>'."\n";
				}else{
					$output .= '	{{'.$tool['coluna_mysql'].'}}'."\n";
				}
			}
		}
		if(isset($_REQUEST['type']) && $_REQUEST['type']=='gal'){
			$output .= '	{{img_count}}'."\n";
			$output .= '	{{titulo}}'."\n";
			$output .= '	{{texto}}'."\n";
			$output .= '	{{url}}'."\n";
			$output .= '	<img src="/ws-img/0/0/{{avatar}}"/>'."\n";
		}

		if(isset($_REQUEST['type']) && $_REQUEST['type']=='img_gal'){
			$output .= '	{{titulo}}'."\n";
			$output .= '	{{texto}}'."\n";
			$output .= '	{{url}}'."\n";
			$output .= '	{{token}}'."\n";
			$output .= '	{{filename}}'."\n";
			$output .= '	{{posicao}}'."\n";
			$output .= '	<img src="/ws-img/0/0/{{file}}"/>'."\n";
		}
		if(isset($_REQUEST['type']) && $_REQUEST['type']=='img'){
			$output .= '	{{titulo}}'."\n";
			$output .= '	{{texto}}'."\n";
			$output .= '	{{url}}'."\n";
			$output .= '	{{filename}}'."\n";
			$output .= '	{{token}}'."\n";
			$output .= '	<img src="/ws-img/0/0/{{imagem}}"/>'."\n";
		}

		if(isset($_REQUEST['type']) && $_REQUEST['type']=='cat'){
			$output .= '	{{titulo}}'."\n";
			$output .= '	{{texto}}'."\n";
			$output .= '	{{avatar}}'."\n";
			$output .= '	{{token}}'."\n";
			$output .= '	<img src="/ws-img/0/0/{{avatar}}"/>'."\n";
		}
		if(isset($_REQUEST['type']) && $_REQUEST['type']=='file'){
			$output .= '	{{posicao}}'."\n";
			$output .= '	{{uploaded}}'."\n";
			$output .= '	{{titulo}}'."\n";
			$output .= '	{{url}}'."\n";
			$output .= '	{{texto}}'."\n";
			$output .= '	{{file}}'."\n";
			$output .= '	{{filename}}'."\n";
			$output .= '	{{token}}'."\n";
		}
		$output .= "\n\n".'</ws-tool>';
	}
	echo ($output);
	exit;
}

function getShortCodesPlugin (){
	$jsonConfig = $_REQUEST['path'].'/plugin.config.json';
	$phpConfig 	= $_REQUEST['path'].'/plugin.config.php';
	$path 		= $_REQUEST['path'];
	if(file_exists($phpConfig)){
			ob_start(); @include($phpConfig); $jsonRanderizado=ob_get_clean();
			$contents 		=	$plugin;
	}elseif(file_exists($jsonConfig)){
			$contents 		=	json_decode(file_get_contents($jsonConfig));
	}

	if(empty($contents->shortcode) || $contents->shortcode==1 ){
			######################################################## SHORTCODE ##########################################################################################
			#############################################################################################################################################################
			if(isset($contents->style) && count($contents->style)>=1){
				echo PHP_EOL.'<!-- '.PHP_EOL.PHP_EOL.'	Inclua esses styles entre as tags <head></head> :'.PHP_EOL;
				foreach ($contents->style as $value) {
					if(is_array($value) && count($value)==1){
						$link = $value[0];
						$link = filter_var($link, FILTER_SANITIZE_URL);
						if (!filter_var($link, FILTER_VALIDATE_URL) === false){
							echo '	<link rel="stylesheet" type="text/css" href="'.$link.'">'.PHP_EOL;
						}else{
							echo '	<link rel="stylesheet" type="text/css" href="/'.basename($path).'/'.$link.'">'.PHP_EOL;
						}
					}elseif(is_array($value) && count($value)==2){
						$link = $value[0];
						$link = filter_var($link, FILTER_SANITIZE_URL);
						if (!filter_var($link, FILTER_VALIDATE_URL) === false){
							echo '	<link rel="stylesheet" type="text/css" href="'.$link.'" '.$value[1].'>'.PHP_EOL;
						}else{
							echo '	<link rel="stylesheet" type="text/css" href="/'.basename($path).'/'.$link.'" '.$value[1].'>'.PHP_EOL;
						}
					}else{
						$link = $value;
						$link = filter_var($link, FILTER_SANITIZE_URL);
						if (!filter_var($link, FILTER_VALIDATE_URL) === false){
							echo '	<link rel="stylesheet" type="text/css" href="'.$link.'" >'.PHP_EOL;
						}else{
							echo '	<link rel="stylesheet" type="text/css" href="/'.basename($path).'/'.$link.'" >'.PHP_EOL;
						}
					}
				}
				echo PHP_EOL.'-->'.PHP_EOL.PHP_EOL;
			}
			$arrReq   = array();
			if(isset($contents->requiredData) && is_array($contents->requiredData) && $contents->requiredData!="" && count($contents->requiredData)>1){
				foreach($contents->requiredData as $req){
					if(is_array($req)){
							$r = array_slice($req,1);
							$data = array();
							foreach($r as $d){ $data[] = $d;}

							if(count($data)>1){
								$data = "array('".(implode($data,"','"))."')";
							}else{
								$data = implode($data);
							}
						$arrReq[]=  $req[0].'="'.$data.'" ';
					}else{
						$arrReq[]= $req.'="" ';
					}
				}
			}
			echo '<ws-plugin path="'.basename($path).'" '.implode($arrReq," ").'></ws-plugin>'.PHP_EOL;


			if(isset($contents->script) && count($contents->script)>=1){
				echo PHP_EOL.'<!-- '.PHP_EOL.PHP_EOL.'	Para esse plugin funcionar corretamente, é necessário incluir esses arquivos ao final da página:'.PHP_EOL;
				foreach ($contents->script as $value) {
					if(is_array($value) && count($value)==1){
						$link = $value[0];
						$link = filter_var($link, FILTER_SANITIZE_URL);
						if (!filter_var($link, FILTER_VALIDATE_URL) === false){
							echo '	<script 	type="text/javascript" src="'.$link.'"></script>'.PHP_EOL;
						}else{
							echo '	<script 	type="text/javascript" src="/'.basename($path).'/'.$link.'"></script>'.PHP_EOL;
						}
					}elseif(is_array($value) && count($value)==2){
						$link = $value[0];
						$link = filter_var($link, FILTER_SANITIZE_URL);
						if (!filter_var($link, FILTER_VALIDATE_URL) === false){
							echo '	<script 	type="text/javascript" src="'.$link.'" id="'.$value[1].'"></script>'.PHP_EOL;
						}else{
							echo '	<script 	type="text/javascript" src="/'.basename($path).'/'.$link.'"  id="'.$value[1].'"></script>'.PHP_EOL;
						}
					}else{
						$link = $value;
						$link = filter_var($link, FILTER_SANITIZE_URL);
						if (!filter_var($link, FILTER_VALIDATE_URL) === false){
							echo '	<script 	type="text/javascript" src="'.$link.'"></script>'.PHP_EOL;
						}else{
							echo '	<script 	type="text/javascript" src="/'.basename($path).'/'.$link.'"></script>'.PHP_EOL;
						}
					}
				}
				echo PHP_EOL.'-->';
			}
		#############################################################################################################################################################
		#############################################################################################################################################################
	}elseif($contents->shortcode==2){
			echo file_get_contents($path.'/'.$contents->plugin);
	}elseif($contents->shortcode==3){
			$arrReq = array();
			$shortCode =  '[ws]{"slug":"'.@$contents->slug.'"';
			if(isset($contents->requiredData) && is_array($contents->requiredData) && $contents->requiredData!="" && count($contents->requiredData)>1){
				foreach($contents->requiredData as $req){
					if(is_array($req)){
							$r = array_slice($req,1);
							$data = array();
							foreach($r as $d){
									if(is_string($d)){
										$data[] = '"'.$d.'"';
									}else{
										$data[] = $d;
									}
							}
							if(count($data)>1){
								$data = '['.implode($data,',').']';
							}else{
								$data = implode($data);
							}
						$arrReq[]= '"'.$req[0].'":'.$data;
					}else{
						$arrReq[]= '"'.$req.'":""';
					}
				}
			}
			if(count($arrReq)>1){$shortCode .=   ','.implode($arrReq,","); }
			$shortCode .=   '}[/ws] '.PHP_EOL;
			$ws =  (object) array('rootPath'=>str_replace('./../../../website/','/',$_REQUEST['path']),'shortcode'=>$shortCode,'vars' =>(object)$contents->requiredData,'json' => $contents);
			ob_start(); @include($path.'/'.$contents->plugin); $jsonRanderizado=ob_get_clean();
			echo $jsonRanderizado;
	}
	exit;

}
function loadShortCodes (){
	$setupdata 	= new MySQL();
	$setupdata->set_table(PREFIX_TABLES.'setupdata');
	$setupdata->set_order('id','DESC');
	$setupdata->set_limit(1);
	$setupdata->debug(0);
	$setupdata->select();
	$setupdata = $setupdata->fetch_array[0];
	$path = ROOT_WEBSITE.'/'.$setupdata['url_plugin'];

	echo '<div style="comboShortCode">
		<div style="font-size: 20px;font-weight: bold;padding-bottom: 12px;">Adicionar um plugin</div>
		<div class="descricao">Escolha um dos plugins instalados e adicione em seu conteudo</div>
		<div class="c"></div>
		<div style="padding: 20px;">
			<select id="shortcodes" style="width:450px;padding: 10px;border: none;color: #3A639A;-moz-border-radius: 7px;-webkit-border-radius: 7px;border-radius: 7px;">
				<option value="">Selecione uma popção</option>
			';
			$dh = opendir($path);
			while($diretorio = readdir($dh)){
				if($diretorio != '..' && $diretorio != '.' && $diretorio != '.htaccess'){
					if(file_exists($path.'/'.$diretorio.'/active')){
						$jsonConfig = $path.'/'.$diretorio.'/plugin.config.json';
						$phpConfig 	= $path.'/'.$diretorio.'/plugin.config.php';
						if(file_exists($phpConfig)){
								ob_start(); @include($phpConfig); $jsonRanderizado=ob_get_clean();
								$contents 		=	$plugin;
						}elseif(file_exists($jsonConfig)){
								$contents 		=	json_decode(file_get_contents($jsonConfig));
						}
						echo "<option value='".$path.'/'.$diretorio."'>".$contents->pluginName.'</option>';
					}
				}
			}
			echo '</select>
			</div>
		</div>';
	exit;
}


function returnBKP (){
		$file_exists_dir 				= new MySQL();
		$file_exists_dir->set_table(PREFIX_TABLES.'ws_webmaster');
		$file_exists_dir->set_where('path="'.str_replace('./../../..', '',$_REQUEST['pathFile']).'"');
		$file_exists_dir->set_where('AND original="'.$_REQUEST['filename'].'"');
		$file_exists_dir->set_order('id','DESC');
		$file_exists_dir->select();
		if($file_exists_dir->_num_rows==0){
			echo '<option>Nenhum bkp gerado</option>'.PHP_EOL;
		}elseif($file_exists_dir->_num_rows<2){
			echo '<option>1 bkp gerado </option>'.PHP_EOL;
			echo '<option value="original">Arquivo oficial</option>'.PHP_EOL;
		}else{
			echo '<option>'.$file_exists_dir->_num_rows.' bkp gerados </option>'.PHP_EOL;
			echo '<option value="original">Arquivo oficial</option>'.PHP_EOL;
		}
		foreach($file_exists_dir->fetch_array as $opt){echo '<option value="'.$opt['token'].'">'.$opt['created'].'</option>'.PHP_EOL;};
}
function _excl_dir_(){
		$Dir = $_REQUEST['exclFolder'];
		function ExcluiDir($Dir){
			if ($dd = opendir($Dir)) {
				while (false !== ($Arq = readdir($dd))) {
					if($Arq != "." && $Arq != ".."){
						$Path = "$Dir/$Arq";
						if(is_dir($Path)){
							ExcluiDir($Path);
						}elseif(is_file($Path)){
							if(!unlink($Path)){_erro("ops, houve um erro!".__LINE__);};
						}
					}
				}
				closedir($dd);
			}
			chmod($Dir,0777);
			if(!rmdir($Dir)){_erro("ops, houve um erro!".__LINE__);};
		}
		$versoes = str_replace('./../../../',"./versoes/", $Dir);
		$ftp 	= $Dir;
		ExcluiDir($ftp);
		ExcluiDir($versoes);

		echo "sucesso";
}

function createFolder($NewPath=null){

	if($NewPath==null && isset($_REQUEST['newFile'])){$newFile = $_REQUEST['newFile'];}else{$newFile = $NewPath;}

	$newFile = explode('/',$newFile);
	$fullPath = array();
	foreach ($newFile as $value) {
		if($value!=".." && $value!="." ) {$fullPath[]=$value;}
		$verifica = implode('/',$fullPath);
		if(!is_dir(ROOT_WEBSITE.$verifica)){
			mkdir(ROOT_WEBSITE.$verifica);
			if(!file_exists(ROOT_WEBSITE.$verifica.'/.htaccess')){
				file_put_contents(ROOT_WEBSITE.$verifica.'/.htaccess',"#".PHP_EOL."#".PHP_EOL."#Exclua apenas se souber o que está fazendo.".PHP_EOL."#".PHP_EOL."#".PHP_EOL."RewriteEngine off");
			}
		}
	}
	if($NewPath==null && isset($_REQUEST['newFile'])){echo "sucesso"; }
}

function CriaPastas($dir,$oq=0){
	if (is_dir($dir)) {
		$dh = opendir($dir);
		while($diretorio = readdir($dh)){
			if($diretorio != '..' && $diretorio != '.' && is_dir($dir.'/'.$diretorio)){
				echo '<div class="w1 folder_alert folder" data-folder="'.str_replace(ROOT_WEBSITE,"",$dir.'/'.$diretorio).'">'.$diretorio."</div>".PHP_EOL;
				echo "<div class='w1 container'>".PHP_EOL;
				CriaPastas($dir.'/'.$diretorio."/",$oq);
				if($oq==1 || $oq==true) MostraFiles($dir.'/'.$diretorio."/");
				echo "</div>".PHP_EOL;
			};
		};
	};
};


function createFile (){
		$dirname 	= dirname($_REQUEST['newFile']);
		$filename 	= basename($_REQUEST['newFile']);
		$fileCreate = "";
		createFolder($dirname);
		if($dirname=='.'){
			$fileCreate = ROOT_WEBSITE.'/'.$filename;
		}elseif(substr($dirname,0,1)!='/'){
			$fileCreate = ROOT_WEBSITE.'/'.$dirname.'/'.$filename;
		}else{
			$fileCreate = ROOT_WEBSITE.$dirname.'/'.$filename;
		}
		if(file_put_contents($fileCreate,$filename)){
			loadFile(ROOT_WEBSITE.$dirname.'/'.$filename);
		}else{
			echo "falha";
		};
	exit;
}
function ListFolderNewFile (){
	echo 'Criar um arquivo novo
	<div class="nave_folders">';
	CriaPastas(ROOT_WEBSITE);
	echo '</div>
	<div class="c"></div>
	<input class="inputText path" placeholder="Digite o path do seu diretório:">
	<div class="c"></div>
	<script>
		$("*[legenda]").LegendaOver();

		var newFolder = null;
		$(".folder_alert").unbind("click tap").bind("click tap",function(){
			var getFolder = $(this).data("folder");
			$("input.path").val(getFolder.replace("./../../../website","")+"/")
		})
		sanfona(\'.folder_alert\');
	</script>';
}
function ListFolderExclFolder (){
	echo 'Selecione um diretório e complemente com o nome do novo folder <br>ou apenas escreva o nome do novo folder no campo a baixo:
	<div class="c"></div>
	<div class="bg08" style="padding: 10px 60px; margin: 10px; color: #D80000;">Atenção, ao apagar serão excluidos também os arquivos de BKP em seu sistema.<br>E isso não terá mais volta!</div>
	<div class="c"></div>

	<div class="nave_folders"><form>';
	CriaPastas(ROOT_WEBSITE);
	echo '</div></form>
	<div class="c"></div>
	<script>
		var newFolder = null;
		$(".folder_alert").unbind("click tap").bind("click tap",function(){
			var getFolder = $(this).data("folder");
			$("input.path").val(getFolder.replace("./../../../","")+"/")
		})
		sanfona(\'.folder_alert\');
	</script>';}
function ListFolderNewFolder (){
	echo 'Selecione um diretório e complemente com o nome do novo folder <br>ou apenas escreva o nome do novo folder no campo a baixo:
	<div class="c"></div>

	<div class="nave_folders">';
	CriaPastas(ROOT_WEBSITE);
	echo '</div>
	<div class="c"></div>
	<input class="inputText path" placeholder="Digite o path do seu diretório:">
	<div class="c"></div>
	<script>
		var newFolder = null;
		$(".folder_alert").unbind("click tap").bind("click tap",function(){
			var getFolder = $(this).data("folder");
			$("input.path").val(getFolder.replace("./../../../website/","")+"/")
		})
		sanfona(\'.folder_alert\');
	</script>';}
	
function MostraFiles($dir){
	$dh = opendir($dir);
	while($arquivo = readdir($dh)){
		if($arquivo != '..' && $arquivo != '.' && !is_dir($dir.$arquivo)){
			$ext = explode('.',$arquivo);
			$ext = @$ext[1];
			if(	isset($ext)		&&($ext=="txt" 	||$ext=="htm" 	||$ext=="html" 	||$ext=="xhtml" 	||$ext=="xml" 	||$ext=="js"	 	||$ext=="json" 	||$ext=="php" 	||$ext=="css" 	||$ext=="less" 	||$ext=="sass" 	||$ext=="htaccess"||$ext=="key" 	||$ext=="asp" 	||$ext=="aspx" 	||$ext=="net" 	||$ext=="conf" 	||$ext=="ini" 	||$ext=="sql" 	||$ext=="as" 		||$ext=="htc" 	||$ext=="--")){
				echo '	<div class="w1 file '.$ext.' multiplos" data-id="null" data-file="'.$dir.$arquivo.'"  >'.$arquivo."</div>".PHP_EOL;
			};
		};
	};
};

function 	refreshFolders (){
	CriaPastas(ROOT_WEBSITE,true);
	MostraFiles(ROOT_WEBSITE);
	echo '<script>sanfona(\'.folder\');</script>';
}

function loadFile($pathFile=null){
	global $_conectMySQLi_;
		if(isset($_REQUEST['pathFile']) && $pathFile==null){
			$pathFile 	= $_REQUEST['pathFile'];
		}else{
			$_REQUEST['pathFile'] = $pathFile;
		}

		$pathFile 	= explode("/",$pathFile);
		$file 		= end($pathFile);
		$pathFile 	= array_slice($pathFile,0, -1);
		$pathFile	= implode($pathFile,'/'); 
		$file_exists_dir 				= new MySQL();
		$file_exists_dir->set_table(PREFIX_TABLES.'ws_webmaster');
		$file_exists_dir->set_where('path="'.$pathFile.'"');
		$file_exists_dir->set_where('AND original="'.$file.'"');
		$file_exists_dir->set_order('id','DESC');
		$file_exists_dir->select();
		$count =$file_exists_dir->_num_rows;
		$ext = explode('.',$file);
		$ext = end($ext);
		$newTokenFile = createPass(rand(9,50), $maiusculas = true, $numeros = false, $simbolos = false);
		if($ext=="txt"){$ext="text";}
		if($ext=="js"){$ext="javascript";}
		$stringFile = mysqli_real_escape_string($_conectMySQLi_,file_get_contents($pathFile.'/'.$file));

	echo 'if(!$(\'.fileTabContainer .fileTab[data-pathFile="'.$pathFile.'"][data-loadFile="'.$file.'"]\').length){';
				echo '$("#nameFile").html("<span class=\'b1 noSelect\'>Nome do arquivo:</span> /'.str_replace('./../../../','', $_REQUEST['pathFile']).'");';
				echo '$("#mode option[value 	=\''.$ext.'\']").attr("selected","true").trigger("chosen:updated");';
				echo 'window.typeLoaded			= "file";';
				echo 'window.pathFile 			= "'.$pathFile.'";';
				echo 'window.loadFile 			= "'.$file.'";';
				echo 'window.newTokenFile 		= "'.$newTokenFile.'";';
				echo 'window.htmEditor.setReadOnly(false);';
				//MONTA O OBJETO COM OS ARQUIVOS E AS SESSÕES 
				echo 'window.listFilesWebmaster.'.$newTokenFile.' = Object();';
				echo 'window.listFilesWebmaster.'.$newTokenFile.' ={'.
																	'session:		ace.createEditSession("'.$stringFile.'" ,"ace/mode/'.$ext.'")'.
																	',file:			"'.$file.'"'.
																	',pathFile:		"'.$pathFile.'"'.
																	',newTokenFile: "'.$newTokenFile.'"'.
																	',setReadOnly: 	false'.
																	',saved: "saved"'.
																'};'.PHP_EOL.PHP_EOL;
				//APLICA AS SESSÕES AO EDITOR
				echo 'window.htmEditor.setSession(window.listFilesWebmaster.'.$newTokenFile.'.session);';
				echo 'window.addTab("'.$newTokenFile.'",window.pathFile,window.loadFile,"saved");';
				echo 'window.htmEditor.getSession().on("changeScrollTop", function(scroll) {setDestque()});';
				echo 'setDestque();';

	echo '}else{
				$(\'.fileTabContainer .fileTab[data-pathFile="'.$pathFile.'"][data-loadFile="'.$file.'"]\').click();
		 };';

	}
	function loadFileBKP(){
		if($_REQUEST['token']=="original"){
			$_REQUEST['pathFile'] = $_REQUEST['pathFile'].'/'.$_REQUEST['filename'];
			loadFile();
			exit;
		}
		$file_exists_dir 				= new MySQL();
		$file_exists_dir->set_table(PREFIX_TABLES.'ws_webmaster');
		$file_exists_dir->set_where('token="'.$_REQUEST['token'].'"');
		$file_exists_dir->set_order('id','DESC');
		$file_exists_dir->select();
		$count =$file_exists_dir->fetch_array[0];
		$ext = explode('.',$count['bkpfile']);
		$ext = end($ext);

		if($ext=="txt")$ext="text";
		if($ext=="js")$ext="javascript";


		echo '$("#mode option[value 	=\''.$ext.'\']").attr("selected","true").trigger("chosen:updated");';
		echo 'window.typeLoaded			= "bkp";';
		echo 'window.htmEditor.setReadOnly(true);';
		echo 'window.htmEditor.getSession().setMode("ace/mode/'.$ext.'");';
		echo 'window.htmEditor.setValue("'.mysql_real_escape_string(file_get_contents('./versoes/'.$count['path'].'/'.$count['bkpfile'])).'");';
		echo 'setTimeout(function(){$(".ace_scrollbar").perfectScrollbar("update");},200);';}

function geraBKPeAplica(){
		parse_str($_POST['GET'], $POST);
		$pathFile 	= str_replace(array(ROOT_WEBSITE.'/','./../../../../website/'),'', $POST['pathFile']);
		$_FILE_NAME 		= $POST['filename'];
		$_NAME_FILE_BKP 	= 'bkp_'.date('d-m-y_H-i-s').'_'.$_FILE_NAME;

		$folderFTP 	= ROOT_WEBSITE.'/'.$pathFile.'/';
		if($_POST['bkp']=='true'){
			file_put_contents($folderFTP.$_NAME_FILE_BKP, file_get_contents($folderFTP.$_FILE_NAME));
			$file_exists_dir 				= new MySQL();
			$file_exists_dir->set_table(PREFIX_TABLES.'ws_webmaster');
			$file_exists_dir->set_insert('path',$folderFTP);
			$file_exists_dir->set_insert('original',$_FILE_NAME);
			$file_exists_dir->set_insert('bkpfile',$_NAME_FILE_BKP);
			$file_exists_dir->set_insert('responsavel',$_SESSION['user']['id']);
			$file_exists_dir->set_insert('token',_token(PREFIX_TABLES.'ws_webmaster','token'));
			$file_exists_dir->insert();
		}
		if(file_put_contents($folderFTP.$_FILE_NAME, $POST['ConteudoDoc'])){ echo "sucesso";};
		exit;
	}
function getVersionsFile(){
	if(empty($_SESSION['user']['id']) || $_SESSION['user']['id']==""){echo "window.location.reload()";exit;}
	$pathFile 	= $_REQUEST['pathFile'];
	$pathFile 	= explode("/",$pathFile);
	$file 		= end($pathFile);
	$pathFile 	= array_slice($pathFile,0, -1);
	$pathFile	= implode($pathFile,'/'); 
	// verifica se existe registro
	$file_exists_dir 				= new MySQL();
	$file_exists_dir->set_table(PREFIX_TABLES.'ws_webmaster');
	$file_exists_dir->set_where('path="'.$pathFile.'"');
	$file_exists_dir->set_where('AND original="'.$file.'"');
	$file_exists_dir->set_order('id','DESC');
	$file_exists_dir->select();
	$count =$file_exists_dir->_num_rows;

	if($count==0){
	}elseif($count>1){
		//********************************************************************************************************************************************************************
		$resposta =  '<div style="margin-bottom:20px;font-weight:800;">Este arquivo tem versões disponíveis. Escolha uma delas: </div>';
		$resposta .=  '<div style="position: relative;overflow: auto;height: 230px;margin-right: 10px;margin-bottom: -40px;">';
		foreach($file_exists_dir->fetch_array as $file){
			if($file['checkin']=='1'){
					$resposta .=  '<div class=" bg06" style="padding: 10px;margin:0 9px;height: 28px;">';
					$resposta .=  '<div style="position: relative;float: left;margin-top: 5px;font-size: 12px;font-weight: 800;"><span style="color:#19AB00;font-size: 15px;">[ Chek-In ]</span> Salvo em: '.$file['updated'].'</div>';
					if($file['id_checkout']==$_SESSION['user']['id']){
						$resposta .=  '<div data-id="'.$file['id'].'" class="botao botao_load_file" style="position: relative;float:right;padding: 6px 30px;">Abrir versão</div>';
					}else{
						if($file['id_checkout']!="0"){
							$ws_usuarios 				= new MySQL();
							$ws_usuarios->set_table(PREFIX_TABLES.'ws_usuarios');
							$ws_usuarios->set_where('id="'.$file['id_checkout'].'"');
							$ws_usuarios->select();
							$resposta .=  '<div style="position: absolute;right: 0;padding: 6px 30px;color: #A41500;">Sendo editado por '.$ws_usuarios->fetch_array[0]['nome'].'</div>';
						}else{
							$resposta .=  '<div style="position: absolute;right: 0;padding: 6px 30px;color: #A41500;">Já está sendo editado.</div>';

						}
					}
			}else{
				$resposta .=  '<div class=" bg02" style="padding: 10px;margin:0 9px;height: 28px;">';
				$resposta .=  '<div style="position: relative;float: left;margin-top: 6px;">Salvo em: '.$file['updated'].'</div>';
				$resposta .=  '<div data-id="'.$file['id'].'" class="botao botao_load_file" style="position: relative;float:right;padding: 6px 30px;">Abrir versão</div>';
			}
			$resposta .=  '</div>';
		};
		$resposta .=  '</div>';
		$resposta .=  '<script type="text/javascript">
				$(function(){
					$(".botao_load_file").click(function(){
						var id_file = $(this).data("id");
							functions({
								funcao:"loadFileVersion",
								vars:"id_file="+id_file,
								patch:"'.$_SESSION["_PATCH_"].'",
								Sucess:function(a){eval(a);}
							});
						$("#close").click();
					});
				})
			</script>';
		echo 'confirma({conteudo:"'.str_replace(array(PHP_EOL,"	",'"','\n','\r'),array("","",'\"','',''),$resposta).'", bot1: 0,bot2: 0,drag:0,botclose:1});';
		//********************************************************************************************************************************************************************
	}else{
		//  se for apenas 1 arquivo
		// verifica se tem rascunho
			if($arrayMysql['rascunho']==""){
				$codigo = urldecode(stripslashes($arrayMysql['codigo']));
			}else{
				$codigo = urldecode(stripslashes($arrayMysql['rascunho']));
			};
		
		//  se foi vc que fez o checkin ele libera o checkout
		if($arrayMysql['checkin']=='1' && $arrayMysql['id_checkout']==$_SESSION['user']['id']){
			checkinchekcout($arrayMysql['id'], $arrayMysql['checkin'],$codigo);
			echo "out('1');";
		}else{
				// se não ele deixa como aberto.
				echo 'window.id_file_open="'.$arrayMysql['id'].'";';
				echo '$("#aplicar").show();';
				echo '$("#voltar_original").hide();';
				echo '$("#check").show();';
				echo 'window.doc_version="'.urlencode(stripslashes($codigo)).'";';
				echo 'window.editor.getDoc().setValue("'.mysql_real_escape_string($codigo).'");';
				echo '$(".CodeMirror textarea").attr("readOnly","");';
				echo "out('2');";
				validaChekin();
		}
	}}
function loadFileVersion(){
	if($_SESSION['user']['id']==""){echo "window.location.reload()";exit;}
	$id_file = $_REQUEST['id_file'];
	$load_version_file 				= new MySQL();
	$load_version_file->set_table(PREFIX_TABLES.'ws_webmaster');
	$load_version_file->set_where('id="'.$id_file.'"');
	$load_version_file->select();

	if($load_version_file->fetch_array[0]['rascunho']==""){
		$codigo = urldecode($load_version_file->fetch_array[0]['codigo']);
	}else{
		$codigo = urldecode($load_version_file->fetch_array[0]['rascunho']);
	};
	echo 'window.code_original="'.mysql_real_escape_string(file_get_contents($load_version_file->fetch_array[0]['path'])).'";';
	echo 'window.doc_version="'.urlencode($codigo).'";';
	checkinchekcout($load_version_file->fetch_array[0]['id'], $load_version_file->fetch_array[0]['checkin'],$codigo);}
function load_path(){
	if($_SESSION['user']['id']==""){echo "window.location.reload()";exit;}
	$load_path_file 				= new MySQL();
	$load_path_file->set_table(PREFIX_TABLES.'ws_webmaster');
	$load_path_file->set_where('path="'.$_REQUEST['pathFile'].'"');
	$load_path_file->set_limit(1);
	$load_path_file->set_order('id','DESC');
	$load_path_file->select();
	if($load_path_file->fetch_array[0]['rascunho']==""){
		$codigo = urldecode($load_path_file->fetch_array[0]['codigo']);
	}else{
		$codigo = urldecode($load_path_file->fetch_array[0]['rascunho']);
	};

	echo 'window.code_original="'.mysql_real_escape_string(file_get_contents($load_path_file->fetch_array[0]['path'])).'";';
	echo 'window.doc_version="'.urlencode($codigo).'";';
	checkinchekcout($load_path_file->fetch_array[0]['id'], $load_path_file->fetch_array[0]['checkin'],$codigo);}
function exclui_file(){
		$U= new MySQL();
		$U->set_table(PREFIX_TABLES.'ws_webmaster');
		$U->set_where('path="'.str_replace('./../../..', '',$_REQUEST['pathFile']).'"');
		$U->set_where('AND original="'.$_REQUEST['loadFile'].'"');
		$U->select();
		$qtdd_total = $U->_num_rows;
		if($qtdd_total>0){
			foreach ($U->fetch_array as $value) {
				$E= new MySQL();
				$E->set_table(PREFIX_TABLES.'ws_webmaster');
				$E->set_where('id="'.$value['id'].'"');
				$E->exclui();
				$NewFile = './versoes'.$value['path'].'/'.$value['bkpfile'];
				@unlink($NewFile);
			}
		}
		$UNIC = $_REQUEST['pathFile'].'/'.$_REQUEST['loadFile'];
		@unlink($_REQUEST['pathFile'].'/'.$_REQUEST['loadFile']);
		echo 'window.htmEditor.getSession().setMode("ace/mode/text");';
		echo '$("#mode option[value 	=\'Ttext\']").attr("selected","true").trigger("chosen:updated");';
		echo 'window.newTokenFile		= null;';
		echo 'window.typeLoaded			= null;';
		echo 'window.pathFile 			= null;';
		echo 'window.loadFile 			= null;';
		echo 'window.newTokenFile 		= null;';
		echo 'window.htmEditor.setValue("");';
		echo '$("#bkpsFile").html("").trigger("chosen:updated");';
	}
function saveFileBKP(){

			if($_SESSION['user']['id']==""){echo "window.location.reload()";exit;}

			$U					= new MySQL();
			$U->set_where('id="'.$_REQUEST['id'].'"');
			$U->set_table(PREFIX_TABLES.'ws_webmaster');
			$U->set_update('rascunho',urlencode(stripslashes($_REQUEST['file_content'])));
			$U->set_update('responsavel_altera',$_SESSION['user']['id']);
			if($U->salvar()){
				echo 'TopAlert({mensagem: "Salvo com sucesso!",type: 3});';
			}else{
				echo 'TopAlert({mensagem: "Ops, houve uma falha ao salvar",type: 2});';
			}}
function aplica_file(){
	if($_SESSION['user']['id']==""){echo "window.location.reload()";exit;}
	$path 			=		$_REQUEST['path']; 
	$doc_version 	=		$_REQUEST['doc_version']; 
	if(file_put_contents($path, stripslashes($doc_version))){
			echo 'TopAlert({mensagem: "Sucesso em aplicar!",type: 3});';
	}else{
			echo 'TopAlert({mensagem: "Ops, houve uma falha ao aplicar",type: 2});';
	}}
/**/

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

