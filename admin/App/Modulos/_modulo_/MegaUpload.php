<?
	$r = $_SERVER["DOCUMENT_ROOT"];
	$_SERVER["DOCUMENT_ROOT"] = (substr($r, -1) == '/') ? substr($r, 0, -1) : $r;
	include_once($_SERVER["DOCUMENT_ROOT"].'/ws-config.php');
?>
<iframe id="megaupload" src="/<?=ROOT_ADMIN.'/App/Modulos/megaupload'?>" style="top: 0;position: relative; width: 100%;height: calc(100% - 56px);"></iframe>
