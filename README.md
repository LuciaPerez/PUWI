PUWI
====
PhpUnit Web Interface
---------------------

###Requirements
It's necessary to have 'git' and 'curl' installed:  
	`sudo apt-get install git`  
	`sudo apt-get install curl php5-curl`

It's necessary too:   
	   PHP >= 5.4.7  
	   xdebug >= 2.2.1

In order to revise the **coverage report**, it's necessary to have 'coverage-html' as logging type in the PHPUnit configuration file and also you have to establish write permissions for the directory where the resultant files of HTML coverage analysis are going to be saved.

### Installing PUWI
1. Download the project from the URL:   
  `git clone https://github.com/LuciaPerez/PUWI.git`

2. Download the correct PHPUnit version to run this project and save it in the PUWI directory.

3. First of all, it's necessary to check in `~/PUWI/config.ini.inc` the following locations:

	 **- serverDirectory** is the directory where *php.ini* file is located, in Apache server is /etc/apache2/

	 **- pubDirectory** is the location where files are published at the web server, in Apache server is /var/www

	 **- runService** indicates the location of the service which must be restarted after modifying *php.ini* file, in Apache server is /etc/init.d/

    2.1. Dependencies

    Install PUWI involves installing automatically the last available PHPUnit version and every dependency that needs to work properly.
    The default location to install these dependencies is *PUWI/vendor/*.
    
    
4. Execute the installation script:  `sudo ~/PUWI/install.bash`

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


Developed by [Lucía Pérez](http://www.linkedin.com/pub/luc%C3%ADa-p%C3%A9rez-fern%C3%A1ndez/77/9a1/227 "Linkedin: Lucía Pérez")
