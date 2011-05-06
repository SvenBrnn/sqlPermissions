<?php
require('libs/Smarty.class.php');
require_once("includes/config.php");
require_once("includes/defines.php");
require('includes/sqlDatabase.php');

global $smarty;
global $db;

include ('includes/globalFunctions.php');
initSession();

$smarty = new Smarty;
$db = new sqlDatabase(SQL_HOST, SQL_USER, SQL_PASS, SQL_DATABASE);

//$smarty->force_compile = true;
//$smarty->debugging = true;
$smarty->caching = false;
$smarty->cache_lifetime = 120;
$error = "";
if(isset($_POST['submit']))
{
    $user = $db->cleanStatement($_POST['username']);
    $pass = $db->cleanStatement($_POST['password']);
    $query = "SELECT * FROM perm_webusers WHERE username='".$user."' AND password=md5('".$pass."')";
    $db->query($query);
    echo $query;
    if($db->getNumRows() > 0)
    {
        $res = $db->fetchObject();
        $_SESSION['userID'] = $res->id;
        header("Location: index.php");
        exit;
    }
}

$smarty->assign('tmpl_path', TEMPLATE_DIR . "/" . TEMPLATE);
$smarty->display(TEMPLATE_DIR . DS . TEMPLATE . DS . 'login.tpl');
?>