//--------------------------------------------------------------------------------------------------
//*	shell window code
//--------------------------------------------------------------------------------------------------

//--------------------------------------------------------------------------------------------------
//	chat window object
//--------------------------------------------------------------------------------------------------

function Live_RemoteShellWindow(sessionUID) {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	if (!kutils) { alert('WARNING: kutils not available'); }
	
	this.sessionUID = sessionUID;		//_	uniquely identifies this shell window [string]
	this.history = new Array();			//_	array of shellCmd objects [array:string]
	this.bufferLength = 20;				//_	number of history items to keep [int]
	this.bufferPointer = -1;			//_	array index of last command [int]
	this.replayPointer = -1;			//_	array index of selected command [int]
	this.pending = 0;					//_	number of AJAX requests outstanding [int]

	this.divHistory = document.getElementById('divHistory');
	this.taPrompt = document.getElementById('content');
	this.taPrompt.focus();

	//----------------------------------------------------------------------------------------------
	//	resize controls in response to window event
	//----------------------------------------------------------------------------------------------

	kwnd.onResize = function() {
		var divCPS = document.getElementById('divSessionSummary');			//%	div
		var divH = document.getElementById('divHistory');					//%	div
		var selPeer = document.getElementById('selPeer');					//% select peer
		var txtBox = document.getElementById('content');					//% textarea

		$('#selPeer').width($(window).width() - 2);

		$('#divHistory').height($(window).height() - $('#content').height() - 6);
		$('#divHistory').width($(window).width() - 2);
		$('#content').width($(window).width() - 6);
		$('#content').css('top', $('#divHistory').height());
	}

	kwnd.onResize();
	kwnd.setBanner("Kapenta Web Shell <span style='font-size: 8pt;'>version 0.1 alpha</span>");

	//----------------------------------------------------------------------------------------------
	//.	user has entered a command in the box and pressed enter
	//----------------------------------------------------------------------------------------------
	//returns: true if command created / run, false if not [bool]

	this.cmdSubmitted = function() {
		//------------------------------------------------------------------------------------------
		//	tidy and check the command
		//------------------------------------------------------------------------------------------
		this.taPrompt.value = this.taPrompt.value.replace(/(\r\n|\n|\r)/gm,"");
		//alert('command submitted: ' + this.taPrompt.value);
		var cmdStr = kutils.trim(this.taPrompt.value);
		if ('' == cmdStr) { return false; }
		
		//------------------------------------------------------------------------------------------
		//	make a new shell cmd object and add it to the array
		//------------------------------------------------------------------------------------------
		var newCmd = new Live_ShellCmd(this, cmdStr);

		if (this.bufferPointer >= this.bufferLength) {
			// bump first item off start of queue
			for (var idx = 0; idx < (this.bufferLength - 1); idx++) {
				this.history[idx] = this.history[idx + 1];
			}
			this.bufferPointer = this.bufferLength;
		} else {
			this.bufferPointer++;
		}

		this.history[this.bufferPointer] = newCmd;		// add to end of array
		this.replayPointer = this.bufferPointer;		// point to last item in command array
		this.history[this.bufferPointer].send();		// POST to server

		//------------------------------------------------------------------------------------------
		// render new command into main (history) div
		//------------------------------------------------------------------------------------------
		this.divHistory.innerHTML = this.divHistory.innerHTML + newCmd.toHtml();
		this.scrollToBottom();
		this.taPrompt.value = '';

		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	up arrow pressed, load previous history item
	//----------------------------------------------------------------------------------------------
	//returns: true if history was navigated, false if not [bool]

	this.historyPrev = function() {
		if (-1 == this.bufferPointer) { return false; }			// no commands in history
		if (this.replayPointer > 0) { this.replayPointer--; }
		this.taPrompt.value = this.history[this.replayPointer].cmdStr;
		this.setStatus('History ' + this.replayPointer);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	down arrow pressed, load next history item or clear ta if none available
	//----------------------------------------------------------------------------------------------
	//returns: true if hostory was navigated, false if not [bool]

	this.historyNext = function() {
		if (this.history.length > 0) {
			if (this.replayPointer < this.bufferPointer) { this.replayPointer++; }
			this.taPrompt.value = this.history[this.replayPointer].cmdStr;
			this.setStatus('History ' + this.replayPointer);
		} else { this.setStatus('no history'); }
	}

	//----------------------------------------------------------------------------------------------
	//.	find array index of a message given its UID
	//----------------------------------------------------------------------------------------------
	//arg: UID - UID of a chat message [string]
	//returns: array index if found, -1 if not found [int]

	this.getHistoryIndex = function(UID) {
		for (var i in this.history) { if (this.history[i].UID == UID) { return i; } }
		return -1;
	}

	//----------------------------------------------------------------------------------------------
	//.	set the status bar text for this window
	//----------------------------------------------------------------------------------------------

	this.setStatus = function(txt) {
		window.parent.kwindowmanager.windows[windowIdx].setStatus(txt);
	}

	//----------------------------------------------------------------------------------------------
	//.	scroll history to the bottom
	//----------------------------------------------------------------------------------------------

	this.scrollToBottom = function() {
		this.divHistory.scrollTop = this.divHistory.scrollHeight;
	}

	//----------------------------------------------------------------------------------------------
	//.	clear history
	//----------------------------------------------------------------------------------------------

	this.clearHistory = function() {
		this.history = new Array();
		this.divHistory.innerHTML = '';
	}

	//----------------------------------------------------------------------------------------------
	//.	handle key presses in the textarea
	//----------------------------------------------------------------------------------------------
	//TODO: figure out how to reference this without the global

	this.taKeyUp = function(e) {
		var e = e || window.event;
		var keyID = (window.event) ? event.keyCode : e.keyCode;
		
		switch(keyID) {
			case 38:	
				kshellwindow.historyPrev();
				break;		// .....................................................................
	
			case 40:	
				kshellwindow.historyNext();
				break;		// .....................................................................

			default:
				if (
					(kshellwindow.taPrompt.value.indexOf("\n") != -1) || 
					(kshellwindow.taPrompt.value.indexOf("\r") != -1)
				) {
					kshellwindow.cmdSubmitted();
				}
				break;		// .....................................................................

		}
	}

	//----------------------------------------------------------------------------------------------
	//.	submit a command via JavaScript rather than typing it in the box
	//----------------------------------------------------------------------------------------------
	//arg: cmdTxt - a command to run as this user [string]

	this.submit = function(cmdTxt) {
		this.taPrompt.value = cmdTxt;
		kshellwindow.cmdSubmitted();
	}

	//----------------------------------------------------------------------------------------------
	//.	poll for new results
	//----------------------------------------------------------------------------------------------

	this.poll = function() {

		var pollUrl = jsServerPath + 'live/pollremotesession/' + this.sessionUID;
		var params = 'x=y';

		var cbFn = function(responseText, status) {
			$('#divHistory').append(responseText);
			window.setTimeout("kshellwindow.poll();", 3000)
		}

		kutils.httpPost(pollUrl, params, cbFn);					//	send it
	}

	//----------------------------------------------------------------------------------------------
	//.	set and clear throbber
	//----------------------------------------------------------------------------------------------
	//arg: byNumber - always 1 or -1 [int]

	this.changePending = function(byNumber) {
		this.pending = this.pending + byNumber;
		if (this.pending > 0) { kwnd.setThrobber(true); }
		else { kwnd.setThrobber(false); }
	}

	this.taPrompt.onkeyup = this.taKeyUp;
	this.poll();							//	start the polling
}

//--------------------------------------------------------------------------------------------------
//	object representing a single console messages / commands
//--------------------------------------------------------------------------------------------------

function Live_ShellCmd(oShellWindow, cmdStr) {
	
	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	this.UID = kutils.createUID();		//_	UID of this Live_ShellCmd object [string]
	this.cmdStr = cmdStr;				//_ command as entered by user [string]
	this.state = 'new';					//_	current state of execution [string]
	this.oShell = oShellWindow;			//_	shell window this command runs from [object]

	//----------------------------------------------------------------------------------------------
	//.	render as HTML
	//----------------------------------------------------------------------------------------------
	//returns: html as displayed in the messages page of the chat window [string]

	this.toHtml = function() {
		var html = '';		//% return value [string]
		var dc = 'chatmessagegray';
		dc = 'chatmessageblack';

		if ('sent' == this.state) { var dc = 'chatmessagered'; }
		if ('done' == this.state) { var dc = 'chatmessagegreen'; }

		var throbber = jsServerPath 
			+ 'themes/clockface/images/throbber-inline.gif';

		html = html + "<div class='" + dc + "' id='hist" + this.UID + "'>"
			+ "<small><b>" + this.cmdStr + "</b></small><br/>"
			+ "<div id='result" + this.UID  + "'>"
			+ "<img src='" + throbber + "' align='right' /><br/>"
			+ "</div>"
			+ "</div>\n";

		return html;
	}

	//----------------------------------------------------------------------------------------------
	//.	convert to params string for HTTP POST
	//----------------------------------------------------------------------------------------------
	//returns: urlencoded POST body [string]

	this.toFormData = function() {
		var params = 'cmd=' + base64_encode(this.cmdStr);		//% POST body [string]
		return params;
	}

	//----------------------------------------------------------------------------------------------
	//.	send this command to be run on the server
	//----------------------------------------------------------------------------------------------

	this.send = function() {
		var that = this;										//%	ref from clusures [string]		
		var sendUrl = jsServerPath + 'live/doremotecmd/';		//%	URL to POST to [string]

		var params = ''
		 + 'peer=' + $('#selPeer').val() + '&'
		 + 'session=' + this.oShell.sessionUID + '&'
		 + 'cmd=' + kutils.base64_encode(cmdStr);				//%	POST vars [string]

		this.oShell.changePending(1);							//	increment outstanding requests

		var cbFn = function(responseText, status) {
			if (200 == status) {
				var resultDiv = document.getElementById('result' + that.UID);
				var strCls = '<!-- kshellwindow.clearHistory() -->';
				var strExt = '<!-- kshellwindow.exit() -->';
				var strErr = '<!-- cmd.error() -->';
				var strAOk = '<!-- cmd.ok() -->';

				resultDiv.innerHTML = "<pre>" + responseText + "<pre>\n";
				that.oShell.scrollToBottom();

				//NOTE: using .class proeprty breaks IE7
				if (responseText.indexOf(strErr) > 0) { 
					resultDiv.setAttribute('class', 'chatmessagered');
				}
				if (responseText.indexOf(strAOk) > 0) {
					resultDiv.setAttribute('class', "chatmessagegreen");
				}

				if (responseText.indexOf(strCls) > 0) { that.oShell.clearHistory(); }
				if (responseText.indexOf(strExt) > 0) { kwnd.closeWindow(); }
			}
			that.oShell.changePending(-1);						//	decrement outstanding requests
		}

		kutils.httpPost(sendUrl, params, cbFn);					//	send it
	}

}
