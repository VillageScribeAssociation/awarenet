<?php

//-------------------------------------------------------------------------------------------------
//|	dispays a form to toggle peer firewall status
//-------------------------------------------------------------------------------------------------
//arg: peerUID - UID of a P2P_Peer object [string]
//arg: firewalled - firewall status of this peer [string]

function p2p_firewallform($args) {
	global $kapenta;
	global $theme;

	$html = '';

	//---------------------------------------------------------------------------------------------
	//	check arguments and use role
	//---------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	
	if (false == array_key_exists('peerUID', $args)) { return '(peerUID not given)'; }

	//---------------------------------------------------------------------------------------------
	//	make the block
	//---------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/p2p/views/firewallform.block.php');

	$toggle = 'yes';
	if ('yes' == $args['firewalled']) { $toggle = 'no'; }

	$labels = array(
		'peerUID' => $args['peerUID'],
		'firewalled' => $args['firewalled'],
		'toggle' => $toggle
	);

	$html = $theme->replaceLabels($labels, $block);

	return $html;
}

?>
