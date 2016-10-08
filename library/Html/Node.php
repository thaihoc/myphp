<?php

namespace Nth\Html;

use Exception;
use Nth\Html\AttributeHelper;
use Nth\Html\AbstractAttributes;

class Node extends AbstractAttributes {

    private $name;
    private $content;
    private $childs;

    public function __construct($name, $content = null, array $attrs = [], $childs = []) {
        $this->name = $name;
        $this->content = $content;
        $this->childs = $childs;
        parent::__construct($attrs);
    }

    public function getName() {
        return $this->name;
    }

    public function getContent() {
        return $this->content;
    }

    public function setName($name) {
        $this->name = $name;
        return $this;
    }

    public function setContent($content) {
        $this->content = $content;
        return $this;
    }
    
    public function appendContent($content) {
        $this->content .= $content;
        return $this;
    }
    
    public function prependContent($content) {
        $this->content = $content . $this->content;
        return $this;
    }

    public function getChilds() {
        return $this->childs;
    }

    public function setChilds(array $childs) {
        $this->childs = $childs;
        return $this;
    }
    
    public function addClass($cls) {
        $class = explode(' ', $this->getAttr('class'));
        $arr = array_filter(explode(' ', $cls));
        if (count($arr)) {
            foreach ($arr as $cls) {
                $trimClass = trim($cls);
                if ($trimClass && !in_array($trimClass, $class)) {
                    array_push($class, $trimClass);
                }
            }
            $this->setAttr('class', implode(' ', $class));
        }
        return $this;
    }
    
    public function css($attr, $value) {
        if (false === $value) {
            return $this->removeCss($attr);
        }
        $style = $this->getArrayStyleAttribute();
        $style[$attr] = $value;
        $this->setAttr('style', $style);
        return $this;
    }
    
    public function removeCss($attr) {
        $style = $this->getArrayStyleAttribute();
        unset($style[$attr]);
        $this->setAttr('style', $style);
        return $this;
    }
    
    private function getArrayStyleAttribute() {
        $style = $this->getAttr('style');
        if (!is_array($style)) {
            $arr = array_filter(explode(';', $style));
            $style = [];
            foreach ($arr as $item) {
                $tmp = explode(':', $item);
                if (count($tmp) === 2) {
                    $style[$tmp[0]] = $tmp[1];
                }
            }
        }
        return $style;
    }

    public function appendChild() {
        $arguments = func_get_args();
        for ($i = 0; $i < count($arguments); $i++) {
            if (!$arguments[$i] instanceof Node) {
                throw new Exception('The every argument passed must be an instance of Node');
            }
            array_push($this->childs, $arguments[$i]);
        }
        return $this;
    }

    public function prependChild() {
        $arguments = func_get_args();
        for ($i = 0; $i < count($arguments); $i++) {
            if (!$arguments[$i] instanceof Node) {
                throw Exception('The every argument passed must be an instance of Node');
            }
            array_unshift($this->childs, $arguments[$i]);
        }
        return $this;
    }

    public function toString() {
        $attributeHelper = new AttributeHelper($this->attrs);
        $content = $this->getContent();
        if (false === $content) {
            return sprintf('<%s %s/>', $this->getName(), $attributeHelper->toString());
        }
        if ($content instanceof Node) {
            $content = $content->toString();
        }
        $childs = $this->getChilds();
        if (!empty($childs)) {
            foreach ($childs as $child) {
                $content .= $child->toString();
            }
        }
        return sprintf('<%s %s>%s</%s>', $this->getName()
                , $attributeHelper->toString(), $content, $this->getName());
    }
    
    public function openTag() {
        $attributeHelper = new AttributeHelper($this->attrs);
        return sprintf('<%s %s>', $this->getName(), $attributeHelper->toString());
    }
    
    public function closeTag() {
        return sprintf('</%s>', $this->getName());
    }

}
