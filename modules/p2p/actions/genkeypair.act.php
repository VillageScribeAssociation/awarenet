<?

	require_once($kapenta->installPath . 'modules/p2p/inc/phpseclib/Math/BigInteger.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/phpseclib/Crypt/Random.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/phpseclib/Crypt/Hash.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/phpseclib/Crypt/RSA.php');
	require_once($kapenta->installPath . 'modules/p2p/inc/phpseclib/Crypt/AES.php');

//--------------------------------------------------------------------------------------------------
//*	RSA key generation utility action
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	authentication (only admins can do this)
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	// generate a bit rsa private key (usually 1024 bit)
	//----------------------------------------------------------------------------------------------
	$keylen = (int)$kapenta->registry->get('p2p.keylength');
	if (0 == $keylen) { $keylen = 1024; }

	$keypair = array('publickey' => '', 'privatekey' => '');

	if (true == array_key_exists('generate', $kapenta->request->args)) {
		$rsa = new Crypt_RSA();
		$keypair = $rsa->createKey($keylen);
	}

	//----------------------------------------------------------------------------------------------
	// render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/p2p/actions/genkeypair.page.php');
	$kapenta->page->blockArgs['publicKeyTxt'] = $keypair['publickey'];
	$kapenta->page->blockArgs['privateKeyTxt'] = $keypair['privatekey'];
	$kapenta->page->render();


?>
