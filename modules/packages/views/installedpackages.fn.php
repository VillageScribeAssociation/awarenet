<?

	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/kpackage.class.php');

//--------------------------------------------------------------------------------------------------
//|	shows a list of packages installed on this system
//--------------------------------------------------------------------------------------------------

function packages_installedpackages($args) {
	global $user;
	global $theme;
	global $registry;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and get list of installed packages
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { return ''; }

	$updateManager = new KUpdateManager();
	$installed = $updateManager->listInstalledPackages();		//%	[array:string]

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/packages/views/packagesummary.block.php');

	foreach($installed as $UID => $package) {
		$package = new KPackage($UID);
		$html .= $theme->replaceLabels($package->extArray(), $block);
	}

	return $html;
}

?>
