<?php
require_once('krumo/class.krumo.php');
require_once('config.php');

// override mysqli for PDO
$CFG->dbtype = ($CFG->dbtype == 'mysqli') ? 'mysql' : $CFG->dbtype;
$conn = new PDO($CFG->dbtype.':host='.''.$CFG->dbhost.';dbname='.$CFG->dbname, "moodle_26", "m768zVWyH3c5Hyez");

// to do - probably should make student and course objects rather than functions
function student_get_course($sid='treadings') {
  global $conn;
  try {
    $stmt = $conn->prepare('SELECT u.username, ue.userid, ue.enrolid, e.enrol, e.courseid, c.fullname, c.shortname 
FROM mdl_user as u
RIGHT JOIN mdl_user_enrolments as ue 
ON u.id = ue.userid 
JOIN mdl_enrol as e 
ON ue.enrolid = e.id 
JOIN mdl_course as c 
ON e.courseid = c.id
WHERE u.username = :sid');
    $stmt->execute(array('sid' => $sid));
   
    $result = $stmt->fetchAll();
   
    if ( count($result) ) { 
      foreach($result as $row) {
        krumo($row);
      }   
    } else {
      echo "No courses found.";
    }
  } catch(PDOException $e) {
      echo 'ERROR: ' . $e->getMessage();
  }
}