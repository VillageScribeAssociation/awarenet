<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');

//--------------------------------------------------------------------------------------------------
//*	display complete revision history for a given wiki article
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('wiki_article');
	$model = new Wiki_Article($UID);
	if (false == $model->loaded) { $page->do404('no such wiki article'); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/wiki/actions/history.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['raUID'] = $req->ref;
	$page->blockArgs['articleTitle'] = $model->title;
	$page->render();

?>
