<?

//--------------------------------------------------------------------------------------------------
//*	this is the default action of the home module, it loads a static page or the home page
//--------------------------------------------------------------------------------------------------

	if ('' == $req->ref) { include $kapenta->installPath . 'modules/home/actions/home.act.php';	}
	else { include $kapenta->installPath . 'modules/home/actions/show.act.php'; }

?>
