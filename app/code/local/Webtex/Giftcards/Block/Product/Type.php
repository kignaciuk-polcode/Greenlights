<?php
class Webtex_Giftcards_Block_Product_Type extends Mage_Catalog_Block_Product_View_Abstract
{
    protected function _prepareLayout()
    {
        if ($head = $this->getLayout()->getBlock('head')) {
            $head->setCanLoadCalendarJs(true);
        }
        return parent::_prepareLayout();
    }

    public function getCalendarDateHtml()
    {
        $calendar = $this->getLayout()
            ->createBlock('core/html_date')
            ->setId('day-to-send')
            ->setName('day-to-send')
            ->setClass('input-text input-text')
            ->setImage($this->getSkinUrl('images/calendar.gif'))
            ->setFormat('%Y-%m-%d');
        return $calendar->getHtml();
    }
}
