/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package SvenBrnn.sqlPermissions;

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
                    World wo = worlds.next();
                    System.out.println("[sqlPermissons] Reading Permission file: " + wo.getName());
                    plugin.permEdit.loadPermissionsToDatabase(wo.getName());
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
