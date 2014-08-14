<?php
// Menu Settings
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