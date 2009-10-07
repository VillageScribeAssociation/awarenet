<? /*
<form name='editschool' method='POST' action='/schools/save/'>
<input type='hidden' name='action' value='saveRecord' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Name:</b></td>
    <td><input type='text' name='name' value='%%name%%' size='50' /></td>
  </tr>
  <tr>
    <td><b>Country:</b></td>
    <td><input type='text' name='country' value='%%country%%' size='50' /></td>
  </tr>
</table>
<br/>
<b>Description of this school:</b><br/>

%%descriptionJs64%%
[[:editor::base64::jsvar=descriptionJs64::name=description:]]
<br/>

<table noborder>
  <tr>
   <td valign='top'>
    <input type='submit' value='save' />
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
[[:images::uploadmultiple::refModule=schools::refUID=%%UID%%:]]
*/ ?>
