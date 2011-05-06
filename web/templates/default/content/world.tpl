<table width="100%" cellpadding="0" cellspacing="0" border="1">
    <tr>
        <td>World</td>
        <td width="50">&nbsp;</td>
        <td width="50">&nbsp;</td>
    </tr>
    {foreach name=aussen item=entry from=$worldArr}
    <tr>
        <td>
            {if $entry.world}
                {$entry.world}
            {else}
                &nbsp;
            {/if}
        </td>
        <td width="50"><a href="index.php?page=world&op=edit&itemid={$entry.id}">EDIT</a></td>
        <td width="50"><a href="index.php?page=world&op=delete&itemid={$entry.id}">DELETE</a></td>
    </tr>
    {/foreach}
</table>
<a href="index.php?page=world&op=new">New World</a>