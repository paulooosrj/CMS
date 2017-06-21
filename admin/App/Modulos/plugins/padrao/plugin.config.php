<?
// Montamos o objeto
$plugin = new stdClass();
/*
########################################################################################################################################
• 	Valor monetário do Plugin, caso queira monetizar seus plugins
• 	Definido como 0, é gratuito eppoderá ser baixado
########################################################################################################################################
*/
$plugin->price 				=	0;
/*
########################################################################################################################################
• Nome da pasta do seu plugin 
########################################################################################################################################
*/
$plugin->pluginPath 		=	"padrao";
/*
########################################################################################################################################
• Versão do plugin
########################################################################################################################################
*/
$plugin->version 			=	"0.0.1";
/*
########################################################################################################################################
• Versão mínima do painel WebSheep
########################################################################################################################################
*/
$plugin->minWsVersion 		=	"0.0.4.3";
/*
########################################################################################################################################
• Nome do Plugin
########################################################################################################################################
*/
$plugin->pluginName 		=	"Plugin Local";
/*
########################################################################################################################################
• Slug do plugin 
########################################################################################################################################
*/
$plugin->slug 				=	"newPlugin";
/*
########################################################################################################################################
• Painel de configurações do seu plugin (caso haja um)
########################################################################################################################################
*/
$plugin->painel 			=	"painel.php";
/*
########################################################################################################################################
• Arquivo do próprio plugin
########################################################################################################################################
*/
$plugin->plugin 			=	"plugin.php";
/*
########################################################################################################################################
• 1 = insere em forma de Shortcode <ws-plugin></ws-plugin>
• 2 = Insere o arquivo do plugin no código com os scripts php completo
• 3 = Insere o arquivo do plugin no código com o php já processado
########################################################################################################################################
*/
$plugin->shortcode			=	1;
/*
########################################################################################################################################
• Ícone pequeno, 16x16 da aplicação, que aparecerá no topo ou na lateral do painel
########################################################################################################################################
*/
$plugin->icon				=	"favicon.png";
/*	
########################################################################################################################################
• Avatar da sua aplicação, 150x150. 
• Ela que aparecerá na listagem dos plugins
########################################################################################################################################
*/
$plugin->avatar				=	"avatar.png";
/*	
########################################################################################################################################
• Descrição do plugin
########################################################################################################################################
*/ 
$plugin->description		=	"Modelo base para criação de plugins WebSheep";
/*
########################################################################################################################################
• Nome do autor 
########################################################################################################################################
*/
$plugin->author				=	"Nome do usuário";
/*	
########################################################################################################################################
	Como será aberto o $plugin->painel
	• inner: Abrirá  dentro do websheep, como um módulo (todo CSS ser encapsulado dentro de .ws-plugin *{} para evitar conflitos)
	• iframe: Abrirá  dentro do websheep, em um iframe (caso seja um link externo)
	• modal: Abrirá  dentro do modal padrão do sistema 
	• Quando utilizar o "modal" será habilitado os outros 2 parâmetros, largura e altura
########################################################################################################################################
*/
$plugin->loadType 			=	array("inner",500 ,500);
/*
########################################################################################################################################
	Mostra aonde você quer habilitar a inserção do seu plugin
	• topo:  		Será exibito o ícone e nome do plugin no menu de topo do painel WebSheep 
	• lateral: 	Exibirá o ícone e o nome do plugin no menu lateral esquerdo do painel WebSheep
	• editor:		Sua aplicação poderá ser inserida via editor de códigos do sistema
	• textarea:	Sua aplicação poderá ser inserida pelo editor de texto de cada ítem dos módulos do site
	exemplos:
	• $plugin->menu 			=	"topo";
	• $plugin->menu 			=	array("topo");
	• $plugin->menu 			=	array("topo","lateral","editor","textarea");
########################################################################################################################################
*/
$plugin->menu 				=	array();
/*
########################################################################################################################################
	Variáveis requeridas no plugin
	Será os valores pré definidos por você
	Caso seja apenas uma estring, ela entrará vazia em seu Shortcode
	$plugin->requiredData 	=	array(
										"Variavel_vazia"
										,array("variavel_1","boxPlugin")
										,array("variavel_2","Novo plugin")
										,array("variavel_3","Conteúdo do novo plugin")
										,array("variavel_4",500)
									);
########################################################################################################################################
*/
$plugin->requiredData 		=	array();
/*
########################################################################################################################################
	Ficas a baixo do título do plugin na listagem
	Indicado para documentação, termos de uso etc 
	• $plugin->innerload 	= 	array(
									"String (_blank)"=>array("painel.php","iframe"),
									"String (_blank)"=>array("painel.php","_blank"),
									"String (_self)" =>array("painel.php","_self"),
									"String (inner)" =>array("painel.php","inner"),
									"String (modal)" =>array("painel.php","modal",500,500),
									"String (popup)" =>array("painel.php","popup",500,500)
								);
########################################################################################################################################
*/
$plugin->innerload 	= 	array(
									"String (_blank)"=>array("painel.php","_blank"),
									"String (_self)" =>array("painel.php","_self"),
									"String (inner)" =>array("painel.php","inner"),
									"String (modal)" =>array("painel.php","modal",500,500),
									"String (popup)" =>array("painel.php","popup",500,500)
								);
/*
########################################################################################################################################
	• $plugin->links	=array(
							"String (_blank)"=>array("painel.php","iframe"),
							"String (_blank)"=>array("painel.php","_blank"),
							"String (_self)" =>array("painel.php","_self"),
							"String (inner)" =>array("painel.php","inner"),
							"String (modal)" =>array("painel.php","modal",500,500),
							"String (popup)" =>array("painel.php","popup",500,500)
						);
	// Ficas logo a baixo da descrição do plugin na listagem, ao lado do nome do autor
	// Indicado para portfolio, sites, rede sociais etc 
########################################################################################################################################
*/
$plugin->links	=array(
							"String (_blank)"=>array("painel.php","_blank"),
							"String (_self)" =>array("painel.php","_self"),
							"String (inner)" =>array("painel.php","inner"),
							"String (modal)" =>array("painel.php","modal",500,500),
							"String (popup)" =>array("painel.php","popup",500,500)
						);
/*
########################################################################################################################################
	caso seu plugin tenha alguma dependencia de script,
	adicione a URL dos arquivos.
	• $plugin->script =	array(
								$plugin->pluginPath.'/js/myScript.js'
								,array("//code.jquery.com/jquery-3.0.0.min.js")
								,array("//code.jquery.com/jquery-3.0.0.min.js","jquery")
							);
*/
$plugin->script 			=	array();
/*
########################################################################################################################################
	caso seu plugin tenha alguma dependencia de CSS, adicione a URL dos arquivos.
	• $plugin->style 		=	array(
									$plugin->pluginPath.'/css/style.css'
									,array($plugin->pluginPath.'/css/style.css')
									,array($plugin->pluginPath.'/css/style.css','media="All"')
								);
########################################################################################################################################
*/
$plugin->style 				=	array(
									$plugin->pluginPath.'/css/style.css'
									,array($plugin->pluginPath.'/css/style.css')
									,array($plugin->pluginPath.'/css/style.css','media="All"')
								);

/*
########################################################################################################################################
	ARQUIVOS DE INCLUDE PHP
	caso seu plugin algum PHP que será incluído em todas as páginas do site.
	Poderá ser inserido uma string simples:
	Com a variável "after" ou sem variável, será incluído antes do carregamento geral to site.
	Já a variável "before", será carregado após o fechamento do site

	string: 
	• $plugin->globalphp	= "path/path/arquivo.php"
	Array: 
	• $plugin->globalphp	= array("path/path/arquivo.php")
	Array: 
	• $plugin->globalphp	= array("path/path/arquivo.php","before")
	Array: 
	• $plugin->globalphp	= array("path/path/arquivo.php","after")
	Array: 
	• $plugin->globalphp	= array(
					"path/path/arquivo.php",
					array("path/path/arquivo.php"),
					array("path/path/arquivo.php","after"),
					array("path/path/arquivo.php","before")
	)
########################################################################################################################################
*/
$plugin->globalphp			=	array();
/*
########################################################################################################################################
	Dependencia de algum plugin instalado pode ser usado assim:

	$plugin->dependency	=	"45g0y304ohf2g93469tf9owehf";
	assim:
	$plugin->dependency	=	array("45g0y304ohf2g93469tf9owehf");
	ou assim:
	$plugin->dependency	=	array(
										"45g0y304ohf2g93469tf9owehf",
										"hgne56yjnrtje457jymki890pol",
										"70k9iwe4gvstrjmk587ijadfnhut",
										"sdfg54ujrnmi8p678jetrjthw346"
									);
########################################################################################################################################
*/
$plugin->dependency		=	array();
$plugin->keycode		=	'nl2g54og78osadhvlwq346fojasdfjçwth83456uhbjçdfjg';