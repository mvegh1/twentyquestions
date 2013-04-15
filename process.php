<?php
session_start();

  if( isset($_POST['question']) && isset($_POST['answer']) && count($_SESSION['20Q-QUESTIONS']) < 20) {

	 require_once('db.inc.php');
  	 $dbcid = ConnectToDB();
	 $q = $dbcid->real_escape_string($_POST['question']);
	 $a = $dbcid->real_escape_string($_POST['answer']);
	 $_SESSION['20Q-QUESTIONS'][$q] = $a;
	 $primarycnt = count($_SESSION['20Q-PRIMARYQUESTIONS']);
	 //Ask all 5 primary questions first before guessing
	 if($primarycnt < 5) {
	    $_SESSION['20QNEXTQ'] = GetNextPrimaryQuestion();
		    header("Location: .");
			exit;
	 }
	 
 
	 $sql = "SELECT * FROM twenty_questions as t WHERE";
	 $cnt = count($_SESSION['20Q-QUESTIONS']);
	 $iter = 0;

	foreach($_SESSION['20Q-QUESTIONS'] as $key => $value) {
      $sql .= " guess IN (SELECT guess FROM twenty_questions WHERE question = '$key' AND answer = '$value') ";
	  $iter++;
	  if($iter < $cnt) {
	     $sql .= " AND";
	  }
    }		 
	 $sql .= "GROUP BY t.guess ORDER BY COUNT( 'guess' ) DESC";
	 $result = mysqli_query($dbcid,$sql);
	 $num = mysqli_num_rows($result);
	 $guesses =  array();
	 
	 //Based on asked questions, get all possible guesses
	 if($num >= 1) {

	 	 $row = mysqli_fetch_assoc($result);
	     $guess = $row["guess"];
		 $_SESSION['20QGUESS'] = $guess;
		 $guesses[count($guesses)] = $row["guess"];
		 for($i = 0; $i < $num-1; $i++) {
			 $row = mysqli_fetch_assoc($result);
			 $guesses[count($guesses)] = "'" . $row["guess"] . "'";		   
		 }
		 //20 questions were asked, test the guess
	    if($cnt == 20) {
		   $_SESSION['20QGAMEOVER'] = true;
		    header("Location: .");
			exit;
		} else {
		    
			$keys = "'" . join("', '", array_keys($_SESSION['20Q-QUESTIONS'])) . "'";
			$guesses = "'" . join("', '", $guesses) . "'";
			 //Grab a question from possible guesses that hasn't been asked yet
			 $sql = "SELECT * FROM twenty_questions WHERE guess IN ($guesses) AND question NOT IN ($keys) ORDER BY RAND()";
			 $result = mysqli_query($dbcid,$sql);
			 $num = mysqli_num_rows($result);

			 if($num == 0) {
			 
			 		$_SESSION['20QGAMEOVER'] = true;
					header("Location: .");		
					exit;
				//No questions in database. Grab any unasked question now 
			 /*
			    $keys = "'" . join("', '", array_keys($_SESSION['20Q-QUESTIONS'])) . "'";
				 $sql = "SELECT * FROM twenty_questions WHERE question NOT IN ($keys) ORDER BY RAND()";
				 echo $sql;
				 $result = mysqli_query($dbcid,$sql);
				 $num = mysqli_num_rows($result);
				 if($num == 0) {
				 
					$_SESSION['20QGAMEOVER'] = true;
					header("Location: .");		
					exit;

				 }	else {
				 
						$row = mysqli_fetch_assoc($result);
						$nextq = $row['question'];
						$_SESSION['20QNEXTQ'] = $nextq;
						header("Location: .");	
					    exit;
					   var_dump($_SESSION);

					   
				 }
               */
			} else {
				$row = mysqli_fetch_assoc($result);
				$nextq = $row['question'];
				$_SESSION['20QNEXTQ'] = $nextq;
				header("Location: .");		
                exit;				
			}
		}
	 } else {
		$_SESSION['20QGAMEOVER'] = true;
		header("Location: .");		
		echo "here!";
		exit;	
		
		/*
		if($cnt == 20) {
		   $_SESSION['20QGAMEOVER'] = true;
		    header("Location: .");
			exit;
		} else {
	
	       $result = GetGuesses2();
		   $guesses = array();
		   $num = mysqli_num_rows($result);
			if($num >=1 ) {
				 $row = mysqli_fetch_assoc($result);
				 $guess = $row["guess"];
				 $_SESSION['20QGUESS'] = $guess;
				 $guesses[count($guesses)] = $row["guess"];
				 for($i = 0; $i < $num-1; $i++) {
					 $row = mysqli_fetch_assoc($result);
					 $guesses[count($guesses)] = "'" . $row["guess"] . "'";		   
				 }
			}
	
			$keys = "'" . join("', '", array_keys($_SESSION['20Q-QUESTIONS'])) . "'";
			$guesses = "'" . join("', '", $guesses) . "'";
			 //Grab a question from possible guesses that hasn't been asked yet
			 $sql = "SELECT * FROM twenty_questions WHERE guess IN ($guesses) AND question NOT IN ($keys) ORDER BY RAND()";
			 $result = mysqli_query($dbcid,$sql);
			 $num = mysqli_num_rows($result);
			 //No questions in database. Grab any unasked question now 
			 if($num == 0) {
			    $keys = "'" . join("', '", array_keys($_SESSION['20Q-QUESTIONS'])) . "'";
				 $sql = "SELECT * FROM twenty_questions WHERE question NOT IN ($keys) ORDER BY RAND()";
				 echo $sql;
				 $result = mysqli_query($dbcid,$sql);
				 $num = mysqli_num_rows($result);
				 if($num == 0) {
				 
					$_SESSION['20QGAMEOVER'] = true;
					header("Location: .");		
					exit;

				 }	else {
				 
						$row = mysqli_fetch_assoc($result);
						$nextq = $row['question'];
						$_SESSION['20QNEXTQ'] = $nextq;
						header("Location: .");	
					    exit;
					   var_dump($_SESSION);
   
				 }
 
			} else {
				$row = mysqli_fetch_assoc($result);
				$nextq = $row['question'];
				$_SESSION['20QNEXTQ'] = $nextq;
				header("Location: .");		
                exit;				
			}
        }			
                 */
	 }

  }
  else if(isset($_POST['confirm'])) {
    if($_POST['confirm'] == 'y') {
         $_SESSION['20QCONFIRMGUESS'] = true;
	} else {
	         $_SESSION['20QCONFIRMGUESS'] = false;
	}
		header("Location: .");		
		exit;			
	
  }
  else if(isset($_POST['idea'])) {


  	 require_once('db.inc.php');
  	 $dbcid = ConnectToDB();
	     $idea = $dbcid->real_escape_string($_POST['idea']);
	 
	 foreach($_SESSION['20Q-QUESTIONS'] as $key => $value) {
	 
	    $sql = "INSERT INTO twenty_questions (question,answer,guess) VALUES ('$key' , '$value' , '$idea')";
		$result = mysqli_query($dbcid,$sql);
	 }
    if(isset($_POST['newq']) && isset($_POST['newans']) && strlen($_POST['newans']) > 0  && strlen($_POST['newq']) > 0) {
	 $q = $dbcid->real_escape_string($_POST['newq']);
	 $a = $dbcid->real_escape_string($_POST['newans']);
      $sql = "INSERT INTO twenty_questions (question,answer,guess) VALUES ('$q' , '$a' , '$idea')";     
	  $result = mysqli_query($dbcid,$sql);
    }
    unset($_SESSION['20QGAMEOVER']);	
    unset($_SESSION['20Q-QUESTIONS']);	
	unset($_SESSION['20Q-PRIMARYQUESTIONS']);
	unset($_SESSION['20QNEXTQ']);	
	unset($_SESSION['20QGUESS']);	
	unset($_SESSION['20QCONFIRMGUESS']);	
		header("Location: .");		
		exit;			
  }
  

echo "Hello world!";
    unset($_SESSION['20QGAMEOVER']);	
    unset($_SESSION['20Q-QUESTIONS']);	
    unset($_SESSION['20Q-PRIMARYQUESTIONS']);
	unset($_SESSION['20QNEXTQ']);	
	unset($_SESSION['20QGUESS']);	
	unset($_SESSION['20QCONFIRMGUESS']);	
		header("Location: .");		
		exit;
		
function GetGuesses2() {

  	 require_once('db.inc.php');
  	 $dbcid = ConnectToDB();
	 $sql = "SELECT * FROM twenty_questions as t WHERE";
	 $cnt = count($_SESSION['20Q-QUESTIONS']);
	 $iter = 0;

	foreach($_SESSION['20Q-QUESTIONS'] as $key => $value) {
      $sql .= " guess IN (SELECT guess FROM twenty_questions WHERE question = '$key' AND answer = '$value') ";
	  $iter++;
	  if($iter < $cnt) {
	     $sql .= " OR";
	  }
	  
    }		 
	 $sql .= "GROUP BY t.guess ORDER BY COUNT( 'guess' ) DESC";
     

	 
	 $result = mysqli_query($dbcid,$sql);
	 return $result;

}

function GetNextPrimaryQuestion() {
	 $asked = "'" . join("', '", array_keys($_SESSION['20Q-PRIMARYQUESTIONS'])) . "'";
  	 require_once('db.inc.php');
  	 $dbcid = ConnectToDB();
	 $sql = "SELECT * FROM twenty_questions WHERE priority = 1 AND question NOT IN ($asked) LIMIT 0,1";
	 $result = mysqli_query($dbcid,$sql);
	 $row = mysqli_fetch_assoc($result);
	 $q = $row['question'];
	 $cnt = count($_SESSION['20Q-PRIMARYQUESTIONS']);
	 $_SESSION['20Q-PRIMARYQUESTIONS'][$q] = true;
	 return $q;
}
?>
