<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection parameters
$servername = "localhost"; // Your server
$username = "your_db_username"; // Your database username
$password = "your_db_password"; // Your database password
$dbname = "contactform"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die('Invalid email format');
    }

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO submission (name, email, subject, message) VALUES (?, ?, ?, ?)");
    
    // Check for prepare errors
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssss", $name, $email, $subject, $message);

    // Execute the query
    if ($stmt->execute()) {
        echo "Message saved successfully.";

        // Send email
        $to = "meharamir985@gmail.com"; // Replace with your Gmail address
        $email_subject = "New Contact Form Submission: $subject";
        $email_body = "You have received a new message from your contact form.\n\n";
        $email_body .= "Name: $name\n";
        $email_body .= "Email: $email\n";
        $email_body .= "Subject: $subject\n";
        $email_body .= "Message:\n$message\n";

      

        if (mail($to, $email_subject, $email_body, $headers)) {
            echo " Email sent successfully.";
        } else {
            echo " Failed to send email.";
        }
    } else {
        echo "Failed to save message: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
