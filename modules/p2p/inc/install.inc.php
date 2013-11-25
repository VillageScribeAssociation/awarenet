<?

	require_once($kapenta->installPath . 'core/dbdriver/mysqladmin.dbd.php');
	require_once($kapenta->installPath . 'modules/p2p/models/deleted.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/gift.mod.php');
	require_once($kapenta->installPath . 'modules/p2p/models/peer.mod.php');

//--------------------------------------------------------------------------------------------------
//*	install script for p2p module
//--------------------------------------------------------------------------------------------------
//+	reports are human-readable HTML, with script-readable HTML comments

//--------------------------------------------------------------------------------------------------
//|	install the p2p module
//--------------------------------------------------------------------------------------------------
//returns: html report or empty string if not authorized [string][bool]

function p2p_install_module() {
	global $user;
	global $kapenta;
	global $kapenta;

	if ('admin' != $user->role) { return ''; }

	$report = '';				//% return value [string:html]

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	create or upgrade p2p_deleted table
	//----------------------------------------------------------------------------------------------
	$model = new p2p_Deleted();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade p2p_gift table
	//----------------------------------------------------------------------------------------------
	$model = new p2p_Gift();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create or upgrade p2p_peer table
	//----------------------------------------------------------------------------------------------
	$model = new p2p_Peer();
	$dbSchema = $model->getDbSchema();
	$report .= $dba->installTable($dbSchema);

	//----------------------------------------------------------------------------------------------
	//	create RSA keypair for this peer
	//----------------------------------------------------------------------------------------------
	if ('' == $kapenta->registry->get('p2p.server.prvkey')) {

		if (true == function_exists('openssl_pkey_get_details')) {
			//--------------------------------------------------------------------------------------
			// generate a bit rsa private key (usually 1024 bit)
			//--------------------------------------------------------------------------------------
			$keyLength = 4096;
			$kapenta->registry->set('p2p.keylength', '4096');
			$kapenta->registry->set('p2p.keytype', 'RSA');

			$config = array();
			$config['private_key_bits'] = $keyLength;		//	setting?
			$config['private_key_type'] = OPENSSL_KEYTYPE_RSA;

			$prvkey = '';

			$res = openssl_pkey_new($config);					//	make new key pair 
			openssl_pkey_export($res, $prvkey);					//	get the private key
			$details = openssl_pkey_get_details($res);			//	get metadata
			$pubkey = $details['key'];							//	get public key


			$kapenta->registry->set('p2p.server.pubkey', $pubkey);
			$kapenta->registry->set('p2p.server.prvkey', $prvkey);

			$report .= "Set RSA $keyLength public key.<br/><pre>$pubkey</pre><br/>\n";
			$report .= "Set RSA $keyLength private key.<br/><pre>(not shown)</pre><br/>\n";

		} else {
			$report .= "Could not generate key pair, openSSL support missing or incomplete.";
		}
	} else {
		$report .= "RSA keypair installed.<br/>";
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	return $report;
}

//--------------------------------------------------------------------------------------------------
//|	discover if this module is installed
//--------------------------------------------------------------------------------------------------
//:	if installed correctly report will contain HTML comment <!-- installed correctly -->
//returns: HTML installation status report or empty string if not authorized [string]

function p2p_install_status_report() {
	global $user;
	global $kapenta;

	if ('admin' != $user->role) { return false; }

	$report = '';				//%	return value [string:html]
	$installNotice = '<!-- table installed correctly -->';
	$installed = true;

	$dba = $kapenta->getDBAdminDriver();

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Deleted objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new p2p_Deleted();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Gift objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new p2p_Gift();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	ensure the table which stores Peer objects exists and is correct
	//----------------------------------------------------------------------------------------------
	$model = new p2p_Peer();
	$dbSchema = $model->getDbSchema();
	$treport = $dba->getTableInstallStatus($dbSchema);

	if (false == strpos($treport, $installNotice)) { $installed = false; }
	$report .= $treport;

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	if (true == $installed) { $report .= '<!-- module installed correctly -->'; }
	return $report;
}

?>
