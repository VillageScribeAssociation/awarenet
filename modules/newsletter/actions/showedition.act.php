<?

	require_once($kapenta->installPath . 'modules/newsletter/models/edition.mod.php');

//--------------------------------------------------------------------------------------------------
//*	 action to display a single Edition object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404('No Edition specified.'); } 
	$UID = $aliases->findRedirect('newsletter_edition');
	$model = new Newsletter_Edition($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404("Unkown Edition");}

	if (
		(false == $user->authHas('newsletter', 'Newsletter_Edition', 'view', $model->UID)) && 
		(false == array_key_exists('allow', $kapenta->request->args))
	) {
		if ('published' != $model->status) {
			$kapenta->page->do403('You are not authorized to view this Edition.');
		} 
	}

	$attachmentsNav = ''
	 . '[[:live::manageattachments'
	 . '::refModule=newsletter'
	 . '::refModel=newsletter_edition'
	 . '::refUID=' . $model->UID
	 . ':]]';

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/newsletter/actions/showedition.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['raUID'] = $model->UID;
	$kapenta->page->blockArgs['editionUID'] = $model->UID;
	$kapenta->page->blockArgs['attachmentsnav'] = $attachmentsNav;
	//	^ add any further block arguments here
	$kapenta->page->render();

?>
