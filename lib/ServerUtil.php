<?php
/**
* 服务器相关类库
*/
class ServerUtil {
	/**
	* 获取课件文件播放地址
	* 会根据servers.config中的服务器地址进行处理，如果多个，则会随机取值
	* 取值策略可以根据此处调整
	*/
	public function getCourseSource() {
		$serverlist = Ebh::app()->getConfig()->load('servers');
        $source = '';
		if(empty($serverlist))
			return $source;
		$scount = count($serverlist);
		if($scount == 1) {
			$source = $serverlist[0];
		} else {
			$spos = rand(0, $scount - 1);
			$source = $serverlist[$spos];
		}
		$source = 'http://'.$source.'/';
		return $source;
	}
	/**
	* 获取课件文件 m3u8 协议播放地址
	* 会根据servers.config中的服务器地址进行处理，如果多个，则会随机取值
	* 取值策略可以根据此处调整
	*/
	public function getM3u8CourseSource() {
		$serverlist = Ebh::app()->getConfig()->load('m3u8servers');
        $source = '';
		if(empty($serverlist))
			return $source;
		$scount = count($serverlist);
		if($scount == 1) {
			$source = $serverlist[0];
		} else {
			$spos = rand(0, $scount - 1);
			$source = $serverlist[$spos];
		}
		return $source;
	}
	/**
	* 获取课件文件rtmp协议播放地址
	* 会根据servers.config中的服务器地址进行处理，如果多个，则会随机取值
	* 取值策略可以根据此处调整
	*/
	public function getRtmpCourseSource() {
		$serverlist = Ebh::app()->getConfig()->load('rtmpservers');
        $source = '';
		if(empty($serverlist))
			return $source;
		$scount = count($serverlist);
		if($scount == 1) {
			$source = $serverlist[0];
		} else {
			$spos = rand(0, $scount - 1);
			$source = $serverlist[$spos];
		}
		return $source;
	}
}