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
        $query = "SELECT * 
                  FROM mdl_sessions as ms
                  RIGHT JOIN mdl_user as mu 
                  ON ms.userid = mu.id
                  WHERE ms.userid = :uid";
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
   * returns a user object, or false if not found
   *
   */
  public function getenrolments(&$user = NULL) {
    if ($user)  {
      try {
        $query = 'SELECT e.courseid, c.fullname, c.shortname 
        FROM mdl_user as u
        RIGHT JOIN mdl_user_enrolments as ue 
        ON u.id = ue.userid 
        JOIN mdl_enrol as e 
        ON ue.enrolid = e.id 
        JOIN mdl_course as c 
        ON e.courseid = c.id
        WHERE u.id = :sid';
        $stmt = $this->mdb->prepare($query);
        $stmt->execute(array('sid' => $user->id));
       
        $result = $stmt->fetchAll();
        return $result;
        if ( count($result) ) { 
          foreach($result as $row) {
           return $row;
          }   
        }
      } catch(PDOException $e) {
          echo 'ERROR: ' . $e->getMessage();
      }
    }
    return false;
  }
}