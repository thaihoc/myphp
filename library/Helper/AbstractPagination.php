<?php

namespace Nth\Helper;

use Zend\Stdlib\ArrayObject;
use Nth\Helper\Convertor;

abstract class AbstractPagination {

    const PAGE_PARAM = 'page';
    const ITEMS_PER_PAGE_PARAM = 'items-per-page';
    const MAX_ITEMS_PER_PAGE = 100;

    private $itemsPerPage;
    private $currentPage;
    private $totalItems;
    private $visiblePages;
    private $queryParams = array();
    private $url;
    private $data;
    private $hiddenParameters = array();
    private $infoVisibility = false;

    public function __construct($url, $totalItems = 0, $itemsPerPage = 15, $currentPage = 1, $visiblePages = 7) {
        $this->setUrl($url);
        $this->setTotalItems($totalItems);
        $this->setItemsPerPage($itemsPerPage);
        $this->setCurrentPage($currentPage);
        $this->setVisiblePages($visiblePages);
        $this->hideQueryParameter(self::ITEMS_PER_PAGE_PARAM);
    }
    
    public function getInfoVisibility() {
        return $this->infoVisibility;
    }

    public function setInfoVisibility($infoVisibility) {
        $this->infoVisibility = $infoVisibility;
        return $this;
    }
    
    public function getHiddenParameters() {
        if (is_array($this->hiddenParameters)) {
            return $this->hiddenParameters;
        }
        return [];
    }

    public function setHiddenParameters($hiddenParameters) {
        $hp = [];
        if ($hiddenParameters instanceof ArrayObject) {
            $hp = $hiddenParameters->getArrayCopy();
        } else if (is_array($hiddenParameters)) {
            $hp = $hiddenParameters;
        }
        $this->hiddenParameters = $hp;
    }

    public function showQueryParameter($parameter) {
        $key = array_search($parameter, $this->getHiddenParameters());
        if (false !== $key) {
            unset($this->hiddenParameters[$key]);
        }
        return $this;
    }
    
    public function hideQueryParameter($parameter) {
        array_push($this->hiddenParameters, $parameter);
        return $this;
    }
    
    public function isHiddenQueryParameter($parameter) {
        return in_array($parameter, $this->getHiddenParameters());
    }
    
    public function getData() {
        return $this->data;
    }

    public function setData($data) {
        $this->data = $data;
        return $this;
    }
    
    public function getVisiblePages() {
        return $this->visiblePages;
    }

    public function setVisiblePages($visiblePages) {
        $this->visiblePages = (int) $visiblePages;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }

    public function getQueryParams() {
        return $this->queryParams;
    }

    public function setQueryParams($queryParams) {
        $this->queryParams = Convertor::toArrayObject($queryParams);
        return $this;
    }

    public function addQueryParam($name, $value) {
        $this->queryParams[$name] = $value;
    }

    public function getItemsPerPage() {
        return $this->itemsPerPage;
    }

    public function getCurrentPage() {
        return $this->currentPage;
    }

    public function getTotalItems() {
        return $this->totalItems;
    }

    public function setItemsPerPage($itemsPerPage) {
        if(self::MAX_ITEMS_PER_PAGE < (int) $itemsPerPage){
            $itemsPerPage = self::MAX_ITEMS_PER_PAGE;
        }
        $this->itemsPerPage = (int) $itemsPerPage;
    }

    public function setCurrentPage($currentPage) {
        $this->currentPage = (int) $currentPage;
    }

    public function setTotalItems($totalItems) {
        $this->totalItems = (int) $totalItems;
    }

    public function getStartItemIndex() {
        return $this->getItemsPerPage() * ($this->getCurrentPage() - 1) + 1;
    }

    public function getNumberPages() {
        $itemsPerPage = $this->getItemsPerPage();
        if((int) $itemsPerPage <= 0){
            return 0;
        }
        return ceil($this->getTotalItems() / $this->getItemsPerPage());
    }

    public function isCurrentPage($page) {
        return $this->getCurrentPage() == $page;
    }

    public function getPageLink($page) {
        $this->addQueryParam(self::PAGE_PARAM, $page);
        $this->addQueryParam(self::ITEMS_PER_PAGE_PARAM, $this->getItemsPerPage());
        $queryParameters = $this->getQueryParams();
        $hiddenParameters = $this->getHiddenParameters();
        foreach ($hiddenParameters as $parameter) {
            unset($queryParameters[$parameter]);
        }
        $url = $this->getUrl();
        $url .= (strpos($url, '?') === false ? '?' : '&');
        return $url . http_build_query($queryParameters->getArrayCopy());
    }
    
    public function getStartDrawPage() {
        $currentPage = $this->getCurrentPage();
        $visiblePages = $this->getVisiblePages();
        if ($currentPage > $visiblePages) {
            return $currentPage - (int) ($visiblePages / 2);
        }
        return 1;
    }

    abstract public function drawLinks();

    abstract public function getItemHtml($page);

    abstract public function getPreviousHtml($page);

    abstract public function getNextHtml($page);
}
