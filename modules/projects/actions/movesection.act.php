<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	change the order (weight) of sections relative to one another
//--------------------------------------------------------------------------------------------------
//TODO: re-implement for current setup (sections as independant objects) or remove

	//----------------------------------------------------------------------------------------------
	//	check reference, args and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $req->ref) { $page->do404(); }
	if (array_key_exists('move', $req->args) == false) { $page->do404(); }
	if (array_key_exists('section', $req->args) == false) { $page->do404(); } 

	$model = new Projects_Project($req->ref);
	if (false == $model->loaded) { $page->do404(); }

	// check section exists
	$sectionUID = $req->args['section'];
	if (false == array_key_exists($sectionUID, $model->sections) == false) { $page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	check user is authorised to edit this project
	//----------------------------------------------------------------------------------------------
	$authorised = false;
	if ($model->hasMember($user->UID) == true) { $authorised = true; }
	if ('admin' == $user->role) { $authorised = true; }

	if ($authorised == false) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	move the section up (decrease weight)
	//----------------------------------------------------------------------------------------------

	if ($req->args['move'] == 'up') {
		$currWeight = $model->sections[$sectionUID]['weight'];

		// find section with next lowest weight
		foreach($model->sections as $sUID => $section) {
			if ($section['weight'] == ($currWeight - 1)) { 

				// exchange weights
				$model->sections[$sectionUID]['weight'] = $model->sections[$sUID]['weight'];
				$model->sections[$sUID]['weight'] = $currWeight;

				// sort sections by weight and save the project
				$model->sort();
				$model->save();
				break;
			}
		}
		$page->do302('projects/edit/' . $model->alias);
	} 

	//----------------------------------------------------------------------------------------------
	//	move the section down (increase weight)
	//----------------------------------------------------------------------------------------------

	if ($req->args['move'] == 'down') {
		$currWeight = $model->sections[$sectionUID]['weight'];

		// find section with next heighest weight
		foreach($model->sections as $sUID => $section) {
			if ($section['weight'] == ($currWeight + 1)) { 

				// exchange weights
				$model->sections[$sectionUID]['weight'] = $model->sections[$sUID]['weight'];
				$model->sections[$sUID]['weight'] = $currWeight;

				// sort sections by weight and save the project
				$model->sort();
				$model->save();
				break;
			}
		}

		$page->do302('projects/edit/' . $model->alias);
	} 

	//----------------------------------------------------------------------------------------------
	//	if unhandled
	//----------------------------------------------------------------------------------------------
	$page->do404();

?>
