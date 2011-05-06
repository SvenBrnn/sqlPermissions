/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package SvenBrnn.sqlPermissions;

import com.nijikokun.bukkit.Permissions.Permissions;
import java.io.File;
import java.sql.Time;
import java.text.DateFormat;
import java.text.FieldPosition;
import java.text.ParsePosition;
import java.util.Date;
import java.util.Iterator;
import java.util.List;
import java.util.logging.Level;
import java.util.logging.Logger;
import org.bukkit.World;

/**
 *
 * @author Sven
 */
public class sqlPermissionsWorker extends Thread {

    private boolean stoprequested;
    private sqlPermissions plugin;

    public sqlPermissionsWorker(sqlPermissions plugin) {
        super();
        this.plugin = plugin;
        stoprequested = false;
    }

    public synchronized void requestStop() {
        stoprequested = true;
    }

    public void run() {
        try {
            Thread.sleep(10000);
        } catch (InterruptedException ex) {
            //Logger.getLogger(sqlPermissionsWorker.class.getName()).log(Level.SEVERE, null, ex);
        }
        while (!stoprequested) {
            List<World> worldList = plugin.getServer().getWorlds();
            Iterator<World> worlds = worldList.iterator();  
            while (worlds.hasNext()) {
                try {
                    boolean changes = false;
                    World wo = worlds.next();
                    String worldFile = "plugins/Permissions/" + wo.getName() + ".yml";
                    File fi = new File(worldFile);
                    if (fi.exists()) {
                        Date lastMod = new Date(fi.lastModified() / 1000);
                        String[][] res = plugin.database.executeQuery("SELECT UNIX_TIMESTAMP(value) FROM perm_config WHERE param='lastDBChange'");
                        Long lm = new Long(res[0][0]);
                        Date LastDBMod = new Date(lm);

                        //System.out.println("[sqlPermissons][DEBUG] lastMod " + lastMod.getTime());
                        //System.out.println("[sqlPermissons][DEBUG] LastDBMod "  + LastDBMod.getTime());
                        if (lastMod.getTime() > LastDBMod.getTime()) {
                            System.out.println("[sqlPermissons] Reading Permission file: " + wo.getName());
                            plugin.permEdit.loadPermissionsToDatabase(wo.getName());
                        } else {
                            System.out.println("[sqlPermissons] Writing Permission file: " + wo.getName());
                            plugin.permEdit.loadPermissionsFromDatabase(wo.getName());
                            changes = true;
                        }
                    } else {
                        System.out.println("[sqlPermissons] Writing Permission file: " + wo.getName());
                        plugin.permEdit.loadPermissionsFromDatabase(wo.getName());
                        changes = true;
                    }

                    if(changes)
                    {
                        System.out.println("[sqlPermissons] Reloading Permissions!");
                        plugin.Permissions.reload();
                    }
                    
                } catch (Exception ex) {
                    Logger.getLogger(sqlPermissionsWorker.class.getName()).log(Level.SEVERE, null, ex);
                }
            }
            try {
                Thread.sleep(300000);
            } catch (InterruptedException ex) {
                //Logger.getLogger(sqlPermissionsWorker.class.getName()).log(Level.SEVERE, null, ex);
            }
        }
    }
}
