<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kings_labs";

// Create a connection with error handling
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to retrieve top 5 customers
$sql = "
SELECT
    c.customer_id,
    CONCAT(c.first_name, ' ', c.last_name) AS customer_name,
    COUNT(o.id) AS total_orders,
    SUM(o.total_amount) AS total_spent
FROM
    orders o
INNER JOIN
    customers c ON o.customer_id = c.customer_id
WHERE
    o.status != 'canceled'
    AND o.order_date >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)
GROUP BY
    c.customer_id, c.first_name, c.last_name
ORDER BY
    total_orders DESC
LIMIT 5;
";

// Execute the query
if ($result = $conn->query($sql)) {
    // Check if there are results
    if ($result->num_rows > 0) {
        // Start the table and add headers
        echo "<table border='1'>
                <tr>
                    <th>Customer Id</th>
                    <th>Customer Name</th>
                    <th>Total Orders</th>
                    <th>Total Spent</th>
                </tr>";
        // Output data of each row
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . htmlspecialchars($row["customer_id"]) . "</td>
                    <td>" . htmlspecialchars($row["customer_name"]) . "</td>
                    <td>" . htmlspecialchars($row["total_orders"]) . "</td>
                    <td>" . htmlspecialchars($row["total_spent"]) . "</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "No results found.";
    }
    // Free result set
    $result->free();
} else {
    echo "Error executing query: " . $conn->error;
}

// Close the connection
$conn->close();
?>
    