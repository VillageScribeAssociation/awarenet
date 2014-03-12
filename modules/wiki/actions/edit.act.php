<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a wiki article
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }				// check for ref	
	//$UID = $aliases->findRedirect('wiki_article');			// check correct ref
	$model = new Wiki_Article($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404('Article not found.'); }
	if (false == $kapenta->user->authHas('wiki', 'wiki_article', 'edit', $model->UID)) { $kapenta->page->do403(); }
	
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/wiki/actions/edit.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['raUID'] = $model->UID;
	$kapenta->page->render();

?>
