<?php
namespace app\api\controller;

use think\Session;

class CaptcheImg extends Common
{
	/**
	 * 生成验证码
	 * @return img 验证码图片
	 */
	public function sendCaptche(){
		//一坨生成验证码的东西
		$image = imagecreatetruecolor(100, 30);  
	    $bgcolor = imagecolorallocate($image, 255, 255, 255);  
	    imagefill($image, 0, 0, $bgcolor);  
	  
	    $code = '';  
	    for($i=0;$i<4;$i++) {  
	        $fontsize = 6;  
	        $fontcolor = imagecolorallocate($image, mt_rand(0, 120), mt_rand(0, 120),mt_rand(0, 120));  
	  		//验证码字符集
	        $data = 'abcdefghijkmnpqrstuvwxyABCDEFGHIJKMNQRSTUVWXYZ1234567890';  
	        $fontcontent = substr($data, mt_rand(0, strlen($data)-1), 1);  
	        $code .= $fontcontent;  
	  
	        $x = ($i*100/4) + mt_rand(5, 10);  
	        $y = mt_rand(5, 10);  
	  
	        imagestring($image, $fontsize, $x, $y, $fontcontent, $fontcolor);  
	    }  
	    
	    for($i=0; $i<200;$i++) {  
	        $pointcolor = imagecolorallocate($image, mt_rand(50,200), mt_rand(50,200), mt_rand(50,200));  
	        imagesetpixel($image, mt_rand(1,99), mt_rand(1,29), $pointcolor);  
	    }  
	  
	    for($i=0;$i<3;$i++) {  
	        $linecolor = imagecolorallocate($image, mt_rand(80,220), mt_rand(80,220), mt_rand(80, 220));  
	        imageline($image, mt_rand(1,99), mt_rand(1,29), mt_rand(1,99), mt_rand(1,29), $linecolor);  
	    }  
  		//设置验证码
  		//存入session,大写传入
  		Session::set('captche_name',strtolower($code));

  		//以下是从tp自带验证码的php文件那里抄来的
  		//打开输出控制缓冲
	   	ob_start();
	    // 输出图像
	    imagepng($image);
	    // 得到当前缓冲区的内容并删除当前输出缓
	    $content = ob_get_clean();
	    //销毁图片
	    imagedestroy($image);
	    //返回图片
	    return response($content, 200, ['Content-Length' => strlen($content)])->contentType('image/png');
	}
	/**
	 * @param  string $client_captche 客户端传来的验证码值
	 * @return array                  验证码是否正确
	 */
	public function check_Captche($client_captche){
		//获取验证码缓存
		$server_captche = Session::get('captche_name');
		// return $server_captche;
		//判断是否正确
		if($server_captche == strtolower($client_captche)){
			//销毁session
			Session::delete('captche_name');
			//返回验证码状态 1为通过 0为错误1
			return $this->sendMsg(200,'captche ok',array('captche'=> 1));
		}else{
			return $this->sendMsg(400,'captche error',array('captche'=> 0));
		}
	}
}