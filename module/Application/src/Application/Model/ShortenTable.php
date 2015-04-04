<?php

namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;

class ShortenTable extends AbstractTableGateway {

 protected $table = 'shorten';

 public function __construct(Adapter $adapter) {
  $this->adapter = $adapter;
  $this->resultSetPrototype = new ResultSet();
  $this->resultSetPrototype->setArrayObjectPrototype(new Shorten());
  $this->initialize();
 }

  public function findByAttr(array $attr = null, $paginator = false) {
	$select = new Select();
	$select->from($this->table);
	$select->order('id DESC');

	if (sizeof($attr) > 0) {
	  $where = new Where();
	  foreach ($attr as $k => $a) {
		$where->equalTo($k, $a);
	  }
	  $select->where($where);
	}
	if ($paginator) {
	  return new \Zend\Paginator\Paginator(
		  new \Zend\Paginator\Adapter\DbSelect(
		  $select, $this->adapter
	  ));
	} else {
	  $resultSet = $this->selectWith($select);
	  $resultSet->buffer();

	  return $resultSet;
	}
  }

 public function fetchAll(Select $select = null) {
  if ($select === null) {
   $select = new Select();
  }
  $select->from($this->table);
  $select->order('id DESC');

  $resultSet = $this->selectWith($select);
  $resultSet->buffer();

  return $resultSet;
 }
 
 
 public function increment($short){
	 
	 $this->update(['views' => new \Zend\Db\Sql\Expression('views + 1')], ['short' => $short]);
	 
 }

 public function get($id) {
  $id = (int) $id;
  $rowset = $this->select(array('id' => $id));
  $rowCount = $rowset->count();
  if ($rowCount <= 0) {
   return 0;
  } else {
   return $rowset->current();
  }
 }
 
 public function search($param){

    $select = new Select();
    $select->from($this->table);
    $select->order('id DESC');
	//$select->join('tac_galrycat_tbl', 'tac_photo_tbl.ph_category = tac_galrycat_tbl.cat_id', ['cat_name'=>'category'], 'left');

    $object = get_object_vars(new Shorten());
    
    $where = new Where();
    foreach($object as $obj => $value){
      $where->like($obj, '%'. $param . '%')->OR;
    }
    
    $select->where($where);
    $resultSet = $this->selectWith($select);
    $resultSet->buffer();

    return $resultSet;
  }

 public function save($data) {
  if (isset($data['id']) && $data['id'] > 0) {
   if ($this->get($data['id'])) {
    $this->update($data, array('id' => $data['id']));
   }
  } else {
   $this->insert($data);
   $id = $this->lastInsertValue;
   return $id;
  }
 }

 public function del($id) {
  if ((int) $id > 0) {
   if ($this->get($id)) {
    $this->delete(array('id' => $id));
   }
  }
 }

 public function getActive($active = 1) {
  $select = new Select();
  $where = new Where();

  $select->from($this->table);
  $where->equalTo('active', $active);
  $select->where($where);
  $resultSet = $this->selectWith($select);
  $resultSet->buffer();

  return $resultSet;
 }

}
