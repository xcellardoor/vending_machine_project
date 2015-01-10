/**
 * Created by cellardoor on 18/12/14.
 */


function toggle_button(button_name) {
    var button = $('#' + button_name);
    if ($(button).prop('disabled')) {
        $(button.prop('disabled', false));
        alert('WARNING: You may lose items if the Stockroom or Vending Machine have not already been emptied!');
    }
    else {
        $(button).prop('disabled', true);
    }
}