//JavaScript function used to toggle a button. Input along with the function is simply the button 'id' in the HTML parent.
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

//Javascript function used by the HTML pages to have their database controls follow the user as they scroll up and down the page. Input is simply the id of the div holding the controls that needs to follow the user.
function sidebar_follow_user_script(div_to_adjust) {
    var $sidebar = $(div_to_adjust),
        $window = $(window),
        offset = $sidebar.offset(),
        topPadding = 15;

    $window.scroll(function () {
        if ($window.scrollTop() > offset.top) {
            $sidebar.stop().animate({
                marginTop: $window.scrollTop() - offset.top + topPadding
            });
        } else {
            $sidebar.stop().animate({
                marginTop: 0
            });
        }
    });

}