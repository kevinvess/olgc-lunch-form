<?php
/**
 * The main template file.
 *
 * @package OLGC
 * @subpackage HotLunch
 * @since HotLunch 1.0
 *
 * @author Kevin M. Vess (kevin@vess.me)
 */

// Google Authentication
$google_username = 'username@gmail.com';
$google_password = 'password';
$spreadsheet = 'My Spreadsheet';

// Contact Personnel
$organizers = array(
    array(
    'name'  => 'John Doe',
    'email' => 'jdoe@example.com',
    )
);

// Menus
$menus = array(
    'menu-1' => array(
        'title' => 'Chick-Fil-A',
        'description' => 'Each meal includes fruit, carrots, and choice of chips OR dessert.',
        'days' => array('9/25', '10/30', '12/11', '1/29', '3/5', '4/23', '5/28'),
        'note' => '<strong>Health Note:</strong><br>Chick-Fil-A meals are cooked in 100% fully refined peanut oil that is cholesterol and trans fat free. Additional nutritional information can be found at: <a href="http://www.chick-fil-a.com/Documents/AllergenReference" target="_blank">www.chick-fil-a.com/Documents/AllergenReference</a>',
        'fields' => array(
            array(
                'name' => 'Type of Meal',
                'slug' => 'meal',
                'price' => '5.00',
                'type' => 'radio',
                'options' => array('Nuggets Meal', 'Sandwich Meal')
            ),
            array(
                'name' => 'Extra Nuggets',
                'slug' => 'extra-nuggets',
                'price' => '2.00',
                'type' => 'number'
            ),
            array(
                'name' => 'Extra Sandwich(es)',
                'slug' => 'extra-sandwiches',
                'price' => '2.00',
                'type' => 'number'
            )
        )
    ),
    'menu-2' => array(
        'title' => 'Ledo\'s Penne Pasta',
        'description' => 'The meal includes penne pasta with Ledo marinara sauce, warm garlic bread and a salad bar with homemade dressings.',
        'days' => array('10/2', '11/6', '12/18', '2/5', '3/12', '4/30', '6/4'),
        'fields' => array(
            array(
                'name' => 'Single Pasta Meal',
                'slug' => 'meal',
                'price' => '6.00',
                'type' => 'checkbox'
            )
        )
    ),
    'menu-3' => array(
        'title' => 'Sweet Leaf',
        'description' => 'Each meal includes a sandwich with yogurt tube, fruit, carrots, and choice of chips OR dessert.',
        'days' => array('10/9', '11/13', '1/8', '2/12', '3/19', '5/7'),
        'fields' => array(
            array(
                'name' => 'Type of Meal',
                'slug' => 'meal',
                'price' => '6.00',
                'type' => 'radio',
                'options' => array('Ham & Cheese Sandwich', 'Turkey & Cheese Sandwich')
            ),
            array(
                'name' => 'Extra 6-inch Sandwich(es)',
                'slug' => 'extra-sandwiches',
                'price' => '2.00',
                'type' => 'number'
            )
        )
    ),
    'menu-4' => array(
        'title' => 'Church Street Pizza',
        'description' => 'Each meal includes one slice of New York style pizza with fruit, carrots, and choice of chips OR dessert.',
        'days' => array('9/11', '10/16', '11/20', '1/15', '2/19', '3/26', '5/14'),
        'fields' => array(
            array(
                'name' => 'Single Slice Meal',
                'slug' => 'meal',
                'price' => '5.00',
                'type' => 'checkbox'
            ),
            array(
                'name' => 'Extra Slice(s)',
                'slug' => 'extra-slices',
                'price' => '1.00',
                'type' => 'number'
            )
        )
    ),
    'menu-5' => array(
        'title' => 'Baja Fresh',
        'description' => 'Each meal includes one quesadilla with rice, tortilla chips & salsa, and applesauce.',
        'days' => array('9/18', '10/23', '12/4', '1/22', '2/26', '4/16', '5/21'),
        'fields' => array(
            array(
                'name' => 'Type of Meal',
                'slug' => 'meal',
                'price' => '5.00',
                'type' => 'radio',
                'options' => array('Cheese Quesadilla Meal', 'Chicken Quesadilla Meal')
            ),
            array(
                'name' => 'Extra Quesadilla(s)',
                'slug' => 'extra-quesadillas',
                'price' => '1.00',
                'type' => 'number'
            )
        )
    )
);

/** Required functions and definitions */
require_once('functions.php');

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
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/themes/flick/jquery-ui.css">
    <link rel="stylesheet" href="css/all.css">

    <!-- All JavaScript at the bottom, except for Modernizr / Respond.
       Modernizr enables HTML5 elements & feature detects; Respond is a polyfill for min/max-width CSS3 Media Queries
       For optimal performance, use a custom Modernizr build: www.modernizr.com/download/ -->
    <script src="js/vendor/modernizr-2.6.2.min.js" type="text/javascript"></script>
</head>
<body>
    <div class="wrapper">
        <h1 class="logo"><a href="http://www.olgcschool.org/">Our Lady of Good Counsel School</a></h1>
        
        <h2>2014-2015 Hot Lunch Order Form – <strong>Due Sunday, September 7</strong></h2>
        
        <?php
        if(!empty($msg))
            echo $msg;
        else {
        ?>

        <p>Welcome back to another school year! The Hot Lunch Committee will again be offering lunches on Thursdays throughout the year from our five great vendors: Chick-fil-A, Ledo's Pasta, Sweet Leaf, Church Street Pizza, and Baja Fresh.</p>
        <p><strong>Payment:</strong> Lunches must be ordered online and paid with either credit or debit card. No paper orders or checks will be accepted through the school office.</p>
        <p>Orders are processed through PayPal BUT a <strong>PayPal account is NOT needed</strong>. Just choose the <strong>"Don't have a PayPal Account?"</strong> option to pay with a credit card. Also, if a Security Warning pop-up appears, please click Continue to complete your secure transaction.</p>
        <p>Instructions for purchasing lunch:</p>
        <ul>
            <li>Complete an individual order form including payment for each child. Separate payments ensure a detailed receipt for each order. After payment is complete and order is successfully processed, confirmation will be sent to your email.</li>
            <li>Lunches are ordered for the entire 2014-2015 school year.</li>
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

    <!-- Grab Google CDN's jQuery UI, with a protocol relative URL -->
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>

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