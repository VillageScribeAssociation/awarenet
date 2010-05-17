<?

//--------------------------------------------------------------------------------------------------
//	edit an announcement and associated files/images
//--------------------------------------------------------------------------------------------------

	if ($request['ref'] == '') { do404(); }
	if (authHas('announcements', 'edit', '') == false) { do403(); }
	require_once($installPath . 'modules/announcements/models/announcement.mod.php');

	//----------------------------------------------------------------------------------------------
	//	check user is authorised to edit this
	//----------------------------------------------------------------------------------------------

	$model = new Announcement($request['ref']);
	$cb = "[[:". $model->data['refModule'] ."::haseditauth::raUID=".  $model->data['refUID'] .":]]";
	$result = expandBlocks($cb, '');
	if ('yes' != $result) { do403(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/announcements/actions/edit.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->render();

?>
