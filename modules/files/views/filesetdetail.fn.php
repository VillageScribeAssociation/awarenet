<?

	require_once($kapenta->installPath . 'modules/files/models/file.mod.php');

//--------------------------------------------------------------------------------------------------
//|	display a set of files associated with something, along with some metadata
//--------------------------------------------------------------------------------------------------
//arg: refModule - module to list on [string]
//arg: refUID - UID of item which owns these files [string]

function files_filesetdetail($args) {
	global $db;

	global $theme;

	global $serverPath;
	
	//----------------------------------------------------------------------------------------------
	//	check args and authorisation
	//----------------------------------------------------------------------------------------------
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }
	$authArgs = array('UID' => $args['refUID']);
	if (authHas($args['refModule'], 'files', $authArgs) == false) { return false; }

	//----------------------------------------------------------------------------------------------
	//	load the file records and make html
	//----------------------------------------------------------------------------------------------
	$sql = "select * from files where refModule='" . $db->addMarkup($args['refModule']) 
	     . "' and refUID='" . $db->addMarkup($args['refUID']) . "'";
	
	$result = $db->query($sql);	

	$html = '';
	$block = $theme->loadBlock('modules/files/listing.block.php');
	
	while($row = $db->fetchAssoc($result)) {
		
		$f = new Files_File();
		$f->loadArray($db->rmArray($row));
		
		$labels = $f->extArray();
		
		$labels['editUrl'] = $serverPath . 'files/edit/return_uploadmultiple/' . $f->alias;
		if (authHas($row['refModule'], 'files', '') == false) {
		  $labels['editUrl'] = $serverPath . 'files/viewset/return_uploadmultiple/' 
		                     . $f->alias;
		}
		
		$returnUrl = '/files/uploadmultiple/refModule_' . $i->refModule 
			   . '/refUID_' . $f->refUID . '/';
		
		$labels['deleteForm'] = "
		<form name='deletefile' method='POST' action='%%serverPath%%files/delete/' >
		<input type='hidden' name='action' value='deletefile' />
		<input type='hidden' name='UID' value='" . $f->UID . "' />
		<input type='hidden' name='return' value='" . $returnUrl . "' />
		<input type='submit' value='delete' />
		</form>
		";

		$html .= $theme->replaceLabels($labels, $block);
	}
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
