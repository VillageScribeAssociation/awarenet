<?

	require_once($kapenta->installPath . 'modules/pages/models/page.mod.php');

//--------------------------------------------------------------------------------------------------
//|	list all pages on a module
//--------------------------------------------------------------------------------------------------

function pages_listmodule($args) {
	global $user;
	$html = '';				//%	return value [string]
	
	if ('admin' != $user->role) { return ''; }

	if (true == array_key_exists('module', $args)) {
		$pageList = listPages($args['module']);
		foreach($pageList as $pg) {
			$editUrl = '%%serverPath%%pages/edit/module_' . $args['module'] . '/' . $pg;
			$html .= "\t\t<a href='" . $editUrl . "'>$pg</a><br/>\n";
		}		
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
