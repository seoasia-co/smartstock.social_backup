<?php
use Illuminate\Support\Str;
use DOMDocument;

function rearrangeImage($htmlString, $position='middle')
{
    $dom = new DOMDocument();
    
    $dom->loadHTML($htmlString, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    $images = $dom->getElementsByTagName('img');
    $image = $images->item(0);

    switch($position) {
        case 'middle':
            $paragraphs = $dom->getElementsByTagName('p');
            $middleParagraph = $paragraphs->item(intval($paragraphs->length / 2));
            $middleParagraph->insertBefore($image, $middleParagraph->firstChild);
            break;
        case 'topright':
        case 'top-right':
            $body = $dom->getElementsByTagName('body')->item(0);
            $image->setAttribute('style', 'float:right');
            $body->insertBefore($image, $body->firstChild);
            break;
        case'above':
            $body = $dom->getElementsByTagName('body')->item(0);
            $body->insertBefore($image, $body->firstChild);
            break;
        default:
            break;
    }

    $newHtmlString = $dom->saveHTML();
    return $newHtmlString;
}

/* You can use this function like this:

php
$htmlString = '<html>... some your sample HTML here...</html>';

//rearrange image to the middle of the paragraph
echo rearrangeImage($htmlString, 'middle');

//rearrange image to the top right of the body area
echo rearrangeImage($htmlString, 'topright');

//rearrange image to be the first element of the body area
echo rearrangeImage($htmlString, 'above');
```
The `float:right` CSS style is used in the `top-right` case to align the image on the right side of the body area. */