<?

//--------------------------------------------------------------------------------------------------------------
//	edit an file
//--------------------------------------------------------------------------------------------------------------
//	needs the file's UID/recordAlias and optionally /return_uploadmultiple
	
	require_once($installPath . 'modules/files/models/file.mod.php');
	
	//------------------------------------------------------------------------------------------------------
	//	check page arguments and authorisation
	//------------------------------------------------------------------------------------------------------
	if ($request['ref'] == '') { do404(); }
	$f = new file($request['ref']);
	if ($f->data['fileName'] == '') { do404(); }
	if (authHas($f->data['refModule'], 'files', '') == false) { do403(); }
	
	$return = '';
	if (array_key_exists('return', $request['args'])) { $return = $request['args']['return']; }
	
	//------------------------------------------------------------------------------------------------------
	//	load the page :-)
	//------------------------------------------------------------------------------------------------------
	$page->load($installPath . 'modules/files/actions/edit.if.page.php');
	$page->blockArgs['raUID'] = $request['ref'];
	$page->blockArgs['return'] = $return;
	$page->render();

?>
