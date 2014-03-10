<?php

	require_once(dirname(__FILE__) . '/../modules/cache/models/entry.mod.php');

//--------------------------------------------------------------------------------------------------
//*	kapenta block cache
//--------------------------------------------------------------------------------------------------
//+	This cache is used by modules to store rendered views where they are invariant between users.
//+	The process of storing and retrieving items from the cache is handled by the views themselves,
//+	since the modules can best keep track of the contexts in which caching is possible.
//+
//+	Cached items must be invalidated by the modules which created them, generally in response to
//+	object_updated events.  Modules may assign cached blocks a 'channel' to simplify this process.
//+	For example, the 'recent blog posts' view might be invalidated in the event of new post, 
//+	whereas the summary of a single blog post would only need to be cleared from the cache when
//+	that specific post is edited or deleted.
//+
//+	While the channel system is less efficient than binding cached items to the objects or
//+	database queries they depend on, it requires less space, complexity and row inserts.
//+	
//+	The default TTL of items in the cache is one week (604800 seconds), measured from the editedOn
//+	field of the Cache_Entry object.  If the item is hit in the second half of its TTL the countdown
//+	will be reset.
//+
//+	When memcached is enabled items will also be stored opportunistically in memory for one hour.

class KCache {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	var $maxAge = 0;					//_	TTL of items in the cache, seconds [int]
	var $mcAge = 3600;					//_	memcached items for one hour [int]

	//----------------------------------------------------------------------------------------------
	//	constructor
	//----------------------------------------------------------------------------------------------

	function KCache() {
		global $registry;

		if ('' == $registry->get('cache.ttl')) { $registry->set('cache.ttl', '604800'); }

		$this->maxAge = (int)$registry->get('cache.ttl');

	}

	//----------------------------------------------------------------------------------------------
	//	get a block from the cache
	//----------------------------------------------------------------------------------------------
	//arg: area - content area this belongs to (nav1, menu1, etc) [string]
	//arg: tag - literal block tag [string]
	//arg: returnUID - return UID of entry, rather than content (default is false) [bool]
	//returns: cached block content, or empty string on failure [string]

	function get($area, $tag, $returnUID = false) {
		global $kapenta;

		//------------------------------------------------------------------------------------------
		//	try memcached
		//------------------------------------------------------------------------------------------

		$cacheKey = 'dbcache::' . $area . '::' . $kapenta->user->role . '::' . $tag;
		if ((true == $kapenta->mcEnabled) && (true == $kapenta->cacheHas($cacheKey))) {
			return $kapenta->cacheGet($cacheKey);
		}

		//------------------------------------------------------------------------------------------
		//	try database cache
		//------------------------------------------------------------------------------------------

		$conditions = array(
			"tag='" . $kapenta->db->addMarkup($tag) . "'",
			"area='" . $kapenta->db->addMarkup($area) . "'",
			"role='" . $kapenta->db->addMarkup($kapenta->user->role) . "'"
		);

		$range = $kapenta->db->loadRange('cache_entry', '*', $conditions);

		if (0 == count($range)) { return ''; }

		foreach($range as $item) {
			$this->renew($item['UID'], $item['editedOn']);
			if (true == $returnUID) { return $item['UID']; }
			if (true == $kapenta->mcEnabled) { $kapenta->cacheSet($cacheKey, $item['content']); }
			return $item['content'];
		}
	}

	//----------------------------------------------------------------------------------------------
	//	store a rendered block in the cache
	//----------------------------------------------------------------------------------------------
	//arg: channel - module-defined, used for cache invalidation [string]
	//arg: area - content area in which the block is being rendered (content, nav1, etc) [string]
	//arg: tag - literal block tag [string]
	//arg: content - rendered HTML correcponding to block tag [string]
	//returns: true on success, false on failure [bool]

	function set($channel, $area, $tag, $content) {
		global $kapenta;

		//------------------------------------------------------------------------------------------
		//	check for existing block
		//------------------------------------------------------------------------------------------

		$extantUID = $this->get($area, $tag, true);

		//------------------------------------------------------------------------------------------
		//	create overwrite
		//------------------------------------------------------------------------------------------

		$model = new Cache_Entry($extantUID);
		$model->tag = $tag;
		$model->role = $kapenta->user->role;
		$model->area = $area;
		$model->content = $content;
		$model->channel = $channel;
		$model->shared = 'no';					//	never shared with other peers

		$report = $model->save();
		if ('' == $report) {
			//------------------------------------------------------------------------------------------
			//	reset memcached
			//------------------------------------------------------------------------------------------
			$cacheKey = 'dbcache::' . $area . '::' . $kapenta->user->role . '::' . $tag;

			if (true == $kapenta->mcEnabled) {
				$kapenta->cacheSet($cacheKey, $content, $this->mcAge);
			}

			return true;
		}

		$kapenta->session->msgAdmin("Could not cache block tag: $tag<br/>\n$report", 'bad');
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//	clear a channel from the cache
	//----------------------------------------------------------------------------------------------
	//arg: channel - name of a cache channel [string]
	//returns: true on success, false on failure [bool]

	function clear($channel) {
		global $kapenta;

		$allOk = true;

		$conditions = array("channel='" . $kapenta->db->addMarkup($channel) . "'");
		$range = $kapenta->db->loadRange('cache_entry', '*', $conditions);

		foreach($range as $item) {
			$model = new Cache_Entry($item['UID']);
			$model->delete();							//	will invalidate memcached
		}

		return $allOk;
	}

	//----------------------------------------------------------------------------------------------
	//	clear all cached views
	//----------------------------------------------------------------------------------------------

	function clearAll() {
		global $kapenta;

		$kapenta->cacheFlush();
		$sql = "delete from cache_entry";
		$kapenta->db->query($sql);
	}

	//----------------------------------------------------------------------------------------------
	//	renew a cached item if more than half of its TTL has expired
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a Cache_Entry [string]
	//arg: editedOn - date of last renewal (MySQL format) [string]
	//returns: true if entry was TTL was reset, false if not [string]

	function renew($UID, $editedOn) {
		global $kapenta;

		$editTime = $kapenta->strtotime($editedOn);
		$now = $kapenta->time();

		if (($now - $editTime) > ($this->maxAge / 2)) {
			$model = new Cache_Entry($UID);
			if (false == $model->loaded) { return false; }

			$sql = ''
			 . "update cache_entry"
			 . " set editedOn='" . $kapenta->db->datetime() . "'"
			 . " where UID='" . $kapenta->db->addMarkup($UID) . "'";

			$check = $kapenta->db->query($sql);
			return true;
		}

		return false;
	}

}

?>
