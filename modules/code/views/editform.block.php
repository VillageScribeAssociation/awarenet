<? /*
<form name='editCode' method='POST' action='/code/save/'>
<input type='hidden' name='action' value='saveCodeRecord' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Title:</b></td>
    <td><input type='text' name='title' value='%%title%%' size='50' /></td>
  </tr>
  <tr>
    <td><b>Type:</b></td>
    <td>[[:code::selecttype::default=%%type%%:]]</td>
  </tr>
</table>
<br/>
<b>Description:</b><br/>

<input type='hidden' id='description-edit-hidden' name='description-loader' value='%%descriptionJs%%' />
<script language='JavaScript' type='text/javascript' src='/modules/editor/HyperTextArea.js'></script>
<script language='JavaScript' type='text/javascript'>
<!--
// strip temporary markup
var refFieldName = '';
descriptionJSEHR = document.getElementById('description-edit-hidden');
descriptionJSEHR.value = descriptionJSEHR.value.replace(/--squote--/g, String.fromCharCode(39));
descriptionJSEHR.value = descriptionJSEHR.value.replace(/--dquote--/g, String.fromCharCode(34));
area = new HyperTextArea('description', descriptionJSEHR.value, 800, 300,'/modules/editor/');
//-->
</script><br/>

<b>Content:</b><br/>
<textarea rows='20' cols='114' name='content' id='txtContent'>%%safeContent%%</textarea>
<br/>
<b>Reason: </b><input type='text' name='reason' size='50' /><br/>
<small>Edit summary, why was this change made?</small>

<table noborder>
  <tr>
   <td valign='top'>
    <input type='submit' value='save' />
    </form>
   </td>
   <td valign='top'>
   <input type='button' value='clean code' onClick='cleanCode();' />
   </td>
   <td>
   <form name='cDelete' method='GET' action='%%delUrl%%'>
   <input type='submit' value='delete' />
   </form>
   </td>
 </tr>
</table>
*/ ?>
