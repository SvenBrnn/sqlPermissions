<?php

function renderContent() {
    $op = "";
    if (isset($_GET['op']))
        $op = $_GET['op'];
    switch ($op) {
        case 'delete':
            if (delete_world () == -1)
                $tpl = renderStart();
            break;
        case 'new':
        case 'edit':
            $tpl = edit_world();
            break;
        default:
            $tpl = renderStart();
            break;
    }
    return $tpl;
}

function edit_world() {
    $s = new Smarty();
    /* @var $db sqlDatabase */
    global $db;
    $error = "";
    if (isset($_POST['submit'])) {
        if (isset($_POST['wId']) && $_POST['wId'] != "") {
            if (!isset($_POST['wName']) || $_POST['wName'] == "")
                $error .= "Please set a Correct World Name!";

            if ($error == "") {
                $db->query("UPDATE perm_worlds SET world='" . $db->cleanStatement($_POST['wName']) . "', copies='" . $db->cleanStatement($_POST['wCopy']) . "' WHERE id='" . $db->cleanStatement($_POST['wId']) . "'");
                if ($db->getError())
                    $error .= $db->getError();
                setDBChanged();
            }
        } else {
            if (!isset($_POST['wName']) || $_POST['wName'] == "")
                $error .= "Please set a Correct World Name!";

            if ($error == "") {
                $db->query("SELECT * FROM perm_worlds WHERE world='".$db->cleanStatement($_POST['wName'])."'");
                if($db->getNumRows() != null)
                     $error .= $_POST['wName']." already Exists!";
                if ($error == ""){
                $db->query("INSERT INTO perm_worlds(world, copies, system) VALUES('" . $db->cleanStatement($_POST['wName']) . "','" . $db->cleanStatement($_POST['wCopy']) . "','default')");
                if ($db->getError())
                    $error .= $db->getError();
                }
                setDBChanged();
            }
        }
        if ($error == "") {
            header("Location: index.php?page=world");
            exit;
        }
    }

    $s = new Smarty();
    if (isset($_GET['itemid'])&&$error == "") {
        $db->query("SELECT * FROM perm_worlds WHERE id='" . $db->cleanStatement($_GET['itemid']) . "'");
        $world = $db->fetchObject();
        $s->assign('wId', $world->id);
        $s->assign('wName', $world->world);
        $s->assign('wCopy', $world->copies);
    } else if ($error == "") {
        $s->assign('wId', '');
        $s->assign('wName', '');
        $s->assign('wCopy', '');
    } else {
        $s->assign('wId', $_POST['wId']);
        $s->assign('wName', $_POST['wName']);
        $s->assign('wCopy', $_POST['wCopy']);
    }
    $s->assign('errors', $error);
    $db->query("SELECT world FROM perm_worlds");
    $s->assign('worldArr', $db->fetchArrayList());
    $tpl = $s->fetch(TEMPLATE_DIR . DS . TEMPLATE . DS . CONTENT_DIR . DS . 'world_edit.tpl');
    return $tpl;
}

function renderStart($error = "") {
    /* @var $db sqlDatabase */
    global $db;
    $s = new Smarty();
    $db->query("SELECT * FROM perm_worlds");
    $worldArr = $db->fetchArrayList();
    $s->assign('worldArr', $worldArr);
    $tpl = $s->fetch(TEMPLATE_DIR . DS . TEMPLATE . DS . CONTENT_DIR . DS . 'world.tpl');
    return $tpl;
}

function delete_world() {
    global $db;
    $wID = $_GET['itemid'];

    if (!isset($wID))
        return -1;

    $wID = $db->cleanStatement($wID);

    $db->query("DELETE FROM perm_groups WHERE world='" . $wID . "'");
    $db->query("DELETE FROM perm_users WHERE world='" . $wID . "'");
    $db->query("DELETE FROM perm_worlds WHERE id='" . $wID . "'");
    setDBChanged();

    header("Location: index.php?page=world");
    exit;
}

?>