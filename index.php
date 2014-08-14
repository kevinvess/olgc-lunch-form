<?php
/**
 * The main template file.
 *
 * @package OLGC
 * @subpackage HotLunch
 * @since 2.0
 *
 * @author Kevin M. Vess (kevin@vess.me)
 */

require_once('functions.php');
require_once('Google_Spreadsheet.php');

// Google Docs settings
$google_username = 'username@gmail.com';
$google_password = 'password';
$spreadsheet = 'My Spreadsheet';

// Contact Personal
$organizers = array(
    array(
    'name'  => 'John Doe',
    'email' => 'jdoe@example.com',
    )
);

// Form settings
$send = isset($_GET['submit']);
$success = isset($_REQUEST['success']) || isset($_REQUEST['uid']);
$free = isset($_GET['olgcfreelunch']);
$msg = '';

// Send to PayPal
if($send) {
    // Get form data
    $order = $_POST['order'];

    // Special hidden fields
    $spam = stripslashes( $_POST['subject'] ); // SPAM Blocker
    $ip = $_SERVER['REMOTE_ADDR']; // Get Users IP Address

    // Validation
    if ($spam)
        $msg = '<p class="error">Uh oh, you have been considered SPAM. If you are not SPAM please email ' . get_organizers($organizers) . '</p>';
    elseif (!empty($order[user][email])) {
        // Format data for spreadsheet
        $ss_order = get_orderArray($_POST);

        // Process Order
        $ss = new Google_Spreadsheet($google_username,$google_password);
        $ss->useSpreadsheet($spreadsheet);
        if ($ss->addRow($ss_order)) {
            // Successful spreadsheet entry
            if ($_POST['key'] == 'olgcfreelunch')
                header('Location: ' . $_SERVER['PHP_SELF'] . '?uid=' . $ss_order['id'] . "&freelunch=true");
            else
                $ss->goToPaypal($ss_order['id'], $ss_order['total'], $_POST);
        }
    }
    else
        $msg = '<p class="error">Hmm... we couldn\'t process your order, please try again or send an email to ' . get_organizers($organizers) . '</p>';
}

// Return from PayPal
if($success) {
    // Get form data
    $uniqueID = $_REQUEST['uid'];
    $freelunch = $_GET['freelunch'];

    // Validation
    if (!empty($uniqueID)) {
        
        // Update Google Spreadsheet
        $ss = new Google_Spreadsheet($google_username,$google_password);
        $ss->useSpreadsheet($spreadsheet);
        if ($freelunch == 'true')
            $receipt = $ss->updatePaid($uniqueID, true);
        else
            $receipt = $ss->updatePaid($uniqueID, false);

        $user_paid = $ss->hasPaid($uniqueID);

        // Thank you message & receipt
        $msg = get_receipt($menus, $receipt, $freelunch);

        // Send Emails
        if ($user_paid) {
            $headers = 'From: ' . 'no-reply@' . $_SERVER['HTTP_HOST'] . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $fullname = $receipt[0]['user-first-name'] . " " . $receipt[0]['user-last-name'];

            // Email Receipt
            $email_msg = $msg;
            $email_msg = str_replace(array(", a copy of this receipt will be emailed to you"), array(""), $email_msg);
            mail($receipt[0]['user-email'], 'Hot Lunch Order Receipt', $email_msg, $headers);

            // Send Admin Notification(s)
            $alert_msg = 'This is an alert that ' . $fullname . ' has placed an order for lunch.  The total price of the order was ' . $receipt[0]['total'] . '. <br><br> Please check the spreadsheet for the order details. <br><br> Order Number: ' . $uniqueID;
            foreach ($organizers as $organizer) {
                mail($organizer['name'].' <'.$organizer['email'].'>', 'Hot Lunch Order Alert', $alert_msg, $headers);
            }
        }
    }
    else
        header('Location: ' . $_SERVER['PHP_SELF']);
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width">

    <title>OLGC School - Hot Lunch Order Form</title>

    <link rel="icon" type="image/png" href="http://www.olgcschool.org/favicon.ico">

    <!--Stylesheets-->
    <link rel="stylesheet" href="css/all.css">

    <!-- All JavaScript at the bottom, except for Modernizr / Respond.
       Modernizr enables HTML5 elements & feature detects; Respond is a polyfill for min/max-width CSS3 Media Queries
       For optimal performance, use a custom Modernizr build: www.modernizr.com/download/ -->
    <script src="js/vendor/modernizr-2.6.2.min.js" type="text/javascript"></script>
</head>
<body>
    <div class="wrapper">
        <h1 class="logo"><a href="http://www.olgcschool.org/">Our Lady of Good Counsel School</a></h1>
        
        <h2>2013-2014 Hot Lunch Order Form – <strong>Due Friday, September 13</strong></h2>
        
        <?php
        if(!empty($msg))
            echo $msg;
        else {
        ?>

        <p>Welcome back to another school year! The Hot Lunch Committee will again be offering lunches on Thursdays throughout the year from our five great vendors: Chick-fil-A, Ledo's Pasta, Panera, Church Street Pizza, and Baja Fresh.</p>
        <p><strong>Payment:</strong> Lunches must be ordered online and paid with either credit or debit card. No paper orders or checks will be accepted through the school office.</p>
        <p>Orders are processed through PayPal BUT a <strong>PayPal account is NOT needed</strong>. Just choose the <strong>"Don't have a PayPal Account?"</strong> option to pay with a credit card. Also, if a Security Warning pop-up appears, please click Continue to complete your secure transaction.</p>
        <p>Instructions for purchasing lunch:</p>
        <ul>
            <li>Complete an individual order form including payment for each child. Separate payments ensure a detailed receipt for each order. After payment is complete and order is successfully processed, confirmation will be sent to your email.</li>
            <li>Lunches are ordered for the entire 2013-2014 school year.</li>
            <li>When purchasing lunch from a particular vendor, you must order the same meal each day that vendor serves lunch.</li>
        </ul>
        <p>Questions? Contact <?php echo get_organizers($organizers); ?></p>

        <form id="OrderForm" action="?submit" method="post">
            <input type="hidden" name="cmd" value="_xclick">
            <input type="hidden" name="charset" value="utf-8">
            <input type="hidden" name="key" value="<?=($free) ? 'olgcfreelunch' : '';?>">
            <input type="hidden" name="rm" value="2">
            <input type="hidden" name="cbt" value="COMPLETE YOUR LUNCH ORDER">
            <input type="hidden" name="business" value="auction@olgcva.org">
            <input type="hidden" name="item_name" value="OLGC Hot Lunch">
            <input id="purchaseAmount" type="hidden" name="amount" value="0">

            <h3><?=($free) ? "Faculty/Staff" : "Student";?> Information</h3>
            <div class="section info">
                <div>
                    <label for="first_name">First Name:</label>
                    <input type="text" id="first-name" name="order[user][first-name]" value="" class="required">
                    
                    <label for="last_name">Last Name:</label>
                    <input type="text" id="last-name" name="order[user][last-name]" value="" class="required">
                    
                    <?php if(!$free) { ?>

                    <label for="teacher">Teacher:</label>
                    <input type="text" id="teacher" name="order[user][teacher]" value="" class="required">
                    <?php } ?>

                </div>
                <div>
                    <label for="room">Room #:</label>
                    <input type="text" id="room" name="order[user][room]" value="" class="required">
                    
                    <label for="email">Email Address:</label>
                    <input type="text" id="email" name="order[user][email]" value="" class="required">
                    
                    <label for="phone">Telephone Number:</label>
                    <input type="text" id="phone" name="order[user][phone]" value="">
                </div>
            </div>

            <?php echo build_menu($menus); ?>

            <h3>TOTAL</h3>
            <div class="section">
                <div>
                    <label for="order-total">TOTAL (All Combined Vendor Totals):</label>
                    <input type="text" id="order-total" name="order[total]" value="$0.00" readonly="readonly" class="readonly">
                </div>
                <div style="text-align:right;">
                    <?php if($free) { ?>

                    <br /><input id="submit" type="submit" name="submit" value="Submit Order" class="button">
                    <?php } else { ?>

                    <input id="submit" type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
                    <?php } ?>

                </div>
            </div>
            
            <?php /*SPAM Blocker*/ ?>
            <input type="text" name="subject" value="" style="display:none;">
        </form>

        <?php } ?>

    </div>
    <?php if(empty($msg)) { ?>

    <div class="processing">
        <div>
            <h1>Your order is being processed, this might take a few minutes.</h1>
            <?=(!$free) ? "<p>You will be directed to PayPal to pay for your order, please be sure to click the link to return to this site to complete your order.</p>" : "";?>
        </div>
    </div>
    <?php } ?>

    <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.9.1.min.js"><\/script>')</script>

    <script src="js/main.js" type="text/javascript"></script>

    <!-- Analytics -->
    <script>
        var _gaq=[['_setAccount','UA-XXXXX-X'],['_trackPageview']];
        (function(d,t){var g=d.createElement(t),s=d.getElementsByTagName(t)[0];
        g.src=('https:'==location.protocol?'//ssl':'//www')+'.google-analytics.com/ga.js';
        s.parentNode.insertBefore(g,s)}(document,'script'));
    </script>
</body>
</html>