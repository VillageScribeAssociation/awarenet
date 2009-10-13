
//-------------------------------------------------------------------------------------------------
//	javascript to allow blocks to subscribe to notifications
//-------------------------------------------------------------------------------------------------

	var msgOutbox = '';				// used to add subscriptions
	var msgHandlers = new Array();	// used for callback functions

	var msgPullFreq = 5000;			// how often to poll server
	var msgPullFreqMin = 5000;		// minimum time between checks
	var msgPullFreqMax = 60000;		// maximum time between checks

	//---------------------------------------------------------------------------------------------
	//	subscribe to a channel (callbackFn is string, name of function)
	//---------------------------------------------------------------------------------------------
	function msgSubscribe(channelID, callbackFn) {
		logDebug('subscribing to channel ' + channelID + ' with callback ' + callbackFn.name);
		msgOutbox = msgOutbox + channelID + "\n";
		msgHandler = new Array(channelID, callbackFn);
		msgHandlers.push(msgHandler);
	}

	//---------------------------------------------------------------------------------------------
	//	periodically initilize pull of messages from server, if page is subscribed to anything
	//---------------------------------------------------------------------------------------------
	function msgPump() {
		var theDate = new Date();
		setTimeout('msgPump();', msgPullFreq);
		if (msgHandlers.length > 0) { 
			msgCheck(); 
			logDebug('checking: ' + theDate.getTime());
		}
	}

	//---------------------------------------------------------------------------------------------
	//	dev only
	//---------------------------------------------------------------------------------------------
	function logDebug(msg) {
		theDiv = document.getElementById('debugger');
		theDiv.innerHTML = theDiv.innerHTML + msg + "<br>\n";
	}

	//---------------------------------------------------------------------------------------------
	//	poll server for messages via XMLHttpRequest
	//---------------------------------------------------------------------------------------------
	function msgCheck() {
		var requestPath = jsServerPath + "notifications/pagecheck/" + jsPageUID;
		var parameters = "action=subscribe&detail=" + encodeURIComponent(msgOutbox);
		xmlhttp = new XMLHttpRequest();

		logDebug('checking notifications... ' + requestPath);

		if ('' == msgOutbox) {
			//-------------------------------------------------------------------------------------
			//	if outbox is empty, use simple GET request
			//-------------------------------------------------------------------------------------
		 	xmlhttp.open('GET', requestPath, true);
			parameters = null;
			logDebug('checking: sent HTTP GET');

		} else {
			//-------------------------------------------------------------------------------------
			//	if outbox is not empty, use simple get request
			//-------------------------------------------------------------------------------------
		 	xmlhttp.open('POST', requestPath, true);
			xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			msgOutbox = '';	

			logDebug("checking: sent HTTP POST");
		}

		//-----------------------------------------------------------------------------------------
		//	do it
		//-----------------------------------------------------------------------------------------
	 	xmlhttp.onreadystatechange = function() {
	  		if (4 == xmlhttp.readyState) { msgProcessIncoming(xmlhttp.responseText); }
	 	}

		try { xmlhttp.send(parameters); }
		catch(err) { logDebug("There was an error: " + err.message); }
	}

	//---------------------------------------------------------------------------------------------
	//	break server response into individual messages
	//---------------------------------------------------------------------------------------------
	function msgProcessIncoming(inbox) {
		var noMessages = true;
		var lines = inbox.split("\n");
		for(var i in lines) {
			var line = lines[i];
			if ((line.length > 3) && (line.substring(0, 1) != '#')) {
				logDebug('routing message: ' + line);
				msgRoute(line);
				noMessages = false;				
			} else {
				if (line.length > 0) { logDebug('ignoring line: ' + line); }
			}
		}

		if (noMessages == true) {
			// nothing found, increase period between checks by 10% unless >= msgPullFreqMax
			if (msgPullFreq < msgPullFreqMax) { msgPullFreq = (msgPullFreq * 1.1); }
		} else {
			// messages posted, check every 
			msgPullFreq = msgPullFreqMin;
		}

	}

	//---------------------------------------------------------------------------------------------
	//	route inbound messages to their callback functions
	//---------------------------------------------------------------------------------------------
	function msgRoute(msg) {
		var parts = msg.split('|');
		if (parts.length < 3) { return false; }

		var channelID = parts[0];
		var msgEvent = parts[1];
		var msgData = base64_decode(parts[2]);

		logDebug('recieved message: ' + parts[0] + " - " + parts[1] + "<br>\n");

		for (var i in msgHandlers) {
			var msgHandler = msgHandlers[i];
			if (msgHandler[0] == channelID) {;
				callBackFn = msgHandler[1];
				callBackFn(channelID, msgEvent, msgData);
			}
		}
	}

