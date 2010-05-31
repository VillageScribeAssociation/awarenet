<?

//--------------------------------------------------------------------------------------------------
//	display a gallery page
//--------------------------------------------------------------------------------------------------
	
	if (authHas('forums', 'show', '') == false) { do403(); }		// check basic permissions
	if ($request['ref'] == '') { do404(); }							// check ref

	$UID = raFindRedirect('forums', 'show', 'forums', $request['ref']); 	// check correct ref
	if (raGetOwner($request['ref'], 'forums') == false) { do404(); }  		// check forum exists
	
	require_once($installPath . 'modules/forums/models/forum.mod.php');			// load the model
	$model = new Forum($request['ref']);									

	$pageNo = 1; // if not specified
	if (array_key_exists('page', $request['args']) == true) 
		{ $pageNo = floor($request['args']['page']); }

	$page->load($installPath . 'modules/forums/actions/show.page.php');
	$page->blockArgs['UID'] = $UID;
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['pageno'] = $pageNo;
	$page->blockArgs['school'] = $model->data['school'];
	$page->blockArgs['forumTitle'] = $model->data['title'];
	$page->render();

?>
