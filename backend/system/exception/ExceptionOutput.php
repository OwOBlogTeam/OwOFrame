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
	* Telegram: https://t.me/HanskiJay E-Mail: support@owoblog.com

************************************************************************/

declare(strict_types=1);
namespace backend\system\exception;

use backend\OwOFrame;
use backend\system\utils\LogWriter;

class ExceptionOutput
{

	public static function ErrorHandler($errno, $errstr, $errfile, $errline, $context, $trace = null)
	{
		if(error_reporting() === 0) return false;
		$errorConversion = 
		[
			E_ERROR             => 'E_ERROR',
			E_WARNING           => 'E_WARNING',
			E_PARSE             => 'E_PARSE',
			E_NOTICE            => 'E_NOTICE',
			E_CORE_ERROR        => 'E_CORE_ERROR',
			E_CORE_WARNING      => 'E_CORE_WARNING',
			E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
			E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
			E_USER_ERROR        => 'E_USER_ERROR',
			E_USER_WARNING      => 'E_USER_WARNING',
			E_USER_NOTICE       => 'E_USER_NOTICE',
			E_STRICT            => 'E_STRICT',
			E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
			E_DEPRECATED        => 'E_DEPRECATED',
			E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
		];
		$errno = isset($errorConversion[$errno]) ? $errorConversion[$errno] : $errno;
		if(($pos = strpos($errstr, "\n")) !== false) $errstr = substr($errstr, 0, $pos);
		if(defined("DEBUG_MODE") && DEBUG_MODE) {
			if(!preg_match('/Cannot use "parent" when current class scope has no parent/i', $errstr)) {
				echo "{$errno} happened: {$errstr} in {$errfile} at line {$errline}" . (OwOFrame::isRunningWithCGI() ? ' <br/><br/>' : PHP_EOL);
			}
		}
	}

	public static function ExceptionHandler(\Throwable $exception)
	{
		$encode    = strtoupper(mb_detect_encoding($exception->getMessage(), ["ASCII", 'UTF-8', "GB2312", "GBK", 'BIG5']));
		$type      = "[".((is_object($exception) && ($exception instanceof OwOFrameException)) ? "OwOError" : "PHPError")."] ";
		$type     .= \backend\OwOFrame::getShortClassName($exception);
		$debugMode = (defined("DEBUG_MODE") && DEBUG_MODE) ? '<span id="debugMode">DebugMode</span>' : '';
		$runTime   = \OwOBootstrap\runTime();

		if(OwOFrame::isRunningWithCGI()) {
			if(defined('LOG_ERROR') && LOG_ERROR) {
				$logged = '<span id="logged">--- Logged ---</span>';
				LogWriter::setFileName('owoblog_error.log');
				LogWriter::write(\trim(mb_convert_encoding($exception->__toString(), 'UTF-8', $encode)), 'OwOBlogErrorHandler');
			} else {
				$logged = '';
			}
			echo
<<<EOF
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>程序错误机制显示页面 - OwOFrame</title>
		<style>
			html {
				padding: 50px 10px;
				font-size: 16px;
				font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
				line-height: 1.4;
				color: #666;
				background: #F6F6F3;
				-webkit-text-size-adjust: 100%;
				-ms-text-size-adjust: 100%;
			}
			pre {
				font-size: 14px;
				overflow: hidden;
				text-overflow: ellipsis;
				font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
			}
			.container {
				position: relative;
				margin: 0 auto;
				padding: 30px 20px;
				width: 50%;
				border-radius: 5px;
				box-shadow: 2px 2px 2px #eee;
				background: #FFF;
			}
			p#title {
				text-align: center;
				padding: 10px 5px;
				border-radius: 5px;
				font-weight: bold;
				background-color: #212121;
				color: #FFFF00;
			}
			div#type {
				font-weight: bold;
				color: #FF5722;
			}
			div#message {
				margin: 5px auto;
				padding: 20px;
				font-weight: bold;
				border-radius: 5px;
				background-color: #eee;
				color: #DD2C00;
			}
			span#class {
				font-weight: bold;
				color: #00B8D4;
			}
			span#line {
				font-weight: bold;
				color: #FF6D00;
			}
			span#debugMode {
				padding: 5px 10px;
				font-weight: bold;
				border-radius: 5px;
				background-color: #299c08;
				color: #fff46f;
			}
			span#noPassed {
				padding: 5px 10px;
				font-weight: bold;
				border-radius: 5px;
				background-color: #C51162;
				color: white;
			}
			span#logged {
				padding: 5px 10px;
				font-weight: bold;
				border-radius: 5px;
				background-color: #7576d4;
				color: white;
			}
			div#runTime span{
				padding: 5px;
				width: 150px;
				text-align: center;
				border-radius: 5px;
				background-color: #6D6D82;
				color: white;
			}
		</style>
	</head>
	<body>
		<div class="container">
			<p id="title">OwOBlog Exception Handler &nbsp;{$debugMode}</p>
			<p>Status:
				<span id="noPassed">--- NoPassed ---</span>
				{$logged}
			</p>
			
			<div id="type">{$type}:</div>
			<div id="message">{$exception->getMessage()}</div>
			in <span id="class">{$exception->getFile()}</span> at line <span id="line">{$exception->getLine()}</span>

			<p><b>Stack Trace</b>: <br/><pre>{$exception->getTraceAsString()}</pre></p>
			<div id="runTime"><b>Used Time:</b> <span>{$runTime}s</span></div>
		</div>
	</body>
</html>
EOF;
			exit(1);
		} else {
			// TODO: Display Exception for CLI Mode;
		}
	}
}
?>