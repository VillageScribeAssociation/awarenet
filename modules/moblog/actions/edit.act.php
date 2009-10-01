<?

//--------------------------------------------------------------------------------------------------
//	edit a moblog post and associated files/images
//--------------------------------------------------------------------------------------------------
//	users can edit their own blog, admins can do as they please

	require_once($installPath . 'modules/moblog/models/moblog.mod.php');

	//----------------------------------------------------------------------------------------------
	//	check authorisation
	//----------------------------------------------------------------------------------------------

	$authorised = false;

	if ($request['ref'] == '') { do404(); }
	if (authHas('moblog', 'edit', '') == false) { do403(); }	// not authorised to have a blog

	$model = new Moblog();
	if ($model->load($request['ref']) == false) { do404(); }	// no such post

	if ($model->data['createdBy'] == $user->data['UID']) { $authorised = true; } 	// own post
	if ($user->data['ofGroup'] == 'admin') { $authorised = true; }					// admin
	if ($authorised == false) { do403(); }

	//----------------------------------------------------------------------------------------------
	//	show the edit page
	//----------------------------------------------------------------------------------------------	

	$page->load($installPath . 'modules/moblog/actions/edit.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['postUID'] = $model->data['UID'];
	$page->blockArgs['postTitle'] = $model->data['title'];
	$page->blockArgs['userRa'] = $user->data['recordAlias'];
	$page->blockArgs['userName'] = $user->getName();
	$page->render();

?>
