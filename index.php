<!-- inspiration for php db connection: https://www.w3schools.com/php/php_mysql_select.asp -->
<html>

<head>
<meta http-equiv="Content-Type" content="text/html"; charset="utf-8" />
</head>

<body>

<?php
include("secrets.php");

function get_active_users($servername, $username, $password, $dbname_challenges){
    $conn = new mysqli($servername, $username, $password, $dbname_challenges);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "SELECT ID
                 , userID
                 , challengeID
                 , challengeState 
            FROM accomplishedChallenges 
            WHERE challengeState > 0";
    $result = $conn->query($sql);
    $conn->close();
    
    return $result;
}

$active_users = get_active_users($SERVERNAME, $USERNAME, $PASSWORD, $DBNAME_CHALLENGES);

// connect to wp data base
$conn = new mysqli($SERVERNAME, $USERNAME, $PASSWORD, $DBNAME_WP);
$conn->query("SET NAMES 'utf8';"); 
$conn->query("SET CHARACTER SET 'utf8';");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// create temp table
$create_temp_table = "CREATE TEMPORARY TABLE IF NOT EXISTS temp_tbl(userID INT, challengeID INT, challengeState INT);";
$creation_res = $conn->query($create_temp_table);

// fill temp table
if ($active_users->num_rows > 0) {
    while($row = $active_users->fetch_assoc()) {
        $insertion_query = "INSERT INTO temp_tbl (userID, challengeID, challengeState)
                                 VALUES ('" . $row["userID"] . "', '" . $row["challengeID"]."', '" . $row["challengeState"] . "')";
        $insertion_res = $conn->query($insertion_query);
        // echo "id: " . $row["ID"]. " - userID: ". $row["userID"]. " - challengeID: ". $row["challengeID"]. "<br>";
    }
} else {
    echo "0 results";
}

$join_query = "
    SELECT userID
         , challengeID
         , challengeState
         , posts.post_title post_title
         , users.user_nicename user_nicename
         , CASE 
                 WHEN challengeState = 2 THEN post_meta.meta_value + post_meta2.meta_value * 2
                 ELSE post_meta.meta_value
             END punktzahl
    FROM temp_tbl tt
    LEFT JOIN 8K7u11mT_users users
           ON tt.userID = users.ID
    JOIN 8K7u11mT_usermeta user_meta
      ON users.ID = user_meta.user_id
    LEFT JOIN 8K7u11mT_posts posts 
      ON tt.challengeID = posts.ID
    JOIN 8K7u11mT_postmeta post_meta
      ON posts.ID = post_meta.post_id
    JOIN 8K7u11mT_postmeta post_meta2
      ON posts.ID = post_meta2.post_id
    WHERE user_meta.meta_key = 'account_status'
    AND user_meta.meta_value != 'inactive'
    AND post_meta.meta_key LIKE 'wpcf%punktzahl'
    AND post_meta2.meta_key LIKE 'wpcf%zusatzpunkte'
    ORDER BY userID ASC
";

$join_result = $conn->query($join_query);

echo "<table>";
echo (
    "<tr>" .
    "<th>userID</th>" .
    "<th>challengeID</th>" .
    "<th>challengeState</hh>" .
    "<th>post_title</th>" . 
    "<th>user_nicename</th>" . 
    "<th>Punktzahl</th>" . 
    "<th>Gesamtpunktzahl</th>" . 
    "</tr>"
);

function print_user_points($user_points){
    echo "<tr> <td></td> <td></td> <td></td> <td></td> <td></td> <td></td> <td><b>" . $user_points . "</b></td></tr>";
}

if ($join_result->num_rows > 0) {
    $current_user = -1;
    $user_points = 0;
    while($row = $join_result->fetch_assoc()) {
        if ($current_user != $row["userID"]){
            if ($current_user != -1){
                print_user_points($user_points);
            }
            $current_user = $row["userID"];
            $user_points = $row["punktzahl"];
        } else{
            $user_points = $user_points + $row["punktzahl"];
        }
        echo (
            "<tr>" . 
            "<td>" . $row["userID"] . "</td>" . 
            "<td>" . $row["challengeID"] . "</td>" . 
            "<td>" . $row["challengeState"] . "</td>" . 
            "<td>" . $row["post_title"] . "</td>" . 
            "<td>" . $row["user_nicename"] . "</td>" . 
            "<td>" . $row["punktzahl"] . "</td>" . 
            "</tr>"
        );
    }
    print_user_points($user_points);
} else {
    echo "0 results";
}
echo "</table>";

$conn->close();
?>

</body>

</html>
