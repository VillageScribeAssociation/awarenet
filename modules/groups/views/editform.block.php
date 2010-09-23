<? /*
<form name='editschool' method='POST' action='%%serverPath%%groups/save/'>
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

%%descriptionJs64%%
[[:editor::base64::jsvar=descriptionJs64::name=description:]]
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
[[:images::uploadmultiple::refModule=groups::refModel=Groups_Group::refUID=%%UID%%:]]
*/ ?>
