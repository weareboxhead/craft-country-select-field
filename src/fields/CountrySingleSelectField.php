<?php

/**
 * Country Select plugin for Craft CMS 3.x
 *
 * Country select field type.
 *
 * @link      https://github.com/weareboxhead
 * @copyright Copyright (c) 2023 Boxhead
 */

namespace boxhead\countryselect\fields;

use Craft;
use craft\base\ElementInterface;

/**
 * @author    Boxhead
 * @package   CountrySelectField
 * @since     1.0.0
 */
class CountrySingleSelectField extends CountrySelectBaseOptionsField
{
    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('country-select-field', 'Country Select');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getInputHtml(mixed $value, ?ElementInterface $element = null): string
    {
        $name = $this->handle;
        $options = $this->translatedOptions();

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($name);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'country-select-field/_select',
            [
                'name' => $name,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
                'options' => $options,
            ]
        );
    }
}
