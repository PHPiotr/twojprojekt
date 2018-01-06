<?

defined('_PHPIOTR') or die('Restricted access');

class View {   

    public function render($page_dir, $page = false) {        
        
        $header = 'templates/header.php';
        $footer = 'templates/footer.php';

        include($header);
        include($page == true ? "templates/{$page_dir}/{$page}.php" : "templates/{$page_dir}/index.php");
        include($footer);
    }

    public function sendHeader($page) {
        header("Location:$page");
        exit();
    }

}