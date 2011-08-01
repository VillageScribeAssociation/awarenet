<? /*

<div id='divRiver%%riverUID%%'>
[[:%%rivermodule%%::%%riverview%%::%%riverargs%%::%%riverpagevar%%=1:]]
</div>

<script language='Javascript'>
	function river%%riverUID%%proto() {
		
		//------------------------------------------------------------------------------------------
		//	properties
		//------------------------------------------------------------------------------------------

		this.pageno = 1;

		//------------------------------------------------------------------------------------------
		//.	load the next page of results
		//------------------------------------------------------------------------------------------

		this.more = function() {
			var nextblock = '';
			this.pageno = this.pageno + 1;
			nextblock = "[" + "[:%%rivermodule%%::%%riverview%%::%%riverargs%%::%%riverpagevar%%=" + this.pageno + ":]" + "]" ;
			//alert('loading page' + this.pageno + ': ' + nextblock);

			nextDivID = 'riverX' + createUID();

			theDiv = document.getElementById('divRiver%%riverUID%%');
			theDiv.innerHTML = theDiv.innerHTML
				 + "<div id='" + nextDivID + "'>"
				 + "<div style='background-color: #b1d27e;'>loading more...</div>"
				 + "</div>";

			if(typeof klive === "undefined") {
				this.waitForInit(nextDivID, nextblock);
			} else {
				klive.bindDivToBlock(nextDivID, nextblock, false);
			}

		}

		//------------------------------------------------------------------------------------------
		//.	delay loading results until klive (message pump) is available)
		//------------------------------------------------------------------------------------------

		this.waitForInit = function (nextDivID, nextblock) {
			if(typeof klive === "undefined") {
				// not yet
				var t = setTimeout("river%%riverUID%%.waitForInit('" + nextDivID + "', '" + nextblock + "')", 1000);

			} else {		
				// get a page of content
				klive.bindDivToBlock(nextDivID, nextblock, false);

			}
		}

	}

	river%%riverUID%% = new river%%riverUID%%proto();

</script>
<br/>	
<div class='actionbox' onClick='river%%riverUID%%.more();'>More &gt;&gt;</div>

*/ ?>
