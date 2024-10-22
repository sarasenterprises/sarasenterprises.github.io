<?php

// Import the Postmark Client Class:
require_once('../vendor/autoload.php');
use Postmark\PostmarkClient;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST["subject"]));
    $message = trim($_POST["message"]);

    if (empty($name) || empty($subject) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Invalid input
        echo "Please complete the form and try again.";
        exit;
    }

    // Initialize Postmark client
    $client = new PostmarkClient("ad4cd163-6f2b-463c-bdf8-8b5682a8935a");
    $client::$VERIFY_SSL = false;
    $fromEmail = "shreyas.habade@stonybrook.edu";
    $toEmail = "shabade@cs.stonybrook.edu";
    $htmlBody = "<strong>Name: </strong>$name<br><strong>Email: </strong>$email<br><br><strong>Message:</strong><br>$message";
    $textBody = "";
    $tag = "example-email-tag";
    $trackOpens = true;
    $trackLinks = "None";
    $messageStream = "outbound";

    // Handle attachments
    $attachments = [];

    if (isset($_FILES["attachment"]) && $_FILES["attachment"]["error"] == 0) {
        $attachments[] = [
            'Name' => $_FILES["attachment"]["name"],
            'Content' => base64_encode(file_get_contents($_FILES["attachment"]["tmp_name"])),
            'ContentType' => mime_content_type($_FILES["attachment"]["tmp_name"])
        ];
    }

    // Send an email
    try {
        $sendResult = $client->sendEmail(
            $fromEmail,
            $toEmail,
            $subject,
            $htmlBody,
            $textBody,
            $tag,
            $trackOpens,
            NULL, // Reply To
            NULL, // CC
            NULL, // BCC
            NULL, // Header array
            $attachments, // Attachment array
            $trackLinks,
            NULL, // Metadata array
            $messageStream
        );

        echo "Your message has been sent successfully.";
    } catch (Exception $e) {
        echo "There was a problem sending your message. Please try again. Error: " . $e->getMessage();
    }
}

?>
