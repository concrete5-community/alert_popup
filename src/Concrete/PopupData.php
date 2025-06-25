<?php

namespace Concrete\Package\AlertPopup;

use ArrayAccess;
use Concrete\Core\Editor\LinkAbstractor;

defined('C5_EXECUTE') or die('Access Denied.');

class PopupData
{
    /**
     * @public
     */
    const RX_VALIDATE_CSS_CLASSLIST = '-?[_a-zA-Z]+[_a-zA-Z0-9\-]*( -?[_a-zA-Z]+[_a-zA-Z0-9\-]*)*';

    /**
     * The width of the popup.
     *
     * @Doctrine\ORM\Mapping\Column(type="string", length=20, nullable=false, options={"comment": "Width of the popup"})
     *
     * @var string
     */
    protected $width;

    /**
     * The min width (in pixels) of the popup.
     *
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true, options={"unsigned": true, "comment": "Min width (in pixels) of the popup"})
     *
     * @var int|null
     */
    protected $minWidth;

    /**
     * The max width (in pixels) of the popup.
     *
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true, options={"unsigned": true, "comment": "Max width (in pixels) of the popup"})
     *
     * @var int|null
     */
    protected $maxWidth;

    /**
     * The height of the popup.
     *
     * @Doctrine\ORM\Mapping\Column(type="string", length=20, nullable=false, options={"comment": "Height of the popup"})
     *
     * @var string
     */
    protected $height;

    /**
     * The min height (in pixels) of the popup.
     *
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true, options={"unsigned": true, "comment": "Min height (in pixels) of the popup"})
     *
     * @var int|null
     */
    protected $minHeight;

    /**
     * The max height (in pixels) of the popup.
     *
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=true, options={"unsigned": true, "comment": "Max height (in pixels) of the popup"})
     *
     * @var int|null
     */
    protected $maxHeight;

    /**
     * The width (in pixels) of border of the popup.
     *
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=false, options={"unsigned": true, "comment": "Width (in pixels) of border of the popup"})
     *
     * @var int
     */
    protected $borderWidth;

    /**
     * The color of the border of the popup.
     *
     * @Doctrine\ORM\Mapping\Column(type="string", length=50, nullable=false, options={"comment": "Color of the border of the popup"})
     *
     * @var string
     */
    protected $borderColor;

    /**
     * The color of the background of the popup.
     *
     * @Doctrine\ORM\Mapping\Column(type="string", length=50, nullable=false, options={"comment": "Color of the background of the popup"})
     *
     * @var string
     */
    protected $backgroundColor;

    /**
     * The color of the backdrop of the popup.
     *
     * @Doctrine\ORM\Mapping\Column(type="string", length=50, nullable=false, options={"comment": "Color of the backdrop of the popup"})
     *
     * @var string
     */
    protected $backdropColor;

    /**
     * The list of popup animations.
     *
     * @Doctrine\ORM\Mapping\Column(type="text", nullable=false, options={"comment": "List of popup animations"})
     *
     * @var string
     */
    protected $animations;

    /**
     * The animations duration, in milliseconds.
     *
     * @Doctrine\ORM\Mapping\Column(type="integer", nullable=false, options={"unsigned": true, "comment": "Animations duration, in milliseconds"})
     *
     * @var int
     */
    protected $animationDuration;

    /**
     * The CSS classes for the popup.
     *
     * @Doctrine\ORM\Mapping\Column(type="string", length=255, nullable=false, options={"comment": "CSS classes for the popup"})
     *
     * @var string
     */
    protected $cssClass;

    /**
     * The Rich Text of the popup.
     *
     * @Doctrine\ORM\Mapping\Column(type="text", nullable=false, options={"comment": "Rich Text of the popup"})
     *
     * @var string
     */
    protected $content;

    public function __construct()
    {
        $this->width = '67vw';
        $this->minWidth = 200;
        $this->maxWidth = 600;
        $this->height = '';
        $this->minHeight = 100;
        $this->maxHeight = 500;
        $this->borderWidth = 5;
        $this->borderColor = '#dddddd';
        $this->backgroundColor = '#ffffff';
        $this->backdropColor = '';
        $this->animations = '';
        $this->animationDuration = 600;
        $this->cssClass = '';
        $this->content = '';
    }

    /**
     * Get the width of the popup.
     *
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set the width of the popup.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setWidth($value)
    {
        $this->width = (string) $value;

        return $this;
    }

    /**
     * Get the min width (in pixels) of the popup.
     *
     * @return int|null
     */
    public function getMinWidth()
    {
        return $this->minWidth;
    }

    /**
     * Set the min width (in pixels) of the popup.
     *
     * @param int|string|null $value
     *
     * @return $this
     */
    public function setMinWidth($value)
    {
        $this->minWidth = (string) $value === '' ? null : (int) $value;

        return $this;
    }

    /**
     * Get the max width (in pixels) of the popup.
     *
     * @return int|null
     */
    public function getMaxWidth()
    {
        return $this->maxWidth;
    }

    /**
     * Set the max width (in pixels) of the popup.
     *
     * @param int|string|null $value
     *
     * @return $this
     */
    public function setMaxWidth($value)
    {
        $this->maxWidth = (string) $value === '' ? null : (int) $value;

        return $this;
    }

    /**
     * Get the height of the popup.
     *
     * @return string
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set the height of the popup.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setHeight($value)
    {
        $this->height = (string) $value;

        return $this;
    }

    /**
     * Get the min height (in pixels) of the popup.
     *
     * @return int|null
     */
    public function getMinHeight()
    {
        return $this->minHeight;
    }

    /**
     * Set the min height (in pixels) of the popup.
     *
     * @param int|string|null $value
     *
     * @return $this
     */
    public function setMinHeight($value)
    {
        $this->minHeight = (string) $value === '' ? null : (int) $value;

        return $this;
    }

    /**
     * Get the max height (in pixels) of the popup.
     *
     * @return int|null
     */
    public function getMaxHeight()
    {
        return $this->maxHeight;
    }

    /**
     * Set the max height (in pixels) of the popup.
     *
     * @param int|string|null $value
     *
     * @return $this
     */
    public function setMaxHeight($value)
    {
        $this->maxHeight = (string) $value === '' ? null : (int) $value;

        return $this;
    }

    /**
     * Get the width (in pixels) of border of the popup.
     *
     * @return int
     */
    public function getBorderWidth()
    {
        return $this->borderWidth;
    }

    /**
     * Se the width (in pixels) of border of the popup.
     *
     * @param int|string $value
     *
     * @return $this
     */
    public function setBorderWidth($value)
    {
        $this->borderWidth = (int) $value;

        return $this;
    }

    /**
     * Get the color of the border of the popup.
     *
     * @return string
     */
    public function getBorderColor()
    {
        return $this->borderColor;
    }

    /**
     * Set the color of the border of the popup.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setBorderColor($value)
    {
        $this->borderColor = (string) $value;

        return $this;
    }

    /**
     * Get the color of the background of the popup.
     *
     * @return string
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * Set the color of the background of the popup.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setBackgroundColor($value)
    {
        $this->backgroundColor = (string) $value;

        return $this;
    }

    /**
     * Get the color of the backdrop of the popup.
     *
     * @return string
     */
    public function getBackdropColor()
    {
        return $this->backdropColor;
    }

    /**
     * Set the color of the backdrop of the popup.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setBackdropColor($value)
    {
        $this->backdropColor = (string) $value;

        return $this;
    }

    /**
     * Get the list of popup animations.
     *
     * @return string[]
     */
    public function getAnimations()
    {
        return preg_split('/[^\w\-]/', $this->animations, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * Set the list of popup animations.
     *
     * @param string|string[] $value
     *
     * @return $this
     */
    public function setAnimations($value)
    {
        if (is_array($value)) {
            $value = array_map(
                static function ($animation) {
                    return preg_replace('/[^\w\-]/', '', (string) $animation);
                },
                $value
            );
        } else {
            $value = preg_split('/[^\w\-]/', (string) $value, -1, PREG_SPLIT_NO_EMPTY);
        }
        $animations = [];
        foreach ($value as $animation) {
            if ($animation !== '' && !in_array($animation, $animations, true)) {
                $animations[] = $animation;
            }
        }
        $this->animations = implode(' ', $animations);

        return $this;
    }

    /**
     * Get the animations duration, in milliseconds.
     *
     * @return int
     */
    public function getAnimationDuration()
    {
        return $this->animationDuration;
    }

    /**
     * Set the animations duration, in milliseconds.
     *
     * @param int|string $value
     *
     * @return $this
     */
    public function setAnimationDuration($value)
    {
        $this->animationDuration = (int) $value;

        return $this;
    }

    /**
     * Get the CSS classes for the popup.
     *
     * @return string
     */
    public function getCssClass()
    {
        return $this->cssClass;
    }

    /**
     * Set the CSS classes for the popup.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setCssClass($value)
    {
        $this->cssClass = trim(preg_replace('/\s+/', ' ', (string) $value));

        return $this;
    }

    /**
     * Get the Rich Text of the popup.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the Rich Text of the popup.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setContent($value)
    {
        $this->content = (string) $value;

        return $this;
    }

    /**
     * @return void
     */
    public function validate(ArrayAccess $errors)
    {
        if ($this->getWidth() === '') {
            $errors[] = t('Please specify the width of the popup');
        } elseif (!preg_match('/^(100|([1-9][0-9]?))vw$/', $this->getWidth()) && !preg_match('/^[1-9]\d{0,9}px$/', $this->getWidth())) {
            $errors[] = t('Invalid width of the popup');
        }
        if ($this->getMinWidth() !== null && $this->getMinWidth() < 1) {
            $errors[] = t('Invalid minimum width of the popup');
        }
        if ($this->getMaxWidth() !== null && $this->getMaxWidth() < 1) {
            $errors[] = t('Invalid maximum width of the popup');
        }
        if ($this->getHeight() !== '' && (!preg_match('/^(100|([1-9][0-9]?))vh$/',$this->getHeight()) && !preg_match('/^[1-9]\d{0,9}px$/',$this->getHeight()))) {
            $errors[] = t('Invalid height of the popup');
        }
        if ($this->getMinHeight() !== null && $this->getMinHeight() < 1) {
            $errors[] = t('Invalid minimum height of the popup');
        }
        if ($this->getMaxHeight() !== null && $this->getMaxHeight() < 1) {
            $errors[] = t('Invalid maximum height of the popup');
        }
        if ($this->getBorderWidth() < 0) {
            $errors[] = t('Invalid width of the border of the popup');
        }
        if ($this->getBorderWidth() > 0 && $this->getBorderColor() === '') {
            $errors[] = t('Please specify the color of the border of the popup');
        }
        if (!preg_match('/^(' . static::RX_VALIDATE_CSS_CLASSLIST . ')?$/', $this->getCssClass())) {
            $errors[] = t('The CSS classes of the popup contain invalid characters');
        }
        if ($this->getBackgroundColor() === '') {
            $errors[] = t('Please specify the background color of the popup');
        }
        if ($this->getAnimations() !== [] && $this->getAnimationDuration() <= 0) {
            $errors[] = t('Invalid duration of the animations');
        }
        if ($this->getContent() === '') {
            $errors[] = t('Please specify the content of the popup');
        }
    }

    /**
     * @return static
     */
    public static function fromEditUI(array $fields, ArrayAccess $errors)
    {
        $result = new static();
        $result
            ->setWidth(isset($fields['popupWidth']) ? trim((string) $fields['popupWidth']) : '')
            ->setMinWidth(isset($fields['popupMinWidth']) ? trim((string) $fields['popupMinWidth']) : null)
            ->setMaxWidth(isset($fields['popupMaxWidth']) ? trim((string) $fields['popupMaxWidth']) : null)
            ->setHeight(isset($fields['popupHeight']) ? trim((string) $fields['popupHeight']) : '')
            ->setMinHeight(isset($fields['popupMinHeight']) ? trim((string) $fields['popupMinHeight']) : null)
            ->setMaxHeight(isset($fields['popupMaxHeight']) ? trim((string) $fields['popupMaxHeight']) : null)
            ->setBorderWidth(isset($fields['popupBorderWidth']) ? trim((string) $fields['popupBorderWidth']) : 0)
            ->setBorderColor(isset($fields['popupBorderColor']) ? trim((string) $fields['popupBorderColor']) : '')
            ->setBackgroundColor(isset($fields['popupBackgroundColor']) ? trim((string) $fields['popupBackgroundColor']) : '')
            ->setBackdropColor(isset($fields['popupBackdropColor']) ? trim((string) $fields['popupBackdropColor']) : '')
            ->setAnimations(isset($fields['popupAnimations']) ? $fields['popupAnimations'] : [])
            ->setAnimationDuration(isset($fields['popupAnimationDuration']) ? trim((string) $fields['popupAnimationDuration']) : 0)
            ->setCssClass(isset($fields['popupCssClass']) ? trim((string) $fields['popupCssClass']) : '')
            ->setContent(isset($fields['popupContent']) ? LinkAbstractor::translateTo(trim((string) $fields['popupContent'])) : '')
            ->validate($errors)
        ;

        return $result;
    }

    /**
     * @return array
     */
    public function toEditOptions()
    {
        $matches = null;
        $result = [
            'minWidth' => $this->getMinWidth(),
            'maxWidth' => $this->getMaxWidth(),
            'minHeight' => $this->getMinHeight(),
            'maxHeight' => $this->getMaxHeight(),
            'borderWidth' => $this->getBorderWidth(),
            'borderColor' => $this->getBorderColor(),
            'backgroundColor' => $this->getBackgroundColor(),
            'backdropColorRGB' => preg_match('/^#[0-9a-f]{8}$/i', $this->getBackdropColor()) ? substr($this->getBackdropColor(), 0, 7) : '#000000',
            'backdropColorAlpha' => preg_match('/^#[0-9a-f]{8}$/i', $this->getBackdropColor()) ? round(100 * hexdec(substr($this->getBackdropColor(), 7, 2)) / 255) : 10,
            'cssClass' => $this->getCssClass(),
        ];

        $matched = preg_match('/^(\d+)(\D+)$/', $this->getWidth(), $matches);
        $result['widthValue'] = $matched ? (int) $matches[1] : '';
        $result['widthUnit'] = $matched ? $matches[2] : 'vw';

        $matched = preg_match('/^(\d+)(\D+)$/', $this->getHeight(), $matches);
        $result['heightValue'] = $matched ? (int) $matches[1] : '';
        $result['heightUnit'] = $matched ? $matches[2] : 'vh';

        return $result;
    }

    /**
     * @return array
     */
    public function toEditAnimations()
    {
        return [
            'selectedAnimations' => $this->getAnimations(),
            'animationDuration' => $this->getAnimationDuration() ?: 600,
        ];
    }
}
