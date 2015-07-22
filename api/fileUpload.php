<?php
    $sanitizedPath = sanitizeFilename($_POST['path']);
    $path = '../uploads/' . $sanitizedPath . '/';
    if ( ! is_dir($path)) {
        mkdir($path);
    }
    $filename = sanitizeFilename($_FILES['file']['name']);
    $destination = $path . $filename;
    if(move_uploaded_file( $_FILES['file']['tmp_name'] , $destination )) {
        #echo $sanitizedPath.'/'.$filename; 
        $fileDetails = array(
            path  =>  $sanitizedPath.'/'.$filename,
            fileName => $filename
        );
        echo json_encode($fileDetails);
    };
    /**
     * Function: sanitize
     * Returns a sanitized string, typically for URLs.
     *
     * Parameters:
     *     $string - The string to sanitize.
     *     $force_lowercase - Force the string to lowercase?
     *     $anal - If set to *true*, will remove all non-alphanumeric characters.
     */
    function sanitize($string, $force_lowercase = true, $anal = false) {
        $strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
                       "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
                       "â€”", "â€“", ",", "<", ".", ">", "/", "?");
        $clean = trim(str_replace($strip, "", strip_tags($string)));
        $clean = preg_replace('/\s+/', "-", $clean);
        $clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "", $clean) : $clean ;
        return ($force_lowercase) ?
            (function_exists('mb_strtolower')) ?
                mb_strtolower($clean, 'UTF-8') :
                strtolower($clean) :
            $clean;
    }
    function sanitizeFilename($f) {
     // a combination of various methods
     // we don't want to convert html entities, or do any url encoding
     // we want to retain the "essence" of the original file name, if possible
     // char replace table found at:
     // http://www.php.net/manual/en/function.strtr.php#98669
     $replace_chars = array(
         'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
         'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
         'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
         'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
         'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
         'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
         'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f'
     );
     $f = strtr($f, $replace_chars);
     // convert & to "and", @ to "at", and # to "number"
     $f = preg_replace(array('/[\&]/', '/[\@]/', '/[\#]/'), array('-and-', '-at-', '-number-'), $f);
     $f = preg_replace('/[^(\x20-\x7F)]*/','', $f); // removes any special chars we missed
     $f = str_replace(' ', '-', $f); // convert space to hyphen 
     $f = str_replace('\'', '', $f); // removes apostrophes
     $f = preg_replace('/[^\w\-\.]+/', '', $f); // remove non-word chars (leaving hyphens and periods)
     $f = preg_replace('/[\-]+/', '-', $f); // converts groups of hyphens into one
     return strtolower($f);
    }
?>
