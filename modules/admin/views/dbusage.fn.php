<?

//--------------------------------------------------------------------------------------------------
//|	shows total database size
//--------------------------------------------------------------------------------------------------
//opt: refresh - force recalculation [string]

function admin_dbusage($args) {
	global $kapenta;
	global $kapenta;
	global $kapenta;
	global $kapenta;
	global $theme;

	$refresh = 'no';									//%	re-calculate [string]
	$sizeStr = '(unknown)';								//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if ('no' == $kapenta->registry->get('kapenta.db.measure')) { return $sizeStr; }
	if (true == array_key_exists('refresh', $args)) { $refresh = $args['refresh']; }

	$driverName = strtolower($kapenta->registry->get('db.driver'));
	$block = $theme->loadBlock('modules/admin/views/dbusage.block.php');

	//----------------------------------------------------------------------------------------------
	//	get database size (MySQL)
	//----------------------------------------------------------------------------------------------

	if ('mysql' == $driverName) {

		//------------------------------------------------------------------------------------------
		//	this operation is expensive on MySQL, use cached value by default
		//------------------------------------------------------------------------------------------

		if ('no' == $refresh) {
			$lastCheck = (int)$kapenta->registry->get('kapenta.db.measured');
			$now = $kapenta->time();
			if (($now - $lastCheck) < 3600) {
				$sizeStr = $kapenta->registry->get('kapenta.db.usage');
				return str_replace('%%dbusage%%', $sizeStr, $block);
			}
		}

		$sql = "select SUM(data_length) + SUM(index_length) as size from information_schema.tables"; 
		$result = $kapenta->db->query($sql);
		if (false == $result) { return '(unknown)'; }
		while($row = $kapenta->db->fetchAssoc($result)) {
			$sizeStr = $row['size'];
			$size = (int)$row['size'];
			if ($size > 1024) { $sizeStr = floor($size / 1024) . 'kb'; }
			if ($size > (1024 * 1024)) { $sizeStr = floor($size / (1024 * 1024)) . 'mb'; }
		}

		$kapenta->registry->set('kapenta.db.usage', $sizeStr);
		$kapenta->registry->set('kapenta.db.measured', $kapenta->time());

	}

	//----------------------------------------------------------------------------------------------
	//	get database size (SQLite)
	//----------------------------------------------------------------------------------------------

	if ('sqlite' == $driverName) {
		$dbFile = $kapenta->registry->get('db.sqlite.name');
		if (true == file_exists($dbFile . '.sq3')) {
			$size = filesize($dbFile . '.sq3');
			$sizeStr = (string)$size . 'bytes';
			if ($size > 1024) { $sizeStr = floor($size / 1024) . 'kb'; }
			if ($size > (1024 * 1024)) { $sizeStr = floor($size / (1024 * 1024)) . 'mb'; }
		}
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------

	return str_replace('%%dbusage%%', $sizeStr . '*', $block);

}


?>
