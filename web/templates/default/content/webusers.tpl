<table width="100%" cellpadding="0" cellspacing="0" border="1">
    <tr>
        <td width="50">User ID</td>
        <td>Username</td>
        <td width="50">&nbsp;</td>
        <td width="50">&nbsp;</td>
    </tr>
    {foreach name=aussen item=entry from=$permUsers}
    <tr>
        <td width="50">{$entry.id}</td>
        <td>
            {$entry.username}
        </td>
        <td width="50"><a href="index.php?page=webusers&op=edit&itemid={$entry.id}">EDIT</a></td>
        <td width="50"><a href="index.php?page=webusers&op=delete&itemid={$entry.id}">DELETE</a></td>
    </tr>
    {/foreach}
</table>
<a href="index.php?page=webusers&op=new">New Webuser</a>