<?

//--------------------------------------------------------------------------------------------------
//	show an announcement
//--------------------------------------------------------------------------------------------------
	
	if ($request['ref'] == '') { do404(); }
	raFindRedirect('announcements', 'show', 'announcements', $request['ref']);
	require_once($installPath . 'modules/announcements/models/announcements.mod.php');
	$model = new Announcement($request['ref']);

	//----------------------------------------------------------------------------------------------
	//	get name of announcement owner
	//----------------------------------------------------------------------------------------------

	$cb = '[[:'. $model->data['refModule'] .'::name::raUID='. $model->data['refUID'] .'::link=no:]]';
	$ownerName = expandBlocks($cb, '');

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/announcements/actions/show.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['UID'] = raGetOwner('announcements', $request['ref']);
	$page->blockArgs['refUID'] = $model->data['refUID'];
	$page->blockArgs['refModule'] = $model->data['refModule'];
	$page->blockArgs['announceLink'] = $aaLink;
	$page->blockArgs['announcementOwner'] = $ownerName;
	$page->blockArgs['announcementTitle'] = $model->data['title'];
	$page->render();

?>
