<?php

namespace Nth\Bootstrap\Pagination;

use Nth\Helper\AbstractPagination;
use Nth\Html\Node;

class Pagination extends AbstractPagination {

    const LARGE_SIZE = 'pagination-lg';
    const DEFAULT_SIZE = '';
    const SMALL_SIZE = 'pagination-sm';

    private $size;

    public function getSize() {
        return $this->size;
    }

    public function setSize($size) {
        $this->size = $size;
    }

    private function getSizeClass() {
        return (string) $this->getSize();
    }

    public function drawLinks() {
        $numberPages = $this->getNumberPages();
        if ($numberPages <= 0) {
            return null;
        }
        $currentPage = $this->getCurrentPage();
        $startDrawPage = $this->getStartDrawPage();
        $visiblePages = $this->getVisiblePages();
        $links = $this->getFirstHtml() . $this->getPreviousHtml($currentPage);
        for ($page = $this->getStartDrawPage(); $page <= $numberPages; $page++) {
            if ($page - $startDrawPage + 1 > $visiblePages) {
                break;
            }
            $links .= $this->getItemHtml($page);
        }
        $links .= $this->getNextHtml($currentPage) . $this->getLastHtml();
        $ul = new Node('ul', $links, [ 'class' => 'pagination' . $this->getSizeClass() ]);
        $nav = new Node('nav', null, [ 'class' => 'text-center' ]);
        $nav->appendChild($ul);
        if ($this->getInfoVisibility()) {
            $ul->setAttr('style', 'margin-bottom:5px');
            $content = sprintf('Tổng số có %s dòng trong %s trang', $this->getTotalItems(), $this->getNumberPages());
            $p = new Node('p', $content, ['class' => 'pagination-info']);
            $nav->appendChild($p);
        }
        return $nav->toString();
    }

    public function getItemHtml($page, $label = null) {
        $a = new Node('a', $label ? $label : $page, [
            'href' => 'javascript:;',
        ]);
        $li = new Node('li');
        if ($this->isCurrentPage($page)) {
            $li->setAttr('class', 'active');
        } else {
            $a->setAttr('href', $this->getPageLink($page));
        }
        return $li->appendChild($a)->toString();
    }

    public function getPreviousHtml($page) {
        $a = new Node('a', '&laquo;', [
            'href' => 'javascript:;',
            'aria-label' => 'Previous'
        ]);
        $li = new Node('li');
        if ($page == 1) {
            $li->setAttr('class', 'disabled');
        } else {
            $a->setAttr('href', $this->getPageLink($page - 1));
            $a->setAttr('title', 'Trang kề trước');
        }
        return $li->appendChild($a)->toString();
    }

    public function getNextHtml($page) {
        $a = new Node('a', '&raquo;', [
            'href' => 'javascript:;',
            'aria-label' => 'Next'
        ]);
        $li = new Node('li');
        if ($page == $this->getNumberPages()) {
            $li->setAttr('class', 'disabled');
        } else {
            $a->setAttr('href', $this->getPageLink($page + 1));
            $a->setAttr('title', 'Trang kế tiếp');
        }
        return $li->appendChild($a)->toString();
    }
    
    public function getFirstHtml($label = 'Trang đầu'){
        $a = new Node('a', $label, [ 'href' => 'javascript:;' ]);
        $li = new Node('li');
        if ((int) $this->getCurrentPage() === 1) {
            $li->setAttr('class', 'disabled');
        } else {
            $a->setAttr('href', $this->getPageLink(1));
        }
        return $li->appendChild($a)->toString();
    }
    
    public function getLastHtml($label = 'Trang cuối'){
        $a = new Node('a', $label, [ 'href' => 'javascript:;' ]);
        $li = new Node('li');
        $numberPages = (int) $this->getNumberPages();
        if ((int) $this->getCurrentPage() === $numberPages) {
            $li->setAttr('class', 'disabled');
        } else {
            $a->setAttr('href', $this->getPageLink($numberPages));
        }
        return $li->appendChild($a)->toString();
    }
    
    

}
