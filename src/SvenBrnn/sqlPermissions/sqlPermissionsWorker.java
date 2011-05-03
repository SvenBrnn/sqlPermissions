/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package SvenBrnn.sqlPermissions;

import java.util.Iterator;
import java.util.List;
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
        while(!stoprequested)
        {
            List<World> worldList = plugin.getServer().getWorlds();
            Iterator<World> worlds = worldList.iterator();
            while(worlds.hasNext())
            {
                World wo = worlds.next();
            }
        }
    }
}
