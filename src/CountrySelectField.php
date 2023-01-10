<?php

/**
 * Country Select plugin for Craft CMS 3.x
 *
 * Country select field type.
 *
 * @link      https://github.com/weareboxhead
 * @copyright Copyright (c) 2023 Boxhead
 */

namespace boxhead\countryselect;

use Craft;
use craft\base\Plugin;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;
use boxhead\countryselect\fields\CountrySingleSelectField as CountrySingleSelectField;
use boxhead\countryselect\fields\CountryMultiSelectField as CountryMultiSelectField;
use yii\base\Event;

/**
 * Class CountrySelect
 *
 * @author    Boxhead
 * @package   CountrySelectField
 * @since     1.0.0
 *
 */
class CountrySelectField extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var CountrySelectField
     */
    public static CountrySelectField $plugin;

    // Public Properties 
    // =========================================================================

    /**
     * @inheritdoc
     */
    public bool $hasCpSettings = false;

    /**
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = CountrySingleSelectField::class;
                $event->types[] = CountryMultiSelectField::class;
            }
        );

        Craft::info(
            Craft::t(
                'country-select-field',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================
}
