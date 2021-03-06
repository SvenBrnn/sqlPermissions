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
    private String sqlDriver;

    sqlPermissionsConfig(sqlPermissions plugin) {
        this.plugin = plugin;
        file = "plugins/sqlPermissions/config.yml";
        cfgFile = new File(file);
        this.config = new Configuration(cfgFile);
        if (!cfgFile.exists()) {
            createConfig();
        }
        readConfig();
    }

    private void readConfig() {
        config.load();
        Boolean setMySQL = (Boolean) config.getProperty("config.i.have.set.the.mysql.data");
        if (setMySQL == null || !setMySQL) {
            System.out.println("[sqlPermissions] Please set the MySQL Data first.");
            plugin.disableSqlPermission();
        }
        cfgVersion = (String) config.getProperty("config.configVersion");
        sqlHost = (String) config.getProperty("config.mysql.host");
        sqlPort = (String) config.getProperty("config.mysql.port");
        sqlUser = (String) config.getProperty("config.mysql.user");
        sqlPassword = (String) config.getProperty("config.mysql.password");
        sqlDatabase = (String) config.getProperty("config.mysql.database");
        sqlDriver = (String) config.getProperty("config.mysql.driver");

        if(sqlDriver == null)
            sqlDriver = "com.mysql.jdbc.Driver";

        if (cfgVersion != null && (cfgVersion.equals("0.1") || cfgVersion.equals("0.2") || cfgVersion.equals("0.3") || cfgVersion.equals("0.4"))) {
            updateVersion();
        }
        if(cfgVersion != null && cfgVersion.equals("0.5"))
        {

        }
        if (cfgVersion == null || sqlHost == null || sqlPort == null || sqlUser == null || sqlPassword == null || sqlDatabase == null) {
            System.out.println("[sqlPermissions] Error in config file.");
            plugin.disableSqlPermission();
        }
    }

    private void createConfig() {
        config.setProperty("config.configVersion", "0.6");
        config.setProperty("config.mysql.host", "localhost");
        config.setProperty("config.mysql.port", "3306");
        config.setProperty("config.mysql.user", "permissions");
        config.setProperty("config.mysql.password", "changeme");
        config.setProperty("config.mysql.database", "permissions");
        config.setProperty("config.mysql.driver", "com.mysql.jdbc.Driver");
        config.setProperty("config.i.have.set.the.mysql.data", false);
        config.save();
    }

    public String getSQLHost() {
        return sqlHost;
    }

    public String getSQLPort() {
        return sqlPort;
    }

    public String getSQLUser() {
        return sqlUser;
    }

    public String getSQLPassword() {
        return sqlPassword;
    }

    public String getSQLDatabase() {
        return sqlDatabase;
    }

    public String getSQLDriver() {
        return sqlDriver;
    }

    private void updateVersion() {
        config.load();
        config.setProperty("config.configVersion", "0.5");
        config.save();
    }

    private void updateVersion2() {
        config.load();
        config.setProperty("config.configVersion", "0.6");
        config.setProperty("config.mysql.driver", "com.mysql.jdbc.Driver");
        config.save();
    }
}
