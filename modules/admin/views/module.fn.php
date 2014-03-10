<?

	require_once($kapenta->installPath . 'core/kmodule.class.php');
	require_once($kapenta->installPath . 'core/kmodel.class.php');

//--------------------------------------------------------------------------------------------------
//	display module details, as read from module.xml.php
//--------------------------------------------------------------------------------------------------
//arg: modulename - name of a kapenta module [string]

function admin_module($args) {
		global $user;
		global $theme;

	$html = '';			//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (false == array_key_exists('modulename', $args)) { return '(modulename not given)'; }
	
	$module = new KModule($args['modulename']);
	if (false == $module->loaded) { return '(no such module)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	
	$html .= "<p><b>Description: </b>" . $module->description . "</p>\n";

	foreach($module->models as $model) {
		$html .= "<h2>" . $model['name'] . "</h2>";
	}

	$html .= "";

	return $html;
}

?>
