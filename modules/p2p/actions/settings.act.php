<?

//--------------------------------------------------------------------------------------------------
//*	form for editing P2P client settings
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	check defaults
	//----------------------------------------------------------------------------------------------

	$ps = 'p2p.server.';
	if ('' == $registry->get('p2p.enabled')) { $registry->set('p2p.enabled', 'yes'); }
	if ('' == $registry->get('p2p.keylength')) { $registry->set('p2p.keylength', '4096'); }
	if ('' == $registry->get('p2p.keytype')) { $registry->set('p2p.keytype', 'RSA'); }
	if ('' == $registry->get('p2p.batchsize')) { $registry->set('p2p.batchsize', '20'); }
	if ('' == $registry->get('p2p.batchsize')) { $registry->set('p2p.batchparts', '20'); }
	if ('' == $registry->get('p2p.filehours')) { $registry->set('p2p.filehours', '3, 4'); }
	if ('' == $registry->get($ps . 'uid')) { $registry->set($ps . 'uid', $kapenta->createUID()); }
	if ('' == $registry->get($ps . 'url')) { $registry->set($ps . 'url', $kapenta->serverPath);	}
	if ('' == $registry->get($ps . 'fw')) { $registry->set($ps . 'fw', 'yes'); }

	if ('' == $registry->get($ps . 'pubkey')) { 
		if (true == function_exists('openssl_pkey_get_details')) {
			//--------------------------------------------------------------------------------------
			// generate a bit rsa private key (usually 1024 bit)
			//--------------------------------------------------------------------------------------
			$config = array();
			$config['private_key_bits'] = (int)$registry->get('p2p.keylength');		//	setting?
			$config['private_key_type'] = OPENSSL_KEYTYPE_RSA;

			$prvkey = '';

			$res = openssl_pkey_new($config);					//	make new key pair 
			openssl_pkey_export($res, $prvkey);					//	get the private key
			$details = openssl_pkey_get_details($res);			//	get metadata
			$pubkey = $details['key'];							//	get public key

			$registry->set('p2p.server.pubkey', $pubkey);
			$registry->set('p2p.server.prvkey', $prvkey);
		} else {
			$session->msg('OpenSSL support missing or incomplete, please eneter key manually.');
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
					case 'p2p_enabled':			$registry->set('p2p.enabled', $value);		break;
					case 'p2p_batchsize':		$registry->set('p2p.batchsize', $value);	break;
					case 'p2p_batchparts':		$registry->set('p2p.batchparts', $value);	break;
					case 'p2p_filehours':		$registry->set('p2p.filehours', $value);	break;
					case 'p2p_server_uid':		$registry->set($ps . 'uid', $value);		break;
					case 'p2p_server_name':		$registry->set($ps . 'name', $value);		break;
					case 'p2p_server_url':		$registry->set($ps . 'url', $value);		break;
					case 'p2p_server_fw':		$registry->set($ps . 'fw', $value);			break;
					case 'p2p_server_pubkey':	$registry->set($ps . 'pubkey', $value);		break;
					case 'p2p_server_prvkey':	$registry->set($ps . 'prvkey', $value);		break;
				}
			}

			$msg = "Updated registry settings:<br/>\n"
				. "<b>p2p.enabled:</b> " . $registry->get('p2p.enabled') . "<br/>"
				. "<b>p2p.server.uid:</b> " . $registry->get('p2p.server.uid') . "<br/>"
				. "<b>p2p.server.name:</b> " . $registry->get('p2p.server.name') . "<br/>"
				. "<b>p2p.server.url:</b> " . $registry->get('p2p.server.url') . "<br/>";

			$session->msg($msg, 'ok');
		}

		//------------------------------------------------------------------------------------------
		//	test settings
		//------------------------------------------------------------------------------------------

	}

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$page->load('modules/p2p/actions/settings.page.php');
	$page->render();

?>
