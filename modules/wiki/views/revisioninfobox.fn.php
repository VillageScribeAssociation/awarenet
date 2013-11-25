<?php

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/revision.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display navbox for an article
//--------------------------------------------------------------------------------------------------
//arg: raUID - UID of a wiki_article object [string]

function wiki_revisioninfobox($args) {
	global $user;
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
	$article->nav = $model->nav;
	$article->expandWikiCode();

	if ('' != trim($article->wikicode->infobox)) {
		$html .= $theme->ntb($atyicle->wikicode->infobox, 'Infobox', 'divRevInfo', 'show');
	}

	return $html;
}

?>
