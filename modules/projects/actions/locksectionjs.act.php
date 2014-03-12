<?

//--------------------------------------------------------------------------------------------------
//*	locks a section for editing to a particular user, AJAX version
//--------------------------------------------------------------------------------------------------
//postarg: action - set to 'lockSection' [string]
//postarg: UID - UID of Projects_Section object to lock [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->doXmlError("Action not given."); }	
	if ('lockSection' != $_POST['action']) { $kapenta->page->doXmlError('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->doXmlError("No UID given"); }

	$model = new Projects_Section($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->doXmlError("Uknown section."); }

	if (false == $kapenta->user->authHas('projects', 'projects_section', 'edit', $model->UID)) {
		$kapenta->page->doXmlError("Not authorized."); 
	}

	if ($model->lockedBy != '') { $kapenta->page->doXmlError("Already Locked."); }

	//----------------------------------------------------------------------------------------------
	//	lock the section
	//----------------------------------------------------------------------------------------------
	$model->lockedBy = $kapenta->user->UID;
	$model->lockedOn = $kapenta->db->datetime();
	$report = $model->save();

	if ('' != $report) { $kapenta->page->doXmlError($report); }
	return '<ok/>'

?>
