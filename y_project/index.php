<?php
/**
 * Created by PhpStorm.
 * User: BEN
 * Date: 7/10/2017
 * Time: 1:23 AM
 *
 * $db -> query($query) => Used With Select , Update , Delete
 * $db -> exec($query) => Used With Insert
 * $db-> execute($query) => the best use to handle SQL Injection
 * $db->prepare() => must be used before executing the query
 */



session_start();

require_once "db.php";
require 'employees.php';



if (isset($_POST['submit'])){
    $id = filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST,'name',FILTER_SANITIZE_STRING);
    $adress = filter_input(INPUT_POST,'adress',FILTER_SANITIZE_STRING);
    $age = filter_input(INPUT_POST,'age',FILTER_SANITIZE_NUMBER_INT);
    $salary = filter_input(INPUT_POST,'salary',FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $tax = filter_input(INPUT_POST,'tax',FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    $bindParams = array(
        ':name' => $name,
        ':adress' => $adress,
        ':age' => $age,
        ':salary' => $salary,
        ':tax' => $tax);

   if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])){
       $id = filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);
       $sql = 'UPDATE emplyees SET name = :name ,adress = :adress,age = :age,salary = :salary,tax = :tax WHERE id = :id';
       $bindParams[':id'] = $id;
   } else{
       $sql = 'INSERT INTO emplyees SET name = :name ,adress = :adress,age = :age,salary = :salary,tax = :tax';
   }

    //Inserting or Updating Data
    $result = $connect->prepare($sql);
    if ($result->execute($bindParams)){
        $_SESSION['message'] = 'Employee '.$name.' Saved Successfully';
        header('Location: http://localhost/y_project');
        sesssion_write_close();
        exit();
    } else{
        $_SESSION['message'] = 'Error';
    }
}
//UPDATE Employee
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])){
    $id = filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);
    if ($id > 0){
        $sql = "SELECT * FROM emplyees WHERE id = :id";
        $result = $connect->prepare($sql);
        $founderUser = $result->execute(array(':id' => $id));
        if ($founderUser === true){
            $user = $result->fetchAll(PDO::FETCH_ASSOC);
            $user = array_shift($user);

        }
    }
}

//DELETE Employee
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])){
    $id = filter_input(INPUT_GET,'id',FILTER_SANITIZE_NUMBER_INT);
    if ($id > 0){
        $sql = "DELETE FROM emplyees WHERE id = :id";
        $result = $connect->prepare($sql);
        $founderUser = $result->execute(array(':id' => $id));
        if ($founderUser === true){
            $_SESSION['message'] = 'Employee Deleted Successfully';
            header('Location: http://localhost/y_project');
            sesssion_write_close();
            exit();

        }
    }
}
//Reading Data
$select = "SELECT * FROM emplyees";
$stmt = $connect->query($select);




?>

<html>
    <head>
        <title>PDO - Example</title>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="IE=edge">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
    </head>

    <body>
    <div class="wrapper">
        <div class="empFrom">
            <form class="appFrom" method="POST" enctype="application/x-www-form-urlencoded">
                <fieldset>
                    <legend>Employees Information</legend>
                        <?php if (isset($_SESSION['message'])){ ?>
                            <p class="message <?= isset($error) ? 'error' : '' ?>"><?= $_SESSION['message'] ?></p>
                        <?php
                            unset($_SESSION['message']);
                        } ?>
                    <table>
                        <tr>
                            <td>
                                <label for="name">Employees name:</label>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="text" name="name" id="name" placeholder="Name" required maxlength="50" value="<?= isset($user) ? $user['name'] : '' ?>"></td>
                        </tr>
                        <tr>
                            <td>
                                <label for="age">Employees Age:</label>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="number" name="age" id="age" placeholder="Age" min="22" max="60" required value="<?= isset($user) ? $user['age'] : '' ?>"></td>
                        </tr>
                        <tr>
                            <td>
                                <label for="adress">Employees Adress:</label>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="text" name="adress" id="adress" placeholder="Address" required maxlength="100" value="<?= isset($user) ? $user['adress'] : '' ?>"></td>
                        </tr>
                        <tr>
                            <td>
                                <label for="salary">Employees Salary:</label>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="number" name="salary" id="salary" placeholder="Salary"  step="0.01" min="1500" max="9999" required value="<?= isset($user) ? $user['salary'] : '' ?>"></td>
                        </tr>
                        <tr>
                            <td>
                                <label for="tax">Employees Tax(%):</label>
                            </td>
                        </tr>
                        <tr>
                            <td><input type="number" name="tax" step="0.01" id="tax" placeholder="Tax" min="1" max="5" required value="<?= isset($user) ? $user['tax'] : '' ?>"></td>
                        </tr>
                        <tr>
                            <td><input type="submit" name="submit"></td>
                        </tr>
                    </table>
                </fieldset>
            </form>
        </div>
        <div class="employees">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Adress</th>
                        <th>Salary</th>
                        <th>Tax (%)</th>
                        <th>Control</th>
                    </tr>
                </thead>
              <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){ ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['age']; ?></td>
                        <td><?php echo $row['adress']; ?></td>
                        <td><?php echo $row['salary']; ?></td>
                        <td><?php echo $row['tax']; ?></td>
                        <td>
                            <a href="/y_project?action=edit&id=<?php echo $row['id']; ?>"><i class="fa fa-edit"></i></a>
                            <a href="/y_project?action=delete&id=<?php echo $row['id']; ?>" onclick=" if(!confirm('Are you sure you want to delete this employee ?')) return false;"><i class="fa fa-times"></i></a>
                        </td>
                    </tr>

              <?php } ?>
              </tbody>
            </table>

        </div>
    </div>

    </body>
</html>

