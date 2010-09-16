<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show an announcement
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	get name of announcement owner
	//----------------------------------------------------------------------------------------------	
	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('Announcements_Announcement');
	$model = new Announcements_Annoucement($req->ref);
	if (false == $user->authHas('announcements', 'Announcements_Announcement', 'show', $UID)) 
		{ $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	get name of announcement owner	//TODO: get rid of this
	//----------------------------------------------------------------------------------------------
	$cb = '[[:'. $model->refModule .'::name::raUID='. $model->refUID .'::link=no:]]';
	$ownerName = $theme->expandBlocks($cb, '');

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/announcements/actions/show.page.php');
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['refUID'] = $model->refUID;
	$page->blockArgs['refModule'] = $model->refModule;
	$page->blockArgs['announceLink'] = $aaLink;
	$page->blockArgs['announcementOwner'] = $ownerName;
	$page->blockArgs['announcementTitle'] = $model->title;
	$page->render();

?>
