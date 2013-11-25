<?php

//--------------------------------------------------------------------------------------------------
//* Automatically generated API wrapper for Kapenta v2 modules
//--------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------
//classname: KRequest (core/krequest.class.php)
//--------------------------------------------------------------------------------------------------

//Propety: raw 
//Propety: parts 
//Propety: module 
//Propety: action 
//Propety: ref 
//Propety: args 
//Propety: mvc 
//Propety: local 
//Propety: agent 
class KLegacy_request {

	public function __get($name) {
		global $kapenta;
		switch($name) {
			case 'raw':	return $kapenta->request->raw;	break;
			case 'parts':	return $kapenta->request->parts;	break;
			case 'module':	return $kapenta->request->module;	break;
			case 'action':	return $kapenta->request->action;	break;
			case 'ref':	return $kapenta->request->ref;	break;
			case 'args':	return $kapenta->request->args;	break;
			case 'mvc':	return $kapenta->request->mvc;	break;
			case 'local':	return $kapenta->request->local;	break;
			case 'agent':	return $kapenta->request->agent;	break;
		}
	}

	public function __set($name, $value) {
		global $kapenta;
		switch($name) {
			case 'raw':	$kapenta->request->raw = $value;	break;
			case 'parts':	$kapenta->request->parts = $value;	break;
			case 'module':	$kapenta->request->module = $value;	break;
			case 'action':	$kapenta->request->action = $value;	break;
			case 'ref':	$kapenta->request->ref = $value;	break;
			case 'args':	$kapenta->request->args = $value;	break;
			case 'mvc':	$kapenta->request->mvc = $value;	break;
			case 'local':	$kapenta->request->local = $value;	break;
			case 'agent':	$kapenta->request->agent = $value;	break;
		}
	}

	function getRequestArguments() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('request', 'getRequestArguments');
		return $kapenta->request->getRequestArguments();
	}

	function splitRequestURI() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('request', 'splitRequestURI');
		return $kapenta->request->splitRequestURI();
	}

	function checkIfLocal() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('request', 'checkIfLocal');
		return $kapenta->request->checkIfLocal();
	}

	function ipToInt($ip) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('request', 'ipToInt');
		return $kapenta->request->ipToInt($ip);
	}

	function guessDeviceProfile() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('request', 'guessDeviceProfile');
		return $kapenta->request->guessDeviceProfile();
	}

	function toArray() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('request', 'toArray');
		return $kapenta->request->toArray();
	}

}

//--------------------------------------------------------------------------------------------------
//classname: KRegistry (core/kregistry.class.php)
//--------------------------------------------------------------------------------------------------

//Propety: keys 
//Propety: files 
//Propety: path 
//Propety: lockTTL 
//Propety: maxFailures 
class KLegacy_registry {

	public function __get($name) {
		global $kapenta;
		switch($name) {
			case 'keys':	return $kapenta->registry->keys;	break;
			case 'files':	return $kapenta->registry->files;	break;
			case 'path':	return $kapenta->registry->path;	break;
			case 'lockTTL':	return $kapenta->registry->lockTTL;	break;
			case 'maxFailures':	return $kapenta->registry->maxFailures;	break;
		}
	}

	public function __set($name, $value) {
		global $kapenta;
		switch($name) {
			case 'keys':	$kapenta->registry->keys = $value;	break;
			case 'files':	$kapenta->registry->files = $value;	break;
			case 'path':	$kapenta->registry->path = $value;	break;
			case 'lockTTL':	$kapenta->registry->lockTTL = $value;	break;
			case 'maxFailures':	$kapenta->registry->maxFailures = $value;	break;
		}
	}

	function load($prefix) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('registry', 'load');
		return $kapenta->registry->load($prefix);
	}

	function loadAll() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('registry', 'loadAll');
		return $kapenta->registry->loadAll();
	}

	function save($prefix) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('registry', 'save');
		return $kapenta->registry->save($prefix);
	}

	function getLock($prefix) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('registry', 'getLock');
		return $kapenta->registry->getLock($prefix);
	}

	function setLock($prefix, $type) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('registry', 'setLock');
		return $kapenta->registry->setLock($prefix,  $type);
	}

	function has($key) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('registry', 'has');
		return $kapenta->registry->has($key);
	}

	function get($key, $forceReload = false) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('registry', "get('$key')");
		return $kapenta->registry->get($key, $forceReload);
	}

	function set($key, $value) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('registry', "set('$key','$value')");
		return $kapenta->registry->set($key,  $value);
	}

	function delete($key) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('registry', 'delete');
		return $kapenta->registry->delete($key);
	}

	function getPrefix($key) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('registry', 'getPrefix');
		return $kapenta->registry->getPrefix($key);
	}

	function listFiles($fullName = false) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('registry', 'listFiles');
		return $kapenta->registry->listFiles($fullName);
	}

	function search($prefix, $begins) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('registry', 'search');
		return $kapenta->registry->search($prefix,  $begins);
	}

	function log($prefix, $event, $key, $value) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('registry', 'log');
		return $kapenta->registry->log($prefix,  $event,  $key,  $value);
	}

	function toHtml($prefix) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('registry', 'toHtml');
		return $kapenta->registry->toHtml($prefix);
	}

	function filePutContents($fileName, $contents, $inData = false, $phpWrap = false, $m = 'wb+') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('registry', 'filePutContents');
		return $kapenta->registry->filePutContents($fileName,  $contents, $inData, $phpWrap, $m);
	}

}

//--------------------------------------------------------------------------------------------------
//classname: KDBDriver_MySQL (core/dbdriver/mysql.dbd.php)
//--------------------------------------------------------------------------------------------------

//Propety: type 
//Propety: host 
//Propety: user 
//Propety: pass 
//Propety: name 
//Propety: tables 
//Propety: tablesLoaded 
//Propety: count 
//Propety: time 
//Propety: lasterr 
//Propety: lastquery 
class KLegacy_db {

	public function __get($name) {
		global $kapenta;
		switch($name) {
			case 'type':	return $kapenta->db->type;	break;
			case 'host':	return $kapenta->db->host;	break;
			case 'user':	return $kapenta->db->user;	break;
			case 'pass':	return $kapenta->db->pass;	break;
			case 'name':	return $kapenta->db->name;	break;
			case 'tables':	return $kapenta->db->tables;	break;
			case 'tablesLoaded':	return $kapenta->db->tablesLoaded;	break;
			case 'count':	return $kapenta->db->count;	break;
			case 'time':	return $kapenta->db->time;	break;
			case 'lasterr':	return $kapenta->db->lasterr;	break;
			case 'lastquery':	return $kapenta->db->lastquery;	break;
		}
	}

	public function __set($name, $value) {
		global $kapenta;
		switch($name) {
			case 'type':	$kapenta->db->type = $value;	break;
			case 'host':	$kapenta->db->host = $value;	break;
			case 'user':	$kapenta->db->user = $value;	break;
			case 'pass':	$kapenta->db->pass = $value;	break;
			case 'name':	$kapenta->db->name = $value;	break;
			case 'tables':	$kapenta->db->tables = $value;	break;
			case 'tablesLoaded':	$kapenta->db->tablesLoaded = $value;	break;
			case 'count':	$kapenta->db->count = $value;	break;
			case 'time':	$kapenta->db->time = $value;	break;
			case 'lasterr':	$kapenta->db->lasterr = $value;	break;
			case 'lastquery':	$kapenta->db->lastquery = $value;	break;
		}
	}

	function query($query) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'query');
		return $kapenta->db->query($query);
	}

	function transactionStart() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'transactionStart');
		return $kapenta->db->transactionStart();
	}

	function transactionEnd() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'transactionEnd');
		return $kapenta->db->transactionEnd();
	}

	function fetchAssoc($handle) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'fetchAssoc');
		return $kapenta->db->fetchAssoc($handle);
	}

	function numRows($handle) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'numRows');
		return $kapenta->db->numRows($handle);
	}

	function validate($serialized, $dbSchema) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'validate');
		return $kapenta->db->validate($serialized,  $dbSchema);
	}

	function load($UID, $dbSchema) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'load');
		return $kapenta->db->load($UID,  $dbSchema);
	}

	function loadAlias($raUID, $dbSchema) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'loadAlias');
		return $kapenta->db->loadAlias($raUID,  $dbSchema);
	}

	function save($data, $dbSchema, $setdefaults = true, $broadcast = true, $revision = true) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'save');
		return $kapenta->db->save($data,  $dbSchema, $setdefaults, $broadcast, $revision);
	}

	function delete($UID, $dbSchema) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'delete');
		return $kapenta->db->delete($UID,  $dbSchema);
	}

	function updateQuiet($model, $UID, $field, $value) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'updateQuiet');
		return $kapenta->db->updateQuiet($model,  $UID,  $field,  $value);
	}

	function loadRange($model, $fields ='*', $conditions ='', $by ='', $limit ='', $offset ='') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'loadRange');
		return $kapenta->db->loadRange($model, $fields, $conditions, $by, $limit, $offset);
	}

	function countRange($model, $conditions = '') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'countRange');
		return $kapenta->db->countRange($model, $conditions);
	}

	function loadTables() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'loadTables');
		return $kapenta->db->loadTables();
	}

	function listTables() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'listTables');
		return $kapenta->db->listTables();
	}

	function tableExists($model) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'tableExists');
		return $kapenta->db->tableExists($model);
	}

	function makeBlank($dbSchema) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'makeBlank');
		return $kapenta->db->makeBlank($dbSchema);
	}

	function getSchema($tableName) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'getSchema');
		return $kapenta->db->getSchema($tableName);
	}

	function checkSchema($dbSchema) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'checkSchema');
		return $kapenta->db->checkSchema($dbSchema);
	}

	function quoteType($dbType) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'quoteType');
		return $kapenta->db->quoteType($dbType);
	}

	function queryToArray($sql) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'queryToArray');
		return $kapenta->db->queryToArray($sql);
	}

	function objectExists($model, $UID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'objectExists');
		return $kapenta->db->objectExists($model,  $UID);
	}

	function getObject($model, $UID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'getObject');
		return $kapenta->db->getObject($model,  $UID);
	}

	function getObjectXml($model, $UID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'getObjectXml');
		return $kapenta->db->getObjectXml($model,  $UID);
	}

	function storeObjectXml($xml, $setdefaults = true, $broadcast = true, $revision = true) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'storeObjectXml');
		return $kapenta->db->storeObjectXml($xml, $setdefaults, $broadcast, $revision);
	}

	function objectXmlToArray($xml) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'objectXmlToArray');
		return $kapenta->db->objectXmlToArray($xml);
	}

	function isShared($model, $UID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'isShared');
		return $kapenta->db->isShared($model,  $UID);
	}

	function datetime($timestamp = 0) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'datetime');
		return $kapenta->db->datetime($timestamp);
	}

	function addMarkup($text) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'addMarkup');
		return $kapenta->db->addMarkup($text);
	}

	function removeMarkup($text) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'removeMarkup');
		return $kapenta->db->removeMarkup($text);
	}

	function amArray($ary) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'amArray');
		return $kapenta->db->amArray($ary);
	}

	function rmArray($ary) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'rmArray');
		return $kapenta->db->rmArray($ary);
	}

	function serialize($fields) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'serialize');
		return $kapenta->db->serialize($fields);
	}

	function unserialize($fields64) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('db', 'unserialize');
		return $kapenta->db->unserialize($fields64);
	}

}

//--------------------------------------------------------------------------------------------------
//classname: KUtils (core/kutils.class.php)
//--------------------------------------------------------------------------------------------------

class KLegacy_utils {

	public function __get($name) {
		global $kapenta;
		switch($name) {
		}
	}

	public function __set($name, $value) {
		global $kapenta;
		switch($name) {
		}
	}

	function random($min = 0, $max = 1) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'random');
		return $kapenta->utils->random($min, $max);
	}

	function cleanHtml($html) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'cleanHtml');
		return $kapenta->utils->cleanHtml($html);
	}

	function cleanTitle($txt) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'cleanTitle');
		return $kapenta->utils->cleanTitle($txt);
	}

	function cleanYesNo($yesno) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'cleanYesNo');
		return $kapenta->utils->cleanYesNo($yesno);
	}

	function cleanString($toClean) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'cleanString');
		return $kapenta->utils->cleanString($toClean);
	}

	function stripHTML($someText) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'stripHTML');
		return $kapenta->utils->stripHTML($someText);
	}

	function trimHtml($html) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'trimHtml');
		return $kapenta->utils->trimHtml($html);
	}

	function makeAlphaNumeric($txt, $allow = '') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'makeAlphaNumeric');
		return $kapenta->utils->makeAlphaNumeric($txt, $allow);
	}

	function arrayToXml2d($rootType, $members, $docType = false) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'arrayToXml2d');
		return $kapenta->utils->arrayToXml2d($rootType,  $members, $docType);
	}

	function txtToHtml($txt) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'txtToHtml');
		return $kapenta->utils->txtToHtml($txt);
	}

	function addHtmlEntities($txt) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'addHtmlEntities');
		return $kapenta->utils->addHtmlEntities($txt);
	}

	function removeHtmlEntities($txt) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'removeHtmlEntities');
		return $kapenta->utils->removeHtmlEntities($txt);
	}

	function b64wrap($txt, $width = 80) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'b64wrap');
		return $kapenta->utils->b64wrap($txt, $width);
	}

	function base64EncodeJs($varName, $text, $scriptTags = true) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'base64EncodeJs');
		return $kapenta->utils->base64EncodeJs($varName,  $text, $scriptTags);
	}

	function strdelim($str, $start, $end) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'strdelim');
		return $kapenta->utils->strdelim($str,  $start,  $end);
	}

	function jsMarkup($txt) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'jsMarkup');
		return $kapenta->utils->jsMarkup($txt);
	}

	function printFileSize($bytes) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'printFileSize');
		return $kapenta->utils->printFileSize($bytes);
	}

	function curlGet($url, $password = '', $headers = false, $cookie = '') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'curlGet');
		return $kapenta->utils->curlGet($url, $password, $headers, $cookie);
	}

	function curlPost($url, $postvars, $headers = false, $cookie = '') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('utils', 'curlPost');
		return $kapenta->utils->curlPost($url,  $postvars, $headers, $cookie);
	}

}

//--------------------------------------------------------------------------------------------------
//classname: KTheme (core/ktheme.class.php)
//--------------------------------------------------------------------------------------------------

//Propety: name 
//Propety: loaded 
//Propety: style 
//Propety: styleLoaded 
class KLegacy_theme {

	public function __get($name) {
		global $kapenta;
		switch($name) {
			case 'name':	return $kapenta->theme->name;	break;
			case 'loaded':	return $kapenta->theme->loaded;	break;
			case 'style':	return $kapenta->theme->style;	break;
			case 'styleLoaded':	return $kapenta->theme->styleLoaded;	break;
		}
	}

	public function __set($name, $value) {
		global $kapenta;
		switch($name) {
			case 'name':	$kapenta->theme->name = $value;	break;
			case 'loaded':	$kapenta->theme->loaded = $value;	break;
			case 'style':	$kapenta->theme->style = $value;	break;
			case 'styleLoaded':	$kapenta->theme->styleLoaded = $value;	break;
		}
	}

	function load($theme) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', 'load');
		return $kapenta->theme->load($theme);
	}

	function loadBlock($fileName) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', "loadBlock('$fileName')");
		return $kapenta->theme->loadBlock($fileName);
	}

	function replaceLabels($labels, $txt) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', 'replaceLabels');
		return $kapenta->theme->replaceLabels($labels,  $txt);
	}

	function saveBlock($fileName, $raw) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', 'saveBlock');
		return $kapenta->theme->saveBlock($fileName,  $raw);
	}

	function stripBlocks($txt) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', 'stripBlocks');
		return $kapenta->theme->stripBlocks($txt);
	}

	function makeSummary($html, $length = 300) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', 'makeSummary');
		return $kapenta->theme->makeSummary($html, $length);
	}

	function runBlock($ba) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', 'runBlock');
		return $kapenta->theme->runBlock($ba);
	}

	function getBlockApiFile($module, $fn) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', 'getBlockApiFile');
		return $kapenta->theme->getBlockApiFile($module,  $fn);
	}

	function findUniqueBlocks($txt) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', 'findUniqueBlocks');
		return $kapenta->theme->findUniqueBlocks($txt);
	}

	function blockToArray($block) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', 'blockToArray');
		return $kapenta->theme->blockToArray($block);
	}

	function expandBlocks($txt, $area = 'content') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', 'expandBlocks');
		return $kapenta->theme->expandBlocks($txt, $area);
	}

	function arrayToHtmlTable($ary, $wireframe = false, $firstrowtitle = false) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', 'arrayToHtmlTable');
		return $kapenta->theme->arrayToHtmlTable($ary, $wireframe, $firstrowtitle);
	}

	function tb($html, $title, $divId, $toggle = 'off') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', 'tb');
		return $kapenta->theme->tb($html,  $title,  $divId, $toggle);
	}

	function ntb($html, $title, $divId, $toggle = 'off') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', 'ntb');
		return $kapenta->theme->ntb($html,  $title,  $divId, $toggle);
	}

	function makeTagCloud($tags) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', 'makeTagCloud');
		return $kapenta->theme->makeTagCloud($tags);
	}

	function readStyle() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', 'readStyle');
		return $kapenta->theme->readStyle();
	}

	function writeStyle() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('theme', 'writeStyle');
		return $kapenta->theme->writeStyle();
	}

}

//--------------------------------------------------------------------------------------------------
//classname: KAliases (core/kaliases.class.php)
//--------------------------------------------------------------------------------------------------

//Propety: maxLen 
class KLegacy_aliases {

	public function __get($name) {
		global $kapenta;
		switch($name) {
			case 'maxLen':	return $kapenta->aliases->maxLen;	break;
		}
	}

	public function __set($name, $value) {
		global $kapenta;
		switch($name) {
			case 'maxLen':	$kapenta->aliases->maxLen = $value;	break;
		}
	}

	function create($refModule, $refModel, $refUID, $plainText) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('aliases', 'create');
		return $kapenta->aliases->create($refModule,  $refModel,  $refUID,  $plainText);
	}

	function findRedirect($model) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('aliases', 'findRedirect');
		return $kapenta->aliases->findRedirect($model);
	}

	function saveAlias($refModule, $refModel, $refUID, $alias) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('aliases', 'saveAlias');
		return $kapenta->aliases->saveAlias($refModule,  $refModel,  $refUID,  $alias);
	}

	function getDbSchema() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('aliases', 'getDbSchema');
		return $kapenta->aliases->getDbSchema();
	}

	function stringToAlias($plainText) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('aliases', 'stringToAlias');
		return $kapenta->aliases->stringToAlias($plainText);
	}

	function deleteAll($refModule, $refModel, $refUID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('aliases', 'deleteAll');
		return $kapenta->aliases->deleteAll($refModule,  $refModel,  $refUID);
	}

	function getDefault($model, $UID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('aliases', 'getDefault');
		return $kapenta->aliases->getDefault($model,  $UID);
	}

	function getOwner($refModule, $refModel, $alias) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('aliases', 'getOwner');
		return $kapenta->aliases->getOwner($refModule,  $refModel,  $alias);
	}

	function getAll($refModule, $refModel, $refUID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('aliases', 'getAll');
		return $kapenta->aliases->getAll($refModule,  $refModel,  $refUID);
	}

	function findAvailable($refModule, $refModel, $default, $depth = 0) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('aliases', 'findAvailable');
		return $kapenta->aliases->findAvailable($refModule,  $refModel,  $default, $depth);
	}

}

//--------------------------------------------------------------------------------------------------
//classname: Users_Session (modules/users/models/session.mod.php)
//--------------------------------------------------------------------------------------------------

//Propety: data 
//Propety: dbSchema 
//Propety: loaded 
//Propety: user 
//Propety: role 
//Propety: debug 
//Propety: UID 
//Propety: status 
//Propety: serverUID 
//Propety: serverName 
//Propety: serverUrl 
//Propety: createdOn 
//Propety: createdBy 
//Propety: editedOn 
//Propety: editedBy 
//Propety: shared 
//Propety: maxAge 
class KLegacy_session {

	public function __get($name) {
		global $kapenta;
		switch($name) {
			case 'data':	return $kapenta->session->data;	break;
			case 'dbSchema':	return $kapenta->session->dbSchema;	break;
			case 'loaded':	return $kapenta->session->loaded;	break;
			case 'user':	return $kapenta->session->user;	break;
			case 'role':	return $kapenta->session->role;	break;
			case 'debug':	return $kapenta->session->debug;	break;
			case 'UID':	return $kapenta->session->UID;	break;
			case 'status':	return $kapenta->session->status;	break;
			case 'serverUID':	return $kapenta->session->serverUID;	break;
			case 'serverName':	return $kapenta->session->serverName;	break;
			case 'serverUrl':	return $kapenta->session->serverUrl;	break;
			case 'createdOn':	return $kapenta->session->createdOn;	break;
			case 'createdBy':	return $kapenta->session->createdBy;	break;
			case 'editedOn':	return $kapenta->session->editedOn;	break;
			case 'editedBy':	return $kapenta->session->editedBy;	break;
			case 'shared':	return $kapenta->session->shared;	break;
			case 'maxAge':	return $kapenta->session->maxAge;	break;
		}
	}

	public function __set($name, $value) {
		global $kapenta;
		switch($name) {
			case 'data':	$kapenta->session->data = $value;	break;
			case 'dbSchema':	$kapenta->session->dbSchema = $value;	break;
			case 'loaded':	$kapenta->session->loaded = $value;	break;
			case 'user':	$kapenta->session->user = $value;	break;
			case 'role':	$kapenta->session->role = $value;	break;
			case 'debug':	$kapenta->session->debug = $value;	break;
			case 'UID':	$kapenta->session->UID = $value;	break;
			case 'status':	$kapenta->session->status = $value;	break;
			case 'serverUID':	$kapenta->session->serverUID = $value;	break;
			case 'serverName':	$kapenta->session->serverName = $value;	break;
			case 'serverUrl':	$kapenta->session->serverUrl = $value;	break;
			case 'createdOn':	$kapenta->session->createdOn = $value;	break;
			case 'createdBy':	$kapenta->session->createdBy = $value;	break;
			case 'editedOn':	$kapenta->session->editedOn = $value;	break;
			case 'editedBy':	$kapenta->session->editedBy = $value;	break;
			case 'shared':	$kapenta->session->shared = $value;	break;
			case 'maxAge':	$kapenta->session->maxAge = $value;	break;
		}
	}

	function load($UID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'load');
		return $kapenta->session->load($UID);
	}

	function loadUser($userUID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'loadUser');
		return $kapenta->session->loadUser($userUID);
	}

	function loadArray($ary) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'loadArray');
		return $kapenta->session->loadArray($ary);
	}

	function save() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'save');
		return $kapenta->session->save();
	}

	function verify() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'verify');
		return $kapenta->session->verify();
	}

	function getDbSchema() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'getDbSchema');
		return $kapenta->session->getDbSchema();
	}

	function toArray() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'toArray');
		return $kapenta->session->toArray();
	}

	function extArray() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'extArray');
		return $kapenta->session->extArray();
	}

	function toXml($xmlDec = false, $indent = '') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'toXml');
		return $kapenta->session->toXml($xmlDec, $indent);
	}

	function delete() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'delete');
		return $kapenta->session->delete();
	}

	function updateLastSeen() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'updateLastSeen');
		return $kapenta->session->updateLastSeen();
	}

	function logout() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'logout');
		return $kapenta->session->logout();
	}

	function has($key) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'has');
		return $kapenta->session->has($key);
	}

	function set($key, $value) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', "set('$key', '$value')");
		return $kapenta->session->set($key,  $value);
	}

	function get($key) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', "get('$key')");
		return $kapenta->session->get($key);
	}

	function msg($message, $icon = 'info') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'msg');
		return $kapenta->session->msg($message, $icon);
	}

	function msgAdmin($message, $icon = 'info') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'msgAdmin');
		return $kapenta->session->msgAdmin($message, $icon);
	}

	function messagesToHtml() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'messagesToHtml');
		return $kapenta->session->messagesToHtml();
	}

	function clearMessages() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('session', 'clearMessages');
		return $kapenta->session->clearMessages();
	}

}

//--------------------------------------------------------------------------------------------------
//classname: Users_User (modules/users/models/user.mod.php)
//--------------------------------------------------------------------------------------------------

//Propety: data 
//Propety: dbSchema 
//Propety: loaded 
//Propety: UID 
//Propety: role 
//Propety: school 
//Propety: grade 
//Propety: firstname 
//Propety: surname 
//Propety: username 
//Propety: password 
//Propety: lang 
//Propety: profile 
//Propety: permissions 
//Propety: settings 
//Propety: lastOnline 
//Propety: createdOn 
//Propety: createdBy 
//Propety: editedOn 
//Propety: editedBy 
//Propety: alias 
//Propety: registry 
//Propety: registryLoaded 
//Propety: profileFields 
class KLegacy_user {

	public function __get($name) {
		global $kapenta;
		switch($name) {
			case 'data':	return $kapenta->user->data;	break;
			case 'dbSchema':	return $kapenta->user->dbSchema;	break;
			case 'loaded':	return $kapenta->user->loaded;	break;
			case 'UID':	return $kapenta->user->UID;	break;
			case 'role':	return $kapenta->user->role;	break;
			case 'school':	return $kapenta->user->school;	break;
			case 'grade':	return $kapenta->user->grade;	break;
			case 'firstname':	return $kapenta->user->firstname;	break;
			case 'surname':	return $kapenta->user->surname;	break;
			case 'username':	return $kapenta->user->username;	break;
			case 'password':	return $kapenta->user->password;	break;
			case 'lang':	return $kapenta->user->lang;	break;
			case 'profile':	return $kapenta->user->profile;	break;
			case 'permissions':	return $kapenta->user->permissions;	break;
			case 'settings':	return $kapenta->user->settings;	break;
			case 'lastOnline':	return $kapenta->user->lastOnline;	break;
			case 'createdOn':	return $kapenta->user->createdOn;	break;
			case 'createdBy':	return $kapenta->user->createdBy;	break;
			case 'editedOn':	return $kapenta->user->editedOn;	break;
			case 'editedBy':	return $kapenta->user->editedBy;	break;
			case 'alias':	return $kapenta->user->alias;	break;
			case 'registry':	return $kapenta->user->registry;	break;
			case 'registryLoaded':	return $kapenta->user->registryLoaded;	break;
			case 'profileFields':	return $kapenta->user->profileFields;	break;
		}
	}

	public function __set($name, $value) {
		global $kapenta;
		switch($name) {
			case 'data':	$kapenta->user->data = $value;	break;
			case 'dbSchema':	$kapenta->user->dbSchema = $value;	break;
			case 'loaded':	$kapenta->user->loaded = $value;	break;
			case 'UID':	$kapenta->user->UID = $value;	break;
			case 'role':	$kapenta->user->role = $value;	break;
			case 'school':	$kapenta->user->school = $value;	break;
			case 'grade':	$kapenta->user->grade = $value;	break;
			case 'firstname':	$kapenta->user->firstname = $value;	break;
			case 'surname':	$kapenta->user->surname = $value;	break;
			case 'username':	$kapenta->user->username = $value;	break;
			case 'password':	$kapenta->user->password = $value;	break;
			case 'lang':	$kapenta->user->lang = $value;	break;
			case 'profile':	$kapenta->user->profile = $value;	break;
			case 'permissions':	$kapenta->user->permissions = $value;	break;
			case 'settings':	$kapenta->user->settings = $value;	break;
			case 'lastOnline':	$kapenta->user->lastOnline = $value;	break;
			case 'createdOn':	$kapenta->user->createdOn = $value;	break;
			case 'createdBy':	$kapenta->user->createdBy = $value;	break;
			case 'editedOn':	$kapenta->user->editedOn = $value;	break;
			case 'editedBy':	$kapenta->user->editedBy = $value;	break;
			case 'alias':	$kapenta->user->alias = $value;	break;
			case 'registry':	$kapenta->user->registry = $value;	break;
			case 'registryLoaded':	$kapenta->user->registryLoaded = $value;	break;
			case 'profileFields':	$kapenta->user->profileFields = $value;	break;
		}
	}

	function load($raUID = '') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'load');
		return $kapenta->user->load($raUID);
	}

	function loadByName($username) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'loadByName');
		return $kapenta->user->loadByName($username);
	}

	function loadArray($ary) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'loadArray');
		return $kapenta->user->loadArray($ary);
	}

	function save() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'save');
		return $kapenta->user->save();
	}

	function verify() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'verify');
		return $kapenta->user->verify();
	}

	function getDbSchema() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'getDbSchema');
		return $kapenta->user->getDbSchema();
	}

	function toArray() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'toArray');
		return $kapenta->user->toArray();
	}

	function extArray() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'extArray');
		return $kapenta->user->extArray();
	}

	function delete() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'delete');
		return $kapenta->user->delete();
	}

	function getUserUID($username) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'getUserUID');
		return $kapenta->user->getUserUID($username);
	}

	function checkPassword($candidate) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'checkPassword');
		return $kapenta->user->checkPassword($candidate);
	}

	function setPassword($password) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'setPassword');
		return $kapenta->user->setPassword($password);
	}

	function authHas($module, $model, $permission, $UID = '') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'authHas');
		return $kapenta->user->authHas($module,  $model,  $permission, $UID);
	}

	function initProfile() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'initProfile');
		return $kapenta->user->initProfile();
	}

	function expandProfile($xml) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'expandProfile');
		return $kapenta->user->expandProfile($xml);
	}

	function collapseProfile() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'collapseProfile');
		return $kapenta->user->collapseProfile();
	}

	function loadRegistry() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'loadRegistry');
		return $kapenta->user->loadRegistry();
	}

	function set($key, $value) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'set');
		return $kapenta->user->set($key,  $value);
	}

	function get($key) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'get');
		return $kapenta->user->get($key);
	}

	function sameGrade() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'sameGrade');
		return $kapenta->user->sameGrade();
	}

	function getName() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'getName');
		return $kapenta->user->getName();
	}

	function getUrl() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'getUrl');
		return $kapenta->user->getUrl();
	}

	function getNameLink() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'getNameLink');
		return $kapenta->user->getNameLink();
	}

	function getSchoolName() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('user', 'getSchoolName');
		return $kapenta->user->getSchoolName();
	}

}

//--------------------------------------------------------------------------------------------------
//classname: Users_Role (modules/users/models/role.mod.php)
//--------------------------------------------------------------------------------------------------

//Propety: data 
//Propety: dbSchema 
//Propety: loaded 
//Propety: UID 
//Propety: name 
//Propety: description 
//Propety: permissions 
//Propety: createdOn 
//Propety: createdBy 
//Propety: editedOn 
//Propety: editedBy 
//Propety: alias 
class KLegacy_role {

	public function __get($name) {
		global $kapenta;
		switch($name) {
			case 'data':	return $kapenta->role->data;	break;
			case 'dbSchema':	return $kapenta->role->dbSchema;	break;
			case 'loaded':	return $kapenta->role->loaded;	break;
			case 'UID':	return $kapenta->role->UID;	break;
			case 'name':	return $kapenta->role->name;	break;
			case 'description':	return $kapenta->role->description;	break;
			case 'permissions':	return $kapenta->role->permissions;	break;
			case 'createdOn':	return $kapenta->role->createdOn;	break;
			case 'createdBy':	return $kapenta->role->createdBy;	break;
			case 'editedOn':	return $kapenta->role->editedOn;	break;
			case 'editedBy':	return $kapenta->role->editedBy;	break;
			case 'alias':	return $kapenta->role->alias;	break;
		}
	}

	public function __set($name, $value) {
		global $kapenta;
		switch($name) {
			case 'data':	$kapenta->role->data = $value;	break;
			case 'dbSchema':	$kapenta->role->dbSchema = $value;	break;
			case 'loaded':	$kapenta->role->loaded = $value;	break;
			case 'UID':	$kapenta->role->UID = $value;	break;
			case 'name':	$kapenta->role->name = $value;	break;
			case 'description':	$kapenta->role->description = $value;	break;
			case 'permissions':	$kapenta->role->permissions = $value;	break;
			case 'createdOn':	$kapenta->role->createdOn = $value;	break;
			case 'createdBy':	$kapenta->role->createdBy = $value;	break;
			case 'editedOn':	$kapenta->role->editedOn = $value;	break;
			case 'editedBy':	$kapenta->role->editedBy = $value;	break;
			case 'alias':	$kapenta->role->alias = $value;	break;
		}
	}

	function load($raUID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('role', 'load');
		return $kapenta->role->load($raUID);
	}

	function loadByName($name) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('role', 'loadByName');
		return $kapenta->role->loadByName($name);
	}

	function loadArray($ary) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('role', 'loadArray');
		return $kapenta->role->loadArray($ary);
	}

	function save() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('role', 'save');
		return $kapenta->role->save();
	}

	function verify() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('role', 'verify');
		return $kapenta->role->verify();
	}

	function getDbSchema() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('role', 'getDbSchema');
		return $kapenta->role->getDbSchema();
	}

	function toArray() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('role', 'toArray');
		return $kapenta->role->toArray();
	}

	function toXml($xmlDec = false, $indent = '') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('role', 'toXml');
		return $kapenta->role->toXml($xmlDec, $indent);
	}

	function extArray() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('role', 'extArray');
		return $kapenta->role->extArray();
	}

	function delete() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('role', 'delete');
		return $kapenta->role->delete();
	}

	function expandPermissions($data) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('role', 'expandPermissions');
		return $kapenta->role->expandPermissions($data);
	}

	function collapsePermissions() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('role', 'collapsePermissions');
		return $kapenta->role->collapsePermissions();
	}

	function addPermission($type, $module, $model, $permission, $condition = '') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('role', 'addPermission');
		return $kapenta->role->addPermission($type,  $module,  $model,  $permission, $condition);
	}

	function hasPermission($type, $module, $model, $permission, $condition = '') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('role', 'hasPermission');
		return $kapenta->role->hasPermission($type,  $module,  $model,  $permission, $condition);
	}

	function toHtml() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('role', 'toHtml');
		return $kapenta->role->toHtml();
	}

}

//--------------------------------------------------------------------------------------------------
//classname: KNotifications (core/knotifications.class.php)
//--------------------------------------------------------------------------------------------------

class KLegacy_notifications {

	public function __get($name) {
		global $kapenta;
		switch($name) {
		}
	}

	public function __set($name, $value) {
		global $kapenta;
		switch($name) {
		}
	}

	function KNotification() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'KNotification');
		return $kapenta->notifications->KNotification();
	}

	function create($refModule, $refModel, $refUID, $refEvent, $title, $content, $url = '', $private = false) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'create');
		return $kapenta->notifications->create($refModule,  $refModel,  $refUID,  $refEvent,  $title,  $content, $url, $private);
	}

	function count($refModule, $refModel, $refUID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'count');
		return $kapenta->notifications->count($refModule,  $refModel,  $refUID);
	}

	function existsRecent($refModule, $refModel, $refUID, $userUID, $refEvent, $maxAge) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'existsRecent');
		return $kapenta->notifications->existsRecent($refModule,  $refModel,  $refUID,  $userUID,  $refEvent,  $maxAge);
	}

	function annotate($notificationUID, $annotation) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'annotate');
		return $kapenta->notifications->annotate($notificationUID,  $annotation);
	}

	function addUser($notificationUID, $userUID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'addUser');
		return $kapenta->notifications->addUser($notificationUID,  $userUID);
	}

	function addEveryone($notificationUID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'addEveryone');
		return $kapenta->notifications->addEveryone($notificationUID);
	}

	function addAdmins($notificationUID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'addAdmins');
		return $kapenta->notifications->addAdmins($notificationUID);
	}

	function addSchool($notificationUID, $schoolUID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'addSchool');
		return $kapenta->notifications->addSchool($notificationUID,  $schoolUID);
	}

	function addSchoolGrade($notificationUID, $schoolUID, $grade) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'addSchoolGrade');
		return $kapenta->notifications->addSchoolGrade($notificationUID,  $schoolUID,  $grade);
	}

	function addGroup($notificationUID, $groupUID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'addGroup');
		return $kapenta->notifications->addGroup($notificationUID,  $groupUID);
	}

	function addFriends($notificationUID, $userUID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'addFriends');
		return $kapenta->notifications->addFriends($notificationUID,  $userUID);
	}

	function addProject($notificationUID, $projectUID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'addProject');
		return $kapenta->notifications->addProject($notificationUID,  $projectUID);
	}

	function addProjectAdmins($notificationUID, $projectUID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'addProjectAdmins');
		return $kapenta->notifications->addProjectAdmins($notificationUID,  $projectUID);
	}

	function addForumThread($notificationUID, $projectUID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'addForumThread');
		return $kapenta->notifications->addForumThread($notificationUID,  $projectUID);
	}

	function getContent($notificationUID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'getContent');
		return $kapenta->notifications->getContent($notificationUID);
	}

	function setContent($notificationUID, $content) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'setContent');
		return $kapenta->notifications->setContent($notificationUID,  $content);
	}

	function setTitle($notificationUID, $title) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'setTitle');
		return $kapenta->notifications->setTitle($notificationUID,  $title);
	}

	function getDbSchema() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('notifications', 'getDbSchema');
		return $kapenta->notifications->getDbSchema();
	}

}

//--------------------------------------------------------------------------------------------------
//classname: KRevisions (core/krevisions.class.php)
//--------------------------------------------------------------------------------------------------

class KLegacy_revisions {

	public function __get($name) {
		global $kapenta;
		switch($name) {
		}
	}

	public function __set($name, $value) {
		global $kapenta;
		switch($name) {
		}
	}

	function storeRevision($changes, $dbSchema, $UID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('revisions', 'storeRevision');
		return $kapenta->revisions->storeRevision($changes,  $dbSchema,  $UID);
	}

	function recordDeletion($fields, $dbSchema, $isShared = true) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('revisions', 'recordDeletion');
		return $kapenta->revisions->recordDeletion($fields,  $dbSchema, $isShared);
	}

	function isDeleted($model, $UID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('revisions', 'isDeleted');
		return $kapenta->revisions->isDeleted($model,  $UID);
	}

	function undoLastDeletion($model, $UID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('revisions', 'undoLastDeletion');
		return $kapenta->revisions->undoLastDeletion($model,  $UID);
	}

	function restoreDependant($model, $UID) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('revisions', 'restoreDependant');
		return $kapenta->revisions->restoreDependant($model,  $UID);
	}

}

//--------------------------------------------------------------------------------------------------
//classname: KCache (core/kcache.class.php)
//--------------------------------------------------------------------------------------------------

//Propety: maxAge 
//Propety: mcAge 
class KLegacy_blockcache {

	public function __get($name) {
		global $kapenta;
		switch($name) {
			case 'maxAge':	return $kapenta->blockcache->maxAge;	break;
			case 'mcAge':	return $kapenta->blockcache->mcAge;	break;
		}
	}

	public function __set($name, $value) {
		global $kapenta;
		switch($name) {
			case 'maxAge':	$kapenta->blockcache->maxAge = $value;	break;
			case 'mcAge':	$kapenta->blockcache->mcAge = $value;	break;
		}
	}

	function get($area, $tag, $returnUID = false) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('blockcache', 'get');
		return $kapenta->blockcache->get($area,  $tag, $returnUID);
	}

	function set($channel, $area, $tag, $content) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('blockcache', 'set');
		return $kapenta->blockcache->set($channel,  $area,  $tag,  $content);
	}

	function clear($channel) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('blockcache', 'clear');
		return $kapenta->blockcache->clear($channel);
	}

	function clearAll() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('blockcache', 'clearAll');
		return $kapenta->blockcache->clearAll();
	}

	function renew($UID, $editedOn) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('blockcache', 'renew');
		return $kapenta->blockcache->renew($UID,  $editedOn);
	}

}

//--------------------------------------------------------------------------------------------------
//classname: KPage (core/kpage.class.php)
//--------------------------------------------------------------------------------------------------

//Propety: fileName 
//Propety: loaded 
//Propety: UID 
//Propety: data 
//Propety: blockArgs 
//Propety: debug 
//Propety: logDebug 
//Propety: template 
//Propety: title 
//Propety: content 
//Propety: nav1 
//Propety: nav2 
//Propety: script 
//Propety: jsinit 
//Propety: banner 
//Propety: head 
//Propety: menu1 
//Propety: menu2 
//Propety: section 
//Propety: subsection 
//Propety: breadcrumb 
class KLegacy_page {

	public function __get($name) {
		global $kapenta;
		switch($name) {
			case 'fileName':	return $kapenta->page->fileName;	break;
			case 'loaded':	return $kapenta->page->loaded;	break;
			case 'UID':	return $kapenta->page->UID;	break;
			case 'data':	return $kapenta->page->data;	break;
			case 'blockArgs':	return $kapenta->page->blockArgs;	break;
			case 'debug':	return $kapenta->page->debug;	break;
			case 'logDebug':	return $kapenta->page->logDebug;	break;
			case 'template':	return $kapenta->page->template;	break;
			case 'title':	return $kapenta->page->title;	break;
			case 'content':	return $kapenta->page->content;	break;
			case 'nav1':	return $kapenta->page->nav1;	break;
			case 'nav2':	return $kapenta->page->nav2;	break;
			case 'script':	return $kapenta->page->script;	break;
			case 'jsinit':	return $kapenta->page->jsinit;	break;
			case 'banner':	return $kapenta->page->banner;	break;
			case 'head':	return $kapenta->page->head;	break;
			case 'menu1':	return $kapenta->page->menu1;	break;
			case 'menu2':	return $kapenta->page->menu2;	break;
			case 'section':	return $kapenta->page->section;	break;
			case 'subsection':	return $kapenta->page->subsection;	break;
			case 'breadcrumb':	return $kapenta->page->breadcrumb;	break;
		}
	}

	public function __set($name, $value) {
		global $kapenta;
		switch($name) {
			case 'fileName':	$kapenta->page->fileName = $value;	break;
			case 'loaded':	$kapenta->page->loaded = $value;	break;
			case 'UID':	$kapenta->page->UID = $value;	break;
			case 'data':	$kapenta->page->data = $value;	break;
			case 'blockArgs':	$kapenta->page->blockArgs = $value;	break;
			case 'debug':	$kapenta->page->debug = $value;	break;
			case 'logDebug':	$kapenta->page->logDebug = $value;	break;
			case 'template':	$kapenta->page->template = $value;	break;
			case 'title':	$kapenta->page->title = $value;	break;
			case 'content':	$kapenta->page->content = $value;	break;
			case 'nav1':	$kapenta->page->nav1 = $value;	break;
			case 'nav2':	$kapenta->page->nav2 = $value;	break;
			case 'script':	$kapenta->page->script = $value;	break;
			case 'jsinit':	$kapenta->page->jsinit = $value;	break;
			case 'banner':	$kapenta->page->banner = $value;	break;
			case 'head':	$kapenta->page->head = $value;	break;
			case 'menu1':	$kapenta->page->menu1 = $value;	break;
			case 'menu2':	$kapenta->page->menu2 = $value;	break;
			case 'section':	$kapenta->page->section = $value;	break;
			case 'subsection':	$kapenta->page->subsection = $value;	break;
			case 'breadcrumb':	$kapenta->page->breadcrumb = $value;	break;
		}
	}

	function load($fileName) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'load');
		return $kapenta->page->load($fileName);
	}

	function save() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'save');
		return $kapenta->page->save();
	}

	function toArray() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'toArray');
		return $kapenta->page->toArray();
	}

	function render() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'render');
		return $kapenta->page->render();
	}

	function replaceLabels($labels, $txt, $marker = '%%') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'replaceLabels');
		return $kapenta->page->replaceLabels($labels,  $txt, $marker);
	}

	function allowBlockArgs($argNames) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'allowBlockArgs');
		return $kapenta->page->allowBlockArgs($argNames);
	}

	function requireCss($url) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'requireCss');
		return $kapenta->page->requireCss($url);
	}

	function requireJs($url) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'requireJs');
		return $kapenta->page->requireJs($url);
	}

	function do301($URI) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'do301');
		return $kapenta->page->do301($URI);
	}

	function do403($message = '', $iframe = false) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'do403');
		return $kapenta->page->do403($message, $iframe);
	}

	function do302($URI) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'do302');
		return $kapenta->page->do302($URI);
	}

	function do404($message = '', $iframe = false) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'do404');
		return $kapenta->page->do404($message, $iframe);
	}

	function doXmlError($msg = '') {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'doXmlError');
		return $kapenta->page->doXmlError($msg);
	}

	function setTrigger($module, $channel, $block) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'setTrigger');
		return $kapenta->page->setTrigger($module,  $channel,  $block);
	}

	function doTrigger($module, $channel) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'doTrigger');
		return $kapenta->page->doTrigger($module,  $channel);
	}

	function logDebugItem($system, $msg) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'logDebugItem');
		return $kapenta->page->logDebugItem($system,  $msg);
	}

	function logDebug($system, $msg) {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'logDebug');
		return $kapenta->page->logDebug($system,  $msg);
	}

	function debugToHtml() {
		global $kapenta;
		$kapenta->utils->noteDeprecated('page', 'debugToHtml');
		return $kapenta->page->debugToHtml();
	}

}

?>
