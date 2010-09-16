<? /*

<script src='%%serverPath%%modules/messages/compose.js'></script>
<h1>New Message</h1>
<form name='sendMessage' method='POST' action='%%serverPath%%messages/send/' />
<input type='hidden' name='action' value='sendMessage' />
<input type='hidden' name='re' value='%%reMsg%%' />
<input type='hidden' id='txtRecipients' name='recipients' value='%%jsRecipientUID%%' />

<b>Subject: </b> <input type='text' name='subject' size='60' value='%%subject%%' /><br/>
<table noborder>
  <tr>
    <td width='292' valign='top'>
	  <b>To:</b><br/>
	   <div id='composeDisplayRecip'>%%jsRecipientHtml%%</div>
	</td>
    <td valign='top'>
       <iframe src='%%serverPath%%users/usersearch/' id='ifCUserSearch' name='userSearchIf'
			frameborder='no' width='292' height='140'></iframe>
    </td>
  </tr>
</table>

[[:editor::add::name=content:]]
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
