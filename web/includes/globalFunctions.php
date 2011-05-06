<?php

function setDBChanged() {
    /* @var $db sqlDatabase */
    global $db;
    $db->query("UPDATE perm_config SET VALUE=NOW() WHERE param='lastDBChange'");
}

function initSession() {

    if(!isset($_SESSION))
    {
        session_start();
    }
}

?>