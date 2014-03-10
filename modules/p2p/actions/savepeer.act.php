<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save updates to server records
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check user role and POST vars
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); } // only admins can use this module
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('savePeer' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $kapenta->page->do404('Server not specified (UID).'); }

	$model = new P2P_Peer($_POST['UID']);
	if (false == $model->loaded) { $kapenta->page->do404("could not load Peer Server");}

	//----------------------------------------------------------------------------------------------
	//	update the P2P_Peer object
	//----------------------------------------------------------------------------------------------

	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'UID':			$model->UID = $utils->cleanTitle($value); 			break;
			case 'name':		$model->name = $utils->cleanTitle($value); 			break;
			case 'url':			$model->url = $value; 								break;
			case 'firewalled':	$model->firewalled = $utils->cleanYesNo($value); 	break;
			case 'pubkey':		$model->pubkey = trim($value);						break;
		}
	}

	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $session->msg('Saved changes to peer server: ' . $model->name, 'ok'); }
	else { $session->msg('Could not save Server:<br/>' . $report, 'bad'); }

	if (true == array_key_exists('return', $_POST)) { $kapenta->page->do302($_POST['return']); }
	else { $kapenta->page->do302('/p2p/peers/' . $model->UID); }

?>
