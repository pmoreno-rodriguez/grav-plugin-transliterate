<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use Grav\Plugin\Twig\TransliterateTwigFilter;

/**
 * Class TransliteratePlugin
 * @package Grav\Plugin
 */
class TransliteratePlugin extends Plugin
{
    /**
     * The getSubscribedEvents() method returns a list of events
     * the plugin is interested in. The key represents the event,
     * and the value is an array containing the callable function and its priority.
     * The higher the number, the higher the priority.
     *
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onPluginsInitialized' => [
                // Uncomment the following line when the plugin is for Grav < 1.7
                // ['autoload', 100000],
                ['onPluginsInitialized', 0]
            ]
        ];
    }

    /**
     * Initializes the plugin by enabling necessary events.
     * It ensures the plugin does not proceed if we're in the admin area.
     */
    public function onPluginsInitialized()
    {
        // Don't proceed if we are in the admin plugin
        if ($this->isAdmin()) {
            return;
        }

        // Enable the main events the plugin is interested in
        $this->enable([
            'onTwigExtensions' => ['onTwigExtensions', 1000],
        ]);
    }

    /**
     * Registers the Twig filters for transliteration.
     * Adds TransliterateTwigFilter to the Twig environment.
     */
    public function onTwigExtensions()
    {
        // Make sure to include the necessary file for the Twig filter class
        require_once(__DIR__ . '/classes/TransliterateTwigFilter.php');
        
        // Register the TransliterateTwigFilter as an extension in Twig
        $this->grav['twig']->twig->addExtension(new TransliterateTwigFilter($this->grav));
    }
}
