# Transliterate Plugin

The **Transliterate** Plugin is an extension for [Grav CMS](https://github.com/getgrav/grav). It adds Twig filters for transliterating and converting text to ASCII, making it easier to handle special characters, diacritics, and non-Latin scripts in your Grav templates.

## Installation

Installing the Transliterate plugin can be done in one of three ways: The GPM (Grav Package Manager) installation method lets you quickly install the plugin with a simple terminal command, the manual method lets you do so via a zip file, and the admin method lets you do so via the Admin Plugin.

### GPM Installation (Preferred)

To install the plugin via the [GPM](https://learn.getgrav.org/cli-console/grav-cli-gpm), through your system's terminal (also called the command line), navigate to the root of your Grav-installation, and enter:

    bin/gpm install transliterate

This will install the Transliterate plugin into your `/user/plugins`-directory within Grav. Its files can be found under `/your/site/grav/user/plugins/transliterate`.

### Manual Installation

To install the plugin manually, download the zip-version of this repository and unzip it under `/your/site/grav/user/plugins`. Then rename the folder to `transliterate`. You can find these files on [GitHub](https://github.com/pmoreno-rodriguez/grav-plugin-transliterate) or via [GetGrav.org](https://getgrav.org/downloads/plugins).

You should now have all the plugin files under:

    /your/site/grav/user/plugins/transliterate

> NOTE: This plugin is a modular component for Grav which may require other plugins to operate, please see its [blueprints.yaml-file on GitHub](https://github.com/pmoreno-rodriguez/grav-plugin-transliterate/blob/develop/blueprints.yaml).

### Admin Plugin

If you use the Admin Plugin, you can install the plugin directly by browsing the `Plugins`-menu and clicking on the `Add` button.

## Configuration

Before configuring this plugin, you should copy the `user/plugins/transliterate/transliterate.yaml` to `user/config/plugins/transliterate.yaml` and only edit that copy.

Here is the default configuration and an explanation of available options:

```yaml
enabled: true
```

Note that if you use the Admin Plugin, a file with your configuration named `transliterate.yaml` will be saved in the `user/config/plugins/`-folder once the configuration is saved in the Admin.

## Usage

The Transliterate plugin provides two Twig filters for handling text:

1. **`transliterate`**: Converts text with special characters, diacritics, or non-Latin scripts into a Latin-based equivalent.
2. **`to_ascii`**: Converts text to ASCII, removing all non-ASCII characters (including symbols and spaces).

### Examples

#### Basic Transliteration
```twig
{{ 'Café en París' | transliterate }}
```

**Output:**
```
Cafe en Paris
```

#### ASCII Conversion
```twig
{{ 'Café en París' | to_ascii }}
```

**Output:**
```
Cafe en Paris
```

#### Using Custom Transliteration Rules
You can specify custom transliteration rules for more control over the output. For example:

```twig
{{ 'Café en París' | transliterate('Any-Latin; Latin-ASCII; [\\u0100-\\u017F] remove') }}
```

**Output:**
```
Cafe en Paris
```

#### Allowed Characters Configuration
You can configure which characters are allowed in the output. For example, to allow commas and periods:

```twig
{{ 'Café, en París.' | to_ascii }}
```
**Output:**
```
Cafe, en Paris.
```

#### Combining Filters
You can chain the filters for more advanced transformations:

```twig
{{ 'Café en París' | transliterate | to_ascii }}
```

**Output:**
```
Cafe en Paris
```

## Credits

This plugin uses the following third-party libraries and tools:
- **PHP's `intl` extension**: For advanced transliteration using the `Transliterator` class.
- **`iconv`**: As a fallback for transliteration when the `intl` extension is not available.

Special thanks to the Grav CMS team for providing a robust and extensible platform.

Feel free to contribute to this plugin by submitting issues or pull requests on [GitHub](https://github.com/pmoreno-rodriguez/grav-plugin-transliterate/issues). Your feedback and contributions are welcome!

### Notes
- Ensure the `intl` extension is enabled on your server for the best results.
- Test the plugin with texts in various languages and special characters to ensure compatibility.