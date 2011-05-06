{if $pId}
<form action="index.php?page=player&amp;op=edit&amp;itemid={$pId}" method="post">
{else}
    <form action="index.php?page=player&amp;op=new" method="post">
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
                <td width="150">Player Name:</td>
                <td><input name="pName" type="text" value="{$pName}" /></td>
            </tr>
            <tr>
                <td width="150">Player Group:</td>
                <td>
                    <select name="pGroup">
                        <option value="null">No Group</option>

               {foreach name=aussen item=entry from=$groupArr}
                        <option value="{$entry.id};{$entry.wID}"
                            {if $entry.id == $pGroup}
                                selected="selected"
                            {/if}
                                >{$entry.group}({$entry.world})</option>
               {/foreach}
                    </select>
                </td>
            </tr>
            <tr>
                <td width="150"><input type="hidden" value="{$pId}" name="pId" /></td>
                <td><input type="submit" name="submit" value="Save" /></td>
            </tr>
        </table>
    </form>