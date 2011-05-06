<table width="100%" cellpadding="0" cellspacing="0" border="1">
    <tr>
        <td width="200">Permission Name</td>
        <td>Player Group</td>
        <td>World</td>
        <td width="85">&nbsp;</td>
        <td width="40">&nbsp;</td>
        <td width="50">&nbsp;</td>
    </tr>
    {foreach name=aussen item=entry from=$userArr}
    <tr>
        <td width="200">{$entry.login}</td>
        <td>
            {if $entry.group}
                {$entry.group}
            {else}
                &nbsp;
            {/if}
        </td>
        <td>
            {if $entry.world}
                {$entry.world}
            {else}
                &nbsp;
            {/if}
        </td>
        <td width="50"><a href="index.php?page=player&op=editperm&itemid={$entry.id}">EDIT PERM</a></td>
        <td width="50"><a href="index.php?page=player&op=edit&itemid={$entry.id}">EDIT</a></td>
        <td width="50"><a href="index.php?page=player&op=delete&itemid={$entry.id}">DELETE</a></td>
    </tr>
    {/foreach}
</table>
<a href="index.php?page=player&op=new">New Player</a>