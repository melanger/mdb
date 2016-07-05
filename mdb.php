<?php
require_once __DIR__ . "/idiorm/idiorm.php";

final class Mdb extends ORM {
public static function insert_by_array($table, $data){
	$row = self::for_table($table)->create();
  foreach($data as $column=>$value){
    $row->$column = $value;
  }
	return $row->save();
	}
public static function select_by_array($table, $where=array(), $limit="0", $join_condition="AND"){
  if($join_condition != "AND"){
    throw new Exception("Other delete condition than AND not supported yet.");
  }
  $r = self::for_table($table);
  foreach($where as $column=>$value){
    $r = $r->where_equal($column, $value);
  }
  if($limit > 0) $r = $r->limit($limit);
  return $r;
}
public static function delete_by_array($table, $where=array(), $limit="0", $join_condition="AND"){
	return self::select_by_array($table, $where, $limit)->delete_many();
	}
public static function update_by_array($table, $data, $where=array(), $limit="0", $join_condition="AND"){
	$r = self::select_by_array($table, $where, $limit, $join_condition)->find_result_set();
  foreach($data as $column=>$value){
    $r->set($column, $value);
  }
  return $r->save();
	}
}
?>