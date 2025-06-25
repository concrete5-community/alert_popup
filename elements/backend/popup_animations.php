<?php

use Punic\Unit;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Package\AlertPopup\PopupData $popupData
 */

ob_start();
?>
<div>
    <input type="hidden" name="popupAnimations" v-bind:value="serializedSelectedAnimations" />
    <div class="form-group">
        <?= $form->label('', t('Transition effects')) ?>
        <div v-for="A in ANIMATIONS">
            <?php
            if (version_compare(APP_VERSION, '9') < 0) {
                ?>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" v-bind:value="A.key" v-model="selectedAnimations" />
                        {{ A.name }}
                    </label>
                </div>
                <?php
            } else {
                ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-bind:value="A.key" v-bind:id="`alertpopup-editor-animation-${A.key}`" v-model="selectedAnimations" />
                    <label class="form-check-label" v-bind:for="`alertpopup-editor-animation-${A.key}`">
                        {{ A.name }}
                    </label>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <div class="form-group">
        <?= $form->label('popupAnimationDuration', t('Transition duration')) ?>
        <div class="input-group input-group-sm">
            <?= $form->number(
                'popupAnimationDuration',
                '',
                [
                    'v-model' => 'animationDuration',
                    'min' => '1',
                    'max' => '9999999999',
                    'v-bind:required' => 'selectedAnimations.length !== 0',
                ]
            ) ?>
            <span class="input-group-addon input-group-text" title="<?= Unit::getName('duration/millisecond', 'long') ?>"><?= Unit::getName('duration/millisecond', 'narrow') ?></span>
        </div>
    </div>
</div>
<?php
$template = ob_get_contents();
ob_end_clean();
?>
<script>
(function() {

function ready() {
    Vue.component('alertpopup-popup-animations', {
        template: <?= json_encode($template) ?>,
        data() {
            return <?= json_encode(
                $popupData->toEditAnimations() + [
                    'ANIMATIONS' => [
                        [
                            'key' => 'fade-in',
                            'name' => t('Fade in'),
                        ],
                        [
                            'key' => 'slide-in',
                            'name' => t('Slide in'),
                        ],
                        [
                            'key' => 'zoom-in',
                            'name' => t('Zoom in'),
                        ],
                    ],
                ]
            ) ?>;
        },
        computed: {
            serializedSelectedAnimations() {
                return this.selectedAnimations.join(' ');
            },
        },
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', ready);
} else {
    ready();
}

})();
</script>