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
namespace owoframe\application;

use owoframe\exception\InvalidControllerException;
use owoframe\module\{ModuleBase, ModuleLoader};

abstract class AppBase
{
	/* @AppBase 返回本类实例 */
	protected static $instance = null;

	/* @string 当前的App访问地址 */
	protected $currentSiteUrl = null;
	/* @string 默认控制其名称 */
	protected $defaultController = '';

	/* @array 不允许通过路由请求的控制器(方法)组 */
	protected $controllerFilter = [];



	public function __construct(string $siteUrl)
	{
		if(static::$instance === null) {
			static::$instance = $this;
		}
		$this->currentSiteUrl = $siteUrl;
		$this->initialize();
	}

	/**
	 * @method      isControllerMethodBanned
	 * @description 判断控制器的方法是否不允许直接访问
	 * @author      HanskiJay
	 * @doneIn      2021-04-30
	 * @param       string                   $controllerName 控制器名
	 * @param       string                   $methodName     方法名
	 * @return      boolean
	 */
	public function isControllerMethodBanned(string $controllerName, string $methodName) : bool
	{
		$controllerName =ucfirst(strtolower($controllerName));
		return isset($this->controllerFilter[$controllerName]) && in_array($methodName, $this->controllerFilter[$controllerName]);
	}

	/**
	 * @method      banControllerMethod
	 * @description 禁止通过路由请求此控制器的方法
	 * @author      HanskiJay
	 * @doneIn      2021-04-29
	 * @param       string              $controllerName 控制器名
	 * @param       array               $args           多选方法名组
	 * @return      void
	 */
	public function banControllerMethod(string $controllerName, array $args) : void
	{
		$controllerName = ucfirst(strtolower($controllerName));
		if(!$this->getController($controllerName, false)) {
			throw new InvalidControllerException(static::getName(), $controllerName);
		}
		if(!isset($this->controllerFilter[$controllerName])) {
			$this->controllerFilter[$controllerName] = [];
		}
		$this->controllerFilter[$controllerName] = array_merge($this->controllerFilter[$controllerName], $args);
	}

	/**
	 * @method      allowControllerMethod
	 * @description 允许通过路由请求此控制器的方法
	 * @author      HanskiJay
	 * @doneIn      2021-04-29
	 * @param       string              $controllerName 控制器名
	 * @param       array               $args           多选方法名组
	 * @return      void
	 */
	public function allowControllerMethod(string $controllerName, array $args) : void
	{
		$controllerName = ucfirst(strtolower($controllerName));
		if(!$this->getController($controllerName, false)) {
			throw new InvalidControllerException(static::getName(), $controllerName);
		}
		foreach($args as $key => $methodName) {
			if($this->isControllerMethodBanned($controllerName, $methodName)) {
				unset($this->controllerFilter[$controllerName][$key]);
			}
		}
		ksort($this->controllerFilter);
	}

	/**
	 * @method      setDefaultController
	 * @description 设置默认控制器
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @param       string      $defaultController 默认控制器名称
	 * @return      void
	*/
	public function setDefaultController(string $defaultController) : void
	{
		if(!$this->getController($defaultController, false)) {
			throw new InvalidControllerException(static::getName(), $defaultController);
		}
		$this->defaultController = $defaultController;
	}

	/**
	 * @method      getDefaultController
	 * @description 获取默认控制器
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @param       bool      $returnName 返回控制器名称
	 * @return      string|ControllerBase
	*/
	public function getDefaultController(bool $returnName = false)
	{
		return $returnName ? $this->defaultController : $this->getController($this->defaultController);
	}

	/**
	 * @method      getController
	 * @description 获取一个有效的控制器
	 * @description Return a valid Controller
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @param       string      $controllerName 控制器名称
	 * @return      boolean|ControllerBase
	*/
	public function getController(string $controllerName, bool $autoMake = true)
	{
		$controller = '\\application\\' . static::getName() . '\\controller\\' . $controllerName;
		if(class_exists($controller)) {
			return ($autoMake) ? new $controller($this) : true;
		} else {
			return false;
		}
	}

	/**
	 * @method      getCachePath
	 * @description 返回本Application的Cache目录
	 * @author      HanskiJay
	 * @doneIn      2021-03-14
	 * @param       string     $option 可选参数(文件/文件夹路径)
	 * @return      string
	 */
	public static function getCachePath(string $option = '') : string
	{
		return A_CACHE_PATH . static::getName() . DIRECTORY_SEPARATOR . $option;
	}

	/**
	 * @method      getResourcePath
	 * @description 返回本Application的资源目录
	 * @author      HanskiJay
	 * @doneIn      2021-08-14
	 * @param       string     $option 可选参数(文件/文件夹路径)
	 * @return      string
	 */
	public static function getResourcePath(string $option = '') : string
	{
		return RESOURCE_PATH . static::getName() . DIRECTORY_SEPARATOR . $option;
	}

	/**
	 * @method      getAppPath
	 * @description 获取当前App目录
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @param       bool      $selectMode 选择模式[True: 返回绝对路径|Return absolute path][False: 返回相对路径|Return relative path]](Default:true)
	 * @return      string
	*/
	final public static function getAppPath(bool $selectMode = true) : string
	{
		return (($selectMode) ? AppManager::getPath() : static::getNameSpace()) . static::getName() . DIRECTORY_SEPARATOR;
	}

	/**
	 * @method      getNameSpace
	 * @description 自动解析并返回当前App的命名空间
	 * @description Automatically parse and return the namespace of the current App
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @return      string
	*/
	final public static function getNameSpace() : string
	{
		$ns = explode("\\", __CLASS__);
		return implode("\\", array_slice($ns, 0, count($ns) - 1));
	}

	/**
	 * @method      getModule
	 * @access      protected
	 * @description 获取模块实例化对象
	 * @author      HanskiJay
	 * @doneIn      2021-02-08
	 * @param       string      $name 插件名称
	 * @return      null|ModuleBase
	 */
	final protected function getModule(string $name) : ?ModuleBase
	{
		return ModuleLoader::getModule($name);
	}

	/**
	 * @method      getCurrentSiteUrl
	 * @description 返回当前站点Url
	 * @author      HanskiJay
	 * @doneIn      2020-08-08
	 * @return      string
	*/
	public function getCurrentSiteUrl() : string
	{
		return $this->currentSiteUrl;
	}

	/**
	 * @method      getInstance
	 * @description 返回本类实例
	 * @description Return this class instance
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @return      null|@AppBase
	*/
	public static function getInstance() : ?AppBase
	{
		return static::$instance ?? null;
	}



	/* 抽象化方法 | Abstraction Methods */

	/**
	 * @method      initialize
	 * @description 初始化App时自动调用该方法
	 * @description A Method for when the Application in initialization
	 * @author      HanskiJay
	 * @doneIn      2020-09-10
	 * @return      void
	*/
	abstract public function initialize() : void;

	/**
	 * @method      autoTo404Page
	 * @description 告知路由组件是否自动跳转到404页面(如果指定)
	 * @description Tell the Router whether to automatically jump to the 404 page (if specified)
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @return      boolean
	*/
	abstract public static function autoTo404Page() : bool;

	/**
	 * @method      getName
	 * @description 获取App名称
	 * @author      HanskiJay
	 * @doneIn      2020-09-09
	 * @return      string
	*/
	abstract public static function getName() : string;
}
?>