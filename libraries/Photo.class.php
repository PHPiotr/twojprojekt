<?

class Photo {

    public static function upload($post, $path) {
        foreach ($_FILES[$post]['tmp_name'] as $key => $tmp) {
            $file = $_FILES[$post]['name'][$key];
            move_uploaded_file($tmp, $path . $file);
        }
    }

    public static function removeDir($dir) {
        $it = new RecursiveDirectoryIterator($dir);
        $files = new RecursiveIteratorIterator($it,
                        RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                continue;
            }
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        rmdir($dir);
    }

    public static function extension($filename) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        return strtolower($ext);
    }

    public function uploadOne($post, $path) {
        move_uploaded_file($_FILES[$post]['tmp_name'], $path . $_FILES[$post]['name']);
    }

    public function uploadRename($post, $path, $name = false) {
        if (!empty($_FILES[$post]['name'])) {
            $ext = self::extension($_FILES[$post]['name']);
            $new_name = $name == true ? $name : $post;
            $new_name = $new_name . '.' . strtolower($ext);
            move_uploaded_file($_FILES[$post]['tmp_name'], $path . $new_name);
        }
    }
    
    public static function uploadMultipleRename($post, $path) {
        foreach ($_FILES[$post]['tmp_name'] as $key => $tmp) {
            $filename = $_FILES[$post]['name'][$key];
            $ext = self::extension($filename);
            $name = Url::create(basename($filename, $ext));
            $new_name = $name . '.' . strtolower($ext);
            move_uploaded_file($tmp, $path . $new_name);
        }
    }

    public static function resize($width, $height, $path, $destination) {

        $directory = array_slice(scandir($path), 2);
        foreach ($directory as $file) {
            if (is_file($path . $file)) {

                $src_size = getimagesize($path . '/' . $file);
                $thumb_width = $width;
                $thumb_height = $height;

                if ($src_size['mime'] === 'image/jpeg') {
                    $src = imagecreatefromjpeg($path . $file);
                }

                $src_aspect = round(($src_size[0] / $src_size[1]), 1);
                $thumb_aspect = round(($thumb_width / $thumb_height), 1);

                if ($src_aspect < $thumb_aspect) {
                    $new_size = array($thumb_width, ($thumb_width / $src_size[0]) * $src_size[1]);
                    $src_pos = array(0, ($new_size[1] - $thumb_height) / 2);
                } else if ($src_aspect > $thumb_aspect) {
                    $new_size = array(($thumb_width / $src_size[1]) * $src_size[0], $thumb_height);
                    $src_pos = array(($new_size[0] - $thumb_width) / 2, 0);
                } else {
                    $new_size = array($thumb_width, $thumb_height);
                    $src_pos = array(0, 0);
                }

                if ($new_size[0] < 0) {
                    $new_size[0] == 1;
                }

                if ($new_size[1] < 0) {
                    $new_size[1] == 1;
                }

                $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
                imagecopyresampled($thumb, $src, 0, 0, $src_pos[0], $src_pos[1], $new_size[0], $new_size[1], $src_size[0], $src_size[1]);
                if ($src_size['mime'] === 'image/jpeg') {
                    imagejpeg($thumb, $destination . $file);
                }
            }
        }
    }

    public static function resizeOne($width, $height, $file, $path, $destination) {

        if (is_file($path . $file)) {

            $src_size = getimagesize($path . '/' . $file);
            $thumb_width = $width;
            $thumb_height = $height;

            if ($src_size['mime'] === 'image/jpeg') {
                $src = imagecreatefromjpeg($path . $file);
            }

            $src_aspect = round(($src_size[0] / $src_size[1]), 1);
            $thumb_aspect = round(($thumb_width / $thumb_height), 1);

            if ($src_aspect < $thumb_aspect) {
                $new_size = array($thumb_width, ($thumb_width / $src_size[0]) * $src_size[1]);
                $src_pos = array(0, ($new_size[1] - $thumb_height) / 2);
            } else if ($src_aspect > $thumb_aspect) {
                $new_size = array(($thumb_width / $src_size[1]) * $src_size[0], $thumb_height);
                $src_pos = array(($new_size[0] - $thumb_width) / 2, 0);
            } else {
                $new_size = array($thumb_width, $thumb_height);
                $src_pos = array(0, 0);
            }

            if ($new_size[0] < 0) {
                $new_size[0] == 1;
            }

            if ($new_size[1] < 0) {
                $new_size[1] == 1;
            }

            $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
            imagecopyresampled($thumb, $src, 0, 0, $src_pos[0], $src_pos[1], $new_size[0], $new_size[1], $src_size[0], $src_size[1]);
            if ($src_size['mime'] === 'image/jpeg') {
                imagejpeg($thumb, $destination . $file);
            }
        }
    }

    public static function create($width, $height, $text, $save) {

        $im = imagecreatetruecolor($width, $height);
        imageantialias($im, true);
        imagefilledrectangle($im, 2, 2, $width - 2, $height - 2, 0x555555);
        $p = 1;
        for ($i = 0; $i < strlen($text); $i++) {
            imagettftext(
                    $im, rand(14, 17), rand(-30, 30), 14 * $p, 90, 0xFFFFFF, '/libs/fonts/arial.ttf', // czcionka
                    $text[$i]
            );
            $p++;
        }
        imagejpeg($im, $save);
        imagedestroy($im);
    }

    public static function rename($file) {
        
    }

}