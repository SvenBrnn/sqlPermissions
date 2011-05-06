<?php

function renderContent() {
    $operation = "";
    $tpl = "";
    if (isset($_GET['op']))
        $operation = $_GET['op'];
    switch ($operation) {
        case 'delete':
           if ( delete_perm() == -1)
                $tpl = renderStart();
            break;
        case 'edit':
        case 'new':
            $tpl = edit_perm();
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
    $db->query("SELECT * FROM perm_permissions");
    $permArr = $db->fetchArrayList();
    $s->assign('permArr', $permArr);
    $tpl = $s->fetch(TEMPLATE_DIR . DS . TEMPLATE . DS . CONTENT_DIR . DS . 'permission.tpl');
    return $tpl;
}

function delete_perm() {
    //$s = new Smarty();
    if (!isset($_GET['itemid']))
        return -1;

    /* @var $db sqlDatabase */
    global $db;
    $item = $_GET['itemid'];
    $db->query("DELETE FROM perm_grp_to_perm WHERE permID='" . $db->cleanStatement($item) . "'");
    $db->query("DELETE FROM perm_user_to_perm WHERE permID='" . $db->cleanStatement($item) . "'");
    $db->query("DELETE FROM perm_permissions WHERE id='" . $db->cleanStatement($item) . "'");
    setDBChanged();

    header("Location: index.php?page=permission");
    exit;
}

function edit_perm() {
    /* @var $db sqlDatabase */
    global $db;
    $s = new Smarty();

    $error = "";
    if (isset($_POST['submit'])) {
        if (isset($_POST['pId']) && $_POST['pId'] != "") {
            if (!isset($_POST['pName']) || $_POST['pName'] == "")
                $error .= "Please set a Correct Permission Name!";

            if ($error == "") {
                $db->query("UPDATE perm_permissions SET name='" . $db->cleanStatement($_POST['pName']) . "', description='" . $db->cleanStatement($_POST['pDesc']) . "' WHERE id='" . $db->cleanStatement($_POST['pId']) . "'");
                    setDBChanged();
                if ($db->getError())
                    $error .= $db->getError();
            }
        }
        else {
            if (!isset($_POST['pName']) || $_POST['pName'] == "")
                $error .= "Please set a correct Permission Name!";

            if ($error == "") {
                $db->query("SELECT * FROM perm_permissions WHERE name='" . $db->cleanStatement($_POST['pName']) . "'");
                    setDBChanged();
                if ($db->getNumRows() != null)
                    $error .= $_POST['pName'] . " already Exists!";
                if ($error == "") {
                    $db->query("INSERT INTO perm_permissions(name, description) VALUES('" . $db->cleanStatement($_POST['pName']) . "','" . $db->cleanStatement($_POST['pDesc']) . "')");
                    if ($db->getError())
                        $error .= $db->getError();
                }
            }
        }

        if ($error == "") {
            header("Location: index.php?page=permission");
            exit;
        }
    }

    $item = "";
    if ($_GET['op'] == 'new') {
        if (isset($_GET['itemid']))
            $item = $_GET['itemid'];
    }

    if ($item != "" && $error == "") {
        $db->query("SELECT * FROM perm_permissions WHERE id='" . $item . "'");
        $permArr = $db->fetchArray();
        $s->assign('pId', $permArr['id']);
        $s->assign('pName', $permArr['name']);
        $s->assign('pDesc', $permArr['description']);
    } else if ($error == "") {
        $s->assign('pId', '');
        $s->assign('pName', '');
        $s->assign('pDesc', '');
    } else {
        $s->assign('pId', $_POST['pId']);
        $s->assign('pName', $_POST['pName']);
        $s->assign('pDesc', $_POST['pDesc']);
    }
    $s->assign('errors', $error);
    //$s->assign('permArr', $permArr);

    $tpl = $s->fetch(TEMPLATE_DIR . DS . TEMPLATE . DS . CONTENT_DIR . DS . 'permission_edit.tpl');
    return $tpl;
}

?>