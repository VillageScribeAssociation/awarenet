<?

	require_once($kapenta->installPath . 'modules/tags/models/tag.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show all blog posts with a given tag
//--------------------------------------------------------------------------------------------------
//note: $kapenta->request->ref should be a tag name

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $page->do404('No tag given.'); }
	
	$tag = new Tags_Tag($kapenta->request->ref, true);
	if (false == $tag->loaded) { $page->do404('Tag not recognised.'); }
	//TODO: permissions check here
	//TODO: pagination setup

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/moblog/actions/tag.page.php');
	$kapenta->page->blockArgs['tagUID'] = $tag->UID;
	$kapenta->page->blockArgs['tagName'] = $tag->name;
	$kapenta->page->render();

?>
