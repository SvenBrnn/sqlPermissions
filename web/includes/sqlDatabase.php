<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sqlDatabase
 *
 * @author Sven
 */
class sqlDatabase {

    var $sqlUser;
    var $sqlPass;
    var $sqlHost;
    var $sqlDB;
    var $dbResult;
    var $dbError;
    var $dbErrorNo;

    public function __construct($host, $user, $pass, $database) {
        $this->sqlUser = $user;
        $this->sqlPass = $pass;
        $this->sqlHost = $host;
        $this->sqlDB = $database;

        //Try to connect
        $con = $this->conOpen();
        $this->conClose($con);
    }

     /**
     * Executes an Query
     *
     * @param string $query A MySQL Query
     * @return void
     */
    public function query($query) {
        $con = $this->conOpen();
        $this->dbResult = mysql_query($query, $con);
        $this->dbError = mysql_error($con);
        $this->dbErrorNo = mysql_errno($con);

        $this->conClose($con);
    }
     /**
     * Gets if there was an Error
     *
     * @param string $query A MySQL Query
     * @return bool Was there an Error.
     */
    public function isError() {
        if ($this->dbError != NULL)
            return true;
        else
            return false;
    }
     /**
     * Gets the Error
     *
     * @return string The Error
     */
    public function getError() {
        return $this->dbError;
    }

     /**
     * Gets the Errornumber
     *
     * @return int The Errornumber
     */
    public function getErrorNum() {
        return $this->dbErrorNo;
    }

     /**
     * Gets Num Rows
     *
     * @return int Num Rows
     */
    public function getNumRows() {
        return mysql_num_rows($this->dbResult);
    }

     /**
     * Gets an Array of Row Arrays
     *
     * @return array Array of Row Arrays
     */
    public function fetchArrayList() {
        $arr = array();
        while (($a = mysql_fetch_array($this->dbResult)) != NULL)
            $arr[] = $a;

        return $arr;
    }

         /**
     * Gets an Array of Row Objects
     *
     * @return array Array of row Objects
     */
    public function fetchObjectList() {
        $arr = array();
        while (($a = mysql_fetch_object($this->dbResult)) != NULL)
            $arr[] = $a;

        return $arr;
    }

     /**
     * Gets Next Row as Array
     *
     * @return array Row Array
     */
    public function fetchArray() {
        return mysql_fetch_array($this->dbResult);
    }

     /**
     * Gets next Row as Object
     *
     * @param string $query A MySQL Query
     * @return objekt Next Row
     */
    public function fetchObject() {
        return mysql_fetch_object($this->dbResult);
    }

    private function conOpen() {
        $con = mysql_connect($this->sqlHost, $this->sqlUser, $this->sqlPass) or die ("Unable to Connect!");
        mysql_select_db($this->sqlDB, $con) or die ("Unable to Select DB!");
        return $con;
    }

    private function conClose($con) {
        mysql_close($con);
    }

    public function cleanStatement($statement)
    {
        return mysql_real_escape_string($statement);
    }
}

?>
