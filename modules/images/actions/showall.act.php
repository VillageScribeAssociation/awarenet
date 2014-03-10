<?

//--------------------------------------------------------------------------------------------------
//*	display all images in the database ordered by dat added to the system
//--------------------------------------------------------------------------------------------------
//TODO: expand this as an administrative option too monitor user-generated content and activity

	//----------------------------------------------------------------------------------------------
	//	check permissions and any arguments
	//----------------------------------------------------------------------------------------------
	if (false == $user->authHas('images', 'images_image', 'list')) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/images/actions/showall.page.php');
	$kapenta->page->allowBlockArgs('page,refMod');
	$kapenta->page->render();

?>
