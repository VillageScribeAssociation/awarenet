<?

//--------------------------------------------------------------------------------------------------
//|	form for searching this peer's codebase
//--------------------------------------------------------------------------------------------------
//opt: searchqB64 - search term base 64 encoded [string]
//opt: excludeqB64 - search term base 64 encoded [string]

function admin_codesearchform($args) {
		global $user;
		global $theme;

	$html = '';			//%	return value [string]
	$searchq = '';		//%	query to search for [string]
	$excludeq = '';		//%	exclude lines conatining this string [string]	

	//----------------------------------------------------------------------------------------------
	//	check args and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	if (true == array_key_exists('searchqB64', $args)) 
		{ $searchq = base64_decode($args['searchqB64']; }

	if (true == array_key_exists('excludeqB64', $args)) 
		{ $excudeq = base64_decode($args['excludeqB64']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/admin/views/codesearchform.block.php');
	$labels = array('searchq' => $searchq, 'excludeq' => $excludeq);
	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
