<div class = "helpdesk_searchform">
  <div class="rolesform">
    <label for="rolesform_jump">{"currentrole:role"|s}&nbsp;</label>
    {$select}
  </div>
</div>
{$heading}
<div class="box">
  <table id="participants" class="box flexible generaltable generalbox">
      <tr>
        <th class="header c0" scope="col">{"userpic:moodle"|s}</th>
        <th class="header c1" scope="col">{"fullname:moodle"|s}</th>
      </tr>
    {foreach $users as $user}
      <tr class="r{if $user@iteration is even}0{else}1{/if}">
        <td class="cell c0">{picture user=$user}</td>
        <td class="cell c1"><strong><a href="{$wwwroot}/user/view.php?id={$user->id}">{$user->firstname} {$user->lastname}</a></strong></td>
      </tr>
    {/foreach}
  </table>
</div>
