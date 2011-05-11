<?php

function renderContent() {
    $op = "";
    $tpl = "";
    if (isset($_GET['op']))
        $op = $_GET['op'];
    switch ($op) {
        case 'delete':
            if (delete_player () == -1)
                $tpl = renderStart();
            break;
        case 'new':
        case 'edit':
            $tpl = edit_player();
            break;
        case 'editperm':
            if (($tpl = edit_player_perm()) == -1)
                $tpl = renderStart();
            break;
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
    $db->query("SELECT u.id, u.login, w.world, g.name as 'group' FROM perm_users u, perm_groups g, perm_worlds w WHERE u.grp=g.id AND u.world=w.id ORDER BY w.id, g.id");
    $usrArr = $db->fetchArrayList();
    $s->assign('userArr', $usrArr);
    $tpl = $s->fetch(TEMPLATE_DIR . DS . TEMPLATE . DS . CONTENT_DIR . DS . 'player.tpl');
    return $tpl;
}

function delete_player() {
    /* @var $db sqlDatabase */
    global $db;
    $pID = $_GET['itemid'];

    if (!isset($pID))
        return -1;

    $pID = $db->cleanStatement($pID);
    $db->query("DELETE FROM perm_user_to_perm WHERE usrID='" . $pID . "'");
    $db->query("DELETE FROM perm_users WHERE id='" . $pID . "'");
    setDBChanged();
    //exit($db->getError());
    header("Location: index.php?page=player");
    exit;
}

function edit_player_perm() {
    /* @var $db sqlDatabase */
    global $db;
    $pID = $_GET['itemid'];

    if (!isset($pID))
        return -1;

    $pID = $db->cleanStatement($pID);

    if (isset($_POST['submit'])) {
        $perms = $_POST['perms'];
        $userID = $db->cleanStatement($_POST['pId']);
        $db->query("DELETE FROM perm_user_to_perm WHERE usrID='" . $userID . "'");
        foreach ($perms as $p) {
            $pe = $db->cleanStatement($p);
            $db->query("INSERT INTO perm_user_to_perm(usrID, permID) VALUES('" . $userID . "', '" . $pe . "')");
            echo $db->getError();
            setDBChanged();
        }
        header("Location: index.php?page=player");
        exit;
    }

    $db->query("SELECT * FROM perm_permissions");
    $allPerm = $db->fetchObjectList();
    echo $db->getError();

    $db->query("SELECT * FROM perm_user_to_perm WHERE usrID='" . $pID . "'");
    $permUser = $db->fetchObjectList();
    echo $db->getError();

    $qu = "SELECT gp.permID, g.name as 'group' FROM perm_users u, perm_groups g, perm_grp_to_perm gp WHERE u.id='" . $pID . "' AND u.grp=g.id AND g.id=gp.grpID";
    $db->query($qu);
    $permGroup = array();
    $permGroup[] = $db->fetchObjectList();
    echo $db->getError();

    $db->query("SELECT grp FROM perm_users WHERE id='" . $pID . "'");
    $ob = $db->fetchObject();

    $grpAdded = true;
    $grpList = array();
    $grpList[0] = $ob->grp;
    $x = 1;
    do {
        $grpAdded = false;
        for ($i = 0; $i < count($grpList); $i++) {
            $db->query("SELECT refTo FROM perm_instances WHERE grpID='" . $grpList[$i] . "'");
            $obList = $db->fetchObjectList();
            foreach ($obList as $o) {
                if (!in_array($o->refTo, $grpList)) {
                    $grpList[] = $o->refTo;
                    $qu = "SELECT gp.permID, g.name as 'group' FROM perm_groups g, perm_grp_to_perm gp WHERE g.id='" . $o->refTo . "' AND g.id=gp.grpID";
                    $db->query($qu);
                    $permGroup[$x] = $db->fetchObjectList();
                    $grpAdded = true;
                    $x++;
                }
            }
        }
    } while ($grpAdded);

    $userPerms = array();
    for ($i = 0; $i < count($allPerm); $i++) {
        $userPerms[$i] = array(
            'pId' => $allPerm[$i]->id,
            'pName' => $allPerm[$i]->name,
            'pDesc' => $allPerm[$i]->description,
            'pHas' => false,
            'pHasFromGrp' => false,
            'pHasFromGrpName' => ""
        );
        for ($j = 0; $j < count($permUser); $j++) {
            if ($userPerms[$i]['pId'] == $permUser[$j]->permID) {
                $userPerms[$i]['pHas'] = true;
                break;
            }
        }
        foreach ($permGroup as $permGr) {
            $br = false;
            for ($j = 0; $j < count($permGr); $j++) {
                if ($userPerms[$i]['pId'] == $permGr[$j]->permID) {
                    $userPerms[$i]['pHasFromGrp'] = true;
                    $userPerms[$i]['pHasFromGrpName'] = $permGr[$j]->group;
                    $br = true;
                    break;
                }
                if ($br)
                    break;
            }
        }
    }
    $s = new Smarty();
    $s->assign("perms", $userPerms);
    $s->assign("item", $pID);
    $tpl = $s->fetch(TEMPLATE_DIR . DS . TEMPLATE . DS . CONTENT_DIR . DS . 'player_perm.tpl');
    return $tpl;
}

function edit_player() {
    $s = new Smarty();
    /* @var $db sqlDatabase */
    global $db;
    $error = "";
    if (isset($_POST['submit'])) {
        if (isset($_POST['pId']) && $_POST['pId'] != "") {
            if (!isset($_POST['pName']) || $_POST['pName'] == "")
                $error .= "Please set a Correct Player Name!";
            if (!isset($_POST['pGroup']) || $_POST['pGroup'] == "null" || $_POST['pGroup'] == "")
                $error .= "Please set a Correct Group!";

            if ($error == "") {
                $split = explode(";", $db->cleanStatement($_POST['pGroup']));
                $db->query("UPDATE perm_users SET login='" . $db->cleanStatement($_POST['pName']) . "', world='" . $split[1] . "', grp='" . $split[0] . "' WHERE id='" . $db->cleanStatement($_POST['pId']) . "'");
                if ($db->getError())
                    $error .= $db->getError();
                setDBChanged();
            }
        } else {
            if (!isset($_POST['pName']) || $_POST['pName'] == "")
                $error .= "Please set a Correct Player Name!";
            if (!isset($_POST['pGroup']) || $_POST['pGroup'] == "null" || $_POST['pGroup'] == "")
                $error .= "Please set a Correct Group!";

            if ($error == "") {
                $db->query("SELECT * FROM perm_users WHERE login='" . $db->cleanStatement($_POST['pName']) . "'");
                if ($db->getNumRows() != null)
                    $error .= $_POST['pName'] . " already Exists!";
                if ($error == "") {
                    $split = explode(";", $db->cleanStatement($_POST['pGroup']));
                    $db->query("INSERT INTO perm_users(login, world, grp) VALUES('" . $db->cleanStatement($_POST['pName']) . "','" . $db->cleanStatement($split[1]) . "','" . $db->cleanStatement($split[0]) . "')");
                    if ($db->getError())
                        $error .= $db->getError();
                    setDBChanged();
                }
            }
        }
        if ($error == "") {
            header("Location: index.php?page=player");
            exit;
        }
    }

    $s = new Smarty();
    if (isset($_GET['itemid']) && $error == "") {
        $db->query("SELECT * FROM perm_users WHERE id='" . $db->cleanStatement($_GET['itemid']) . "'");
        $res = $db->fetchObject();
        $s->assign('pId', $res->id);
        $s->assign('pGroup', $res->grp);
        $s->assign('pWorld', $res->world);
        $s->assign('pName', $res->login);
    } else if ($error == "") {
        $s->assign('pId', '');
        $s->assign('pGroup', '');
        $s->assign('pWorld', '');
        $s->assign('pName', '');
    } else {
        $split = explode(";", $_POST['pGroup']);
        $s->assign('pId', $_POST['pId']);
        if ($_POST['pGroup'] == "null" || $_POST['pGroup'] == "" || !isset($_POST['pGroup'])) {
            $s->assign('pGroup', '');
            $s->assign('pWorld', '');
        } else {
            $s->assign('pGroup', $split[0]);
            $s->assign('pWorld', $split[1]);
        }
        $s->assign('pName', $_POST['pName']);
    }
    $s->assign('errors', $error);
    $db->query("SELECT g.id, w.world, g.world as 'wID', g.name as 'group' FROM perm_groups g, perm_worlds w WHERE g.world=w.id");
    $s->assign('groupArr', $db->fetchArrayList());
    $tpl = $s->fetch(TEMPLATE_DIR . DS . TEMPLATE . DS . CONTENT_DIR . DS . 'player_edit.tpl');
    return $tpl;
}

?>