{if $wId}
<form action="index.php?page=world&amp;op=edit&amp;itemid={$wId}" method="post">
{else}
<form action="index.php?page=world&amp;op=new" method="post">
{/if}
<table width="100%" cellpadding="0" cellspacing="0" border="1">
    {if $errors != ""}
    <tr>
        <td colspan="2" class="error">
            <pre>{$errors}</pre>
        </td>
    </tr>
    {/if}
    <tr>
        <td width="150">World Name:</td>
        <td><input name="wName" type="text" value="{$wName}" /></td>
    </tr>
    <tr>
        <td width="150">World Copies:</td>
        <td>
            <select name="wCopy">
                <option value="null">No Copy</option>
               {foreach name=aussen item=entry from=$worldArr}
                   {if $entry.world != $wName}
                            <option value="{$entry.world}"
                            {if $entry.world == $wCopy}
                            selected
                            {/if}
                            >{$entry.world}</option>
                {/if}
               {/foreach}
            </select>
        </td>
    </tr>
    <tr>
        <td width="150"><input type="hidden" value="{$wId}" name="wId" /></td>
        <td><input type="submit" name="submit" value="Save" /></td>
    </tr>
</table>
</form>