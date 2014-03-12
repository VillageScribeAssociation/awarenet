<?

	require_once($kapenta->installPath . 'modules/packages/inc/kpackage.class.php');

//--------------------------------------------------------------------------------------------------
//|	make table of package files	showing updates / status
//--------------------------------------------------------------------------------------------------
//arg: UID - UID of an installed package [string]
//opt: packageUID - overrides UID if present [string]

function packages_packagefilestatus($args) {
	global $kapenta;
	global $theme;
	global $kapenta;
	global $kapenta;

	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if ('admin' != $kapenta->user->role) { return ''; }
	if (true == array_key_exists('packageUID', $args)) { $args['UID'] = $args['packageUID']; }
	if (false == array_key_exists('UID', $args)) { return '(package not specified)'; }

	$package = new KPackage($args['UID']);
	if (false == $package->loaded) { return '(could not load package)'; }

	$localFiles = $package->getLocalList();
	
	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$table = array();								//%	[array:array:string]
	$table[] = array('File', 'Status');
	$okitems = array();								//%	[array:array:string]

	$ok = "<span style='ajaxmessage'>ok</span>";

	//----------------------------------------------------------------------------------------------
	//	check files on the system agaist package
	//----------------------------------------------------------------------------------------------
	foreach($localFiles as $lf) {
		$path = $lf['path'] . "<br/>"
			 . "<span><small style='color: #aaa;'><tt>"
			 . $lf['hash'] . " (sha1) " . $lf['type'] . "</tt></small></span>";

		$act = '';

		if (true == array_key_exists($lf['uid'], $package->files)) {
			// file exists in manifest
			if ($package->files[$lf['uid']]['hash'] == $lf['hash']) {
				// hashes macth, file is up to date with manifest
				$act .= $ok;
			} else {
				$act .= ''
				 . '[[:packages::updatefileform::'
				 . 'packageUID=' . $package->UID . '::'
				 . 'fileUID=' . $lf['uid'] . ':]]';
			}
		} else {
			$ret = 'packages/showpackage/' . $package->UID;
			$act .= '[[:admin::removefileform::fileName='. $lf['path'] .'::return='. $ret .':]]';
		}

		if ($ok == $act) { $okitems[] = array($path, $act); }	// end of list
		else { $table[] = array($path, $act); }					// beginning of list
	}

	//----------------------------------------------------------------------------------------------
	//	look for any missing files from the package
	//----------------------------------------------------------------------------------------------
	foreach($package->files as $pf) {
		if (false == $kapenta->fs->exists($pf['path'])) {
			$path = $pf['path'] . "<br/>"
				 . "<span><small style='color: #aaa;'><tt>"
				 . $pf['hash'] . " (sha1) " . $pf['type'] . "</tt></small></span>";

			$act = ''
			 . '[[:packages::downloadfileform::'
			 . 'packageUID=' . $package->UID
			 . '::fileUID=' . $pf['uid'] . ':]]';

			$table[] = array($path, $act);
		}
	}

	//----------------------------------------------------------------------------------------------
	//	done
	//----------------------------------------------------------------------------------------------
	foreach($okitems as $item) { $table[] = $item; }

	$html = $theme->arrayToHtmlTable($table, true, true);
	return $html;
}

?>
