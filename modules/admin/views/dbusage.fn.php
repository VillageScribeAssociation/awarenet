<?

//--------------------------------------------------------------------------------------------------
//|	shows total database size
//--------------------------------------------------------------------------------------------------

function admin_dbusage($args) {
	global $user;
	global $db;
	global $registry;

	$sizeStr = '(unknown)';				//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }	

	if ('no' == $registry->get('kapenta.db.measure')) { return $sizeStr; }

	//----------------------------------------------------------------------------------------------
	//	get database size
	//----------------------------------------------------------------------------------------------
	$sql = "select SUM(data_length) + SUM(index_length) as size from information_schema.tables"; 
	$result = $db->query($sql);
	if (false == $result) { return '(unknown)'; }
	while($row = $db->fetchAssoc($result)) {
		$sizeStr = $row['size'];
		$size = (int)$row['size'];
		if ($size > 1024) { $sizeStr = floor($size / 1024) . 'kb'; }
		if ($size > (1024 * 1024)) { $sizeStr = floor($size / (1024 * 1024)) . 'mb'; }
		return $sizeStr;
	}
	return $sizeStr;
}


?>
