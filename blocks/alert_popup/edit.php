<?php

use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\View\View;

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
 * @var string $popupID
 * @var Concrete\Package\AlertPopup\PopupData $popupData
 */

$monoStyle = 'font-family: Menlo, Monaco, Consolas, \'Courier New\', monospace;';
$tabsPrefix = version_compare(APP_VERSION, '9') < 0 ? 'ccm-tab-content-' : '';

View::element('backend/popup_options', [
    'form' => $form,
    'popupData' => $popupData,
], 'alert_popup');

View::element('backend/popup_animations', [
    'form' => $form,
    'popupData' => $popupData,
], 'alert_popup');

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
            <alertpopup-popup-options></alertpopup-popup-options>
        </div>

        <div class="ccm-tab-content tab-pane" role="tabpanel" id="<?= $tabsPrefix ?>alertpopup-editor-animations">
            <alertpopup-popup-animations></alertpopup-popup-animations>
        </div>

        <div class="ccm-tab-content tab-pane" role="tabpanel" id="<?= $tabsPrefix ?>alertpopup-editor-content">
            <?= $editor->outputBlockEditModeEditor('popupContent', LinkAbstractor::translateFromEditMode($popupData->getContent())) ?>
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
                'launcherType' => $launcherType,
                'launcherContentType' => $launcherText === '' ? 'image' : 'text',
                'launcherText' => $launcherText,
                'launcherCssClass' => $launcherCssClass,
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
