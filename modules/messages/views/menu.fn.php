<?

	require_once($installPath . 'modules/messages/models/message.mod.php');

//--------------------------------------------------------------------------------------------------
//	menu for forums, no arguments
//--------------------------------------------------------------------------------------------------

function messages_menu($args) {
	$labels = array();
	if (authHas('messages', 'send', '')) {
		$labels['newEntry'] = '[[:theme::submenu::label=Compose::link=/messages/compose/:]]';
	} else { $labels['newEntry'] = ''; }
	
	$html = replaceLabels($labels, loadBlock('modules/messages/views/menu.block.php'));
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>