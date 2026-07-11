<?php
// ============================================
// send_email.php - AJAX Email handler
// Returns JSON response for better debugging
// ============================================

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON response header
header('Content-Type: application/json');

// Check if form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data
    $name = isset($_POST["name"]) ? strip_tags(trim($_POST["name"])) : '';
    $email = isset($_POST["email"]) ? filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL) : '';
    $phone = isset($_POST["phone"]) ? strip_tags(trim($_POST["phone"])) : '';
    $message = isset($_POST["message"]) ? strip_tags(trim($_POST["message"])) : '';
    
    // Validate
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "نام خود را وارد کنید";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "ایمیل معتبر وارد کنید";
    }
    
    if (empty($message)) {
        $errors[] = "پیام خود را وارد کنید";
    }
    
    // If validation errors
    if (!empty($errors)) {
        echo json_encode([
            'status' => 'error',
            'message' => implode(' - ', $errors)
        ]);
        exit;
    }
    
    // ============================================
    // 🔴 CHANGE THIS TO YOUR EMAIL ADDRESS
    // ============================================
    $to = "aa.najafabad@gmail.com"; // <-- PUT YOUR EMAIL HERE
    
    // Email subject
    $subject = "📩 پیام جدید از سایت الکلی‌های ناشناس ناحیه ۱۴";
    
    // Email body (HTML)
    $email_content = "
    <html dir='rtl'>
    <head>
        <style>
            body { font-family: Tahoma, Arial, sans-serif; background: #f4f7fc; padding: 20px; }
            .container { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); border-right: 5px solid #3a86ff; }
            h2 { color: #0b1f33; border-bottom: 2px solid #3a86ff; padding-bottom: 10px; }
            .label { font-weight: 700; color: #0b1f33; }
            .value { color: #1a2f44; margin-bottom: 15px; padding: 8px 12px; background: #f8fafc; border-radius: 8px; }
            .footer { margin-top: 30px; padding-top: 20px; border-top: 2px dashed #dce5f0; color: #6c7a8a; font-size: 0.9rem; text-align: center; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>📩 پیام جدید از وبسایت</h2>
            
            <p><span class='label'>👤 نام:</span><br><span class='value'>" . htmlspecialchars($name) . "</span></p>
            
            <p><span class='label'>📧 ایمیل:</span><br><span class='value'>" . htmlspecialchars($email) . "</span></p>
            
            <p><span class='label'>📞 تلفن:</span><br><span class='value'>" . (empty($phone) ? 'وارد نشده' : htmlspecialchars($phone)) . "</span></p>
            
            <p><span class='label'>💬 پیام:</span><br><span class='value' style='white-space: pre-wrap;'>" . nl2br(htmlspecialchars($message)) . "</span></p>
            
            <div class='footer'>
                <p>🔹 الکلی‌های ناشناس ناحیه ۱۴ خدماتی</p>
                <p style='font-size: 0.8rem; color: #8a9aa8;'>این پیام از طریق فرم تماس وبسایت ارسال شده است</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    // Plain text version
    $email_content_plain = "
    پیام جدید از سایت الکلی‌های ناشناس ناحیه ۱۴
    ============================================
    
    نام: $name
    ایمیل: $email
    تلفن: " . (empty($phone) ? 'وارد نشده' : $phone) . "
    
    پیام:
    $message
    
    --------------------------------------------
    الکلی‌های ناشناس ناحیه ۱۴ خدماتی
    ";
    
    // Headers
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    
    // Try to send email
    $mail_sent = mail($to, $subject, $email_content, $headers);
    
    // If failed, try plain text
    if (!$mail_sent) {
        $mail_sent = mail($to, $subject, $email_content_plain, "From: " . $email);
    }
    
    // Return response
    if ($mail_sent) {
        echo json_encode([
            'status' => 'success',
            'message' => 'پیام با موفقیت ارسال شد'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'خطا در ارسال ایمیل. لطفاً دوباره تلاش کنید.'
        ]);
    }
    
} else {
    // Not a POST request
    echo json_encode([
        'status' => 'error',
        'message' => 'درخواست نامعتبر'
    ]);
}
?>