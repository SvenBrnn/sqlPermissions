{if $gId}
<form action="index.php?page=group&amp;op=edit&amp;itemid={$gId}" method="post">
{else}
    <form action="index.php?page=group&amp;op=new" method="post">
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
                <td width="150">Group Name:</td>
                <td><input name="gName" type="text" value="{$gName}" /></td>
            </tr>
            <tr>
                <td width="150">Group Suffix:</td>
                <td><input name="gSufix" type="text" value="{$gSufix}" /></td>
            </tr>
            <tr>
                <td width="150">Group Prefix:</td>
                <td><input name="gPrefix" type="text" value="{$gPrefix}" /></td>
            </tr>
                        <tr>
                <td width="150">Group Default:</td>
                <td>
                    <select name="gDefault">
                        <option{if $gDefault == 'true'} selected{/if}>true</option>
                        <option{if $gDefault == 'false'} selected{/if}>false</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="150">Group can Build:</td>
                <td>
                    <select name="gBuild">
                        <option{if $gBuild == 'true'} selected{/if}>true</option>
                        <option{if $gBuild == 'false'} selected{/if}>false</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td width="150">Group World:</td>
                <td>
                    <select name="gWorld">
                        <option value="">Select World</option>
                        {foreach name=aussen item=entry from=$worldArr}
                        <option value="{$entry.id}"{if $gWorld == $entry.id} selected="selected"{/if}>{$entry.name}</option>
                        {/foreach}
                    </select>
                </td>
            </tr>
            <tr>
                <td width="150">Group Instances:</td>
                <td>
                    <select name="gInstance[]" multiple>
                        {foreach name=aussen item=entry from=$instArr}
                        <option value="{$entry.gId}"{if $entry.gSel} selected="selected"{/if}>{$entry.gName}</option>
                        {/foreach}
                    </select>
                </td>
            </tr>
            <tr>
                <td width="150"><input type="hidden" value="{$gId}" name="gId" /></td>
                <td><input type="submit" name="submit" value="Save" /></td>
            </tr>
        </table>
    </form>