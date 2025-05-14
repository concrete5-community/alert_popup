<?php

use Concrete\Core\Page\Page;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Package\AlertPopup\Block\AlertPopup\Controller $controller
 * @var Concrete\Core\Block\View\BlockView $view
 *
 * @var Concrete\Core\Page\Page|null $c
 *
 * @var string $launcherType
 * @var string $launcherText
 * @var string $launcherCssClass
 * @var string $popupID
 * @var string $popupContent
 * @var string $popupCssClass
 * @var string $popupWidth
 * @var int|null $popupMaxWidth
 * @var string $popupHeight
 * @var int|null $popupMaxHeight
 *
 * @var Concrete\Core\Localization\Localization $localization
 * @var string[] $editMessages
 * @var string $launcherInnerHtml
 * @var string $launcherJS
 * @var string $popupHtml
 * 
 */

if ($editMessages !== []) {
    if (!isset($c) || $c->isError()) {
        $c = Page::getCurrentPage();
    }
    if ($c && !$c->isError() && $c->isEditMode()) {
        $localization->withContext(Localization::CONTEXT_UI, static function() use($editMessages) {
            ?>
            <div class="ccm-edit-mode-disabled-item"><?= implode('<br />', array_map('h', $editMessages)) ?></div>
            <?php
        });
    }
}

echo $popupHtml;

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
