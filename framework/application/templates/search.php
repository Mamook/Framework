<?php

use Mamook\Form\SearchFormProcessor;
use Mamook\Utility\Utility;

# Instantiate a new SearchFormProcessor object.
$search_form_processor = new SearchFormProcessor();

# Get the search form.
require Utility::locateFile(TEMPLATES . 'forms' . DS . 'search_form.php');

echo '<section id="searchbox">' .
    $display_search_form .
    '</section>';
