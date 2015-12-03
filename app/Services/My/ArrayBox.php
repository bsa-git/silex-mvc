<?php

// app/Services/My/ArrayBox.php

namespace Services\My;

/**
 * Class - ArrayBox
 *
 * @category Service
 * @package  app\Services
 * @author   Sergei Beskorovainyi <bsa2657@yandex.ru>
 * @license  MIT <http://www.opensource.org/licenses/mit-license.php>
 * @link     http://my.site
 */
Class ArrayBox implements \IteratorAggregate, \Countable {

    /**
     * @var array 
     */
    private $_array = array();

    //----------------------------

    /**
     * Constructor
     * 
     * 
     * @param array|string|int $a
     * @param string|int $delimiter 
     */
    public function __construct($a = array(), $delimiter = '&') {
        $this->set($a, $delimiter);
    }

    /**
     * Destructor to prevent memory leaks.
     */
    public function __destruct() {
        unset($this);
    }

    /**
     * Returns a parameter by name or the entire array
     *
     * @param string $path    The key ("")
     * @param mixed  $default The default value if the parameter key does not exist
     * @param bool   $deep    If true, a path like foo[bar] will find deeper items
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     * 
     */
    function get($path = null, $default = null, $deep = false) { // 
        $array = $this->_array;
        //----------------------
        if ($path === NULL) {
            return $array;
        } else {

            if (is_int($path)) {
                return $array[$path];
            }

            if (!$deep || false === $pos = strpos($path, '[')) {
                return array_key_exists($path, $array) ? $array[$path] : $default;
            }

            $root = substr($path, 0, $pos);
            if (!array_key_exists($root, $array)) {
                return $default;
            }

            $value = $array[$root];
            $currentKey = null;
            for ($i = $pos, $c = strlen($path); $i < $c; ++$i) {
                $char = $path[$i];

                if ('[' === $char) {
                    if (null !== $currentKey) {
                        throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "[" at position %d.', $i));
                    }

                    $currentKey = '';
                } elseif (']' === $char) {
                    if (null === $currentKey) {
                        throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "]" at position %d.', $i));
                    }

                    if (!is_array($value) || !array_key_exists($currentKey, $value)) {
                        return $default;
                    }

                    $value = $value[$currentKey];
                    $currentKey = null;
                } else {
                    if (null === $currentKey) {
                        throw new \InvalidArgumentException(sprintf('Malformed path. Unexpected "%s" at position %d.', $char, $i));
                    }

                    $currentKey .= $char;
                }
            }

            if (null !== $currentKey) {
                throw new \InvalidArgumentException(sprintf('Malformed path. Path must end with "]".'));
            }
            if (is_array($value)) {
                return new self($value);
            } else {
                return $value;
            }
        }
    }

    /**
     * Returns the all array.
     *
     * @return array An array of parameters
     */
    function all() {
        $array = $this->_array;
        return $array;
    }

    /**
     * Replaces the current array by a new set.
     *
     * @param array $a An array of parameters
     */
    function replace(array $a = array()) {
        return new self($a);
    }

    /**
     * Adds parameters.
     *
     * @param array $a An array of parameters
     *
     * @api
     */
    function add(array $a = array()) {
        $array = $this->_array;
        $new = array_replace($array, $a);
        return new self($new);
    }

    /**
     * Removes a value.
     *
     * @param string $key The key
     */
    function remove($key) {
        $array = $this->_array;
        unset($array[$key]);
        return new self($array);
    }

    /**
     * Returns the alphabetic characters of the array value.
     *
     * @param string $key     The parameter key
     * @param mixed  $default The default value if the parameter key does not exist
     * @param bool   $deep    If true, a path like foo[bar] will find deeper items
     *
     * @return string The filtered value
     *
     * @api
     */
    function getAlpha($key, $default = '', $deep = false) {
        return preg_replace('/[^[:alpha:]]/', '', $this->get($key, $default, $deep));
    }

    /**
     * Returns the alphabetic characters and digits of the array value.
     *
     * @param string $key     The parameter key
     * @param mixed  $default The default value if the parameter key does not exist
     * @param bool   $deep    If true, a path like foo[bar] will find deeper items
     *
     * @return string The filtered value
     *
     * @api
     */
    function getAlnum($key, $default = '', $deep = false) {
        return preg_replace('/[^[:alnum:]]/', '', $this->get($key, $default, $deep));
    }

    /**
     * Returns the digits of the array value.
     *
     * @param string $key     The parameter key
     * @param mixed  $default The default value if the parameter key does not exist
     * @param bool   $deep    If true, a path like foo[bar] will find deeper items
     *
     * @return string The filtered value
     *
     * @api
     */
    function getDigits($key, $default = '', $deep = false) {
        // we need to remove - and + because they're allowed in the filter
        return str_replace(array('-', '+'), '', $this->filter($key, $default, $deep, FILTER_SANITIZE_NUMBER_INT));
    }

    /**
     * Returns the array value converted to integer.
     *
     * @param string $key     The parameter key
     * @param mixed  $default The default value if the parameter key does not exist
     * @param bool   $deep    If true, a path like foo[bar] will find deeper items
     *
     * @return int The filtered value
     *
     * @api
     */
    function getInt($key, $default = 0, $deep = false) {
        return (int) $this->get($key, $default, $deep);
    }

    /**
     * Returns the array value converted to boolean.
     *
     * @param string $key     The parameter key
     * @param mixed  $default The default value if the parameter key does not exist
     * @param bool   $deep    If true, a path like foo[bar] will find deeper items
     *
     * @return bool The filtered value
     */
    function getBoolean($key, $default = false, $deep = false) {
        return $this->filter($key, $default, $deep, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Filter key.
     *
     * @param string $key     Key.
     * @param mixed  $default Default = null.
     * @param bool   $deep    Default = false.
     * @param int    $filter  FILTER_* constant.
     * @param mixed  $options Filter options.
     *
     * @see http://php.net/manual/en/function.filter-var.php
     *
     * @return mixed
     */
    function filter($key, $default = null, $deep = false, $filter = FILTER_DEFAULT, $options = array()) {
        $value = $this->get($key, $default, $deep);

        // Always turn $options into an array - this allows filter_var option shortcuts.
        if (!is_array($options) && $options) {
            $options = array('flags' => $options);
        }

        // Add a convenience check for arrays.
        if (is_array($value) && !isset($options['flags'])) {
            $options['flags'] = FILTER_REQUIRE_ARRAY;
        }

        return filter_var($value, $filter, $options);
    }

    /**
     * Returns an iterator for array.
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    function getIterator() {
        return new \ArrayIterator($this->_array);
    }

    /**
     * 
     * Get the last value of the array
     * 
     * @return mixed 
     */
    function getLast() { // 
        $array = $this->_array;
        if (count($array)) {
            $index = count($array) - 1;
            return $array[$index];
        } else {
            return NULL;
        }
    }

    /**
     * Set or add the value of the array index
     * 
     * 
     * @param array|string|int $a
     * @param string|int $delimiter 
     * @return Services\ArrayBox
     */
    function set($a = array(), $delimiter = '&') { //
        if (is_array($a)) {// $a -> array
            $this->_array = $a;
        } elseif (is_string($a)) {
            if ($delimiter === NULL) {// $a -> string (unserialize)
                $this->_array = unserialize(base64_decode($a));
            } elseif ($delimiter == '&') {// $a -> string (query)
                parse_str($a, $this->_array);
            } else {// $a -> string (delimiter)
                $this->_array = array(); // Очистим массив
                $array = explode($delimiter, $a);
                foreach ($array as $value) {
                    $pos = strpos($value, '=');
                    if ($pos !== false) {
                        $key = substr($value, 0, $pos);
                        $key = trim($key);
                        $value = substr($value, $pos + 1);
                        $value = trim($value);
                        $this->_array[$key] = $value;
                    }
                }
                if (!count($this->_array)) {
                    $this->_array = $array;
                }
            }
        } elseif (is_integer($a) && is_integer($delimiter)) {// range ($low, $high)
            $low = $a; // Lower range value
            $high = $delimiter; // The upper range value
            $this->_array = range($low, $high);
        }

        return $this;
    }

    /**
     * Set value
     * 
     * @param string|int $key
     * @param string|int $value 
     * @return Services\ArrayBox
     */
    function setVal($key, $value = NULL) { //
        $array = $this->_array;
        $array[$key] = $value;
        return new self($array);
    }

    /**
     * Returns string representation of the object
     * @return string string representation of the object
     */
    public function __toString() {
        return var_export($this->_array, true);
    }

    /**
     * Get the max. value of an array
     * 
     * @return array 
     */
    function max() {
        $array = $this->values()->get();
        $max = floatval($array[0]);
        $index = 0;
        foreach ($array as $key => $val) {
            $val = floatval($val);
            if ($val > $max) {
                $max = $val;
                $index = $key;
            }
        }
        return array("index" => $index, "value" => $max);
    }

    /**
     * Get the min. value of an array
     * 
     * @param bool $notZero  It is considered min, taking into account non-zero values 
     * @return array 
     */
    function min($notZero = false) {
        $array = $this->values()->get();
        if ($notZero) {
            $min = $this->max();
            $min = $min["value"];
        } else {
            $min = floatval($array[0]);
        }
        $index = 0;
        foreach ($array as $key => $val) {
            $val = floatval($val);
            if ($notZero && $val == 0) {
                continue;
            }
            if ($val < $min) {
                $min = $val;
                $index = $key;
            }
        }
        return array("index" => $index, "value" => $min);
    }

    /**
     * Get the sum. of an array
     * 
     * @return float
     */
    function sum() {
        $array = $this->values()->get();
        $sum = 0;
        foreach ($array as $val) {
            $val = floatval($val);
            $sum += $val;
        }
        return $sum;
    }

    /**
     * Get the avg. value of an array
     * 
     * @param bool $notZero  It is considered average, taking into account non-zero values 
     * @return float
     */
    function avg($notZero = false) { //
        $avg = 0;
        $count = 0;
        //-------------------------
        $values = $this->values()->get();
        $count = $this->count($notZero);
        if ($count) {
            $sum = $this->sum();
            $avg = $sum / $count;
        }
        return $avg;
    }

    /**
     * Get the mode value of an array
     * 
     * @return float
     */
    function mode() { //
        $array = $this->values()->get();
        foreach ($array as $val) {
            $frequency[$val] ++;
        }
        $fr = new self($frequency);
        $r = $fr->max();
        return $r["index"];
    }

    /**
     * Get the median value of an array
     * 
     * @return float
     */
    function median() { //
        $count = $this->count();
        $array = $this->order()->get();
        if ($count % 2 == 0) {
            return ($array[$count / 2 - 1] + $array[$count / 2]) / 2;
        } else {
            return $array[$count / 2 - 0.5];
        }
    }

    /**
     * Get array in order
     * 
     * @param bool $ascending
     * @return ArrayBox 
     */
    function order($ascending = true) { //
        $array = $this->_array;
        if ($ascending) {
            usort($array, cmp_a);
        } else {
            usort($array, cmp_d);
        }
        return new self($array);
    }

    /**
     * Serialize array to a string
     * 
     * @param bool $isBase64
     * @return boolean|string 
     */
    function serialize($isBase64 = true) { //
        $array = $this->_array;
        if (is_array($array)) {
            if ($isBase64) {
                return base64_encode(serialize($array));
            } else {
                return serialize($array);
            }
        } else {
            return false;
        }
    }

    /**
     * Unserialize string to array
     * 
     * @param string $value
     * @param bool $isBase64
     * @return ArrayBox 
     */
    function unserialize($value, $isBase64 = true) { //
        if (is_string($value)) {
            if ($isBase64) {
                $array = unserialize(base64_decode($value));
            } else {
                $array = unserialize($value);
            }
        } else {
            $array = $this->_array;
        }
        return new self($array);
    }

    /**
     * The union of the two arrays
     * 
     * @param array $aArray
     * @return ArrayBox 
     */
    function merge($aArray) { // $array + $aArray
        $array = $this->_array;
        $result = array_replace($array, $aArray);
        return new self($result);
    }

    /**
     * Select all the keys of an array
     * 
     * @return ArrayBox 
     */
    function keys() { //
        $array = $this->_array;
        return new self(array_keys($array));
    }

    /**
     * Select all the values of an array
     * 
     * @return ArrayBox 
     */
    function values() { //
        $array = $this->_array;
        return new self(array_values($array));
    }

    /**
     * Проверить, присутствует ли в массиве указанный ключ или индекс
     * 
     * @param mixed $key
     * @return bool
     */
    function isKey($key) {
        $array = $this->_array;
        return array_key_exists($key, $array);
    }

    /**
     * Проверить, присутствует ли в массиве значение
     * 
     * 
     * @param mixed $value
     * @return bool
     */
    function isValue($value) {
        $array = $this->_array;
        return in_array($value, $array);
    }

    /**
     * Size of array
     * 
     * @param bool $notZero  It is considered average, taking into account non-zero values 
     * @return int 
     */
    function count($notZero = false) { // 
        $count = 0;
        //-----------------
        $array = $this->_array;
        if ($notZero) {
            foreach ($array as $value) {
                $value = floatval($value);
                if ($value == 0) {
                    continue;
                }
                $count++;
            }
        } else {
            $count = count($array);
        }
        return $count;
    }

    /**
     * Delete the last value in the array or set index
     * 
     * @param int|string $index
     * @return ArrayBox
     */
    function pop($index = NULL) { //
        $array = $this->_array;
        if (!$index === NULL) {
            unset($array[$index]); // Удалить элемент массива по индексу
        } else {
            array_pop($array); // Удалить последний элемент массива
        }
        return new self($array);
    }

    /**
     * Delete the first value in the array or set index
     * 
     * @param int|string $index
     * @return ArrayBox
     */
    function shift($index = NULL) {
        $array = $this->_array;
        if (!$index === NULL) {
            unset($array[$index]); // Удалить элемент массива по индексу
        } else {
            array_shift($array); // Удалить первый элемент массива
        }
        return new self($array);
    }

    /**
     * Add value ​​to the end of the array
     * 
     * @param mixed $values
     * @return ArrayBox 
     */
    function push($value) { // 
        $array = $this->_array;
        array_push($array, $value);
        $this->_array = $array;
        return new self($array);
    }

    /**
     * Add value ​​to the first of the array
     * 
     * @param mixed $value
     * @return ArrayBox 
     */
    function addToFirst($value) { // 
        $array = $this->_array;
        array_unshift($array, $value);
        return new self($array);
    }

    /**
     * Add (key = value) ​​to the first of the array
     * 
     * @param mixed $values
     * @return ArrayBox 
     */
    function addToFirstAssoc($key, $value) { // 
        $array = $this->_array;
        $arr = array_reverse($array, true);
        $arr[$key] = $value;
        $array = array_reverse($arr, true);
        return new self($array);
    }

    /**
     * Join array elements into a string
     *  
     * @param string $delimiter
     * @return string 
     */
    function join($delimiter = ' ') { //
        $array = $this->_array;
        return implode($delimiter, $array);
    }

    /**
     * Splits a string into a array
     * 
     * @param string $value
     * @param string $delimiter
     * @return ArrayBox 
     */
    function split($value, $delimiter = ' ') { //
        $array = $this->_array;
        if (is_string($value)) {
            $array = explode($delimiter, $value);
        }
        return new self($array);
    }

    /**
     * Converts an array to a query string, 
     * etc.  - "first=value&arr[]=foo+bar&arr[]=baz"
     * 
     * @return string 
     */
    function array2query() { //
        $array = $this->_array;
        return http_build_query($array);
    }

    /**
     * Converts the query string etc.  - "first=value&arr[]=foo+bar&arr[]=baz"
     * to a array
     * 
     * @param string $query
     * @return ArrayBox 
     */
    function query2array($query) {
        $array = array();
        parse_str($query, $array);
        return new self($array);
    }

    /**
     * Get a slice of the array of values (arrays / objects) on the key
     * and remove duplicate values from the cut, if needed
     *
     * @param  string|array $key     //The name of the field or fields ['id', 'email']
     * @param  bool $unique    //Sign of the uniqueness of the output array
     *
     * @return ArrayBox 
     */
    function slice($key, $unique = false) {
        $array = array();
        $items = $this->_array;
        //--------------------------
        foreach ($items as $item) {
            if (is_array($item)) {
                if (is_array($key)) {
                    $key_ = $key[0];
                    $value_ = $key[1];
                    $array[$item[$key_]] = $item[$value_];
                } else {
                    $array[] = $item[$key];
                }
            } elseif (is_object($item)) {
                if (is_array($key)) {
                    $key_ = $key[0];
                    $value_ = $key[1];
                    $array[$item->$key_] = $item->$value_;
                } else {
                    $array[] = $item->$key;
                }
            }
        }
        if ($unique && is_string($key)) {
            $array = array_unique($array);
        }

        return new self($array);
    }

    /**
     * Apply a filter to the array of keys, using a callback function 
     * 
     * 
     * @param array|string $aCallback // array("class-name", "metod-class") or array("object-link", "metod-object") or "func-name"
     * @return ArrayBox 
     */
    function filterKeys($aCallback = array()) {
        $arrayFilter = array();
        $array = $this->_array;
        $keys = $this->keys()->get();
        $keys = array_filter($keys, $aCallback);
        foreach ($keys as $key) {
            $arrayFilter[$key] = $array[$key];
        }
        return new self($arrayFilter);
    }

    /**
     * It applies a filter to an array of values using a callback function 
     * 
     * 
     * @param array|string $aCallback // array("class-name", "metod-class") or array("object-link", "metod-object") or "func-name"
     * @return ArrayBox 
     */
    function filterValues($aCallback = array()) {
        $array = $this->_array;
        $array = array_filter($array, $aCallback);
        return new self($array);
    }

    /*
     * Get an array whose values are represented as
     * ["key1 = 'val1'", "key2 = 'val2'" ...]
     *
     *
     * @return ArrayBox
     */

    function KeyEqVal() {
        $new_array = array();
        $array = $this->_array;
        foreach ($array as $key => $val) {
            $new_array[] = "{$key} = {$val}";
        }
        return new self($new_array);
    }

    /*
     * Delete keys and values from the corresponding array of characters
     *
     * @param string $character_mask
     * @return ArrayBox
     */

    function trimArray($character_mask = " \t\n\r\0\x0B") {
        $new_array = array();
        $array = $this->_array;
        foreach ($array as $key => $val) {
            if (is_string($key)) {
                $key = trim($key, $character_mask);
            }
            if (is_string($val)) {
                $val = trim($val, $character_mask);
            }
            $new_array[$key] = $val;
        }
        return new self($new_array);
    }

    /*
     * Remove the rows in the array
     * keys or values depending on the parameter -> "$forKeys" 
     * are present / absent in "$removals"
     * depending on the parameter -> $except
     * If $except=false, then removes the corresponding -> "$removals"
     * If $except=true, then leave  the corresponding -> "$removals"
     *
     * @param array $removals 
     * @param bool $forKeys 
     * @param bool $except 
     * @return ArrayBox
     */
    function delRows($removals = array("", null,), $forKeys = false, $except = false) {
        $new_array = array();
        $array = $this->_array;
        foreach ($array as $key => $val) {

            $eql = $forKeys ? $key : $val;

            if ($except) {
                if (in_array($eql, $removals)) {
                    $new_array[$key] = $val;
                }
            } else {
                if (!in_array($eql, $removals)) {
                    $new_array[$key] = $val;
                }
            }
        }
        return new self($new_array);
    }

    /*
     * filtering data for MSSQL
     *
     * source: http://stackoverflow.com/questions/574805/how-to-escape-strings-in-mssql-using-php
     *
     *
     * @return ArrayBox
     */

    function ms_escape() {
        $new_array = array();
        $array = $this->_array;
        foreach ($array as $key => $val) {
            $new_array[$key] = $this->_getEscapeValForMsSql($val);
        }
        return new self($new_array);
    }

    //================ ADD FUNCTIONS =========//

    /*
     * filtering data for MSSQL
     *
     * source: http://stackoverflow.com/questions/574805/how-to-escape-strings-in-mssql-using-php
     *
     * @param str/int $data
     *
     * @return str/int $data - filtered
     */

    private function _getEscapeValForMsSql($data) {
        if (!isset($data) or empty($data)) {
            return '';
        }
        $is_sign = preg_match("/[+-]/", $data);
        if (is_numeric($data) && !$is_sign) {//sign $pos = strpos($mystring, $findme); 
            return $data;
        }
        $non_displayables = array(
            '/%0[0-8bcef]/', // url encoded 00-08, 11, 12, 14, 15
            '/%1[0-9a-f]/', // url encoded 16-31
            '/[\x00-\x08]/', // 00-08
            '/\x0b/', // 11
            '/\x0c/', // 12
            '/[\x0e-\x1f]/'             // 14-31
        );
        foreach ($non_displayables as $regex) {
            $data = preg_replace($regex, '', $data);
        }
        $data = str_replace("'", "''", $data);
        $data = "'{$data}'";
        return $data;
    }

}

function cmp_a($a, $b) {
    if ($a == $b)
        return 0;
    return ($a < $b) ? -1 : 1;
}

function cmp_d($a, $b) {
    if ($a == $b)
        return 0;
    return ($a > $b) ? -1 : 1;
}
