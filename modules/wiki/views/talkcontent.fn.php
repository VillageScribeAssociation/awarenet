<?php

	require_once($kapenta->installPath . 'modules/wiki/models/talk.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display content of an article
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID of a wiki_article object [string]

function wiki_talkcontent($args) {
	global $kapenta;
	global $theme;	
	
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return '(article not specified)'; }

	$model = new Wiki_Talk($args['raUID']);
	if (false == $model->loaded) { return '(article not found: ' . $args['raUID'] . ')'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$model->expandWikiCode();
	$html = '<h1>' . $model->title . '</h1>'
	 . $model->wikicode->html;

	return $html;
}

?>
