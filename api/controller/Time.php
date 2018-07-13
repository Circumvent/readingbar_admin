<?php
namespace app\api\controller;

class Time extends Common{
	/**
	 * 获取当前日期
	 * 3.1-8.31第一学期
	 * 9.1-2.29第二学期
	 * @return array 当前的日期（第几学年第几学期第几周）
	 */
	public function what_week(){
		//获取当前的年份
		$year = date('Y');
		//获取当前的月日
		$month = date('md');
		//通过月日来判断当前的学期
		if($month >= 301 && $month <= 831)
			$term = 2;
		else
			$term = 1;
		//第二学期
		if($term == 2){
			//获取当前是本学期的第几周
			$weekNum = $this->count_week('March 01');
			//得到当前学年
			$studyYear = $year - 1 . '-' . $year;
		}
		//第一学期
		else{
			//因为第一学期跨越了两个年份，所以要判断一下
			//如果月份在9-12之间，照常判断
			if(date('m') >= 9){
				//获取当前是本学期的第几周
				$weekNum = $this->count_week('September 01');
			}
			//若果月份在1-2月之间，则需要加上之前的天数。9.1-12.31相隔122天
			else{
				//获取当前是本学期的第几周
				$weekNum = $this->count_week('January 01',122);
			}
			//得到当前学年
			$studyYear = $year . '-' . ($year+1);
		}
		$data = [
			'studyYear'	=> $studyYear,
			'term'		=> $term,
			'week'		=> $weekNum
		];
		return $this->sendMsg(200,'',$data);
	}
	/**
	 * 获取当前第几周的接口（供what_week使用，外面不需调用此接口）
	 * @param  string $date 传入的开始日期（格式：January 01）
	 * @param  int $addDate 要加起来的天数（可以不传）
	 * @return int       当期是本学期的第几周
	 */
	protected function count_week($date,$addDate = 0){
		//把$date转换成时间戳
		$startTime=strtotime($date);
		//通过除法运算得到开始时间距离现在有多少天
		$dateNum=ceil((time()-$startTime)/60/60/24) + $addDate;
		//通过/7获取当前是第几周
		$weekNum = ceil($dateNum / 7);
		return $weekNum;
	}
}