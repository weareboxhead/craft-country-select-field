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
use boxhead\countryselect\fields\CountrySelectField as CountrySelectField;
use boxhead\countryselect\fields\CountrySelectMultiField as CountrySelectMultiField;
use yii\base\Event;

/**
 * Class CountrySelect
 *
 * @author    Boxhead
 * @package   CountrySelect
 * @since     1.0.0
 *
 */
class CountrySelect extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var CountrySelect
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = CountrySelectField::class;
                $event->types[] = CountrySelectMultiField::class;
            }
        );

        Craft::info(
            Craft::t(
                'country-select',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================
}
