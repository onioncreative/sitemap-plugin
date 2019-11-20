<?php namespace OnionCreative\Sitemap\Models;

use Model;
use Cms\Classes\Page;

class SitemapSetting extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'onioncreative_sitemap_settings';

    public $settingsFields = 'fields.yaml';

    public function getPagesOptions()
    {
        $pages = Page::sortBy('baseFileName')
            ->lists('title', 'baseFileName');

        foreach ($pages as $key => $page_title) {
            $pages[$key] = 'CMS page: '.$page_title;
        }

        return $pages;
    }

    public function getChangeFrequencyOptions()
    {
        // https://www.sitemaps.org/protocol.html#changefreqdef
        return [
            'always'  => 'Always',
            'hourly'  => 'Hourly',
            'daily'   => 'Daily',
            'weekly'  => 'Weekly',
            'monthly' => 'Monthly',
            'yearly'  => 'Yearly',
            'never'   => 'Never',
        ];
    }

    public function getPriorityOptions()
    {
        // https://www.sitemaps.org/protocol.html#prioritydef
        return [
            '0.1' => '0.1',
            '0.2' => '0.2',
            '0.3' => '0.3',
            '0.4' => '0.4',
            '0.5' => '0.5',
            '0.6' => '0.6',
            '0.7' => '0.7',
            '0.8' => '0.8',
            '0.9' => '0.9',
            '1.0' => '1.0',
        ];
    }
}
