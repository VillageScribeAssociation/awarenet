<?

//--------------------------------------------------------------------------------------------------
//|	makes form for changing lesson module registry settings
//--------------------------------------------------------------------------------------------------

function lessons_settings($args) {
	global $theme, $user, $registry, $kapenta;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/lessons/views/settings.block.php');

	$labels = array(
		'addPresetForm' => $theme->loadBlock('modules/lessons/views/addpresetform.block.php'),
		'kalite.installation' => $kapenta->registry->get('kalite.installation'),
		'kalite.admin.user' => $kapenta->registry->get('kalite.admin.user'),
		'kalite.admin.pwd' => $kapenta->registry->get('kalite.admin.pwd'),
		'kalite.db.file' => $kapenta->registry->get('kalite.db.file')
	);

	$html = $theme->replaceLabels($labels, $block); 

	return $html;
}

?>
