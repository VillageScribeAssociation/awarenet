<? /*
<hr/>
<form
	id='frmReply%%UID%%'
	name='reply%%UID%%'
	method='POST'
	action='%%serverPath%%comments/reply/'
	onSubmit='khta.updateAllAreas();'
>

<input type='hidden' name='action' value='addReply' />
<input type='hidden' name='parentUID' value='%%UID%%' />

<div
	id='htaReply%%UID%%'
	class='HyperTextArea64'
	title='reply%%UID%%'
	width='-1'
	height='200'
	refModule='%%refModule%%'
	refModel='%%refModel%%'
	refUID='%%refUID%%'
></div>
</form>

<table nborder>
  <tr>
    <td valign='top'><input type='button' value='Reply To Comment' onClick="comments_saveReply('%%UID%%');" /></td>
    <td valign='top'><input type='button' value='Cancel' onClick="comments_hideReplyInline('%%UID%%');" /></td>
  </tr>
</table>
<script>
	khta.convertDivs();
</script>

*/ ?>
