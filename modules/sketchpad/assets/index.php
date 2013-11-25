<?php

	die('No. Bad.');

//--------------------------------------------------------------------------------------------------
//*	disabled on awareNet for security reasons
//--------------------------------------------------------------------------------------------------
//+	This may be made into an action if it proves necessesary

/*

header("Content-type: text/html; charset=UTF-8");

//session_start();
$AGENT = Array(
	'root' => dirname(dirname(__FILE__))."/", // /Library/WebServer/Documents/mugtug/
	'project' => dirname(__FILE__)."/", // /Library/WebServer/Documents/mugtug/darkroom/
	'file' => $_SERVER['SCRIPT_NAME'],
);
$SERVER = Array(
	'root' => "mugtug.com/",
	'project' => "mugtug.com/darkroom/",
	'file' => "mugtug.com/darkroom/index.php"
);

$FILE = $_SERVER['SCRIPT_NAME'];
$DIR = substr($FILE, 0, strrpos($FILE, "/"));
$ROOT = substr($DIR, 0, strrpos($DIR, "/")) . "/";

function query_parse($v) {
	$r = Array();
	$v = explode("&", $v);
	foreach($v as $key=>$value) {
		$value = explode("=", $value);
		$r[$value[0]] = $value[1];
	}
	return $r;
};

include("./js_interface.php"); // agent, session_stab, session_grab
include("./js_agent.php"); // upgrades client browser

$AgentManager = new VersionManager(Array(
	prototype=>Array( 'CanvasRenderingContext2D', 'CanvasGradient', 'CanvasPattern' ),
	canvas2D=>Array( 'Typeface' )
));
$agent = $AgentManager->getInfo();

//$AgentManager->updateDB();
$ServerManager = new ServerManager();
$server = $ServerManager->getInfo();

$body = file_get_contents('sketchpad.html');

$b = strpos($body,'<!-- Sketchpad -->');
$body = substr($body,0,$b).'<script type="text/javascript">'.file_get_contents("./all.js").'</script>'.substr($body,strpos($body,'<!-- VersionManager -->',$b));

$body = str_replace('<!-- VersionManager -->', $AgentManager->getFix(), $body);
echo $body;

*/

?>
