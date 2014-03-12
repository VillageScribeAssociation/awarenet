<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');

//--------------------------------------------------------------------------------------------------
//*	change the order (weight) of sections relative to one another
//--------------------------------------------------------------------------------------------------
//TODO: re-implement for current setup (sections as independant objects) or remove

	//----------------------------------------------------------------------------------------------
	//	check reference, args and permissions
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->request->ref) { $kapenta->page->do404(); }
	if (array_key_exists('move', $kapenta->request->args) == false) { $kapenta->page->do404(); }
	if (array_key_exists('section', $kapenta->request->args) == false) { $kapenta->page->do404(); } 

	$model = new Projects_Project($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404(); }

	// check section exists
	$sectionUID = $kapenta->request->args['section'];
	if (false == array_key_exists($sectionUID, $model->sections) == false) { $kapenta->page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	check user is authorised to edit this project
	//----------------------------------------------------------------------------------------------
	$authorised = false;
	if ($model->hasMember($kapenta->user->UID) == true) { $authorised = true; }
	if ('admin' == $kapenta->user->role) { $authorised = true; }

	if ($authorised == false) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	move the section up (decrease weight)
	//----------------------------------------------------------------------------------------------

	if ($kapenta->request->args['move'] == 'up') {
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
		$kapenta->page->do302('projects/edit/' . $model->alias);
	} 

	//----------------------------------------------------------------------------------------------
	//	move the section down (increase weight)
	//----------------------------------------------------------------------------------------------

	if ($kapenta->request->args['move'] == 'down') {
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

		$kapenta->page->do302('projects/edit/' . $model->alias);
	} 

	//----------------------------------------------------------------------------------------------
	//	if unhandled
	//----------------------------------------------------------------------------------------------
	$kapenta->page->do404();

?>
