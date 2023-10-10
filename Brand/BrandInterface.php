<?php
namespace ParseBrand;

interface BrandInterface
{
    public function url(string $brandLink): object;

    public function collection(string $brandLink): object;

    public function elements(array $arCollection): object;

    public function getElements(): array;

}

?>