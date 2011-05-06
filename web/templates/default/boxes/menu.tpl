{foreach name=aussen item=entry from=$menuArr}
<a href="index.php?page={$entry.link}">{$entry.name}</a><br/>
{/foreach}