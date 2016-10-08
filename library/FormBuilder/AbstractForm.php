<?php

namespace Nth\FormBuilder;

use Nth\Html\Node;
use Nth\Form\AbstractForm as AbstractBaseForm;

abstract class AbstractForm extends AbstractBaseForm {

    public function initScripts() {
        $script = new Node('script'
                , sprintf('$("#%s").FormHelper("bindDataSettings");', $this->getAttr('id'))
                , ['type' => 'text/javascript']);
        return $script->toString();
    }

    public function actionScripts($selector) {
        $id = $this->getAttr('id');
        $action = $this->getAttr('action');
        $content = sprintf('$("%s").on("click", function(){'
                . 'if($("#%s").FormHelper("validate")){'
                . '$("#%s").attr("action", "%s").submit();}})'
                , $selector, $id, $id, $action);
        $script = new Node('script', $content, ['type' => 'text/javascript']);
        return $script->toString();
    }

}
