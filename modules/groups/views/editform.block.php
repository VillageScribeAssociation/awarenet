<? /*
<form name='editschool' method='POST' action='/groups/save/'>
<input type='hidden' name='action' value='saveRecord' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Name:</b></td>
    <td><input type='text' name='name' value='%%name%%' size='50' /></td>
  </tr>
  <tr>
    <td><b>School:</b></td>
    <td>
      [[:schools::select::default=%%school%%:]] &nbsp;
      <b>Type:</b> 
      <select name='type'>
        <option value='%%type%%'>%%type%%</option>
        <option value='Club'>Club</option>
        <option value='Team'>Team</option>
        <option value='Society'>Society</option>
        <option value='Association'>Association</option>
        <option value='Production'>Production</option>
        <option value='Group'>Group</option>
      </select>
    </td>
  </tr>
</table>
<br/>
<b>Description of this group:</b><br/>

<input type='hidden' id='description-edit-hidden' name='description-loader' value='%%descriptionJs%%' />
<script language='JavaScript' type='text/javascript' src='/modules/editor/HyperTextArea.js'></script>
<script language='JavaScript' type='text/javascript'>
<!--
// strip temporary markup
var $refFieldName = '';
descriptionJSEHR = document.getElementById('description-edit-hidden');
descriptionJSEHR.value = descriptionJSEHR.value.replace(/--squote--/g, String.fromCharCode(39));
descriptionJSEHR.value = descriptionJSEHR.value.replace(/--dquote--/g, String.fromCharCode(34));
area = new HyperTextArea('description', descriptionJSEHR.value, 500, 400,'/modules/editor/');
//-->
</script><br/>
<table noborder>
  <tr>
   <td valign='top'>
    <input type='submit' value='Save' />
    </form>
   </td>
   <td>
   <form name='cDelete' method='GET' action='%%delUrl%%'>
   <input type='submit' value='Delete' />
   </form>
   </td>
 </tr>
</table>

<h2>Images</h2>
[[:images::uploadmultiple::refModule=groups::refUID=%%UID%%:]]
*/ ?>