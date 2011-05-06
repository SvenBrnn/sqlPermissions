{if $uId}
<form action="index.php?page=webusers&amp;op=edit&amp;itemid={$uId}" method="post">
{else}
    <form action="index.php?page=webusers&amp;op=new" method="post">
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
                <td><input name="uName" type="text" value="{$uName}" /></td>
            </tr>
            <tr>
                <td width="150">Player Password:</td>
                <td><input name="uPass" type="password" value="{$uPass}" /></td>
            </tr>
            <tr>
                <td width="150"><input type="hidden" value="{$uId}" name="uId" /></td>
                <td><input type="submit" name="submit" value="Save" /></td>
            </tr>
        </table>
    </form>