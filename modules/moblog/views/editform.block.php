<? /*
<br/>
<form name='editPost' method='POST' action='%%serverPath%%/moblog/save/'>
<input type='hidden' name='action' value='saveRecord' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Title: </b></td>
    <td><input type='text' name='title' value='%%mbTitle%%' size='50' /></td>
  </tr>
  <tr>
    <td><b>Published: </b></td>
    <td>
      <select name='published'>
        <option value='yes'>yes</option>
        <option value='no'>no</option>
      </select>
    </td>
  </tr>
</table>
<br/>
<b>Post Body: </b><small>(use special {fold} marker at end of introduction)
%%contentJs64%%
[[:editor::base64::jsvar=contentJs64::name=content:]]
<br/>
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
[[:images::uploadmultiple::refModule=moblog::refUID=%%UID%%:]]
*/ ?>
