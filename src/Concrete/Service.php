<?php

namespace Concrete\Package\AlertPopup;

use Concrete\Core\Editor\LinkAbstractor;

defined('C5_EXECUTE') or die('Access Denied.');

class Service
{
    /**
     * @param string $popupID
     *
     * @return string
     */
    public function generatePopupHtml(PopupData $popupData, $popupID)
    {
        $cssClasses = [
            'ccm-alert-popup',
        ];
        $cssStyles = [
            'width: ' . h($popupData->getWidth()),
        ];
        $attributes = [];
        $ontentCSSStyles = [];
        if (($animations = $popupData->getAnimations()) !== []) {
            foreach ($animations as $animation) {
                $cssClasses[] = "ccm-alert-popup-anim-{$animation}";
            }
            if (($animationDuration = $popupData->getAnimationDuration()) > 0) {
                $cssStyles[] = "transition-duration: {$animationDuration}ms";
            }
        }
        if (($backdropColor = $popupData->getBackdropColor()) !== '') {
            $attributes[] = 'data-backdrop-color="' . h($backdropColor) . '"';
        }
        if (($cssClass = $popupData->getCssClass()) !== '') {
            $cssClasses[] = h($cssClass);
        }
        if (($borderWidth = $popupData->getBorderWidth()) > 0) {
            $borderColor = h($popupData->getBorderColor());
            $cssStyles[] = "border: solid {$borderWidth}px {$borderColor}";
        }
        if (($backgroundColor = $popupData->getBackgroundColor()) !== '') {
            $cssStyles[] = 'background-color: ' . h($backgroundColor);
        }
        if (($minWidth = $popupData->getMinWidth()) !== null) {
            $cssStyles[] = "min-width: {$minWidth}px";
        }
        if (($maxWidth = (int) $popupData->getMaxWidth()) !== null && $maxWidth > 0) {
            $cssStyles[] = "max-width: {$maxWidth}px";
        }
        if (($height = $popupData->getHeight()) !== '') {
            $ontentCSSStyles[] = 'height: calc(' . h($height) . ' - 19px)';
        }
        if (($minHeight = $popupData->getMinHeight()) !== null) {
            $ontentCSSStyles[] = "min-height: calc({$minHeight}px - 19px)";
        }
        if (($maxHeight = (int) $popupData->getMaxHeight()) !== null && $maxHeight > 0) {
            $ontentCSSStyles[] = "max-height: calc({$maxHeight}px - 19px)";
        }
        if (!$height && !$maxHeight) {
            $ontentCSSStyles[] = 'max-height: calc(100vh - 80px)';
        }
        $result = '<dialog id="' . h($popupID) . '" class="' . implode(' ', $cssClasses) . '" style="' . implode('; ', $cssStyles) . '"';
        if ($attributes !== []) {
            $result .= ' ' . implode(' ', $attributes);
        }
        $result .= '><div class="ccm-alert-popup-content" style="' . implode('; ', $ontentCSSStyles) . '">' . LinkAbstractor::translateFrom($popupData->getContent()) . '</div></dialog>';

        return $result;
    }
}
