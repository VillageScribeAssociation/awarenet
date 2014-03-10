<?

	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a new peer server
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check user role and POST vars
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $kapenta->page->do403(); } // only admins can use this module
	if (false == array_key_exists('action', $_POST)) { $kapenta->page->do404('Action not specified.'); }
	if ('newPeer' != $_POST['action']) { $kapenta->page->do404('Action not supported.'); }

	$UID = '';
	$name = '';
	$url = '';
	$firewalled = '';
	$pubkey = '';

	if (true == array_key_exists('UID', $_POST)) { $UID = trim($_POST['UID']); }
	if (true == array_key_exists('name', $_POST)) { $name = trim($_POST['name']); }
	if (true == array_key_exists('url', $_POST)) { $url = trim($_POST['url']); }
	if (true == array_key_exists('firewalled', $_POST)) { $firewalled = trim($_POST['firewalled']);}
	if (true == array_key_exists('pubkey', $_POST)) { $pubkey = trim($_POST['pubkey']); }

	if ('' == $UID) { $kapenta->page->do404('Server UID not given.'); }
	if ('' == $name) { $kapenta->page->do404('Server name not given.'); }
	if ('' == $url) { $kapenta->page->do404('Server URL not given.'); }
	if ('' == $firewalled) { $kapenta->page->do404('Server firewall status not given.'); }
	if ('' == $pubkey) { $kapenta->page->do404('Server RSA key not given.'); }

	if (true == $kapenta->db->objectExists('p2p_peer', $UID)) { 
		$session->msg('Peer already exists in database.', 'warn');
		$kapenta->page->do302('p2p/peers/');
	}

	//----------------------------------------------------------------------------------------------
	//	create the P2P_Peer object
	//----------------------------------------------------------------------------------------------
	$model = new P2P_Peer();
	$model->status = 'untrusted';

	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'uid':			$model->UID = $utils->cleanTitle($value); 			break;
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
	if ('' == $report) { $session->msg('Created new peer server: ' . $model->name, 'ok'); }
	else { $session->msg('Could not save Server:<br/>' . $report, 'bad'); }

	if (true == array_key_exists('return', $_POST)) { $kapenta->page->do302($_POST['return']); }
	else { $kapenta->page->do302('/p2p/peers/' . $model->UID); }

?>
