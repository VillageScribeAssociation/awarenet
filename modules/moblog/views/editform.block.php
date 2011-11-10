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
<b>Post Body: </b><small>(use special {fold} marker at end of introduction)</small>
<div class='HyperTextArea64' title='content' width='570' height='400' style='visibility: hidden; display: none'>
%%content64%%
</div>
<script language='Javascript'> khta.convertDivs(); </script>
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
<br/>
[[:theme::navtitlebox::label=Images::toggle=divBlogImages:]]
<div id='divBlogImages'>
[[:images::uploadmultiple::refModule=moblog::refModel=moblog_post::refUID=%%UID%%:]]
</div>
<br/>
*/ ?>
