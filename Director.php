<?php
namespace ParseBrand;

/**
 * Class Director
 * @package ParseBrand
 */
class Director
{
    /**
     * @param $link
     * @param $brandName
     */
    public static function addBrandPoduction($link, $brandName)
    {
        $brand = new Brand();
        $element = new Element();
        $arBrand = $brand->url($link)->getElements();
        $arrArticuls = [];
        foreach ($arBrand as $key => $collection)
        {
            foreach ($collection as $item)
            {
                $res = $element
                    ->url($item)
                    ->parseImg('.detail-image')
                    ->parseProperties(".prop-list li .prop-name", ".prop-list li .prop-value")
                    ->parseTable('.table-s1')
                    ->getElementInfo();
                foreach ($res as $el)
                {
                    if ($arrArticuls && array_search($el['articul'], $arrArticuls) !== false) continue;
                    $arrArticuls[] = $el['articul'];
                    $arResult[$key][] = $el;
                }
            }
        }
        Product::addProduct($arResult, $brandName, 9, 1125);
    }
}