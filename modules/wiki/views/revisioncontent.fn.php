<?php

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display content of an article
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID of a wiki_article object [string]

function wiki_revisioncontent($args) {
	global $kapenta;
	global $theme;	
	
	$html = '';						//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('raUID', $args)) { return '(article not specified)'; }

	$model = new Wiki_Revision($args['raUID']);
	if (false == $model->loaded) { return '(article not found: ' . $args['raUID'] . ')'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------

	$article = new Wiki_Article();
	$article->content = $model->content;

	$article->expandWikiCode();
	$html = $article->wikicode->html;

	return $html;
}

?>
