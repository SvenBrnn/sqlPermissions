<table width="100%" cellpadding="0" cellspacing="0" border="1">
    <tr>
        <td>Player Group</td>
        <td>World</td>
        <td width="80">&nbsp;</td>
        <td width="45">&nbsp;</td>
        <td width="50">&nbsp;</td>
    </tr>
    {foreach name=aussen item=entry from=$grpArr}
    <tr>
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
        <td width="85"><a href="index.php?page=group&op=editperm&itemid={$entry.id}">EDIT PERM</a></td>
        <td width="45"><a href="index.php?page=group&op=edit&itemid={$entry.id}">EDIT</a></td>
        <td width="50"><a href="index.php?page=group&op=delete&itemid={$entry.id}">DELETE</a></td>
    </tr>
    {/foreach}
</table>
<a href="index.php?page=group&op=new">New Group</a>