<?

	require_once($kapenta->installPath . 'modules/p2p/inc/klargefile.class.php');

//--------------------------------------------------------------------------------------------------
//*	test local KLargeFile object
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }

	$klf = new KLargeFile('data/videos/2/1/1/211976849720202474');
	$klf->makeFromFile();

	echo '<pre>' . htmlentities($klf->toXml()) . '</pre>';


?>
