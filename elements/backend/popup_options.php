<?php

use Punic\Unit;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Package\AlertPopup\PopupData $popupData
 */

$monoStyle = 'font-family: Menlo, Monaco, Consolas, \'Courier New\', monospace;';
list($inpupGroupButtonsOpen, $inpupGroupButtonsClose) = version_compare(APP_VERSION, '9') < 0 ? ['<span class="input-group-btn">', '</span>'] : ['', ''];
$inputColorStyle = 'padding: 0;';
$inputRangeStyle = version_compare(APP_VERSION, '9') < 0 ? 'padding: 0;' : '';

ob_start();
?>
<div>
    <div class="row">
        <div class="col-6 col-sm-6">
            <div class="form-group">
                <?= $form->label('popupWidthValue', t('Width')) ?>
                <div class="input-group input-group-sm">
                    <?= $form->number(
                        'popupWidthValue',
                        '',
                        [
                            'v-model' => 'widthValue',
                            'min' => '1',
                            'v-bind:max' => "widthUnit === 'vw' ? '100' : '9999999999'",
                            'required' => 'required',
                        ]
                    ) ?>
                    <?= $inpupGroupButtonsOpen ?>
                        <button
                            type="button"
                            class="btn"
                            v-bind:class="widthUnit === 'px' ? 'btn-primary' : 'btn-default'"
                            v-on:click.prevent="widthUnit = 'px'"
                            title="<?= Unit::getName('graphics/pixel', 'long') ?>"
                        ><?= Unit::getName('graphics/pixel', 'narrow') ?></button>
                        <button
                            type="button"
                            class="btn"
                            v-bind:class="widthUnit === 'vw' ? 'btn-primary' : 'btn-default'"
                            v-on:click.prevent="widthUnit = 'vw'"
                            title="<?= t('Percentage of window width') ?>"
                        >%</button>
                    <?= $inpupGroupButtonsClose ?>
                </div>
            </div>
            <input type="hidden" name="popupWidth" v-bind:value="popupWidth" />
        </div>
        <div class="col-6 col-sm-6">
            <div class="form-group">
                <?= $form->label('popupHeightValue', t('Height')) ?>
                <div class="input-group input-group-sm">
                    <?= $form->number(
                        'popupHeightValue',
                        '',
                        [
                            'v-model' => 'heightValue',
                            'min' => '1',
                            'v-bind:max' => "heightUnit === 'vh' ? '100' : '9999999999'",
                            'placeholder' => tc('height', 'Empty - automatic'),
                        ]
                    ) ?>
                    <?= $inpupGroupButtonsOpen ?>
                        <button
                            type="button"
                            class="btn"
                            v-bind:class="heightUnit === 'px' ? 'btn-primary' : 'btn-default'"
                            v-on:click.prevent="heightUnit = 'px'"
                            title="<?= Unit::getName('graphics/pixel', 'long') ?>"
                        ><?= Unit::getName('graphics/pixel', 'narrow') ?></button>
                        <button
                            type="button"
                            class="btn"
                            v-bind:class="heightUnit === 'vh' ? 'btn-primary' : 'btn-default'"
                            v-on:click.prevent="heightUnit = 'vh'"
                            title="<?= t('Percentage of window height') ?>"
                        >%</button>
                    <?= $inpupGroupButtonsClose ?>
                </div>
            </div>
            <input type="hidden" name="popupHeight" v-bind:value="popupHeight" />
        </div>
    </div>
    <div class="row">
        <div class="col-6 col-sm-6">
            <div class="form-group" v-if="widthUnit === 'vw'">
                <?= $form->label('', t('Minimum width')) ?>
                <div class="input-group input-group-sm">
                    <?= $form->number(
                        'popupMinWidth',
                        '',
                        [
                            'v-model' => 'minWidth',
                            'min' => '1',
                            'max' => '9999999999',
                            'placeholder' => tc('width', 'Empty - none'),
                        ]
                    ) ?>
                    <?= $inpupGroupButtonsOpen ?>
                        <button
                            type="button"
                            class="btn btn-primary"
                            title="<?= Unit::getName('graphics/pixel', 'long') ?>"
                        ><?= Unit::getName('graphics/pixel', 'narrow') ?></button>
                        <button
                            type="button"
                            class="btn btn-default"
                            style="visibility: hidden"
                        >%</button>
                    <?= $inpupGroupButtonsClose ?>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-6">
            <div class="form-group" v-if="!/^\d+px$/.test(popupHeight)">
                <?= $form->label('popupMinHeight', t('Minimum height')) ?>
                <div class="input-group input-group-sm">
                    <?= $form->number(
                        'popupMinHeight',
                        '',
                        [
                            'v-model' => 'minHeight',
                            'min' => '1',
                            'max' => '9999999999',
                            'placeholder' => tc('height', 'Empty - none'),
                        ]
                    ) ?>
                    <?= $inpupGroupButtonsOpen ?>
                        <button
                            type="button"
                            class="btn btn-primary"
                            title="<?= Unit::getName('graphics/pixel', 'long') ?>"
                        ><?= Unit::getName('graphics/pixel', 'narrow') ?></button>
                        <button
                            type="button"
                            class="btn btn-default"
                            style="visibility: hidden"
                        >%</button>
                    <?= $inpupGroupButtonsClose ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6 col-sm-6">
            <div class="form-group" v-if="widthUnit === 'vw'">
                <?= $form->label('popupMaxWidth', t('Maximum width')) ?>
                <div class="input-group input-group-sm">
                    <?= $form->number(
                        'popupMaxWidth',
                        '',
                        [
                            'v-model' => 'maxWidth',
                            'min' => '1',
                            'max' => '9999999999',
                            'placeholder' => tc('width', 'Empty - none'),
                        ]
                    ) ?>
                    <?= $inpupGroupButtonsOpen ?>
                        <button
                            type="button"
                            class="btn btn-primary"
                            title="<?= Unit::getName('graphics/pixel', 'long') ?>"
                        ><?= Unit::getName('graphics/pixel', 'narrow') ?></button>
                        <button
                            type="button"
                            class="btn btn-default"
                            style="visibility: hidden"
                        >%</button>
                    <?= $inpupGroupButtonsClose ?>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-6">
            <div class="form-group" v-if="!/^\d+px$/.test(popupHeight)">
                <?= $form->label('popupMaxHeight', t('Maximum height')) ?>
                <div class="input-group input-group-sm">
                    <?= $form->number(
                        'popupMaxHeight',
                        '',
                        [
                            'v-model' => 'maxHeight',
                            'min' => '1',
                            'max' => '9999999999',
                            'placeholder' => tc('height', 'Empty - none'),
                        ]
                    ) ?>
                    <?= $inpupGroupButtonsOpen ?>
                        <button
                            type="button"
                            class="btn btn-primary"
                            title="<?= Unit::getName('graphics/pixel', 'long') ?>"
                        ><?= Unit::getName('graphics/pixel', 'narrow') ?></button>
                        <button
                            type="button"
                            class="btn btn-default"
                            style="visibility: hidden"
                        >%</button>
                    <?= $inpupGroupButtonsClose ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6 col-sm-6">
            <div class="form-group">
                <?= $form->label('popupBorderWidth', t('Border width')) ?>
                <div class="input-group input-group-sm">
                    <?= $form->number(
                        'popupBorderWidth',
                        '',
                        [
                            'v-model' => 'borderWidth',
                            'min' => '0',
                            'max' => '999',
                            'required' => 'required',
                        ]
                    ) ?>
                    <?= $inpupGroupButtonsOpen ?>
                        <button
                            type="button"
                            class="btn btn-primary"
                            title="<?= Unit::getName('graphics/pixel', 'long') ?>"
                        ><?= Unit::getName('graphics/pixel', 'narrow') ?></button>
                        <button
                            type="button"
                            class="btn btn-default"
                            style="visibility: hidden"
                        >%</button>
                    <?= $inpupGroupButtonsClose ?>
                </div>
            </div>
        </div>
        <div class="col-6 col-sm-6">
            <div class="form-group" v-if="borderWidth && borderWidth !== '0'">
                <?= $form->label('popupBorderColor', t('Border color')) ?>
                <?= $form->color(
                    'popupBorderColor',
                    '',
                    [
                        'v-model' => 'borderColor',
                        'required' => 'required',
                        'style' => $inputColorStyle,
                    ]
                ) ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6 col-sm-6">
            <?= $form->label('popupBackgroundColor', t('Background color')) ?>
            <?= $form->color(
                'popupBackgroundColor',
                '',
                [
                    'v-model' => 'backgroundColor',
                    'required' => 'required',
                    'style' => $inputColorStyle,
                ]
            ) ?>
        </div>
        <div class="col-6 col-sm-6">
            <div class="form-group form-group-sm">
                <?= $form->label('popupCssClass', t('CSS classes')) ?>
                <?= $form->text(
                    'popupCssClass',
                    '',
                    [
                        'v-model.trim' => 'cssClass',
                        'maxlength' => '255',
                        'pattern' => '\s*-?[_a-zA-Z]+[_a-zA-Z0-9\-]*(\s+-?[_a-zA-Z]+[_a-zA-Z0-9\-]*)*\s*',
                        'style' => $monoStyle,
                        'class' => 'form-control-sm',
                    ]
                ) ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-6 col-sm-6">
            <?= $form->label('popupBackdropColorRGB', t('Backdrop color')) ?>
            <?= $form->color(
                'popupBackdropColorRGB',
                '',
                [
                    'v-model' => 'backdropColorRGB',
                    'required' => 'required',
                    'style' => $inputColorStyle,
                ]
            ) ?>
        </div>
        <div class="col-6 col-sm-6">
            <?= $form->label('popupBackdropColorAlpha', t('Backdrop opacity') . ' ({{ backdropColorAlpha }}%)</span>') ?>
            <?= $form->range(
                'popupBackdropColorAlpha',
                '',
                [
                    'v-model' => 'backdropColorAlpha',
                    'min' => '0',
                    'max' => '100',
                    'step' => '1',
                    'required' => 'required',
                    'style' => $inputRangeStyle,
                ]
            ) ?>
        </div>
        <input type="hidden" name="popupBackdropColor" v-bind:value="popupBackdropColor" />
    </div>
</div>

<?php

$template = ob_get_contents();
ob_end_clean();
?>
<script>
(function() {

function ready() {
    Vue.component('alertpopup-popup-options', {
        template: <?= json_encode($template) ?>,
        data() {
            return <?= json_encode($popupData->toEditOptions()) ?>;
        },
        computed: {
            popupWidth() {
                return this.widthValue ? `${this.widthValue}${this.widthUnit}` : '';
            },
            popupHeight() {
                return this.heightValue ? `${this.heightValue}${this.heightUnit}` : '';
            },
            popupBackdropColor() {
                const alpha = Number(this.backdropColorAlpha);
                if (!/^#[0-9a-f]{6}$/i.test(this.backdropColorRGB) || isNaN(alpha) || alpha < 0 || alpha > 100) {
                    return '';
                }
                return this.backdropColorRGB + ('00' + Math.round(255 * alpha / 100).toString(16)).slice(-2);
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