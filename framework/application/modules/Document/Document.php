<?php

namespace Mamook\Document;

use Mamook\Email\Email;
use Mamook\ExceptionHandler\Exception;
use Mamook\Session\Session;
use Mamook\Utility\Utility;
use Mamook\WebUtility\WebUtility;

# Make sure the script is not accessed directly.
if (!defined('BASE_PATH')) {
    exit('No direct script access allowed');
}

/**
 * Document
 * The Document Class is used to add data scripts and data to a document.
 */
class Document
{
    private static $document;
    private $error = null;
    private $footer_js = array();
    private $fwPopUp_settings = '{}';
    private $is_mobile = false;
    private $java_scripts = array();
    private $style = array();

    /**
     * __construct
     */
    public function __construct()
    {
        # Check if the user is on a mobile device. If so, set it to the data member.
        $this->findMobileDevice();
    }

    /**
     * setError
     * Sets the data member $error.
     *
     * @param        $error
     *
     * @access    public
     */
    public function setError($error)
    {
        $this->error = $error;
    } #==== End -- setError

    /**
     * setFooterJS
     * Sets the data member $footer_js.
     *
     * @param        $script_name (Must be the name of the script. May be an array of java scripts.)
     *
     * @access    public
     */
    public function setFooterJS($script_name)
    {
        $script_name = explode(',', $script_name);
        foreach ($script_name as $script) {
            $this->footer_js[] = $script;
        }
    } #==== End -- setFooterJS

    /**
     * Sets the data member $fwPopUp_settings.
     *
     * @param $fwPopUp_settings
     */
    public function setFwPopUpSettings($fwPopUp_settings)
    {
        # Clean it up.
        $fwPopUp_settings = trim($fwPopUp_settings);
        if (!empty($fwPopUp_settings)) {
            $this->fwPopUp_settings = $fwPopUp_settings;
        }
    }

    /**
     * setIsMobile
     * Sets the data member $is_mobile.
     *
     * @param        $mobile_device
     *
     * @access    public
     */
    public function setIsMobile($mobile_device)
    {
        if (empty($mobile_device)) {
            $mobile_device = false;
        }
        $this->is_mobile = $mobile_device;
    } #==== End -- setIsMobile

    /**
     * setJavaScripts
     * Sets the data member $java_scripts. The script name must be in the format of -'FolderPath/FileName' or
     * 'FileName'. If there is more than one, they may be comma separated ('FileName1,FolderPath2/FileName2,
     * FileName3') or an Array (array(0=>'FileName1', 1=>'FolderPath2/FileName2', 3->' FileName3')). If there is
     * whitespace at the beginning or end of the fileneame, it will be trimmed off.
     *
     * @param        $scripts (Must be in the format of -'FolderPath/FileName' or 'FileName')
     *
     * @access    public
     */
    public function setJavaScripts($scripts)
    {
        if (!is_array($scripts)) {
            $scripts = explode(',', $scripts);
        }
        foreach ($scripts as $script_name) {
            $this->java_scripts[] = trim($script_name);
        }
    } #==== End -- setJavaScripts

    /**
     * Sets the data member $style. Returns FALSE on failure. Accepts comma separated stylesheet paths. If specific
     * stylesheets for a responsive design are to be included, a JSON String may be appended to the stylesheet
     * pathname. The properties in the JSON may be: "device", "property", "value", and "tag". The "device" value being
     * the device the specific CSS is targeting (ie "screen"). The "property" value being the property to measure
     * against (ie "max-width"). The "value" value being the property value to measure against (ie "1000em"). The "tag"
     * value being a string that is appended to the end of the stylesheet name to make it locatable and unique (ie if
     * "path/stylesheet.css" is the passed stylesheet name, a "tag" value of "screen1000" would indicate
     * "path/stylesheet.screen1000.css".)
     *
     * @example
     *
     * @param        string $style_sheet Must be a url to a style sheet. May be comma seperated style sheets or an
     *                                   array of stylesheets.
     *
     * @throws Exception
     */
    public function setStyle($style_sheet)
    {
        if (!empty($style_sheet)) {
            # Check if the passed value is NOT an Array.
            if (!is_array($style_sheet)) {
                # Split the passed string on commas returning an array of stylesheets. Do NOT split on commas  inside of JSON.
                $style_sheet = preg_split('/,(?![^{}]*+\\})/i', $style_sheet);
            }
            # Loop through the Array of stylesheets.
            foreach ($style_sheet as $style) {
                # Get the position of the last backslash.
                $pos = strrpos($style, '/');
                # Check if there was NO backslash. (If not, it's not a path, just a filename.)
                if ($pos === false) {
                    # Prepend the path to the current Theme's CSS folder to the stylesheet filename.
                    $style = THEME . 'css/' . $style;
                }
                # Add each stylesheet to the "style" data member Array.
                $this->style[] = $style;
            }
        } else {
            throw new Exception('The Style Sheet was not set!', E_RECOVERABLE_ERROR);
        }
    } #==== End -- setStyle

    /**
     * getError
     * Returns the data member $error.
     *
     * @access    public
     */
    public function getError()
    {
        return $this->error;
    } #==== End -- getError

    /**
     * getFooterJS
     * Returns the data member $footer_js.
     *
     * @access    protected
     */
    protected function getFooterJS()
    {
        return $this->footer_js;
    } #==== End -- getFooterJS

    /**
     * getFwPopUpSettings
     * Returns the data member $fwPopUp_settings.
     *
     * @access    protected
     */
    protected function getFwPopUpSettings()
    {
        return $this->fwPopUp_settings;
    } #==== End -- getFwPopUpSettings

    /**
     * getIsMobile
     * Returns the data member $is_mobile.
     *
     * @access    protected
     */
    public function getIsMobile()
    {
        return $this->is_mobile;
    } #==== End -- getIsMobile

    /**
     * getJavaScripts
     * Returns the data member $java_scripts.
     *
     * @access    protected
     */
    protected function getJavaScripts()
    {
        return $this->java_scripts;
    } #==== End -- getJavaScripts

    /**
     * Returns the data member $style. Returns FALSE on failure.
     *
     * @throws Exception
     */
    protected function getStyle()
    {
        if (isset($this->style) && !empty($this->style)) {
            return $this->style;
        } else {
            throw new Exception('The Style is not set', E_RECOVERABLE_ERROR);
        }
    } #==== End -- getStyle

    /**
     * Returns the passed class name if the passed number is odd. Returns NULL if passed number is even.
     *
     * @param $number
     * @param $class_name
     *
     * @return    string or NULL
     */
    public static function addAlternatingClass($number, $class_name)
    {
        /* The modulous operator (%) returns the remainder of a division.
        The remainder of any even integer that is divided by two will always be zero.  PHP will conveniently interpret 0 as false and any non-zero as true. */
        if ($number % 2) {
            return ' ' . $class_name;
        } else {
            return null;
        }
    } #==== End -- addAlternatingClass

    /**
     * Adds a div for errors and messages.
     */
    public function addErrorBox()
    {
        $session = Session::getInstance();
        $session_message = $session->getMessage();

        global $alert_title;

        $error_box = '';
        $error = $this->getError();
        $js_errors = '';
        if (!empty($error) || !empty($session_message)) {
            $error_box .= '<noscript>';
            $error_box .= '<section class="alertBox">';
            $error_box .= '<h1 class="h-1">' . $alert_title . '</h1>';
            if (!empty($error)) {
                # Concatenate the error message to the error box.
                $error_box .= $error;
                # Concatenate the error message to the errorvariable for Javascript error display.
                $js_errors .= $error;
                # clear the error
                unset($error);
            }
            if (!empty($session_message)) {
                # Format the message for html display and set it to a variable.
                $message = '<div>' . $session_message . '</div>';
                # Concatenate the error message to the error box.
                $error_box .= $message;
                # Concatenate the error message to the errorvariable for Javascript error display.
                $js_errors .= $message;
            }
            $error_box .= '</section>';
            $error_box .= '</noscript>';
            # Set the error data member for Javascript display.
            $this->setError($js_errors);
        }

        return $error_box;
    } #==== End -- addErrorBox

    /**
     * Puts the correct javascripts into the footer for the page.
     */
    public function addFooterJS()
    {
        return $this->addJavaScript($this->getFooterJS());
    } #==== End -- addFooterJS

    /**
     * Adds a div for errors and messages via Javascript.
     *
     * @return string
     */
    public function addJSErrorBox()
    {
        # $error and $no_cookie_msg may be set outside of this method. If they are not set, defaults are created in this method.
        global $error, $no_cookie_msg, $alert_title, $check_cookies;

        if (!isset($error) || empty($error)) {
            $error = $this->getError();
        }

        $page_error = '""';
        $js_no_cookie_msg = '';
        if (!empty($error)) {
            $error = preg_replace('/\r?\n/', '\\n', addslashes($error));
            $page_error = '"' . $error . '"';
        }
        if (!isset($no_cookie_msg) || empty($no_cookie_msg)) {
            $js_no_cookie_msg = 'You do not have cookies enabled in your browser. Some aspects of this website may not function correctly. You may enable or disable cookies in your browser. You may find out more about cookies at <a href="http://en.wikipedia.org/wiki/HTTP_cookie" target="_blank">http://en.wikipedia.org/wiki/HTTP_cookie</a>';
            $js_no_cookie_msg = preg_replace('/\r?\n/', '\\n', addslashes($js_no_cookie_msg));
        }

        $js_error_msg = '(function(){';
        $js_error_msg .= '$().fwAlert({' .
            'close:"OK",' .
            'draggable:true});';
        $js_error_msg .= 'var noCookie=false,';
        $js_error_msg .= 'noCookieMsg="",';
        $js_error_msg .= 'pageError=' . $page_error . ';';
        $js_error_msg .= 'if(!navigator.cookieEnabled){noCookie=true}';
        if ($check_cookies !== false) {
            $js_error_msg .= 'if(noCookie){noCookieMsg="' . $js_no_cookie_msg . '"}';
        }
        $js_error_msg .= 'if((noCookieMsg)||(pageError)){' .
            'alert("' . $alert_title . '",noCookieMsg+pageError)' .
            '}';
        $js_error_msg .= '})();';

        return $js_error_msg;
    } #==== End -- addJSErrorBox

    /**
     * Adds the "here" css class if we are already at the page that the link sends us to.
     *
     * @param string  $link          The link to check
     * @param boolean $exact_match
     * @param boolean $add_attribute Adds 'class="'
     *
     * @return string
     */
    public static function addHereClass($link, $exact_match = false, $add_attribute = true)
    {

        # Create and empty variable to hold the potential class tag.
        $class = '';
        # Create an empty variable to hold the "class" attribute.
        $attribute = (($add_attribute === true) ? 'class="' : '');
        # Remove any Scheme Name (ie http://) from the passed link.
        $link = WebUtility::removeSchemeName($link);
        # Remove any index page from the passed link.
        $current_page = WebUtility::removeIndex(FULL_URL);
        # Remove www. from $link.
        $link = str_replace('www.', '', $link);
        # Remove www. from $current_page.
        $current_page = str_replace('www.', '', $current_page);
        # Check if this is an error page.
        if (strpos($current_page, WebUtility::removeSchemeName(ERROR_PAGE)) !== false) {
            # Explicitly set the error page as the current page without the query portion.
            $current_page = WebUtility::removeSchemeName(ERROR_PAGE);
        }
        # Remove any Query (ie GET data) from the passed link.
        $current_page = WebUtility::removePageQuery($current_page);
        # Are we looking for an exact match?
        if ($exact_match === true) {
            # Check if the url passed matches the current page exactly.
            if ($current_page == $link) {
                # Set the class tag to the variable.
                $class = ' ' . $attribute . 'here' . (($add_attribute === true) ? '"' : '');
            }
        } # Check if the url passed is part of the current page.
        elseif (strpos($current_page, $link) !== false) {
            # Set the class tag to the variable.
            $class = ' ' . $attribute . 'here' . (($add_attribute === true) ? '"' : '');
        }

        return $class;
    } #==== End -- addHereClass

    /**
     * Loops through the array of JavaScript names set in the java_scripts data member and includes the php file. The
     * required php file will live in the path associated with the JAVASCRIPTS constant in a folder with the current
     * JavaScript name. The php file itself will also have the passed JavaScript name. Each php file will set a String
     * value to the temporary variable $js. The values of $js will be concatenated together and returned as $display.
     * Any errors thrown in the course of the loop will be caight and rethrown. Any errors thrown in the course of the
     * getJavaScripts method will be caught and rethrown.
     *
     * @see            #setJavaScripts()
     * @see            #getJavaScripts()
     *
     * @param null $scripts
     *
     * @return string
     */
    public function addJavaScript($scripts = null)
    {
        if ($scripts === null) {
            # Get the String of all passed php files. (will be in the format of - 'FolderPath/FileName' or 'FileName')
            $scripts = $this->getJavaScripts();
        }
        $display = '';
        $js = '';
        if (!empty($scripts)) {
            foreach ($scripts as $script_name) {
                # Create a variable to hold the name uf the sub-folder in the scripts folder.
                $sub_folder = '';
                # Get the position of the last occurrence of the current directory separator in the current JavaScript name.
                $last_ds = strrchr($script_name, DS);
                # Check if the JavaScript name contains an instance of the value of the DS constant (a String).
                if ($last_ds !== false) {
                    # Set the sub folder path the the sub_folder variable. (This will leave the directory separator off the end.)
                    $sub_folder = substr($script_name, 0, $last_ds);
                    # Check if the directory separator is empty (FALSE, NULL, 0, '', or ' ').
                    if (!empty($sub_folder)) {
                        # Append the directory separator to the end of the path.
                        $sub_folder .= DS;
                        # Reset the script_name variable replacing the subfolder path with an empty String.
                        $script_name = strtr($script_name, array($sub_folder => ''));
                    } else {
                        # Reset the sub_folder to an empty String to normailize the empty value.
                        $sub_folder = '';
                    }
                }
                # Require the passed php file.
                require Utility::locateFile(JAVASCRIPTS . $sub_folder . $script_name . '.php');
                # Add the returned value of $js to the display variable (will be returned as the conclusion of the method).
                $display .= $js;
                # Reset the $js variable to an empty String to go through the foreach loop again.
                $js = '';
            }
        }

        # Return the accumulated values from the passed php files.
        return $display;

    } #==== End -- addJavaScript

    /**
     * Loops through the array of JavaScript names set in the java_scripts data member and includes the php file. The
     * required php file will live in the path associated with the JAVASCRIPTS constant in a folder with the current
     * JavaScript name. The php file itself will also have the passed JavaScript name. Each php file will set a String
     * value to the temporary variable $js. The values of $js will be concatenated together and returned as $display.
     * Any errors thrown in the course of the loop will be caight and rethrown. Any errors thrown in the course of the
     * getJavaScripts method will be caught and rethrown.
     *
     * @see            #setisMobile()
     * @see            #getIsMobile()
     */
    public function addMobileJavaScriptVariable()
    {
        $is_mobile = $this->getIsMobile();
        $display = '<script>';
        $display .= 'var fwIsMobile=';
        if ($is_mobile !== false) {
            $display .= (($is_mobile === true) ? 'true' : '"' . $is_mobile . '"');
        } else {
            $display .= 'false';
        }
        $display .= ';';
        $display .= '</script>';

        # Return the accumulated values.
        return $display;
    } #==== End -- addMobileJavaScriptVariable

    /**
     * Puts the correct stylesheets into the header for the page being viewed.
     *
     * @throws Exception
     */
    public function addStyle()
    {
        # Create an empty variable to hold the link tags.
        $links = '';
        try {
            # Get the passed styles. They will be in an Array.
            $styles = $this->getStyle();
            # Loop through the styles.
            foreach ($styles as $css) {
                # Get the Style sheet.
                preg_match('/^[^{]+/i', $css, $stylesheet);
                $stylesheet = $stylesheet[0];
                # Check if the stylesheet ends with a ".css".
                $has_extension = preg_match('/\.css$/i', $stylesheet);
                if ($has_extension === 0) {
                    # Append the extension.
                    $stylesheet .= '.css';
                }
                # Get the stylesheet properties.
                $number_returned = preg_match_all('/({[^}]*})/i', $css, $value_array, PREG_SET_ORDER);

                # Check if there were stylesheet properties.
                if ($number_returned > 0) {
                    # Loop through the properties.
                    for ($key = 0; $key < $number_returned; $key++) {
                        # The properties are in JSON. Decode them to convert them to objects.
                        $current_properties = json_decode($value_array[$key][0]);
                        # Set the device the CSS should be targeting. The default is "all".
                        $device = ((empty($current_properties->device)) ? 'all' : $current_properties->device);
                        # Set the property to be measuring against.
                        $property = ((empty($current_properties->property)) ? '' : $current_properties->property);
                        # Set the tag that is appended to the end of the css filename to make it locatable and unique.
                        $tag = ((empty($current_properties->tag)) ? '' : $current_properties->tag);
                        # Set the value of the property to be measurting against.
                        $value = ((empty($current_properties->value)) ? '' : $current_properties->value);
                        # Build the value of the media attribute for the link tag.
                        $media = $device . ((empty($property)) ? '' : ' and (' . $property . ':' . $value . ')');
                        # Build the name of the stylesheet. If there is no tag, use the passed name without appending anything.
                        $media_css = str_ireplace('.css', ((empty($tag)) ? '' : '.') . $tag . '.css', $stylesheet);
                        # Create the link tag and concatenate it to the links variable.
                        $links .= '<link rel="stylesheet" type="text/css" media="' . $media . '" href="' . $media_css . '">';
                    }
                } else {
                    # Build the link tag with no specific target or properties and concatenate it to the links variable.
                    $links .= '<link rel="stylesheet" type="text/css" href="' . $stylesheet . '">';
                }
            }

            # Return the built link tag(s).
            return $links;
        } catch (Exception $e) {
            throw $e;
        }
    } #==== End -- addStyle

    /**
     * Inserts proper Internet Explorer label into CSS filename (ie turns 'path/style.css' to
     * 'path/'.$ie_version.'.style.css').
     *
     * @param $ie_versions
     *
     * @return string
     * @throws Exception
     */
    public function addIEStyle($ie_versions)
    {
        try {
            # Remove any white space from the passed string (allows developers to be lazy).
            $ie_versions = preg_replace('/\s/', '', $ie_versions);
            # Explode ie versions into an array.
            $ie_versions = explode(',', $ie_versions);
            # Create an empty array to hold
            $ie_style = array(
                'ie' => '',
                'ie11' => '',
                'ie10' => '',
                'ie9' => '',
                'ie8' => '',
                'ie7' => '',
                'ie6' => '',
                'ie5mac' => ''
            );
            # Get the set styles.
            $styles = $this->getStyle();
            # Create an empty variable to hold the link markup display.
            $display_ie_style = '';
            # loop through the stylesheets.
            foreach ($styles as $css) {
                # Get the Style sheet.
                preg_match('/^[^{]+/i', $css, $stylesheet);
                $stylesheet = $stylesheet[0];
                # Check if the stylesheet ends with a ".css".
                $has_extension = preg_match('/\.css$/i', $stylesheet);
                if ($has_extension === 0) {
                    # Append the extension.
                    $stylesheet .= '.css';
                }
                # Get the stylesheet properties.
                $number_returned = preg_match_all('/({[^}]*})/i', $css, $value_array, PREG_SET_ORDER);

                # Get the position of the last backslash.
                $pos = strrpos($stylesheet, '/');
                # Loop through the ie versions array.
                foreach ($ie_versions as $ie_version) {
                    # Extract the filename.
                    $filename = substr($stylesheet, $pos + 1);
                    # Extract the filepath.
                    $filepath = substr($stylesheet, 0, $pos + 1);
                    # Rebuild the stylesheet name with the ie version prepended to the filename.
                    $stylesheet_name = $filepath . $ie_version . '.' . $filename;

                    # Check if the ie version is the VERY OLD IE5 for Mac.
                    if ($ie_version == 'ie5mac') {
                        # Create the appropriate tag to pull in the stylesheet ONLY if the browser is IE5 for Mac.
                        $ie_style[$ie_version] .= '@import("' . $stylesheet_name . '");';
                    } else {
                        # Check if there were stylesheet properties.
                        if ($number_returned > 0) {
                            # Loop through the properties.
                            for ($key = 0; $key < $number_returned; $key++) {
                                # The properties are in JSON. Decode them to convert them to objects.
                                $current_properties = json_decode($value_array[$key][0]);
                                # Set the device the CSS should be targeting. The default is "all".
                                $device = ((empty($current_properties->device)) ? 'all' : $current_properties->device);
                                # Set the property to be measuring against.
                                $property = ((empty($current_properties->property)) ? '' : $current_properties->property);
                                # Set the tag that is appended to the end of the css filename to make it locatable and unique.
                                $tag = ((empty($current_properties->tag)) ? '' : $current_properties->tag);
                                # Set the value of the property to be measurting against.
                                $value = ((empty($current_properties->value)) ? '' : $current_properties->value);
                                # Build the value of the media attribute for the link tag.
                                $media = $device . ((empty($property)) ? '' : ' and (' . $property . ':' . $value . ')');
                                # Build the name of the stylesheet. If there is no tag, use the passed name without appending anything.
                                $media_css = str_ireplace('.css', ((empty($tag)) ? '' : '.') . $tag . '.css',
                                    $stylesheet_name);
                                # Create the link tag and concatenate it to the links variable.
                                $ie_style[$ie_version] .= '<link rel="stylesheet" type="text/css" media="' . $media . '" href="' . $media_css . '">';
                            }
                        } else {
                            # Build the link tag with no specific target or properties and concatenate it to the links variable.
                            $ie_style[$ie_version] .= '<link rel="stylesheet" type="text/css" href="' . $stylesheet_name . '">';
                        }
                    }
                }
            }
            # Build the IE conditionals.
            $display_ie_style .= ((!empty($ie_style['ie'])) ? '<!--[if IE]>' . $ie_style['ie'] . '<![endif]-->' : '');
            $display_ie_style .= ((!empty($ie_style['ie11'])) ? '<!--[if IE 11]>' . $ie_style['ie11'] . '<![endif]-->' : '');
            $display_ie_style .= ((!empty($ie_style['ie10'])) ? '<!--[if IE 10]>' . $ie_style['ie10'] . '<![endif]-->' : '');
            $display_ie_style .= ((!empty($ie_style['ie9'])) ? '<!--[if IE 9]>' . $ie_style['ie9'] . '<![endif]-->' : '');
            $display_ie_style .= ((!empty($ie_style['ie8'])) ? '<!--[if IE 8]>' . $ie_style['ie8'] . '<![endif]-->' : '');
            $display_ie_style .= ((!empty($ie_style['ie7'])) ? '<!--[if IE 7]>' . $ie_style['ie7'] . '<![endif]-->' : '');
            $display_ie_style .= ((!empty($ie_style['ie6'])) ? '<!--[if IE 6]>' . $ie_style['ie6'] . '<![endif]-->' : '');
            $display_ie_style .= ((!empty($ie_style['ie5mac'])) ?
                '<style type="text/css">' . "\n" .
                '/*\*//*/' . "\n" .
                $ie_style['ie5mac'] . "\n" .
                '/**/' . "\n" .
                '</style>'
                : '');

            # Return the built markup.
            return $display_ie_style;
        } catch (Exception $e) {
            throw $e;
        }
    } #==== End -- addIEStyle

    /**
     * Check if URL is a sub-domain and, based off that information, returns the views folder for that subdomain.
     */
    public static function findDomainFolder()
    {
        # Find the sub-domain and set it.
        if (SUB_DOMAIN == "www" || SUB_DOMAIN == "") {
            $folder = '';
        } else {
            $folder = SUB_DOMAIN . DS;
        }

        return $folder;
    } #==== End -- findDomainFolder

    /**
     * Check if the user's device is mobile. If it is, return the type.
     */
    public function findMobileDevice()
    {
        # Set the current device value to a local variable.
        $useragent = (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        $is_mobile = false;

        # Compare the user agent to possible mobile device values. If there is a match set true, otherwise set false.
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|wpdesktop|wphone|ip(ad|hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino|playbook|silk/i',
                $useragent) === 1) {
            $is_mobile = true;
        }
        # Compare the user agent to possible mobile device values. If there is a match set true, otherwise set false.
        if (preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',
                substr($useragent, 0, 4)) === 1) {
            $is_mobile = true;
        }
        # Check specifically for Android.
        if (preg_match('/android/i', $useragent) === 1) {
            $is_mobile = 'Android';
        }
        # Check specifically for BlackBerry.
        if (preg_match('/blackberry/i', $useragent) === 1) {
            $is_mobile = 'BlackBerry';
        }
        # Check specifically for iOS.
        if (preg_match('/ip(ad|hone|od)/i', $useragent) === 1) {
            $is_mobile = 'iOS';
        }
        # Check specifically for Windows phones.
        if (preg_match('/iemobile|wpdesktop|wphone|windows (ce|phone)/i', $useragent) === 1) {
            $is_mobile = 'Windows';
        }
        $this->setIsMobile($is_mobile);

        return $this->getIsMobile();
    } #==== End -- findMobileDevice

    /**
     * Gets the singleton instance of this class.
     */
    public static function getInstance()
    {
        if (!self::$document) {
            self::$document = new Document();
        }

        return self::$document;
    } #==== End -- getInstance

    /**
     * Checks if an URL really leads to a valid page (as opposed to generating “404 Not Found” or some other kind of
     * error).
     *
     * @param    string $url The URL to check.
     *
     * @return bool
     */
    public function pageExists($url)
    {
        $parts = parse_url($url);
        # The URL was seriously wrong.
        if (!$parts) {
            return false;
        }

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);

        # Set the user agent - might help, doesn't hurt.
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        # Try to follow redirects.
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

        # Timeout after the specified number of seconds. assuming that this script runs
        #	on a server, 20 seconds should be plenty of time to verify a valid URL.
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($curl, CURLOPT_TIMEOUT, 20);

        # Don't download the page, just the header (much faster in this case).
        curl_setopt($curl, CURLOPT_NOBODY, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        # Handle HTTPS links.
        if ($parts['scheme'] == 'https') {
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }

        $response = curl_exec($curl);
        curl_close($curl);

        # Get the status code from HTTP headers.
        if (preg_match('/HTTP\/1\.\d+\s+(\d+)/', $response, $matches)) {
            $code = intval($matches[1]);
        } else {
            return false;
        }

        # See if code indicates success.
        return (($code >= 200) && ($code < 400));
    } #==== End -- pageExists

    /**
     * Try PHP header redirect, then Java redirect, then try http redirect.
     *
     * @param string $url   The url to redirect to.
     * @param int    $delay The delay in seconds before redirecting.
     * @param bool   $clear_session_data
     *
     * @return bool
     */
    public function redirect($url, $delay = 0, $clear_session_data = false)
    {
        # Check if the URL is empty.
        if (!empty($url)) {
            if (ob_get_level()) {
                ob_end_clean();
            }
            # Begin output buffering.
            ob_start();
            # Parse the passed url.
            $url_parsed = parse_url($url);
            # Check if the domain is this site.
            if (isset($url_parsed['host']) && ($url_parsed['host'] === FULL_DOMAIN)) {
                $url = $url_parsed['path'] . $url_parsed['query'];
            }
            $session = Session::getInstance();
            # Ensure the $_SESSION data is cleared or kept as passed.
            $session->keepSessionData(!$clear_session_data);
            # Check if headers have already been sent.
            if (headers_sent() === false) {
                # Do a PHP redirect with the passed delay.
                header('Refresh: ' . $delay . '; url=' . str_replace(array("\n", "\r"), array('', ''), $url));
            }
            # Do a Javascript redirect.
            $script = '<script type="text/javascript">';
            $script .= 'setTimeout(function(){window.location="' . $url . '";}),' . ($delay * 1000) . ');';
            $script .= '</script>';
            # Do an HTML redirect.
            $script .= '<noscript>';
            $script .= '<meta http-equiv="refresh" content="' . $delay . ';url=' . $url . '" />';
            $script .= '</noscript>';
            # Output the content. This will cause the header will get pushed as well.
            echo $script;
            # Send the buffer to the user's browser.
            ob_flush();
            # Kill PHP so nothing else will execute.
            die;
        }

        return false;
    } #==== End -- Redirect

    /**
     * Removes GET query from the passed URL. Must be called before removeIndex method.
     *
     * @param string $url The URL to check.
     *
     * @return mixed|string
     */
    public static function removeGetQuery($url)
    {
        return WebUtility::removeGetQuery($url);
    } #==== End -- removeGetQuery

    /**
     * Removes "?page=#" query from the passed URL.
     *
     * @param string $url The URL to check.
     *
     * @return null|string|string[]
     */
    public static function removePageQuery($url)
    {
        return WebUtility::removePageQuery($url);
    } #==== End -- removePageQuery

    /**
     * Removes "index.php" from the passed URL.
     *
     * @param    $url (The URL to check.)
     *
     * @return mixed|string
     */
    public static function removeIndex($url)
    {
        return WebUtility::removeIndex($url);
    } #==== End -- removeIndex

    /**
     * Removes scheme name (ie http://) from the passed URL.
     *
     * @param    $url (The URL to check.)
     *
     * @return null|string|string[]
     */
    public static function removeSchemeName($url)
    {
        return WebUtility::removeSchemeName($url);
    } #==== End -- removeSchemeName

    /**
     * Handles all emailing from one place. A wrapper method for sendMail of the Email class.
     *
     * @param        $subject
     * @param        $to
     * @param        $body
     * @param string $reply_to
     *
     * @return    bool        TRUE/FALSE
     * @throws Exception
     */
    public function sendEmail($subject, $to, $body, $reply_to = SMTP_FROM)
    {
        try {
            # Instantiate a new Email object.
            $email = new Email();
            # Send the mail.
            $sent = $email->sendEmail($subject, $to, $body, $reply_to);

            return $sent;
        } catch (Exception $e) {
            throw $e;
        }
    } #==== End -- sendEmail
}
