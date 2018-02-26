<?php

# Create the login link.
use Mamook\Document\Document;

$login_link = '<li class="list-nav-1' . Document::addHereClass(REDIRECT_TO_LOGIN, true, false) . '">';
$login_link .= '<a href="' . REDIRECT_TO_LOGIN . '"' . Document::addHereClass(REDIRECT_TO_LOGIN, true,
        false) . ' class="link-login" title="Login">Login</a>';
$login_link .= '</li>';
# Check if the user is logged in.
if ($login->isLoggedIn() === true) {
    # Create the logout link.
    $login_link = '<li class="list-nav-1' . Document::addHereClass(REDIRECT_TO_LOGIN . 'logout/', false, false) . '">';
    $login_link .= '<a href="' . REDIRECT_TO_LOGIN . 'logout/"' . Document::addHereClass(REDIRECT_TO_LOGIN . 'logout/',
            false, false) . ' class="link-logout" title="Logout">Logout</a>';
    $login_link .= '</li>';
    # Create the link to the user's admin pages.
    if ($login->checkAccess(ALL_ADMIN_MAN) === true) {
        $login_link .= '<li class="list-nav-1' . Document::addHereClass(ADMIN_URL, false, false) . '">';
        $login_link .= '<a href="' . ADMIN_URL . '"' . Document::addHereClass(ADMIN_URL) . ' class="link-admin" title="Admin">Admin</a>';
    } else {
        $login_link .= '<li class="list-nav-1' . Document::addHereClass(SECURE_URL . 'MyAccount/', false, false) . '">';
        $login_link .= '<a href="' . SECURE_URL . 'MyAccount/"' . Document::addHereClass(SECURE_URL . 'MyAccount/') . ' class="link-myaccount" title="MyAccount">MyAccount</a>';
    }
    $login_link .= '</li>';
}

# Check if social network buttons are enabled on the current page.
$social_item = $main_content->displaySocial();
if (!empty($social_item)) {
    # Create the social list item.
    $social_item = '<li class="list-nav-1 social-buttons">' . $social_item . '</li>';
}

# Display the subnavbar.
echo '<nav id="subnavbar" class="nav subnav">',
'<ol class="nav-1">',
$login_link,
$social_item,
'</ol>',
'</nav>';
