## OwOFrame
开源许可证: ![License](https://img.shields.io/badge/License-Apache%202.0-blue.svg) [Learn More](https://opensource.org/licenses/Apache-2.0)

OwO! 欢迎来到本项目. `OwOFrame` 基于 `MVC (Model-Views-Controller)` 模式开发, 框架的制定标准借鉴了 `ThinkPHP` 和 `Laravel`, 因此有部分方法的命名规则看起来与其有相似之处.

------

## What can I do?
> OwOFrame 是我利用个人的空闲时间以及数不尽多少天熬夜开发出来的小框架, 当然也有很多不足之处. 本框架目前支持的功能请参见下列:

#### Basic System Components
- [x] `AppManager`          本框架为多应用共存框架, 通过HTTP_URI识别当前的应用
- [x] `ConfigurationParser` 配置文件解析器
- [x] `Exception`           错误异常抓取及Stack输出(我知道高级的框架都拥有且比我的更好QAQ)
- [ ] `PluginLoader`        插件加载器(独立于Application之外的可灵活更改的一种支持方法)
- [x] `RouterManager`       路由管理器
- [x] `Template`            后端渲染模板(基本功能已经完成, 龟速开发进阶功能o(*￣▽￣*)o)
- [ ] `Terminal`            未来将支持CLI指令操作

#### Basic Util Components
- [x] `CookieClass`         一个普通的Cookie处理类
- [ ] `EmptyAppGenerator`   一键生成新的Application模板
- [ ] `FileUploadClass`     文件上传类支持
- [ ] `OwOFrame`            一个集成化的方法类(具体请看源代码)
- [x] `SessionClass`        一个普通的Session处理类

#### Third Party Resources
- [x] [`I18n`](https://typecho.org/) 由Typecho团队编写的国际化语言支持(未来可能会移除此类库支持)
- [x] [`PasswordHash`](http://www.openwall.com/phpass/) 在此项目中包含/集成了该类库
- [x] [`Think-ORM`](https://github.com/top-think/think-orm) 使用此项目达到了OwOFrame的ModelBase目的实现



## How to use me?
先来介绍一下目录格式吧.

- 引导文件为根目录下的 `index.php`, 通过此文件初始化框架.
- `/backend/OwOFrame.php` 此文件的解释参考上方.
- `/backend/system/` 此目录为框架核心目录.
- `/backend/application/` 此目录为Application目录(默认存在一个作为演示用途的IndexApp).
- `/backend/utils/` 此目录为工具类, 需要时方便调用.
- `/backend/tmp/` 此目录为系统存储的资源文件, 例如运行日志或者配置文件等.
- `/backend/common/` 此目录为系统的公共文件夹, 可存储静态资源文件, 默认的全局配置文件 `global.config` 将存放在此处.

#### 是否需要修改Web环境?
需要. 具体修改方法请参考文件 `/backend/bootstrap.php` 中第32~40行的注释.

------

## Statement
&copy; 2016-2020 [`OwOBlog-DGMT`](https://www.owoblog.com) All Rights Reserved. Please comply with the open source license of this project for modification, derivative or commercial use of this project. The ultimate ownership of this project belongs to HanskiJay(Tommy131).

> My Contact: 
- Website: [`HanskiJay`](https://www.owoblog.com)
- Telegram: [`HanskiJay`](https://t.me/HanskiJay)
- E-Mail: [`HanskiJay`](mailto:support@owoblog.com)