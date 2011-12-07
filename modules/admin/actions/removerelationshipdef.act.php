<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');
	require_once($kapenta->installPath . 'core/kmodel.class.php');

//--------------------------------------------------------------------------------------------------
//*	removes a relationship definition (between a user and a model - module.xml.php)
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check POST vars and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	$modulename = '';		//%	name of a kapenta module [string]
	$modelname = '';		//%	name of object type [string]
	$relationship = '';		//%	name of relationship between user and object [string]

	if (true == array_key_exists('module', $_POST)) { $modulename = $_POST['module']; }
	if (true == array_key_exists('model', $_POST)) { $modelname = $_POST['model']; }
	if (true == array_key_exists('relationship', $_POST)) { 
		$relationship = $utils->cleanTitle($_POST['relationship']); 
	}

	if (true == array_key_exists('module', $req->args)) { $modulename = $req->args['module']; }
	if (true == array_key_exists('model', $req->args)) { $modelname = $req->args['model']; }
	if (true == array_key_exists('relationship', $req->args)) { 
		$relationship = $utils->cleanTitle($req->args['relationship']); 
	}

	if ('' == trim($relationship)) { $page->do404('Relationship not specified.'); }

	$module = new KModule($modulename);
	if (false == $module->loaded) { $page->do404('Unkown module.'); }
	if (false == $module->hasModel($modelname)) { $page->do404('Unknown model.'); }

	$model = new KModel();
	$model->loadArray($module->models[$modelname]);

	$check = $model->removeRelationship($relationship);
	if (true == $check) {
		$session->msg('Removed relationship: ' . $relationship, 'ok');
		$module->models[$modelname] = $model->toArray();
		$module->save();

	} else { 
		$session->msg('Could not remove relationship.', 'bad'); 
	}

	//----------------------------------------------------------------------------------------------
	//	redirect back to /editmodule/
	//----------------------------------------------------------------------------------------------
	$page->do302('admin/editmodule/' . $modulename);

?>
