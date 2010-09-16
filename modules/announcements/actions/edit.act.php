<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit an announcement and associated files/images
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and permisisons
	//----------------------------------------------------------------------------------------------

	if ('' == $req->ref) { $page->do404(); }
	$model = new Announcements_Announcement($req->ref);
	if (false == $model->loaded) { $page->do404(); }
	if (false == $user->authHas('announcements', 'Announcements_Announcement', 'edit', $model->UID))
		{ $page->do403('You are not authorized to edit this announcement.'); }

	//$cb = "[[:". $model->refModule ."::haseditauth::raUID=".  $model->refUID .":]]";
	//$result = expandBlocks($cb, '');
	//if ('yes' != $result) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/announcements/actions/edit.page.php');
	$page->blockArgs['raUID'] = $req->ref;
	$page->render();

?>
