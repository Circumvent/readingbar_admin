<?php
namespace app\api\controller;

class Txt extends Common{
	/**
	 * 写入txt文件方法
	 * @param    string     $path    txt文件要保存的路径
	 * @param    string     $content txt文件的内容
	 * @return   array              	 写入的字节数
	 */
	public function create_txt($path,$content){
		//创建文件
		$file = fopen($path,'w+');
		//写入文件
		fwrite($file,$content);
		//关闭文件
		fclose($file);
		//统计有多少字
		$countNum = mb_strlen($content,'UTF-8');
		if($countNum == 0)
			return $this->sendMsg(200,'输入错误，未录入');
		//返回写入了多少字
		return $this->sendMsg(200,'',array('countNum',$countNum));
	}
	/**
	 * 获取txt内容方法
	 * @param    string     $path txt文件所在的路径
	 * @return   array	          txt文件里的内容
	 */
	public function read_txt($path){
		//读取文件
		$file = fopen($path,'r');
		//读取文件最大字节数
		$content = fread($file,filesize($path));
		//关闭文件
		fclose($file);
		return $this->sendMsg(200,'',array('content',$content));
	}
}