<?

	require_once($kapenta->installPath . 'modules/images/models/image.mod.php');

//--------------------------------------------------------------------------------------------------
//*	page to display a single image
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	authentication (no public users)
	//----------------------------------------------------------------------------------------------
	if (($user->role == 'public') || ($user->role == 'banned')) { $page->do403(); }
	//TODO: use a permission

	//----------------------------------------------------------------------------------------------
	//	check reference
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }
	$UID = $aliases->findRedirect('images_image');	

	$model = new Images_Image($req->ref);
	if (false == $model->loaded) { $page->do404('Image not found.'); }

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
	$page->load('modules/images/actions/show.page.php');
	$page->blockArgs['raUID'] = $model->alias;
	$page->blockArgs['UID'] = $model->UID;
	$page->blockArgs['title'] = $model->title;
	$page->blockArgs['createdBy'] = $model->createdBy;
	$page->render();

?>
