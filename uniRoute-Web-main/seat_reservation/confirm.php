<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// Include PHPMailer autoloader
require '../vendor/autoload.php';

$conn = new mysqli("localhost", "root", "pass", "uniroot");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data safely
$seat_id = isset($_POST['seat_id']) ? (int)$_POST['seat_id'] : null;
$bus_id = isset($_POST['bus_id']) ? (int)$_POST['bus_id'] : null;
$name = isset($_POST['name']) ? $conn->real_escape_string($_POST['name']) : '';
$email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
$phone = isset($_POST['phone']) ? $conn->real_escape_string($_POST['phone']) : '';

// Handle missing data
if (!$seat_id || !$name || !$email || !$phone) {
    echo "<h3 style='color: red; text-align: center;'>❌ Error: Missing customer information.</h3>";
    exit;
}

// Get seat information
$seat_query = $conn->query("SELECT seat_number, bus_id FROM seats WHERE seat_id = $seat_id");
if ($seat_query->num_rows === 0) {
    echo "<h3 style='color: red; text-align: center;'>❌ Error: Seat not found.</h3>";
    exit;
}
$seat = $seat_query->fetch_assoc();
$seat_number = $seat['seat_number'];

// If bus_id wasn't passed in POST, get it from the seat record
if (!$bus_id) {
    $bus_id = $seat['bus_id'];
}

// Get more detailed bus information
$bus_info = "";
$bus_route = "";
$bus_driver_email = "";

if ($bus_id) {
  $bus_query = $conn->query("SELECT buses.route_id, buses.driver_email FROM buses
  LEFT JOIN route ON buses.route_id = route.route_id 
  WHERE buses.bus_id = $bus_id");

    
    if ($bus_query->num_rows > 0) {
        $bus = $bus_query->fetch_assoc();
        $bus_route = $bus['route_id'];
        $bus_driver_email = $bus['driver_email']; 
        
        $bus_info = "<p><strong>Route:</strong> " . htmlspecialchars($bus_route) . "</p>";
    }
}

// Generate ticket reference
$ticket_reference = 'UR-' . date('Ymd') . '-' . $seat_id;

// Insert into customers table
$conn->query("INSERT INTO customers (seat_id, bus_id, name, email, phone) VALUES ($seat_id, $bus_id, '$name', '$email', '$phone')");

// Mark seat as reserved
$conn->query("UPDATE seats SET is_reserved = 1 WHERE seat_id = $seat_id");

// Email Configuration
$mail_config = [
    'host' => 'smtp.gmail.com',    
    'username' => 'brendon5860@gmail.com', 
    'password' => 'fkgehgastzzkrghi',
    'port' => 587,                  
    'encryption' => 'tls',          
    'from_email' => 'bookings@uniroot.com',
    'from_name' => 'UniRoute Bus Booking'
];

// Function to send emails
function sendEmail($to, $subject, $body, $config) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = $config['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['username'];
        $mail->Password = $config['password'];
        if ($config['encryption']) {
            $mail->SMTPSecure = $config['encryption'];
        }
        $mail->Port = $config['port'];
        
        // Recipients
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags(str_replace('<br>', "\n", $body));
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

// Send confirmation email to customer
$customer_subject = "UniRoute Bus Booking Confirmation - Ref: $ticket_reference";
$customer_body = "
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #273c75; color: white; padding: 15px; text-align: center; }
        .content { padding: 20px; border: 1px solid #ddd; }
        .details { margin: 20px 0; }
        .details p { margin: 5px 0; }
        .footer { margin-top: 30px; font-size: 12px; color: #777; text-align: center; }
        .ticket-ref { background: #f8f9fa; padding: 10px; border: 1px dashed #ccc; text-align: center; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2>Seat Booking Confirmation</h2>
        </div>
        <div class='content'>
            <p>Dear " . htmlspecialchars($name) . ",</p>
            <p>Thank you for booking with UniRoute Bus Services. Your seat has been successfully reserved!</p>
            
            <div class='details'>
                <h3>Booking Details:</h3>
                <p><strong>Seat Number:</strong> " . htmlspecialchars($seat_number) . "</p>
                <p><strong>Bus ID:</strong> " . htmlspecialchars($bus_id) . "</p>";

$customer_body .= "
                <p><strong>Name:</strong> " . htmlspecialchars($name) . "</p>
                <p><strong>Email:</strong> " . htmlspecialchars($email) . "</p>
                <p><strong>Phone:</strong> " . htmlspecialchars($phone) . "</p>
            </div>
            
            <div class='ticket-ref'>
                <p>Your Booking Reference Number: <strong>" . $ticket_reference . "</strong></p>
                <p>Please keep this reference number for any future communication.</p>
            </div>
            
            <p>We wish you a pleasant journey!</p>
        </div>
        <div class='footer'>
            <p>This is an auto-generated email, please do not reply to this message.</p>
            <p>&copy; " . date('Y') . " UniRoute. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
";

// Send notification email to bus driver if driver email is available
$driver_notification_sent = false;
if ($bus_driver_email) {
    $driver_subject = "New Seat Booking Notification - Bus #$bus_id";
    $driver_body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #2c3e50; color: white; padding: 15px; text-align: center; }
            .content { padding: 20px; border: 1px solid #ddd; }
            .details { margin: 20px 0; }
            .details p { margin: 5px 0; }
            .footer { margin-top: 30px; font-size: 12px; color: #777; text-align: center; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Booking Alert</h2>
            </div>
            <div class='content'>
                <p>A new seat has been booked on your bus.</p>
                
                <div class='details'>
                    <h3>Booking Details:</h3>
                    <p><strong>Bus ID:</strong> " . htmlspecialchars($bus_id) . "</p>";
                    
    if ($bus_route) {
        $driver_body .= "<p><strong>Route:</strong> " . htmlspecialchars($bus_route) . "</p>";
    }
    
    
    $driver_body .= "
                    <p><strong>Seat Number:</strong> " . htmlspecialchars($seat_number) . "</p>
                    <p><strong>Passenger Name:</strong> " . htmlspecialchars($name) . "</p>
                    <p><strong>Passenger Phone:</strong> " . htmlspecialchars($phone) . "</p>
                    <p><strong>Booking Reference:</strong> " . $ticket_reference . "</p>
                </div>
                
                <p>Please update your passenger manifest accordingly.</p>
            </div>
            <div class='footer'>
                <p>This is an auto-generated email, please do not reply to this message.</p>
                <p>&copy; " . date('Y') . " UniRoute Bus Services. All rights reserved.</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    $driver_notification_sent = sendEmail($bus_driver_email, $driver_subject, $driver_body, $mail_config);
}

// Send email to customer
$customer_notification_sent = sendEmail($email, $customer_subject, $customer_body, $mail_config);

// Store email status messages to display
$email_status = "";
if ($customer_notification_sent) {
    $email_status .= "<p style='color: green;'>✅ Confirmation email sent to your email address.</p>";
} else {
    $email_status .= "<p style='color: orange;'>⚠️ Could not send confirmation email. Please save your booking reference.</p>";
}

if ($bus_driver_email) {
    if ($driver_notification_sent) {
        $email_status .= "<p style='color: green;'>✅ Bus driver has been notified of your booking.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Confirmation</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: #f5f6fa;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }

    .confirmation {
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      text-align: center;
      max-width: 500px;
      width: 100%;
    }

    h2 {
      color: #2ecc71;
      margin-bottom: 20px;
    }

    p {
      font-size: 18px;
      color: #2f3640;
      margin: 8px 0;
    }

    .email-status {
      margin: 15px 0;
      padding: 10px;
      border-radius: 5px;
      background: #f8f9fa;
    }

    .ticket-number {
      font-size: 16px;
      color: #7f8c8d;
      margin-top: 20px;
      border-top: 1px dashed #bdc3c7;
      padding-top: 15px;
    }

    a {
      display: inline-block;
      margin-top: 30px;
      text-decoration: none;
      background: #273c75;
      color: white;
      padding: 12px 24px;
      border-radius: 8px;
      transition: background 0.3s ease;
    }

    a:hover {
      background: #40739e;
    }
  </style>
</head>
<body>
  <div class="confirmation">
    <h2>✅ Payment Successful!</h2>
    <p><strong>Seat Number:</strong> <?php echo htmlspecialchars($seat_number); ?></p>
    <p><strong>Bus ID:</strong> <?php echo htmlspecialchars($bus_id); ?></p>
    <?php echo $bus_info; ?>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
    <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
    
    <div class="email-status">
      <?php echo $email_status; ?>
    </div>
    
    <p class="ticket-number">Reference #: <?php echo $ticket_reference; ?></p>
    <a href="../home/home.php">Back to Home</a>
  </div>
</body>
</html>
<?php
$conn->close();
?>