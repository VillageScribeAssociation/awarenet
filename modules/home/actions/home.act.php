<?

//--------------------------------------------------------------------------------------------------
//*	action to show the home page
//--------------------------------------------------------------------------------------------------
//+	this is a stub.  a more advanced version would allow one to choose which static page is Home
//+	according to a system setting.  TODO

	$req->ref = $registry->get('home.frontpage');			// default from registry
	if ('' == $req->ref) { $req->ref = 'frontpage'; }		// fallback - previous default
	include $kapenta->installPath . 'modules/home/actions/show.act.php';

?>
