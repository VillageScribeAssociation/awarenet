<?

	require_once($installPath . 'modules/pages/models/pages.mod.php');

//--------------------------------------------------------------------------------------------------
//	form for editing pages
//--------------------------------------------------------------------------------------------------------------

function pages_editform($args) {
	global $installPath;

	if ((array_key_exists('xmodule', $args) AND (array_key_exists('xpage', $args)))) {

		$fileName = $installPath . 'modules/' . $args['xmodule'] . '/' . $args['xpage'];
		if (file_exists($fileName) == false) { return false; }

		$p = new Page();
		$p->load($fileName);

		$labels = $p->data;
		$labels['moduleName'] = $args['xmodule'];
		$labels['pageName'] = $args['xpage'];

		// sanitize content (prevent blocks running, </textarea>)
		foreach($labels as $label => $txt) {
			$labels[$label] = str_replace('[', '&#91;', $labels[$label]);			
			$labels[$label] = str_replace(']', '&#93;', $labels[$label]);			
			$labels[$label] = str_replace('<', '&lt;', $labels[$label]);			
			$labels[$label] = str_replace('>', '&gt;', $labels[$label]);
		}

		$block = loadBlock('modules/pages/views/editform.block.php');	// load form
		return replaceLabels($labels, $block);
	}
}


?>