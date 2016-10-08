<?php

namespace Nth\FormBuilder\Element;

use Nth\Html\Node;
use Nth\Helper\Icon;
use Nth\Helper\Convertor;
use Nth\File\File as LocalFile;
use Nth\File\Upload\ResultSet;
use Nth\File\Upload\Uploader;
use Nth\FormBuilder\Element\AbstractElement;
use Nth\FormBuilder\Element\ElementInterface;

class File extends AbstractElement implements ElementInterface {
    
    private $uploadDirectory;
    
    public function __construct($name, $attrs = [], $options = [], $order = 0, $groups = []) {
        parent::__construct(Element::FILE, $name, $attrs, $options, $order, $groups);
    }
    
    public function getUploadDirectory() {
        return $this->uploadDirectory;
    }

    public function setUploadDirectory($uploadDirectory) {
        $this->uploadDirectory = $uploadDirectory;
    }

    public function getControlNodeName() {
        return 'input';
    }

    public function getValue() {
        return $this->getOption('value');
    }

    public function setValue($value) {
        $this->setOption('value', $value);
        return $this;
    }
    
    public function toString() {
        $this->setAttr('type', 'file');
        if ($this->getAttr('multiple')) {
            $this->setAttr('name', $this->getAttr('name') . '[]');
        }
        $components = $this->getComponents();
        $value = $this->getArrayValue();
        $ul = $this->createFileList($value);
        $div = $this->createHiddenArea();
        $wrapper = $components->getWrapper()->getNode();
        $wrapper->appendChild($ul)->appendChild($div);
        $control = $components->getControl()->getNode();
        $control->setContent(false);
        return parent::toString();
    }
    
    private function getFileListOptions() {
        $options = Convertor::toArrayObject($this->getOption('fileList'));
        if (is_null($options->show)) {
            $options->show = true;
        }
        if (is_null($options->removable)) {
            $options->removable = true;
        }
        if (!is_array($options->aAttributes)) {
            $options->aAttributes = [];
        }
        return $options;
    }
    
    private function createFileList($value) {
        $options = $this->getFileListOptions();
        if (empty($value) || !$options->show) {
            $div = new Node('div', null, ['class' => 'empty-file-list']);
            if ($options->emptyText && $options->show) {
                $div->setContent($options->emptyText);
            }
            return $div;
        }
        $ul = new Node('ul', null, ['class' => 'fuploaded']);
        foreach ($value as $path) {
            $file = new LocalFile($this->uploadDirectory . $path);
            $li = new Node('li');
            if ($options->removable) {
                $label = new Node('label', '<i class="fa fa-trash-o"></i>', ['title' => 'Xóa tệp tin này']);
                $li->appendChild($label);
            }
            $a = new Node('a', $file->getBasename(), array_merge([
                'title' => $file->getBasename(),
                'data-file-path' => $this->uploadDirectory . $path,
                'href' => $file->getDownloadLink()
            ], (array) $options->aAttributes));
            $a->prependContent('&nbsp;');
            $a->prependContent(Icon::getImageHtml($path));
            $ul->appendChild($li->appendChild($a));
        }
        return $ul;
    }

    private function createHiddenArea() {
        $div = new Node('div', null, ['area-hidden' => 'true']);
        if (!$this->getValue()) {
            return $div;
        }
        $id = $this->getAttr('id');
        $name = rtrim($this->getAttr('name'), '[]');
        $div->appendChild(new Node('input', false, [
            'type' => 'hidden',
            'id' => Uploader::REMOVING_FILES_CONTROL_NAME_PREFIX . $id,
            'name' => Uploader::REMOVING_FILES_CONTROL_NAME_PREFIX . $name,
        ]))->appendChild(new Node('input', false, [
            'type' => 'hidden',
            'id' => Uploader::REMAINING_FILES_CONTROL_NAME_PREFIX . $id,
            'name' => Uploader::REMAINING_FILES_CONTROL_NAME_PREFIX . $name,
            'value' => $this->getValue()
        ]));
        return $div;
    }

    public function getArrayValue() {
        $value = $this->getValue();
        if (empty($value)) {
            return [];
        }
        return explode(ResultSet::DELIMETER, $value);
    }

}
