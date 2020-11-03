## Supermetrics


#### Installation and requirements
PHP 7+

No special attention is required, besides the minimum PHP version.

Code was tested using  virtual machine, runing on a custom configured `bento/ubuntu-18.04` with PHP 7.3.13.


#### Execution
Script can be run from command line, by executing `run.php` file or by running `run.sh` script. Both scripts are located in the root of the project.

In the run directory, file `token.json` will be created. This file will hold registration token, for reuse with the script.

After run, file `stats.json` will be created in the run directory of the script, containing required results,

#### Security
Security aspect was disregarded during the build, in order to reduce complexity. Appropriate comments were left in code, where security should be increased.