<?php

function renderContent() {
    /* @var $db sqlDatabase */
    global $db;
    
    $s = new Smarty();
    $tpl = $s->fetch(TEMPLATE_DIR . DS . TEMPLATE . DS . CONTENT_DIR . DS . 'home.tpl');
    return $tpl;
}

?>