<?php
namespace app\api\controller;

class Admin extends Common{
	/**
	 * 获取管理员信息
	 * @param    int     $id 要获取的管理员的id
	 * @return   array         管理员的信息
	 */
	public function list_admin_info($id){
		//判断，如果id不是数字，返回报错
		if(!is_numeric($id))
			return $this->sendMsg(403,'id must be a number');
		//调用Auth类中的getGroups方法获取管理员信息，是二维数组只需获取第一个
		$info = $this->auth->getGroups($id)[0];
		return $this->sendMsg(200,'',$info);
	}
}