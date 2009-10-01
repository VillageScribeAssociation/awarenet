<?

//--------------------------------------------------------------------------------------------------
//	edit an image
//--------------------------------------------------------------------------------------------------
//	needs the image's UID/recordAlias and optionally /return_uploadmultiple
	
	require_once($installPath . 'modules/images/models/image.mod.php');
	
	//----------------------------------------------------------------------------------------------
	//	check page arguments and authorisation
	//----------------------------------------------------------------------------------------------
	if ($request['ref'] == '') { do404(); }
	$i = new Image($request['ref']);
	if ($i->data['fileName'] == '') { do404(); }
	if (authHas($i->data['refModule'], 'images', '') == false) { do403(); }
	
	$return = '';
	if (array_key_exists('return', $request['args'])) { $return = $request['args']['return']; }
	
	//----------------------------------------------------------------------------------------------
	//	load the page :-)
	//----------------------------------------------------------------------------------------------
	$page->load($installPath . 'modules/images/actions/edit.if.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['return'] = $return;
	$page->render();

?>
