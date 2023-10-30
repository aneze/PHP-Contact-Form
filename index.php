<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Simple contact form</title>
    <meta name="description" content="Example of a simple contact form in PHP programming language">

    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php
session_start();

if (!empty($_POST['action']) && ($_POST['action'] == 'submit')) {
    if (!empty($_SESSION['captcha_code']) && !empty($_POST['captcha_code']) &&
        ($_SESSION['captcha_code'] == $_POST['captcha_code']))
    {
        $first_name = !empty($_POST['first_name']) ? $_POST['first_name'] : '';
        $last_name = !empty($_POST['last_name']) ? $_POST['last_name'] : '';
        $phone = !empty($_POST['phone']) ? $_POST['phone'] : '';
        $email = !empty($_POST['email']) ? $_POST['email'] : '';
        $message = !empty($_POST['message']) ? $_POST['message'] : '';

        $mail_to = 'email@example.com';
        $mail_subject = 'Contact form submission';
        $mail_content =
            'First Name: ' . $first_name . "\n" .
            'Last Name: ' . $last_name . "\n" .
            'Phone: ' . $phone . "\n" .
            'Email: ' . $email . "\n" .
            'Message: ' . $message . "\n";

        if (mail($mail_to, $mail_subject, $mail_content)) {
            echo '<p class="status-success">Your message has been successfully sent.</p>';
        } else {
            echo '<p class="status-failure">Failed to send your message.</p>';
        }
    } else {
        echo '<p class="status-failure">Verification failed, numbers did not match, please try again.</p>';
    }
}
?>
    <form method="post">
        <input type="hidden" name="action" value="submit">
        <div class="form-row">
            <input type="text" name="first_name" placeholder="First Name">
        </div>
        <div class="form-row">
            <input type="text" name="last_name" placeholder="Last Name">
        </div>
        <div class="form-row">
            <input type="tel" name="phone" placeholder="Phone">
        </div>
        <div class="form-row">
            <input type="email" name="email" placeholder="Email">
        </div>
        <div class="form-row">
            <textarea name="message" placeholder="Message"></textarea>
        </div>
        <div class="form-row">
            <?php
            $font_id = 5;
            $width = 70;
            $height = 36;
            $characters = 6;

            $captcha_code = strval(rand(100000, 999999));

            $_SESSION["captcha_code"] = $captcha_code;

            $image = imagecreatetruecolor($width, $height);
            $text_color = imagecolorallocate($image, 50, 50, 50);
            $background_color = imagecolorallocate($image, 255, 255, 255);
            $line_color = imagecolorallocate($image, 120, 120, 120);
            $pixel_color = imagecolorallocate($image, 0, 0, 255);

            imagefill($image, 0, 0, $background_color);
            imagestring(
                $image,
                $font_id,
                ($width - $characters*imagefontwidth($font_id)) / 2,
                ($height - imagefontheight($font_id)) / 2,
                $captcha_code,
                $text_color);

            for ($i = 0; $i < 5; $i++) {
                imageline(
                    $image,
                    0,
                    rand() % $height,
                    $width,
                    rand() % $height,
                    $line_color);
            }

            for ($i = 0; $i < 200; $i++) {
                imagesetpixel(
                    $image,
                    rand() % $width,
                    rand() % $height,
                    $pixel_color);
            }

            ob_start();
            imagepng($image, null, 9, PNG_NO_FILTER);
            $image_prefix = 'data:image/png;base64,';
            $image_content = $image_prefix . base64_encode(ob_get_contents());
            imagedestroy($image);
            ob_end_clean();
            ?>
            <img alt="" class="captcha-image" src="<?php echo $image_content ?>">
            <input type="text"
                class="captcha-input"
                name="captcha_code"
                placeholder="Enter number"
                maxlength="<?php echo $characters ?>"
                size="<?php echo $characters ?>">
        </div>
        <div class="form-row">
            <input type="submit" value="Submit">
        </div>
    </form>
</body>
</html>