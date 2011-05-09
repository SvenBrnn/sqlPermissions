<?php

function renderContent() {
    $op = "";
    if (isset($_GET['op']))
        $op = $_GET['op'];
    switch ($op) {
        case 'delete':
            if (delete_group () == -1)
                $tpl = renderStart();
            break;
        case 'new':
        case 'edit':
            $tpl = edit_group();
            break;
        case 'editperm':
            if (($tpl = edit_group_perm()) == -1)
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
    $db->query("SELECT g.id, g.name as 'group', w.world FROM perm_groups g, perm_worlds w WHERE g.world=w.id ORDER BY w.id, g.id");
    $grpArr = $db->fetchArrayList();
    $s->assign('grpArr', $grpArr);
    $tpl = $s->fetch(TEMPLATE_DIR . DS . TEMPLATE . DS . CONTENT_DIR . DS . 'group.tpl');
    return $tpl;
}

function delete_group() {
    /* @var $db sqlDatabase */
    global $db;
    $gID = $_GET['itemid'];

    if (!isset($gID))
        return -1;

    $gID = $db->cleanStatement($gID);
    $db->query("DELETE FROM perm_instances WHERE grpID='" . $gID . "'");
    $db->query("DELETE FROM perm_groups WHERE id='" . $gID . "'");
    setDBChanged();
    //exit($db->getError());
    header("Location: index.php?page=group");
    exit;
}

function edit_group_perm() {
    /* @var $db sqlDatabase */
    global $db;
    $gID = $_GET['itemid'];

    if (!isset($gID))
        return -1;

    $gID = $db->cleanStatement($gID);

    if (isset($_POST['submit'])) {
        $perms = $_POST['perms'];
        $grpID = $db->cleanStatement($_POST['pId']);
        $db->query("DELETE FROM perm_grp_to_perm WHERE grpID='" . $grpID . "'");
        foreach ($perms as $p) {
            $pe = $db->cleanStatement($p);
            $db->query("INSERT INTO perm_grp_to_perm(grpID, permID) VALUES('" . $grpID . "', '" . $pe . "')");
            echo $db->getError();
        }
        setDBChanged();
        header("Location: index.php?page=group");
        exit;
    }

    $db->query("SELECT * FROM perm_permissions");
    $allPerm = $db->fetchObjectList();
    echo $db->getError();

    $db->query("SELECT * FROM perm_grp_to_perm WHERE grpID='" . $gID . "'");
    $permUser = $db->fetchObjectList();
    echo $db->getError();

    //$qu = "SELECT gp.permID, g.name as 'group' FROM perm_groups g, perm_grp_to_perm gp WHERE g.id='" . $gID . "' AND g.id=gp.grpID";
    //$db->query($qu);
    $permGroup = array();
    //$permGroup[] = $db->fetchObjectList();
    //echo $db->getError();

    $grpAdded = true;
    $grpList = array();
    $grpList[0] = $gID;
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
    $s->assign("item", $gID);
    $tpl = $s->fetch(TEMPLATE_DIR . DS . TEMPLATE . DS . CONTENT_DIR . DS . 'group_perm.tpl');
    return $tpl;
}

function edit_group() {
    $s = new Smarty();
    /* @var $db sqlDatabase */
    global $db;
    $error = "";
    if (isset($_POST['submit'])) {
        if (isset($_POST['gId']) && $_POST['gId'] != "") {
            if (!isset($_POST['gName']) || $_POST['gName'] == "")
                $error = "Set a Correct Name!";
            if (!isset($_POST['gWorld']) || $_POST['gWorld'] == "")
                $error = "Set a Correct World!";

            if ($error == "") {
                $qu = "UPDATE perm_groups SET ";
                $qu .= "name='" . $db->cleanStatement($_POST['gName']) . "', ";
                $qu .= "def='" . $db->cleanStatement($_POST['gDefault']) . "', ";
                $qu .= "sufix='" . $db->cleanStatement($_POST['gSufix']) . "', ";
                $qu .= "prefix='" . $db->cleanStatement($_POST['gPrefix']) . "', ";
                $qu .= "build='" . $db->cleanStatement($_POST['gBuild']) . "', ";
                $qu .= "world='" . $db->cleanStatement($_POST['gWorld']) . "' ";
                if ($db->cleanStatement($_POST['gRank']) != "")
                    $qu .= ",rank='" . $db->cleanStatement($_POST['gRank']) . "' ";
                $qu .= "WHERE id='" . $db->cleanStatement($_POST['gId']) . "'";
                $db->query($qu);
                $error .= $db->getError();
                $db->query("DELETE FROM perm_instances WHERE grpID='" . $db->cleanStatement($_POST['gId']) . "'");
                $error .= $db->getError();
                if (isset($_POST['gInstance'])) {
                    foreach ($_POST['gInstance'] as $in) {
                        $insta = $db->cleanStatement($in);
                        $db->query("INSERT INTO perm_instances(grpID, refTo) VALUES('" . $db->cleanStatement($_POST['gId']) . "', '" . $insta . "')");
                    }
                }
                setDBChanged();
            }
        } else {
            if (!isset($_POST['gName']) || $_POST['gName'] == "")
                $error = "Set a Correct Name!";
            if (!isset($_POST['gWorld']) || $_POST['gWorld'] == "")
                $error = "Set a Correct World!";

            if ($error == "") {
                $db->query("SELECT * FROM perm_groups WHERE name='" . $db->cleanStatement($_POST['gName']) . "' AND world='" . $db->cleanStatement($_POST['gWorld']) . "'");
                if ($db->getNumRows() != null)
                    $error = $_POST['gName'] . " already exists in " . $_POST['gWorld'] . "!";
            }
            if ($error == "") {
                if ($db->cleanStatement($_POST['gRank']) != "")
                    $qu = "INSERT INTO perm_groups(name, def, sufix, prefix, build, rank, world) VALUES( ";
                else
                    $qu = "INSERT INTO perm_groups(name, def, sufix, prefix, build, world) VALUES( ";
                $qu .= "'" . $db->cleanStatement($_POST['gName']) . "', ";
                $qu .= "'" . $db->cleanStatement($_POST['gDefault']) . "', ";
                $qu .= "'" . $db->cleanStatement($_POST['gSufix']) . "', ";
                $qu .= "'" . $db->cleanStatement($_POST['gPrefix']) . "', ";
                $qu .= "'" . $db->cleanStatement($_POST['gBuild']) . "', ";
                if ($db->cleanStatement($_POST['gRank']) != "")
                    $qu .= "'" . $db->cleanStatement($_POST['gRank']) . "',";
                $qu .= "'" . $db->cleanStatement($_POST['gWorld']) . "')";
                //$error = $qu;
                $db->query($qu);
                $error .= $db->getError();
                if ($error == "") {
                    $db->query("SELECT id FROM perm_groups WHERE name='" . $db->cleanStatement($_POST['gName']) . "' AND world='" . $db->cleanStatement($_POST['gWorld']) . "'");
                    $groID = $db->fetchObject()->id;
                    $error .= $db->getError();
                    if ($error == "") {
                        $db->query("DELETE FROM perm_instances WHERE grpID='" . $groID . "'");
                        $error .= $db->getError();
                        if (isset($_POST['gInstance']) && $error == "") {
                            foreach ($_POST['gInstance'] as $in) {
                                $insta = $db->cleanStatement($in);
                                $db->query("INSERT INTO perm_instances(grpID, refTo) VALUES('" . $groID . "', '" . $insta . "')");
                                $error .= $db->getError();
                            }
                        }
                    }
                }
                setDBChanged();
            }
        }
        if ($error == "") {
            header("Location: index.php?page=group");
            exit;
        }
    }

    $s = new Smarty();
    $db->query("SELECT * FROM perm_groups");
    $grps = $db->fetchObjectList();

    $instArr = array();
    if (isset($_GET['itemid']) && $error == "") {
        $db->query("SELECT * FROM perm_groups WHERE id='" . $db->cleanStatement($_GET['itemid']) . "'");
        $res = $db->fetchObject();
        $s->assign('gId', $res->id);
        $s->assign('gName', $res->name);

        if ($res->prefix == 'null')
            $s->assign('gPrefix', '');
        else
            $s->assign('gPrefix', $res->prefix);

        if ($res->sufix == 'null')
            $s->assign('gSufix', '');
        else
            $s->assign('gSufix', $res->sufix);

        $s->assign('gWorld', $res->world);
        $s->assign('gDefault', $res->def);
        $s->assign('gBuild', $res->build);
        $s->assign('gRank', $res->rank);

        $db->query("SELECT * FROM perm_instances WHERE grpID='" . $db->cleanStatement($_GET['itemid']) . "'");
        $inst = $db->fetchObjectList();
        for ($i = 0; $i < count($grps); $i++) {
            $instArr[$i] = array(
                'gName' => $grps[$i]->name,
                'gId' => $grps[$i]->id,
                'gSel' => false
            );
            for ($j = 0; $j < count($inst); $j++) {
                if ($inst[$j]->refTo == $instArr[$i]['gId']) {
                    $instArr[$i]['gSel'] = true;
                    break;
                }
            }
        }
    } else if ($error == "") {
        $s->assign('gId', '');
        $s->assign('gName', '');
        $s->assign('gPrefix', '');
        $s->assign('gSufix', '');
        $s->assign('gWorld', '');
        $s->assign('gDefault', 'false');
        $s->assign('gBuild', 'false');
        $s->assign('gRank', '');
        for ($i = 0; $i < count($grps); $i++) {
            $instArr[$i] = array(
                'gName' => $grps[$i]->name,
                'gId' => $grps[$i]->id,
                'gSel' => false
            );
        }
    } else {
        $s->assign('gId', $_POST['gId']);
        $s->assign('gName', $_POST['gName']);
        $s->assign('gPrefix', $_POST['gPrefix']);
        $s->assign('gSufix', $_POST['gSufix']);
        $s->assign('gWorld', $_POST['gWorld']);
        $s->assign('gDefault', $_POST['gDefault']);
        $s->assign('gBuild', $_POST['gBuild']);
        $s->assign('gRank', $_POST['gRank']);
        for ($i = 0; $i < count($grps); $i++) {
            $instArr[$i] = array(
                'gName' => $grps[$i]->name,
                'gId' => $grps[$i]->id,
                'gSel' => false
            );
            if (isset($_POST['gInstance'])) {
                for ($j = 0; $j < count($_POST['gInstance']); $j++) {
                    if ($_POST['gInstance'][$j] == $instArr[$i]['gId']) {
                        $instArr[$i]['gSel'] = true;
                        break;
                    }
                }
            }
        }
    }
    $db->query("SELECT id, world as 'name' FROM perm_worlds");
    $s->assign('worldArr', $db->fetchArrayList());
    $s->assign('instArr', $instArr);
    $s->assign('errors', $error);
    $tpl = $s->fetch(TEMPLATE_DIR . DS . TEMPLATE . DS . CONTENT_DIR . DS . 'group_edit.tpl');
    return $tpl;
}

?>