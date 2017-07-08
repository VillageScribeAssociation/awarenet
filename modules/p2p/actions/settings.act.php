<?

//--------------------------------------------------------------------------------------------------
//*	form for editing P2P client settings
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { $kapenta->page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	check defaults
	//----------------------------------------------------------------------------------------------

	$ps = 'p2p.server.';
	if ('' == $kapenta->registry->get('p2p.enabled')) { $kapenta->registry->set('p2p.enabled', 'yes'); }
	if ('' == $kapenta->registry->get('p2p.keylength')) { $kapenta->registry->set('p2p.keylength', '1024'); }
	if ('' == $kapenta->registry->get('p2p.keytype')) { $kapenta->registry->set('p2p.keytype', 'RSA'); }
	if ('' == $kapenta->registry->get('p2p.batchsize')) { $kapenta->registry->set('p2p.batchsize', '20'); }
	if ('' == $kapenta->registry->get('p2p.batchsize')) { $kapenta->registry->set('p2p.batchparts', '20'); }
	if ('' == $kapenta->registry->get('p2p.filehours')) { $kapenta->registry->set('p2p.filehours', '3, 4'); }
	if ('' == $kapenta->registry->get($ps . 'uid')) { $kapenta->registry->set($ps . 'uid', $kapenta->createUID()); }
	if ('' == $kapenta->registry->get($ps . 'url')) { $kapenta->registry->set($ps . 'url', $kapenta->serverPath);	}
	if ('' == $kapenta->registry->get($ps . 'fw')) { $kapenta->registry->set($ps . 'fw', 'yes'); }

	if ('' == $kapenta->registry->get($ps . 'pubkey')) { 
		if (true == function_exists('openssl_pkey_get_details')) {
			//--------------------------------------------------------------------------------------
			// generate a bit rsa private key (usually 1024 bit)
			//--------------------------------------------------------------------------------------
			$config = array();
			$config['private_key_bits'] = (int)$kapenta->registry->get('p2p.keylength');		//	setting?
			$config['private_key_type'] = OPENSSL_KEYTYPE_RSA;

			$prvkey = '';

			$res = openssl_pkey_new($config);					//	make new key pair 
			openssl_pkey_export($res, $prvkey);					//	get the private key
			$details = openssl_pkey_get_details($res);			//	get metadata
			$pubkey = $details['key'];							//	get public key

			$kapenta->registry->set('p2p.server.pubkey', $pubkey);
			$kapenta->registry->set('p2p.server.prvkey', $prvkey);
		} else {
			$kapenta->session->msg('OpenSSL support missing or incomplete, please eneter key manually.');
		}
	}

	//----------------------------------------------------------------------------------------------
	//	handle POST vars
	//----------------------------------------------------------------------------------------------
	if (true == array_key_exists('action', $_POST)) {
		
		//------------------------------------------------------------------------------------------
		//	change registry settings (excluding password)
		//------------------------------------------------------------------------------------------

		if ('changeSettings' == $_POST['action']) {
			foreach($_POST as $key => $value) {
				switch($key) {
					case 'p2p_enabled':			$kapenta->registry->set('p2p.enabled', $value);		break;
					case 'p2p_batchsize':		$kapenta->registry->set('p2p.batchsize', $value);	break;
					case 'p2p_batchparts':		$kapenta->registry->set('p2p.batchparts', $value);	break;
					case 'p2p_filehours':		$kapenta->registry->set('p2p.filehours', $value);	break;
					case 'p2p_server_uid':		$kapenta->registry->set($ps . 'uid', $value);		break;
					case 'p2p_server_name':		$kapenta->registry->set($ps . 'name', $value);		break;
					case 'p2p_server_url':		$kapenta->registry->set($ps . 'url', $value);		break;
					case 'p2p_server_fw':		$kapenta->registry->set($ps . 'fw', $value);			break;
					case 'p2p_server_pubkey':	$kapenta->registry->set($ps . 'pubkey', $value);		break;
					case 'p2p_server_prvkey':	$kapenta->registry->set($ps . 'prvkey', $value);		break;
				}
			}

			$msg = "Updated registry settings:<br/>\n"
				. "<b>p2p.enabled:</b> " . $kapenta->registry->get('p2p.enabled') . "<br/>"
				. "<b>p2p.server.uid:</b> " . $kapenta->registry->get('p2p.server.uid') . "<br/>"
				. "<b>p2p.server.name:</b> " . $kapenta->registry->get('p2p.server.name') . "<br/>"
				. "<b>p2p.server.url:</b> " . $kapenta->registry->get('p2p.server.url') . "<br/>";

			$kapenta->session->msg($msg, 'ok');
		}

		//------------------------------------------------------------------------------------------
		//	test settings
		//------------------------------------------------------------------------------------------

	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/p2p/actions/settings.page.php');
	$kapenta->page->render();

?>
