<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');
	require_once($kapenta->installPath . 'core/kmodel.class.php');

//--------------------------------------------------------------------------------------------------
//*	form to manage relationship definitions (module.xml.php)
//--------------------------------------------------------------------------------------------------
//arg: modulename - name of a kapenta module [string]
//arg: modelname - name of an object type [string]

function admin_editrelationshipdefs($args) {
	global $kapenta;
	global $theme;

	$modulename = '';		//%	name of a kapenta module [string]
	$modelname = '';		//%	name of an object type [string]
	$html = '';				//%	return value [html]

	//----------------------------------------------------------------------------------------------
	//	check user role and arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
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
	//$block = $theme->loadBlock('modules/admin/views/delrelationship.block.php');

	if (0 == count($model->relationships)) { 
		return "<div class='inlinequote'>No relationships known.</div>";
	}

	$table = array();
	$table[] = array('Relationship', '[x]');
	foreach($model->relationships as $relationship) {
		$delUrl = '%%serverPath%%admin/removerelationshipdef'
			. '/module_' . $modulename
			. '/model_' . $modelname
			. '/relationship_' . $relationship . '/';

		$delLink = "<a href='" . $delUrl . "'>[remove]</a>";

		$table[] = array($relationship, $delLink);
	}

	$html .= $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
