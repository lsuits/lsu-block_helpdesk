<div class = "helpdesk_searchform">
  <form method="POST" name="search_form">
    {foreach $criterion as $k => $c}
        <span class="search_label">{$c}</span>
          <select name="{$k}_equality">
            {foreach $availability as $avail => $available}
                <option value="{$avail}" {if $avail == 'contains'} SELECTED {/if}>{$available}</option>
            {/foreach}
          </select>
          <input class="inputbox" type="text" name="{$k}_terms" id="{$k}"/>
          <br/>
    {/foreach}
    <input type="hidden" name="mode" value="{$mode}"/>
    <input class="searchbutton" type="submit" value="{"submit:moodle"|s}"/>
  </form>
</div>

<div class="box">
{if $data}
    {if !$results}
        <span class="results">{"no_results"|s}<span>
    {else}
        <table id="participants" class="flexible generaltable generalbox">
             <tr>
                <th class="header c1" scope="col">{"fullname:moodle"|s}</th>
             </tr>
             {foreach $results as $id => $result}
                <tr class="r{if $result@iteration is even}0{else}1{/if}">
                      <td class="cell c1">
                        <a class="{if isset($result->visible)}
                                    {if !$result->visible} dimmed {/if}
                                  {/if}" href="{$follow_link}?id={$id}">
                                    {fullname obj=$result}
                      </td>
                </tr>
             {/foreach}
        </table>
    {/if}
{/if}
</div>
