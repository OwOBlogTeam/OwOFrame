<?php

/*********************************************************************
	 _____   _          __  _____   _____   _       _____   _____
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___|
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| |
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/

	* Copyright (c) 2015-2021 OwOBlog-DGMT.
	* Developer: HanskiJay(Tommy131)
	* Telegram:  https://t.me/HanskiJay
	* E-Mail:    support@owoblog.com
	* GitHub:    https://github.com/Tommy131

**********************************************************************/

declare(strict_types=1);
namespace owoframe\database;

use owoframe\exception\OwOFrameException;
use owoframe\object\INI;

use think\facade\Db;

class DbConfig extends Db
{
	/* @array ThinkPHP-ORM 数据库配置文件 */
	private static $dbConfig = [];

	public static function init() : void
	{
		static::$dbConfig =
		[
			'default' => INI::_global('mysql.default', 'mysql'),
			'connections' =>
			[
				INI::_global('mysql.default', 'mysql') =>
				[
					// 数据库类型
					'type'     => INI::_global('mysql.type', 'mysql'),
					// 主机地址
					'hostname' => INI::_global('mysql.hostname', '127.0.0.1'),
					// 用户名
					'username' => INI::_global('mysql.username', 'root'),
					// 密码
					'password' => INI::_global('mysql.password', '123456'),
					// 数据库名
					'database' => INI::_global('mysql.database', 'owocloud'),
					// 数据库编码默认采用utf8mb4
					'charset'  => INI::_global('mysql.charset', 'utf8mb4'),
					// 数据库表前缀
					'prefix'   => INI::_global('mysql.prefix', 'owo_'),
					// 数据库调试模式
					'debug'    => true
				]
			]
		];
		self::setConfig(static::$dbConfig);
		// 定义初始化标识;
		if(!defined('DB_INIT')) {
			define('DB_INIT', true);
		}
	}

	/**
	 * 设置默认数据库连接配置
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $default 配置文件标识
	 * @return void
	*/
	public static function setDefault(string $default) : void
	{
		if(self::hasDbConfig($default)) {
			static::$dbConfig['default'] = $default;
		}
		throw new OwOFrameException("Database configuration '{$default}' doesn't exists!");
	}

	/**
	 * 获取默认的配置文件
	 *
	 * @author HanskiJay
	 * @since  2021-01-09
	 * @param  string      $index   键名
	 * @param  mixed       $default 默认返回值
	 * @return mixed
	 */
	public static function getDefault(string $index, $default ='')
	{
		return static::$dbConfig['connections'][static::$dbConfig['default']][$index] ?? $default;
	}

	/**
	 * 获取数据库配置中的某个元素
	 *
	 * @author HanskiJay
	 * @since  2020-09-19 18:03
	 * @param  string      $index   配置索引
	 * @param  string      $default 默认返回值
	 * @return string
	*/
	public static function getIndex(string $index, string $default = '') : string
	{
		return static::$dbConfig[$index] ?? DbConfig::getDefault($index) ?? $default;
	}

	/**
	 * 获取数据库配置
	 *
	 * @author HanskiJay
	 * @since  2020-09-19
	 * @return string
	*/
	public static function getAll() : array
	{
		return static::$dbConfig;
	}

	/**
	 * 设置数据库配置某项元素的值
	 *
	 * @author HanskiJay
	 * @since  2020-09-19
	 * @param  string      $index 配置索引
	 * @param  string      $value 更新值
	 * @return void
	*/
	public static function setIndex(string $index, string $value) : void
	{
		// if(isset(DbConfig::DEFAULT_DB_CONFIG[$index])) {
			static::$dbConfig[$index] = $value;
		// }
	}

	/**
	 * 判断是否存在某一个数据库配置文件
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $nickName 配置文件标识
	 * @return boolean
	*/
	public static function hasDbConfig(string $nickName) : bool
	{
		return isset(static::$dbConfig['connections'][$nickName]);
	}

	/**
	 * 组合数据库配置文件;
	 *
	 * @author HanskiJay
	 * @since  2020-09-10
	 * @param  string      $nickName 配置文件标识
	 * @param  array       $dbConfig 传入的数据
	 * @return void
	*/
	public static function addConfig(string $nickName, array $dbConfig) : void
	{
		static::$dbConfig['connections'][$nickName] = $dbConfig;
	}

}