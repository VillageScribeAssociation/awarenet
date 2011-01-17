//==================================================================================================
//*	Live Page Update
//==================================================================================================
//+	this should be instantiated into a global called klive.
//+
//+	divs are registered as corresponding to blocks by special comments like:
//+	<!-- REGSITERBLOCK:divId:BASE64+ENCODED+BLOCK -->

//--------------------------------------------------------------------------------------------------
//	message pump object (periodically polls server for new messages)
//--------------------------------------------------------------------------------------------------

function Live_Pump(jsPageUID, jsServerPath) {
	
	this.interval = 5000;				//_ default initial interval, milliseconds [int]
	this.maxinterval = 20000;			//_	max interval between poll, milliseconds [int]

	this.UID = jsPageUID;				//_	Unique identifier of this page [string]
	this.serverPath = jsServerPath;		//_	URL of a kapenta installation [string]

	this.divs = Array();				//_	map of div names to block tags [array]
	this.blocks = Array();				//_	map of block tags to block content [array]

	this.lastChatMessage = '0';			//_	time when last chat message was created [string]

	//----------------------------------------------------------------------------------------------
	//.	start the pump
	//----------------------------------------------------------------------------------------------

	this.start = function() {
		this.log('[*] Starting Message Pump...');
		this.registerAllBlocks(document.body.innerHTML);
		// TODO: any initialization here (eg, scan page for register blocks, etc)
		this.pump();
	}

	//----------------------------------------------------------------------------------------------
	//.	poll the server - check this page's mailbox via XMLHttpRequest GET
	//----------------------------------------------------------------------------------------------

	this.pump = function() {
		//------------------------------------------------------------------------------------------
		//	create XMLHttpRequest object
		//------------------------------------------------------------------------------------------
		var req = new XMLHttpRequest();  
		var mailUrl = this.serverPath + 'live/getmessages/' + this.UID;
		this.log('[*] Polling server: ' + mailUrl + '...');	

		req.open('POST', mailUrl, true);  
		req.setRequestHeader('Connection', 'close');
		req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');

		//------------------------------------------------------------------------------------------
		//	create handler for returned content
		//------------------------------------------------------------------------------------------ 
		req.onreadystatechange = function (aEvt) {  
			//klive.log('loading: ' + req.status);
			if ((4 == req.readyState) && (200 == req.status))  {
				klive.procMessages(req.responseText); 
			}
		}

		//------------------------------------------------------------------------------------------
		//	add params (just chat for now) and send the request
		//------------------------------------------------------------------------------------------ 
		params = "";
		this.log("[i] klive.lastChatMessage=" + this.lastChatMessage);
		if (true == awareNetChat) {	params = 'chatsince=' + escape(this.lastChatMessage); }
		req.send(params);

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
						this.updateBlock(base64_decode(msg));
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

				//note: base64_decode as from phpJs
				newDiv = new Live_DivMap(parts[1], base64_decode(parts[2]));
				this.divs[this.divs.length] = newDiv;
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//.	update a block on the page
	//----------------------------------------------------------------------------------------------
	//arg: block64 - base64 encoded block tag [string]	
	//returns: true on success, false on failure [bool]

	this.updateBlock = function(blockTag) {
		divId = this.getDivId(blockTag);

		// if there is no such div registered, then we have nowhere to put the updated content
		if (false == divId) { return false; }

		//------------------------------------------------------------------------------------------
		//	set/throb div background color
		//------------------------------------------------------------------------------------------
		var theDiv = document.getElementById(divId);
		var oldBgColor = theDiv.style.backgroundColor;
		theDiv.style.backgroundColor = '#b1d27e';

		//------------------------------------------------------------------------------------------
		//	download new content from server
		//------------------------------------------------------------------------------------------
		var block64 = base64_encode(blockTag);
		var params = 'b=' + escape(block64);
		//alert('sending: ' + params);

		var req = new XMLHttpRequest();  
		req.open('POST', this.serverPath + 'live/getblock/', true);  
		req.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		//req.setRequestHeader('Content-length', params.length);
		req.setRequestHeader('Connection', 'close'); 
		req.replaceDivId = divId;
		req.newDivBgColor = oldBgColor;
		req.onreadystatechange = function (aEvt) {  
			klive.log('[i] Downloading updated content: ' + req.status);
			if ((4 == req.readyState) && (200 == req.status))  {
				//----------------------------------------------------------------------------------
				//	update the page
				//----------------------------------------------------------------------------------
				//: hat tip to Stack Overflow :3
				//:	http://stackoverflow.com/questions/1700870/how-do-i-do-outerhtml-in-firefox/

				var xDiv = document.getElementById(this.replaceDivId);
				var s = req.responseText
		        var r = xDiv.ownerDocument.createRange();  
		        r.setStartBefore(xDiv);  
		        var df = r.createContextualFragment(s);  
		        xDiv.parentNode.replaceChild(df, xDiv);  

				xDiv.style.backgroundColor = this.newDivBgColor;
			}
		}

		req.send(params);

		return true;
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
	//.	log a message to the js debug console
	//----------------------------------------------------------------------------------------------
	//arg: msg - message to log to the console [string]

	this.log = function(msg) {
		var theDiv = document.getElementById('pumpDiv');
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
	klive.log('Mapping block ' + blockTag + ' to div ' + divId);
}

//--------------------------------------------------------------------------------------------------
//	temporary, remove this when all msgSubscribe calls have been found and removed
//--------------------------------------------------------------------------------------------------

function msgSubscribe(something, somethingElse) {
	return true;
}
