<?php namespace OnionCreative\Sitemap;

use System\Classes\PluginBase;
use OnionCreative\Sitemap\Classes\Sitemap;
use OnionCreative\Sitemap\Models\SitemapSetting;

/**
 * Sitemap Plugin Information File
 */
class Plugin extends PluginBase
{

    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'Sitemap',
            'description' => 'Manage sitemap.xml',
            'author'      => 'Onion Creative',
            'icon'        => 'icon-edit'
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Sitemap Settings',
                'description' => 'Manage sitemap settings.',
                'icon'        => 'icon-cog',
                'class'       => SitemapSetting::class,
                'order'       => 500,
                'permissions' => ['onioncreative.sitemap.access_settings']
            ]
        ];
    }

    public function registerPermissions()
    {
        return [
            'onioncreative.sitemap.access_settings' => [
                'tab' => 'Sitemap',
                'label' => 'Access sitemap settings'
            ],
        ];
    }

    public function registerSchedule($schedule)
    {
        // generate sitemap daily at midnight 00:00 UTC+0
        $schedule->call(function () {
            app(Sitemap::class)->generateSitemap();
        })->dailyAt('00:00');
    }
}
