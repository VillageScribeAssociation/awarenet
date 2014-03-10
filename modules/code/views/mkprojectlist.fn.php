<?

	require_once($kapenta->installPath . 'modules/code/models/bugs.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/code.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/codeproject.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/coderevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	show list of project files (tab delimited UID, SHA1, type, path)
//--------------------------------------------------------------------------------------------------
// * $args['project'] = project UID

function code_mkprojectlist($args) {
	global $db;

	if (array_key_exists('project', $args) == false) { return false; }
	$sql = "select * from code where project='". $db->addMarkup($args['project']) ."' and parent='root'";
	$result = $db->query($sql);
	if ($db->numRows($result) == 0) { return false; }
	$row = $db->fetchAssoc($result);
	$txt = code__mkprojectlistfrom($row['UID'], '/');
	return $txt;
}

function code__mkprojectlistfrom($UID, $relPath) {
	global $db;

	$txt = '';
	$sql = "select * from code where parent='" . $UID . "'";
	$result = $db->query($sql);

	while ($row = $db->fetchAssoc($result)) {
		$row = $db->rmArray($row);
		$txt .= $row['UID'] . "\t" 
				. sha1($row['content']) . "\t" 
				. $row['type'] . "\t" 
				. $relPath . $row['title'] . "\n";

		if ($row['type'] == 'folder') 
			{ $txt .= code__mkprojectlistfrom($row['UID'], $relPath . $row['title']); }

	}

	return $txt;		
}


?>