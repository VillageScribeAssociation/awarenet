<? /*
<h2>edit page: %%pageName%% (%%moduleName%%)</h2>
<form name='editPage' method='POST' action='/pages/save/'>
<input type='hidden' name='action' value='savePage' />
<input type='hidden' name='page' value='%%pageName%%' />
<input type='hidden' name='module' value='%%moduleName%%' />

<table noborder>
  <tr>
    <td><b>title: </b></td>
    <td><input type='text' name='title' size='60' value='%%title%%' /></td>
  </tr>
  <tr>
    <td><b>theme: </b></td>
    <td>[[:theme::selecttemplates::selected=%%template%%:]]</td>
  </tr>
  <tr>
    <td><b>menu1: </b></td>
    <td><input type='text' name='menu1' value='%%menu1%%' /></td>
  </tr>
  <tr>
    <td><b>menu2: </b></td>
    <td><input type='text' name='menu2' value='%%menu2%%' /></td>
  </tr>
</table><br/>

<b>content (main content panel, not the whole page)</b><br/>
<textarea name='content' rows='20' cols='80'>%%content%%</textarea><br/><br/>
<b>nav1 (primary or leftmost navigation box/column)</b><br/>
<textarea name='nav1' rows='5' cols='80'>%%nav1%%</textarea><br/><br/>
<b>nav2 (secondary or rightmost navigation box/column)</b><br/>
<textarea name='nav2' rows='5' cols='80'>%%nav2%%</textarea><br/><br/>
<b>breadcrumb (list of items showing the user where they are on the page)</b><br/>
<textarea name='breadcrumb' rows='5' cols='80'>%%breadcrumb%%</textarea><br/><br/>
<b>script (inside javascript tags in page head)</b><br/>
<textarea name='script' rows='5' cols='80'>%%script%%</textarea><br/><br/>

<table noborder>
  <tr>
    <td valign='top'>
      <input type='submit' value='save' />
      </form>
    </td>
    <td valign='top'>
      <form name='cancelSave' method='/pages/list/'>
      <input type='submit' value='cancel' />
      </form>
    </td>
  </tr>
</table>
*/ ?>
