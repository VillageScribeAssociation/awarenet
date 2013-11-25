<? /*

<div id='divReport%%refUID%%' style="visibility: hidden; display: none;">
  <div class='inlinequote' style='background-color: #ffaaaa;'>
    <div class='indent'>
    <h3>Report Abuse</h3>
    <p>Something wrong?  Please let the moderators know - use the text box below to to tell 
	 us what the problem is.</p>
	</div>
    <form name='reportAbuse%%refUID%%' method='POST' action='%%serverPath%%abuse/newreport/' />
	  <input type='hidden' name='action' value='newAbuseReport' />
	  <input type='hidden' name='refModule' value='%%refModule%%' />
	  <input type='hidden' name='refModel' value='%%refModel%%' />
	  <input type='hidden' name='refUID' value='%%refUID%%' />
	  <input type='hidden' id='abuseFrom%%refUID%%' name='fromurl' value='' />
	  <textarea rows='5' cols='38' id='abuseComment%%refUID%%' name='comment'></textarea><br/>
	  <small>This form is for alerting administrators to problems on awareNet.</small>
    </form>
    <table noborder>
      <tr>
        <td><input type='button' value='Submit Abuse Report' onClick="
		// submitAbuse%%refUID%%X(); 
		var theDiv = document.getElementById('divReport%%refUID%%');
		theDiv.innerHTML = '';	// remove this so there's only one form with this id
		txtFrom = document.getElementById('abuseFrom%%refUID%%');
		txtFrom.value = window.location.href;
		taReport = document.getElementById('abuseComment%%refUID%%');
		if ('' == taReport.value) {
			alert('Please describe the problem in the text box.');
			return false;
		}
		document.forms.reportAbuse%%refUID%%.submit();
			" /></td>
        <td><input type='button' value='Cancel' 
			onClick="var theDiv = document.getElementById('divAbuse%%refUID%%'); theDiv.innerHTML = '';" />
        </td>
      </tr>
    </table>
  </div>
</div>

<script language='Javascript'>
	function submitAbuse%%refUID%%X() {
		var theDiv = document.getElementById('divReport%%refUID%%');
		theDiv.innerHTML = '';	// remove this so there's only one form with this id
		txtFrom = document.getElementById('abuseFrom%%refUID%%');
		txtFrom.value = window.location.href;
		taReport = document.getElementById('abuseComment%%refUID%%');
		if ('' == taReport.value) {
			alert('Please describe the problem in the text box.');
			return false;
		}
		document.forms.reportAbuse%%refUID%%.submit();
	}
</script>

<span id='divAbuse%%refUID%%'></span>

<img src='%%serverPath%%themes/%%defaultTheme%%/images/icons/abuse3.png' 
	onClick="divCopyInnerHtml('divReport%%refUID%%', 'divAbuse%%refUID%%');" 
	style='float: right;' width='20px'
	border='0' alt='report abuse' title='report abuse' />


*/ ?>
