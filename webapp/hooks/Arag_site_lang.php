<?php defined('SYSPATH') or die('No direct script access.');

/**
 * @copyright  (c) 2007, Geert De Deckere
 * @see        http://kohanaphp.com/tutorials/multilingual.html
 */

// This hook sets the locale.language and locale.lang config values
// based on the language found in the first segment of the URL.

Event::add('system.routing', 'site_lang');

function site_lang()
{
        // Array of allowed languages
        $locales = Config::item('locale.allowed_locales');

        $lang = (preg_match('|^[a-zA-Z]{2}/|', url::current() . '/')) ? strtolower(substr(url::current(), 0, 2)) : 'xx';

        // Invalid language is given in the URL
        if ( ! array_key_exists($lang, $locales))
        {
            // Look for default alternatives and store them in order
            // of importance in the $new_langs array:
            //  1. cookie
            //  2. http_accept_language header
            //  3. default lang

            // Look for cookie
            $new_langs[] = (string) cookie::get('lang');

            // Look for HTTP_ACCEPT_LANGUAGE
            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
            {
                foreach(explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $part)
                {
                    $new_langs[] = substr($part, 0, 2);
                }
            }

            // Lowest priority goes to default language
            $new_langs[] = Config::item('locale.lang');

            // Now loop through the new languages and pick out the first valid one
            foreach(array_unique($new_langs) as $new_lang)
            {
                $new_lang = strtolower($new_lang);

                if (array_key_exists($new_lang, $locales))
                {
                    $lang = $new_lang;
                    break;
                }
            }

            // Remove language from url
            $uri = preg_replace('|^[a-zA-Z]{2}/|', '', url::current());
            url::redirect($lang.'/'.$uri);            
        }

        // Store locale config values
        Config::set('locale.lang', $lang);
        Config::set('locale.language', $locales[$lang]);

        // Overwrite setlocale which has already been set before in Kohana::setup()
        setlocale(LC_ALL, $locales[$lang].'.utf8');
        putenv('LANG=' . $locales[$lang]);
        putenv('LANGUAGE=' . $locales[$lang]);

        // Finally set a language cookie for 6 months
        cookie::set('lang', $lang, 15768000);
}
