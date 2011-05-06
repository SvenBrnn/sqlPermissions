package SvenBrnn.sqlPermissions;

import com.nijiko.permissions.PermissionHandler;
import com.nijikokun.bukkit.Permissions.Permissions;
import java.util.HashMap;
import org.bukkit.entity.Player;
import org.bukkit.plugin.Plugin;
import org.bukkit.plugin.PluginDescriptionFile;
import org.bukkit.plugin.java.JavaPlugin;
import org.bukkit.plugin.PluginManager;

/**
 * sqlPermissions for Bukkit
 *
 * @author SvenBrnn
 */
public class sqlPermissions extends JavaPlugin {

    private final HashMap<Player, Boolean> debugees = new HashMap<Player, Boolean>();
    public PermissionHandler Permissions;
    public sqlPermissionsConfig config;
    public sqlPermissionsDB database;
    public sqlPermissionsPermisionEditor permEdit;
    public sqlPermissionsWorker worker;
    private boolean enabled = true;

    public void onEnable() {
        PluginManager pm = getServer().getPluginManager();
        setupPermissions();
        if (enabled) {
            config = new sqlPermissionsConfig(this);
        }
        if (enabled) {
            database = new sqlPermissionsDB(this);
        }
        if (enabled) {
            permEdit = new sqlPermissionsPermisionEditor(this);
            worker = new sqlPermissionsWorker(this);
            worker.start();
        }
        if (enabled) {
            PluginDescriptionFile pdfFile = this.getDescription();
            System.out.println(pdfFile.getName() + " version " + pdfFile.getVersion() + " is enabled!");
        }
    }

    public void onDisable() {
        System.out.println("sqlPermissions disabled!");
    }

    public boolean isDebugging(final Player player) {
        if (debugees.containsKey(player)) {
            return debugees.get(player);
        } else {
            return false;
        }
    }

    private void setupPermissions() {
        Plugin test = this.getServer().getPluginManager().getPlugin("Permissions");

        if (this.Permissions == null) {
            if (test != null) {
                this.Permissions = ((Permissions) test).getHandler();
                System.out.println("[sqlPermissions] Permission system detected!");
            } else {
                System.out.println("[sqlPermissions] Permission system not detected.");
                disableSqlPermission();
            }
        } else {
            System.out.println("[sqlPermissions] Permission system not detected.");
            disableSqlPermission();
        }
    }

    public void disableSqlPermission() {
        PluginManager pm = getServer().getPluginManager();
        pm.disablePlugin(this);
        enabled = false;
    }

    public void setDebugging(final Player player, final boolean value) {
        debugees.put(player, value);
    }
}
