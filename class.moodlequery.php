<?php

class MoodleQuery
{
  private $config = NULL;
  private $mdb = NULL;

  public function __construct($CFG)
  { 
    $this->config = $CFG;
    // override mysqli for PDO
    $this->config->dbtype = ($this->config->dbtype == 'mysqli') ? 'mysql' : $this->config->dbtype;
    $db = new PDO($this->config->dbtype.':host='.''.$this->config->dbhost.';dbname='.$this->config->dbname, "moodle_26", "m768zVWyH3c5Hyez");

    if (is_object($db)) {
      $this->mdb = $db;
      return true;
    } else {
      return false;
    }
  }

  /*
   * gets a moodle user (eg student) from the moodle database
   *
   * accepts session id or user id as parameter
   * returns a user object, or false if not found
   *
   */
  public function getuser($studentid = NULL) {

    if (is_string($studentid))  {
      try {
        $query = "SELECT * 
                  FROM mdl_sessions as ms
                  RIGHT JOIN mdl_user as mu 
                  ON ms.userid = mu.id
                  WHERE ms.sid = :msid";
        $stmt = $this->mdb->prepare($query);
        $stmt->execute(array('msid' => $studentid));
        $result = $stmt->fetchObject();
        if ( count($result) ) { 
          return $result; 
        }
      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      }
    } else {
      try {
        $query = "SELECT * FROM
                  mdl_user as mu
                  WHERE mu.id = :uid";
        $stmt = $this->mdb->prepare($query);
        $stmt->execute(array('uid' => $studentid));
        $result = $stmt->fetchObject();
        if ( count($result) ) { 
          return $result; 
        }
      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      }
    }
    return false;
  }
  /*
   * gets enrolments for moodle user (eg student) from the moodle database
   *
   * accepts user object as parameter
   * returns an array of course objects, or false if not found
   *
   */
  public function getenrolments(&$user = NULL) {
    if (is_object($user)) {
      try {
        $query = 'SELECT c.*
        FROM mdl_user as u
        RIGHT JOIN mdl_user_enrolments as ue 
        ON u.id = ue.userid 
        JOIN mdl_enrol as e 
        ON ue.enrolid = e.id 
        JOIN mdl_course as c 
        ON e.courseid = c.id
        WHERE u.id = :uid
        AND c.visible = 1';
        $stmt = $this->mdb->prepare($query);
        $stmt->execute(array('uid' => $user->id));
        // $result = $stmt->fetchAll();
        // return $result;
   
        // todo - fetchall then call constructor for course object (eg add path etc)
        $courses = array();
        while (is_object($course = $stmt->fetchObject())) {
          $courses[] = $this->getcourse($course);
          // $courses[] = $course;
        }
        return $courses;

      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      }
    }
    return false;
  }

  public function getaspire(&$user = NULL) {
    try {
        $query = "SELECT * FROM mdl_config_plugins
        WHERE plugin = 'mod_aspirelists'
        OR plugin = 'aspirelists'";
        $stmt = $this->mdb->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
        
      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      }
  }

  private function getcourse($course) {
    if (is_object($course)) {
      // todo - given course details, call constructor for course object
      // (in meantime just add details like path to course object)
      $course->url = $this->config->wwwroot.'/course/view.php?id='.$course->id;

    } 
    else if (is_numeric($course)) {
      // todo - query to get course info from course id
      
    }
    return $course;
  }

}