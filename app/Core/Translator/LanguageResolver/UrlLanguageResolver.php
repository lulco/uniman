<?php

namespace Adminerng\Core\Translator\LanguageResolver;

use Nette\Http\Url;

class UrlLanguageResolver implements LanguageResolverInterface
{
    /** @var Url */
    private $url;

    private $queryParam;

    public function __construct($url, $queryParam = 'locale')
    {
        $this->url = new Url($url);
        $this->queryParam = $queryParam;
    }

    /**
     * @return string|null
     */
    public function resolve()
    {
        return $this->url->getQueryParameter($this->queryParam, null);
    }
}
