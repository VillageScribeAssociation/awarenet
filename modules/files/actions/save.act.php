<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to a Files_File object
//--------------------------------------------------------------------------------------------------

	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not given.', true); }
	if ('savefile' != $_POST['action']) { $page->do404('Unkown action requested.', true); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not given.', true); }

	//----------------------------------------------------------------------------------------------
	//	check reference and authorisation
	//----------------------------------------------------------------------------------------------
	   
	$model = new Files_File($_POST['UID']);
	if (false == $model->loaded) { $page->do404('File not found.', true); }
		
	if (false == $user->authHas($model->refModule, $model->refModel, 'files-edit', $model->refUID)) 
			{ $page->do403('Not authorized', true); }

	//TODO: more permission options here

	//----------------------------------------------------------------------------------------------
	//	make the changes
	//----------------------------------------------------------------------------------------------
	foreach($_POST as $key => $value) {
		switch($key) {
			case 'title':			$model->title = $value;				break;
			case 'caption':			$model->caption = $value;			break;
			case 'licence':			$model->licence = $value;			break;
			case 'attribName':		$model->attribName = $value;		break;
			case 'attribUrl':		$model->attribUrl = $value;			break;
		}
	}

	$report = $model->save();
	if ('' != $report) { $session->msg('Changes could not be saved:<br/>' . $report, 'bad'); }

	//----------------------------------------------------------------------------------------------
	//	redirect back
	//----------------------------------------------------------------------------------------------
		
	if (true == array_key_exists('return', $_POST)) {
		if ('uploadmultiple' == $_POST['return']) {
			$retUrl = 'files/uploadmultiple'
				 . '/refModule_' . $model->refModule 
				 . '/refModel_' . $model->refModel
				 . '/refUID_' . $model->refUID . '/';
			$page->do302($retUrl);
		}
	}

	$page->do302('files/edit/' . $model->alias);

?>
