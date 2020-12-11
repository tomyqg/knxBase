1. Explanation of script service usage:

Scripts in this folder can be called either by
- the user-interface of the openEISS Backend or 
- the cron-daemon for the www user to which this directory belongs.

All scripts should be designed in a way which is indepedant from the way they are called.

Scripts can be either
- bash executables, these should be ending in .job or .bash, or
- php executables, these should be ending in .php

Difference between .job and .bash scripts

.job scripts can be called whatever the current working directory is. Therefor the .job needs to determine the correct script path and
perform a cd to this location.

.bash scripts can assume to be run ONLY from within the script path and do not have to determine the path. .bash files can assume that
'./' refers to the PATH-TO-SCRIPT path.

2. Execution Environments

2.1	crontab

In order to facilitate a single-command crontab entry the following basic "Interface" scripts are available

- jobHourly.job
- jobDaily.job
- jobWeekly.job
- jobWeeklyMon.job, jobWeeklyTue.job, ...
- jobMonthly.job

The jobs should be started on an -asneeded basis through crontab with the web servers user-id.
Edit crontab like follows:

su <provide root credentials as needed>
crontab -u <LINUX:wwwrun, MacOS:_www> -e

Add:

......... to add the interval job

......... to add the daily job

Cron will execute the shell script (job) by calling e.g. '/<PATH-TO-SCRIPT>/jobDaily.job'.
job.Daily.job will perform a cd to the script-directory, i.e. <PATH-TO-SCRIPT>, and execute the command './jobControl.php <SCHEDULE, e.g. daily>'.
jobControl.php will walk through the entire list of jobs meeting the selected <SCHEDULE> and execute these in case the job is enabled.

  cron             jobDaily.job        jobControl.php       job_<sometask>.bash
   |
   +-------<>----------->+
          bash           |
                         +----<SCHEDULE>----->+
                                 bash         |
                                              +----------<>--------->+
                                                  php:system(...)    |
                                                                     |
                                                                     |
                                              +<---------------------+
                                              |
                         +<-------------------+
                         |
   +<--------------------+
   |                       
 

2.2 