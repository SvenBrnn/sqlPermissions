<?php

function renderContent() {
    $operation = "";
    $tpl = "";
    if (isset($_GET['op']))
        $operation = $_GET['op'];
    switch ($operation) {
        case 'delete':
            if (delete_webuser() == -1)
                $tpl = renderStart();
            break;
        case 'edit':
        case 'new':
            $tpl = edit_webuser();
            break;
        case '':
        default:
            $tpl = renderStart();
            break;
    }

    return $tpl;
}

function renderStart() {
    /* @var $db sqlDatabase */
    global $db;
    $s = new Smarty();
    $db->query("SELECT * FROM perm_webusers");
    $permUsers = $db->fetchArrayList();
    $s->assign('permUsers', $permUsers);
    $tpl = $s->fetch(TEMPLATE_DIR . DS . TEMPLATE . DS . CONTENT_DIR . DS . 'webusers.tpl');
    return $tpl;
}

function delete_webuser() {
    //$s = new Smarty();
    if (!isset($_GET['itemid']))
        return -1;

    /* @var $db sqlDatabase */
    global $db;
    $item = $_GET['itemid'];
    $db->query("DELETE FROM perm_webusers WHERE id='" . $db->cleanStatement($item) . "'");
}

function edit_webuser() {
    $s = new Smarty();
    /* @var $db sqlDatabase */
    global $db;
    $error = "";
    if (isset($_POST['submit'])) {
        if (isset($_POST['uId']) && $_POST['uId'] != "") {
            if (!isset($_POST['uName']) || $_POST['uName'] == "")
                $error .= "Please set a Correct World Name!";
            if (!isset($_POST['uPass']) || $_POST['uPass'] == "")
                $error .= "You have to set a Password!";

            if ($error == "") {
                $db->query("UPDATE perm_webusers SET username='" . $db->cleanStatement($_POST['uName']) . "', password=md5('" . $db->cleanStatement($_POST['uPass']) . "') WHERE id='" . $db->cleanStatement($_POST['uId']) . "'");
                if ($db->getError())
                    $error .= $db->getError();
            }
        } else {
            if (!isset($_POST['uName']) || $_POST['uName'] == "")
                $error .= "Please set a Correct World Name!";
            if (!isset($_POST['uPass']) || $_POST['uPass'] == "")
                $error .= "You have to set a Password!";

            if ($error == "") {
                $db->query("SELECT * FROM perm_webusers WHERE username='" . $db->cleanStatement($_POST['uName']) . "'");
                if ($db->getNumRows() != null)
                    $error .= $_POST['uName'] . " already Exists!";
                if ($error == "") {
                    $db->query("INSERT INTO perm_webusers(username, password) VALUES('" . $db->cleanStatement($_POST['uName']) . "',md5('" . $db->cleanStatement($_POST['uPass']) . "'))");
                    if ($db->getError())
                        $error .= $db->getError();
                }
            }
        }
        if ($error == "") {
            header("Location: index.php?page=webusers");
            exit;
        }
    }

    $s = new Smarty();
    if (isset($_GET['itemid']) && $error == "") {
        $db->query("SELECT * FROM perm_webusers WHERE id='" . $db->cleanStatement($_GET['itemid']) . "'");
        $user = $db->fetchObject();
        $s->assign('uId', $user->id);
        $s->assign('uName', $user->username);
        $s->assign('uPass', '');
    } else if ($error == "") {
        $s->assign('uId', '');
        $s->assign('uName', '');
        $s->assign('uPass', '');
    } else {
        $s->assign('uId', $_POST['uId']);
        $s->assign('uName', $_POST['uName']);
        $s->assign('uPass', $_POST['uPass']);
    }
    $s->assign('errors', $error);
    $tpl = $s->fetch(TEMPLATE_DIR . DS . TEMPLATE . DS . CONTENT_DIR . DS . 'webusers_edit.tpl');
    return $tpl;
}

?>