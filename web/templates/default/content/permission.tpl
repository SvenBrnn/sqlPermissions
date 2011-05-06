<table width="100%" cellpadding="0" cellspacing="0" border="1">
    <tr>
        <td width="200">Permission Name</td>
        <td>Permission Description</td>
        <td width="50">&nbsp;</td>
        <td width="50">&nbsp;</td>
    </tr>
    {foreach name=aussen item=entry from=$permArr}
    <tr>
        <td width="200">{$entry.name}</td>
        <td>
            {if $entry.description}
                {$entry.description}
            {else}
                &nbsp;
            {/if}
        </td>
        <td width="50"><a href="index.php?page=permission&op=edit&itemid={$entry.id}">EDIT</a></td>
        <td width="50"><a href="index.php?page=permission&op=delete&itemid={$entry.id}">DELETE</a></td>
    </tr>
    {/foreach}
</table>
<a href="index.php?page=permission&op=new">New Permission</a>