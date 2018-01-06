<?

class Url {
    
    public static function create($url){
        
    $zamiana = array(
        'ą' => 'a', 'ę' => 'e', 'ś' => 's', 'ć' => 'c',
        'ó' => 'o', 'ń' => 'n', 'ż' => 'z', 'ź' => 'z', 'ł' => 'l',
        'Ą' => 'A', 'Ę' => 'E', 'Ś' => 'S', 'Ć' => 'C',
        'Ó' => 'O', 'Ń' => 'N', 'Ż' => 'Z', 'Ź' => 'Z', 'Ł' => 'L'
    );       
        
        $url = str_replace(array_keys($zamiana), array_values($zamiana), $url);
        $url = strtolower($url);
        $url = str_replace(' ', '-', $url);
        $url = preg_replace('/[^0-9a-z\-]+/', '', $url);
        $url = preg_replace('/[\-]+/', '-', $url);
        $url = trim($url, '-'); 
        return $url;
    }
    
    public static function createPostValue($url){
        
    $zamiana = array(
        'ą' => 'a', 'ę' => 'e', 'ś' => 's', 'ć' => 'c',
        'ó' => 'o', 'ń' => 'n', 'ż' => 'z', 'ź' => 'z', 'ł' => 'l',
        'Ą' => 'A', 'Ę' => 'E', 'Ś' => 'S', 'Ć' => 'C',
        'Ó' => 'O', 'Ń' => 'N', 'Ż' => 'Z', 'Ź' => 'Z', 'Ł' => 'L'
    );       
        
        $url = str_replace(array_keys($zamiana), array_values($zamiana), $url);
        $url = strtolower($url);
        $url = str_replace(' ', '_', $url);
        $url = preg_replace('/[^0-9a-z\-]+/', '', $url);
        $url = preg_replace('/[\-]+/', '_', $url);
        $url = trim($url, '_'); 
        return $url;
    }

}
