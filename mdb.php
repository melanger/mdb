<?php
require_once __DIR__ . "/ezSQL/shared/ez_sql_core.php";
require_once __DIR__ . "/ezSQL/mysqli/ez_sql_mysqli.php";

final class Mdb extends ezSQL_mysqli{

public function tn($table_name){
	return $this->safe($table_name);
	}

public function timestamp($ts){
	return strftime("%Y-%m-%d %H:%M:%S", $ts);
	}

public function insert($table, $data){
	$columns = array_keys($data);
	foreach($columns as $i=>$column)
		{
		$columns[$i] = $this->safe($column);
		}
	$values = array_values($data);
	foreach($values as $i=>$value)
		{
		$values[$i] = $this->escape($value);
		}
	$query = "INSERT INTO ".$this->tn($table)." (".implode(",", $columns).") VALUES ('".implode("','", $values)."')";
	return $this->query($query);
	}
public function delete($table, $where=array(), $limit="0", $join_condition="AND"){
	$where_conditions = $this->map_where_conditions($where);
	$query = "DELETE FROM ".$this->tn($table);
	if(!empty($where) && count($where)>0)
		{
		$query .= " WHERE ".implode(" ".$join_condition." ", $where_conditions);
		}
	if($limit)
		{
		$query .= " LIMIT ".$limit;
		}
	return $this->query($query);
	}
public function update($table, $data, $where=array(), $limit="0", $join_condition="AND"){
	$where_conditions = $this->map_where_conditions($where);
	$query = "UPDATE ".$this->tn($table)." SET ";
	$query .= implode(",", $this->map_where_conditions($data));
	if(!empty($where) && count($where)>0)
		{
		$query .= " WHERE ".implode(" ".$join_condition." ", $where_conditions);
		}
	if($limit)
		{
		$query .= " LIMIT ".$limit;
		}
	return $this->query($query);
	}
public function safe($mysql_string){
	return '`'.preg_replace("/[^a-zA-Z_0-9]/", "", $mysql_string).'`';
	}
public function map_where_conditions($where){
	if(empty($where) || count($where)==0)
		{
		return array();
		}
	$where_conditions = array();
	foreach($where as $column=>$value)
		{
		$where_conditions[] = $this->safe($column)."='".$this->escape($value)."'";
		}
	return $where_conditions;
	}
public function escape($query, $like=false, $entities=false){
	$query = strtr($query, array("\\"=>"\\\\","\0"=>"","\n"=>"\\n","\r"=>"\\r","'"=>"\\'","\x1a"=>""));
	if($like) $query = strtr($query, array("%"=>"\\%","_"=>"\\_"));
	if($entities) $query = strtr($query, array("&"=>"&amp;",">"=>"&gt;","<"=>"&lt;",'"'=>"&quot;"));
	return $query;
	}
public function no_nl($query){
	return str_replace(array("\r","\n"), "", $query);
	}
function __construct($user,$pass,$name,$host,$encoding){
	return parent::__construct($user,$pass,$name,$host,$encoding);
	}

}
?>