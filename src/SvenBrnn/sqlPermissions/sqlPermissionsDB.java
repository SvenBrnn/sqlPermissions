/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package SvenBrnn.sqlPermissions;

import java.sql.Connection;
import java.sql.ResultSetMetaData;
import java.sql.Statement;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;

/**
 *
 * @author Sven
 */
public class sqlPermissionsDB {

    private sqlPermissions plugin;
    private Connection con;
    private Statement st;
    private ResultSet rs;

    sqlPermissionsDB(sqlPermissions plugin) {
        this.plugin = plugin;
        tryConnectAndCreateDB();
    }

    private void tryConnectAndCreateDB() {
        try {
            this.conOpen();
            createDatabase();
        } catch (ClassNotFoundException ex) {
            System.out.println("[sqlPermissions] Error while Trying to connect to Database:");
            System.out.println(ex.getMessage());
        } catch (SQLException ex) {
            System.out.println("[sqlPermissions] Error while Trying to connect to Database:");
            System.out.println(ex.getMessage());
        }
    }

    public void conOpen()
            throws SQLException, ClassNotFoundException {
        Class.forName(plugin.config.getSQLDriver());
        con = (Connection) DriverManager.getConnection("jdbc:mysql://" + plugin.config.getSQLHost() + ":" + plugin.config.getSQLPort() + "/" + plugin.config.getSQLDatabase(), plugin.config.getSQLUser(), plugin.config.getSQLPassword());
    }

    public void conClose()
            throws SQLException {
        con.close();
        while (!con.isClosed()) {
            try {
                Thread.sleep(30);
            } catch (InterruptedException ex) {
                //Logger.getLogger(sqlPermissionsDB.class.getName()).log(Level.SEVERE, null, ex);
            }
        }
        con = null;
    }

    public String[][] executeQuery(String query)
            throws SQLException, ClassNotFoundException {
        //conOpen();
        if (con == null) {
            conOpen();
        }
        st = (Statement) con.createStatement();
        rs = st.executeQuery(query);
        ResultSetMetaData rsmd = (ResultSetMetaData) rs.getMetaData();
        int n = rsmd.getColumnCount();
        ArrayList arOut = new ArrayList();
        for (int j = 0; rs.next(); j++) {
            ArrayList ArrIn = new ArrayList();
            for (int i = 1; i <= n; i++) {
                ArrIn.add(rs.getString(i));
            }

            arOut.add(ArrIn);
        }

        String strArr[][] = new String[arOut.toArray().length][n];
        for (int i = 0; i < arOut.toArray().length; i++) {
            ArrayList ar = (ArrayList) arOut.toArray()[i];
            for (int k = 0; k < ar.toArray().length; k++) {
                strArr[i][k] = (String) ar.toArray()[k];
            }

        }

        //conClose();
        return strArr;
    }

    public void executeChangeQuery(String query)
            throws SQLException, ClassNotFoundException {
        if (con == null) {
            conOpen();
        }
        st = (Statement) con.createStatement();
        st.executeUpdate(query);
    }

    private void createDatabase() throws ClassNotFoundException, SQLException {
        String[] sqlArray = new String[9];
        sqlArray[0] = "CREATE TABLE IF NOT EXISTS perm_permissions ("
                + "id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,"
                + "name VARCHAR(255) NULL,"
                + "description VARCHAR(255) NULL,"
                + "PRIMARY KEY(id)"
                + ")";
        sqlArray[1] = "CREATE TABLE IF NOT EXISTS perm_webusers ("
                + "id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,"
                + "username VARCHAR(255) NOT NULL,"
                + "password VARCHAR(255) NOT NULL,"
                + "PRIMARY KEY(id)"
                + ")";
        sqlArray[2] = "CREATE TABLE IF NOT EXISTS perm_worlds ("
                + "id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,"
                + "world VARCHAR(255) NULL,"
                + "copies VARCHAR(255) NULL,"
                + "system VARCHAR(255) NULL,"
                + "PRIMARY KEY(id)"
                + ")";
        sqlArray[3] = "CREATE TABLE IF NOT EXISTS perm_groups ("
                + "world INTEGER UNSIGNED NOT NULL,"
                + "id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,"
                + "name VARCHAR(255) NULL,"
                + "def VARCHAR(5) NULL,"
                + "prefix VARCHAR(255) NULL,"
                + "sufix VARCHAR(255) NULL,"
                + "build VARCHAR(255) NULL,"
                + "rank int(11) NULL,"
                //+ "instances VARCHAR(255) NULL,"
                + "PRIMARY KEY(id),"
                + "INDEX perm_groups_FKIndex1(world),"
                + "FOREIGN KEY(world)"
                + "  REFERENCES perm_worlds(id)"
                + "    ON DELETE NO ACTION"
                + "    ON UPDATE NO ACTION"
                + ")";
        sqlArray[4] = "CREATE TABLE IF NOT EXISTS perm_config ("
                + "id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,"
                + "param VARCHAR(255) NULL,"
                + "value VARCHAR(255) NULL,"
                + "PRIMARY KEY(id)"
                + ")";
        sqlArray[5] = "CREATE TABLE IF NOT EXISTS perm_instances ("
                + "grpID INTEGER UNSIGNED NOT NULL,"
                + "refTo INTEGER UNSIGNED NOT NULL,"
                + "PRIMARY KEY(grpID, refTo),"
                + "INDEX perm_instances_FKIndex1(grpID),"
                + "INDEX perm_instances_FKIndex2(refTo),"
                + "FOREIGN KEY(grpID)"
                + "  REFERENCES perm_groups(id)"
                + "    ON DELETE NO ACTION"
                + "    ON UPDATE NO ACTION,"
                + "FOREIGN KEY(refTo)"
                + "  REFERENCES perm_groups(id)"
                + "    ON DELETE NO ACTION"
                + "    ON UPDATE NO ACTION"
                + ")";
        sqlArray[6] = "CREATE TABLE IF NOT EXISTS perm_users ("
                + "id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,"
                + "grp INTEGER UNSIGNED NOT NULL,"
                + "world INTEGER UNSIGNED NOT NULL,"
                + "login VARCHAR(255) NULL,"
                + "pass VARCHAR(255) NULL,"
                + "PRIMARY KEY(id),"
                + "INDEX perm_users_FKIndex2(world),"
                + "FOREIGN KEY(world)"
                + "  REFERENCES perm_worlds(id)"
                + "    ON DELETE NO ACTION"
                + "    ON UPDATE NO ACTION,"
                + "INDEX perm_users_FKIndex1(grp),"
                + "FOREIGN KEY(grp)"
                + "   REFERENCES perm_groups(id)"
                + "     ON DELETE NO ACTION"
                + "     ON UPDATE NO ACTION"
                + ")";
        sqlArray[7] = "CREATE TABLE IF NOT EXISTS perm_grp_to_perm ("
                + "grpID INTEGER UNSIGNED NOT NULL,"
                + "permID INTEGER UNSIGNED NOT NULL,"
                + "PRIMARY KEY(grpID, permID),"
                + "INDEX perm_groups_has_perm_permissions_FKIndex1(grpID),"
                + "INDEX perm_groups_has_perm_permissions_FKIndex2(permID),"
                + "FOREIGN KEY(grpID)"
                + "  REFERENCES perm_groups(id)"
                + "    ON DELETE NO ACTION"
                + "    ON UPDATE NO ACTION,"
                + "FOREIGN KEY(permID)"
                + "  REFERENCES perm_permissions(id)"
                + "    ON DELETE NO ACTION"
                + "    ON UPDATE NO ACTION"
                + ")";
        sqlArray[8] = "CREATE TABLE IF NOT EXISTS perm_user_to_perm ("
                + "usrID INTEGER UNSIGNED NOT NULL,"
                + "permID INTEGER UNSIGNED NOT NULL,"
                + "PRIMARY KEY(usrID, permID),"
                + "INDEX perm_groups_has_perm_permissions_FKIndex1(usrID),"
                + "INDEX perm_groups_has_perm_permissions_FKIndex2(permID),"
                + "FOREIGN KEY(usrID)"
                + "  REFERENCES perm_users(id)"
                + "    ON DELETE NO ACTION"
                + "    ON UPDATE NO ACTION,"
                + "FOREIGN KEY(permID)"
                + "  REFERENCES perm_permissions(id)"
                + "    ON DELETE NO ACTION"
                + "    ON UPDATE NO ACTION"
                + ")";
        for (String sql : sqlArray) {
            //System.out.println("[Debug] " + sql);
            executeChangeQuery(sql);
        }
        if (executeQuery("SELECT value FROM perm_config WHERE param='sqlVersion'").length == 0) {
            executeChangeQuery("INSERT INTO perm_config(param, value) VALUES('sqlVersion', '0.2')");
        }
        if (executeQuery("SELECT value FROM perm_config WHERE param='lastDBChange'").length == 0) {
            executeChangeQuery("INSERT INTO perm_config(param, value) VALUES('lastDBChange', FROM_UNIXTIME(0))");
        }
        if (executeQuery("SELECT * FROM perm_webusers WHERE 1").length == 0) {
            executeChangeQuery("INSERT INTO perm_webusers(username, password) VALUES('admin', md5('password'))");
        }
        String[][] ret = executeQuery("SELECT value FROM perm_config WHERE param='sqlVersion'");
        if (ret.length > 0) {
            if (ret[0][0].equals("0.1")) {
                updateFrom01To02();
            }
        }
        //plugin.disableSqlPermission();
        //plugin.disableSqlPermission();
    }

    private void updateFrom01To02() throws SQLException, ClassNotFoundException {
        System.out.println("[sqlPermissions] Updating Database from 0.1 to 0.2!");
        String query = "ALTER TABLE perm_groups add rank int(11)";
        executeChangeQuery(query);
        executeChangeQuery("UPDATE perm_config SET value='0.2' WHERE param='sqlVersion'");
    }
}
