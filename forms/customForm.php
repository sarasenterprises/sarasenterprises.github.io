<?php
require_once('../vendor/autoload.php');
use Postmark\PostmarkClient;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $name = strip_tags(trim($_POST["name"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = strip_tags(trim($_POST["subject"]));
    $message = trim($_POST["message"]);

    // Basic validation
    if (empty($name) || empty($subject) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Please complete the form and try again.";
        exit;
    }

    // Postmark config
    $client = new PostmarkClient("ad4cd163-6f2b-463c-bdf8-8b5682a8935a");
    $fromEmail = "";
    $toEmail = "shreyas.habade18@gmail.com";

    $htmlBody = "<strong>Name:</strong> $name<br><strong>Email:</strong> $email<br><br><strong>Message:</strong><br>$message";
    $textBody = strip_tags($htmlBody);
    $attachments = [];

    // File attachment
    if (isset($_FILES["attachment"]) && $_FILES["attachment"]["error"] == UPLOAD_ERR_OK) {
        $fileTmp = $_FILES["attachment"]["tmp_name"];
        $fileName = basename($_FILES["attachment"]["name"]);
        $fileType = mime_content_type($fileTmp);
        $fileContent = base64_encode(file_get_contents($fileTmp));

        $attachments[] = [
            'Name' => $fileName,
            'Content' => $fileContent,
            'ContentType' => $fileType
        ];
    }

    // Attempt to send
    try {
        $client->sendEmail(
            $fromEmail,
            $toEmail,
            $subject,
            $htmlBody,
            $textBody,
            "contact-form",
            true, // trackOpens
            null, null, null, null, // ReplyTo, CC, BCC, Headers
            $attachments,
            "None", // trackLinks
            null,   // Metadata
            "outbound"
        );

        echo "Your message has been sent successfully.";
    } catch (Exception $e) {
        echo "There was a problem sending your message. Please try again. Error: " . $e->getMessage();
    }
}
?>
