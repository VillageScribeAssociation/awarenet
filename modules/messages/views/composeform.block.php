<? /*

<script src='%%serverPath%%modules/messages/js/compose.js'></script>
<h1>New Message</h1>
<form name='sendMessage' method='POST' action='%%serverPath%%messages/send/' />
<input type='hidden' name='action' value='sendMessage' />
<input type='hidden' name='re' value='%%reMsg%%' />
<input type='hidden' id='txtRecipients' name='recipients' value='%%jsRecipientUID%%' />

<table noborder width='100%'>
  <tr>
    <td><b>Subject: </b></td>
	<td><input type='text' name='subject' style='width: 100%;' value='%%subject%%' /></td>
  </tr>
  <tr>
	<td valign='top'><b>To:</b></td>
	<td><div id='divRecipients'></div></td>
  </tr>
</table>
<br/>

[[:editor::add::name=content::height=400:]]
<br/>

<table noborder>
  <tr>
    <td valign='top'>
      <input type='submit' value='Send Message' /> 
      </form>
    </td>
    <td valign='top'>
      <form name='cancelSend' method='GET' action='%%serverPath%%messages/'>
      <input type='submit' value='Cancel' />
      </form>
    </td>
  </tr>
</table>

*/ ?>
