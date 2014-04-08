<?

//--------------------------------------------------------------------------------------------------
//|	lessons submenu
//--------------------------------------------------------------------------------------------------

function lessons_menukalite($args) { 
	global $theme;
	global $user;

	$html = '';							//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/lessons/views/menukalite.block.php');

	if ('admin' !== $user->role and 'teacher' !== $user->role) 
	{ 
		$block = $theme->loadBlock('modules/lessons/views/menukalitestudent.block.php');
	}
	//$labels = array();
	//$html = $theme->replaceLabels($labels, $block);
	$html = $block;
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
