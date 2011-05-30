<?

require_once($kapenta->installPath . 'modules/docgen/inc/readcomments.inc.php');

//-------------------------------------------------------------------------------------------------
//|	list and describe all models on a module
//-------------------------------------------------------------------------------------------------
//arg: module - module to display views for [string]

function docgen_moduleabout($args) {
	global $kapenta->installPath;
	if (array_key_exists('module', $args) == false) { return ''; }

	$fileName = $kapenta->installPath . 'modules/' . $args['module'] . '/about.txt';
	if (file_exists($fileName) == true) {
			$about = phpUnComment(implode(file($fileName)));
			$about = trim($about);
			$about = str_replace("\r", '', $about);
			$about = str_replace("<", "&lt;", $about);
			$about = str_replace(">", "&gt;", $about);
			$about = str_replace("\n", "<br/>\n", $about);
			return $about;
	}



	return '';
}

?>
