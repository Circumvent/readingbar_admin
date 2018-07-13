<?php
namespace app\api\model;

use think\Model;

class Common extends Model{
	/**
	 * 使用全文索引进行模糊查询方法
	 * @param  $table string 要查询的表名
	 * @param  $column string 要查询的字段
	 * @param  $where string 输入模糊查询的字（不能少于2个）
	 * @return array 查询的结果
	 * 
	 */
	public function fullText($table,$column,$where){
		return $this->query("SELECT * FROM {$table} WHERE MATCH ({$column})AGAINST ('+{$where}' IN BOOLEAN MODE)");
	}
}