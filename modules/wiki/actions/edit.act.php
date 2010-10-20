<?

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit a wiki article
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }				// check for ref	
	//$UID = $aliases->findRedirect('Wiki_Article');			// check correct ref
	$model = new Wiki_Article($req->ref);
	if (false == $model->loaded) { $page->do404('Article not found.'); }
	if (false == $user->authHas('wiki', 'Wiki_Article', 'edit', $model->UID)) { $page->do403(); }
	
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/wiki/actions/edit.page.php');
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['raUID'] = $model->UID;
	$page->render();

?>
