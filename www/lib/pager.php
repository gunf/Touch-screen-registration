<?php

class Pager {

    private $itemsCount;
    private $currentPage;
    private $itemsPerPage;
    private $name;

    function __construct($name, $itemsCount, $currentPage, $itemsPerPage) {
        $this->name = $name;
        $this->itemsCount = $itemsCount;
        $this->currentPage = $currentPage;
        $this->itemsPerPage = $itemsPerPage;
    }

    function getPaging() {
        return array($this->currentPage * $this->itemsPerPage, $this->itemsPerPage);
    }

    function getParams() {
        return $this->name . "_per_page=$this->itemsPerPage";
    }

    function getHtml($class, $additionalHtml, $itemsVisible = 30) {
        $result = "<div class=\"$class\">";
        $pagesCount = ceil($this->itemsCount / $this->itemsPerPage);
        $result .= "<div class=\"page\" >...</div>";

        for ($page = $this->currentPage - $itemsVisible / 2; $page < ($this->currentPage + $itemsVisible / 2); $page++) {
            if (($page >= 0) && ($page < $pagesCount)) {
                $result .= "<div class=\"page " . ($page == $this->currentPage ? "current" : "") . "\">";
                $result .= "<a href=\"?$this->name" . "_current=$page&$this->name" . "_per_page=$this->itemsPerPage&$additionalHtml\">" . ($page + 1) . "</a>";
                $result .= "</div>";
            }
        }
        $result .= "<div class=\"page\" >...</div>";
        $result .= "</div>";
        return $result;
    }

}

?>
