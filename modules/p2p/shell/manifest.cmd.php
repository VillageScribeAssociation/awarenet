<?

	require_once($kapenta->installPath . 'modules/live/inc/cmdaliases.class.php');
	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/downloads.set.php');

//--------------------------------------------------------------------------------------------------
//|	web shell command for listing p2p downloads (files)
//--------------------------------------------------------------------------------------------------

function p2p_WebShell_manifest($args) {
	global $kapenta;
	global $user;
	global $shell;
	global $db;

	$mode = 'pull';			//%	operation [string]
	$html = '';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	switch($args[0]) {
		case '-h':		$mode = 'help';		break;
		case '-p':		$mode = 'get';		break;
		case '--help':	$mode = 'help';		break;
		case '--pull':	$mode = 'pull';		break;
	}

	if ('admin' != $user->role) { $mode = 'noauth'; }

	//----------------------------------------------------------------------------------------------
	//	execute
	//----------------------------------------------------------------------------------------------
	
	switch($mode) {
		case 'help':
			$html = p2p_WebShell_manifest_help();
			break;			

		case 'noauth':
			$html = "Admin permissions required to perform this operation.";
			break;	//..............................................................................

		case 'pull':
			//--------------------------------------------------------------------------------------
			//	pull a manifest from a peer
			//--------------------------------------------------------------------------------------
			//print_r($args);
			
			$peerUID = 'local';
			$fileName = '';

			if (1 == count($args)) { $fileName = $args[0]; }

			if (2 == count($args)) {
				$peerUID = $args[0];
				$fileName = $args[1];
			}

			if ('local' == $peerUID) {
				//----------------------------------------------------------------------------------
				//	get file manifest from a local peer
				//----------------------------------------------------------------------------------
				if (false == $kapenta->fs->exists($fileName)) { return 'No such file.'; }

				$klf = new KLargeFile($fileName);
				$check = $klf->makeFromFile();
				$html .= "<pre>" . htmlentities($klf->toXml()) . "</pre><br/>\n";

			} else {
				//----------------------------------------------------------------------------------
				//	get file manifest from a remote peer
				//----------------------------------------------------------------------------------
				$peer = new P2P_Peer($peerUID);
				if (false == $peer->loaded) { return 'Unkown peer.'; }

				echo "Downlading manifest from peer " . $peer->url . "p2p/file/ ...<br/>\n";
				$xml = $peer->sendMessage('file', $fileName);
				$klf = new KLargeFile($fileName);
				$klf->loadMetaXml($xml);

				$html .= "<pre>" . htmlentities($klf->toXml()) . "</pre><br/>\n";
			}

			break;	//..............................................................................
	}

	return $html;
}

//--------------------------------------------------------------------------------------------------
//|	manpage for the admin.bash command
//--------------------------------------------------------------------------------------------------
//opt: short - display command summary [bool]

function p2p_WebShell_manifest_help($short = false) {
	if (true == $short) { return "Request a menifest from a peer."; }

	$html = "
	<b>usage: p2p.manifest [<i>peerUID</i>] <i>fileName</i></b><br/>
	Display a file manifest from local or peer server.<br/>
	<br/>
	<b>[--help|-h]</b><br/>
	Displays this manpage.<br/>
	<br/>
	";

	return $html;
}


?>
