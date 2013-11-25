<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show talk page for a given article 
//--------------------------------------------------------------------------------------------------
//+	NOTE! talks pages are now articles in a different namespace
//+	The refrence that is givne should be to the prant (main) atricle, to which the talk page 
//+	belongs, not to the talk page itself.

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404(); } 
	$UID = $aliases->findRedirect('wiki_article');

	$model = new Wiki_Article($UID);
	if (false == $model->loaded) { $page->do404('Could not load parent article.'); }
	if (false == $user->authHas('wiki', 'wiki_article', 'show', $model->UID)) { $page->do403(); }

	$kapenta->page->load('modules/wiki/actions/talk.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['raUID'] = $model->UID;
	$kapenta->page->blockArgs['articleTitle'] = $model->title;
	$kapenta->page->blockArgs['articleAlias'] = $model->alias;
	$kapenta->page->blockArgs['parentUID'] = $model->UID;

	$kapenta->page->render();

?>
