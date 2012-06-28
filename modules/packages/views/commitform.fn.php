<?

//--------------------------------------------------------------------------------------------------
//|	form for commiting package changes to repository
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of an installed package [string]
//opt: packageUID - overrides UID if present [string]

function packages_commitform($args) {
	global $user;
	global $theme;
	global $registry;
	global $kapenta;

	$maxUploads = 200;			//%	commit in batches [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }
	if (true == array_key_exists('packageUID', $args)) { $args['UID'] = $args['packageUID']; }
	if (false == array_key_exists('UID', $args)) { return ''; }

	$package = new KPackage($args['UID']);
	if (false == $package->loaded) { return '(Could not load package: ' . $args['UID'] . ')'; }

	$ext = $package->extArray();
	if ('' == $ext['username']) { return ''; }		// no credentials, nothing to do

	//----------------------------------------------------------------------------------------------
	//	make file list (informational only)
	//----------------------------------------------------------------------------------------------
	$ext['fileListHtml'] = '';
	$uploadList = $package->getLocalDifferent();
	if (0 == count($uploadList)) { return''; }		// nothing to commit

	foreach($uploadList as $uid => $item) {
		if ($maxUploads > 0) {
			$fieldName = 'file' . $kapenta->createUID();
			$ext['fileListHtml'] .= ''
			 . "<input type='checkbox' name='$fieldName' value='" . $item['path'] . "' checked=1 />"
			 . $item['path'] . "<br/>\n";
			$maxUploads--;
		}
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/packages/views/commitform.block.php');
	$html = $theme->replaceLabels($ext, $block);

	if (0 == $maxUploads) {
		$html .= ''
		 . "<b>IMPORTANT:</b> Some files are not included in this commit.  "
		 . "You can commit a maximum of 200 files at once.<br/>";	
	}

	return $html;
}

?>
