<?php
session_start();
if( !isset($_SESSION['questions']) ) {
   $_SESSION['questions'] =  array();
}
  if( isset($_POST['question']) && isset($_POST['answer']) && count($_SESSION['questions']) < 20) {

   $dbcid = new mysqli(......);
	 $q = $dbcid->real_escape_string($_POST['question']);
	 $a = $dbcid->real_escape_string($_POST['answer']);
	 $_SESSION['questions'][$q] = $a;
	 $sql = "SELECT * FROM twenty_questions as t WHERE";
	 $cnt = count($_SESSION['questions']);
	 $iter = 0;

	foreach($_SESSION['questions'] as $key => $value) {
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
	     echo $sql ;
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
		    header("Location: http://difractal.com/20q");
			exit;
		} else {
		    
			$keys = "'" . join("', '", array_keys($_SESSION['questions'])) . "'";
			$guesses = "'" . join("', '", $guesses) . "'";
			 //Grab a question from possible guesses that hasn't been asked yet
			 $sql = "SELECT * FROM twenty_questions WHERE guess IN ($guesses) AND question NOT IN ($keys) ORDER BY RAND()";
			 $result = mysqli_query($dbcid,$sql);
			 $num = mysqli_num_rows($result);
			 //No questions in database. Grab any unasked question now 
			 if($num == 0) {
			    $keys = "'" . join("', '", array_keys($_SESSION['questions'])) . "'";
				 $sql = "SELECT * FROM twenty_questions WHERE question NOT IN ($keys) ORDER BY RAND()";
				 echo $sql;
				 $result = mysqli_query($dbcid,$sql);
				 $num = mysqli_num_rows($result);
				 if($num == 0) {
				 
					$_SESSION['20QGAMEOVER'] = true;
					header("Location: http://difractal.com/20q");		
					exit;

				 }	else {
				 
						$row = mysqli_fetch_assoc($result);
						$nextq = $row['question'];
						$_SESSION['20QNEXTQ'] = $nextq;
						header("Location: http://difractal.com/20q");	
					    exit;
					   var_dump($_SESSION);

					   
				 }
 
			} else {
				$row = mysqli_fetch_assoc($result);
				$nextq = $row['question'];
				$_SESSION['20QNEXTQ'] = $nextq;
				header("Location: http://difractal.com/20q");		
                exit;				
			}
		}
	 } else {
		/*$_SESSION['20QGAMEOVER'] = true;
		header("Location: http://difractal.com/20q");		
		echo "here!";
		exit;	*/
		if($cnt == 20) {
		   $_SESSION['20QGAMEOVER'] = true;
		    header("Location: http://difractal.com/20q");
			exit;
		} else {
		    
			$keys = "'" . join("', '", array_keys($_SESSION['questions'])) . "'";
			$guesses = "'" . join("', '", $guesses) . "'";
			 //Grab a question from possible guesses that hasn't been asked yet
			 $sql = "SELECT * FROM twenty_questions WHERE guess IN ($guesses) AND question NOT IN ($keys) ORDER BY RAND()";
			 $result = mysqli_query($dbcid,$sql);
			 $num = mysqli_num_rows($result);
			 //No questions in database. Grab any unasked question now 
			 if($num == 0) {
			    $keys = "'" . join("', '", array_keys($_SESSION['questions'])) . "'";
				 $sql = "SELECT * FROM twenty_questions WHERE question NOT IN ($keys) ORDER BY RAND()";
				 echo $sql;
				 $result = mysqli_query($dbcid,$sql);
				 $num = mysqli_num_rows($result);
				 if($num == 0) {
				 
					$_SESSION['20QGAMEOVER'] = true;
					header("Location: http://difractal.com/20q");		
					exit;

				 }	else {
				 
						$row = mysqli_fetch_assoc($result);
						$nextq = $row['question'];
						$_SESSION['20QNEXTQ'] = $nextq;
						header("Location: http://difractal.com/20q");	
					    exit;
					   var_dump($_SESSION);
   
				 }
 
			} else {
				$row = mysqli_fetch_assoc($result);
				$nextq = $row['question'];
				$_SESSION['20QNEXTQ'] = $nextq;
				header("Location: http://difractal.com/20q");		
                exit;				
			}
        }			

	 }

  }
  else if(isset($_POST['confirm'])) {
    if($_POST['confirm'] == 'y') {
         $_SESSION['20QCONFIRMGUESS'] = true;
	} else {
	         $_SESSION['20QCONFIRMGUESS'] = false;
	}
		header("Location: http://difractal.com/20q");		
		exit;			
	
  }
  else if(isset($_POST['idea'])) {
    $idea = $_POST['idea'];
  	 $dbcid = new mysqli(......);
	 
	 foreach($_SESSION['questions'] as $key => $value) {
	 
	    $sql = "INSERT INTO twenty_questions (question,answer,guess) VALUES ('$key' , '$value' , '$idea')";
		$result = mysqli_query($dbcid,$sql);
	 }
    if(isset($_POST['newq']) && isset($_POST['newans']) && strlen($_POST['newans']) > 0 ) {
	  $q = $_POST['newq'];
	  $a = $_POST['newans'];
      $sql = "INSERT INTO twenty_questions (question,answer,guess) VALUES ('$q' , '$a' , '$idea')";     
	  $result = mysqli_query($dbcid,$sql);
    }
    unset($_SESSION['20QGAMEOVER']);	
    unset($_SESSION['questions']);	
	unset($_SESSION['20QNEXTQ']);	
	unset($_SESSION['20QGUESS']);	
	unset($_SESSION['20QCONFIRMGUESS']);	
		header("Location: http://difractal.com/20q");		
		exit;			
  }
  

echo "Hello world!";
    unset($_SESSION['20QGAMEOVER']);	
    unset($_SESSION['questions']);	
	unset($_SESSION['20QNEXTQ']);	
	unset($_SESSION['20QGUESS']);	
	unset($_SESSION['20QCONFIRMGUESS']);	
		header("Location: http://difractal.com/20q");		
		exit;

?>
