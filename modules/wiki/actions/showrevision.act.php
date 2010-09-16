<?

//--------------------------------------------------------------------------------------------------
//	show current and previous versions of a wiki document
//--------------------------------------------------------------------------------------------------

	require_once($kapenta->installPath . 'modules/wiki/models/article.mod.php');
	require_once($kapenta->installPath . 'modules/wiki/models/revision.mod.php');

	//----------------------------------------------------------------------------------------------
	//	check permissions and reference
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('wiki', 'Wiki_Article', 'show', "TODO:UID here")) { $page->do403(); }
	if ('' == $req->ref) { $page->do404(); }
	if (false == $db->objectExists('Wiki_Revision', $req->ref)) { $page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	revision exists, load it
	//----------------------------------------------------------------------------------------------
	$model = new Wiki_Revision($req->ref);

	//----------------------------------------------------------------------------------------------
	//	render page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/wiki/showrevision.page.php');
	$page->blockArgs['currentRevision'] = $model->UID;
	$page->blockArgs['previousRevision'] = $model->getPrevious();

?>
