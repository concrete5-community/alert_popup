<?php

use Punic\Unit;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Block\View\BlockView $view
 * @var Concrete\Package\AlertPopup\Block\AlertPopup\Controller $controller
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Application\Service\UserInterface $ui
 * @var Concrete\Core\Application\Service\FileManager $al
 * @var Concrete\Core\Editor\EditorInterface $editor
 *
 * @var string $launcherType
 * @var string $launcherText
 * @var int|null $launcherImage
 * @var string $launcherCssClass
 * @var string $popupWidth
 * @var int|null $popupMinWidth
 * @var int|null $popupMaxWidth
 * @var string $popupHeight
 * @var int|null $popupMinHeight
 * @var int|null $popupMaxHeight
 * @var int $popupBorderWidth
 * @var string $popupBorderColor
 * @var string $popupBackgroundColor
 * @var string $popupBackdropColor
 * @var string $popupAnimations
 * @var int $popupAnimationDuration
 * @var string $popupCssClass
 * @var string $popupID
 * @var string $popupContent
 */

$monoStyle = 'font-family: Menlo, Monaco, Consolas, \'Courier New\', monospace;';
$tabsPrefix = version_compare(APP_VERSION, '9') < 0 ? 'ccm-tab-content-' : '';
$inputRangeStyle = version_compare(APP_VERSION, '9') < 0 ? 'padding: 0;' : '';
$inputColorStyle = 'padding: 0;';
$matches = null;

$matched = preg_match('/^(\d+)(\D+)$/', $popupWidth, $matches);
$popupWidthValue = $matched ? (int) $matches[1] : '';
$popupWidthUnit = $matched ? $matches[2] : 'vw';

$matched = preg_match('/^(\d+)(\D+)$/', $popupHeight, $matches);
$popupHeightValue = $matched ? (int) $matches[1] : '';
$popupHeightUnit = $matched ? $matches[2] : 'vh';
list($inpupGroupButtonsOpen, $inpupGroupButtonsClose) = version_compare(APP_VERSION, '9') < 0 ? ['<span class="input-group-btn">', '</span>'] : ['', ''];

ob_start();
?>
<div id="ccm-alertpopup-editor" v-cloak>

    <?= $ui->tabs([
        ['alertpopup-editor-launcher', t('Launcher'), true],
        ['alertpopup-editor-popup', t('Popup')],
        ['alertpopup-editor-animations', t('Animations')],
        ['alertpopup-editor-content', t('Content')],
    ]) ?>

    <div class="tab-content">

        <div class="ccm-tab-content tab-pane active" role="tabpanel" id="<?= $tabsPrefix ?>alertpopup-editor-launcher">
            <div class="form-group">
                <?= $form->label('launcherType', t('Item to be clicked to open the popup')) ?>
                <?= $form->select(
                    'launcherType',
                    [
                        $controller::LAUNCHERTYPE_BUTTON => t('Button'),
                        $controller::LAUNCHERTYPE_LINK => t('Link'),
                        $controller::LAUNCHERTYPE_NONE => tc('Launcher', 'None'),
                    ],
                    [
                        'v-model' => 'launcherType',
                        'required' => 'required',
                    ]
                ) ?>
            </div>
            <div v-show="<?= h('launcherType !== ' . json_encode($controller::LAUNCHERTYPE_NONE)) ?>">
                <div class="form-group">
                    <?= $form->label('launcherContentType', t('Content of the item to be clicked')) ?>
                    <?= $form->select(
                        'launcherContentType',
                        [
                            'text' => t('Text'),
                            'image' => t('Image'),
                        ],
                        [
                            'v-model' => 'launcherContentType',
                            'required' => 'required',
                        ]
                    ) ?>
                </div>
                <div v-if="launcherContentType === 'text'" class="form-group">
                    <?= $form->label('launcherText', t('Text')) ?>
                    <?= $form->text(
                        'launcherText',
                        '',
                        [
                            'v-model.trim' => 'launcherText',
                            'maxlength' => '255',
                            'required' => 'required',
                        ]
                    ) ?>
                </div>
                <div v-show="launcherContentType === 'image'" class="form-group">
                    <?= $al->image(
                        'ccm-alertpopup-editor-image-file',
                        'launcherImage',
                        t('Choose Image'),
                        $launcherImage
                    ) ?>
                </div>
                <div class="form-group">
                    <?= $form->label('launcherCssClass', t('CSS classes of the launcher')) ?>
                    <?= $form->text(
                        'launcherCssClass',
                        '',
                        [
                            'v-model.trim' => 'launcherCssClass',
                            'maxlength' => '255',
                            'pattern' => '\s*-?[_a-zA-Z]+[_a-zA-Z0-9\-]*(\s+-?[_a-zA-Z]+[_a-zA-Z0-9\-]*)*\s*',
                            'style' => $monoStyle,
                        ]
                    ) ?>
                </div>
            </div>
            <div class="form-group">
                <?= $form->label('popupID', t('ID of the popup')) ?>
                <?= $form->text(
                    'popupID',
                    '',
                    [
                        'v-model.trim' => 'popupID',
                        'maxlength' => '255',
                        'v-bind:required' => h('launcherType === ' . json_encode($controller::LAUNCHERTYPE_NONE)),
                        'pattern' => '[A-Za-z_][A-Za-z0-9_\-]*',
                        'style' => $monoStyle,
                    ]
                ) ?>
                <div class="small text-muted">
                    <?= t("Required if the type of the launcher is set to '%s'", tc('Launcher', 'None')) ?>
                </div>
                <div class="small text-muted" v-bind:style="{visibility: popupID === '' ? 'hidden' : 'visible'}">
                    <?= t('Example:') ?><br />
                    <code>&lt;a href=&quot;#&quot; onclick=&quot;ccmAlertPopup.show('{{ popupID }}'); return false&quot;&gt;<?= t('Show Popup') ?>&lt;/a&gt;</code>
                </div>
            </div>
        </div>

        <div class="ccm-tab-content tab-pane" role="tabpanel" id="<?= $tabsPrefix ?>alertpopup-editor-popup">
            <div class="row">
                <div class="col-6 col-sm-6">
                    <div class="form-group">
                        <?= $form->label('popupWidthValue', t('Width')) ?>
                        <div class="input-group input-group-sm">
                            <?= $form->number(
                                'popupWidthValue',
                                '',
                                [
                                    'v-model' => 'popupWidthValue',
                                    'min' => '1',
                                    'v-bind:max' => "popupWidthUnit === 'vw' ? '100' : '9999999999'",
                                    'required' => 'required',
                                ]
                            ) ?>
                            <?= $inpupGroupButtonsOpen ?>
                                <button
                                    type="button"
                                    class="btn"
                                    v-bind:class="popupWidthUnit === 'px' ? 'btn-primary' : 'btn-default'"
                                    v-on:click.prevent="popupWidthUnit = 'px'"
                                    title="<?= Unit::getName('graphics/pixel', 'long') ?>"
                                ><?= Unit::getName('graphics/pixel', 'narrow') ?></button>
                                <button
                                    type="button"
                                    class="btn"
                                    v-bind:class="popupWidthUnit === 'vw' ? 'btn-primary' : 'btn-default'"
                                    v-on:click.prevent="popupWidthUnit = 'vw'"
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
                                    'v-model' => 'popupHeightValue',
                                    'min' => '1',
                                    'v-bind:max' => "popupHeightUnit === 'vh' ? '100' : '9999999999'",
                                    'placeholder' => tc('height', 'Empty - automatic'),
                                ]
                            ) ?>
                            <?= $inpupGroupButtonsOpen ?>
                                <button
                                    type="button"
                                    class="btn"
                                    v-bind:class="popupHeightUnit === 'px' ? 'btn-primary' : 'btn-default'"
                                    v-on:click.prevent="popupHeightUnit = 'px'"
                                    title="<?= Unit::getName('graphics/pixel', 'long') ?>"
                                ><?= Unit::getName('graphics/pixel', 'narrow') ?></button>
                                <button
                                    type="button"
                                    class="btn"
                                    v-bind:class="popupHeightUnit === 'vh' ? 'btn-primary' : 'btn-default'"
                                    v-on:click.prevent="popupHeightUnit = 'vh'"
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
                    <div class="form-group" v-if="popupWidthUnit === 'vw'">
                        <?= $form->label('popupMinWidth', t('Minimum width')) ?>
                        <div class="input-group input-group-sm">
                            <?= $form->number(
                                'popupMinWidth',
                                '',
                                [
                                    'v-model' => 'popupMinWidth',
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
                                    'v-model' => 'popupMinHeight',
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
                    <div class="form-group" v-if="popupWidthUnit === 'vw'">
                        <?= $form->label('popupMaxWidth', t('Maximum width')) ?>
                        <div class="input-group input-group-sm">
                            <?= $form->number(
                                'popupMaxWidth',
                                '',
                                [
                                    'v-model' => 'popupMaxWidth',
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
                                    'v-model' => 'popupMaxHeight',
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
                                    'v-model' => 'popupBorderWidth',
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
                    <div class="form-group" v-if="popupBorderWidth && popupBorderWidth !== '0'">
                        <?= $form->label('popupBorderColor', t('Border color')) ?>
                        <?= $form->color(
                            'popupBorderColor',
                            '',
                            [
                                'v-model' => 'popupBorderColor',
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
                            'v-model' => 'popupBackgroundColor',
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
                                'v-model.trim' => 'popupCssClass',
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
                            'v-model' => 'popupBackdropColorRGB',
                            'required' => 'required',
                            'style' => $inputColorStyle,
                        ]
                    ) ?>
                </div>
                <div class="col-6 col-sm-6">
                    <?= $form->label('popupBackdropColorAlpha', t('Backdrop opacity') . ' ({{ popupBackdropColorAlpha }})</span>') ?>
                    <?= $form->range(
                        'popupBackdropColorAlpha',
                        '',
                        [
                            'v-model' => 'popupBackdropColorAlpha',
                            'min' => '0',
                            'max' => '100',
                            'step' => '1',
                            'required' => 'required',
                            'style' => $inputRangeStyle,
                        ]
                    ) ?>
                </div>
            </div>
            <input type="hidden" name="popupBackdropColor" v-bind:value="popupBackdropColor" />
        </div>

        <div class="ccm-tab-content tab-pane" role="tabpanel" id="<?= $tabsPrefix ?>alertpopup-editor-animations">
            <input type="hidden" name="popupAnimations" v-bind:value="popupAnimations" />
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
                            'v-model' => 'popupAnimationDuration',
                            'min' => '1',
                            'max' => '9999999999',
                            'v-bind:required' => 'selectedAnimations.length !== 0',
                        ]
                    ) ?>
                    <span class="input-group-addon input-group-text" title="<?= Unit::getName('duration/millisecond', 'long') ?>"><?= Unit::getName('duration/millisecond', 'narrow') ?></span>
                </div>
            </div>
        </div>

        <div class="ccm-tab-content tab-pane" role="tabpanel" id="<?= $tabsPrefix ?>alertpopup-editor-content">
            <?= $editor->outputBlockEditModeEditor('popupContent', $popupContent) ?>
        </div>

    </div>
</div>
<?php
$template = ob_get_contents();
ob_end_clean();
$scripts = [];

$template = preg_replace_callback(
    '#<script\b[^>]*>(.*?)</script>#is',
    static function (array $matches) use (&$scripts) {
        $scripts[] = trim($matches[1]);
        return '';
    },
    $template
);

echo $template;
?>

<script>
$(document).ready(function() {

function launchApp() {
    new Vue({
        el: '#ccm-alertpopup-editor',
        data() {
            return <?= json_encode([
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
                'launcherType' => $launcherType,
                'launcherContentType' => $launcherText === '' ? 'image' : 'text',
                'launcherText' => $launcherText,
                'launcherCssClass' => $launcherCssClass,
                'popupWidthValue' => $popupWidthValue,
                'popupWidthUnit' => $popupWidthUnit,
                'popupMinWidth' => $popupMinWidth,
                'popupMaxWidth' => $popupMaxWidth,
                'popupHeightValue' => $popupHeightValue,
                'popupHeightUnit' => $popupHeightUnit,
                'popupMinHeight' => $popupMinHeight,
                'popupMaxHeight' => $popupMaxHeight,
                'popupBorderWidth' => $popupBorderWidth,
                'popupBorderColor' => $popupBorderColor,
                'popupBackgroundColor' => $popupBackgroundColor,
                'popupBackdropColorRGB' => preg_match('/^#[0-9a-f]{8}$/i', $popupBackdropColor) ? substr($popupBackdropColor, 0, 7) : '#000000',
                'popupBackdropColorAlpha' => preg_match('/^#[0-9a-f]{8}$/i', $popupBackdropColor) ? round(100 * hexdec(substr($popupBackdropColor, 7, 2)) / 255) : 10,
                'selectedAnimations' => preg_split('/[^\w\-]/', $popupAnimations, -1, PREG_SPLIT_NO_EMPTY),
                'popupAnimationDuration' => $popupAnimationDuration > 0 ? $popupAnimationDuration : 600,
                'popupCssClass' => $popupCssClass,
                'popupID' => $popupID,
                'popupPreview' => [
                    'loading' => false,
                    'params' => null,
                    'html' => '',
                ],
            ]) ?>;
        },
        mounted() {
            this.hookInvalidFields();
            var runScripts = function() {
                <?= implode("\n", $scripts) ?>;
            };
            <?php
            if (version_compare(APP_VERSION, '9') < 0) {
                ?>
                var tmr;
                tmr = setInterval(
                    function() {
                        if ($.fn.concreteFileSelector) {
                            clearInterval(tmr);
                            runScripts();
                        }
                    },
                    100
                );
                <?php
            } else {
                ?>
                runScripts();
                <?php
            }
            ?>
            const previewButton = document.createElement('button');
            previewButton.textContent = <?= json_encode(t('Preview')) ?>;
            previewButton.addEventListener('click', async (e) => {
                e.preventDefault();
                previewButton.disabled = true;
                try {
                    await this.showPreview();
                } finally {
                    previewButton.disabled = false;
                }
            });
            <?php
            if (version_compare(APP_VERSION, '9') < 0) {
                ?>
                previewButton.className = 'btn btn-default pull-right';
                previewButton.style.marginRight = '1em';
                setTimeout(
                    () => this.$el.closest('.ui-dialog').querySelector('.ui-dialog-buttonpane .btn-primary').after(previewButton),
                    100
                );
                <?php
            } else {
                ?>
                previewButton.className = 'btn btn-success';
                previewButton.style.marginRight = '1em';
                setTimeout(
                    () => this.$el.closest('.ui-dialog').querySelector('.ui-dialog-buttonpane .btn-primary').before(previewButton),
                    100
                );
                <?php
            }
            ?>
            $(this.$el.closest('.ui-dialog')).on('dialogbeforeclose', () => {
                if (window.ccmAlertPopup.isOpen === true) {
                    window.ccmAlertPopup.hide();
                    previewButton.focus();
                    return false;
                }
            });
        },
        computed: {
            popupWidth() {
                return this.popupWidthValue ? `${this.popupWidthValue}${this.popupWidthUnit}` : '';
            },
            popupHeight() {
                return this.popupHeightValue ? `${this.popupHeightValue}${this.popupHeightUnit}` : '';
            },
            popupAnimations() {
                return this.selectedAnimations.join(' ');
            },
            popupBackdropColor() {
                const alpha = Number(this.popupBackdropColorAlpha);
                if (!/^#[0-9a-f]{6}$/i.test(this.popupBackdropColorRGB) || isNaN(alpha) || alpha < 0 || alpha > 100) {
                    return '';
                }
                return this.popupBackdropColorRGB + ('00' + Math.round(255 * alpha / 100).toString(16)).slice(-2);
            },
        },
        methods: {
            hookInvalidFields() {
                const form = this.$el.closest('form');
                let reporting = false;
                form.addEventListener(
                    'invalid',
                    (e) => {
                        if (reporting) {
                            return;
                        }
                        const field = e.target;
                        if (!field) {
                            return;
                        }
                        const tab = field.closest('.tab-pane');
                        if (!tab) {
                            return;
                        }
                        const id = tab.getAttribute('id').substring(<?= json_encode($tabsPrefix) ?>.length);
                        const link = form.querySelector(`.nav-tabs a[href="#${id}"]`) || form.querySelector(`a[data-tab="${id}"]`);
                        if (!link) {
                            return;
                        }
                        link.click();
                        reporting = true;
                        try {
                            field.reportValidity();
                        } finally {
                            reporting = false;
                        }
                    },
                    true
                );
            },
            preparePopupContent() {
                const input = this.$el.closest('form').querySelector('[name="popupContent"]');
                if (input) {
                    if (window.CKEDITOR?.instances) {
                        const ckEditor = window.CKEDITOR.instances[input.id];
                        if (ckEditor) {
                            ckEditor.updateElement();
                        }
                    }
                }
            },
            getPopupPreviewParent() {
                const ccmPages = document.querySelectorAll('.ccm-page');
                if (ccmPages.length > 0) {
                    return ccmPages[0];
                }
                return document.body;
            },
            async showPreview() {
                if (this.popupPreview.loading) {
                    return;
                }
                this.preparePopupContent();
                const formData = new FormData(this.$el.closest('form'));
                const formParams = new URLSearchParams(formData);
                
                //var editorData = CKEDITOR.instances.editor1.getData();
                if (this.popupPreview.params !== formParams.toString()) {
                    this.popupPreview.loading = true;
                    try {
                        const requestBody = new URLSearchParams(formData);
                        requestBody.append('__ccm_consider_request_as_xhr', '1');
                        requestBody.delete(<?= json_encode($token::DEFAULT_TOKEN_NAME) ?>);
                        requestBody.append(<?= json_encode($token::DEFAULT_TOKEN_NAME) ?>, <?= json_encode($token->generate('ccm-alertpopup-preview')) ?>);
                        const response = await window.fetch(
                            <?= json_encode((string) $controller->getActionURL('generate_preview')) ?>,
                            {
                                headers: {
                                    Accept: 'application/json',
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                method: 'POST',
                                body: requestBody,
                                cache: 'no-store',
                            }
                        );
                        const responseText = await response.text();
                        let responseData;
                        try {
                            responseData = JSON.parse(responseText);
                        } catch (e) {
                            throw new Error(responseText);
                        }
                        if (responseData.error === true && responseData.errors?.length) {
                            throw new Error(responseData.errors.join('\n'));
                        } else if (responseData.error) {
                            throw new Error(responseData.error.message || responseData.error);
                        }
                        if (!response.ok || !responseData.popupHtml) {
                            throw new Error(responseText);
                        }
                        this.popupPreview.html = responseData.popupHtml;
                        this.popupPreview.params = formParams.toString();
                    } catch (e) {
                        ConcreteAlert.error({message: e?.message || e || <?= json_encode(t('Unknown error')) ?>});
                        return;
                    } finally {
                        this.popupPreview.loading = false;
                    }
                }
                const popupContainer = document.createElement('div');
                popupContainer.innerHTML = this.popupPreview.html;
                this.getPopupPreviewParent().appendChild(popupContainer);
                window.ccmAlertPopup.show('ccm-alertpopup-popuppreview', {
                    closed: () => {
                        popupContainer.remove();
                    },
                });
            },
        },
    });
}

if (window.Vue) {
    launchApp();
} else {
    let launchAppTimer;
    launchAppTimer = setInterval(
        function() {
            if (window.Vue) {
                clearInterval(launchAppTimer);
                launchApp();
            }
        },
        100
    );
}

});
</script>
