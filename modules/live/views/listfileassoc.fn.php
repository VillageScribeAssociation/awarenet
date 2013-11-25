<?

//--------------------------------------------------------------------------------------------------
//|	list module file associations recorded in registry
//--------------------------------------------------------------------------------------------------
//opt: module - name of a kapenta module to filter results for [string]

function live_listfileassoc($args) {
	global $user;
	global $theme;
	global $kapenta;

	$module = '';
	$html = '';	

	//----------------------------------------------------------------------------------------------
	//	check user role and arguments
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (true == array_key_exists('module', $args)) { $module = $args['module']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[] = array('File Extension', 'Module', '[x]');

	$delFormBlock = $theme->loadBlock('modules/live/views/delfileassocform.block.php');

	$assoc = array();
	$reg = $kapenta->registry->search('live', 'live.file.');

	foreach($reg as $key => $value) {
		if (('' == $module) || ($value == $module)) {
			$ext = str_replace('live.file.', '', $key);
			$assoc[$ext] = $value;
		}
	}

	ksort($assoc);

	foreach($assoc as $ext => $modName) {
			$modLink = "<a href='%%serverPath%%" . $modName . "/settings/'>$modName</a>";
			$delForm = str_replace('%%ext%%', $ext, $delFormBlock);
			$table[] = array($ext, $modLink, $delForm);
	}
	
	$html = $theme->arrayToHtmlTable($table, true, true);

	if (0 == count($assoc)) { $html .= "(no file associations recorded)<br/>"; }

	return $html;
}

?>
