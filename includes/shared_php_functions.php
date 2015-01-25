<?php

//This function is used to generate drop-down menus quickly and dynamically based upon variables passed in.
function dropdown_menu($name, array $values, array $options, $selected = null)
{
    //Start off the 'dropdown' string to be returned to the calling parent.
    $dropdown = '<select name="' . $name . '" id="' . $name . '">' . "\n";

    //Iterate through the values array passed in and create a new option for each one, again appended to the main string
    foreach (array_combine($values, $options) as $id => $value) {

        $select = $selected == $value ? ' selected' : null;

        $dropdown .= '<option value="' . $id . '"' . $select . '>' . $value . '</option>' . "\n";

    }
    $dropdown .= '</select>' . "\n";

    //Pass the string back to the parent which shall use it for creating the drop-down menu.
    return $dropdown;
}

//This function shall be used by pages which need to keep a check on the 'error' SESSION variable, in case post.php has set it and now the parent HTML needs to alert the user that something is wrong.
function check_for_errors()
{
    if (isset($_SESSION['error']) && ($_SESSION['error'] != null)) {
        $error = json_encode($_SESSION['error']);
        $_SESSION['error'] = null;
        echo '<script>alert(' . $error . ')</script>';
    }
}