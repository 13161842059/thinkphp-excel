<?php

namespace Home\Controller;

use Think\Controller;

class CommonController extends Controller {
	public function _empty() {
		$url = "https://test.com/api/Index/index";
		header ( "Location:" . $url );
		die ();
	}
	
	/**
	 * header头信息支持
	 * 解决ajax请求跨域问题
	 */
	protected function headerinfo() {
		header ( 'Access-Control-Allow-Origin: *' );
		header ( 'Access-Control-Allow-Credentials: TRUE' );
		header ( 'Access-Control-Allow-Methods: GET,POST,OPTIONS' );
		header ( 'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept' );
	}
	
	// 时间转换
	public function getExcelDate($time = '') {
		$date_time = date ( 'Y-m-d', ($time - 25569) * 24 * 60 * 60 );
		return $date_time;
	}
	
	// 根据身份证号码计算年龄
	public function getAgeByID($id) {
		// 过了这年的生日才算多了1周岁
		if (empty ( $id ))
			return '';
		$date = strtotime ( substr ( $id, 6, 8 ) );
		// 获得出生年月日的时间戳
		$today = strtotime ( 'today' );
		// 获得今日的时间戳
		$diff = floor ( ($today - $date) / 86400 / 365 );
		// 得到两个日期相差的大体年数
		// strtotime加上这个年数后得到那日的时间戳后与今日的时间戳相比
		$age = strtotime ( substr ( $id, 6, 8 ) . ' +' . $diff . 'years' ) > $today ? ($diff + 1) : $diff;
		return $age;
	}
	
	// 根据身份证号码获取生日
	public function getBirthday($idcard) {
		if (empty ( $idcard ))
			return null;
		$bir = substr ( $idcard, 6, 8 );
		$year = ( int ) substr ( $bir, 0, 4 );
		$month = ( int ) substr ( $bir, 4, 2 );
		$day = ( int ) substr ( $bir, 6, 2 );
		return $year . "-" . $month . "-" . $day;
	}
	
	// 生成随机验证码
	public function getRandomVerCode() {
		$randNumber = mt_rand ( 100000, 999999 );
		str_shuffle ( $randNumber );
		return $randNumber;
	}
	
	// 计算两个日期相差的月份
	public function getMonthNum($date1, $date2) {
		$date1_stamp = strtotime ( $date1 );
		$date2_stamp = strtotime ( $date2 );
		list ( $date_1 ['y'], $date_1 ['m'], $date_1 ['d'] ) = explode ( "-", date ( 'Y-m-d', $date1_stamp ) );
		list ( $date_2 ['y'], $date_2 ['m'], $date_2 ['d'] ) = explode ( "-", date ( 'Y-m-d', $date2_stamp ) );
		$diff_num = abs ( $date_1 ['y'] - $date_2 ['y'] ) * 12 + $date_2 ['m'] - $date_1 ['m'] + floor ( abs ( $date_1 ['d'] - $date_2 ['d'] ) / 30 );
		return $diff_num;
	}
	
	// 根据身份证号获取性别
	public function getSexByID($cid) {
		// 根据身份证号，自动返回性别
		if (empty ( $cid ))
			return '0';
		$sexint = ( int ) substr ( $cid, 16, 1 );
		return $sexint % 2 === 0 ? '2' : '1';
	}
	
	/**
	 * 求两个日期之间相差的天数
	 * (针对1970年1月1日之后，求之前可以采用泰勒公式)
	 * 
	 * @param string $day1        	
	 * @param string $day2        	
	 * @return number
	 */
	public function diffBetweenTwoDays($day1, $day2) {
		$second1 = strtotime ( $day1 );
		$second2 = strtotime ( $day2 );
		if ($second1 < $second2) {
			$tmp = $second2;
			$second2 = $second1;
			$second1 = $tmp;
		}
		return ($second1 - $second2) / 86400;
	}
	
	// 获取某个时间多少天往后的日期
	public function getdays($day_time, $day_num = 7) {
		for($i = 1; $i <= $day_num; $i ++) {
			if ($i == 1) {
				$day = $day_time;
			} else {
				$day = date ( "Y-m-d", strtotime ( $day_time ) + 24 * 60 * 60 * ($i - 1) );
			}
			$days [$i - 1] = $day;
		}
		return $days;
	}
	
	// 根据入职日期判断处于那一阶段
	public function getStageByDayNum($day_num) {
		$day_num = ( int ) $day_num;
		if ($day_num >= 0 && $day_num <= 30) {
			$stage = 1;
		} elseif ($day_num >= 31 && $day_num <= 90) {
			$stage = 2;
		} elseif ($day_num >= 91) {
			$stage = 3;
		}
		return $stage;
	}
	
	// 过滤emoij表情符号
	public function filterEmoji($str) {
		$str = preg_replace_callback ( '/./u', function (array $match) {
			return strlen ( $match [0] ) >= 4 ? '' : $match [0];
		}, $str );
		
		return $str;
	}
	
	/**
	 * 权限分配
	 * 
	 * @param int $userid        	
	 * @return number
	 */
	public function getAccessPermission($user_id = '') {
		$user_model = D ( "User" );
		$authority = 0;
		$top_permission = array (
				'mayun@126.com',
				'liuqiangdong@126.com',
				'mahuateng@126.com',
				'liyanhong@126.com',
				'leijun@126.com' 
		);
		
		$user_where ['user_id'] = $user_id;
		$userinfo = $user_model->getUserInfo ( $user_where );
		$user_email = isset ( $userinfo ['email'] ) ? $userinfo ['email'] : '';
		// 判断当前用户是否是五人最高权限者
		$isin = in_array ( $user_email, $top_permission );
		if ($isin) {
			$authority = 1;
		}
		
		return $authority;
	}
}