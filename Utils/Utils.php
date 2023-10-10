<?php
namespace ParseBrand;

/**
 * Class BrandFunctions
 * Набор методов для работы с битриксом
 * Вспомогательный класс для Product
 */
class Utils
{
    /**
     * @param $value
     * @return string
     */
    public static function translit($value): string
    {
        $converter = array(
            'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd', 'е' => 'e',    'ё' => 'e',
            'ж' => 'zh',   'з' => 'z',    'и' => 'i', 'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',
            'н' => 'n', 'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't', 'у' => 'u',
            'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch', 'ш' => 'sh',   'щ' => 'sch',  'ь' => '',
            'ы' => 'y',    'ъ' => '', 'э' => 'e',    'ю' => 'yu',   'я' => 'ya',
        );

        $value = mb_ereg_replace('[^-0-9a-z]', '-', strtr(mb_strtolower($value), $converter));
        $value = mb_ereg_replace('[-]+', '-', $value);
        $value = trim($value, '-');

        return $value;
    }

    /**
     * @param $name
     * @param $iblock
     * @param $sectionParent
     */
    public static function checkSection($name, $iblock, $sectionParent)
    {
        $sectCheck = \CIBlockSection::GetList(
            ["SORT"=>"ASC"],
            ["IBLOCK_ID" => $iblock, "SECTION_ID" => $sectionParent, "NAME" => $name],
            false,
            ["ID"],
            false)
            ->Fetch();
        return $sectCheck ? $sectCheck['ID'] : false;

    }

    /**
     * @param $name
     * @param $iblock
     * @param $sectionParent
     * @return int
     */
    public static function addSection($name, $iblock, $sectionParent): int
    {
        $sectionController = new \CIBlockSection;
        $arFields = Array(
            "ACTIVE" => "N",
            "IBLOCK_SECTION_ID" => $sectionParent,
            "IBLOCK_ID" => $iblock,
            "NAME" => $name,
            "CODE" => self::translit($name)
        );
        return $sectionController->Add($arFields);
    }

    /**
     * @param $name
     * @param $iblock
     * @param $sectionParent
     * @return int
     */
    public static function createSection($name, $iblock, $sectionParent): int
    {
        if ($sectCheck = self::checkSection($name, $iblock, $sectionParent))
        {
            $secId = $sectCheck;
        } else
        {
            $secId = self::addSection($name, $iblock, $sectionParent);
        }
        return $secId;
    }

    /**
     * @param $brand
     * @param int $brandIblock
     * @return array
     */
    public static function selectBrandID($brand, $brandIblock = 10): array
    {
        return \CIBlockElement::GetList(
            [],
            ["IBLOCK_ID" => $brandIblock, 'CODE' => strtolower($brand)],
            false,
            false,
            ["ID"])
            ->Fetch();
    }

    /**
     * @param $brand
     * @param int $iblock
     * @return int
     */
    public static function addBrand($brand, $iblock = 10):int
    {
        $el = new \CIBlockElement;
        $arLoadBrand = Array(
            'IBLOCK_ID' => $iblock,
            'NAME' => $brand,
            'ACTIVE' => 'Y',
            'CODE' => self::translit($brand),
        );
        return $el->Add($arLoadBrand);
    }

    /**
     * @param $brand
     * @param int $brandIblock
     * @return int
     */
    public static function createBrand($brand, $brandIblock = 10):int
    {
        if ($brandCheck = self::selectBrandID($brand, $brandIblock))
        {
            $brandId = $brandCheck['ID'];
        } else
        {
            $brandId = self::addBrand($brand, $brandIblock);
        }
        return $brandId;
    }
}
