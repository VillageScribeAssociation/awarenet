<?php

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display navbox for an article
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID of a wiki_article object [string]

function wiki_articleinfobox($args) {
	global $user;
	global $theme;	
	
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return '(article not specified)'; }

	$model = new Wiki_Article($args['raUID']);
	if (false == $model->loaded) { return '(article not found: ' . $args['raUID'] . ')'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$model->expandWikiCode();

	if ('' != trim($model->wikicode->infobox)) {
		$html .= $theme->ntb($model->wikicode->infobox, 'Infobox', 'divWikiInfo', 'show');
	}

	return $html;
}

?>
