<?php
namespace ParseBrand;
/**
 * Class Product
 * Класс содержит один статический метод для добавления товаров из исходящего массива
 */
class Product
{
    /**
     * @param $arrProperties - массим с входными данными для создания товаров.
     * Структура массива: [Общий - [Коллекции - [Элементы - [Свойства элемента]]]
     * @param $brand - Название бренда должно соответствовать названию на сайте, иначе будет создан новый бренд
     * @param $iblock - ID инфоблока где хранятся товары
     * @param $sectionID - ID базовой секции, куда будет происходить загрузка
     */
    public static function addProduct($arrProperties, $brand, $iblock, $sectionID) {
        //Получаем id бренда, если его нет, создаем
        $brandId = Utils::createBrand($brand, 10);

        //Проверяем существует ли такая секция, если нет, то создаем, если да, то передаем ее id в переменную $sectBrandId
        $sectBrandId = Utils::createSection($brand, $iblock, $sectionID);

        foreach ($arrProperties as $keyCollection => $collection)
        {
            //Проверяем существует ли такая секция, если нет, то создаем, если да, то передаем ее id в переменную $sectCollectionId
            $sectCollectionId = Utils::createSection($keyCollection, $iblock, $sectBrandId);

            foreach ($collection as $item)
            {
                //Создаем элементы и добавляем их в ранее созданный раздел
                $el = new \CIBlockElement;
                //Базовый набор свойств
                $properties["ARTIKUL"] = $item['articul'] ? $item['articul'] : ''; // артикул
                $properties["PAR_50"] = $item['PROPERTIES']['Страна'] ? $item['PROPERTIES']['Страна'] : ''; // страна производитель
                $properties["BRAND"] = $brandId ? $brandId : ''; // бренд
                $properties["PAR_25"] = $item['PROPERTIES']['Серия'] ? $item['PROPERTIES']['Серия'] : ''; // коллекция

                $arLoadProductArray = Array(
                    'IBLOCK_SECTION_ID' => $sectCollectionId,
                    'IBLOCK_ID' => $iblock,
                    'NAME' => $item['title'] . $sectCollectionId,
                    'ACTIVE' => 'Y',
                    'CODE' => Utils::translit($item['title']),
                    "DETAIL_PICTURE" => \CFile::MakeFileArray($item['IMAGE']),
                    'PROPERTY_VALUES' => $properties,
                );
                $productID = $el->Add($arLoadProductArray);
                \CCatalogProduct::add(array('ID' => $productID, 'QUANTITY' => 100));
                $result = \Bitrix\Catalog\Model\Price::add(array(
                    'CATALOG_GROUP_ID' => 1,
                    'PRODUCT_ID' => $productID,
                    'PRICE' => preg_replace( '/[^0-9]/', '', $item['price1'] ),
                    'CURRENCY' => 'RUB',
                ));
                echo 'Новый товар - ' . $productID . '<br>';
                \CCatalogProduct::Update($productID, ['QUANTITY' => 100]);
            }
        }
    }
}