<?php
namespace ParseBrand;

interface ElementInterface
{
    public function url(string $elementLink): object;

    public function parseTable(string $tag): object;

    public function parseImg(string $tag): object;

    public function parseProperties(string $propName, string $propValue): object;

    public function getElementInfo(): array;

}

?>