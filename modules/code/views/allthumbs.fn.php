<?

	require_once($kapenta->installPath . 'modules/code/models/bugs.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/code.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/codeproject.mod.php');
	require_once($kapenta->installPath . 'modules/code/models/coderevision.mod.php');

//--------------------------------------------------------------------------------------------------
//	all images of all code (thumbnails, no arguments)
//--------------------------------------------------------------------------------------------------

function code_allthumbs($args) {
	global $kapenta;

	global $kapenta;
	$sql = "select * from images where refModule='code' order by weight";
	$html = '';
	
	$result = $kapenta->db->query($sql);
	if ($kapenta->db->numRows($result) > 0) {
		while ($row = $kapenta->db->fetchAssoc($result)) {
			$coinRa = raGetDefault('code', $row['refUID']);
			$html .= "<a href='/code/show/" . $coinRa . "'>" 
				. "<img src='/images/thumb90/" . $row['recordAlias'] 
				. "' border='0' alt='" . $row['title'] . "'></a> ";
		}
	} 
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>