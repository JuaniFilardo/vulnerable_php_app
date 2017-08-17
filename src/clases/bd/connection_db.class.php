<?php
/*
* @version 1.0
*/
abstract class ConnectionDb {

    /* Demilitador de comienzo de un parámetro */
    const START_DELIMITER_PARAMETER = '{';
    /* Demilitador de fin de un parámetro */
    const END_DELIMITER_PARAMETER = '}';

    protected $_host = 'localhost';
    protected $_data_base = '';
    protected $_user = 'root';
    protected $_password = '';
    /* Objeto de la conexion a la bd*/
    protected $_connection;
    protected $_port;
    protected $_charset = '';
    /* Booleano que indica si esta en modo de transacción */
    protected $_in_transaction = false;

    public function __construct($host, $user, $password, $data_base, $charset = 'utf8', $port = 3306) {
        $this->_host = $host;
        $this->_data_base = $data_base;
        $this->_user = $user;
        $this->_password = $password;
        $this->_charset = $charset;
        $this->_port = $port;
        $this->_connection = NULL;
    }
    /**
     * Retorna el HOST.
     * @return string Host
     */
    protected function get_host() {
        return $this->_host;
    }
    /**
     * Retorna el nombre de la base de datos conectada.
     * @return string Nombre de la base de datos
     */
    protected function get_data_base() {
        return $this->_data_base;
    }
    /**
     * Retorna el nombre de usuario de la conexión a la bd.
     * @return string Nombre de usuario de la bd.
     */
    protected function get_user() {
        return $this->_user;
    }
    /**
     * Retorna el charset seteado
     *
     * @return string Charset
     */
    protected function get_charset() {
        return $this->_charset;
    }

    /**
     * Para comenzar una transacción
     */
    abstract function begin_transaction();

    /**
     * Commit.
     */
    abstract function commit();
    /**
     * Rollback.
     */
    abstract function rollback();
    /**
     * Sanitización para insertar a la bd.
     */
    abstract function sanitize($input);
    /**
     * Permite ejecutar una sentencia en la BD.
     * OJO! NO sanitiza las entradas.
     */
    abstract function execute($sentence);
    /**
     * Realizar una consulta a la BD. Se deben pasar los paramétros en un
     * array. Sanitiza los parámetros.
     * @return Un array con los datos.
     */
    abstract function query($sql_query, $parameters);
    /**
     * Insertar en la BD.
     * @return El último id insertado.
     */
    abstract function insert($sql_insert, $parameters);
    /**
     * Actualizar en la BD. Se puede usar también para un delete.
     * @return La cantidad de filas afectadas.
     */
    abstract function update($sql_update, $parameters);
    /**
     * Llamar a un procedimiento de almacenado.
     */
    abstract function call($procedure);

    /**
     * Dada una cadena con parámetros de la forma {esto_es_un_parametro} lo
     * remplaza por el valor dado en el array $parameters. Cada elemento del
     * array tiene como clave el "id" que identifica al parámetro (por ejemplo
     * "esto_es_un_parametro"), donde cada elemento es un array conteniendo
     * el valor del parámetro y el tipo.
     * Por ejemplo:
     *
     * $sql=SELECT id,nombre FROM usuarios WHERE id={ id } AND nombre= {nombre}
     * $parameters = ["id" = > [4,'i'],"nombre" => ["pabex",'s']];
     *
     * TIPOS:
     *      's': String
     *      'i': Integer
     *      'd': Float | Double
     *      'b': Blob
     *
     * @param string $sql
     * @param array $parameters
     * @return string Cadena con la sentencia con los parámetros incluidos y
     * sanitizados.
     * @throws InvalidArgumentException Si no se pasa un array de parámetros
     * @throws InvalidParameterExceptionDbConnection Si los tipos no coinciden
     * con sus valores correspondientes, o si no se encuentra el parámetro en
     * el array de parámetros.
     */
    protected function replace_parameters($sql,$parameters){
        $aux = '';
        if(!is_array($parameters)){
            throw new InvalidArgumentException("Not found array with parameters");
        }
        for($i = 0; $i < strlen($sql); $i++){
            if($sql[$i] === static::START_DELIMITER_PARAMETER){
                $i++;
                $id = '';
                while($i < strlen($sql) && $sql[$i] !== static::END_DELIMITER_PARAMETER){
                    $id .= ($sql[$i] != ' ')? $sql[$i] : '';
                    $i++;
                }
                if($id === '' || $i === strlen($sql)){
                    throw new InvalidParameterExceptionDbConnection("Malformer parameter");
                }
                if(!isset($parameters[$id])){
                    throw new InvalidParameterExceptionDbConnection("Parameter with id: $id not found");
                }
                $value = $parameters[$id][0];
                $value = $this->sanitize($value);
                $type = $parameters[$id][1];

                switch ($type){
                    case 'i':
                        if(!is_numeric($value)){
                            throw new InvalidParameterExceptionDbConnection("$value is not numeric.");
                        }
                        if((int)$value != $value){
                            throw new InvalidParameterExceptionDbConnection("$value is not a Integer");
                        }
                        $aux .= $value;
                        break;
                    case 'd':
                        if(!is_numeric($value)){
                            throw new InvalidParameterExceptionDbConnection("$value is not numeric.");
                        }
                        $value = floatval($value);
                        if(!is_float($value)){
                            throw new InvalidParameterExceptionDbConnection("$value is not a Float");
                        }
                        $aux .= $value;
                        break;
                    case 'b':
                    case 's':
                        $aux .= "'" . $value . "'";
                        break;
                }

            }  else if($sql[$i] !== static::END_DELIMITER_PARAMETER) {
                $aux .= $sql[$i];
            }
        }
        return $aux;
    }
}

class ExceptionDbConnection extends Exception{

}

class InvalidParameterExceptionDbConnection extends ExceptionDbConnection{

}
