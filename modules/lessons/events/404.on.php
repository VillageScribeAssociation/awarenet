<?
	require_once($kapenta->installPath . 'modules/lessons/inc/khan.inc.php');

//-------------------------------------------------------------------------------------------------
//|	fired when a KA Lite request is identified by index.php
//-------------------------------------------------------------------------------------------------
//arg: method - http POST or GET [string]
//arg: uri - uri of http call [string]
//arg: query - parameters in http call [string]
//arg: remoteAddr - address of caller [string]
//arg: remotePort - port of caller [string]
//arg: remotePort - args of POST [string]

function lessons__cb_404($args) {
    global $kapenta;

    $kaRoutes = array(
        'static/css',
		'static/images',
		'static/data',
		'static/js',
		'static/video-js',
		'content/',
		'jsi18n/',
		'api/info',
		'api/status',
		'api/get',
		'api/start',
		'api/delete',
		'api/check_video',
		'api/check_subtitle',
		'api/save',
		'api/cancel',
		'api/videos',
		'api/updates',
		'math',
		'science',
		'economics-finance-domain',
		'humanities',
		'test-prep',
		'discovery-lab',
		'exercisedashboard',
		'coachreports/table',
		'securesync/api/status',
		'coachreports/scatter',
		'coachreports/api',
		'coachreports/timeline',
		'coachreports/student'
    );

    foreach ($kaRoutes as $route) {

    	if (false !== strpos($kapenta->request->raw, $route)) {

    		$rawdata = file_get_contents('php://input'); //for POSTS
		
    		$requestURI = $_SERVER['REQUEST_URI'];
    		$requestQuery = $_SERVER['QUERY_STRING'];
    		$remoteAddr = $_SERVER['REMOTE_ADDR'];
    		$remotePort = $_SERVER['REMOTE_PORT'];
    		$requestMethod = $_SERVER['REQUEST_METHOD'];
    		$postArgs = $rawdata;

    		$args = array(
    			'method' => $requestMethod,
    			'uri' => $requestURI,
    			'query' => $requestQuery,
    			'remoteAddr' => $remoteAddr,
    			'remotePort' => $remotePort,
    			'postArgs' => $postArgs
    		);

    		$kapenta->raiseEvent('lessons', 'khanlite_request', $args);
    	}

    }

    //  prevent 404 page from rendering
    die();

}

?>
