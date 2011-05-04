/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package SvenBrnn.sqlPermissions;

import java.io.File;
import java.util.ArrayList;
import java.util.Iterator;
import java.util.List;
import org.bukkit.util.config.Configuration;

/**
 *
 * @author Sven
 */
public class sqlPermissionsPermisionEditor {

    private sqlPermissions plugin;

    sqlPermissionsPermisionEditor(sqlPermissions plugin) {
        this.plugin = plugin;
    }

    public void loadPermissionsToDatabase(String world) throws Exception {
        String worldFile = "plugins/Permissions/" + world + ".yml";
        File f = new File(worldFile);
        if (!f.exists()) {
            System.out.println("[sqlPermissions][DEBUG] plugins/Permissions/" + world + ".yml does not exist!");
            return;
        }

        Configuration cfg = new Configuration(f);
        cfg.load();
        String copys = (String) cfg.getProperty("plugin.permissions.copies");
        if (copys != null && !copys.equals("null") && !copys.equals("")) {
            System.out.println("[sqlPermissions][DEBUG] " + world + " Copies " + copys + "!");
            return;
        }

        int worldID = checkAndAddWorld(world, cfg);
        //System.out.println("[sqlPermissions][DEBUG] World is in DB now!");
        List groupList = cfg.getKeys("groups");
        Iterator groups = groupList.iterator();
        while (groups.hasNext()) {
            Object grp = (String) groups.next();
            int grpID;
            grpID = checkAndAddGroup((String) grp, worldID, cfg);
            if (grpID != -1) {
                List permList = cfg.getList("groups." + grp + ".permissions");
                Iterator perms = permList.iterator();
                while (perms.hasNext()) {
                    Object perm = (String) perms.next();
                    checkAndAddPerm((String) perm, grpID, cfg);
                }
                //System.out.println("[sqlPermissions][DEBUG] Group " + grp + " is in DB now!");
            }
        }

        List userList = cfg.getKeys("users");
        Iterator users = userList.iterator();
        while (users.hasNext()) {
            String usr = (String) users.next();
            int usrID = checkAndAddUser(usr, worldID, cfg);
            if (usrID != -1) {
                List permList = cfg.getList("users." + usr + ".permissions");
                if (permList != null) {
                    Iterator perms = permList.iterator();
                    while (perms.hasNext()) {
                        Object perm = (String) perms.next();
                        checkAndAddPermUsr((String) perm, usrID, cfg);
                    }
                }
            }
        }
    }

    private int checkAndAddGroup(String grp, int world, Configuration cfg) throws Exception {
        try {
            String query = "SELECT id FROM perm_groups WHERE name='" + grp + "' AND world='" + world + "'";
            String[][] res = plugin.database.executeQuery(query);
            if (res.length > 0) {
                return new Integer(res[0][0]);
            }

            Boolean defBool = (Boolean) cfg.getProperty("groups." + grp + ".default");
            String def;
            if (defBool) {
                def = "true";
            } else {
                def = "false";
            }

            String instance;
            Object ins = cfg.getProperty("groups." + grp + ".inheritance");
            String sufix = (String) cfg.getProperty("groups." + grp + ".info.sufix");
            String prefix = (String) cfg.getProperty("groups." + grp + ".info.prefix");
            Boolean buildBool = (Boolean) cfg.getProperty("groups." + grp + ".info.build");
            String build;
            if (buildBool) {
                build = "true";
            } else {
                build = "false";
            }

            query = "INSERT INTO perm_groups(name, world, def, prefix, sufix, build) VALUES('" + grp + "','" + world + "','" + def + "','" + prefix + "','" + sufix + "','" + build + "')";
            //System.out.println("[DEBUG] " + query);
            plugin.database.executeChangeQuery(query);

            query = "SELECT id FROM perm_groups WHERE name='" + grp + "' AND world='" + world + "'";
            //System.out.println("[DEBUG] " + query);
            res = plugin.database.executeQuery(query);
            int gID = -1;
            if (res.length > 0) {
                gID = new Integer(res[0][0]);
            }
            if (gID != -1) {
                if (ins instanceof ArrayList) {
                    for (int i = 0; i < ((ArrayList) ins).size(); i++) {
                        instance = (String) ((ArrayList) ins).get(i);
                        System.out.println("[DEBUG] " + instance);
                        if (instance != null && !instance.equals("") && !instance.equals("null")) {
                            int insID = checkAndAddGroup(instance, world, cfg);
                            query = "SELECT grpID, refTo FROM perm_instances WHERE grpID='" + gID + "' AND refTo='" + insID + "'";
                            res = plugin.database.executeQuery(query);
                            if (res.length == 0) {
                                query = "INSERT INTO perm_instances(grpID, refTo) VALUES('" + gID + "', '" + insID + "')";
                                plugin.database.executeChangeQuery(query);
                            }
                        }
                    }
                } else {
                    instance = (String) ins;
                    System.out.println("[DEBUG] " + instance);
                    if (instance != null && !instance.equals("") && !instance.equals("null")) {
                        int insID = checkAndAddGroup(instance, world, cfg);
                        query = "SELECT grpID, refTo FROM perm_instances WHERE grpID='" + gID + "' AND refTo='" + insID + "'";
                        res = plugin.database.executeQuery(query);
                        if (res.length == 0) {
                            query = "INSERT INTO perm_instances(grpID, refTo) VALUES('" + gID + "', '" + insID + "')";
                            plugin.database.executeChangeQuery(query);
                        }
                    }
                }
            }
            return gID;
        } catch (Exception ex) {
            System.out.println(ex.getMessage());
            throw ex;
        }
    }

    private int checkAndAddUser(String usr, int world, Configuration cfg) throws Exception {
        String query = "SELECT id FROM perm_users WHERE login='" + usr + "' AND world='" + world + "'";
        String[][] res = plugin.database.executeQuery(query);
        if (res.length > 0) {
            return new Integer(res[0][0]);
        }

        String grp = (String) cfg.getProperty("users." + usr + ".group");
        int grpID = checkAndAddGroup(grp, world, cfg);
        query = "INSERT INTO perm_users(login, world, grp) VALUES('" + usr + "','" + world + "','" + grpID + "')";
        //System.out.println("[DEBUG] " + query);
        plugin.database.executeChangeQuery(query);

        query = "SELECT id FROM perm_users WHERE login='" + usr + "' AND world='" + world + "'";
        //System.out.println("[DEBUG] " + query);
        res = plugin.database.executeQuery(query);
        if (res.length > 0) {
            return new Integer(res[0][0]);
        }
        return -1;
    }

    private int checkAndAddWorld(String world, Configuration cfg) throws Exception {
        int ret = -1;




        try {
            String query = "SELECT id FROM perm_worlds WHERE world='" + world + "'";
            String[][] res = plugin.database.executeQuery(query);





            if (res.length > 0) {
                return new Integer(res[0][0]);




            }

            String copies = (String) cfg.getProperty("plugin.permissions.copies");
            String system = (String) cfg.getProperty("plugin.permissions.system");

            query = "INSERT INTO perm_worlds(world, copies, system) VALUES('" + world + "','" + copies + "','" + system + "')";
            plugin.database.executeChangeQuery(query);

            query = "SELECT id FROM perm_worlds WHERE world='" + world + "'";
            res = plugin.database.executeQuery(query);





            if (res.length > 0) {
                ret = new Integer(res[0][0]);




            }

        } catch (Exception ex) {
            System.out.println(ex.getMessage());



            throw ex;



        }
        return ret;




    }

    private void checkAndAddPerm(String perm, int grp, Configuration cfg) throws Exception {
        try {
            String query = "SELECT id FROM perm_permissions WHERE name='" + perm + "'";
            String[][] res = plugin.database.executeQuery(query);
            int permID = 0;
            if (res.length > 0) {
                permID = new Integer(res[0][0]);
            } else {
                query = "INSERT INTO perm_permissions(name) VALUES('" + perm + "')";
                plugin.database.executeChangeQuery(query);

                query = "SELECT id FROM perm_permissions WHERE name='" + perm + "'";
                res = plugin.database.executeQuery(query);
                if (res.length > 0) {
                    permID = new Integer(res[0][0]);
                } else {
                    return;
                }
            }
            query = "SELECT grpID, permID FROM perm_grp_to_perm WHERE grpID='" + grp + "' AND permID='" + permID + "'";
            res = plugin.database.executeQuery(query);
            if (res.length > 0) {
                return;
            }

            query = "INSERT INTO perm_grp_to_perm(grpID, permID) VALUES(" + grp + "," + permID + ")";
            //System.out.println("[DEBUG] " + query);
            plugin.database.executeChangeQuery(query);
        } catch (Exception ex) {
            System.out.println(ex.getMessage());
            throw ex;
        }
    }

    private void checkAndAddPermUsr(String perm, int usr, Configuration cfg) throws Exception {
        try {
            String query = "SELECT id FROM perm_permissions WHERE name='" + perm + "'";
            String[][] res = plugin.database.executeQuery(query);
            int permID = 0;
            if (res.length > 0) {
                permID = new Integer(res[0][0]);
            } else {
                query = "INSERT INTO perm_permissions(name) VALUES('" + perm + "')";
                plugin.database.executeChangeQuery(query);

                query = "SELECT id FROM perm_permissions WHERE name='" + perm + "'";
                res = plugin.database.executeQuery(query);
                if (res.length > 0) {
                    permID = new Integer(res[0][0]);
                } else {
                    return;
                }
            }
            query = "SELECT usrID, permID FROM perm_user_to_perm WHERE usrID='" + usr + "' AND permID='" + permID + "'";
            res = plugin.database.executeQuery(query);
            if (res.length > 0) {
                return;
            }

            query = "INSERT INTO perm_user_to_perm(usrID, permID) VALUES(" + usr + "," + permID + ")";
            //System.out.println("[DEBUG] " + query);
            plugin.database.executeChangeQuery(query);
        } catch (Exception ex) {
            System.out.println(ex.getMessage());
            throw ex;
        }
    }
}
