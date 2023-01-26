<?php

class Image {

    public function crop_image($original_filename, $cropped_filename, $max_width, $max_height) {

        if (file_exists($original_filename)) {

            $original_image = imagecreatefromjpeg($original_filename);
            $original_width = imagesx($original_image);
            $original_height = imagesy($original_image);

            if ($original_height > $original_width) {
                $ratio = $max_width / $original_width;
                $new_width = $max_width;
                $new_height = $ratio * $original_height;
            }
            else {
                $ratio = $max_height / $original_height;
                $new_height = $max_height;
                $new_width = $ratio * $original_width;
            }
        }

        // adjust in case max width and max height differ
        if ($max_width < $max_height) {
            if ($max_height > $new_height) {
                $adjustment = $max_height / $new_height;
            }
            else {
                $adjustment = $new_height / $max_height;
            }
            $new_width *= $adjustment;
            $new_height *= $adjustment;
        }
        else if ($max_width > $max_height) {
            if ($max_width > $new_width) {
                $adjustment = $max_width / $new_width;
            }
            else {
                $adjustment = $new_width / $max_width;
            }
            $new_width *= $adjustment;
            $new_height *= $adjustment;
        }

        $new_image = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($new_image, $original_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
        imagedestroy($original_image);

        if ($max_width == $max_height) {
            if ($new_height > $new_width) {
                $diff = $new_height - $new_width;
                $y = round($diff / 2);
                $x = 0;
            }
            else {
                $diff = $new_width - $new_height;
                $x = round($diff / 2);
                $y = 0;
            }
        }
        else if ($max_width > $max_height) {
            if ($new_height > $max_height) {
                $diff = $new_height - $max_height;
            }
            else {
                $diff = $max_height - $new_height;
            }
            $y = round($diff / 2);
            $x = 0;
        }
        else {
            if ($new_width > $max_width) {
                $diff = $new_width - $max_width;
            }
            else {
                $diff = $max_width - $new_width;
            }
            $x = round($diff / 2);
            $y = 0;
        }

        $new_cropped_image = imagecreatetruecolor($max_width, $max_height);
        imagecopyresampled($new_cropped_image, $new_image, 0, 0, $x, $y, $max_width, $max_height, $max_width, $max_height);
        imagedestroy($new_image);
        imagejpeg($new_cropped_image, $cropped_filename, 90);
        imagedestroy($new_cropped_image);

    }

    public function fit_image_width($original_filename, $width_fit_filename, $max_width) {

        if (file_exists($original_filename)) {

            $original_image = imagecreatefromjpeg($original_filename);
            $original_width = imagesx($original_image);
            $original_height = imagesy($original_image);

            if ($original_width > $max_width) {
                $ratio = $max_width / $original_width;
                $new_width = $max_width;
                $new_height = $ratio * $original_height;
            }
        }

        $new_image = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($new_image, $original_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
        imagedestroy($original_image);

        imagejpeg($new_image, $width_fit_filename, 90);
        imagedestroy($new_image);

    }

    public function resize_image($original_filename, $resized_filename, $max_width, $max_height) {

        if (file_exists($original_filename)) {

            $original_image = imagecreatefromjpeg($original_filename);
            $original_width = imagesx($original_image);
            $original_height = imagesy($original_image);

            if ($original_height > $original_width) {
                $ratio = $max_width / $original_width;
                $new_width = $max_width;
                $new_height = $ratio * $original_height;
            }
            else {
                $ratio = $max_height / $original_height;
                $new_height = $max_height;
                $new_width = $ratio * $original_width;
            }
        }

        // adjust in case max width and max height differ
        if ($max_width < $max_height) {
            if ($max_height > $new_height) {
                $adjustment = $max_height / $new_height;
            }
            else {
                $adjustment = $new_height / $max_height;
            }
            $new_width *= $adjustment;
            $new_height *= $adjustment;
        }
        else if ($max_width > $max_height) {
            if ($max_width > $new_width) {
                $adjustment = $max_width / $new_width;
            }
            else {
                $adjustment = $new_width / $max_width;
            }
            $new_width *= $adjustment;
            $new_height *= $adjustment;
        }

        $new_image = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($new_image, $original_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
        imagedestroy($original_image);

        imagejpeg($new_image, $resized_filename, 90);
        imagedestroy($new_image);

    }

    public function get_thumb_cover($filename) {

        $thumbnail = $filename . "_cover_thumb.jpg";
        if (file_exists($thumbnail)) {
            return $thumbnail;
        }

        $this -> crop_image($filename, $thumbnail, 1366, 488);
        if (file_exists($thumbnail)) {
            return $thumbnail;
        }
        else {
            return $filename;
        }
    }

    public function get_thumb_profile($filename) {

        $thumbnail = $filename . "_profile_thumb.jpg";
        if (file_exists($thumbnail)) {
            return $thumbnail;
        }

        $this -> crop_image($filename, $thumbnail, 800, 800);
        if (file_exists($thumbnail)) {
            return $thumbnail;
        }
        else {
            return $filename;
        }
    }

    public function get_thumb_post($filename) {

        $thumbnail = $filename . "_post_thumb.jpg";
        if (file_exists($thumbnail)) {
            return $thumbnail;
        }

        $this -> fit_image_width($filename, $thumbnail, 600);
        if (file_exists($thumbnail)) {
            return $thumbnail;
        }
        else {
            return $filename;
        }
    }

    public function generate_filename($length) {

        $array = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
                    "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z",
                    "A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
        $text = "";

        for ($x = 0; $x < $length; $x ++) {
            $random = rand(0, 61);
            $text .= $array[$random];
        }

        return $text;
    }

}