<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save a school record
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('saveRecord' != $_POST['action']) { $page->do404('Action not supported.'); } 
	if (false == array_key_exists('UID', $_POST)) { $page->do404('School not specified (UID).'); }

	$model = new Schools_School($db->addMarkup($_POST['UID']));
	if (false == $model->loaded) { $page->do404("could not load School $UID");}
	if (false == $user->authHas('schools', 'schools_school', 'edit', $model->UID))
		{ $page->do403('You are not authorized to edit this school.'); }


	//----------------------------------------------------------------------------------------------
	//	load and update the object
	//----------------------------------------------------------------------------------------------
	//TODO: sanitize description
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'name':		$model->name = $utils->cleanString($value); 		break;
			case 'description':	$model->description = $value; 						break;
			case 'geocode':		$model->geocode = $utils->cleanString($value); 		break;
			case 'region':		$model->region = $utils->cleanString($value); 		break;
			case 'country':		$model->country = $utils->cleanString($value); 		break;
			case 'hidden':		$model->hidden = $utils->cleanString($value); 		break;
			case 'type':		$model->type = $utils->cleanString($value); 		break;
			case 'notifyall':	$model->notifyAll = $utils->cleanString($value); 	break;
		}
	}
	$report = $model->save();
		
	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $session->msg('Saved changes to school: ' . $model->name); }
	else { $session->msg('Could not save School:<br/>' . $report); }

	if (true == array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
	else { $page->do302('schools/' . $model->alias); }

	$page->do302('schools/' . $model->alias);
		

?>
