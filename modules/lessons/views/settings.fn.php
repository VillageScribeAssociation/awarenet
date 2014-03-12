<?

//--------------------------------------------------------------------------------------------------
//|	makes form for changing lesson module registry settings
//--------------------------------------------------------------------------------------------------

function lessons_settings($args) {
		global $theme;
		global $kapenta;
		global $kapenta;
		global $kapenta;


	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' !== $kapenta->user->role) { return '(not logged in)'; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $kapenta->theme->loadBlock('modules/lessons/views/settings.block.php');

	$labels = array(
		'kalite.installation' => $kapenta->registry->get('kalite.installation'),
		'kalite.admin.user' => $kapenta->registry->get('kalite.admin.user'),
		'kalite.admin.pwd' => $kapenta->registry->get('kalite.admin.pwd'),
		'kalite.db.file' => $kapenta->registry->get('kalite.db.file'),
		'dbexists' => "<div class='chatmessagegreen'>Database file exists</div>"
	);

	if (false === file_exists($kapenta->registry->get('kalite.db.file'))) {
		$labels['dbexists'] = '<div class="chatmessagered">Database file does not exist or cannot be read.</div>';
	}

	$html = $kapenta->theme->replaceLabels($labels, $block); 

	return $html;
}

?>
