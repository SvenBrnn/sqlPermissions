{if $pId}
<form action="index.php?page=permission&amp;op=edit&amp;itemid={$pId}" method="post">
{else}
<form action="index.php?page=permission&amp;op=new" method="post">
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
        <td width="150">Permission Name:</td>
        <td><input name="pName" type="text" value="{$pName}" /></td>
    </tr>
    <tr>
        <td width="150">Permission Description:</td>
        <td><input name="pDesc" type="text" value="{$pDesc}" /></td>
    </tr>
    <tr>
        <td width="150"><input type="hidden" value="{$pId}" name="pId" /></td>
        <td><input type="submit" name="submit" value="Save" /></td>
    </tr>
</table>
</form>