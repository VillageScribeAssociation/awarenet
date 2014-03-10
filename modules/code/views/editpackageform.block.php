<? /*
<form name='editCode' method='POST' action='%%serverPath%%code/savepackage/'>
<input type='hidden' name='action' value='savePackage' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Name:</b></td>
    <td><input type='text' name='name' value='%%name%%' size='50' /></td>
  </tr>
  <tr>
    <td><b>Version:</b></td>
    <td><input type='text' name='version' value='%%version%%' size='4' /></td>
  </tr>
</table>
<br/>
<b>Description:</b><br/>
<textarea name='description' rows='10' style='width:100%'>%%description%%</textarea>


<table noborder>
  <tr>
   <td valign='top'>
    <input type='submit' value='save' />
    </form>
   </td>
   <td>
   <form name='cDelete' method='GET' action='%%delUrl%%'>
   <input type='submit' value='delete' />
   </form>
   </td>
 </tr>
</table>
<br/>
<small>UID: %%UID%% createdOn: %%createdOn%%</small>
*/ ?>
