<?php
namespace Keizer\KoningLibrary\Helper;

use TYPO3\CMS\Frontend\Page\PageRepository;

/**
 * Helper; Active Menu Functionality
 * Usage:
 * 1 = TMENU
 * 1 {
 *   itemArrayProcFunc = Keizer\KoningLibrary\Helper\MenuHelper->evaluateActiveMenuItems
 *   NO = ..
 * }
 *
 * @package Keizer\KoningLibrary\Helper
 */
class MenuHelper implements \TYPO3\CMS\Core\SingletonInterface
{

    /**
     * @var array
     */
    protected $rootLineIds;

    /**
     * Evaluate all menu items for active states
     *
     * @param array $menu
     * @return mixed
     */
    public function evaluateActiveMenuItems($menu)
    {
        // Loop through each menu item if it is an active element
        foreach ((array) $menu as $key => $item) {
            // Check if page has content from another page
            if ((int) $item['content_from_pid'] > 0) {
                if ($this->isInCurrentPageRoot($item['content_from_pid'])) {
                    $menu[$key]['ITEM_STATE'] = 'ACT';
                }
            }

            // Check if there is a specific shortcut configured
            if ((int) $item['doktype'] === PageRepository::DOKTYPE_SHORTCUT && (int) $item['shortcut'] > 0) {
                if ($this->isInCurrentPageRoot($item['shortcut'])) {
                    $menu[$key]['ITEM_STATE'] = 'ACT';
                }
            }
        }

        return $menu;
    }

    /**
     * Check if page is in current active page root
     *
     * @param integer $pageId
     * @return bool
     */
    protected function isInCurrentPageRoot($pageId)
    {
        if ($this->getTypoScriptFrontendController() !== null) {
            if ($this->rootLineIds === null) {
                $this->rootLineIds = [];
                foreach ($this->getTypoScriptFrontendController()->rootLine as $page) {
                    $this->rootLineIds[] = $page['uid'];
                }
            }

            return in_array($pageId, $this->rootLineIds);
        }
        return false;
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }

}
