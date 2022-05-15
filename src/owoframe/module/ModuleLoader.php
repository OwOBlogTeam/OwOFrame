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
namespace owoframe\module;

use owoframe\MasterManager;
use owoframe\helper\Helper;
use owoframe\object\INI;
use owoframe\utils\Logger;

class ModuleLoader
{
	/**
	 * 模块信息识别文件名称
	 */
	public const IDENTIFY_FILE_NAME = 'info.ini';

	/**
	 * 模块池
	 *
	 * @access private
	 * @var array
	 */
	private static $modulePool = [];


	/**
	 * 自动从加载路径加载模块
	 *
	 * @author HanskiJay
	 * @since  2021-01-23
	 * @param  MasterManager      $master 主进程实例
	 * @return void
	 */
	public static function autoLoad(MasterManager $master) : void
	{
		// try {
			$dirArray = scandir(MODULE_PATH);
			// unset dots and pathname;
			unset($dirArray[array_search('.', $dirArray)], $dirArray[array_search('..', $dirArray)]);
			$path = [];
			foreach($dirArray as $name) {
				if(is_dir($dir = MODULE_PATH . $name . DIRECTORY_SEPARATOR) && is_file($dir . self::IDENTIFY_FILE_NAME)) {
					$path[$name] = $dir;
				}
			}
			foreach($path as $name => $dir) {
				self::loadModule($dir, $name, $master);
			}
		// } catch(\Throwable $e) {

		// }
	}

	/**
	 * 判断模块是否存在
	 *
	 * @author HanskiJay
	 * @since  2021-01-23
	 * @return boolean
	 */
	public static function existsModule(string $name, &$info = null) : bool
	{
		if(isset(self::$modulePool[strtolower($name)])) return true;
		// Start judgment;
		$hisPath = MODULE_PATH . $name . DIRECTORY_SEPARATOR;
		if(!is_dir($hisPath)) return false;
		if(!file_exists($ic = $hisPath . self::IDENTIFY_FILE_NAME)) return false;
		$info = new INI($ic);
		if(!self::checkInfo($info->getAll())) return false;
		$info = $info->obj(); // Format to JSON Object;
		if(!file_exists($hisPath .$info->className . '.php')) return false;
		/*$info->className = str_replace('/', '\\', trim($info->className));
		if(!class_exists($info->className)) return false;
		if(is_bool($c = (new \ReflectionClass($info->className))->getParentClass())) return false;
		if($c->getName() !== ModuleBase::className) return false;*/
		if(isset($info->onlyCLI) && $info->onlyCLI && !Helper::isRunningWithCLI()) return false;
		// End judgment;
		return true;
	}

	/**
	 * 获取模块实例化对象
	 *
	 * @author HanskiJay
	 * @since  2021-02-08
	 * @param  string      $name 模块名称
	 * @return ModuleBase|null
	 */
	public static function getModule(string $name) : ?ModuleBase
	{
		return self::$modulePool[strtolower($name)] ?? null;
	}

	/**
	 * 加载模块
	 *
	 * @author HanskiJay
	 * @since  2021-01-23
	 * @param  string             $dir  模块所在的路径
	 * @param  string             $name 模块名称
	 * @param  MasterManager      $master 主进程实例
	 * @return boolean
	 */
	public static function loadModule(string $dir, string $name, MasterManager $master) : bool
	{
		$logger = Logger::getInstance();
		if(self::existsModule($name, $info)) {
			// include_once($dir . $info->className . '.php');
			$namespace = $info->namespace ?? '';
			$class     = $namespace . '\\' . $info->className;

			if(class_exists($class)) {
				$class = self::$modulePool[strtolower($info->name)] = new $class($dir, $info, $master);
				$class->onLoad();
				$class->setEnabled();
				return true;
			} else {
				$logger->error("ModuleLoader > Load module '{$info->name}' failed: The NameSpace or ClassName may incorrect!");
			}
		} else {
			$logger->error("ModuleLoader > Module '{$name}' not exists! Please check the module information file '" . self::IDENTIFY_FILE_NAME . "' !");
		}
		return false;
	}

	/**
	 * 卸载模块
	 *
	 * @author HanskiJay
	 * @since  2021-02-08
	 * @param  string      $name 模块名称
	 * @return boolean
	 */
	public static function disableModule(string $name) : bool
	{
		$name = strtolower($name);
		if(($module = self::getModule($name)) !== null) {
			self::$modulePool[$name]->onDisable();
			self::$modulePool[$name]->setDisabled();
			unset(self::$modulePool[$name]);
		}
		return false;
	}

	/**
	 * 检查模块信息文件是否有效
	 *
	 * @author HanskiJay
	 * @since  2021-01-23
	 * @param  array      $info 已加载的配置文件
	 * @return boolean
	 */
	public static function checkInfo(array $info, string &$missParam = '') : bool
	{
		return checkArrayValid($info, ['author', 'className', 'name', 'description', 'version', 'priority'], $missParam);
	}
}