<?php
// contact.php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $name = htmlspecialchars(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = htmlspecialchars(trim($_POST["subject"]));
    $message = htmlspecialchars(trim($_POST["message"]));

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Set recipient emails (PLACEHOLDERS - update before going live)
    $to = "main-contact@boleskine.com"; // Primary recipient
    $cc1 = "you@example.com";           // CC recipient 1 (User)
    $cc2 = "colleague@example.com";     // CC recipient 2 (Colleague)

    // Construct headers
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Cc: " . $cc1 . ", " . $cc2 . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Construct email body
    $email_body = "You have received a new message from the contact form.\n\n";
    $email_body .= "Name: " . $name . "\n";
    $email_body .= "Email: " . $email . "\n";
    $email_body .= "Subject: " . $subject . "\n\n";
    $email_body .= "Message:\n" . $message . "\n";

    // Send email
    if (mail($to, "Website Contact: " . $subject, $email_body, $headers)) {
        // Redirect back to contact page with a success anchor or query param
        header("Location: contact.html?status=success");
        exit;
    } else {
        die("There was an error sending your message. Please try again later.");
    }
} else {
    // Not a POST request
    header("Location: contact.html");
    exit;
}
?>
