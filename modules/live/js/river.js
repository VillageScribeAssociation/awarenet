
//--------------------------------------------------------------------------------------------------
//*	javascript object to sequentially load pages of results
//--------------------------------------------------------------------------------------------------
//+	To use this on must initialize a new Live_River object with a divId and block template.  This
//+	template is a block tag missing its outer braces, where %%pageNumber%% will be iterated, eg:
//+
//+		[:moblog::list::something=whatever::myPageNo=%%pageNumber%%:]

//arg: divId - id of div to load content into [string]
//arg: blockTemplate - kapenta block tag, a pattern for loading a view [string]

function Live_River(riverUID, divId, blockTemplate) {
		
	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------
	if (!kutils) { alert('Warning: kutils not yet available.'); }

	this.UID = riverUID;					//_	UID of this river [string]
	this.pageNumber = 1;					//_	current page count [int]
	this.divId = divId;						//_	div into which content will be placed [string]
	this.blockTemplate = blockTemplate;		//_	view to iteratively load [string]

	//----------------------------------------------------------------------------------------------
	//	disable this if the first page of results is also the last
	//----------------------------------------------------------------------------------------------

	var theDiv = document.getElementById(this.divId);
	if (-1 != theDiv.innerHTML.indexOf('<!-- end of results -->')) {
		var btnDiv = document.getElementById('btn' + this.UID);
		btnDiv.style.visibility = 'hidden';
		btnDiv.style.display = 'none';
	}

	//----------------------------------------------------------------------------------------------
	//.	load the next page of results
	//----------------------------------------------------------------------------------------------

	this.more = function() {
		if (typeof kutils === "undefined") { this.waitForInit(); }

		this.pageNumber = this.pageNumber + 1;
		var nextBlock = this.blockTemplate.replace(/%%pageNumber%%/, this.pageNumber);
		var nextDivId = 'riverX' + kutils.createUID();
		var divBtnId = 'btn' + this.UID;

		theDiv = document.getElementById(this.divId);
		if (theDiv) {

			//--------------------------------------------------------------------------------------
			//	set the loading notification
			//--------------------------------------------------------------------------------------
			theDiv.innerHTML = theDiv.innerHTML
				 + "<div id='" + nextDivId + "'>"
				 + "<div style='background-color: #b1d27e;'>loading more...</div>"
				 + "</div>";

			//--------------------------------------------------------------------------------------
			//	load the next page of results
			//--------------------------------------------------------------------------------------
			var url = jsServerPath + 'live/getblock/';
			var params = 'b=' + kutils.base64_encode(nextBlock);	
			var that = this;

			var cbFn = function(responseText, status) {
				if (200 == status) {
					var myDiv = document.getElementById(nextDivId);
					myDiv.innerHTML = responseText;
					that.runJs(responseText);

					if (-1 != responseText.indexOf('<!-- end of results -->')) {
						var divBtn = document.getElementById(divBtnId);
						divBtn.innerHTML = "End.";
						divBtn.style.backgroundColor = '#aaaaaa';
					}

				} else { alert("Error: could not conntect to server: " + status); }
			}

			kutils.httpPost(url, params, cbFn);
						
		} else { alert('Missing div: ' + this.divId); }

	}

	//----------------------------------------------------------------------------------------------
	//.	run javascript embedded in the new page section
	//----------------------------------------------------------------------------------------------
	//; this is just to get videos working in feeds for now

	this.runJs = function(rawHtml) {
		var start = 0;
		var end = 1;
		var done = false;
					
		while (start != -1) {
			start = rawHtml.indexOf("<script", start);
			if (-1 != start) { end = rawHtml.indexOf("</script>", start); }

			if ((start > -1) && (end > start)) {
				start = rawHtml.indexOf('>', start) + 1;
				var evStr = rawHtml.substring(start, end);
				if (evStr.length > 5) {
					//alert('start: ' + start + ' end: ' + end + "\n" + evStr);
					eval(evStr);
				}
			}

		}

	}

	//----------------------------------------------------------------------------------------------
	//.	delay loading results until klive (message pump) is available)
	//----------------------------------------------------------------------------------------------

	this.waitForInit = function() {
		if(typeof kutils === "undefined") {
			// keep waiting
			var jsExec = "river" + this.UID + ".waitForInit('" + nextDivID + "', '" + nextblock + "')";
			var t = setTimeout(jsExec, 1000);

		} else {
			// get a page of content
			this.more();
		}
	}

}
