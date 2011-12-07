<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');
	require_once($kapenta->installPath . 'core/kmodel.class.php');

//--------------------------------------------------------------------------------------------------
//*	form to manage permission definitions (module.xml.php)
//--------------------------------------------------------------------------------------------------
//arg: modulename - name of a kapenta module [string]
//arg: modelname - name of an object type [string]

function admin_editpermissiondefs($args) {
	global $user;
	global $theme;

	$modulename = '';		//%	name of a kapenta module [string]
	$modelname = '';		//%	name of an object type [string]
	$html = '';				//%	return value [html]

	//----------------------------------------------------------------------------------------------
	//	check user role and arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (true == array_key_exists('modulename', $args)) { $modulename = $args['modulename']; }
	if (true == array_key_exists('modelname', $args)) { $modelname = $args['modelname']; }

	if ('' == trim($modelname)) { return '(model name not given)'; }

	$module = new KModule($modulename);
	if (false == $module->loaded) { return "(unkown moduule: $modulename)"; }
	if (false == $module->hasModel($modelname)) { return '(unkown model)'; }

	$model = new KModel();
	$model->loadArray($module->models[$modelname]);

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	//NOTE: strictly, this should be a POST operation

	if ((0 == count($model->permissions)) && (0 == count($model->export))) { 
		return "<div class='inlinequote'>No permissions known.</div>";
	}

	$table = array();
	$table[] = array('Permission', 'Export', '[x]');

	foreach($model->permissions as $permission) {
		$delUrl = '%%serverPath%%admin/removepermissiondef'
			. '/module_' . $modulename
			. '/model_' . $modelname
			. '/permission_' . $permission . '/';

		$delLink = "<a href='" . $delUrl . "'>[remove]</a>";

		$table[] = array($permission, 'no', $delLink);
	}

	foreach($model->export as $permission) {
		$delUrl = '%%serverPath%%admin/removepermissiondef'
			. '/module_' . $modulename
			. '/model_' . $modelname
			. '/permission_' . $permission . '/';

		$delLink = "<a href='" . $delUrl . "'>[remove]</a>";

		$table[] = array($permission, 'yes', $delLink);
	}

	$html .= $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
