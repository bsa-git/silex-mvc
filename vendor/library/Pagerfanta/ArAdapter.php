<?php

// vendor/library/Pagerfanta/PfAdapter.php

namespace Pagerfanta;


/**
 * ArAdapter - Adapter for use with PHP ActiveRecord
 *
 * @author ASUTP
 */
class ArAdapter implements Adapter\AdapterInterface {

    private $classname = null;
    private $params = null;

    public function __construct($classname, $params = array()) {
        $this->classname = $classname;
        $this->params = $params;
    }

    public function getNbResults() {
        $params = array('select' => 'COUNT(*) as cnt',);
        if ($this->params) {
            $params = array_merge($this->params, $params);
        } 
        $cnt = call_user_func_array(array($this->classname, "all"), array($params));
        if (!$cnt) {
            return 0;
        } 
        return $cnt[0]->cnt;
    }

    public function getSlice($offset, $length) {
        $params = array('limit' => $length, 'offset' => $offset);
        if ($this->params) {
            $params = array_merge($params, $this->params);
        } 
        return call_user_func_array(array($this->classname, "all"), array($params));
    }

}
