//==================================================================================================
//*	Live Page Update
//==================================================================================================
//+	this should be instantiated into a global called klive.
//+
//+	divs are registered as corresponding to blocks by special comments like:
//+	<!-- REGISTERBLOCK:divId:BASE64+ENCODED+BLOCK -->

//--------------------------------------------------------------------------------------------------
//	message pump object (periodically polls server for new messages)
//--------------------------------------------------------------------------------------------------

function Live_Pump(jsPageUID, jsServerPath) {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------	

	this.interval = 5000;				//_ default initial interval, milliseconds [int]
	this.maxinterval = 20000;			//_	max interval between poll, milliseconds [int]

	this.UID = jsPageUID;				//_	Unique identifier of this page [string]
	this.serverPath = jsServerPath;		//_	URL of a kapenta installation [string]

	this.divs = Array();				//_	map of div names to block tags [array:string]
	this.blocks = Array();				//_	map of block tags to block content [array:string]

	this.lastChatMessage = '0';			//_	time when last chat message was created [string]

	this.loadColor = '#b1d27e';			//_	div backgrounds set to this while loading [string]

	//----------------------------------------------------------------------------------------------
	//.	start the pump
	//----------------------------------------------------------------------------------------------

	this.start = function() {
		this.log('[*] Starting Message Pump...');
		this.registerAllBlocks(document.body.innerHTML);
		// TODO: any further initialization here
		this.pump();
	}

	//----------------------------------------------------------------------------------------------
	//.	poll the server - check this page's mailbox via XMLHttpRequest GET
	//----------------------------------------------------------------------------------------------

	this.pump = function() {
		//------------------------------------------------------------------------------------------
		//	create XMLHttpRequest object
		//------------------------------------------------------------------------------------------
		var url = kutils.serverPath + 'live/getmessages/' + this.UID;
		this.log('[*] Polling server: ' + url + '...');	

		//------------------------------------------------------------------------------------------
		//	create handler for returned content
		//------------------------------------------------------------------------------------------ 
		var cbFn = function(responseText, status) {  
			klive.log('loading: ' + status);
			if (200 == status)  {
				klive.procMessages(responseText); 
			}
		}

		//------------------------------------------------------------------------------------------
		//	add params (just chat for now) and send the request
		//------------------------------------------------------------------------------------------ 
		params = "";
		this.log("[i] klive.lastChatMessage=" + this.lastChatMessage);
		if (true == awareNetChat) {	params = 'chatsince=' + escape(this.lastChatMessage); }

		kutils.httpPost(url, params, cbFn);

		//------------------------------------------------------------------------------------------
		//	schedule next poll
		//------------------------------------------------------------------------------------------ 		
		window.setTimeout("klive.pump();", this.interval);		// schedule next poll
	}

	//----------------------------------------------------------------------------------------------
	//.	process incoming messages
	//----------------------------------------------------------------------------------------------
	//arg: messages - messages for this page, one per line [string]

	this.procMessages = function(messages) {
		//this.log(messages);
		lines = messages.split("\n");
		for (var idx in lines) {
			var line = lines[idx];
			if ('' != line) {
				//this.log('[i] Received message: ' + line);
				var parts = line.split(":");
				var route = parts[0];
				var msg = parts[1];

				//----------------------------------------------------------------------------------
				//	route messages to correct subsystem
				//----------------------------------------------------------------------------------
				switch (route) {
					case 'block':
						//alert('recieved block update instruction: ' + base64_decode(msg));
						this.updateBlockFromServer(kutils.base64_decode(msg));
						break;	//..................................................................

					case 'chat':
						if (true == awareNetChat) { kchatclient.receive(msg); }
						break;	//..................................................................

					default:
						this.log('[i] Unhandled message: ' + line);
						break;	//..................................................................
				}

				//alert(msg);
				// process message here
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	bind div to a block (ie, bind the content of a div to server-side view)
	//----------------------------------------------------------------------------------------------
	//arg: divId - element ID to bind to a view [string]
	//arg: blockTag - a block tag, possibly base64 encoded [string]
	//arg: B64 - set to true if the block tag is base64 encoded [bool]

	this.bindDivToBlock = function(divId, blockTag, B64) {
		var found = false;
		if (true == B64) { blockTag = kutils.base64_decode(blockTag); }
		this.log('[i] binding div: ' + divId + ' to block ' + blockTag);

		//	replace any existing binding to a block tag
		for (var idx in this.divs) {
			if (divId == this.divs[idx].divId) { 
				this.divs[idx].blockTag = blockTag; 
				found = true; 
			} 
		}

		// no prior binding, register the div
		if (false == found) { this.divs[this.divs.length] = new Live_DivMap(divId, blockTag); }

		// set div content
		this.setDivContent(divId, blockTag);				
	}

	//----------------------------------------------------------------------------------------------
	//.	set the content of a div to
	//----------------------------------------------------------------------------------------------
	//arg: divId - html element ID, host have innerHTML [string]
	//arg: blockTag - a block tag [string]
	//returns: true on success, false on failure [bool]

	this.setDivContent = function(divId, blockTag) {
		var theDiv = document.getElementById(divId);
		this.log('[i] setting div content: ' + divId + ' to block ' + blockTag);
		//TODO: check here that the div was found

		//------------------------------------------------------------------------------------------
		//	register with / update div map
		//------------------------------------------------------------------------------------------

		var found = false;
		for (var idx in this.divs) {
			if (this.divs[idx].divId == divId) { 
				this.divs[idx].blockTag = blockTag; 
				found = true;
			}
		}

		if (false == found) { 
			var newDiv = new Live_DivMap(divId, blockTag);
			this.divs[this.divs.length] = newDiv;
		}

		//------------------------------------------------------------------------------------------
		//	look for existing content for div
		//------------------------------------------------------------------------------------------
		for (var idx in this.blocks) {
			if (blockTag == this.blocks[idx].tag) {
				theDiv.innerHTML = this.blocks[idx].content;
				this.log('[i] found block in cache: ' + divId + ' to block ' + blockTag);
				return true;
			}
		}

		//------------------------------------------------------------------------------------------
		//	content not cached; try download it from server, and register the div if not already done
		//------------------------------------------------------------------------------------------
		this.log('[i] block found not found in cache, loading from server: ' + blockTag);

		this.updateBlockFromServer(blockTag);
	}

	//----------------------------------------------------------------------------------------------
	//.	go through a piece of HTML and action all 'register block' comments
	//----------------------------------------------------------------------------------------------
	//arg: html - or any text which may contain REGISTERBLOCK comments

	this.registerAllBlocks = function(html) {
		html = html.replace(/\n/g, '');
		html = html.replace(/<!--/g, "\n<!--");
		html = html.replace(/-->/g, "-->\n");
		var lines = html.split("\n");
		for (var i in lines) {
			var line = lines[i];
			if (-1 != line.indexOf('REGISTERBLOCK')) {
				line = line.replace(/-->/g, '');
				line = line.replace(/<!--/g, '');
				line = line.replace(/ /g, '');
				var parts = line.split(':');

				var newDiv = new Live_DivMap(parts[1], kutils.base64_decode(parts[2]));
				this.divs[this.divs.length] = newDiv;
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	set load color/effect to all divs bound to a given view
	//----------------------------------------------------------------------------------------------
	//arg: blockTag - a block tag [string]
	//returns: number of divs [int]

	this.setLoadColor= function (blockTag) {
		var divCount = 0;				//%	number of divs bound to this block [int]

		for (var idx in this.divs) {
			if (blockTag == this.divs[idx].blockTag) {
				var theDiv = document.getElementById(this.divs[idx].divId);
				//TODO: check theDiv exists/is correct
				this.divs[idx].bgColor = theDiv.style.backgroundColor;
				theDiv.style.backgroundColor = this.loadColor;
				divCount++;
			}
		}

		return divCount;
	}

	//----------------------------------------------------------------------------------------------
	//.	update a block on the page
	//----------------------------------------------------------------------------------------------
	//arg: blockTag - base64 encoded block tag [string]	
	//returns: true on success, false on failure [bool]

	this.updateBlockFromServer = function(blockTag) {
		this.log('getting block/view from server: ' + blockTag);

		//------------------------------------------------------------------------------------------
		//	set throbber effect
		//------------------------------------------------------------------------------------------
		if (0 == this.setLoadColor(blockTag)) { 
			// if no div is bound to this view (block tag) we have nothing to do
			this.log('no registered divs for this block: ' + blockTag);
			return false; 
		}

		//------------------------------------------------------------------------------------------
		//	download new content from server
		//------------------------------------------------------------------------------------------
		var block64 = kutils.base64_encode(blockTag);
		var params = 'b=' + escape(block64);
		this.log('sending: ' + params);

		var cbfn = function(responseText, status) {
			if (200 == status) {
				klive.log('[i] setting block content: ' + blockTag);
				klive.setBlockContent(blockTag, responseText);
			} else {
				alert('WARNING: ' + status + "<br/>" + responseText);
			}
		}

		kutils.httpPost(kutils.serverPath + 'live/getblock/', params, cbfn);

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	set block content
	//----------------------------------------------------------------------------------------------
	//arg: blockTag - block tag, uniquely identifies a view [string]
	//arg: content - text/html [string]	

	this.setBlockContent = function(blockTag, content) {
		var found = false;

		// try update cache
		for (var idx in this.blocks) {		
			if (blockTag == this.blocks[idx].tag) { 
				this.blocks[idx].content = content;
				found = true;
			}
		}		

		// add to cache if new block
		if (false == found) { this.blocks[this.blocks.length] = new Live_Block(blockTag, content); }

		// update any divs bound to this block
		for (var idx in this.divs) {
			if (blockTag == this.divs[idx].blockTag) {
				var theDiv = document.getElementById(this.divs[idx].divId);
				//TODO: check the div was found
				theDiv.innerHTML = content;
				//if ('' != this.divs[idx].bgColor) {
					theDiv.style.backgroundColor = '#ffffff';
				//}
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	remove a block from the cache (to force re-download)
	//----------------------------------------------------------------------------------------------
	//arg: blockTag - a block tag, possibly base64 encoded [string]
	//arg: B64 - set to true if the block tag is base64 encoded [bool]
	//returns: true on sucess, false if not found [bool]

	this.removeBlock = function(blockTag, B64) {
		var found = false;								//%	return value [bool]
		var newBlocks = new Array();					//%	[array:object]
		if (true == B64) { blockTag = kutils.base64_decode(blockTag); }		

		for (var idx in this.blocks) {
			if (blockTag == this.blocks[idx].tag) {
				found = true;
			} else {
				newBlocks[newBlocks.length] = this.blocks[idx];
			}
		}

		this.blocks = newBlocks;
		return found;
	}

	//----------------------------------------------------------------------------------------------
	//.	get divId given blockTag
	//----------------------------------------------------------------------------------------------
	//arg: divId - DOM element ID [string]
	//returns: divID on success, false on failure [bool]

	this.getDivId = function(blockTag) {
		for (var idx in this.divs) {
			if (blockTag == this.divs[idx].blockTag) {
				return this.divs[idx].divId;
			}
		}
		return false;
	}

	//----------------------------------------------------------------------------------------------
	//.	map divs and blocks for debugging
	//----------------------------------------------------------------------------------------------

	this.mapDivs = function() {
		var divmap = '';
		for (var idx in this.divs) {
			divmap = divmap + 'divId: ' + this.divs[idx].divId + ' blockTag: ' + this.divs[idx].blockTag + "\n";
		}
		return divmap;
	}

	//----------------------------------------------------------------------------------------------
	//.	log a message to the js debug console
	//----------------------------------------------------------------------------------------------
	//arg: msg - message to log to the console [string]

	this.log = function(msg) {
		//console.log(msg);
		var theDiv = document.getElementById('pumpDiv');
		theDiv.align = 'left';
		if (theDiv.innerHTML.length > 4000) { this.clearLog(); }
		//theDiv.innerHTML = theDiv.innerHTML + msg + "<br/>\n"
	}

	//----------------------------------------------------------------------------------------------
	//.	clear the log
	//----------------------------------------------------------------------------------------------

	this.clearLog = function() {
		var theDiv = document.getElementById('pumpDiv');
		theDiv.innerHTML = '';
	}

	this.setlastChatMessage = function(lastMessage) {
		this.lastChatMessage = lastMessage;
		alert(lastMessage);
	}

	//----------------------------------------------------------------------------------------------
	//.	diagnostic report
	//----------------------------------------------------------------------------------------------

	this.diagnostic = function() {
		var report = ''
		 + 'KLIVE OBJECT\n'
		 + '-------------------------------------------------------------------------------------\n'
		 + 'Page UID: ' + this.UID + '\n'
		 + 'ServerPath: ' + this.serverPath + '\n'
		 + 'User UID: ' + jsUserUID + ' (env)\n'
		 + 'Polling Interval: ' + this.interval + '\n'
		 + 'Max Interval: ' + this.maxinterval + '\n'
		 + '\n'
		 + 'DIV MAP\n'
		 + '-------------------------------------------------------------------------------------\n'
		 + '\n';

		for (var i = 0; i < this.divs.length; i++) {
			report = report + 'd:' + this.divs[i].divId + ' => b:' + this.divs[i].blockTag + '\n'
		}

		return report;
	}

}

//--------------------------------------------------------------------------------------------------
//	block object, represents a unit of content
//--------------------------------------------------------------------------------------------------

function Live_Block(blockTag, blockContent) {
	this.tag = blockTag;
	this.content = blockContent;
}

//--------------------------------------------------------------------------------------------------
//	map object, associates divs with blocks
//--------------------------------------------------------------------------------------------------

function Live_DivMap(divId, blockTag) {
	this.divId = divId;
	this.blockTag = blockTag;
	this.bgColor = '';
	klive.log('Mapping block ' + blockTag + ' to div ' + divId);
}

if (!kutils) { alert('Warning: kutils not loaded)'); }
klive = new Live_Pump();

//--------------------------------------------------------------------------------------------------
//	temporary, remove this when all msgSubscribe calls have been found and removed
//--------------------------------------------------------------------------------------------------

function msgSubscribe(something, somethingElse) {
	return true;
}
