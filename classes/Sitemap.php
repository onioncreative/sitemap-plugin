<?php namespace OnionCreative\Sitemap\Classes;

use File;
use DOMDocument;
use Cms\Classes\Page;
use RainLab\Translate\Models\Locale;
use RainLab\Translate\Classes\Translator;
use OnionCreative\Sitemap\Models\SitemapSetting;

class Sitemap
{
    /**
     * @var DOMDocument
     */
    protected $xmlObject;

    /**
     * @var DOMDocument element
     */
    protected $urlSet;

    /**
     * @var array All enabled locales
     */
    protected $locales;

    public function __construct()
    {
        date_default_timezone_set('Asia/Hong_Kong');

        if (class_exists(Locale::class)) {
            $this->locales = array_keys(Locale::listEnabled());
        }
        else {
            $this->locales = [app()->getLocale()];
        }
    }

    public function generateSitemap()
    {
        $this->prepareUrls();

        $urlSet = $this->makeUrlSet();
        $xml    = $this->makeXmlObject();

        $xml->appendChild($urlSet);
        $this->saveSitemap($xml, base_path('sitemap-vms-pages.xml'));
    }

    protected function prepareUrls()
    {
        $this->makeCmsPagesUrls();
    }

    protected function makeCmsPagesUrls()
    {
        $sitemap = SitemapSetting::get('sitemap');

        if (!$sitemap || !is_iterable($sitemap)) {
            return;
        }

        foreach ($sitemap as $item) {
            $url               = Page::url($item['page']);
            $last_modify       = date('c', strtotime($item['last_modify']));
            $change_frenquency = $item['change_frenquency'];
            $priority          = $item['priority'];

            foreach ($this->locales as $locale) {
                $this->addItemToSet(
                    $this->localeUrl($url, $locale), 
                    $last_modify, 
                    $change_frenquency, 
                    $priority
                );
            }
        }
    }

    protected function saveSitemap($xml, $filename)
    {
        File::put($filename, $xml->saveXML());
    }

    protected function makeUrlSet()
    {
        if ($this->urlSet !== null) {
            return $this->urlSet;
        }

        $xml    = $this->makeXmlObject();
        $urlSet = $xml->createElement('urlset');
        $urlSet->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $urlSet->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $urlSet->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

        return $this->urlSet = $urlSet;
    }

    protected function makeXmlObject()
    {
        if ($this->xmlObject !== null) {
            return $this->xmlObject;
        }

        $xml = new DOMDocument;
        $xml->encoding = 'UTF-8';

        return $this->xmlObject = $xml;
    }

    public function addItemToSet(
        $url, 
        $last_modify, 
        $change_frequency, 
        $priority
    ) {
        $xml        = $this->makeXmlObject();
        $urlSet     = $this->makeUrlSet();
        $urlElement = $this->makeUrlElement(
            $xml,
            $url,
            $last_modify,
            $change_frequency,
            $priority
        );

        if ($urlElement) {
            $urlSet->appendChild($urlElement);
        }

        return $urlSet;
    }

    protected function makeUrlElement(
        $xml, 
        $pageUrl, 
        $lastModified, 
        $frequency, 
        $priority
    ) {
        $url = $xml->createElement('url');
        $url->appendChild($xml->createElement('loc', $pageUrl));
        $url->appendChild($xml->createElement('lastmod', $lastModified));
        $url->appendChild($xml->createElement('changefreq', $frequency));
        $url->appendChild($xml->createElement('priority', $priority));

        return $url;
    }

    public function localeUrl($url, $locale)
    {
        if (class_exists(Translator::class)) {
            $translator = Translator::instance();
            $parts      = parse_url($url);
            $path       = array_get($parts, 'path');

            return http_build_url($parts, [
                'path' => '/' . $translator->getPathInLocale($path, $locale)
            ]);
        }

        return $url;
    }

    public function slugify($string, $separator = '-') 
    {
        $slug = mb_strtolower(
            preg_replace('/([?]|\p{P}|\s)+/u', $separator, str_replace('&', 'and', $string))
        );

        return trim($slug, $separator);
    }
}
