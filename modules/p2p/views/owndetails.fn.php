<?php

//-------------------------------------------------------------------------------------------------
//|	display some basic infomation about this peer
//-------------------------------------------------------------------------------------------------
//: note - only admins can see this

function p2p_owndetails($args) {
	global $kapenta;
	global $kapenta;
	global $theme;

	//---------------------------------------------------------------------------------------------
	//	check user role
	//---------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }

	//---------------------------------------------------------------------------------------------
	//	make the block
	//---------------------------------------------------------------------------------------------

	$table = array();
	$table[] = array('', '');
	$table[] = array('Own UID', $kapenta->registry->get('p2p.server.uid'));
	$table[] = array('Server Name', $kapenta->registry->get('p2p.server.name'));
	$table[] = array('Server Url', $kapenta->registry->get('p2p.server.url'));
	$table[] = array('Last Worker', $kapenta->registry->get('p2p.started'));

	$html = ''
	 . $theme->arrayToHtmlTable($table, true, true)
	 . "<a href='%%serverPath%%p2p/settings/'>[edit p2p settings &gt;&gt;]</a><br/>\n";

	$html = $theme->ntb($html, 'This Server', 'divServerDetails', 'show');

	return $html;
}

?>
