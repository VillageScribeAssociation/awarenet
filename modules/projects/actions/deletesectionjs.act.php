<?

	require_once($kapenta->installPath . 'modules/projects/models/project.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/sections.set.php');
	require_once($kapenta->installPath . 'modules/projects/models/changes.set.php');

//--------------------------------------------------------------------------------------------------
//*	remove a project section
//--------------------------------------------------------------------------------------------------
//postarg: action - set to 'deleteSection' [string]
//postarg: UID - UID of a Project_Sections object [string]
//+	
//+	Note that sections are no longer actually deleted, they are simply hidden so that they can be
//+	undeleted from the project's history.

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->doXmlError("Action not given"); }
	if ('deleteSection' != $_POST['action']) { $kapenta->page->doXmlError('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->doXmlError("Section UID not given"); }

	$model = new Projects_Section($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->doXmlError('Section not found.'); }

	//TODO: permissions check here

	//----------------------------------------------------------------------------------------------
	//	hide the section
	//----------------------------------------------------------------------------------------------
	$model->hidden = 'yes';
	$report = $model->save();
	if ('' == $report) { 

		$changes = new Projects_Changes($model->projectUID, $model->UID);
		$check = $changes->add('s.del', "Removed section:", $model->UID);
		if ('' == $check) {
			// nothing to add here yet, notification maybe?
		} else {
			$kapenta->page->doXmlError('Could not save revision.');
		}

	} else {
		$kapenta->page->doXmlError('Could not delete section.');
	}

	//----------------------------------------------------------------------------------------------
	//	fix section weights
	//----------------------------------------------------------------------------------------------
	$set = new Projects_Sections($model->projectUID);
	$set->checkWeights();

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	echo "<ok/>";

?>
