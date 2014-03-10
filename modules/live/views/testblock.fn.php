<?

//--------------------------------------------------------------------------------------------------
//|	block to test ajax load 
//--------------------------------------------------------------------------------------------------

function live_testblock($args) {
		global $user;
		global $theme;
		global $db;

	$html = '';

	$block = $theme->loadBlock('modules/live/views/testblock.block.php');

	$labels = array(
		'userLink' => $user->getNameLink(),
		'datetime' => $db->datetime()
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
