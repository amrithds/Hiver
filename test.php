<?php
error_reporting(E_ALL);

require ('simple_html_dom.php');

function getTagText($object, &$text)
{
    if (empty($object->children)) {
        $trimmedText = trim($object->plaintext);
        if (! empty($trimmedText)) {
            $text[] = $trimmedText;
        }

        return;
    }

    foreach ($object->children as $children) {
        getTagText($children, $text);
    }
}

function getSectionTag($object)
{
    $sections = [];
    foreach ($object->find('section') as $sectionObj) {
        if ($sectionObj->parent()->tag == 'body') {
            $sections[] = $sectionObj;
        }
    }
    return $sections;
}

function cleanStrings($strings)
{
    $formattedWords = [];

    foreach ($strings as $string) {
        //remove extra spaces
        $formattedString = preg_replace("/[^0-9a-zA-Z\s\"{}]/", "", $string);
        $formated = preg_replace('/\s+/', ' ',$formattedString);
        //$formated = preg_replace('/\s+/', ' ',$string);
        //remove special characters
        //$formattedString = preg_replace("/[^0-9a-zA-Z\s\"{}]/", "", $formated);
        //get array of words
        $formattedWords[] = explode(' ', $formated);
    }

    return $formattedWords;
}

function printTopFiveRepeatedWords($formattedWords){
    $wordCountRef = [];
    //var_dump($formattedWords);die;
    foreach ($formattedWords as $wordsArr){
        foreach ($wordsArr as $word){
            $wordIndex = strtolower($word);
            if(!isset($wordCountRef[$wordIndex])){
                $wordCountRef[$wordIndex] = 1;
            }else{
                $wordCountRef[$wordIndex]++;
            }
        }
    }
    //sort array
    arsort($wordCountRef);

    $result = array_slice($wordCountRef, 0, 5, true);

    //print array
    foreach ($result  as $word=> $count){
        echo '<b>'.$word."</b> repeated ". $count." times. <br/>";
    }
}

$html = file_get_html('https://hiverhq.com/');

$texts = [];
$sections = [];
// get body element content
foreach ($html->find('body') as $element) {
    $sections = getSectionTag($element);
}
// find text from each tags from sections
foreach ($sections as $section) {
    getTagText($section, $texts);
}

$formattedWords = cleanStrings($texts);

printTopFiveRepeatedWords($formattedWords);die;