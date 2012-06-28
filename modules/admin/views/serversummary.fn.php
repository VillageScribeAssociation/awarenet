<?

//--------------------------------------------------------------------------------------------------
//|	display basic info on this node
//--------------------------------------------------------------------------------------------------

function admin_serversummary($args) {
	global $kapenta, $registry, $user, $db, $theme;
	$html = '';					//%	return value [string:html]	

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	//----------------------------------------------------------------------------------------------
	//	check PHP extensions
	//----------------------------------------------------------------------------------------------
	$extTests = array(
		'cURL' => 'curl_init',
		'OpenSSL' => 'openssl_encrypt',
		'GD' => 'imagecreatetruecolor',
		'bzip' => 'bzcompress',
	);
	$exts = '';

	foreach($extTests as $extName => $fnName) {
		if (true == function_exists($fnName)) {
			$exts .= "<span class='ajaxmsg'>$extName</span> ";
		} else {
			$exts .= "<span class='ajaxerror'>$extName</span> ";
		}
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array(
		array('Key', 'Setting'),
		array('<b>iPath:</b>', $registry->get('kapenta.installpath')),
		array('<b>sPath:</b>', $registry->get('kapenta.serverpath')),
		array('<b>date:</b>', $db->datetime()),
		array('<b>db use:</b>', '[[:admin::dbusage:]]'),
		array('<b>extensions:</b>', $exts)
	);

	$html = $theme->arrayToHtmlTable($table, true, true);

	return $html;
}

?>
