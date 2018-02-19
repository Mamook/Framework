<?php

namespace Mamook\API;

# Make sure the script is not accessed directly.
use Document;

if (!defined('BASE_PATH')) {
    exit('No direct script access allowed');
}

/**
 * AddThisAPI
 * The AddThisAPI accesses AddThis data info.
 */
class AddThisAPI
{
    private $share_button_markup = null;

    /**
     * setShareButtonMarkup
     * Sets the data member $share_button_markup.
     *
     * @param    object $share_button_markup
     */
    public function setShareButtonMarkup($share_button_markup)
    {
        # Check if the passed value is empty.
        if (!empty($share_button_markup)) {
            # Set the data member.
            $this->share_button_markup = $share_button_markup;
        } else {
            # Explicitly set the data member to NULL.
            $this->share_button_markup = null;
        }
    } #==== End -- setShareButtonMarkup

    /**
     * getShareButtonMarkup
     * Returns the data member $share_button_markup.
     */
    private function getShareButtonMarkup()
    {
        return $this->share_button_markup;
    } #==== End -- getShareButtonMarkup

    /**
     * __contruct
     * Loads the AddThis JS libraries onto the page.
     */
    public function __construct()
    {
        # Set the Document instance to a variable.
        $doc = Document::getInstance();
        # Include the AddThis JavaScripts in the footer. (Use the script file name before the ".php".)
        $doc->setFooterJS('AddThis');
    } #==== End -- __construct

    /**
     * getDisplayableShareButtonMarkup
     * Gets any custom AddThis share button markup and if there is none, returns the default markup.
     */
    public function getDisplayableShareButtonMarkup()
    {
        $share_button_markup = $this->getShareButtonMarkup();
        # Check if there actually was share button markup.
        if (empty($share_button_markup)) {
            $share_button_markup =
                '<!-- AddThis Button BEGIN -->' .
                '<div class="addthis_toolbox addthis_default_style addthis_16x16_style">' .
                '<a class="addthis_button_preferred_1"></a>' .
                '<a class="addthis_button_preferred_2"></a>' .
                '<a class="addthis_button_google_plusone_badge" g:plusone:size="small"></a>' .
                '<a class="addthis_button_preferred_3"></a>' .
                '<a class="addthis_button_preferred_4"></a>' .
                '<a class="addthis_button_compact"></a>' .
                '</div>' .
                '<!-- AddThis Button END -->';
        }

        return $share_button_markup;
    } #==== End -- getDisplayableShareButtonMarkup
}
