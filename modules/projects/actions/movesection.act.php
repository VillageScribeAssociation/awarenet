<?


//--------------------------------------------------------------------------------------------------
//	change the order (weight) of sections relative to one another
//--------------------------------------------------------------------------------------------------

	require_once($installPath . 'modules/projects/models/project.mod.php');

	if ($request['ref'] == '') { do404(); }
	if (array_key_exists('move', $request['args']) == false) { do404(); }
	if (array_key_exists('section', $request['args']) == false) { do404(); } 

	$projectUID = raGetOwner($request['ref'], 'projects');
	$sectionUID = $request['args']['section'];

	if (dbRecordExists('projects', $projectUID) == false) { do404(); }	// no such project
	$model = new Project($projectUID);

	if (array_key_exists($sectionUID, $model->sections) == false) { do404(); } // no such section

	//----------------------------------------------------------------------------------------------
	//	check user is authorised to edit this project
	//----------------------------------------------------------------------------------------------

	$authorised = false;
	if ($model->isMember($user->data['UID']) == true) { $authorised = true; }
	if ($user->data['ofGroup'] == 'admin') { $authorised = true; }

	if ($authorised == false) { do403(); }

	//----------------------------------------------------------------------------------------------
	//	move the section up (decrease weight)
	//----------------------------------------------------------------------------------------------

	if ($request['args']['move'] == 'up') {
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
		do302('projects/edit/' . $model->data['recordAlias']);
	} 

	//----------------------------------------------------------------------------------------------
	//	move the section down (increase weight)
	//----------------------------------------------------------------------------------------------

	if ($request['args']['move'] == 'down') {
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

		do302('projects/edit/' . $model->data['recordAlias']);
	} 

	//----------------------------------------------------------------------------------------------
	//	if unhandled
	//----------------------------------------------------------------------------------------------
	do404();

?>
