PUWI
====
PhpUnit Web Interface
---------------------

### Installing PUWI
1. Download the project from the URL:   
  `git@github.com:LuciaPerez/PUWI.git`

2. Firs of all, it's necessary to check in `~/PUWI/config.ini.inc` the following locations:

	**serverDirectory** is the directory where *php.ini* file is located

	**pubDirectory** is the location where files are published at the web server

	**runService** indicates the location of the service which must be restarted after modify *php.ini* file.

    2.1. Dependencies.
    The following dependencies will be installed:
    * phpunit 3.7.13
    * dbunit 1.2.2
    * php-code-coverage 1.2.7
    * php-file-iterator 1.3.3
    * php-invoker 1.1.2
    * php-text-template 1.1.4
    * php-timer 1.0.4
    * php-token-stream 1.1.5
    * phpunit-mock-objects 1.2.3
    * phpunit-selenium 1.2.12
    * phpunit-story  1.0.1
    *sebastian/version >= 1.0.0
    *symfony/yaml ~2.0
    
    The default location to install these dependencies is *PUWI/vendor/*.
    
3. Execute the installation script:  `sudo ~/PUWI/install.bash`

### Running tests
To execute tests from a project using PUWI, execute *puwi* command in your project location.
Below an example:
```
~/workspace$ cd MyProject
~/workspace/MyProject$ puwi
  PUWI is running on http://localhost/puwi/...
~/workspace/MyProject$ 
```
Browser is launched in background to show PUWI.
