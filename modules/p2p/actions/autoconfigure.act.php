<?php

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//-------------------------------------------------------------------------------------------------
//*	API to automatically configure a peer
//-------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $kapenta->page->do403(); }
	if (false == array_key_exists('url', $_POST)) { $kapenta->page->do404('URL not given'); }
	if (false == array_key_exists('recover', $_POST)) { $kapenta->page->do404('password not given'); }

	//---------------------------------------------------------------------------------------------
	//	attempt to download peer's public manifest
	//---------------------------------------------------------------------------------------------
	$url = $_POST['url'] . '/p2p/publicxml/';
	$raw = $utils->curlGet($url);
	$raw = str_replace("<?xml version='1.0' encoding='UTF-8' ?>", '', $raw);
	$args = array();

	if (false == strpos($raw, '</peer>')) {
		$session->msg("Could not load peer autoconfiguration file.");
		$kapenta->page->do302('p2p/peers/');
	}

	$xd = new KXmlDocument($raw);

	$xmlroot = $xd->getEntity(1);		//	children of root node.
	foreach($xmlroot['children'] as $childId) {
		$child = $xd->getEntity($childId);
		$args[$child['type']] = $child['value'];
	}

	//---------------------------------------------------------------------------------------------
	//	add or update this peer
	//---------------------------------------------------------------------------------------------
	$model = new P2P_Peer($args['uid']);
	if (false == $model->loaded) { $session->msg('Creating new peer: ' . $args['uid']); }
	else { $session->msg('Updating existing peer: ' . $args['uid']); }

	$model->UID = $args['uid'];
	$model->url = $args['url'];
	$model->name = $args['name'];
	$model->pubkey = $args['pubkey'];
	$model->firewalled = $args['firewalled'];

	$report = $model->save();

	if ('' == $report) { $session->msg("Re/Added peer: " . $model->name, 'ok'); }
	else { $session->msg("Could not save new peer:<br/>\n" . $report, 'bad'); }

	//---------------------------------------------------------------------------------------------
	//	if password was given, attempt to register self with remote peer
	//---------------------------------------------------------------------------------------------

	if ('' != $_POST['recover']) {

		$postArgs = array(
			'recover' => sha1($_POST['recover']),
			'uid' => $kapenta->registry->get('p2p.server.uid'),
			'name' => $kapenta->registry->get('p2p.server.name'),
			'url' => $kapenta->registry->get('p2p.server.url'),
			'pubkey' => $kapenta->registry->get('p2p.server.pubkey'),
			'firewalled' => $kapenta->registry->get('p2p.server.fw')
		);

		$result = $utils->curlPost($_POST['url'] . '/p2p/submitpeer/', $postArgs);

		if ('<ok/>' == $result) {
			$session->msg("Registered self with " . $_POST['url'], 'ok');

			//-------------------------------------------------------------------------------------
			//	broadcast resync request to new peer
			//-------------------------------------------------------------------------------------

			$message = ''
			 . "\t<resynchronize>\n"
			 . "\t\t<peer>" . $kapenta->registry->get('p2p.server.uid') . "</peer>\n"
			 . "\t</resynchronize>\n";

			$detail = array(
				'message' => $message,
				'peer' => $model->UID,
				'priority' => '1'
			);

			$kapenta->raiseEvent('*', 'p2p_narrowcast', $detail);

		} else {
			$session->msg("Remote registration error:<br/>\n" . $result);
		}

	}

	//---------------------------------------------------------------------------------------------
	//	redirect back to peers listing
	//---------------------------------------------------------------------------------------------
	
	$kapenta->page->do302('p2p/peers/');

?>
