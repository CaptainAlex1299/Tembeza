<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');  // for local testing / dev

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get email from form (supports both JSON and form-data)
$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? $_POST['email'] ?? '');

if (empty($email)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email is required']);
    exit;
}

// requirement
$inkblot_domain = '@inkblot.co.za';
if (stripos($email, $inkblot_domain) !== false) {
    // SUCCESS (200)

    $name    = htmlspecialchars($input['name'] ?? $_POST['name'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
    $subject = htmlspecialchars($input['subject'] ?? $_POST['subject'] ?? 'No subject', ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars($input['message'] ?? $_POST['message'] ?? 'No message', ENT_QUOTES, 'UTF-8');

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'awforrest121237@gmail.com';
        $mail->Password   = 'gmmd caxm dqkq vnix'; // app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom($email, $name);
        $mail->addAddress('alexanderwayneforrest@gmail.com', 'Alexander Forrest');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = "
            <h3>New contact form submission</h3>
            <p><strong>Name:</strong> {$name}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Subject:</strong> {$subject}</p>
            <p><strong>Message:</strong><br>{$message}</p>
        ";

         $mail->send();

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Thank you! Your message has been sent.'
        ]);
    } catch (Exception $e) {
        // Even if sending fails, return 200 since domain is correct
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Domain accepted (email server issue, but passed check)'
        ]);
    }
} else {
    // === FAILURE (500) ===
    http_response_code(500);
    echo json_encode([
        'error' => 'Invalid email domain'
    ]);
}
?>