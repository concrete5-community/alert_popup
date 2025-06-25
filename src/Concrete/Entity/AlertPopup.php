<?php

namespace Concrete\Package\AlertPopup\Entity;

use Concrete\Package\AlertPopup\PopupData;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @Doctrine\ORM\Mapping\Entity
 * @Doctrine\ORM\Mapping\Table(
 *     name="AlertPopup",
 *     options={"comment": "Popups for the Alert Popup package"}
 * )
 */
class AlertPopup extends PopupData
{
    /**
     * The popup ID (null if not persisted).
     *
     * @Doctrine\ORM\Mapping\Id
     * @Doctrine\ORM\Mapping\Column(type="integer", options={"unsigned": true, "comment": "Popup ID"})
     * @Doctrine\ORM\Mapping\GeneratedValue(strategy="AUTO")
     *
     * @var int|null
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
        $this->id = null;
    }

    /**
     * Get the popup ID (null if not persisted).
     *
     * @return int|null
     */
    public function getID()
    {
        return $this->id;
    }
}
