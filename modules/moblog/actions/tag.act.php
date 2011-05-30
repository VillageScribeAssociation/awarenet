<?

	require_once($kapenta->installPath . 'modules/tags/models/tag.mod.php');

//--------------------------------------------------------------------------------------------------
//*	show all blog posts with a given tag
//--------------------------------------------------------------------------------------------------
//note: $req->ref should be a tag name

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404('No tag given.'); }
	
	$tag = new Tags_Tag($req->ref, true);
	if (false == $tag->loaded) { $page->do404('Tag not recognised.'); }
	//TODO: permissions check here
	//TODO: pagination setup

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/moblog/actions/tag.page.php');
	$page->blockArgs['tagUID'] = $tag->UID;
	$page->blockArgs['tagName'] = $tag->name;
	$page->render();

?>
