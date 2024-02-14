<?php

class QQPDO extends PDO
{
    static private $inst = null;

    public function __construct($dsn, $user, $pass)
    {
        global $cfg;
        parent::__construct($dsn, $user, $pass);
    }

    public static function clear()
    {
        self::$inst = null;
    }

    public static function setdb($cfg)
    {
        $str = 'mysql:host=' . $cfg['host'] . ';dbname=' . $cfg['dbname'];

        self::$inst = new QQPDO($str, $cfg['user'], $cfg['passw']);
        self::$inst->exec("SET NAMES 'utf8'");

        return self::$inst;
    }

    public static function db()
    {
        global $cfg;
        $str = 'mysql:host=' . $cfg['host'] . ';dbname=' . $cfg['dbname'];

        if (!self::$inst) {
            self::$inst = new QQPDO($str, $cfg['user'], $cfg['passw']);
            self::$inst->exec("SET NAMES 'utf8'");
        }
        return self::$inst;
    }

    public function exec($sql, $params = array())
    {
        try {
            //self::$counter++;
            $sth = $this->prepare($sql);
            $sth->execute($params);

            $err = $sth->errorInfo();
            if (isset($err[1])) {
                throw new \Exception($err[2] . "<br>" . $sql, $err[1]);
            }
        } catch (PDOException $e) {
            throw new \Exception($e->getMessage() . "<br>" . $sql, $e->getCode());
        }
        return $sth;
    }


    public function getResults($sql, $params = array())
    {
        $sth = $this->exec($sql, $params);
        return $sth->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getResult($sql, $params = array())
    {
        $data = $this->getResults($sql, $params);

        if (empty($data)) {
            return null;
        }

        $data = $data[0];

        if (count($data) > 1) {
            return $data;
        } else {
            return array_pop($data);
        }
    }

    public function update($table, $data, $keyarr)
    {
        $keys = '';

        foreach ($data as $k => $el) {
            $keys .= "{$k} = :{$k}, ";
        }
        $keys = substr($keys, 0, -2);

        $cnt = count($keyarr);
        $where = '';

        if ($cnt > 0) {
            $whk = '';
            $i = 0;

            foreach ($keyarr as $k => $el) {
                $whk .= "({$k} = :{$k})";

                if ($i < $cnt - 1) {
                    $whk .= ' AND ';
                }

                $i++;
            }
            $where = "WHERE $whk";
        }
        $sql = "UPDATE {$table} SET {$keys} {$where}";

        $data = array_merge($data, $keyarr);
        $this->exec($sql, $data);
    }

    public function insert($table, $data, $returnid = false)
    {
        $values = $keys = '';
        foreach ($data as $k => $el) {
            $keys .= "{$k}, ";

            $values .= ":{$k}, ";
        }

        $values = substr($values, 0, -2);
        $keys = substr($keys, 0, -2);
        $sql = "INSERT INTO {$table}({$keys}) VALUES({$values})";

        $this->exec($sql, $data);

        if ($returnid) {
            return $this->getLastInsertedId();
        } else {
            return;
        }
    }

    public function escape($str)
    {
        return html_filter($str);
    }

    public function formLimit($start, $num)
    {
        return "LIMIT $start, $num";
    }

    public function getLastInsertedId()
    {
        return $this->getResult('SELECT last_insert_id()');
    }
}

?>