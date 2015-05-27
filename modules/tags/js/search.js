

//--------------------------------------------------------------------------------------------------
//*	javascript to use the tag search object from the universal search bar at the top of the page
//--------------------------------------------------------------------------------------------------
//:	assumes jQuery, expects to be called ktagsearch

function Tags_Search() {
	this.txtId = 'txtGlobalSearch';
	this.divId = 'divGlobalSearch';
	this.spanId = 'spanSearch';
	this.left = -1; 
	this.visible = false;

	this.searchNotice = 'Searching...';

	//----------------------------------------------------------------------------------------------
	//	show the search results div
	//----------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	bind to search txt
	//----------------------------------------------------------------------------------------------

	this.onTxtChange = function() {
		if (!klive) { return; }

		var osLeft = ($('#' + this.txtId).offset().left);

		if (false == this.visible) {
			var spanWidth = ($('#' + this.spanId).width())
			$('#' + this.divId).css('left', osLeft);
			$('#' + this.divId).width(spanWidth);
			$('#' + this.divId).show();
			this.visible = true;
		}

		var qTxt = $('#' + this.txtId).val();

		if (qTxt.length < 3) {
			$('#' + this.divId).html('');
			return;
		}

		var q64 = kutils.base64_encode(qTxt);
		//var block = "[[:tags::linkable::q64=" + q64 + ":]]";
		//$('#' + this.divId).html(this.searchNotice);
		
		//  tag search currently replaced with user search as of issue 402
		
		var block = '[[' + ':users::searchresults'
			 + '::q=' + q64
			 + '::b64=yes'
//			 + '::cbjs=%%cbjs%%'
//			 + '::cblabel=%%cblabel%%'
//			 + '::cbicon=%%cbicon%%'
             + '::bgcolor=#aaaaaa'
			 + ':]]';

		$('#' + this.divId).css('left', osLeft);

		klive.bindDivToBlock(this.divId, block, false);

	}

}

ktagsearch = new Tags_Search();
