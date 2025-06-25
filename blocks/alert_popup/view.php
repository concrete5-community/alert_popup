<?php

use Concrete\Core\Page\Page;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Package\AlertPopup\Block\AlertPopup\Controller $controller
 * @var Concrete\Core\Block\View\BlockView $view
 * @var Concrete\Core\Page\Page|null $c
 *
 * @var Concrete\Package\AlertPopup\PopupData $popupData
 * @var Concrete\Package\AlertPopup\Service $service
 * @var string $launcherType
 * @var string $launcherText
 * @var string $launcherCssClass
 * @var string $popupID
 *
 * @var string[] $editMessages
 * @var string $launcherInnerHtml
 * @var string $launcherJS
 */

if ($editMessages !== []) {
    if (!isset($c) || $c->isError()) {
        $c = Page::getCurrentPage();
    }
    if ($c && !$c->isError() && $c->isEditMode()) {
        ?>
        <div class="ccm-edit-mode-disabled-item"><?= implode('<br />', array_map('h', $editMessages)) ?></div>
        <?php
    }
}

echo $service->generatePopupHtml($popupData, $popupID);

if ($launcherInnerHtml !== '') {
    switch ($launcherType) {
        case $controller::LAUNCHERTYPE_BUTTON:
            ?>
            <button onclick="<?= h($launcherJS) ?>"<?= $launcherCssClass === '' ? '' : " class=\"{$launcherCssClass}\"" ?>><?= $launcherInnerHtml ?></button>
            <?php
            break;
        case $controller::LAUNCHERTYPE_LINK:
            ?>
            <a href="#" onclick="<?= h($launcherJS) ?>"<?= $launcherCssClass === '' ? '' : " class=\"{$launcherCssClass}\"" ?>><?= $launcherInnerHtml ?></a>
            <?php
            break;
    }
}
