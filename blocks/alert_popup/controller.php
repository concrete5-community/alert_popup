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
use Concrete\Core\Statistics\UsageTracker\AggregateTracker;
use Concrete\Core\Utility\Service\Xml;
use Concrete\Package\AlertPopup\PopupData;
use Concrete\Package\AlertPopup\Service;
use SimpleXMLElement;

defined('C5_EXECUTE') or die('Access Denied.');

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
     * @var \Concrete\Core\Statistics\UsageTracker\AggregateTracker|null
     */
    protected $tracker;

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
     * Animations duration, in milliseconds.
     *
     * @var int|string|null
     */
    protected $popupAnimationDuration;

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
        $this->set('popupData', new PopupData());
        $this->set('popupID', '');
    }

    public function edit()
    {
        $this->prepareEditUI();
        $this->set('launcherImage', ((int) $this->launcherImage) ?: null);
        $this->set('popupData', $this->buildPopupData());
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
        $this->popupContent = $normalized['popupContent'];
        $this->launcherImage = $normalized['launcherImage'];
        parent::save($normalized);
        if (version_compare(APP_VERSION, '9.0.2') < 0) {
            $this->getTracker()->track($this);
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::delete()
     */
    public function delete()
    {
        if (version_compare(APP_VERSION, '9.0.2') < 0) {
            $this->getTracker()->forget($this);
        }
        parent::delete();
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
        $withUIContext = function(callable $callback) use ($localization) {
            $originalContext = $localization->getActiveContext();
            $localization->setActiveContext(Localization::CONTEXT_UI);
            try {
                return $callback();
            } finally {
                $localization->setActiveContext($originalContext);
            }
        };
        $this->set('popupData', $this->buildPopupData());
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
        $this->set('service', $this->app->make(Service::class));
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
        if (($id = (int) $this->launcherImage) > 0) {
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
        $popupData = PopupData::fromEditUI($this->request->request->all(), $errors);
        if ($errors->has()) {
            throw new UserMessageException(implode("\n", $errors->getList()));
        }
        $popupHtml = $this->app->make(Service::class)->generatePopupHtml($popupData, 'ccm-alertpopup-popuppreview');

        return $this->app->make(ResponseFactoryInterface::class)->json([
            'popupHtml' => $popupHtml,
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
            if (isset($blockNode->data->record->popupContent)) {
                $args['popupContent'] = LinkAbstractor::import((string) $blockNode->data->record->popupContent);
            }
        }

        return $args;
    }

    /**
     * @return \Concrete\Core\Statistics\UsageTracker\AggregateTracker
     */
    protected function getTracker()
    {
        if ($this->tracker === null) {
            $this->tracker = $this->app->make(AggregateTracker::class);
        }

        return $this->tracker;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Block\BlockController::export()
     */
    public function export(SimpleXMLElement $blockNode)
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
        $errors = $this->app->make('helper/validation/error');
        $popupData = PopupData::fromEditUI($args, $errors);
        $args += [
            'launcherType' => '',
            'launcherContentType' => '',
            'launcherText' => '',
            'launcherImage' => '',
            'launcherCssClass' => '',
            'popupID' => '',
        ];
        $normalized = [
            'launcherType' => trim((string) $args['launcherType']),
            'launcherText' => '',
            'launcherImage' => null,
            'launcherCssClass' => '',
            'popupID' => trim((string) $args['popupID']),
            'popupWidth' => $popupData->getWidth(),
            'popupMinWidth' => $popupData->getMinWidth(),
            'popupMaxWidth' => $popupData->getMaxWidth(),
            'popupHeight' => $popupData->getHeight(),
            'popupMinHeight' => $popupData->getMinHeight(),
            'popupMaxHeight' => $popupData->getMaxHeight(),
            'popupBorderWidth' => $popupData->getBorderWidth(),
            'popupBorderColor' => $popupData->getBorderColor(),
            'popupBackgroundColor' => $popupData->getBackgroundColor(),
            'popupBackdropColor' => $popupData->getBackdropColor(),
            'popupAnimations' => implode(' ', $popupData->getAnimations()),
            'popupAnimationDuration' => $popupData->getAnimationDuration(),
            'popupCssClass' => $popupData->getCssClass(),
            'popupContent' => $popupData->getContent(),
        ];
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
                if (!preg_match('/^(' . PopupData::RX_VALIDATE_CSS_CLASSLIST . ')?$/', $normalized['launcherCssClass'])) {
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
     * @return int[]|string[]
     */
    protected static function getUsedFilesIn($richText)
    {
        $richText = (string) $richText;
        if ($richText === '') {
            return [];
        }
        $rxIdentifier = '(?<id>[1-9][0-9]{0,18})';
        if (method_exists(\Concrete\Core\File\File::class, 'getByUUID')) {
            $rxIdentifier = '(?:(?<uuid>[0-9a-fA-F]{8}(?:-[0-9a-fA-F]{4}){3}-[0-9a-fA-F]{12})|' . $rxIdentifier . ')';
        }
        $result = [];
        $matches = null;
        foreach ([
            '/\<concrete-picture[^>]*?\bfID\s*=\s*[\'"]' . $rxIdentifier . '[\'"]/i',
            '/\bFID_DL_' . $rxIdentifier . '\b/',
        ] as $rx) {
            if (!preg_match_all($rx, $richText, $matches)) {
                continue;
            }
            $result = array_merge($result, array_map('intval', array_filter($matches['id'])));
            if (isset($matches['uuid'])) {
                $result = array_merge($result, array_map('strtolower', array_filter($matches['uuid'])));
            }
        }

        return $result;
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

    private function buildPopupData()
    {
        $result = new PopupData();
        $defaultBorderColor = $result->getBorderColor();
        $result
            ->setWidth($this->popupWidth)
            ->setMinWidth($this->popupMinWidth)
            ->setMaxWidth($this->popupMaxWidth)
            ->setHeight($this->popupHeight)
            ->setMinHeight($this->popupMinHeight)
            ->setMaxHeight($this->popupMaxHeight)
            ->setBorderWidth($this->popupBorderWidth)
            ->setBorderColor($this->popupBorderColor)
            ->setBackgroundColor($this->popupBackgroundColor)
            ->setBackdropColor($this->popupBackdropColor)
            ->setAnimations($this->popupAnimations)
            ->setAnimationDuration($this->popupAnimationDuration)
            ->setCssClass($this->popupCssClass)
            ->setContent($this->popupContent)
        ;
        if ($result->getBorderColor() === '') {
            $result->setBorderColor($defaultBorderColor);
        }

        return $result;
    }
}
