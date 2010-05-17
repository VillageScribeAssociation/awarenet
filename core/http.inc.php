<?

//-------------------------------------------------------------------------------------------------
//*	create HTTP responses other than 200
//-------------------------------------------------------------------------------------------------
//+	Note that full suite of HTTP response codes is not represented, add others as needed
//+ Be aware that some PHP implementations do not respect die(), and may treat it as 'return'

//-------------------------------------------------------------------------------------------------
//|	redirect browser (and search engines) to a different URL, eg when a recordAlias changes
//-------------------------------------------------------------------------------------------------
//arg: URI - url relative to serverPath [string]
//: note that this should not be used for this like for submissions, only when a URL has changed

function do301($URI) {
	global $serverPath;
	$URI = $serverPath . $URI;	
 	header( "HTTP/1.1 301 Moved Permanently" );
 	header( "Location: " . $URI ); 
	echo "The page you requested moved <a href='" . $URI  . "'>here</a>.";
	die(0);
}

//-------------------------------------------------------------------------------------------------
//|	forbidden
//-------------------------------------------------------------------------------------------------
//: called when authentication fails

function do403() {
	global $installPath;
 	header( "HTTP/1.1 403 Forbidden" );
	$errPage = new Page($installPath . 'modules/home/actions/403.page.php');
	$errPage->render();
	die(0);
}

//-------------------------------------------------------------------------------------------------
//|	temporary redirect, for shuffling browsers around
//-------------------------------------------------------------------------------------------------
//arg: URI - url relative to serverPath [string]

function do302($URI) {
	global $serverPath;
	$URI = $serverPath . $URI;
 	header( "HTTP/1.1 302 Moved Temporarily" );
 	header( "Location: " . $URI ); 
	echo "The page you requested moved <a href='" . $URI . "'>here</a>.";
	die(0);
}

//-------------------------------------------------------------------------------------------------
//|	you have died of dysentery
//-------------------------------------------------------------------------------------------------
//opt: msg - any additional information about the messing page (eg, removed TOS violation) [string]

function do404($msg = '') {
	global $serverPath;
 	header( "HTTP/1.1 404 Not Found" );
	$errPage = new Page($installPath . 'modules/home/actions/404.page.php');
	$errPage->render();
	die(0);
}

//-------------------------------------------------------------------------------------------------
//|	xml error
//-------------------------------------------------------------------------------------------------
//opt: msg - any additional information about the messing page (eg, removed TOS violation) [string]
//: returned by various APIs on failure

function doXmlError($msg = '') {
	global $serverPath;
 	header( "HTTP/1.1 404 Not Found" );
	echo "<?xml version=\"1.0\"?>\n";
	echo "<error>$msg</error>\n";
	die(0);
}

?>
