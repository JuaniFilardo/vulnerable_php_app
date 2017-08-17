<?php
/* PoolConnectionDb contiene un pool de conexiones \ConnectionDb */
class PoolConnectionDb {
    /* Objeto singleton*/
    private static $_me = NULL;
    /* Id de la conexión default */
    private $_default_db_connection_id = '';
    /* Array con todas las conexiones del pool */
    private $_connections_db = array();

    private function __construct() {

    }
    /**
     * Retorna el objeto Singleton
     * @return \PoolConnectionDb Instancia singleton de la clase
     */
    public static function get_instance(){
        if(is_null(self::$_me)){
            self::$_me = new PoolConnectionDb();
        }
        return self::$_me;
    }
    /**
     * Agrega una conexión de bd al pool de conexiones.
     * La primera conexión que se agregue, se setea como la conexión por default.
     *
     * @param \ConnectionDb $connection_db Conexión a la bd
     * @param string $id Id identificador de la conexión.
     * @throws InvalidArgumentException
     */
    public function add_connection_db($connection_db,$id = 'default'){
        if(empty($this->_connections_db)){
            $this->_default_db_connection_id = $id;
        }
        if(isset($this->_connections_db[$id])){
            throw new InvalidArgumentException("That Id already exists");
        }
        $this->_connections_db[$id] = $connection_db;
    }

    /**
     * Retorna una conexión del pool.
     * Si no se indica un ID, retorna la conexión por defecto (la primer conexión
     * agregada).
     *
     * @param string $id Id que identifica la conexión a devolver.
     * @return \ConnectionDb
     * @throws InvalidArgumentException Si el ID solicitado, no se encuentra en
     * el array.
     */
    public function get_connection_db($id = ''){
        $dbconnection = NULL;
        if(empty($id) && !empty($this->_connections_db)){
            $dbconnection = $this->_connections_db[$this->_default_db_connection_id];
        }else{
            if(isset($this->_connections_db[$id])){
                $dbconnection = $this->_connections_db[$id];
            }else{
                throw new InvalidArgumentException("Id: $id not found in array connections_db");
            }
        }
        return $dbconnection;
    }
}
