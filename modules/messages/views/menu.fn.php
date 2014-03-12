<?

	require_once($kapenta->installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//|	menu for forums, no arguments
//--------------------------------------------------------------------------------------------------

function messages_menu($args) {
		global $theme;
		global $kapenta;

	$labels = array();
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	$labels['newEntry'] = '';
	if ($kapenta->user->authHas('messages', 'messages_message', 'send')) 
		{ $labels['newEntry'] = '[[:theme::submenu::label=Compose::link=/messages/compose/:]]'; } 

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------	
	$block = $theme->loadBlock('modules/messages/views/menu.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>
