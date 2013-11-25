<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/downloads.set.php');

//--------------------------------------------------------------------------------------------------
//|	shows a list of files this peer is downloading
//--------------------------------------------------------------------------------------------------

function p2p_listdownloads($args) {
	global $kapenta;
	global $user;

	$html = '';							//%	return value [string]
	$count = 0;							//%	number of active downloads [int]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	list downloads in progress
	//----------------------------------------------------------------------------------------------
	$metaFiles = $kapenta->listFiles('data/p2p/transfer/meta/');

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	foreach($metaFiles as $metaFile) {
		if (('.' != $metaFile) && ('..' != $metaFile)) {
			//$html .= "<small>$metaFile</small><br/>";
			$html .= "[[:p2p::showdownload::metafile=" . $metaFile . ":]]";
			$count++;
		}	
	}
	
	$html .= "Total: " . $count . "<br/>";

	return $html;
}

?>
