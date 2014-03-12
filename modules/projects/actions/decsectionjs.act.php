<?

	require_once($kapenta->installPath . 'modules/projects/models/sections.set.php');
	require_once($kapenta->installPath . 'modules/projects/models/section.mod.php');

//--------------------------------------------------------------------------------------------------
//*	decrement a section's weight
//--------------------------------------------------------------------------------------------------
//postarg: action - set to 'decrementSection'
//postarg: UID - UID of a Projects_Section object [string]

	//----------------------------------------------------------------------------------------------
	//	check POST vars and permissions
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('action not given'); }
	if ('incrementSection' != $_POST['action']) { $kapenta->page->do404('action not recognized'); }

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
	$check = $set->decWeight($model->UID);
	if (false == $check) { $kapenta->page->doXmlError("Could not increment weight."); }

	echo "<ok/>";
?>
