<?php

/************************************************************************
	 _____   _          __  _____   _____   _       _____   _____  
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___| 
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |     
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _  
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| | 
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/ 
	
	* Copyright (c) 2015-2019 OwOBlog-DGMT All Rights Reserevd.
	* Developer: HanskiJay(Teaclon)
	* Contact: (QQ-3385815158) E-Mail: support@owoblog.com

************************************************************************/

namespace backend\system\console\command;

use backend\system\app\AppManager;

class RemoveAppCommand extends \backend\system\console\CommandBase
{
	public function execute(array $params) : bool
	{
		$appName = strtolower(array_shift($params));
		if(!AppManager::hasApp($appName)) {
			\OwOBootStrap\logger("Cannot find appName called '{$appName}'!");
			return false;
		}
		$answer = \OwOBootStrap\ask('[WARNING] ARE YOU SURE THAT YOU WANT TO DELETE/REMOVE THIS APPLICATION? THIS OPERATION IS IRREVERSIBLE! [Y/N]', 'N');
		if(strtolower($answer) === 'y') {
			\OwOBootStrap\logger('Now will remove this application forever...', 'OwOCMD');
			self::removeDir($path = AppManager::getPath() . $appName . DIRECTORY_SEPARATOR);
			if(!is_dir($path)) {
				\OwOBootStrap\logger("Removed Application '{$appName}' successfully.", 'OwOCMD');
			} else {
				\OwOBootStrap\logger('Somewhere was wrong that cannot remove this application!', 'OwOCMD', 'ERROR');
				return false;
			}
		}
		return true;
	}

	public static function removeDir(string $path) : bool
	{
		if(!is_dir($path)) return false;
		$path = $path . DIRECTORY_SEPARATOR;
		$dirArray = scandir($path);
		unset($dirArray[array_search('.', $dirArray)], $dirArray[array_search('..', $dirArray)]);

		foreach($dirArray as $fileName) {
			if(is_dir($path . $fileName)) {
				self::removeDir($path . $fileName);
				rmdir($path . $fileName);
			} else {
				unlink($path . $fileName);
			}
		}
		rmdir($path);
		return is_dir($path);
	}

	public static function getAliases() : array
	{
		return ['rma', '-rma'];
	}

	public static function getName() : string
	{
		return 'removeapp';
	}

	public static function getDescription() : string
	{
		return 'Look the version the OwOFrame.';
	}
}