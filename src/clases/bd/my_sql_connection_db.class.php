<?php

class MySqlConnectionDb extends ConnectionDb {

    public function __construct($host, $user, $password, $data_base, $charset = 'utf8', $port = 3306) {
        parent::__construct($host, $user, $password, $data_base, $charset, $port);
    }

    public function begin_transaction() {
        $this->_connection = $this->get_connection();
        $this->_in_transaction = TRUE;
        if (!$this->_connection->begin_transaction()) {
            throw new ExceptionDbConnection("Failed to initiate transactions");
        }
        $this->_connection->autocommit(FALSE);
    }

    public function call($procedure) {
        throw new ExceptionDbConnection("Not implemented");
    }

    public function commit() {
        if (!$this->_in_transaction) {
            throw new ExceptionDbConnection("Call begin_transaction() before call commit()");
        }
        $this->_connection->commit();
        $this->_connection->autocommit(TRUE);
        $this->_connection->close();
        $this->_in_transaction = FALSE;
    }

    public function insert($sql_insert, $parameters) {
        $db = $this->get_connection();
        $sql_insert = $this->replace_parameters($sql_insert, $parameters);
        $result = $db->query($sql_insert);
        if ($result === FALSE) {
            throw new ExceptionDbConnection("Error in insert: $sql_insert");
        }
        $last_id = $db->insert_id;
        $this->close_connection($db);
        return $last_id;
    }

    public function query($sql_query, $parameters = array()) {
        $db = $this->get_connection();
        //No parametrizo las consultas. Vulnerable a SQLi
        //$sql_query = $this->replace_parameters($sql_query, $parameters);
        $result = $db->query($sql_query);
        if ($result === FALSE) {
            throw new ExceptionDbConnection("Error in query: $sql_query");
        }
        $r = $result->fetch_all(MYSQLI_ASSOC);
        $this->close_connection($db);
        return $r;
    }

    public function rollback() {
        if (!$this->_in_transaction) {
            throw new ExceptionDbConnection("Call begin_transaction() before call commit()");
        }
        $this->_connection->rollback();
        $this->_connection->autocommit(TRUE);
        $this->_connection->close();
        $this->_in_transaction = FALSE;
    }

    public function sanitize($input) {
        $mysql = new mysqli($this->_host, $this->_user, $this->_password);
        $sanitize = $mysql->real_escape_string($input);
        $mysql->close();
        return $sanitize;
    }

    public function execute($sentence) {
        $db = $this->get_connection();
        $result = $db->query($sentence);
        if ($result === FALSE) {
            throw new ExceptionDbConnection("Error in execute: $db->error");
        }
        $this->close_connection($db);
        return $result;
    }

    public function update($sql_update, $parameters) {
        $db = $this->get_connection();
        $sql_update = $this->replace_parameters($sql_update, $parameters);
        $result = $db->query($sql_update);
        if ($result === FALSE) {
            throw new ExceptionDbConnection("Error in update: $db->error");
        }
        $num = $db->affected_rows;

        $this->close_connection($db);
        return $num;
    }

    /**
     * Retorna un objeto \mysqli conectado a la BD. En caso de estar en modo
     * transacción devuelve la conexión previamente establecida, en otro caso
     * devuelve una nueva conexión a la BD.
     *
     * @return \mysqli Un objeto mysqli conectado a la bd.
     * @throws ExceptionConnectionDb Excepción en caso de no poder conectarse
     * a la BD.
     */
    public function get_connection() {
        $db;
        if ($this->_in_transaction) {
            $db = $this->_connection;
        } else {
            $db = new mysqli($this->_host, $this->_user, $this->_password, $this->_data_base, $this->_port);
            if ($db->connect_errno) {
                throw new ExceptionConnectionDb("Error for connect to bd");
            }
            $db->set_charset($this->_charset);
        }
        return $db;
    }

    /**
     * Si no se encuentra en modo de transacción, cierra la conexión;
     * @param mysqli $con
     */
    private function close_connection($con) {
        if (!$this->_in_transaction) {
            $con->close();
        }
    }

}
