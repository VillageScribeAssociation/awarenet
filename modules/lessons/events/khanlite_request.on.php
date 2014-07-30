<?
	require_once($kapenta->installPath . 'modules/lessons/inc/khan.inc.php');

//-------------------------------------------------------------------------------------------------
//|	This request is not used anymore! Can be deleted in the next round! fired when a KA Lite request is identified by index.php
//-------------------------------------------------------------------------------------------------
//arg: method - http POST or GET [string]
//arg: uri - uri of http call [string]
//arg: query - parameters in http call [string]
//arg: remoteAddr - address of caller [string]
//arg: remotePort - port of caller [string]
//arg: remotePort - args of POST [string]

function lessons__cb_khanlite_request($args) {
	die();
	//----------------------------------------------------------------------------------------------
	//	ok, done
	//----------------------------------------------------------------------------------------------	
	return true;
}

//-------------------------------------------------------------------------------------------------
?>
