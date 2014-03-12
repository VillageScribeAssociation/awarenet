<?

	require_once($kapenta->installPath . 'modules/users/models/friendship.mod.php');
	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//|	make iframe to search for friends
//--------------------------------------------------------------------------------------------------
//opt: cbjs - optional javascript function to call with the user UID [string]
//opt: cblabel - optional label for the callback function [string]
//opt: cbicon - TODO [string]

function users_searchbox($args) {
		global $kapenta;
		global $theme;

	$html = '';							//%	return value [string]
	$cbjs = '';							//%	js function to call when a result is clicked [string]
	$cblabel = '';						//%	alt text for search result button [string]
	$cbicon = 'arrow_left_green.png';	//%	result button image [string]				

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if (false == $kapenta->user->authHas('users', 'users_user', 'show')) { return ''; }
	if (true == array_key_exists('cbjs', $args)) { $cbjs = $args['cbjs']; }
	if (true == array_key_exists('cblabel', $args)) { $cblabel = $args['cblabel']; }
	if (true == array_key_exists('cbicon', $args)) { $cbicon = $args['cbicon']; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/users/views/searchbox.block.php');
	$labels = array(
		'cbjs' => $cbjs,
		'cblabel' => $cblabel,
		'cbicon' => $cbicon
	);

	$html = $theme->replacelabels($labels, $block);

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
