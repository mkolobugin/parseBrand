<?
namespace ParseBrand;

/**
 * Class Brand
 * Входные данные:
 *  Ссылка на бренд и его название
 * Возвращает:
 *  массив элементов бренда
 */
class Brand implements BrandInterface{

    /**
     * @var string
     */
    private string $projectURL = 'https://eco-dush.ru';

    /**
     * @var string
     */
    private string $brandLink;

    /**
     * @var array
     */
    private array $collection;

    /**
     * @var array
     */
    private array $elements;

    /**
     * @param $brandLink
     * @return object
     */
    public function url($brandLink): Brand
    {
        $this->brandLink = $brandLink;
        return $this;
    }

    /**
     * @param string $brandLink
     * @return object
     * Получаем все коллекции бренда
     */
    public function collection (string $brandLink): object
    {
        $arrResult = [];
        $html = file_get_html($brandLink);
        $DOMLinks = $html->find('.sec-cols a');
        foreach($DOMLinks as $k => $element) {
            $arrResult[$k]['LINK'] = $this->projectURL . $element->href;
            $arrResult[$k]['COLLECTION'] = trim($element->plaintext);
        }
        $this->collection = $arrResult;
        return $this;
    }

    /**
     * @param array $arCollection
     * @return object
     * Получаем все элементы коллекции
     */
    public function elements (array $arCollection): object
    {
        $arrResult = [];
        foreach ($arCollection as $collection) {
            $html = file_get_html($collection['LINK']);
            $DOMLinks = $html->find('.product-card a');
            foreach($DOMLinks as $k => $element) {
                $arrResult[$collection["COLLECTION"]][] = $this->projectURL . $element->href;
            }
        }
        $this->elements = $arrResult;
        return $this;
    }

    /**
     * @return array
     * Возвращаем все элементы коллекции
     */
    public function getElements(): array
    {
        $this->collection($this->brandLink)->elements($this->collection);
        return $this->elements;
    }

}

?>