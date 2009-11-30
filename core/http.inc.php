<?

//-------------------------------------------------------------------------------------------------
//	create HTTP responses other than 200
//-------------------------------------------------------------------------------------------------

//-------------------------------------------------------------------------------------------------
//      redirect browser (and search engines) to latest alias for a record
//-------------------------------------------------------------------------------------------------

function do301($URI) {
	global $serverPath;
	$URI = $serverPath . $URI;	
 	header( "HTTP/1.1 301 Moved Permanently" );
 	header( "Location: " . $URI ); 
	echo "The page you requested moved <a href='" . $URI  . "'>here</a>.";
	die(0);
}

//-------------------------------------------------------------------------------------------------
//      forbidden
//-------------------------------------------------------------------------------------------------

function do403() {
	global $installPath;
 	header( "HTTP/1.1 403 Forbidden" );
	$errPage = new Page($installPath . 'modules/home/actions/403.page.php');
	$errPage->render();
	die(0);
}

//-------------------------------------------------------------------------------------------------
//      temporary redirect, for shuffling browsers around
//-------------------------------------------------------------------------------------------------

function do302($URI) {
	global $serverPath;
	$URI = $serverPath . $URI;
 	header( "HTTP/1.1 302 Moved Temporarily" );
 	header( "Location: " . $URI ); 
	echo "The page you requested moved <a href='" . $URI . "'>here</a>.";
	die(0);
}

//-------------------------------------------------------------------------------------------------
//      you have died of dysentery
//-------------------------------------------------------------------------------------------------

function do404($msg = '') {
	global $serverPath;
 	header( "HTTP/1.1 404 Not Found" );
	$errPage = new Page($installPath . 'modules/home/actions/404.page.php');
	$errPage->render();
	die(0);
}

//-------------------------------------------------------------------------------------------------
//      xml error
//-------------------------------------------------------------------------------------------------

function doXmlError($msg = '') {
	global $serverPath;
 	header( "HTTP/1.1 404 Not Found" );
	echo "<?xml version=\"1.0\"?>\n";
	echo "<error>$msg</error>\n";
	die(0);
}

?>
