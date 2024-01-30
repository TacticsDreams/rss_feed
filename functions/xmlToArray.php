<?php
function xmlToArray($xml) {
    $array = [];
    foreach ($xml as $element) {
        $item = [];
        foreach ($element as $key => $value) {
            $item[$key] = (string) $value;
        }
        $array[] = $item;
    }
    return $array;
}
?>