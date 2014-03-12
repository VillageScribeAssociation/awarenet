<?php

//--------------------------------------------------------------------------------------------------
//|	summarize pending events
//--------------------------------------------------------------------------------------------------

function p2p_eventstats($args) {
	global $kapenta;
	global $kapenta;
	global $theme;
	global $kapenta;

	$html = '';

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	list event files
	//----------------------------------------------------------------------------------------------
	$locks = 0;
	$priorities = array();
	$files = $kapenta->listFiles('data/p2p/received/');

	foreach($files as $file) {
		if (('.' != $file) && ('..' != $file)) {
			$parts = explode('_', $file);
			if (false == array_key_exists($parts[0], $priorities)) { $priorities[$parts[0]] = 0; }
			if ('.lock' != substr($file, -5)) {
				$priorities[$parts[0]] += 1;
			} else {
				$locks += 1;
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array();
	$table[] = array('Priority', 'Pending');

	foreach ($priorities as $priority => $count) {
		$table[] = array($priority, (string)$count . ' events');
	}

	$html .= $theme->arrayToHtmlTable($table, true, true);

	$html = $theme->ntb($html, 'Async Event Queues', 'divEventQueues', 'show');

	return $html;
}

?>
