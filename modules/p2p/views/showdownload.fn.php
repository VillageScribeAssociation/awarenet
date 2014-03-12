<?php

	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//|	display a single download
//--------------------------------------------------------------------------------------------------
//arg: metafile - relative location of XML file manifest [string]

function p2p_showdownload($args) {
	global $kapenta;
	global $kapenta;
	global $theme;	

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if (false == array_key_exists('metafile', $args)) { return 'Metafile not given.'; }

	$klf = new KLargeFile();
	$klf->metaFile = 'data/p2p/transfer/meta/' . $args['metafile'];

	if (false == $kapenta->fs->exists($klf->metaFile)) { return '(no such file)'; }

	//----------------------------------------------------------------------------------------------
	//	load the manifest
	//----------------------------------------------------------------------------------------------
	$klf->loadMetaXml();

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$html = $klf->path . " (" . $klf->percentComplete() . "%)<br/>";

	return $html;
}

?>
