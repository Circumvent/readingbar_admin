<?php
namespace app\api\controller;

use app\api\controller\Common;
use think\Request;
use app\api\model\User as m_user;
use app\api\controller\CaptcheImg as captche;
use app\api\model\Common as m_common;

class User extends Common{
	private $model_user;
	private $controller_captche;
	private $model_common;
	/*
	初始化模型
	 */
	public function _initialize(){
		$this->model_common = new m_common();
	}
	/*
	登陆
	 */
	public function login(){
		//获取post过来的数据
		$request = Request::instance();
		$data = $request->post();
		//使用验证器验证数据是否为空
		if(!empty($error = $this->use_validate('login',$data))){
			//返回报错
			return $error;
		}
		//没错误进行登陆
		$res = $this->model_user->find(array('username'=>$data['username']),'id,password');
		//判断用户是否存在
		if(!empty($res)){
			if($res['password'] == $data['password']){
				return $token = $this->set_token($res['id']);
			}
		}
		//不存在就返回没有此用户
		return $this->sendMsg(400,'username or password error');
	}
	/*
	需要token的机密方法
	 */
	public function secret(){
		//获取post过来的数据
		$request = Request::instance();
		$data = $request->post();
		//使用验证器验证数据是否为空
		if(!empty($error = $this->use_validate('token',$data))){
			//返回报错
			return $error;
		}
		//没错误验证token是否合法
		return $this->check_token($data);
	}
	/*
	调用判断验证码接口
	 */
	public function userCaptche(){
		//获取post过来的数据
		$request = Request::instance();
		$data = $request->post();
		//使用验证器验证数据是否为空
		if(!empty($error = $this->use_validate('captche',$data))){
			//返回报错
			return $error;
		}
		//没错误验证验证码是否正确
		return $this->controller_captche->check_Captche($data['captche_num']);
	}
	/*
	验证form重复提交
	 */
	public function check_form(){
		//获取post过来的数据
		$request = Request::instance();
		$data = $request->post();
		//使用验证器验证数据是否为空
		if(!empty($error = $this->use_validate('token',$data))){
			//返回报错
			return $error;
		}
		//没错误验证form_token是否合法
		return $this->check_form_token($data['token']);
	}
	/*
	发送表单token
	 */
	public function get_form(){
		return $this->set_form_token();
	}

	public function write_txt(){
		$request = Request::instance();
		$content = $request->post()['content'];
		//生成唯一时间戳作为文件的名字
		$fileName = uniqid();
		$path = './txt/good_book/' . $fileName . '.txt';
		var_dump($this->create_txt($path,$content));
	}

	public function read_file(){
		$request = Request::instance();
		$path = $request->post()['path'];
		echo $this->read_txt($path);
	}

	public function insert(){
		return $this->model_common->inster_data('Admin',['username'=>'a','password'=>'123456']);
	}
}