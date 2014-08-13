#Simple Moodle API

Library of simple objects/functions to allow easier access to moodle course/user information by simple, standalone, php rendered pages.

Add config.php file to root directory to use, just copy the moodle conffig and delete everything except:

from

    $CFG = new stdClass();

to 

    $CFG->admin     = 'admin';

(make sure to leave <?php at the top)
