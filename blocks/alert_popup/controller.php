<?php

namespace Concrete\Package\AlertPopup\Block\AlertPopup;

use Concrete\Core\Asset\AssetList;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Editor\LinkAbstractor;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\File\File;
use Concrete\Core\File\Tracker\FileTrackableInterface;
use Concrete\Core\Http\ResponseFactoryInterface;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Utility\Service\Xml;

class Controller extends BlockController implements FileTrackableInterface
{
    const LAUNCHERTYPE_NONE = 'none';

    const LAUNCHERTYPE_LINK = 'link';

    const LAUNCHERTYPE_BUTTON = 'button';

    /**
     * @private
     */
    const RX_VALIDATE_ID = '[A-Za-z_][A-Za-z0-9_\-]*';

    /**
     * @private
     */
    const RX_VALIDATE_CSS_CLASSLIST = '-?[_a-zA-Z]+[_a-zA-Z0-9\-]*( -?[_a-zA-Z]+[_a-zA-Z0-9\-]*)*';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$helpers
     */
    protected $helpers = [];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btTable
     */
    protected $btTable = 'btAlertPopup';

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btInterfaceWidth
     */
    protected $btInterfaceWidth = 750;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btInterfaceHeight
     */
    protected $btInterfaceHeight = 650;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btCacheBlockOutput
     */
    protected $btCacheBlockOutput = true;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btCacheBlockOutputOnPost
     */
    protected $btCacheBlockOutputOnPost = true;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$supportSavingNullValues
     */
    protected $supportSavingNullValues = true;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btExportFileColumns
     */
    protected $btExportFileColumns = ['launcherImage'];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::$btExportContentColumns
     */
    protected $btExportContentColumns = ['popupContent'];

    /**
     * Type of the item to be clicked.
     *
     * @var string|null
     */
    protected $launcherType;

    /**
     * Text of the link to be clicked.
     *
     * @var string|null
     */
    protected $launcherText;

    /**
     * ID of the image to be clicked.
     *
     * @var int|string|null
     */
    protected $launcherImage;

    /**
     * CSS classes for the link/image to be clicked.
     *
     * @var string|null
     */
    protected $launcherCssClass;

    /**
     * Width of the popup.
     *
     * @var string|null
     */
    protected $popupWidth;

    /**
     * Min width (in pixels) of the popup.
     *
     * @var int|string|null
     */
    protected $popupMinWidth;

    /**
     * Max width (in pixels) of the popup.
     *
     * @var int|string|null
     */
    protected $popupMaxWidth;


    /**
     * Height of the popup.
     *
     * @var string|null
     */
    protected $popupHeight;


    /**
     * Min height (in pixels) of the popup.
     *
     * @var int|string|null
     */
    protected $popupMinHeight;

    /**
     * Max height (in pixels) of the popup.
     *
     * @var int|string|null
     */
    protected $popupMaxHeight;

    /**
     * Width (in pixels) of the popup.
     *
     * @var int|string|null
     */
    protected $popupBorderWidth;

    /**
     * Color of the border of the popup.
     *
     * @var string|null
     */
    protected $popupBorderColor;

    /**
     * Color of the background of the popup.
     *
     * @var string|null
     */
    protected $popupBackgroundColor;

    /**
     * Color of the backdrop of the popup.
     *
     * @var string|null
     */
    protected $popupBackdropColor;

    /**
     * List of popup animations.
     *
     * @var string|null
     */
    protected $popupAnimations;

    /**
     * CSS classes for the popup.
     *
     * @var string|null
     */
    protected $popupCssClass;

    /**
     * Optional identifier of the popup.
     *
     * @var string|null
     */
    protected $popupID;

    /**
     * Rich Text of the popup.
     * @var string|null
     */
    protected $popupContent;

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getBlockTypeName()
     */
    public function getBlockTypeName()
    {
        return t('Alert Popup');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getBlockTypeDescription()
     */
    public function getBlockTypeDescription()
    {
        return t('Display text in popups');
    }

    public function add()
    {
        $this->prepareEditUI();
        $this->set('launcherType', self::LAUNCHERTYPE_LINK);
        $this->set('launcherText', '');
        $this->set('launcherImage', null);
        $this->set('launcherCssClass', '');
        $this->set('popupWidth', '67vw');
        $this->set('popupMinWidth', 200);
        $this->set('popupMaxWidth', 600);
        $this->set('popupHeight', '');
        $this->set('popupMinHeight', 100);
        $this->set('popupMaxHeight', 500);
        $this->set('popupBorderWidth', 5);
        $this->set('popupBorderColor', '#dddddd');
        $this->set('popupBackgroundColor', '#ffffff');
        $this->set('popupBackdropColor', '');
        $this->set('popupAnimations', '');
        $this->set('popupCssClass', '');
        $this->set('popupID', '');
        $this->set('popupContent', '');
    }

    public function edit()
    {
        $this->prepareEditUI();
        $this->set('launcherImage', ((int) $this->launcherImage) ?: null);
        $this->set('popupMinWidth', ((int) $this->popupMinWidth) ?: null);
        $this->set('popupMaxWidth', ((int) $this->popupMaxWidth) ?: null);
        $this->set('popupMinHeight', ((int) $this->popupMinHeight) ?: null);
        $this->set('popupMaxHeight', ((int) $this->popupMaxHeight) ?: null);
        $this->set('popupBorderWidth', (int) $this->popupBorderWidth);
        if ($this->popupBorderColor === '') {
            $this->set('popupBorderColor', '#dddddd');
        }
        $this->set('popupContent', LinkAbstractor::translateFromEditMode($this->popupContent));
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::validate()
     */
    public function validate($args)
    {
        $check = $this->normalizeArgs(is_array($args) ? $args : []);

        return is_array($check) ? true : $check;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::save()
     */
    public function save($args)
    {
        $normalized = $this->normalizeArgs(is_array($args) ? $args : []);
        if (!is_array($normalized)) {
            throw new UserMessageException(implode("\n", $normalized->getList()));
        }
        return parent::save($normalized);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::registerViewAssets()
     */
    public function registerViewAssets($outputContent = '')
    {
        $this->requireMyAssets();
    }

    public function view()
    {
        $localization = $this->app->make(Localization::class);
        $this->set('localization', $localization);
        $withUIContext = function(callable $callback) use ($localization) {
            $originalContext = $localization->getActiveContext();
            $localization->setActiveContext(Localization::CONTEXT_UI);
            try {
                return $callback();
            } finally {
                $localization->setActiveContext($originalContext);
            }
        };
        $this->set('popupMinWidth', $this->popupMinWidth ? (int) $this->popupMinWidth : null);
        $this->set('popupMaxWidth', $this->popupMaxWidth ? (int) $this->popupMaxWidth : null);
        $this->set('popupMinHeight', $this->popupMinHeight ? (int) $this->popupMinHeight : null);
        $this->set('popupMaxHeight', $this->popupMaxHeight ? (int) $this->popupMaxHeight : null);
        $this->set('popupBorderWidth', (int) $this->popupBorderWidth);
        $popupContent = LinkAbstractor::translateFrom($this->popupContent);
        $this->set('popupContent', $popupContent);
        $editMessages = [];
        $launcherInnerHtml = '';
        $popupID = $this->popupID;
        if ($popupID === '') {
            $bID = null;
            $b = $this->getBlockObject();
            if ($b) {
                $pb = $b->getProxyBlock();
                if ($pb) {
                    $bID = $pb->getBlockID();
                }
            }
            if (!$bID) {
                $bID = $this->bID;
            }
            $popupID = "alertpopup-{$bID}";
        }
        $this->set('popupID', $popupID);
        switch ($this->launcherType) {
            case self::LAUNCHERTYPE_BUTTON:
            case self::LAUNCHERTYPE_LINK:
                if ($this->launcherText !== '') {
                    $launcherInnerHtml = h($this->launcherText);
                } elseif ($this->launcherImage) {
                    $launcherImageFile = File::getByID((int) $this->launcherImage);
                    $launcherImageFileVersion = $launcherImageFile ? $launcherImageFile->getApprovedVersion() : null;
                    if ($launcherImageFileVersion) {
                        $launcherInnerHtml = '<img src="' . h($launcherImageFileVersion->getRelativePath()) . '" alt="' . h((string) $launcherImageFileVersion->getTitle()) . 'loading="lazy" />';
                    } else {
                        $editMessages[] = $withUIContext(static function() { return t('Unable to find the configured launcher image'); });
                    }
                } else {
                    $editMessages[] = $withUIContext(static function() { return t('Unable to determine the content of the launcher'); });
                }
                break;
            default:
                $editMessages[] = $withUIContext(static function() use ($popupID) { return t('Alert Popup with ID %s launched via code', $popupID); });
                break;
        }
        $this->set('launcherInnerHtml', $launcherInnerHtml);
        $this->set('launcherJS', 'if (window.ccmAlertPopup) window.ccmAlertPopup.show(' . json_encode($popupID) . '); return false');
        $this->set('popupHtml', static::generatePopupHtml($this, $popupID, $popupContent));
        $this->set('editMessages', $editMessages);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Tracker\FileTrackableInterface::getUsedFiles()
     */
    public function getUsedFiles()
    {
        $result = static::getUsedFilesIn($this->popupContent);
        if (($id = (int) $this->launcherImage) > 0 && !in_array($id, $result, true)) {
            $result[] = $id;
        }

        return $result;
    }

    public function getSearchableContent()
    {
        return $this->popupContent;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\File\Tracker\FileTrackableInterface::getUsedCollection()
     */
    public function getUsedCollection()
    {
        return $this->getCollectionObject();
    }


    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function action_generate_preview()
    {
        $token = $this->app->make('token');
        if (!$token->validate('ccm-alertpopup-preview')) {
            throw new UserMessageException($token->getErrorMessage());
        }
        $errors = $this->app->make('helper/validation/error');
        $data = (object) static::parsePopupArguments($this->request->request->all(), $errors);
        if ($errors->has()) {
            throw new UserMessageException(implode("\n", $errors->getList()));
        }

        return $this->app->make(ResponseFactoryInterface::class)->json([
            'popupHtml' => static::generatePopupHtml((object) $data, 'ccm-alertpopup-popuppreview'),
        ]);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::getImportData()
     */
    protected function getImportData($blockNode, $page)
    {
        $args = parent::getImportData($blockNode, $page);
        if (version_compare(APP_VERSION, '9.2.1') < 0) {
            if (!empty($args['popupContent'])) {
                $args['popupContent'] = LinkAbstractor::import($args['popupContent']);
            }
        }

        return $args;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::export()
     */
    public function export(\SimpleXMLElement $blockNode)
    {
        parent::export($blockNode);
        if (version_compare(APP_VERSION, '9.4.0') < 0) {
            $popupContent = (string) $blockNode->data->record->popupContent;
            if ($popupContent !== '') {
                $popupContentFixed = LinkAbstractor::export($popupContent);
                if ($popupContentFixed !== $popupContent) {
                    unset($blockNode->data->record->popupContent);
                    $xmlService = $this->app->make(Xml::class);
                    if (method_exists($xmlService, 'createChildElement')) {
                        $xmlService->createChildElement($blockNode->data->record, 'popupContent', $popupContentFixed);
                    } else {
                        $xmlService->createCDataNode($blockNode->data->record, 'popupContent', $popupContentFixed);
                    }
                }
            }
        }
    }

    private function prepareEditUI()
    {
        $this->requireMyAssets();
        if (version_compare(APP_VERSION, '9') < 0) {
            $this->requireAsset('javascript', 'vue');
            $this->addHeaderItem('<style>.ccm-ui [v-cloak] { display: none!important; }</style>');
        }
        $this->set('token', $this->app->make('token'));
        $this->set('form', $this->app->make('helper/form'));
        $this->set('ui', $this->app->make('helper/concrete/ui'));
        $this->set('al', $this->app->make('helper/concrete/asset_library'));
        $this->set('editor', $this->app->make('editor'));
    }

    /**
     * @param array $args
     *
     * @return \Concrete\Core\Error\Error|\Concrete\Core\Error\ErrorList\ErrorList|array
     */
    private function normalizeArgs(array $args)
    {
        $args += [
            'launcherType' => '',
            'launcherText' => '',
            'launcherImage' => '',
            'launcherCssClass' => '',
            'popupID' => '',
        ];
        $errors = $this->app->make('helper/validation/error');
        $normalized = [
            'launcherType' => trim((string) $args['launcherType']),
            'launcherText' => '',
            'launcherImage' => null,
            'launcherCssClass' => '',
            'popupID' => trim((string) $args['popupID']),
            
        ] + static::parsePopupArguments($args, $errors);
        switch ($normalized['launcherType']) {
            case self::LAUNCHERTYPE_LINK:
            case self::LAUNCHERTYPE_BUTTON:
                if ($args['launcherContentType'] !== 'image') {
                    $normalized['launcherText'] = trim((string) $args['launcherText']);
                    if ($args['launcherContentType'] === 'text' && $normalized['launcherText'] === '') {
                        $errors->add(t('Please specify the text of the launcher'));
                    }
                }
                if ($normalized['launcherText'] === '' && $args['launcherContentType'] !== 'text') {
                    $id = is_numeric($args['launcherImage']) ? (int) $args['launcherImage'] : 0;
                    if ($id > 0) {
                        $file = File::getByID($id);
                        $fileVersion = $file ? $file->getApprovedVersion() : null;
                        if ($fileVersion) {
                            $normalized['launcherImage'] = $id;
                        }
                    }
                    if ($args['launcherContentType'] === 'image' && $normalized['launcherImage'] === null) {
                        $errors->add(t('Please specify the image of the launcher'));
                    }
                }
                if (!$errors->has() && $normalized['launcherText'] === '' && $normalized['launcherImage'] === null) {
                    $errors->add(t('Please specify the text or the image of the launcher'));
                }
                $normalized['launcherCssClass'] = preg_replace('/\s+/', ' ', trim($args['launcherCssClass']));
                if (!preg_match('/^(' . static::RX_VALIDATE_CSS_CLASSLIST . ')?$/', $normalized['launcherCssClass'])) {
                    $errors->add(t('The CSS classes of the launcher contain invalid characters'));
                }
                break;
            case self::LAUNCHERTYPE_NONE:
                if ($normalized['popupID'] === '') {
                    $errors->add(t("The ID of the popup must be specified if there's no launcher"));
                }
                break;
            default:
                $errors->add(t('Please specify the type of the launcher'));
                break;
        }
        if (!preg_match('/^(' . static::RX_VALIDATE_ID . ')?$/', $normalized['popupID'])) {
            $errors->add(t('The ID of the popup contains invalid characters'));
        }

        return $errors->has() ? $errors : $normalized;
    }

    /**
     * @param string|null $richText
     *
     * @return int[]
     */
    protected static function getUsedFilesIn($richText)
    {
        $result = [];
        $matches = null;
        if ($richText) {
            if (preg_match_all('/\<concrete-picture[^>]*?fID\s*=\s*[\'"]([^\'"]*?)[\'"]/i', $richText, $matches)) {
                foreach ($matches[1] as $id) {
                    $id = (int) $id;
                    if ($id > 0) {
                        $result[] = $id;
                    }
                }
            }
            if (preg_match_all('(FID_DL_\d+)', $richText, $matches)) {
                foreach ($matches[0] as $id) {
                    $id = (int) $id;
                    if ($id > 0) {
                        $result[] = $id;
                    }
                }
            }
        }

        return array_values(array_unique($result));
    }

    private function requireMyAssets()
    {
        $assetList = AssetList::getInstance();
        if (!array_key_exists('alert-popup', $assetList->getRegisteredAssetGroups())) {
            $pkg = $this->app->make(PackageService::class)->getByHandle('alert_popup');
            $assetList->register(
                // $assetType
                'css',
                // $assetHandle
                'alert-popup',
                // $filename
                'assets/alert-popup.css',
                [
                    'version' => $pkg->getPackageVersion(),
                    'minify' => false,
                    'combine' => true,
                ],
                'alert_popup'
                );
            $assetList->register(
                // $assetType
                'javascript',
                // $assetHandle
                'alert-popup',
                // $filename
                'assets/alert-popup.js',
                [
                    'version' => $pkg->getPackageVersion(),
                    'minify' => false,
                    'combine' => true,
                ],
                'alert_popup'
                );
            $assetList->registerGroup('alert-popup', [
                ['css', 'alert-popup'],
                ['javascript', 'alert-popup'],
            ]);
        }
        $this->requireAsset('alert-popup');
    }

    /**
     * @param \Concrete\Core\Error\ErrorList\ErrorList $errors
     *
     * @return array
     */
    private static function parsePopupArguments(array $args, $errors)
    {
        $args += [
            'popupWidth' => '',
            'popupMinWidth' => '',
            'popupMaxWidth' => '',
            'popupHeight' => '',
            'popupMinHeight' => '',
            'popupMaxHeight' => '',
            'popupBorderWidth' => '',
            'popupBorderColor' => '',
            'popupBackgroundColor' => '',
            'popupBackdropColor' => '',
            'popupAnimations' => '',
            'popupCssClass' => '',
            'popupContent' => '',
        ];
        $normalized = [
            'popupWidth' => trim((string) $args['popupWidth']),
            'popupMinWidth' => trim((string) $args['popupMinWidth']),
            'popupMaxWidth' => trim((string) $args['popupMaxWidth']),
            'popupHeight' => trim((string) $args['popupHeight']),
            'popupMinHeight' => trim((string) $args['popupMinHeight']),
            'popupMaxHeight' => trim((string) $args['popupMaxHeight']),
            'popupBorderWidth' => trim((string) $args['popupBorderWidth']),
            'popupBorderColor' => trim((string) $args['popupBorderColor']),
            'popupBackgroundColor' => trim((string) $args['popupBackgroundColor']),
            'popupBackdropColor' => trim((string) $args['popupBackdropColor']),
            'popupAnimations' => trim((string) $args['popupAnimations']),
            'popupCssClass' => preg_replace('/\s+/',trim((string) $args['popupCssClass']), ' '),
            'popupContent' => LinkAbstractor::translateTo(trim((string) $args['popupContent'])),
        ];
        if ($normalized['popupWidth'] === '') {
            $errors->add(t('Please specify the width of the popup'));
        } elseif (!preg_match('/^(100|([1-9][0-9]?))vw$/', $normalized['popupWidth']) && !preg_match('/^[1-9]\d{0,9}px$/', $normalized['popupWidth'])) {
            $errors->add(t('Invalid width of the popup'));
        }
        if ($normalized['popupMinWidth'] === '') {
            $normalized['popupMinWidth'] = null;
        } elseif (!preg_match('/^[1-9]\d{0,9}$/', $normalized['popupMinWidth'])) {
            $errors->add(t('Invalid minimum width of the popup'));
        } else {
            $normalized['popupMinWidth'] = (int) $normalized['popupMinWidth'];
        }
        if ($normalized['popupMaxWidth'] === '') {
            $normalized['popupMaxWidth'] = null;
        } elseif (!preg_match('/^[1-9]\d{0,9}$/', $normalized['popupMaxWidth'])) {
            $errors->add(t('Invalid maximum width of the popup'));
        } else {
            $normalized['popupMaxWidth'] = (int) $normalized['popupMaxWidth'];
        }
        if ($normalized['popupHeight'] !== '' && (!preg_match('/^(100|([1-9][0-9]?))vh$/', $normalized['popupHeight']) && !preg_match('/^[1-9]\d{0,9}px$/', $normalized['popupHeight']))) {
            $errors->add(t('Invalid height of the popup'));
        }
        if ($normalized['popupMinHeight'] === '') {
            $normalized['popupMinHeight'] = null;
        } elseif (!preg_match('/^[1-9]\d{0,9}$/', $normalized['popupMinHeight'])) {
            $errors->add(t('Invalid minimum height of the popup'));
        } else {
            $normalized['popupMinHeight'] = (int) $normalized['popupMinHeight'];
        }
        if ($normalized['popupMaxHeight'] === '') {
            $normalized['popupMaxHeight'] = null;
        } elseif (!preg_match('/^[1-9]\d{0,9}$/', $normalized['popupMaxHeight'])) {
            $errors->add(t('Invalid maximum height of the popup'));
        } else {
            $normalized['popupMaxHeight'] = (int) $normalized['popupMaxHeight'];
        }
        if (!preg_match('/^\d{0,9}$/', $normalized['popupBorderWidth'])) {
            $errors->add(t('Invalid width of the border of of the popup'));
        } else {
            $normalized['popupBorderWidth'] = (int) $normalized['popupBorderWidth'];
        }
        if ($normalized['popupBorderWidth'] > 0 && $normalized['popupBorderColor'] === '') {
            $errors->add(t('Please specify the width of the border of the popup'));
        }
        if (!preg_match('/^(' . static::RX_VALIDATE_CSS_CLASSLIST . ')?$/', $normalized['popupCssClass'])) {
            $errors->add(t('The CSS classes of the popup contain invalid characters'));
        }
        if ($normalized['popupBackgroundColor'] === '') {
            $errors->add(t('Please specify the background color of the popup'));
        }
        if ($normalized['popupContent'] === '') {
            $errors->add(t('Please specify the content of the popup'));
        }

        return $normalized;
    }

    /**
     * @param object $data
     * @param string $popupID
     * @param string|null $popupContent
     *
     * @return string
     */
    private static function generatePopupHtml($data, $popupID, $popupContent = null)
    {
        $popupHtml = '<dialog';
        $popupHtml .= ' id="' . h($popupID) . '"';
        $popupHtml .= ' class="ccm-alert-popup';
        foreach (preg_split('/[^\w\-]/', $data->popupAnimations, -1, PREG_SPLIT_NO_EMPTY) as $animation) {
            $popupHtml .= " ccm-alert-popup-anim-{$animation}";
            
        }
        if ($data->popupCssClass !== '') {
            $popupHtml .= ' ' . h($data->popupCssClass);
        }
        $popupHtml .= '"';
        $styles = [
            "background-color: {$data->popupBackgroundColor}",
            "width: {$data->popupWidth}",
        ];
        $contentStyles = [];
        if ($data->popupBorderWidth) {
            $styles[] = "border: solid {$data->popupBorderWidth}px {$data->popupBorderColor}";
        }
        if ($data->popupHeight) {
            $styles[] = "height: {$data->popupHeight}";
        }
        if ($data->popupMinWidth) {
            $styles[] = "min-width: {$data->popupMinWidth}px";
        }
        if ($data->popupMaxWidth) {
            $styles[] = "max-width: {$data->popupMaxWidth}px";
        }
        if ($data->popupMinHeight) {
            $contentStyles[] = "min-height: {$data->popupMinHeight}px";
        }
        if ($data->popupMaxHeight) {
            $contentStyles[] = "max-height: {$data->popupMaxHeight}px";
        }
        $popupHtml .= ' style="' . implode('; ', $styles) . '"';
        if ($data->popupBackdropColor !== '') {
            $popupHtml .= ' data-backdrop-color="' . h($data->popupBackdropColor) . '"';
        }
        $popupHtml .= '><div class="ccm-alert-popup-content"';
        if ($contentStyles !== []) {
            $popupHtml .= ' style="' . implode('; ', $contentStyles) . '"';
        }
        $popupHtml .= '>';
        if ($popupContent === null) {
            $popupContent = LinkAbstractor::translateFrom($data->popupContent);
        }
        $popupHtml .= $popupContent . '</div></dialog>';

        return $popupHtml;
    }
}
