<?php

require('libs/Smarty.class.php');
require_once("includes/config.php");
require_once("includes/defines.php");
require('includes/sqlDatabase.php');

global $smarty;
global $db;

include ('includes/globalFunctions.php');
initSession();

if(!isset($_SESSION['userID']) || $_SESSION['userID']=="")
{
    header("Location: login.php");
    exit;
}

$smarty = new Smarty;
$db = new sqlDatabase(SQL_HOST, SQL_USER, SQL_PASS, SQL_DATABASE);

function endsWith( $str, $sub ) {
return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
}

//$smarty->force_compile = true;
//$smarty->debugging = true;
$smarty->caching = false;
$smarty->cache_lifetime = 120;

//Include all Boxes
$incdir = 'includes/boxes/';
$files = scandir($incdir);
foreach($files as $f)
{
    if(endsWith($f, '.php'))
    {
        include ($incdir.$f);
        $name = substr($f, 0, strlen($f) - 4);
        $ev = eval("\$box = render".$name."Box();");
        $smarty->assign('box_'.$name, $box);
    }
}

//Get Page
$page = "";
if(isset($_GET['page']))
    $page = $_GET['page'];
if($page != "" && file_exists('includes/content/'.$page.'.php'))
    include('includes/content/'.$page.'.php');
else
    include('includes/content/home.php');

$smarty->assign('content', renderContent());
$smarty->assign('tmpl_path', TEMPLATE_DIR . "/" . TEMPLATE);
$smarty->display(TEMPLATE_DIR . DS . TEMPLATE . DS . 'index.tpl');
?>
