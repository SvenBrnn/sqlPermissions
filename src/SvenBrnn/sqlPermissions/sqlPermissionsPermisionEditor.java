/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package SvenBrnn.sqlPermissions;

import java.io.File;
import java.sql.SQLException;
import java.util.Iterator;
import java.util.List;
import java.util.logging.Level;
import java.util.logging.Logger;
import net.minecraft.server.World;
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

    private void loadPermissionsToDatabase(String world) {
        String worldFile = "plugins/Permissions/" + world + ".yml";
        File f = new File(worldFile);
        if (!f.exists()) {
            return;
        }

        Configuration cfg = new Configuration(f);
        cfg.load();
        String copys = (String) cfg.getProperty("plugin.permissions.copies");
        if (copys == null || copys.equals("null") || copys.equals("")) {
            return;
        }

        int worldID = checkAndAddWorld(world, cfg);
        List groupList = cfg.getKeys("groups");
        Iterator groups = groupList.iterator();
        while (groups.hasNext()) {
            String grp = (String) groups.next();
            int grpID = checkAndAddGroup(grp, worldID, cfg);
            List permList = cfg.getKeys("groups." + grp + ".permissions");
            Iterator perms = permList.iterator();
            while (perms.hasNext()) {
                String perm = (String) perms.next();
                checkAndAddPerm(perm, grpID, cfg);
            }
        }
    }

    private int checkAndAddGroup(String grp, int world, Configuration cfg) {
        int ret = -1;
        try {
            String query = "SELECT id FROM perm_groups WHERE name='" + grp + "' AND world='" + world + "'";
            String[][] res = plugin.database.executeQuery(query);
            if (res.length > 0) {
                return new Integer(res[0][0]);
            }

            String def = (String) cfg.getProperty("groups." + grp + ".default");
            String instance = (String) cfg.getProperty("groups." + grp + ".inheritance");
            String sufix = (String) cfg.getProperty("groups." + grp + ".info.sufix");
            String prefix = (String) cfg.getProperty("groups." + grp + ".info.prefix");
            String build = (String) cfg.getProperty("groups." + grp + ".info.build");

            query = "INSERT INTO perm_groups(name, world, default, prefix, sufix, build, instances) VALUES('" + grp + "','" + world + "','" + def + "','" + prefix + "','" + sufix + "','" + build + "','" + instance + "')";
            plugin.database.executeChangeQuery(query);

            query = "SELECT id FROM perm_groups WHERE name='" + grp + "' AND world='" + world + "'";
            res = plugin.database.executeQuery(query);
            if (res.length > 0) {
                return new Integer(res[0][0]);
            }
        } catch (Exception ex) {
            System.out.println(ex.getMessage());
        }
        return ret;
    }

    private int checkAndAddWorld(String world, Configuration cfg) {
        int ret = -1;
        try {
            String query = "SELECT id FROM perm_worlds WHERE name='" + world + "'";
            String[][] res = plugin.database.executeQuery(query);

            if (res.length > 0) {
                return new Integer(res[0][0]);
            }

            String copies = (String) cfg.getProperty("plugin.permissions.copies");
            String system = (String) cfg.getProperty("plugin.permissions.system");

            query = "INSERT INTO perm_worlds(name, copies, system) VALUES('" + world + "','" + copies + "','" + system + "')";
            plugin.database.executeChangeQuery(query);

            query = "SELECT id FROM perm_worlds WHERE name='" + world + "'";
            res = plugin.database.executeQuery(query);

            if (res.length > 0) {
                ret = new Integer(res[0][0]);
            }

        } catch (Exception ex) {
            System.out.println(ex.getMessage());
        }
        return ret;
    }

    private void checkAndAddPerm(String perm, int grp, Configuration cfg) {
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

            query = "INSERT INTO perm_grp_to_perm(grpID, permID) VALUES('" + grp + "','" + permID + "')";
            plugin.database.executeChangeQuery(query);
        } catch (Exception ex) {
            System.out.println(ex.getMessage());
        }
    }
}
