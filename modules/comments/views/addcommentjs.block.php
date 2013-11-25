<? /*

[[:comments::tip_policy:]]

<form name='addComment' id='formCommentsAdd' method='POST' action='%%serverPath%%comments/add/'>
<input type='hidden' id='iCommentRefModule' name='refModule' value='%%refModule%%' />
<input type='hidden' id='iCommentRefModel' name='refModel' value='%%refModel%%' />
<input type='hidden' id='iCommentRefUID' name='refUID' value='%%refUID%%' />
<input type='hidden' id='iCommentReturn' name='return' value='none' />

<textarea id='txtComment' name='comment' rows='7' cols='50'></textarea><br/>

<table noborder>
  <tr>
    <td><input type='button' onClick='commentsPostNew();' value='Post Comment' /></td>
  	<td><div id='divCommentSendStatus'></div></td>
  </tr>
</table>
</form>

<script language='javascript'>

	function commentsPostNew() {
		//-----------------------------------------------------------------------------------------
		// check form
		//-----------------------------------------------------------------------------------------
		var commentTxt = document.getElementById('txtComment').value
		if (trim(commentTxt) == '') {
			alert('Please enter a comment before posting.');
			return false;
		}

		//-----------------------------------------------------------------------------------------
		// set status
		//-----------------------------------------------------------------------------------------
		var sendMsg = "<span class='ajaxmsg'>Posting comment...</span>";
		divSetContent('divCommentSendStatus', sendMsg);

		//-----------------------------------------------------------------------------------------
		// read form values
		//-----------------------------------------------------------------------------------------
		var theForm = document.getElementById('formCommentsAdd');
		var parameters = urlEncodeForm(theForm);

		//-----------------------------------------------------------------------------------------
		// create POST xmlHTTPrequest
		//-----------------------------------------------------------------------------------------
		var requestPath = jsServerPath + 'comments/add/';
		xmlhttp = new XMLHttpRequest();
	 	xmlhttp.open('POST', requestPath, true);
		xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

	 	xmlhttp.onreadystatechange = function() {
	  		if (4 == xmlhttp.readyState) { 
				var sentMsg = "<span class='ajaxmsg'>done.</span>";
				divSetContent('divCommentSendStatus', sendMsg);
				setTimeout("divSetContent('divCommentSendStatus', '');", 3000);

				//---------------------------------------------------------------------------------
				// perform a pageCheck out of sequence (ie, immediately)
				//---------------------------------------------------------------------------------
				//msgPullFreq = msgPullFreqMin;
				//msgCheck();

			}
	 	}

		//-----------------------------------------------------------------------------------------
		// clear the form and send
		//-----------------------------------------------------------------------------------------
		commentsClearForm()
		try { xmlhttp.send(parameters); }
		catch(err) { 
			logDebug("There was an error: " + err.message); 
			errMsg = "<span class='ajaxerror'>&nbsp;Error: POST failed</span>&nbsp;";;
			divSetContent('divCommentSendStatus', errMsg);
		}

		divSetContent('divCommentSendStatus', sendMsg);
	}

	function commentsClearForm() {
		var theTa = document.getElementById('txtComment');
		theTa.value = '';
	}

</script>

*/ ?>
