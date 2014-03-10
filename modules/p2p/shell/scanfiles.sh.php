<?php

	require_once('../../../shinit.php');
	require_once('../models/gift.mod.php');
	require_once('../models/peer.mod.php');
	require_once('../models/offers.set.php');

//--------------------------------------------------------------------------------------------------
//	scan database for gifts
//--------------------------------------------------------------------------------------------------

	$usage_notes = ''
	 . "Usage: shinit.php [peerUID]|[peerName]|[peerUrl]\n"
	 . "This script will rescan the database for shared files.\n\n";

	if (1 == count($argv)) { echo $argv[1] = ''; }

	$range = $kapenta->db->loadRange('p2p_peer', '*');
	$peerUID = '';

	foreach($range as $item) {
		if ($item['UID'] == $argv[1]) { $peerUID = $item['UID']; }
 		if (strtolower($item['name']) == strtolower($argv[1])) { $peerUID = $item['UID']; }
		if (strtolower($item['url']) == strtolower($argv[1])) { $peerUID = $item['UID']; }
		$usage_notes .= "peer: " . $item['UID'] . ' - ' . $item['name'] . " - " . $item['url'] . "\n";
	}
	$usage_notes .= "\n\n";

	if ('' == $peerUID) { echo $usage_notes; die(); }

	$peer = new P2P_Peer($peerUID);
	if (false == $peer->loaded) { echo $usage_notes; die(); }

	$offers = new P2P_Offers($peer->UID);	

	/* ------------------------------------------------------------------------------------------ */

	$count = 0;												//%	return value
	$print = true;

	if ($print) { echo "Scanning database for: " . $peer->name . " (" . $peer->url . ")\n"; }

	//----------------------------------------------------------------------------------------------
	//	get list of tables
	//----------------------------------------------------------------------------------------------
	//----------------------------------------------------------------------------------------------
	//.	ask modules about files we can send to this peer
	//----------------------------------------------------------------------------------------------
	//;	Note: format for listfiles CSV lines is: refModule, refModel, refUID, fileName, hash
	//opt: print - set to true for noisy output [string]
	//returns: number of gifts created / fixed [int]

	$mods = $kapenta->listModules();		//%	all modules on this instance [array]
	$count = 0;								//%	return value [int]

	foreach($mods as $mod) {
		$block = "[[:$mod::listfiles::format=csv:]]";
		$list = $theme->expandBlocks($block, '');

		$lines = explode("\n", $list);
		foreach($lines as $line) {
			if ('' != trim($line)) {
				//----------------------------------------------------------------------------------
				//	parse
				//----------------------------------------------------------------------------------
				echo $line . "<br/>\n";
				$parts = explode(",", $line);
				$refModule = trim($parts[0]);
				$refModel = trim($parts[1]);		
				$refUID = trim($parts[2]);			
				$fileName = trim($parts[3]);		
				$hash = trim($parts[4]);		

				//----------------------------------------------------------------------------------
				//	add to gifts
				//----------------------------------------------------------------------------------
				$localHash = '';
				if (true == $kapenta->fs->exists($fileName)) {
					$localHash = sha1_file($kapenta->installPath . $fileName);
				}

				if (($localHash == $hash) && ('' != $fileName) && ('' != $hash)) {
					$giftUID = $offers->getGiftUID('file', $refModel, $refUID, $fileName);
					
					$model = new P2P_Gift($giftUID);
					$model->peer = $peer->UID;
					$model->type = 'file';
					$model->refModel = $refModel;
					$model->refUID = $refUID;
					$model->fileName = $fileName;
					$model->hash = $hash;
					$model->updated = $kapenta->datetime();
					$model->status = 'want';
					$model->save();
					$count++;
				}
			}
		} // end foreach line
	} // end foreach module

	echo "\n\n$count gifts updated.\n\n";

?>
