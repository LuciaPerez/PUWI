PUWI
====
PhpUnit Web Interface
---------------------

###Requirements
It's necessary to have 'git' and 'curl' installed:  
	`sudo apt-get install git`  
	`sudo apt-get install curl php5-curl`

It's necessary too:   
	   PHP > 5.3.3  
	   xdebug >= 2.2.1

### Installing PUWI
1. Download the project from the URL:   
  `git clone https://github.com/LuciaPerez/PUWI.git`

2. First of all, it's necessary to check in `~/PUWI/config.ini.inc` the following locations:

	 **- serverDirectory** is the directory where *php.ini* file is located, in Apache server is /etc/php5/apache2/

	 **- pubDirectory** is the location where files are published at the web server, in Apache server is var/www

	 **- runService** indicates the location of the service which must be restarted after modify *php.ini* file, in Apache server is /etc/init.d/

    2.1. Dependencies

    Install PUWI involves install automatically the last available PHPUnit version and every dependency that needs to work properly.
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
