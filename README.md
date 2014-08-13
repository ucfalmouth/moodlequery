#Simple Moodle API

Library of simple objects/functions to allow easier access to moodle course/user information from simple, standalone, php rendered pages.

At present this package must be contained within the moodle directory structure so that it can grab the session cookie to check you are logged in etc.

##Usage

Add config.php file to root directory to use, just copy the moodle config.php and delete everything except the following excerpt:

from

    $CFG = new stdClass();

to 

    $CFG->admin     = 'admin';

(make sure to leave <?php at the top)

The example php file (example.html.php) collects your user data and enrolled courses from moodle as a demonstration.


