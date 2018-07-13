<?php
namespace app\api\controller;

use think\Controller;
use think\Cache;
use think\Loader;
use think\Session;

class Common extends Controller{
	protected $auth;
	/**
	 * 判断是否登录，并实例化Auth类
	 */
	public function _initialize(){
		//判断是否登录
		if(!Session::get('id') || !Session::get('username')){
			// 	found 	url()
			$this->error('您尚未登录系统',url());
			die;
		}
		// //实例化Auth类
		$this->auth = new Auth();
		//获取当前控制器和方法
		$request = Request::instance();
		$control = $request->controller();
		$action = $request->action();
		//拼接成权限规则
		$name = $control . '/' . $action;
		//判断是否有权限访问
		if($this->auth->check($name,Session::get('id'))){
			// 	found 	url()
			$this->error('没有权限',url());
		}
	}
	/**
	 * 返回消息
	 * @param  int 	  $code 状态码
	 * @param  string $msg  返回的信息
	 * @param  array  $data 返回的数据
	 * @return array        返回信息
	 */
	public function sendMsg($code,$msg,$data = []){
		$res_msg['code'] = $code;
		if(!empty($data))
			$res_msg['data'] = $data;
		if(!empty($msg))
			$res_msg['msg'] = $msg;
		return $res_msg;
	}
	/**
	 * 调用验证器接口
	 * @param  string $validate 调用的验证器名字	
	 * @param  string $scene 	验证场景
	 * @param  array  $data  	需要验证的数据
	 * @return string        	错误信息
	 */
	public function use_validate($validate,$scene,$data){
		//使用验证器验证数据是否为空
		$validate = Loader::validate($validate);
		if(!$validate->scene($scene)->check($data)){
			//返回报错
			return  $validate->getError();
		}
	}
	/**
	 * 身份验证的验证token
	 * @param  array $data 传入数据
	 * @return array       结果
	 */
	public function check_token($data){
		//服务器端token
		$server_token = Cache::get($data['id']);
		//比较验证是否合法
		if($server_token == $data['token']){
			//合法，返回200状态码
			return $this->sendMsg(200,'token ok');
		}
		//不合法
		return $this->sendMsg(401,'token不合法');
	}
	/**
	 * 设置身份的token
	 * @param int $id 用户id
	 * @return string 设置好的token
	 */
	public function set_token($id){
		//生成32位的token密文
		$token = md5(uniqid() . $id );		//放入redis缓存
		Cache::set($id,$token);
		// return $this->sendMsg(200,'ok',array('token'=>Cache::get($id),'id'=>$id));
		return $this->sendMsg(200,'',Cache::get($id));
	}
	/**
	 * 表单防止重复提交的验证token
	 * @param  string $cilent_token 客户端发来的表单token值
	 * @return array                结果
	 */
	public function check_form_token($cilent_token){
		//判断token是否合法
		if(!($cilent_token == Session::get('token'))){
			return $this->sendMsg(401,'表单重复提交');
			// return Session::get('token');
		}
		else{
			Session::delete('token');
			return $this->sendMsg(200,'正常访问');
		}
	}
	/**
	 * 表单防止重复提交的设置token
	 * @return string 	 token的内容
 	 */
	public function set_form_token(){
		//生成32位的token密文
		$token = md5(uniqid() . 'form');
		//设置session
		Session::set('token',$token);
		return $this->sendMsg(200,'',array('token',Session::get('token')));
	}
}