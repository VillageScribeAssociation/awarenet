<?

	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/kpackage.class.php');

//--------------------------------------------------------------------------------------------------
//|	shows a list of packages installed on this system
//--------------------------------------------------------------------------------------------------

function packages_installedpackages($args) {
	global $kapenta;
	global $theme;
	global $kapenta;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and get list of installed packages
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }

	$updateManager = new KUpdateManager();
	$packages = $updateManager->listAllPackages();		//%	[array:string]

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/packages/views/packagesummary.block.php');

	foreach($packages as $UID => $pkg) {
		if (('installed' == $pkg['status']) && ('' !== trim($pkg['uid'])))
		{
			$package = new KPackage($pkg['uid'], true);
			$labels = $package->extArray();
			$labels['UID'] = $pkg['uid'];
			$html .= $theme->replaceLabels($labels, $block);
		}
	}

	return $html;
}

?>
