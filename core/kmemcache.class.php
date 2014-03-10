<?php

//--------------------------------------------------------------------------------------------------
//*	object to do in-memory caching of data which would otherwise require disk seeks
//--------------------------------------------------------------------------------------------------

class KMemcache {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $mc;						//_	memcached object [object]
	var $enabled = false;			//_	use memcached service if true, local array if false [bool]

	var $cache;						//_	holds in-memory cache if memcached not present [array]
	var $cacheSize = 300;			//_	maximum number of objects to cache [int]

	var $server = 'localhost';		//_	memcached server [string]
	var $port = '11211';			//_	memcached port number [string]

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function KMemcache() {
		global $kapenta;

		$this->cache = array();

		//$this->server = $kapenta->registry->get('memcached.server');
		//$this->port = $kapenta->registry->get('memcached.port');

		if ('' === $this->server) { $this->server = 'localhost'; }
		if ('' === $this->port) { $this->port = '11211'; }

		if (
		//	('yes' == $kapenta->registry->get('memcached.enabled')) && 
			(true == class_exists('Memcached'))
		) {
			$this->mcEnabled = true;
			$this->mc = new Memcached();
			$this->mc->addServer($this->server, (int)$this->port);

			//	commented out for PHP4 compatability, please set in Memcached if necessary
			//$this->mc->setOption($this->mc->OPT_COMPRESSION, true);
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	store an object in the cache
	//----------------------------------------------------------------------------------------------
	//arg: key - reference to stored object [string]
	//arg: objStr - any string, may be a serialized array [string]

	function set($key, $objStr) {
		global $kapenta;

		if (true == is_array($objStr)) { $objStr = serialize($objStr); }

		if (true == $this->enabled) {
			//--------------------------------------------------------------------------------------
			//	compressed, shared memory cache
			//--------------------------------------------------------------------------------------
			if ((true == isset($kapenta->page)) && (true == $kapenta->page->logDebug)) {
				$kapenta->page->logDebugItem('memcached', "cache set: $key");
			}
			$this->mc->set($key, $objStr);

		} else {
			//--------------------------------------------------------------------------------------
			//	basic, unoptimized per-process cache
			//--------------------------------------------------------------------------------------
			$this->cache[$key] = $objStr;
			if (count($this->cache) > $this->cacheSize) { $discard = array_shift($this->cache); }
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	retrieve an object from the cache
	//----------------------------------------------------------------------------------------------
	//arg: key - reference to stored object [string]
	//returns: string reporesentation of the cached item [string]

	function get($key) {	
		global $kapenta;

		if (true == $this->enabled) {
			$objStr = $this->mc->get($key);
			if (false == $objStr) {
				if ((true == isset($kapenta->page)) && (true == $kapenta->page->logDebug)) {
					$kapenta->page->logDebugItem('memcached', "cache miss: $key (cacheGet)");
				}
				return '';
			}					//	no such key

			if (('object' == gettype($kapenta->page)) && (true == $kapenta->page->logDebug)) {
				$kapenta->page->logDebugItem('memcached', "cache hit: $key (cacheGet)");
			}
			return $objStr;

		} else {
			if (false == array_key_exists($key, $this->cache)) { return ''; }
			return $this->cache[$key];
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	discover if an object exists in the cache
	//----------------------------------------------------------------------------------------------

	function has($key) {
		global $kapenta;

		if (true == $this->enabled) {
			$check = $this->mc->get($key);
			if (false === $check) {
				if ((true == isset($kapenta->page)) && (true == $kapenta->page->logDebug)) {
					$kapenta->page->logDebugItem('memcached', "cache miss: $key (cacheHas)");
				}
				return false;
			}
		} else {
			if (false == is_array($this->cache)) { return false; }
			if (false == array_key_exists($key, $this->cache)) { return false; }			
		}

		if ((true == isset($kapenta->page)) && (true == $kapenta->page->logDebug)) {
			$kapenta->page->logDebugItem('memcached', "cache hit: $key (cacheHas)");
		}
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	remove an item from the cache
	//----------------------------------------------------------------------------------------------
	//arg: key - reference to stored object [string]

	function delete($key) {
		global $kapenta;

		if (true == $this->enabled) {
			if ((true == isset($kapenta->page)) && (true == $kapenta->page->logDebug)) {
				$kapenta->page->logDebugItem('memcached', "invaliaded: $key");
			}
			$this->mc->delete($key);
		} else {
			if (true == array_key_exists($key, $this->cache)) { unset($this->cache[$key]); }
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	clear the entire memcache
	//----------------------------------------------------------------------------------------------
	//arg: key - reference to stored object [string]

	function flush() {
		global $kapenta;

		if (true == $this->enabled) {
			if (isset($kapenta->page)) {
				$kapenta->page->logDebugItem('memcached', "cleared all items");
			}
			$this->mc->flush();
		} else {
			$this->cache = array();
		}		
	}

}

?>
