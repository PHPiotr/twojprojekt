<?php

class Pagination {

    /**
     * Creates pagination if needed.
     * @param int $pages Total amount of pages
     * @param int $page Current page
     * @param string $link Link to page 
     * @return string Pagination
     */
    public static function create($pages, $page, $link) {
        $pagination = '';
        if ($pages > 1) {
//            $pagination .= '<div class="pagination pagination-small text-align-right">';
            $pagination .= '<ul>';
            if ($page > 1) {
                if ($page > 5) {
                    $pagination .= '<li><a href="' . $link . '/1" title="Pierwsza">&laquo;</a></li>';
                }
                $pageMinusOne = $page - 1;
                $pagination .= '<li><a href="' . $link . '/' . $pageMinusOne . '" title="Poprzednia">&lsaquo;</a></li>';
            }
            $total = $pages;
            if ($total > 9) {
                $counter = $page - 4 <= 0 ? 1 : ($page > $pages - 4 ? $pages - 8 : $page - 4);
                $pages = $page + 4 > $pages ? $pages : ($page < 5 ? 9 : $page + 4);
            } else {
                $counter = 1;
            }
            for ($i = $counter; $i <= $pages; $i++) {
                $class = $i == $page ? ' class="active"' : null;
                $pagination .= '<li' . $class . '><a href="' . $link . '/' . $i . '">' . $i . '</a></li>';
            }
            if ($page < $pages) {
                $pagePlusOne = $page + 1;
                $pagination .= '<li><a href="' . $link . '/' . $pagePlusOne . '" title="Następna">&rsaquo;</a></li>';
                if ($page < ($total - 4)) {
                    $pagination .= '<li><a href="' . $link . '/' . $total . '" title="Ostatnia">&raquo;</a></li>';
                }
            }
            $pagination .= '</ul>';
//            $pagination .= '</div>';
            return $pagination;
        }
    }

}