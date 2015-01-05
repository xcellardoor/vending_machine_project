/**
 * Created by cellardoor on 18/12/14.
 */


function toggle_button(button_name) {
    var button = $('#' + button_name);
    if ($(button).prop('disabled')) {
        $(button.prop('disabled', false));
    }
    else {
        $(button).prop('disabled', true);
    }
}