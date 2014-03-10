<?

//--------------------------------------------------------------------------------------------------
//|	form for commiting package changes to repository
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of an installed package [string]
//opt: packageUID - overrides UID if present [string]

function packages_commitbutton($args) {
	global $kapenta;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if (true == array_key_exists('packageUID', $args)) { $args['UID'] = $args['packageUID']; }
	if (false == array_key_exists('UID', $args)) { return ''; }

	$package = new KPackage($args['UID']);
	if (false == $package->loaded) { return '(Could not load package: ' . $args['UID'] . ')'; }

	$ext = $package->extArray();
	$ext['packageUID'] = $ext['UID'];
	if ('' == $ext['username']) { return ''; }		// no credentials, nothing to do

	//----------------------------------------------------------------------------------------------
	//	make file list (informational only)
	//----------------------------------------------------------------------------------------------
	$ext['fileListHtml'] = '';
	$uploadList = $package->getLocalDifferent();
	if (0 == count($uploadList)) { return''; }		// nothing to commit
	foreach($uploadList as $uid => $item) { $ext['fileListHtml'] .= $item['path'] . "<br/>\n"; }

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $kapenta->theme->loadBlock('modules/packages/views/commitbutton.block.php');
	$html = $kapenta->theme->replaceLabels($ext, $block);

	return $html;
}

?>
