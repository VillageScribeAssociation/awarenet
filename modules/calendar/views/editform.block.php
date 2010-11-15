<? /*
<form name='editCalendar' method='POST' action='%%serverPath%%calendar/save/'>
<input type='hidden' name='action' value='saveCalendar' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Title:</b></td>
    <td><input type='text' name='title' value='%%title%%' size='50' /></td>
  </tr>
  <tr>
    <td><b>Venue:</b></td>
    <td><input type='text' name='venue' value='%%venue%%' size='50' /></td>
  </tr>
  <tr>
    <td><b>Times:</b></td>
    <td>
    <input type='text' name='eventStart' value='%%eventStart%%' size='5' /> 
    Start (hh:mm)
    <input type='text' name='eventEnd' value='%%eventStart%%' size='5' /> 
    End (hh:mm)
    </td>
  </tr>
  <tr>
    <td><b>Category:</b></td>
    <td>
      <select name='category'/>
        <option value='%%category%%'>%%category%%</option>
        <option value='Government'>Government</option>
        <option value='Entertainment'>Entertainment</option>
        <option value='Education'>Education</option>
        <option value='Astronomy'>Astronomy</option>
        <option value='Astronomy'>Sports</option>
        <option value='Astronomy'>Festival</option>
        <option value='Astronomy'>Wildlife</option>
        <option value='Astronomy'>Outdoors</option>
        <option value='Astronomy'>Official Visit</option>
      </select>
    </td>
  </tr>
  <tr>
    <td><b>Date:</b></td>
    <td>
       <input type='text' name='year' value='%%year%%' size='4' /> (yyyy) 
       <input type='text' name='month' value='%%month%%' size='4' /> (mm) 
       <input type='text' name='day' value='%%day%%' size='4' /> (dd) 

    </td>
  </tr>
</table>
<br/>
<b>Description of event:</b><br/>
%%contentJs64%%
[[:editor::base64::jsvar=contentJs64::name=content:]]
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
[[:images::uploadmultiple::refModule=calendar::refModel=Calendar_Entry::refUID=%%UID%%:]]
<br/>
*/ ?>
