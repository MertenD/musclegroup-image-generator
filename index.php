<?php

include('util/Router.php');
include('controllers/MuscleImageController.php');

Router::createRoute('get', '/getMuscleGroups', function() {
    MuscleImageController::getMuscleGroups();
});

Router::createRouteWithQueryParameters('get', '/getImage', array(
    'muscleGroups' => Router::$PARAMETER_TYPE['string'],
    'color' => Router::$PARAMETER_TYPE['string'],
    'transparentBackground' => Router::$PARAMETER_TYPE['number']
), function($muscleGroups, $color, $transparentBackground) {
    if ($muscleGroups == null) {
        http_response_code(400);
        exit;
    }
    if ($color == null) {
        MuscleImageController::getMuscleImage($muscleGroups, $transparentBackground);
    } else {
        MuscleImageController::getMuscleImageWithCustomColor($muscleGroups, $color, $transparentBackground);
    }
});

Router::createRouteWithQueryParameters('get', '/getMulticolorImage', array(
    'primaryMuscleGroups' => Router::$PARAMETER_TYPE['string'],
    'secondaryMuscleGroups' => Router::$PARAMETER_TYPE['string'],
    'primaryColor' => Router::$PARAMETER_TYPE['string'],
    'secondaryColor' => Router::$PARAMETER_TYPE['string'],
    'transparentBackground' => Router::$PARAMETER_TYPE['number']
), function($primaryMuscleGroups, $secondaryMuscleGroups, $primaryColor, $secondaryColor, $transparentBackground) {
    if ($primaryMuscleGroups == null || $secondaryMuscleGroups == null || $primaryColor == null || $secondaryColor == null) {
        http_response_code(400);
        exit;
    }
    MuscleImageController::getMuscleImageWithMultiColor($primaryMuscleGroups, $secondaryMuscleGroups, $primaryColor, $secondaryColor, $transparentBackground);
});

Router::createRouteWithQueryParameters('get', '/getIndividualColorImage', array(
    'muscleGroups' => Router::$PARAMETER_TYPE['string'],
    'colors' => Router::$PARAMETER_TYPE['string'],
    'transparentBackground' => Router::$PARAMETER_TYPE['number']
), function ($muscleGroups, $colors, $transparentBackground) {
    if ( $muscleGroups == null || $colors == null) {// || sizeof(explode(",", $muscleGroups)) < sizeof(explode(",", $colors))) {
        http_response_code(400);
        exit;
    }
    MuscleImageController::getIndividualColorImage($muscleGroups, $colors, $transparentBackground);
});

Router::createRouteWithQueryParameters('get', '/getBaseImage', array(
    'transparentBackground' => Router::$PARAMETER_TYPE['number']
), function($transparentBackground) {
    MuscleImageController::getMuscleImage("", $transparentBackground);
});

Router::createRoute('get', '/', function() {
    echo "Welcome to the muscle group image generator api.";
});

Router::createRoute('get', '/test', function() {
    MuscleImageController::testCreateImage();
});

// Start the Router
Router::run('/');