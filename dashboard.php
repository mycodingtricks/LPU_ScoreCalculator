<?php
require 'db.php';
$msg = [];
$status = 400;
if(!array_key_exists("userId", $_SESSION)){
    header("Location: ./index.php");
}
$db = new db();
$action = filter_input(INPUT_GET, "action", FILTER_DEFAULT);
if($action=="add"){
    $subject = filter_input(INPUT_POST, "subject", FILTER_DEFAULT);
    $marks = filter_input(INPUT_POST, "marks", FILTER_DEFAULT);
    try{
        $query = $db->connection()->prepare("INSERT INTO user_score (userId, subject, marks) VALUES (:userId, :subject, :marks)");
        $query->bindValue(":userId", $_SESSION["userId"]);
        $query->bindValue(":subject", $subject);
        $query->bindValue(":marks", $marks);
        $query->execute();
        $status = 200;
        $msg[] = "Score added!";
    } catch (Exception $ex) {
        $msg[] = "There's an error: {$ex->getMessage()}";
    }
}
if($action=="delete"){
    $scoreId = filter_input(INPUT_GET, "id");
    try{
        $query = $db->connection()->prepare("DELETE FROM user_score WHERE scoreId=:id AND userId=:userId");
        $query->bindValue(":id", $scoreId);
        $query->bindValue(":userId", $_SESSION["userId"]);
        if($query->execute()){
            $status = 200;
            $msg[] = "Score deleted!";
        }else{
            $msg[] = "Failed to delete score!";
        }
    } catch (Exception $ex) {
        $err[]  = "Error: {$ex->getMessage()}";
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

    <title>Dashboard!</title>
  </head>
  <body>
    <div class="container">
        <div class='row mt-5 mb-5'>
            <?php
            foreach($msg as $m){
                echo "<div class='alert col alert-".(($status==400) ? "danger":"success")."'>{$m}</div>";
            }
            ?>
        </div>
        <div class="row">
            <div class="col shadow-m p-3 m-2 bg-white rounded">
                <h1>Add Score</h1>
                <form action="?action=add" method='POST'>
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" name="subject" id="subject" class='form-control'/>
                    </div>
                    <div class="form-group">
                        <label for="marks">Marks</label>
                        <input type="number" min="0" max="100" step="0.1" name="marks" id="marks" class='form-control'/>
                    </div>
                    <input type="submit" class='btn btn-primary' value='Add Score'/>
                </form>
            </div>
            <div class="col shadow-m p-3 m-2 bg-white rounded">
                <h1>Score</h1>
                <table class="table table-striped">
  <thead class="thead-dark">
    <tr>
      <th scope="col">Subject</th>
      <th scope="col">Marks</th>
      <th scope="col">Percentage</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
    <?php
    try{
        $query = $db->connection()->prepare("SELECT * FROM user_score WHERE userId=:userId");
        $query->bindValue(":userId", $_SESSION["userId"]);
        $query->execute();
        $totalSubject = 0;
        $totalScore = 0;
        while($row = $query->fetch()){
            $totalSubject++;
            $totalScore += $row["marks"];
    ?>
    <tr>
      <th scope="row"><?php echo $row["subject"]; ?></th>
      <td><?php echo $row["marks"]; ?></td>
      <td><?php echo ($row["marks"]/100)*100; ?>%</td>
      <td><a href="?action=delete&id=<?php echo $row["scoreId"]; ?>">Delete</a></td>
    </tr>
    <?php
        }
    } catch (Exception $ex) {

    }
    ?>
  </tbody>
  <tfoot class="thead-light">
      <tr>
          <td colspan="1">Total</td>
          <td><?php echo $totalScore; ?></td>
          <td colspan="2"><strong><?php echo round($totalScore*100/($totalSubject*100),2); ?>%</strong></td>
      </tr>
  </tfoot>
</table>
            </div>
        </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
  </body>
</html>
