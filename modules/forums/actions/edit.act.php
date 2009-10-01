<?

//--------------------------------------------------------------------------------------------------
//	edit an image forums
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	load the model
	//----------------------------------------------------------------------------------------------

	if (authHas('forums', 'edit', '') == false) { do403(); }			// check basic permissions
	if ($request['ref'] == '') { do404(); }								// check for ref
	
	require_once($installPath . 'modules/forums/models/forum.mod.php');

	$model = new Forum();
	if ($model->load($request['ref']) == false)  { do404(); }

	//----------------------------------------------------------------------------------------------
	//	check permissions (must be site admin or forum moderator)
	//----------------------------------------------------------------------------------------------

	$auth = false;
	if ($user->data['ofGroup'] == 'admin') { $auth = true; }
	foreach($model->moderators as $modUID) { if ($modUID == $user->data['UID']) { $auth = true; } }
	// possibly more to come here...
	if ($auth == false) { do404(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------

	$page->load($installPath . 'modules/forums/actions/edit.page.php');
	$page->blockArgs['UID'] = $model->data['UID'];
	$page->blockArgs['raUID'] = $model->data['recordAlias'];
	$page->render();

?>
