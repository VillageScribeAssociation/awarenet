<?

	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');
	require_once($kapenta->installPath . 'modules/projects/models/sections.set.php');

//--------------------------------------------------------------------------------------------------
//*	increment a section's weight
//--------------------------------------------------------------------------------------------------
//postarg: action - set to 'incrementSection'
//postarg: UID - UID of a Projects_Section object [string]

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	//TODO: permission check here

	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->doXmlError("UID not given"); }

	$model = new Projects_Section($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->doXmlError("Section not found."); }

	if (false == $kapenta->user->authHas('projects', 'projects_project', 'edit', $model->projectUID)) {
		$kapenta->page->do403();
	}

	//----------------------------------------------------------------------------------------------
	//	increment the section
	//----------------------------------------------------------------------------------------------
	$set = new Projects_Sections($model->projectUID);
	$check = $set->incWeight($model->UID);
	if (false == $check) { $kapenta->page->doXmlError("Could not increment weight."); }

	echo "<ok/>";
?>
