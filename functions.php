<?php
/**
 * Functions and definitions
 *
 * Sets up the form and provides some helper functions.
 *
 * @package OLGC
 * @subpackage HotLunch
 * @since HotLunch 2.0
 */

// Increase memory limit
ini_set('memory_limit', '128M');

// Include Google_Spreadsheet class
require_once('Google_Spreadsheet.php');

/**
 * Form Setup
 *
 * Configure the page content depending on which stage
 * of the order process the user is currently in.
 *
 * @since HotLunch 2.1
 */

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
                header('Location: ' . $_SERVER['PHP_SELF'] . '?uid=' . $ss_order['id'] . "&freelunch=true&email=true");
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
    $sendEmail = $_GET['email'];

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
        if ($user_paid && $sendEmail == 'true') {
            $headers = 'From: ' . 'no-reply@' . $_SERVER['HTTP_HOST'] . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $fullname = $receipt[0]['user-first-name'] . " " . $receipt[0]['user-last-name'];

            // Email Receipt
            $email_msg = $msg;
            $email_msg = str_replace(array(", a copy of this receipt will be emailed to you"), array(""), $email_msg);
            mail($receipt[0]['user-email'], 'Hot Lunch Order Receipt', $email_msg, $headers);

            // Send Admin Notification(s)
            $alert_msg = 'This is an alert that ' . $fullname . ' has placed an order for lunch.  The total price of the order was ' . $receipt[0]['total'] . '. <br><br> Please check the spreadsheet for the order details. <br><br> Order Number: <a href="http://'.$_SERVER['HTTP_HOST'].'/?uid='.$uniqueID.'">' . $uniqueID . '</a>';
            foreach ($organizers as $organizer) {
                mail($organizer['name'].' <'.$organizer['email'].'>', 'Hot Lunch Order Alert', $alert_msg, $headers);
            }
        }
    }
    else
        header('Location: ' . $_SERVER['PHP_SELF']);
}
// end; Form Setup

/**
 * Displays a list of the adminstrators who should be contacted,
 * used in various places of the form.
 *
 * @since HotLunch 2.0
 *
 * @param array $organizers A list of administrators.
 * @return string An HTML-formatted list.
 */
function get_organizers($organizers) {
    $i = 0;
    $output;
    foreach ($organizers as $organizer) {
        $i++;
        $output .= $organizer['name'] . ' (<a href="mailto:' . $organizer['email'] . '">' . $organizer['email'] . '</a>)';
        if($i == count($organizers))
            $output .= '';
        else
            $output .= ' or ';
    }

    return $output;
}

/**
 * Displays the meal options for each menu.
 *
 * @since HotLunch 2.0
 *
 * @param array $menus A list of menu options.
 * @return string The HTML-formatted menu form.
 */
function build_menu($menus) {
    $output;
    $index = 0;
    foreach ($menus as $menu) {
        $index++;
        $output .= '<h3>' . $menu['title'] . ' Meal</h3>';
        $output .= '<div class="section menus menu-'.$index.'" data-days="'.count($menu['days']).'">';
            $output .= '<div class="notes">';
                $output .= '<p>These meals will be served on: ';
                    $num = count($menu['days']);
                    $i = 0;
                    foreach ($menu['days'] as $day) {
                        if(++$i === $num)
                            $output .= ' and ' . $day;
                        else 
                            $output .= $day . ', ';
                    }
                $output .= '.</p>';
                $output .= '<p>'.$menu['description'].'</p>';
                $output .= '<p style="font-style:italic;">'.$menu['note'].'</p>';
            $output .= '</div>';
            $output .= '<div>';
                $i = 0;
                foreach ($menu['fields'] as $field) {
                    switch ($field['type']) {

                        // radio options
                        case 'radio':
                            $output .= '<p>'.$field['name'].' <em>($'.$field['price'].' ea.)</em>:</p>';
                            $output .= '<div class="options">';
                                $j = 0;
                                foreach ($field['options'] as $option) {
                                    $j++;
                                    $output .= '<input type="radio" id="menu-'.$index.'-meal-'.$j.'" name="order[menu-'.$index.']['.$field['slug'].']" value="'.$option.'" data-price="'.$field['price'].'">';
                                    $output .= '<label for="menu-'.$index.'-meal-'.$j.'" class="inline">'.$option.'</label>';
                                }
                            $output .= '</div>';
                            break;

                        // checkbox
                        case 'checkbox':
                            $output .= '<div class="options">';
                                $output .= '<input type="checkbox" id="menu-'.$index.'-meal" name="order[menu-'.$index.']['.$field['slug'].']" value="'.$field['name'].'" data-price="'.$field['price'].'" style="margin-left:0;">';
                                $output .= '<label for="menu-'.$index.'-meal">'.$field['name'].' <em>($'.$field['price'].' ea.)</em></label>';
                            $output .= '</div>';
                            break;

                        // input
                        case 'number':
                            $i++;
                            $output .= '<label for="menu-'.$index.'-extra-'.$i.'">'.$field['name'].' <em>($'.$field['price'].' ea.)</em>:</label>';
                            $output .= '<input type="text" id="menu-'.$index.'-extra-'.$i.'" name="order[menu-'.$index.']['.$field['slug'].']" value="0" data-price="'.$field['price'].'" maxlength="2" class="sm-field numeric-only">';
                            break;
                    }
                }
                $output .= '<label for="menu-'.$index.'-subtotal"><em>Single Meal Total:</em></label>';
                $output .= '<input type="text" id="menu-'.$index.'-subtotal" name="order[menu-'.$index.'][subtotal]" value="$0.00" readonly="readonly" class="readonly">';
                        
                $output .= '<label for="menu-'.$index.'-total"><em>Vendor Total (Single Meal x '.count($menu['days']).' days served):</em></label>';
                $output .= '<input type="text" id="menu-'.$index.'-total" name="order[menu-'.$index.'][total]" value="$0.00" readonly="readonly" class="readonly">';
            $output .= '</div>';
        $output .= '</div>';
    }
    return $output;
}

/**
 * Calculates the total cost of the order.
 *
 * @since HotLunch 2.0
 *
 * @param array $postData The form $_POST data.
 * @return array The reformatted array.
 */
function calculateTotal($menus, $order) {
    $total;

    return $total;
}

/**
 * Creates an array of the form data to be submited to the Google spreadsheet.
 *
 * @since HotLunch 2.0
 *
 * @param array $postData The form $_POST data.
 * @return array The reformatted array.
 */
function get_orderArray($postData) {
    $result = Array();

    // Reformat $_POST data
    foreach ($postData['order'] as $prefix => $subArray) {
        if (is_array($subArray)) {
            foreach ($subArray as $suffix => $value) {
                $new_key = $prefix . '_' . $suffix;
                $new_key = str_replace('_', '-', $new_key);
                $result[$new_key] = $value;
            }
        }
    }

    // Add new data
    $result['total'] = $postData['order']['total']; //calculateTotal($menus, $result);
    $result['paid'] = 'no';
    $result['id'] = 'z' . md5(microtime(true));

    return $result;
}

/**
 * Creates the receipt.
 *
 * @since HotLunch 2.0
 *
 * @param array $menus A list of menu options.
 * @param array $order A list of purchased order.
 * @param boolean $free Whether to echo the teacher result. Defalut is false.
 * @return string The HTML-formatted receipt.
 */
function get_receipt($menus, $order, $free = false) {
    $output;
    $fullname = $order[0]['user-first-name'] . " " . $order[0]['user-last-name'];
    $tab = '&nbsp;&nbsp;&nbsp;&nbsp;';

    // Intro & User details
    $output = '<p>Thank You! Below is your order, a copy of this receipt will be emailed to you:</p>
        <h3>Your Information</h3>
        <p class="success">
            <strong>Name:</strong> ' . $fullname . '<br />';
    $output .= ($free == true) ? '' : '<strong>Teacher:</strong> ' . $order[0]['user-teacher'] . '<br />';
    $output .= '
            <strong>Room:</strong> ' . $order[0]['user-room'] . ' <br />
            <strong>Order Number:</strong> ' . $order[0]['id'] . ' <br />
        </p>';
    $output .= '<h3>Your Order</h3>';

    // Meals ordered
    $i = 0;
    foreach ($menus as $menu) {
        $i++;
        $meal = $order[0]['menu-'.$i.'-meal'];
        if($meal != '') {
            $output .= '<strong>' . $menu['title'] . ' Meal</strong><br>';
            $output .= $tab . $meal . ' <em>($'.$menu['fields'][0]['price'].' ea. x '.count($menu['days']).' days)</em><br>';
            // --extras
            foreach ($order[0] as $key => $value) {
                $exp_key = explode('-', $key);
                if($exp_key[2] == 'extra' && $exp_key[1] == $i && $value != ''){
                    $output .= $tab . $value . ' ' . $menu['fields'][1]['name'] . ' <em>($'.$menu['fields'][1]['price'].' ea. x '.count($menu['days']).' days)</em><br>';
                }
            }
            $output .= $tab . '<strong>Vendor Total: <span style="float:right">' . $order[0]['menu-'.$i.'-total'] . '</span></strong><br><br>';
        }
    }

    // Total
    $output .= '<hr>
        <p><strong>Total: <span style="float:right">' . $order[0]['total'] . '</strong></p>';

    return $output;
}