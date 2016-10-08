<?php

namespace Nth\FormBuilder;

use Nth\Form\FormInterface as BaseFormInterface;

interface FormInterface extends BaseFormInterface {

    public function initScripts();

    public function actionScripts($selector);
}
