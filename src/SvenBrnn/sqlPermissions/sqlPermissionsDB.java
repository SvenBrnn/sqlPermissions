/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package SvenBrnn.sqlPermissions;

import com.mysql.jdbc.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;

/**
 *
 * @author Sven
 */
public class sqlPermissionsDB {
    private sqlPermissions plugin;
    private Connection con;

    sqlPermissionsDB(sqlPermissions plugin) {
        this.plugin = plugin;
        tryConnect();
    }

    private void tryConnect() {
        try {
            conOpen();
            conClose();
        } catch (ClassNotFoundException ex) {
            System.out.println("[sqlPermissions] Error while Trying to connect to Database:");
            System.out.println(ex.getMessage());
        } catch (SQLException ex) {
            System.out.println("[sqlPermissions] Error while Trying to connect to Database:");
            System.out.println(ex.getMessage());
        }
    }

    private void conOpen()
        throws ClassNotFoundException, SQLException
    {
        Class.forName("com.mysql.jdbc.Driver");
        con = (Connection)DriverManager.getConnection("jdbc:mysql://127.0.0.1:3306/animetv2", "animetv2", "lol123");
    }

    private void conClose()
        throws SQLException
    {
        con.close();
    }
}
