<?php

namespace Concrete\Package\AlertPopup\Controller\Element\Backend;

use Concrete\Core\Controller\ElementController;

defined('C5_EXECUTE') or die('Access Denied.');

class PopupOptions extends ElementController
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\ElementController::$pkgHandle
     */
    protected $pkgHandle = 'alert_popup';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Controller\ElementController::getElement()
     */
    public function getElement()
    {
        return 'backend/popup_options';
    }

    public function view()
    {
        $this->set('ciao', 'ooooooooo');
    }
}
