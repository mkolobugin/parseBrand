<?php
namespace ParseBrand;
/**
 * Class Element
 * Класс принимает ссылку на элемент и парсит его страницу
 * Возвращает массив получившихся элементов
 */
class Element implements ElementInterface{
    /**
     * @var string
     */
    private string $projectURL = 'https://eco-dush.ru';
    /**
     * @var string
     */
    private string $elementLink;
    /**
     * @var object|false|file_get_html
     */
    private object $html;
    /**
     * @var array|string[]
     */
    private array $tableList = ['articul', 'title', 'qty', 'price1'];

    /**
     * @var array
     */
    private array $tableValues;

    /**
     * @var string
     */
    private string $imgUrl;

    /**
     * @var array
     */
    public array $elementProps;

    /**
     * @param $elementLink
     * @return object
     */
    public function url($elementLink): object
    {
        $this->elementLink = $elementLink;
        $this->html = file_get_html($this->elementLink);
        return $this;
    }

    /**
     * @param string $tag
     * @return object
     * Получаем таблицу по указанному тегу
     * Собираем значения указанные в свойстве $tableList
     * Значения записываем в свойства, и возвращаем сам объект
     */
    public function parseTable(string $tag) : object
    {
        $arTable = [];
        foreach ($this->tableList as $tableElement)
        {
            $column = $this->html->find($tag . " ." . $tableElement);
            foreach ($column as $key => $line)
            {
                if (!$key) continue;
                if ($tableElement == 'articul')
                {
                    $arTable[$key][$tableElement] = substr($line->innertext, 0, strpos(trim($line->innertext), '<span class="table-s1-td-hover">'));
                    $arTable[$key][$tableElement] = str_replace('<span class="table-s1-td-hover-wrap">', '', $arTable[$key][$tableElement]);
                    continue;
                }
                $arTable[$key][$tableElement] = trim($line->plaintext);
            }
        }
        $this->tableValues = $arTable;
        return $this;
    }

    /**
     * @param string $tag
     * @return object
     * Получаем URL картинки по ее классу в HTML разметке
     * Возвращаем сам объект
     */
    public function parseImg(string $tag): object
    {
        $elementImage = '';
        $obImage = $this->html->find($tag . " img");
        foreach ($obImage as $img)
        {
            $elementImage = $img->src;
        }
        $this->imgUrl = $this->projectURL . $elementImage;
        return $this;
    }

    /**
     * @param string $propName
     * @param string $propValue
     * @return object
     * На входе две строки
     * В первой указываем теги, по которым можно собрать названия свойств
     * Во второй теги для значений этих свойств
     * Возвращаем сам объект
     */
    public function parseProperties(string $propName, string $propValue): object
    {
        $arPropertiesNames = [];
        $arPropertiesValues = [];
        $obProperties = $this->html->find($propName);
        foreach ($obProperties as $property)
        {
            if (strpos(trim($property->innertext), '<p>') !== false) continue;
            $arPropertiesNames[] = trim($property->innertext);
        }
        $obProperties = $this->html->find($propValue);
        foreach ($obProperties as $property)
        {
            $arPropertiesValues[] = trim($property->plaintext);
        }
        $arProperties = array_combine($arPropertiesNames, $arPropertiesValues);
        $this->elementProps = $arProperties;
        return $this;
    }

    /**
     * @return array
     * Вызываем остальные методы и собираем отдельные элементы по полученным данным
     * Возвращаем массив с готовыми данными для товаров
     */
    public function getElementInfo(): array
    {
        $arElements = [];
        foreach ($this->tableValues as $item)
        {
            $arElements[$item['articul']] = $item;
            $arElements[$item['articul']]["IMAGE"] = $this->imgUrl;
            $arElements[$item['articul']]["PROPERTIES"] = $this->elementProps;
        }
        return $arElements;
    }
}