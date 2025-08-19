<?php

/**
 * Country Select plugin for Craft CMS
 *
 * Country select field type.
 *
 * @link      https://github.com/weareboxhead
 * @copyright Copyright (c) 2023 Boxhead
 */

namespace boxhead\countryselect\fields;

use Craft;
use craft\fields\Dropdown;
use boxhead\countryselect\CountrySelectField;

/**
 * @author    Boxhead
 * @package   CountrySelectField
 * @since     1.0.0
 */
class CountrySingleSelectField extends Dropdown
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
    public function init(): void
    {
        parent::init();

        $this->setCountries();
    }

    public function getSettingsHtml(): null|string
    {
        return null;
    }

    // Protected Methods
    // =========================================================================

    protected function optionsSettingLabel(): string
    {
        return Craft::t('country-select-field', 'Options');
    }

    /**
     * @return array
    */
    protected function setCountries(): void
    {
        // Access countries service class to get options
        $countries = CountrySelectField::getInstance()->countries->getCountries();

        $this->options = [
            [
                'label' => 'Choose a country',
                'value' => '',
                'disabled' => true,
            ]
        ];

        $this->options = array_merge($this->options, $countries);
    }
}
