<form action="index.php?page=player&op=editperm&itemid={$item}" method="post">
    <table width="100%" cellpadding="0" cellspacing="0" border="1">
        <tr>
            <td width="300">Permission Name</td>
            <td>Permission Description</td>
            <td width="50">HasPerm</td>
        </tr>
    {foreach name=aussen item=entry from=$perms}
        <tr>
            <td width="200">
            {if $entry.pHasFromGrp}
                <div class="hasPerm">{$entry.pName} from {$entry.pHasFromGrpName}</div>
            {else}
                <div>{$entry.pName}</div>
            {/if}
            </td>
            <td>
            {if $entry.pDesc}
                {$entry.pDesc}
            {else}
                &nbsp;
            {/if}
            </td>
            <td width="50"><input type="checkbox" name="perms[]" value="{$entry.pId}" {if $entry.pHas}checked{/if} /></td>
        </tr>
    {/foreach}
    </table>
    <input type="hidden" name="pId" value="{$item}" />
    <input type="submit" name="submit" value="Save" />
</form>