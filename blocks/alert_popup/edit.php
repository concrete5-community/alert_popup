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
 * @var string $popupHeight
 * @var int|null $popupMaxWidth
 * @var int|null $popupMaxHeight
 * @var int $popupBorderWidth
 * @var string $popupBorderColor
 * @var string $popupBackgroundColor
 * @var string $popupAnimations
 * @var string $popupCssClass
 * @var string $popupID
 * @var string $popupContent
 */

$monoStyle = 'font-family: Menlo, Monaco, Consolas, \'Courier New\', monospace;';
$tabsPrefix = version_compare(APP_VERSION, '9') < 0 ? 'ccm-tab-content-' : '';
$twoInputGroupsStyle = version_compare(APP_VERSION, '9') < 0 ? ['style' => 'width: 50%'] : [];

$matches = null;

$matched = preg_match('/^(\d+)(\D+)$/', $popupWidth, $matches);
$popupWidthValue = $matched ? (int) $matches[1] : '';
$popupWidthUnit = $matched ? $matches[2] : 'vw';

$matched = preg_match('/^(\d+)(\D+)$/', $popupHeight, $matches);
$popupHeightValue = $matched ? (int) $matches[1] : '';
$popupHeightUnit = $matched ? $matches[2] : 'vh';

ob_start();
?>
<div id="ccm-alertpopup-editor" v-cloak>

    <?= $ui->tabs([
        ['alertpopup-editor-launcher', t('Launcher'), true],
        ['alertpopup-editor-popup', t('Popup')],
        ['alertpopup-editor-animations', t('Animations')],
        ['alertpopup-editor-content', t('Content')],
        // ['alertpopup-editor-preview', t('Preview')],
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
                    <?= $form->text('launcherText', '', ['v-model.trim' => 'launcherText', 'maxlength' => '255', 'required' => 'required']) ?>
                </div>
                <div v-show="launcherContentType === 'image'" class="form-group">
                    <?= $al->image('ccm-alertpopup-editor-image-file', 'launcherImage', t('Choose Image'), $launcherImage) ?>
                </div>
                <div class="form-group">
                    <?= $form->label('launcherCssClass', t('CSS classes of the launcher')) ?>
                    <?= $form->text('launcherCssClass', '', ['v-model.trim' => 'launcherCssClass', 'maxlength' => '255', 'pattern' => '\s*-?[_a-zA-Z]+[_a-zA-Z0-9\-]*(\s+-?[_a-zA-Z]+[_a-zA-Z0-9\-]*)*\s*', 'style' => $monoStyle]) ?>
                </div>
            </div>
        </div>

        <div class="ccm-tab-content tab-pane" role="tabpanel" id="<?= $tabsPrefix ?>alertpopup-editor-popup">
            <input type="hidden" name="popupWidth" v-bind:value="popupWidth" />
            <input type="hidden" name="popupHeight" v-bind:value="popupHeight" />
            <div class="row">
                <div class="col-6 col-sm-6">
                    <div class="form-group">
                        <?= $form->label('popupWidthValue', t('Width')) ?>
                        <div class="input-group">
                            <?= $form->number(
                                'popupWidthValue',
                                '',
                                ['v-model' => 'popupWidthValue', 'min' => '1', 'v-bind:max' => "popupWidthUnit === 'vw' ? '100' : '9999999999'", 'required' => 'required'] + $twoInputGroupsStyle
                            ) ?>
                            <?= $form->select(
                                'popupWidthUnit', 
                                ['px' => Unit::getName('graphics/pixel', 'short'), 'vw' => t('% of the window width')],
                                '',
                                ['v-model' => 'popupWidthUnit', 'required' => 'required'] + $twoInputGroupsStyle
                            ) ?>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-6">
                    <div class="form-group">
                        <?= $form->label('popupHeightValue', t('Height')) ?>
                        <div class="input-group">
                            <?= $form->number(
                                'popupHeightValue',
                                '',
                                ['v-model' => 'popupHeightValue', 'min' => '1', 'v-bind:max' => "popupHeightUnit === 'vh' ? '100' : '9999999999'", 'placeholder' => tc('height', 'Empty - automatic')] + $twoInputGroupsStyle
                            ) ?>
                            <?= $form->select(
                                'popupHeightUnit', 
                                ['px' => Unit::getName('graphics/pixel', 'short'), 'vh' => t('% of the window height')],
                                '',
                                ['v-model' => 'popupHeightUnit', 'required' => 'required'] + $twoInputGroupsStyle
                            ) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-sm-6">
                    <div class="form-group">
                        <?= $form->label('popupMaxWidth', t('Maximum Width')) ?>
                        <div class="input-group">
                            <?= $form->number(
                                'popupMaxWidth',
                                '',
                                ['v-model' => 'popupMaxWidth', 'min' => '1', 'max' => '9999999999', 'placeholder' => tc('width', 'Empty - none')]
                            ) ?>
                            <span class="input-group-addon"><?= Unit::getName('graphics/pixel', 'short') ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-6">
                    <div class="form-group">
                        <?= $form->label('popupMaxHeight', t('Maximum Height')) ?>
                        <div class="input-group">
                            <?= $form->number(
                                'popupMaxHeight',
                                '',
                                ['v-model' => 'popupMaxHeight', 'min' => '1', 'max' => '9999999999', 'placeholder' => tc('height', 'Empty - none')]
                            ) ?>
                            <span class="input-group-addon"><?= Unit::getName('graphics/pixel', 'short') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-sm-6">
                    <div class="form-group">
                        <?= $form->label('popupBorderWidth', t('Border Width')) ?>
                        <div class="input-group">
                            <?= $form->number(
                                'popupBorderWidth',
                                '',
                                ['v-model' => 'popupBorderWidth', 'min' => '0', 'max' => '999', 'required' => 'required']
                            ) ?>
                            <span class="input-group-addon"><?= Unit::getName('graphics/pixel', 'short') ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-sm-6" v-if="popupBorderWidth">
                    <?= $form->label('popupBorderColor', t('Border Color')) ?>
                    <?= $form->color(
                        'popupBorderColor',
                        '',
                        ['v-model' => 'popupBorderColor', 'required' => 'required']
                    ) ?>
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-sm-6">
                    <?= $form->label('popupBackgroundColor', t('Background Color')) ?>
                    <?= $form->color(
                        'popupBackgroundColor',
                        '',
                        ['v-model' => 'popupBackgroundColor', 'required' => 'required']
                    ) ?>
                </div>
                <div class="col-6 col-sm-6">
                    <div class="form-group">
                        <?= $form->label('popupCssClass', t('CSS classes')) ?>
                        <?= $form->text('popupCssClass', '', ['v-model.trim' => 'popupCssClass', 'maxlength' => '255', 'pattern' => '\s*-?[_a-zA-Z]+[_a-zA-Z0-9\-]*(\s+-?[_a-zA-Z]+[_a-zA-Z0-9\-]*)*\s*', 'style' => $monoStyle]) ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <?= $form->label('popupID', t('ID of the popup')) ?>
                <?= $form->text('popupID', '', ['v-model.trim' => 'popupID', 'maxlength' => '255', 'v-bind:required' => h('launcherType === ' . json_encode($controller::LAUNCHERTYPE_NONE)), 'pattern' => '[A-Za-z_][A-Za-z0-9_\-]*']) ?>
                <div class="small text-muted">
                    <?= t("Required if the type of the launcher is set to '%s'", tc('Launcher', 'None')) ?>
                </div>
                <div class="small text-muted" v-bind:style="{visibility: popupID === '' ? 'hidden' : 'visible'}">
                    <?= t('Example:') ?><br />
                    <code>&lt;a href=&quot;#&quot; onclick=&quot;ccmAlertPopup.show('{{ popupID }}'); return false&quot;&gt;<?= t('Show Popup') ?>&lt;/a&gt;</code>
                </div>
            </div>
        </div>

        <div class="ccm-tab-content tab-pane" role="tabpanel" id="<?= $tabsPrefix ?>alertpopup-editor-animations">
            <input type="hidden" name="popupAnimations" v-bind:value="popupAnimations" />
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
                        'name' => t('Fade In'),
                    ],
                    [
                        'key' => 'slide-in',
                        'name' => t('Slide In'),
                    ],
                    [
                        'key' => 'zoom-in',
                        'name' => t('Zoom In'),
                    ],
                ],
                'launcherType' => $launcherType,
                'launcherContentType' => $launcherText === '' ? 'image' : 'text',
                'launcherText' => $launcherText,
                'launcherCssClass' => $launcherCssClass,
                'popupWidthValue' => $popupWidthValue,
                'popupWidthUnit' => $popupWidthUnit,
                'popupHeightValue' => $popupHeightValue,
                'popupHeightUnit' => $popupHeightUnit,
                'popupMaxWidth' => $popupMaxWidth,
                'popupMaxHeight' => $popupMaxHeight,
                'popupBorderWidth' => $popupBorderWidth,
                'popupBorderColor' => $popupBorderColor,
                'popupBackgroundColor' => $popupBackgroundColor,
                'selectedAnimations' => preg_split('/[^\w\-]/', $popupAnimations, -1, PREG_SPLIT_NO_EMPTY),
                'popupCssClass' => $popupCssClass,
                'popupID' => $popupID,
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
            }
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
