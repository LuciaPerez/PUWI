PUWI
====
PhpUnit Web Interface
---------------------

### Installing PUWI:
1. Download the project from the URL:   
  `git clone url_to_clone/puwi.git`

2. If you want to use your own PHPUnit dependencies, modify the default location in the config file:  
  `~/puwi/config.ini`    

    2.1. Dependencies.
    The following are dependencies to install:
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
    
    The default location for install these dependencies is */vendor*.
    
3. Execute the installation script:   `~/puwi/install.bash`

### Running tests
For execute tests from a project using PUWI, execute *puwi* command in your project location.
Below an example:
```
~/workspace$ cd MyProject
~/workspace/MyProject$ puwi
  PUWI is running on http://localhost/puwi/...
~/workspace/MyProject$ 
```
Browser is launched in background to show PUWI.
