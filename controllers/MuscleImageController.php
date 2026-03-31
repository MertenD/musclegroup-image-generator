<?php

class MuscleImageController {

    static array $availableMuscleGroups = array(
        "all",
        "all_lower",
        "all_upper",
        "abductors",
        "abs",
        "adductors",
        "back",
        "back_lower",
        "back_upper",
        "biceps",
        "calfs",
        "chest",
        "core",
        "core_side",
        "core_lower",
        "core_upper",
        "forearms",
        "gluteus",
        "hamstring",
        "hands",
        "latissimus",
        "legs",
        "neck",
        "quadriceps",
        "shoulders",
        "shoulders_back",
        "shoulders_front",
        "triceps"
    );

    public static function getMuscleGroups() {

        header('Content-Type: application/json');
        header("Access-Control-Allow-Origin: *");

        echo json_encode(MuscleImageController::$availableMuscleGroups, JSON_PRETTY_PRINT);
    }

    public static function testCreateImage() {

        header('Content-Type: plain/text');
        header("Access-Control-Allow-Origin: *");

        $im = imagecreatetruecolor(120, 20);
        $text_color = imagecolorallocate($im, 233, 14, 91);
        imagestring($im, 1, 5, 5,  'A Simple Text String', $text_color);
        var_dump($im);
        imagepng($im);
        imagedestroy($im);
    }

    public static function getBaseImage($transparentBackground) {
        header('Content-Type: image/png');
        header("Access-Control-Allow-Origin: *");

        if ($transparentBackground == null || $transparentBackground == 0) {
            $baseImage = imagecreatefrompng('./resources/images/baseImage.png');
        } else {
            $baseImage = imagecreatefrompng('./resources/images/baseImage_transparent.png');
        }

        imagepng($baseImage);
        imagedestroy($baseImage);
    }

    public static function getMuscleImage($muscleGroupsQuery, $transparentBackground) {

        header('Content-Type: image/png');
        header("Access-Control-Allow-Origin: *");

        if ($transparentBackground == null || $transparentBackground == 0) {
            $baseImage = imagecreatefrompng('./resources/images/baseImage.png');
        } else {
            $baseImage = imagecreatefrompng('./resources/images/baseImage_transparent.png');
        }

        $muscleGroups = explode(",", $muscleGroupsQuery);
        foreach ($muscleGroups as $muscleGroup) {

            if (!in_array($muscleGroup, MuscleImageController::$availableMuscleGroups)) {
                http_response_code(400);
                exit;
            }

            $muscleGroupImage = imagecreatefrompng('./resources/images/' . $muscleGroup . '.png');

            imagealphablending($baseImage, false);
            imagesavealpha($baseImage, true);

            imagecopymerge($baseImage, $muscleGroupImage, 0, 0, 0, 0, 1920, 1920, 100);
            imagedestroy($muscleGroupImage);
        }

        imagepng($baseImage);
        imagedestroy($baseImage);
    }

    public static function getMuscleImageWithCustomColor($muscleGroupsQuery, $colorQuery, $transparentBackground) {

        header('Content-Type: image/png');
        header("Access-Control-Allow-Origin: *");

        if ($transparentBackground == null || $transparentBackground == 0) {
            $baseImage = imagecreatefrompng('./resources/images/baseImage.png');
        } else {
            $baseImage = imagecreatefrompng('./resources/images/baseImage_transparent.png');
        }

        // Support both HEX and RGB formats
        if (strpos($colorQuery, ',') !== false) {
            // RGB format: "255,0,0"
            $colorRgb = explode(",", $colorQuery);
            $red = $colorRgb[0];
            $green = $colorRgb[1];
            $blue = $colorRgb[2];
        } else {
            // HEX format: "#FF0000" or "FF0000"
            $rgbColor = self::hex2RGB($colorQuery);
            $red = $rgbColor["red"];
            $green = $rgbColor["green"];
            $blue = $rgbColor["blue"];
        }

        $muscleGroups = explode(",", $muscleGroupsQuery);
        foreach ($muscleGroups as $muscleGroup) {

            if (!in_array($muscleGroup, MuscleImageController::$availableMuscleGroups)) {
                http_response_code(400);
                exit;
            }

            $muscleGroupImage = imagecreatefrompng('./resources/images/' . $muscleGroup . '.png');

            $index = imagecolorexact($muscleGroupImage,89,136,255);
            imagecolorset($muscleGroupImage, $index, $red, $green, $blue);

            imagealphablending($baseImage, false);
            imagesavealpha($baseImage, true);

            imagecopymerge($baseImage, $muscleGroupImage, 0, 0, 0, 0, 1920, 1920, 100);
            imagedestroy($muscleGroupImage);
        }

        imagepng($baseImage);
        imagedestroy($baseImage);
    }

    public static function getMuscleImageWithMultiColor($primaryMuscleGroupsQuery, $secondaryMuscleGroupsQuery, $primaryColorQuery, $secondaryColorQuery, $transparentBackground) {

        header('Content-Type: image/png');
        header("Access-Control-Allow-Origin: *");

        if ($transparentBackground == null || $transparentBackground == 0) {
            $baseImage = imagecreatefrompng('./resources/images/baseImage.png');
        } else {
            $baseImage = imagecreatefrompng('./resources/images/baseImage_transparent.png');
        }

        // Support both HEX and RGB formats for primary color
        if (strpos($primaryColorQuery, ',') !== false) {
            // RGB format: "255,0,0"
            $primaryColorRgb = explode(",", $primaryColorQuery);
            $primaryRed = $primaryColorRgb[0];
            $primaryGreen = $primaryColorRgb[1];
            $primaryBlue = $primaryColorRgb[2];
        } else {
            // HEX format: "#FF0000" or "FF0000"
            $primaryRgbColor = self::hex2RGB($primaryColorQuery);
            $primaryRed = $primaryRgbColor["red"];
            $primaryGreen = $primaryRgbColor["green"];
            $primaryBlue = $primaryRgbColor["blue"];
        }

        // Support both HEX and RGB formats for secondary color
        if (strpos($secondaryColorQuery, ',') !== false) {
            // RGB format: "255,0,0"
            $secondaryColorRgb = explode(",", $secondaryColorQuery);
            $secondaryRed = $secondaryColorRgb[0];
            $secondaryGreen = $secondaryColorRgb[1];
            $secondaryBlue = $secondaryColorRgb[2];
        } else {
            // HEX format: "#FF0000" or "FF0000"
            $secondaryRgbColor = self::hex2RGB($secondaryColorQuery);
            $secondaryRed = $secondaryRgbColor["red"];
            $secondaryGreen = $secondaryRgbColor["green"];
            $secondaryBlue = $secondaryRgbColor["blue"];
        }


        $primaryMuscleGroups = explode(",", $primaryMuscleGroupsQuery);
        foreach ($primaryMuscleGroups as $muscleGroup) {

            if (!in_array($muscleGroup, MuscleImageController::$availableMuscleGroups)) {
                http_response_code(400);
                exit;
            }

            $muscleGroupImage = imagecreatefrompng('./resources/images/' . $muscleGroup . '.png');

            $index = imagecolorexact($muscleGroupImage,89,136,255);
            imagecolorset($muscleGroupImage, $index, $primaryRed, $primaryGreen, $primaryBlue);

            imagealphablending($baseImage, false);
            imagesavealpha($baseImage, true);

            imagecopymerge($baseImage, $muscleGroupImage, 0, 0, 0, 0, 1920, 1920, 100);
            imagedestroy($muscleGroupImage);
        }

        $secondaryMuscleGroups = explode(",", $secondaryMuscleGroupsQuery);
        foreach ($secondaryMuscleGroups as $muscleGroup) {

            if (!in_array($muscleGroup, MuscleImageController::$availableMuscleGroups)) {
                http_response_code(400);
                exit;
            }

            $muscleGroupImage = imagecreatefrompng('./resources/images/' . $muscleGroup . '.png');

            $index = imagecolorexact($muscleGroupImage,89,136,255);
            imagecolorset($muscleGroupImage, $index, $secondaryRed, $secondaryGreen, $secondaryBlue);

            imagealphablending($baseImage, false);
            imagesavealpha($baseImage, true);

            imagecopymerge($baseImage, $muscleGroupImage, 0, 0, 0, 0, 1920, 1920, 100);
            imagedestroy($muscleGroupImage);
        }

        imagepng($baseImage);
        imagedestroy($baseImage);
    }

    public static function getIndividualColorImage($muscleGroups, $colors, $transparentBackground) {

        header('Content-Type: image/png');
        header("Access-Control-Allow-Origin: *");

        if ($transparentBackground == null || $transparentBackground == 0) {
            $baseImage = imagecreatefrompng('./resources/images/baseImage.png');
        } else {
            $baseImage = imagecreatefrompng('./resources/images/baseImage_transparent.png');
        }

        $colorsArray = explode(",", $colors);
        $muscleGroupsArray = explode(",", $muscleGroups);

        $counter = 0;
        foreach ($muscleGroupsArray as $muscleGroup) {

            if ($muscleGroup != "") {
                if (!in_array($muscleGroup, MuscleImageController::$availableMuscleGroups)) {
                    http_response_code(400);
                    exit;
                }

                $muscleGroupImage = imagecreatefrompng('./resources/images/' . $muscleGroup . '.png');

                $index = imagecolorexact($muscleGroupImage, 89, 136, 255);
                $rgbColor = self::hex2RGB($colorsArray[$counter]);
                if ($counter < sizeof($colorsArray) - 1) {
                    $counter++;
                }
                imagecolorset($muscleGroupImage, $index, $rgbColor["red"], $rgbColor["green"], $rgbColor["blue"]);

                imagealphablending($baseImage, false);
                imagesavealpha($baseImage, true);

                imagecopymerge($baseImage, $muscleGroupImage, 0, 0, 0, 0, 1920, 1920, 100);
                imagedestroy($muscleGroupImage);
            }
        }

        imagepng($baseImage);
        imagedestroy($baseImage);
    }

    /**
     * Convert a hexa decimal color code to its RGB equivalent
     *
     * @param string $hexStr (hexadecimal color value)
     * @param boolean $returnAsString (if set true, returns the value separated by the separator character. Otherwise returns associative array)
     * @param string $seperator (to separate RGB values. Applicable only if second parameter is true.)
     * @return array or string (depending on second parameter. Returns False if invalid hex color value)
     */
    public static function hex2RGB(string $hexStr, bool $returnAsString = false, string $separator = ',') {
        $hexStr = preg_replace("/[^\dA-Fa-f]/", '', $hexStr); // Gets a proper hex string
        $rgbArray = array();
        if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
            $colorVal = hexdec($hexStr);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;
        } elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
            $rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
            $rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
            $rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
        } else {
            http_response_code(400);
            exit; //Invalid hex color code
        }
        return $returnAsString ? implode($separator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
    }
}