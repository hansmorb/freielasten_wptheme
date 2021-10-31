<?php

//START WP Standard Login Seite deaktivieren
// Hook the appropriate WordPress action
// 100% fertig
function prevent_wp_login() {
    // WP tracks the current page - global the variable to access it
    global $pagenow;
    // Check if a $_GET['action'] is set, and if so, load it into $action variable
    $action = (isset($_GET['action'])) ? $_GET['action'] : '';
    // Check if we're on the login page, and ensure the action is not 'logout'
    if( $pagenow == 'wp-login.php' && ( ! $action || ( $action && ! in_array($action, array('logout', 'lostpassword', 'rp', 'resetpass'))))) {
        // Load the home page url
        $page = get_bloginfo('url').'/anmelden/';
        // Redirect to the home page
        wp_redirect($page);
        // Stop execution to prevent the page loading for any reason
        exit();
    }
}
add_action('init', 'prevent_wp_login');
//ENDE Standard WP Login Seite deaktivieren


add_filter( 'register_url', 'my_register_url' ); //Ersetzt default register URL (und damit auch return von wp_register_url) zu UM Seite (von Hand eingetragen, nicht so clean)
function my_register_url( $url ) {
    if( is_admin() ) {
        return $url;
    }
    return "registrieren";
}

add_filter( 'login_url', 'my_login_url', 10, 2 );
function my_login_url( $url, $redirect = null ) { //Ersetzt default URL (und damit auch return von wp_login_url) zu UM Seite (von Hand eingetragen, nicht so clean)
    if( is_admin() ) {
        return $url;
    }
    $r = "";
    if( $redirect ) {
        $r = "?redirect_to=".esc_attr($redirect);
    }
    return "anmelden".$r;
}

?>
