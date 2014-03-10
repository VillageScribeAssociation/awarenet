<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	page to display a single image
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	authentication (no public users)
	//----------------------------------------------------------------------------------------------
	if (($user->role == 'public') || ($user->role == 'banned')) { $kapenta->page->do403(); }
	//TODO: use a permission

	//----------------------------------------------------------------------------------------------
	//	check reference
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }
	$UID = $aliases->findRedirect('images_image');	

	$model = new Images_Image($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404('Image not found.'); }

	//----------------------------------------------------------------------------------------------
	//	bump popularity of this item if viewed by someone other than the creator
	//----------------------------------------------------------------------------------------------
	if ($model->createdBy != $user->UID) {
		$args = array(
			'ladder' => 'images.all',
			'item' => $model->UID
		);

		$kapenta->raiseEvent('popular', 'popularity_bump', $args);
		//TODO: consider adding ladders for modules, users, etc
	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/images/actions/show.page.php');
	$kapenta->page->blockArgs['raUID'] = $model->alias;
	$kapenta->page->blockArgs['UID'] = $model->UID;
	$kapenta->page->blockArgs['title'] = $model->title;
	$kapenta->page->blockArgs['createdBy'] = $model->createdBy;
	$kapenta->page->render();

?>
