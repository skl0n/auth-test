<?php

    class db {

        public static $connection = null;
        protected static $host = "localhost";
        protected static $user = "";
        protected static $pass = "";
        protected static $db = "";

        public static function init($db = array())
        {
            if (count($db) > 0) {
                self::$host = isset($db['host']) ? $db['host'] : "localhost";
                self::$user = isset($db['user']) ? $db['user'] : "";
                self::$pass = isset($db['pass']) ? $db['pass'] : "";
                self::$db   = isset($db['name']) ? $db['name'] : "";
            } else {
                die('error in params for DB');
            }

            self::$connection = mysqli_connect(self::$host, self::$user, self::$pass) or die(mysqli_error());
            mysqli_set_charset(self::$connection, 'utf8') or die(mysqli_error());
            mysqli_select_db(self::$connection, self::$db) or die(mysqli_error());

        }

        public static function query($stmt)
        {                                                           
            $res = mysqli_query(self::$connection, $stmt) or die(mysqli_error(self::$connection));
            if ($res !== false and preg_match("/^INSERT/i", ltrim($stmt))) {
                return self::lastID(self::$connection);
            }
            return $res;
        }

        public static function lastID($lastlink)
        {
            return mysqli_insert_id($lastlink);
        }

        public static function close() 
        {
            mysqli_close(self::$connection);
        }

        public function __destruct()
        {
            mysqli_close(self::$connection);
        }

        public static function num($res) 
        {
            return mysqli_num_rows($res);

        }

        public static function fetch($res, $res_type = MYSQLI_BOTH)
        {
            return mysqli_fetch_array($res, $res_type);
        }

        public static function insert_table($table, $vars = array())
        {
            if (count($vars) === 0) {
                return false;
            }
            $stmt = "";
            foreach ($vars as $col_name => $value) {
                $stmt .= "`$col_name`='" . ($value) . "',";
            }
            if ($stmt != "") $stmt = substr($stmt, 0, -1);
            $stmt = "INSERT `" . $table . "` SET " . $stmt . " ";
            return self::query($stmt);
        }

        public static function get_table($table, $var_key = null, $var_search = null, $limit = -1, $res_type = MYSQLI_BOTH, $keys_array = "", $value_array = "")
        {
            if (!isset($var_key)) $d = "*";
            else {
                $d = "`" . implode("`, `", $var_key) . "`";
            }
            $str = array();
            if (count($var_search)) 
                foreach ($var_search as $k => $v) {
                    if (!is_array($v)) {
                        $str[] = "`$k`='" . $v . "'";
                    } elseif(isset($v['mark'])) {
                        $str[] = "`$k` " . $v['mark'] . $v['data'] . ""; 
                    } elseif(isset($v['in']) && is_array($v['in'])) {
                        $str[] = "`$k` IN ('" . implode('\' , \'', $v['in']) . "')";
                    } 
            } 
            $lim = '';
            if (is_array($limit)) {
                $lim =  ' LIMIT ' . $limit[0] . ', ' . $limit[1];
                $limit = -1;
            }
            $whr = count($str) > 0 ? " WHERE  " . implode(" AND ", $str) : '';
            $sql = "SELECT $d FROM `$table` ".$whr . $lim; 
            $query = self::query($sql);
            return self::get_query_result($query, $limit, $res_type, $keys_array, $value_array);
        }

        public static function get_query_result($query, $limit = 1, $res_type = MYSQLI_BOTH, $key = "", $value = "")
        {   
            if ($query && self::num($query) > 0) {
                $res = false;
                if ($limit == 1) {
                    $rec = self::fetch($query, $res_type);
                    $res = self::value_keys($rec, $key, $value);
                } else {
                    if (!empty($key) && !empty($value)) {    
                        while ($rec = self::fetch($query, $res_type)) {
                            $res[$rec[$key]] = $rec[$value];
                        }
                    } elseif (empty($key) && !empty($value)) {
                        while ($rec = self::fetch($query, $res_type)) {
                            $res[] = $rec[$value];
                        }
                    } elseif (empty($value) && !empty($key)) {
                        while ($rec = self::fetch($query, $res_type)) {
                            $res[$rec[$key]] = $rec;
                        }
                    } elseif ($limit != -1) {
                        $i = 1;
                        while ($rec = self::fetch($query, $res_type)) {
                            if ($i >= $limit) {
                                break;
                            }
                            $res[] = $rec;
                            $i++;
                        }
                    } else {
                        while ($rec = self::fetch($query, $res_type)) {
                            if ($rec) {
                                var_dump($rec);
                                $res[] = $rec;
                            }
                        }
                    }
                }
                return $res;
            }
            return false;
        }

        private static function value_keys($array_from, $key = "", $value = "")
        {

            $returned = array();  
            if (!empty($key) && !empty($value)) { 
                if (isset($array_from[$key]) && isset($array_from[$value])) {
                    $returned[$array_from[$key]] = $array_from[$value];
                } 
            } elseif(empty($key) && !empty($value)) {
                if (isset($array_from[$value])) {
                    $returned = $array_from[$value];
                }
            } elseif(empty($value) && !empty($key)) {
                if (isset($array_from[$key])) {
                    $returned[$array_from[$key]] = $array_from;
                }
            } else {
                $returned = $array_from;
            }

            return $returned;
        }

        public static function delete_from_table($table, $values)
        {
            $str = array();
            foreach ($values as $k => $v) {
                $str[] = "`$k`='" . ($v) . "'";
            }
            if (count($str) == 0) return false;
            $stmt =
            "DELETE FROM `" . $table . "` "
            . "WHERE " . implode(" AND ", $str) ;
            return self::query($stmt);
        }

        public static function in_table($table, $values)
        {
            $str = array();
            foreach ($values as $k => $v) {  
                $str[] = "`$k`='" . ($v) . "'";
            }
            if (count($str) == 0) return false;
            $stmt =
            "SELECT `" . implode("`, `", array_keys($values)) . "` "
            . "FROM `" . $table . "` "
            . "WHERE " . implode(" AND ", $str) . " LIMIT 1";

            $res = self::query($stmt);
            return ($res && self::num($res) > 0);
        }

        public static function query_in($query) 
        {
            $res = self::query($query);
            return ($res && self::num($res) > 0);
        }

        public static function update_table($table, $updateValues = null, $whereValues = null)
        {
            if ($updateValues !== null) {
                $q = "UPDATE `$table` SET ";
                foreach($updateValues as $v=>$k) {
                    if ($k instanceof DateTime) {
                        $k = $k->format('Y-m-d H:i:s');
                    }
                    $q .= "`$v`='$k',";
                }
                $q = substr($q,0,-1);
                $q .= " WHERE 1";
                if ($whereValues !== null) {
                    foreach($whereValues as $v => $k){
                        $q .= " AND `$v`='$k'";
                    }
                }
                return self::query($q);
            }
            return false;
        }

        public static function get_query($sql, $limit = -1, $res_type = MYSQLI_BOTH, $key= "", $value = "")
        {
            $query = self::query($sql);
            return self::get_query_result($query, $limit, $res_type, $key, $value);
        }
    }
?>
