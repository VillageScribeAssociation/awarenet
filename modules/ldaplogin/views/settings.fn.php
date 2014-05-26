<?

//--------------------------------------------------------------------------------------------------
//|	makes form for changing ldap login module registry settings
//--------------------------------------------------------------------------------------------------

function ldaplogin_settings($args) {
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
	$block = $kapenta->theme->loadBlock('modules/ldaplogin/views/settings.block.php');

	$labels = array(
		'ldaplogin.server' => $kapenta->registry->get('ldaplogin.server'),
		'ldaplogin.port' => $kapenta->registry->get('ldaplogin.port'),
		'ldaplogin.school' => $kapenta->registry->get('ldaplogin.school'),
		'ldaplogin.schoolshortname' => $kapenta->registry->get('ldaplogin.schoolshortname'),
	);

	$html = $kapenta->theme->replaceLabels($labels, $block); 

	return $html;
}


?>
