 <?
#################################################################################  
# LIMPA O CACHE INTERNO
#################################################################################
	clearstatcache();

#################################################################################  
# INCLUI AS CLASSES  DO SISTEMA
#################################################################################
	include_once($_SERVER['DOCUMENT_ROOT'].'/admin/App/Lib/class-ws-v1.php');
	_session();

#################################################################################  
# VERIFICA SE O USUÁRIO ESTÁ LOGADO OU AS SESSÕES E COOKIES ESTÃO EM ORDEM
#################################################################################
	verifyUserLogin();

#################################################################################  
# DEFINE O PATH QUE SERÁ USADO
#################################################################################
	define("PATH",'App/Modulos/_modulo_');

#################################################################################  
# CAPTAMOS OS DADOS DA FERRAMENTA
#################################################################################
	$_FERRAMENTA_ = new MySQL();
	$_FERRAMENTA_->set_table(PREFIX_TABLES.'ws_ferramentas');
	$_FERRAMENTA_->set_where('id="'.$_GET['ws_id_ferramenta'].'"');
	$_FERRAMENTA_->select();
	$_FERRAMENTA_ = @$_FERRAMENTA_->fetch_array[0];	


	$_GET['token_group'] = (
		(empty($_GET['token_group'])) ? (
										 (
										 	empty($_SESSION['token_group'])) ? _token(PREFIX_TABLES."ws_biblioteca","token_group") : $_SESSION['token_group']
										 ) :$_GET['token_group']
										);
#################################################################################  
# CASO NÃO EXISTA UMA CATEGORIA VINCULADA OU NÍVEIS DEFINIDOS, FORÇAMOS O VALOR 0
#################################################################################
	if(empty($_GET['id_cat'])	){			$_GET['id_cat']			='0';			}
	if(empty($_GET['ws_nivel'])	){			$_GET['ws_nivel']		='0';			}

#################################################################################  
# PESQUISA MYSQL DAS CATEGORIAS
#################################################################################
	$_CATEGORIAS_ 					= new MySQL();
	$_CATEGORIAS_->set_table(PREFIX_TABLES.'_model_cat');
	$_CATEGORIAS_->set_where('ws_id_ferramenta="'.$_GET['ws_id_ferramenta'].'"');
	$_CATEGORIAS_->set_order('posicao',"ASC");
	$_CATEGORIAS_->debug(0);
	$_CATEGORIAS_->select();

#################################################################################  
# INICIA CLASS TEMPLATE
#################################################################################
	$_SET_TEMPLATE_INPUT                = new Template(ROOT_ADMIN . "/App/Templates/html/Modulos/ws-tool-category-template.html", true);

#################################################################################  
# SETA DADOS BASICOS DAS CATEGORIAS
#################################################################################
	$_SET_TEMPLATE_INPUT->ID_CAT       	= $_GET['id_cat'];
	$_SET_TEMPLATE_INPUT->WS_TOKENGROUP = $_GET['token_group'];
	$_SET_TEMPLATE_INPUT->PATH          = PATH;
	$_SET_TEMPLATE_INPUT->ID_FERRAMENTA = $_GET['ws_id_ferramenta'];
	$_SET_TEMPLATE_INPUT->TITLE  		= $_FERRAMENTA_['_tit_topo_'];
	$_GETVARS 							= $_GET;unset($_GETVARS['id_cat']);
	$_SET_TEMPLATE_INPUT->GETVARS  		= http_build_query($_GETVARS);
	
#################################################################################
#  BOTÃO PARA ADICIONAR CATEGORIAS 
#################################################################################
	$_SET_TEMPLATE_INPUT->block("BOT_ADD_GALERY");

#################################################################################
#  CASO TENHA LINK DE REFERENCIA 
#################################################################################
	$_SET_TEMPLATE_INPUT->LINK     = './'.PATH.'/itens.php?token_group='.$_GET['token_group'].'&ws_id_ferramenta='.$_GET['ws_id_ferramenta'];
	$_SET_TEMPLATE_INPUT->block("BOT_BACK");

#################################################################################
#  RETORNA AS CATEGORIAS 
#################################################################################
	if(isset($_CATEGORIAS_->_num_rows)){
		foreach($_CATEGORIAS_->fetch_array as $_cat_){
			$_SET_TEMPLATE_INPUT->LI_IMG 			= $_cat_['avatar']!='' ? '/ws-img/0/42/100/'.$_cat_['avatar'] : '/ws-img/42/42/100/avatar.png';
			$_SET_TEMPLATE_INPUT->LI_CAT 			= $_cat_['id'];
			$_SET_TEMPLATE_INPUT->LI_CAT_PAI	 	= $_GET['id_cat'];
			$_SET_TEMPLATE_INPUT->LI_TITULO 		= substr(strip_tags(urldecode($_cat_['titulo'])),0,100);
			$_SET_TEMPLATE_INPUT->LI_DESCRIPTION	= substr(strip_tags(urldecode($_cat_['texto'])),0,100);
			$_SET_TEMPLATE_INPUT->LI_GROUP	 		= $_GET['token_group'];
			$_SET_TEMPLATE_INPUT->block("ITEMCAT");
		}
	}

#################################################################################
#  RETORNA A STRING DO RESULTADO 
#################################################################################
$_SET_TEMPLATE_INPUT->block("BLOCK_CATEGORY");
$_SET_TEMPLATE_INPUT->show();