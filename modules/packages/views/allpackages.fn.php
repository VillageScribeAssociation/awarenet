<?

	require_once($kapenta->installPath . 'modules/packages/inc/kupdatemanager.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/kpackage.class.php');
	require_once($kapenta->installPath . 'modules/packages/inc/ksource.class.php');

//--------------------------------------------------------------------------------------------------
//|	view for listing all packages which are or may be installed on this system
//--------------------------------------------------------------------------------------------------
//opt: status - limit to packages with this status [string]

function packages_allpackages($args) {
	global $kapenta;
	global $theme;

	$html = '';						//%	return value [string]
	$status = '';					//%	filter [string]

	//----------------------------------------------------------------------------------------------
	//	check user role and any arguments, load list of packages
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if (true == array_key_exists('status', $args)) { $status = $args['status']; }

	$updateManager = new KUpdateManager();
	$packages = $updateManager->listAllPackages();

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/packages/views/packagesummary.block.php');
	$installForm = $theme->loadBlock('modules/packages/views/installpackageform.block.php');

	foreach($packages as $UID => $pkg) {
		$source = new KSource($pkg['source']);
		$meta = $source->getPackageDetails($UID);
		foreach($meta as $key => $value) { $pkg[$key] = $value; }
		$pkg['UID'] = $UID;

		if (('' !== $status) && ($status == $pkg['status'])) {

			$html .= $theme->replaceLabels($pkg, $block); 

		}
	}

	if (0 == count($packages)) {
		$html .= ''
		 . "<div class='inlinequote'>No available packages, please add or update sources.</div>";
	}

	return $html;
}

?>
