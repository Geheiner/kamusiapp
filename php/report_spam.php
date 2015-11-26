<?php
$wordID = $_GET['wordID'];
$definitionID = $_GET['definitionID'];
$userID = $_GET['userID'];


//Increment user reports
$sql = "UPDATE users SET NumReports = NumReports + 1 WHERE userID=?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();


$sql = "SELECT * FROM users WHERE userID=?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $userID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
$result_array = $result->fetch_assoc();

if($result_array["Mute"]) { //User has been muted before--ignore
    exit();
}

$num_reports = $result_array["NumReports"];

$sql = "SELECT * FROM words WHERE ID=?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $wordID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
$results_array = $result->fetch_assoc();
$word = $results_array["Word"];

$sql = "SELECT * FROM definitions WHERE ID=?;";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $definitionID);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
$results_array = $result->fetch_assoc();
$definition = $results_array["Definition"];

$sql =  "SELECT * FROM admin;";
    $stmt = $mysqli->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

while ($row = $result->fetch_assoc()) {
    $alias = $row["Alias"];
    $to = $row["Email"];

    $root_link = "http://ec2-54-186-29-34.us-west-2.compute.amazonaws.com/php/";
    $remove_link = $root_link . "remove_spam.php?definitionID=$definitionID";
    $mute_link = $root_link . "mute_user.php?userID=$userID";

    $headers = "From: " . "Kamusi GAME" . "\r\n"
             . "MIME-Version: 1.0\r\n"
             . "Content-Type: text/html; charset=ISO-8859-1\r\n";

    $message = "<html><body>"
             . "<img src='http://kamusi.org/sites/kamusi.org/themes/bs_Kamusi/logo.png' alt='Kamusi GAME' />"
             . "</br>"
             . '<table style="border-color: #000;" cellpadding="10">'
             . "<tr style='background: #ccc;'><td><strong>User:</strong> </td><td>"
             . "$userID</td></tr>"
             . "<tr><td><strong>Word:</strong> </td><td>$word"
             . "</td></tr>"
             . "<tr style='background: #ccc;'><td><strong>Definition:</strong>"
             . "</td><td>$definition</td></tr>"
             . "<tr><td><strong>User reports:</strong> </td><td>$num_reports</td></tr>"
             . "<tr style='background: #ccc;'><td><a href='$remove_link'>"
             . "Mute spammer</a></td><td><a href='$mute_link'>Mute reporter</a></td></tr>"
             . "</table>"
             . "</body></html>";

    $subject = "Kamusi GAME spam report";

    if (mail($to, $subject, $message, $headers)) {
        echo("<p>Email successfully sent!</p>");
    }
    else {
        echo("<p>Email delivery failed…</p>");
    }
}

?>
