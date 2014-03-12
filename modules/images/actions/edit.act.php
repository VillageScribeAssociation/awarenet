<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	edit an image
//--------------------------------------------------------------------------------------------------
//	needs the image's UID/recordAlias and optionally /return_uploadmultiple
	
	//----------------------------------------------------------------------------------------------
	//	check page arguments and authorisation
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }
	$model = new Images_Image($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404('Image not found.'); }
	//if ('' == $model->fileName) { $kapenta->page->do404(); }
	if (false == $kapenta->user->authHas($model->refModule, $model->refModel, 'images-edit', $model->refUID))
		{ $kapenta->page->do403('You are not authorized to edit this image.'); }
	
	//TODO: add more auth options here

	$return = '';
	if (true == array_key_exists('return', $kapenta->request->args)) { $return = $kapenta->request->args['return']; }
	
	//----------------------------------------------------------------------------------------------
	//	load the page :-)
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/images/actions/edit.if.page.php');
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->blockArgs['return'] = $return;
	$kapenta->page->render();

?>
