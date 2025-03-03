<?php
namespace Grav\Plugin\Twig;

use Grav\Common\Grav;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
* Class TransliteratePlugin
* @category   Extensions
* @package    Grav\Plugin
* @subpackage TransliterateTwigFilter
* @author     Pedro Moreno <https://github.com/pmoreno-rodriguez>
* @license    http://www.opensource.org/licenses/mit-license.html MIT License
* @link       https://github.com/pmoreno-rodriguez/grav-plugin-transliterate
*/

class TransliterateTwigFilter extends AbstractExtension
{
    // Cache for transliterations
    private $transliterationCache = [];

    /**
     * Return the list of Twig filters provided by this extension.
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('transliterate', [$this, 'transliterate']),
            new TwigFilter('to_ascii', [$this, 'toAscii']),
        ];
    }

    /**
     * Transliterate text using Transliterator (intl).
     *
     * @param string $string The input string to transliterate.
     * @param string $rules The transliteration rules (default: 'Any-Latin; Latin-ASCII').
     * @return string The transliterated string.
     * @throws \Exception If transliteration fails.
     */
    public function transliterate(string $string, ?string $rules = null): string
    {
        // Use the cache if the text has already been transliterated
        $cacheKey = md5($string . $rules);
        if (isset($this->transliterationCache[$cacheKey])) {
            return $this->transliterationCache[$cacheKey];
        }

        // Process transliteration
        $result = $this->processTransliteration($string, $rules);

        // Store in cache
        $this->transliterationCache[$cacheKey] = $result;
        return $result;
    }

    /**
     * Process the transliteration logic.
     *
     * @param string $string The input string to transliterate.
     * @param string $rules The transliteration rules.
     * @return string The processed transliterated string.
     */
    private function processTransliteration(string $string, ?string $rules): string
    {
        $grav = Grav::instance();
        if ($rules === null) {
            // Get custom transliteration rules from configuration if available
            $rules = $grav['config']->get('plugins.transliterate.custom_rules', 'Any-Latin; Latin-ASCII');
        }

        if (class_exists('\Transliterator')) {
            try {
                // Use the Transliterator class to transliterate the string
                $transliterator = \Transliterator::create($rules);
                if ($transliterator) {
                    $result = $transliterator->transliterate($string);
                    if ($result !== false) {
                        return $result;
                    }
                }
            } catch (\Exception $e) {
                // Log any exceptions that occur during transliteration
                error_log("Transliteration failed: " . $e->getMessage());
            }
        }

        // Fallback transliteration if transliterator or rules fail
        return $this->transliterateFallback($string);
    }

    /**
     * Fallback transliteration using iconv.
     *
     * @param string $string The input string to transliterate.
     * @return string The transliterated string or empty string if failure.
     */
    private function transliterateFallback(string $string): string
    {
        if (function_exists('iconv')) {
            // Use iconv for transliteration if available
            $string = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string);
            return $string ?: '';
        }

        // If iconv is not available, use manual conversion with mb_convert_encoding
        return strtr(
            mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8'),
            mb_convert_encoding(
                'ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ',
                'ISO-8859-1', 'UTF-8'
            ),
            'SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy'
        );
    }

    /**
     * Convert to ASCII and remove non-ASCII characters.
     *
     * @param string $string The input string to convert.
     * @return string The resulting ASCII string.
     */
    public function toAscii(string $string): string
    {
        $grav = Grav::instance();
        // First transliterate the string
        $string = $this->transliterate($string);

        // Get allowed characters from configuration
        $allowedChars = $grav['config']->get('plugins.transliterate.allowed_chars', 'A-Za-z0-9 \-_');

        // Remove characters that are not allowed
        return preg_replace('/[^' . $allowedChars . ']/', '', $string);
    }
}
