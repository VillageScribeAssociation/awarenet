<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show an announcement
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	get name of announcement owner
	//----------------------------------------------------------------------------------------------	
	if ('' == $kapenta->request->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('announcements_announcement');
	$model = new Announcements_Announcement($kapenta->request->ref);
	if (false == $user->authHas('announcements', 'announcements_announcement', 'show', $UID)) 
		{ $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	get name of announcement owner	//TODO: get rid of this
	//----------------------------------------------------------------------------------------------
	$cb = '[[:'. $model->refModule .'::name::raUID='. $model->refUID .'::link=no:]]';
	$ownerName = $theme->expandBlocks($cb, '');

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/announcements/actions/show.page.php');
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['refUID'] = $model->refUID;
	$kapenta->page->blockArgs['refModule'] = $model->refModule;
	$kapenta->page->blockArgs['announceLink'] = $ownerName;
	$kapenta->page->blockArgs['announcementOwner'] = $ownerName;
	$kapenta->page->blockArgs['announcementTitle'] = $model->title;
	$kapenta->page->render();

?>
