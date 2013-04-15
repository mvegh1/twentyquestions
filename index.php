<?php
require_once('/srv/www/difractal.com/public_html/template.php');
echo "DEBUG: -- CURRENT GUESS: " . $_SESSION['20QGUESS'];

if(!isset($_SESSION['20QNEXTQ'])) {

   $dbcid = new mysqli(..........);  
	 $sql = "SELECT * FROM twenty_questions GROUP BY question ORDER BY RAND()";
	 $result = mysqli_query($dbcid,$sql);
	 $row = mysqli_fetch_assoc($result);
	 $_SESSION['20QNEXTQ'] = $row['question']; 
}

?>



        <div>
		   20 questions!
		   <form action = 'process.php' method = 'POST'>
		   <?php if(!isset($_SESSION['20QGAMEOVER'])) { 
		   
		  ?> Question: <?php echo $_SESSION['20QNEXTQ']; ?>
		   Answer: <input type = 'text' length = '1' name = 'answer'></input>
		   <input type = 'hidden' value = '<?php echo $_SESSION['20QNEXTQ']; ?>' name = 'question' />
		   
		  <?php } 
		  else {
		   if(isset($_SESSION['20QGUESS']) && !isset($_SESSION['20QCONFIRMGUESS']) ) {
            
				  echo "Are you thinking of: " . $_SESSION['20QGUESS'] . "?";
				  echo "<input type = 'text' name = 'confirm' />";
			 } else if(isset($_SESSION['20QCONFIRMGUESS']) && $_SESSION['20QCONFIRMGUESS'] == true) {
			    echo "GOTCHA!";
			 } 
			 
		    else {
		      echo "I have been defeated! Please enter what you were thinking, and a question/answer to help me identify what you thought<br>";
			  echo "My idea: <input type = 'text' name = 'idea' /><br>";
			  echo "Question: <input type = 'text' name = 'newq' /><br>";
			  echo "Answer: <input type = 'text' name = 'newans' />";
		   
		   }
		  }
		  
		  ?>
		   
		   <button id = 'submit'>Submit</button>
		   
		   </form>
		</div>

			</div>
		</div>
	</body>
</html>



