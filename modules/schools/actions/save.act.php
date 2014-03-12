<?

	require_once($kapenta->installPath . 'modules/schools/models/school.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save a school record
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('saveRecord' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); } 
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('School not specified (UID).'); }

	$model = new Schools_School($kapenta->db->addMarkup($_POST['UID']));
	if (false == $model->loaded) { $kapenta->page->do404("could not load School $UID");}
	if (false == $kapenta->user->authHas('schools', 'schools_school', 'edit', $model->UID))
		{ $kapenta->page->do403('You are not authorized to edit this school.'); }


	//----------------------------------------------------------------------------------------------
	//	load and update the object
	//----------------------------------------------------------------------------------------------
	//TODO: sanitize description
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'name':		$model->name = $utils->cleanTitle($value); 			break;
			case 'description':	$model->description = $utils->cleanHtml($value);	break;
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
	if ('' == $report) { $kapenta->session->msg('Saved changes to school: ' . $model->name); }
	else { $kapenta->session->msg('Could not save School:<br/>' . $report); }

	if (true == array_key_exists('return', $_POST)) { $kapenta->page->do302($_POST['return']); }
	else { $kapenta->page->do302('schools/' . $model->alias); }

	$kapenta->page->do302('schools/' . $model->alias);
		

?>
