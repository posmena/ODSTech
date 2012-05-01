<?php

    // import octopus feed
    include('../octopusApi.php');
   
    // use the feed API
    $octopus = new OctopusFeed();
    
    // retrieve the location query from the textbox
    $query = $_GET['query'];
    $suggestions = array();
    
    if(!(empty($query))) {
        $suggestions = $octopus->getCitySuggestions($query);
    }
        
    // return JSON
    header('Content-Type: application/json');
    echo json_encode(array(
        'query' => $query,
        'suggestions' => array_map(function($v) { return $v->name; }, $suggestions),
        'data' => array_map(function($v) { return $v->id; }, $suggestions)
    ));
        
?>