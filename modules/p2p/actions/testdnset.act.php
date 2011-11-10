<?

	require_once($kapenta->installPath . 'modules/p2p/models/downloads.set.php');

//--------------------------------------------------------------------------------------------------
//*	development action to test download list
//--------------------------------------------------------------------------------------------------

	if ('admin' != $user->role) { $page->do403(); }
	if ('' == $req->ref) { $page->do404('peerUID not given'); }

	$dnset = new P2P_Downloads($req->ref);

	if (true == array_key_exists('add', $req->args)) {
		$dnset->add('data/videos/1/4/5/145824693138495692.swf');
		$dnset->save();
	}

	if (true == array_key_exists('remove', $req->args)) {
		$check = $dnset->remove('data/videos/1/4/5/145824693138495692.swf');
		if (false == $check) { echo "Could not remove file...<br/>"; }
		$check = $dnset->save();
		if (false == $check) { echo "Could not save download list...<br/>"; }
	}

	echo $dnset->toHtml();

?>
