<?php

	require_once($kapenta->installPath . 'modules/kjs/inc/parser.inc.php');

//--------------------------------------------------------------------------------------------------
//	temporary / development action to test PHP parser
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }
	
	$view = 'permissions';
	if ('' != $kapenta->request->ref) { $view = $kapenta->request->ref; }

	$fileName = 'modules/users/views/' . $view . ".fn.php";
	header("Content-type: text/plain");

	//----------------------------------------------------------------------------------------------
	//	parse the file and convert to javascript
	//----------------------------------------------------------------------------------------------

	$raw = $kapenta->fs->get($fileName);

	$tokens = kjs_tokenize($raw);

	foreach($tokens as $token) { echo $token['value']; }

	$tokens = kjs_rough_convert_js($tokens);

	echo "\n\n\n --- JAVASCRIPT ---\n";
	foreach($tokens as $token) { echo $token['value']; }

	print_r($tokens);

?>
