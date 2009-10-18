<?php /* $Id$ $URL$ */
if (!defined('W2P_BASE_DIR')) {
	die('You should not access this file directly.');
}

// Copyright 2004, Adam Donnison <adam@saki.com.au>
// Released under GNU General Public License version 2 or later

require_once $AppUI->getSystemClass('w2p');
require_once $AppUI->getSystemClass('query');

class CResource extends CW2pObject {
	public $resource_id = null;
	public $resource_key = null;
	public $resource_name = null;
	public $resource_type = null;
	public $resource_max_allocation = null;
	public $resource_note = null;

	public function CResource() {
    parent::__construct('resources', 'resource_id');
	}

	public function &loadTypes() {
		// If we have loaded the resource types before then we don't need to
		// load them again.
		if (isset($_SESSION['resource_type_list'])) {
			$typelist = &$_SESSION['resource_type_list'];
		} else {
			$this->_query->clear();
			$this->_query->addTable('resource_types');
			$this->_query->addQuery('resource_type_id, resource_type_name');
			$this->_query->addOrder('resource_type_name');

			$res = &$this->_query->exec(ADODB_FETCH_ASSOC);
			$typelist = array();
			$typelist[0] = array('resource_type_id' => 0, 'resource_type_name' => 'All Resources');
			while ($row = $this->_query->fetchRow()) {
				$typelist[] = $row;
			}
			$this->_query->clear();
			$_SESSION['resource_type_list'] = &$typelist;
		}
		return $typelist;
	}

	public function typeSelect() {
		$typelist = &$this->loadTypes();
		$result = array();
		foreach ($typelist as $type) {
			$result[$type['resource_type_id']] = $type['resource_type_name'];
		}
		return $result;
	}

	public function getTypeName() {
		$result = 'All Resources';
		$this->_query->clear();
		$this->_query->addTable('resource_types');
		$this->_query->addWhere('resource_type_id = ' . (int)$this->resource_type);
		$res = &$this->_query->exec(ADODB_FETCH_ASSOC);
		if ($row = $this->_query->fetchRow()) {
			$result = $row['resource_type_name'];
		}
		$this->_query->clear();
		return $result;
	}
}