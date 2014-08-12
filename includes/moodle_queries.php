<?php

require_once('config.php');
$mdl_session_id = $_COOKIE['MoodleSession'];

// override mysqli for PDO
$CFG->dbtype = ($CFG->dbtype == 'mysqli') ? 'mysql' : $CFG->dbtype;
$db = new PDO($CFG->dbtype.':host='.''.$CFG->dbhost.';dbname='.$CFG->dbname, "moodle_26", "m768zVWyH3c5Hyez");


function get_mdl_user() {
  global $db;
  global $mdl_session_id;
  try {
    $query = "SELECT * 
              FROM mdl_sessions as ms
              RIGHT JOIN mdl_user as mu 
              ON ms.userid = mu.id
              WHERE sid = :msid";
    // $query = "SELECT userid FROM mdl_sessions WHERE sid = :msid";
    $stmt = $db->prepare($query);
    $stmt->execute(array('msid' => $mdl_session_id));
    $result = $stmt->fetchObject();
    if ( count($result) ) { 
      return $result; 
    } else {
      return false;
    }
  } catch(PDOException $e) {
      echo 'ERROR: ' . $e->getMessage();
  }
}


// to do - probably should make student and course objects rather than functions
function student_get_course($sid='3') {
  global $db;
  try {
    // $query = 'SELECT u.username, ue.userid, ue.enrolid, e.enrol, e.courseid, c.fullname, c.shortname 
    // FROM mdl_user as u
    // RIGHT JOIN mdl_user_enrolments as ue 
    // ON u.id = ue.userid 
    // JOIN mdl_enrol as e 
    // ON ue.enrolid = e.id 
    // JOIN mdl_course as c 
    // ON e.courseid = c.id
    // WHERE u.username = :sid';
    $query = 'SELECT e.courseid, c.fullname, c.shortname 
    FROM mdl_user as u
    RIGHT JOIN mdl_user_enrolments as ue 
    ON u.id = ue.userid 
    JOIN mdl_enrol as e 
    ON ue.enrolid = e.id 
    JOIN mdl_course as c 
    ON e.courseid = c.id
    WHERE u.id = :sid';
    $stmt = $db->prepare($query);
    $stmt->execute(array('sid' => $sid));
   
    $result = $stmt->fetchAll();
    return $result;
    if ( count($result) ) { 
      foreach($result as $row) {
       return $row;
      }   
    } else {
      return false;
    }
  } catch(PDOException $e) {
      echo 'ERROR: ' . $e->getMessage();
  }
}

function _get_object() {

}

function _get_array() {

}
