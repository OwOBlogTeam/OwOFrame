<?php

/************************************************************************
	 _____   _          __  _____   _____   _       _____   _____
	/  _  \ | |        / / /  _  \ |  _  \ | |     /  _  \ /  ___|
	| | | | | |  __   / /  | | | | | |_| | | |     | | | | | |
	| | | | | | /  | / /   | | | | |  _  { | |     | | | | | |  _
	| |_| | | |/   |/ /    | |_| | | |_| | | |___  | |_| | | |_| |
	\_____/ |___/|___/     \_____/ |_____/ |_____| \_____/ \_____/

	* Copyright (c) 2015-2019 OwOBlog-DGMT.
	* Developer: HanskiJay(Teaclon)
	* Telegram: https://t.me/HanskiJay
	* E-Mail: support@owoblog.com
	*
	* 此配置文件为域名绑定规则的配置文件.
	* This configuration is for bind domain(s) to application.

************************************************************************/
use owoframe\http\route\DomainRule as Domain;

// Domain::bind('example.com', Domain::TAG_BIND_TO_APPLICATION, 'appName');
// Domain::bind('example.com', Domain::TAG_BIND_TO_URL, 'https:://{domain}/appName/...');