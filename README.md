PUWI
====
PhpUnit Web Interface
---------------------

### Installing PUWI
1. Download the project from the URL:   
  `git clone url_to_clone/puwi.git`

2. Firs of all, it's necessary to check in `~/PUWI/config.ini.inc` the following locations:

serverDirectory is the directory where *php.ini* file is located
pubDirectory is the location where files are published at the web server
runService indicates the location of the service which must be restarted after modify *php.ini* file.

    2.1. Dependencies.
    The following dependencies will be installed:
    * phpunit
    * dbunit
    * php-code-coverage
    * php-file-iterator
    * php-invoker
    * php-text-template
    * php-timer
    * php-token-stream
    * phpunit-mock-objects
    * phpunit-selenium
    * phpunit-story  
    
    The default location to install these dependencies is *PUWI/vendor/*.
    
3. Execute the installation script:   `sudo ~/PUWI/install.bash`

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
