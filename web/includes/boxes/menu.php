<?php

function rendermenuBox() {
    $s = new Smarty();
    $menuArr = array();
    $menuArr[] = array('name' => 'Home', 'link' => 'home');
    $menuArr[] = array('name' => 'Groups', 'link' => 'group');
    $menuArr[] = array('name' => 'Players', 'link' => 'player');
    $menuArr[] = array('name' => 'Permissions', 'link' => 'permission');
    $menuArr[] = array('name' => 'Worlds', 'link' => 'world');
    $menuArr[] = array('name' => 'Webusers', 'link' => 'webusers');
    $menuArr[] = array('name' => 'Logout', 'link' => 'logout');
    $s->assign('menuArr', $menuArr);
    $tpl = $s->fetch(TEMPLATE_DIR . DS . TEMPLATE . DS . BOX_DIR . DS . 'menu.tpl');
    return $tpl;
}

?>