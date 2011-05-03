/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package SvenBrnn.sqlPermissions;

import java.io.File;
import org.bukkit.util.config.Configuration;

/**
 *
 * @author Sven
 */
public class sqlPermissionsConfig {

    private sqlPermissions plugin;
    private String sqlHost;
    private String sqlPort;
    private String sqlDatabase;
    private String sqlUser;
    private String sqlPassword;
    private String cfgVersion;

    private String syncWait;
    private Configuration config;
    private String file;
    private File cfgFile;

    sqlPermissionsConfig(sqlPermissions plugin) {
        this.plugin = plugin;
        file = "plugins/sqlPermissions/config.yml";
        cfgFile = new File(file);
        this.config = new Configuration(cfgFile);
        if(!cfgFile.exists())
        {
            createConfig();
        }
        readConfig();
    }

    private void readConfig() {
        Boolean setMySQL = (Boolean) config.getProperty("config.i.have.set.the.mysql.data");
        if(setMySQL == null || !setMySQL)
        {
            System.out.println("[sqlPermissions] Please set the MySQL Data first.");
            plugin.disableSqlPermission();
        }
        cfgVersion = (String) config.getProperty("config.configVersion");
        sqlHost = (String) config.getProperty("config.mysql.host");
        sqlPort = (String) config.getProperty("config.mysql.port");
        sqlUser = (String) config.getProperty("config.mysql.user");
        sqlPassword = (String) config.getProperty("config.mysql.password");
        sqlDatabase = (String) config.getProperty("config.mysql.database");

        if(cfgVersion == null || sqlHost == null || sqlPort == null ||  sqlUser == null  || sqlPassword == null || sqlDatabase == null)
        {
           System.out.println("[sqlPermissions] Error in config file.");
           plugin.disableSqlPermission();
        }
    }

    private void createConfig() {
        config.setProperty("config.configVersion", "0.1");
        config.setProperty("config.mysql.host", "localhost");
        config.setProperty("config.mysql.port", "3306");
        config.setProperty("config.mysql.user", "permissions");
        config.setProperty("config.mysql.password", "changeme");
        config.setProperty("config.mysql.database", "permissions");
        config.setProperty("config.i.have.set.the.mysql.data", false);
    }
}
