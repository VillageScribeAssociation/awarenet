<?

	require_once($kapenta->installPath . 'modules/wiki/models/mwimport.mod.php');

//--------------------------------------------------------------------------------------------------
//*	Scan a MediaWiki and note all articles in the Wiki_MWImport table
//--------------------------------------------------------------------------------------------------
//http://www.wikihow.com/api.php?action=query&list=allpages&apfrom=Kre&aplimit=50&format=xml

	//----------------------------------------------------------------------------------------------
	//	check arguments and permissions
	//----------------------------------------------------------------------------------------------
	if ('admin' != $user->role) { $page->do403('Admins only.', true); }

	if (false == array_key_exists('wikiUrl', $_POST)) {
		$page->load('modules/wiki/actions/mwscan.if.page.php');
		$page->render();;
	}

	//----------------------------------------------------------------------------------------------
	//	try download list of articles
	//----------------------------------------------------------------------------------------------

	if (true == array_key_exists('wikiUrl', $_POST)) {
		$step = 10;
		$startTitle = '';
		$model = new Wiki_MWImport();

		if (true == array_key_exists('startTitle', $_POST)) { $startTitle = $_POST['startTitle']; }
		if (true == array_key_exists('step', $_POST)) { $step = (int)$_POST['step']; }

		$startUrl = $_POST['wikiUrl'] . "api.php?action=query&list=allpages&format=xml"
					. "&apfrom=" . urlencode($startTitle)
					. "&aplimit=" . $step;

		//------------------------------------------------------------------------------------------
		//	a quick page template
		//------------------------------------------------------------------------------------------
		$header = '' .
"<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<head>
<title>awareNet - wiki - scan</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link href='" . $kapenta->serverPath . "home/css/iframe.css' rel='stylesheet' type='text/css' />
<style type='text/css'>
.style1 {font-size: 9px}
</style>

<script language='javascript'>
	function kPageInit() {
		resizeFrame();
	}

	function resizeFrame() {
		frameObj = window.parent.document.getElementsByName(window.name);
		frameObj[0].height = document.body.offsetHeight + 40;

	}


</script>
</head>

<body onLoad='kPageInit();'> 
<i></i>";

		$footer = "</body></html>";

		echo $header;
		echo "<b>start url:</b> $startUrl<br/><br/>\n";

		//------------------------------------------------------------------------------------------
		//	download
		//------------------------------------------------------------------------------------------
	
		$raw = '<?xml version="1.0" encoding="utf-8"?><api><query-continue><allpages apfrom="Load-PDF-Files-Faster" /></query-continue><query><allpages><p pageid="590859" ns="0" title="Load 35Mm Film Into a Manual Camera" /><p pageid="17181" ns="0" title="Load Adobe PDF Files Faster" /><p pageid="642484" ns="0" title="Load Cargo on a Car" /><p pageid="549246" ns="0" title="Load Dice" /><p pageid="1022803" ns="0" title="Load Games Onto Your Ipod" /><p pageid="486861" ns="0" title="Load Games Onto Your iPod" /><p pageid="212965" ns="0" title="Load Java on to Samsung SGH D900/E250" /><p pageid="291849" ns="0" title="Load Java on to Samsung Sgh D900/E250" /><p pageid="100535" ns="0" title="Load Music Onto Your Ipod" /><p pageid="100509" ns="0" title="Load Music Onto Your iPod" /></allpages></query></api>';

		//echo "<textarea rows='10' cols='80'>$raw</textarea>";
		//TODO: handle error cases

		$addCount = 10;

		while ($addCount > 0) {

			$startUrl = $_POST['wikiUrl'] . "api.php?action=query&list=allpages&format=xml"
					. "&apfrom=" . urlencode($startTitle)
					. "&aplimit=" . $step;

			$raw = implode(file($startUrl));

			$objary = $model->expandAllPages($raw);

			echo "<b>next start:</b> " . $objary['apfrom'] . "<br/>";
			foreach($objary['allpages'] as $article) {

			}

			$addCount = $model->recordNewPages($objary, $_POST['wikiUrl']);

			$model->saveApFrom($objary['apfrom']);
			$startTitle = $objary['apfrom'];
			echo "<b>saved apfrom:</b> " . $model->restoreApFrom() . "<br/>";
			echo "<i>added $addCount new objects</i><br/>\n";

			sleep(5);

		}

	}

?>
