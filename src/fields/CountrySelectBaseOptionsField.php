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
use craft\fields\BaseOptionsField;
use craft\fields\data\OptionData;
use craft\fields\data\MultiOptionsFieldData;
use craft\fields\data\SingleOptionFieldData;
use craft\helpers\Db;
use craft\helpers\Json;
use yii\db\Schema;

/**
 * @author    Boxhead
 * @package   CountrySelectField
 * @since     1.0.0
 */
class CountrySelectBaseOptionsField extends BaseOptionsField
{
    // Properties
    // =========================================================================

    /**
     * @var array|null The available options
     */
    public array $options = [];

    /**
     * @var bool Whether the field should support multiple selections
     */
    protected bool $multi = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $this->options = $this->translatedOptions();
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        if ($this->multi) {
            // See how much data we could possibly be saving if everything was selected.
            $length = 0;

            if ($this->options) {
                foreach ($this->options as $option) {
                    if (!empty($option['value'])) {
                        // +3 because it will be json encoded. Includes the surrounding quotes and comma.
                        $length += strlen($option['value']) + 3;
                    }
                }
            }

            // Add +2 for the outer brackets and -1 for the last comma.
            return Db::getTextualColumnTypeByContentLength($length + 1);
        }

        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null): mixed
    {
        if ($value instanceof MultiOptionsFieldData || $value instanceof SingleOptionFieldData) {
            return $value;
        }

        if (is_string($value)) {
            $value = Json::decodeIfJson($value);
        }

        // Normalize to an array
        $selectedValues = (array) $value;

        if ($this->multi) {
            // Convert the value to a MultiOptionsFieldData object
            $options = [];
            foreach ($selectedValues as $val) {
                $label = $this->optionLabel($val);
                $options[] = new OptionData($label, $val, true);
            }
            $value = new MultiOptionsFieldData($options);
        } else {
            // Convert the value to a SingleOptionFieldData object
            $value = reset($selectedValues) ?: null;
            $label = $this->optionLabel($value);
            $value = new SingleOptionFieldData($label, $value, true);
        }

        $options = [];

        if ($this->options) {
            foreach ($this->options as $option) {
                $selected = in_array($option['value'], $selectedValues, true);
                $options[] = new OptionData($option['label'], $option['value'], $selected);
            }
        }

        $value->setOptions($options);

        return $value;
    }

    // Protected Methods
    // =========================================================================

    protected function optionsSettingLabel(): string
    {
        return Craft::t('country-select-field', 'Options');
    }

    protected function defaultValue(): array|string|null
    {
        $options = $this->translatedOptions();

        return $options[0]['value'];
    }

    /**
     * Returns an option's label by its value.
     *
     * @param string|null $value
     * @return string|null
     */
    protected function optionLabel(string $value = null): string
    {
        if ($this->options) {
            foreach ($this->options as $option) {
                if ($option['value'] == $value) {
                    return $option['label'];
                }
            }
        }

        return $value;
    }

    /**
     * @return array
    */
    protected function translatedOptions(bool $encode = false, mixed $value = null, ?ElementInterface $element = null): array
    {
        $countries = [
            ['value' => 'AD', 'label' => Craft::t('country-select-field', 'Andorra')],
            ['value' => 'AE', 'label' => Craft::t('country-select-field', 'United Arab Emirates')],
            ['value' => 'AF', 'label' => Craft::t('country-select-field', 'Afghanistan')],
            ['value' => 'AG', 'label' => Craft::t('country-select-field', 'Antigua and Barbuda')],
            ['value' => 'AI', 'label' => Craft::t('country-select-field', 'Anguilla')],
            ['value' => 'AL', 'label' => Craft::t('country-select-field', 'Albania')],
            ['value' => 'AM', 'label' => Craft::t('country-select-field', 'Armenia')],
            ['value' => 'AO', 'label' => Craft::t('country-select-field', 'Angola')],
            ['value' => 'AP', 'label' => Craft::t('country-select-field', 'Asia/Pacific Region')],
            ['value' => 'AQ', 'label' => Craft::t('country-select-field', 'Antarctica')],
            ['value' => 'AR', 'label' => Craft::t('country-select-field', 'Argentina')],
            ['value' => 'AS', 'label' => Craft::t('country-select-field', 'American Samoa')],
            ['value' => 'AT', 'label' => Craft::t('country-select-field', 'Austria')],
            ['value' => 'AU', 'label' => Craft::t('country-select-field', 'Australia')],
            ['value' => 'AW', 'label' => Craft::t('country-select-field', 'Aruba')],
            ['value' => 'AX', 'label' => Craft::t('country-select-field', 'Aland Islands')],
            ['value' => 'AZ', 'label' => Craft::t('country-select-field', 'Azerbaijan')],
            ['value' => 'BA', 'label' => Craft::t('country-select-field', 'Bosnia and Herzegovina')],
            ['value' => 'BB', 'label' => Craft::t('country-select-field', 'Barbados')],
            ['value' => 'BD', 'label' => Craft::t('country-select-field', 'Bangladesh')],
            ['value' => 'BE', 'label' => Craft::t('country-select-field', 'Belgium')],
            ['value' => 'BF', 'label' => Craft::t('country-select-field', 'Burkina Faso')],
            ['value' => 'BG', 'label' => Craft::t('country-select-field', 'Bulgaria')],
            ['value' => 'BH', 'label' => Craft::t('country-select-field', 'Bahrain')],
            ['value' => 'BI', 'label' => Craft::t('country-select-field', 'Burundi')],
            ['value' => 'BJ', 'label' => Craft::t('country-select-field', 'Benin')],
            ['value' => 'BL', 'label' => Craft::t('country-select-field', 'Saint Bartelemey')],
            ['value' => 'BM', 'label' => Craft::t('country-select-field', 'Bermuda')],
            ['value' => 'BN', 'label' => Craft::t('country-select-field', 'Brunei Darussalam')],
            ['value' => 'BO', 'label' => Craft::t('country-select-field', 'Bolivia')],
            ['value' => 'BQ', 'label' => Craft::t('country-select-field', 'Bonaire, Saint Eustatius and Saba')],
            ['value' => 'BR', 'label' => Craft::t('country-select-field', 'Brazil')],
            ['value' => 'BS', 'label' => Craft::t('country-select-field', 'Bahamas')],
            ['value' => 'BT', 'label' => Craft::t('country-select-field', 'Bhutan')],
            ['value' => 'BV', 'label' => Craft::t('country-select-field', 'Bouvet Island')],
            ['value' => 'BW', 'label' => Craft::t('country-select-field', 'Botswana')],
            ['value' => 'BY', 'label' => Craft::t('country-select-field', 'Belarus')],
            ['value' => 'BZ', 'label' => Craft::t('country-select-field', 'Belize')],
            ['value' => 'CA', 'label' => Craft::t('country-select-field', 'Canada')],
            ['value' => 'CC', 'label' => Craft::t('country-select-field', 'Cocos (Keeling) Islands')],
            ['value' => 'CD', 'label' => Craft::t('country-select-field', 'Congo, The Democratic Republic of the')],
            ['value' => 'CF', 'label' => Craft::t('country-select-field', 'Central African Republic')],
            ['value' => 'CG', 'label' => Craft::t('country-select-field', 'Congo')],
            ['value' => 'CH', 'label' => Craft::t('country-select-field', 'Switzerland')],
            ['value' => 'CI', 'label' => Craft::t('country-select-field', 'Cote d\'Ivoire')],
            ['value' => 'CK', 'label' => Craft::t('country-select-field', 'Cook Islands')],
            ['value' => 'CL', 'label' => Craft::t('country-select-field', 'Chile')],
            ['value' => 'CM', 'label' => Craft::t('country-select-field', 'Cameroon')],
            ['value' => 'CN', 'label' => Craft::t('country-select-field', 'China')],
            ['value' => 'CO', 'label' => Craft::t('country-select-field', 'Colombia')],
            ['value' => 'CR', 'label' => Craft::t('country-select-field', 'Costa Rica')],
            ['value' => 'CU', 'label' => Craft::t('country-select-field', 'Cuba')],
            ['value' => 'CV', 'label' => Craft::t('country-select-field', 'Cape Verde')],
            ['value' => 'CW', 'label' => Craft::t('country-select-field', 'Curacao')],
            ['value' => 'CX', 'label' => Craft::t('country-select-field', 'Christmas Island')],
            ['value' => 'CY', 'label' => Craft::t('country-select-field', 'Cyprus')],
            ['value' => 'CZ', 'label' => Craft::t('country-select-field', 'Czech Republic')],
            ['value' => 'DE', 'label' => Craft::t('country-select-field', 'Germany')],
            ['value' => 'DJ', 'label' => Craft::t('country-select-field', 'Djibouti')],
            ['value' => 'DK', 'label' => Craft::t('country-select-field', 'Denmark')],
            ['value' => 'DM', 'label' => Craft::t('country-select-field', 'Dominica')],
            ['value' => 'DO', 'label' => Craft::t('country-select-field', 'Dominican Republic')],
            ['value' => 'DZ', 'label' => Craft::t('country-select-field', 'Algeria')],
            ['value' => 'EC', 'label' => Craft::t('country-select-field', 'Ecuador')],
            ['value' => 'EE', 'label' => Craft::t('country-select-field', 'Estonia')],
            ['value' => 'EG', 'label' => Craft::t('country-select-field', 'Egypt')],
            ['value' => 'EH', 'label' => Craft::t('country-select-field', 'Western Sahara')],
            ['value' => 'ER', 'label' => Craft::t('country-select-field', 'Eritrea')],
            ['value' => 'ES', 'label' => Craft::t('country-select-field', 'Spain')],
            ['value' => 'ET', 'label' => Craft::t('country-select-field', 'Ethiopia')],
            ['value' => 'EU', 'label' => Craft::t('country-select-field', 'Europe')],
            ['value' => 'FI', 'label' => Craft::t('country-select-field', 'Finland')],
            ['value' => 'FJ', 'label' => Craft::t('country-select-field', 'Fiji')],
            ['value' => 'FK', 'label' => Craft::t('country-select-field', 'Falkland Islands (Malvinas)')],
            ['value' => 'FM', 'label' => Craft::t('country-select-field', 'Micronesia, Federated States of')],
            ['value' => 'FO', 'label' => Craft::t('country-select-field', 'Faroe Islands')],
            ['value' => 'FR', 'label' => Craft::t('country-select-field', 'France')],
            ['value' => 'GA', 'label' => Craft::t('country-select-field', 'Gabon')],
            ['value' => 'GB', 'label' => Craft::t('country-select-field', 'United Kingdom')],
            ['value' => 'GD', 'label' => Craft::t('country-select-field', 'Grenada')],
            ['value' => 'GE', 'label' => Craft::t('country-select-field', 'Georgia')],
            ['value' => 'GF', 'label' => Craft::t('country-select-field', 'French Guiana')],
            ['value' => 'GG', 'label' => Craft::t('country-select-field', 'Guernsey')],
            ['value' => 'GH', 'label' => Craft::t('country-select-field', 'Ghana')],
            ['value' => 'GI', 'label' => Craft::t('country-select-field', 'Gibraltar')],
            ['value' => 'GL', 'label' => Craft::t('country-select-field', 'Greenland')],
            ['value' => 'GM', 'label' => Craft::t('country-select-field', 'Gambia')],
            ['value' => 'GN', 'label' => Craft::t('country-select-field', 'Guinea')],
            ['value' => 'GP', 'label' => Craft::t('country-select-field', 'Guadeloupe')],
            ['value' => 'GQ', 'label' => Craft::t('country-select-field', 'Equatorial Guinea')],
            ['value' => 'GR', 'label' => Craft::t('country-select-field', 'Greece')],
            ['value' => 'GS', 'label' => Craft::t('country-select-field', 'South Georgia and the South Sandwich Islands')],
            ['value' => 'GT', 'label' => Craft::t('country-select-field', 'Guatemala')],
            ['value' => 'GU', 'label' => Craft::t('country-select-field', 'Guam')],
            ['value' => 'GW', 'label' => Craft::t('country-select-field', 'Guinea-Bissau')],
            ['value' => 'GY', 'label' => Craft::t('country-select-field', 'Guyana')],
            ['value' => 'HK', 'label' => Craft::t('country-select-field', 'Hong Kong')],
            ['value' => 'HM', 'label' => Craft::t('country-select-field', 'Heard Island and McDonald Islands')],
            ['value' => 'HN', 'label' => Craft::t('country-select-field', 'Honduras')],
            ['value' => 'HR', 'label' => Craft::t('country-select-field', 'Croatia')],
            ['value' => 'HT', 'label' => Craft::t('country-select-field', 'Haiti')],
            ['value' => 'HU', 'label' => Craft::t('country-select-field', 'Hungary')],
            ['value' => 'ID', 'label' => Craft::t('country-select-field', 'Indonesia')],
            ['value' => 'IE', 'label' => Craft::t('country-select-field', 'Ireland')],
            ['value' => 'IL', 'label' => Craft::t('country-select-field', 'Israel')],
            ['value' => 'IM', 'label' => Craft::t('country-select-field', 'Isle of Man')],
            ['value' => 'IN', 'label' => Craft::t('country-select-field', 'India')],
            ['value' => 'IO', 'label' => Craft::t('country-select-field', 'British Indian Ocean Territory')],
            ['value' => 'IQ', 'label' => Craft::t('country-select-field', 'Iraq')],
            ['value' => 'IR', 'label' => Craft::t('country-select-field', 'Iran, Islamic Republic of')],
            ['value' => 'IS', 'label' => Craft::t('country-select-field', 'Iceland')],
            ['value' => 'IT', 'label' => Craft::t('country-select-field', 'Italy')],
            ['value' => 'JE', 'label' => Craft::t('country-select-field', 'Jersey')],
            ['value' => 'JM', 'label' => Craft::t('country-select-field', 'Jamaica')],
            ['value' => 'JO', 'label' => Craft::t('country-select-field', 'Jordan')],
            ['value' => 'JP', 'label' => Craft::t('country-select-field', 'Japan')],
            ['value' => 'KE', 'label' => Craft::t('country-select-field', 'Kenya')],
            ['value' => 'KG', 'label' => Craft::t('country-select-field', 'Kyrgyzstan')],
            ['value' => 'KH', 'label' => Craft::t('country-select-field', 'Cambodia')],
            ['value' => 'KI', 'label' => Craft::t('country-select-field', 'Kiribati')],
            ['value' => 'KM', 'label' => Craft::t('country-select-field', 'Comoros')],
            ['value' => 'KN', 'label' => Craft::t('country-select-field', 'Saint Kitts and Nevis')],
            ['value' => 'KP', 'label' => Craft::t('country-select-field', 'Korea, Democratic People\'s Republic of')],
            ['value' => 'KR', 'label' => Craft::t('country-select-field', 'Korea, Republic of')],
            ['value' => 'KW', 'label' => Craft::t('country-select-field', 'Kuwait')],
            ['value' => 'KY', 'label' => Craft::t('country-select-field', 'Cayman Islands')],
            ['value' => 'KZ', 'label' => Craft::t('country-select-field', 'Kazakhstan')],
            ['value' => 'LA', 'label' => Craft::t('country-select-field', 'Lao People\'s Democratic Republic')],
            ['value' => 'LB', 'label' => Craft::t('country-select-field', 'Lebanon')],
            ['value' => 'LC', 'label' => Craft::t('country-select-field', 'Saint Lucia')],
            ['value' => 'LI', 'label' => Craft::t('country-select-field', 'Liechtenstein')],
            ['value' => 'LK', 'label' => Craft::t('country-select-field', 'Sri Lanka')],
            ['value' => 'LR', 'label' => Craft::t('country-select-field', 'Liberia')],
            ['value' => 'LS', 'label' => Craft::t('country-select-field', 'Lesotho')],
            ['value' => 'LT', 'label' => Craft::t('country-select-field', 'Lithuania')],
            ['value' => 'LU', 'label' => Craft::t('country-select-field', 'Luxembourg')],
            ['value' => 'LV', 'label' => Craft::t('country-select-field', 'Latvia')],
            ['value' => 'LY', 'label' => Craft::t('country-select-field', 'Libyan Arab Jamahiriya')],
            ['value' => 'MA', 'label' => Craft::t('country-select-field', 'Morocco')],
            ['value' => 'MC', 'label' => Craft::t('country-select-field', 'Monaco')],
            ['value' => 'MD', 'label' => Craft::t('country-select-field', 'Moldova, Republic of')],
            ['value' => 'ME', 'label' => Craft::t('country-select-field', 'Montenegro')],
            ['value' => 'MF', 'label' => Craft::t('country-select-field', 'Saint Martin')],
            ['value' => 'MG', 'label' => Craft::t('country-select-field', 'Madagascar')],
            ['value' => 'MH', 'label' => Craft::t('country-select-field', 'Marshall Islands')],
            ['value' => 'MK', 'label' => Craft::t('country-select-field', 'Macedonia')],
            ['value' => 'ML', 'label' => Craft::t('country-select-field', 'Mali')],
            ['value' => 'MM', 'label' => Craft::t('country-select-field', 'Myanmar')],
            ['value' => 'MN', 'label' => Craft::t('country-select-field', 'Mongolia')],
            ['value' => 'MO', 'label' => Craft::t('country-select-field', 'Macao')],
            ['value' => 'MP', 'label' => Craft::t('country-select-field', 'Northern Mariana Islands')],
            ['value' => 'MQ', 'label' => Craft::t('country-select-field', 'Martinique')],
            ['value' => 'MR', 'label' => Craft::t('country-select-field', 'Mauritania')],
            ['value' => 'MS', 'label' => Craft::t('country-select-field', 'Montserrat')],
            ['value' => 'MT', 'label' => Craft::t('country-select-field', 'Malta')],
            ['value' => 'MU', 'label' => Craft::t('country-select-field', 'Mauritius')],
            ['value' => 'MV', 'label' => Craft::t('country-select-field', 'Maldives')],
            ['value' => 'MW', 'label' => Craft::t('country-select-field', 'Malawi')],
            ['value' => 'MX', 'label' => Craft::t('country-select-field', 'Mexico')],
            ['value' => 'MY', 'label' => Craft::t('country-select-field', 'Malaysia')],
            ['value' => 'MZ', 'label' => Craft::t('country-select-field', 'Mozambique')],
            ['value' => 'NA', 'label' => Craft::t('country-select-field', 'Namibia')],
            ['value' => 'NC', 'label' => Craft::t('country-select-field', 'New Caledonia')],
            ['value' => 'NE', 'label' => Craft::t('country-select-field', 'Niger')],
            ['value' => 'NF', 'label' => Craft::t('country-select-field', 'Norfolk Island')],
            ['value' => 'NG', 'label' => Craft::t('country-select-field', 'Nigeria')],
            ['value' => 'NI', 'label' => Craft::t('country-select-field', 'Nicaragua')],
            ['value' => 'NL', 'label' => Craft::t('country-select-field', 'Netherlands')],
            ['value' => 'NO', 'label' => Craft::t('country-select-field', 'Norway')],
            ['value' => 'NP', 'label' => Craft::t('country-select-field', 'Nepal')],
            ['value' => 'NR', 'label' => Craft::t('country-select-field', 'Nauru')],
            ['value' => 'NU', 'label' => Craft::t('country-select-field', 'Niue')],
            ['value' => 'NZ', 'label' => Craft::t('country-select-field', 'New Zealand')],
            ['value' => 'OM', 'label' => Craft::t('country-select-field', 'Oman')],
            ['value' => 'PA', 'label' => Craft::t('country-select-field', 'Panama')],
            ['value' => 'PE', 'label' => Craft::t('country-select-field', 'Peru')],
            ['value' => 'PF', 'label' => Craft::t('country-select-field', 'French Polynesia')],
            ['value' => 'PG', 'label' => Craft::t('country-select-field', 'Papua New Guinea')],
            ['value' => 'PH', 'label' => Craft::t('country-select-field', 'Philippines')],
            ['value' => 'PK', 'label' => Craft::t('country-select-field', 'Pakistan')],
            ['value' => 'PL', 'label' => Craft::t('country-select-field', 'Poland')],
            ['value' => 'PM', 'label' => Craft::t('country-select-field', 'Saint Pierre and Miquelon')],
            ['value' => 'PN', 'label' => Craft::t('country-select-field', 'Pitcairn')],
            ['value' => 'PR', 'label' => Craft::t('country-select-field', 'Puerto Rico')],
            ['value' => 'PS', 'label' => Craft::t('country-select-field', 'Palestinian Territory')],
            ['value' => 'PT', 'label' => Craft::t('country-select-field', 'Portugal')],
            ['value' => 'PW', 'label' => Craft::t('country-select-field', 'Palau')],
            ['value' => 'PY', 'label' => Craft::t('country-select-field', 'Paraguay')],
            ['value' => 'QA', 'label' => Craft::t('country-select-field', 'Qatar')],
            ['value' => 'RE', 'label' => Craft::t('country-select-field', 'Reunion')],
            ['value' => 'RO', 'label' => Craft::t('country-select-field', 'Romania')],
            ['value' => 'RS', 'label' => Craft::t('country-select-field', 'Serbia')],
            ['value' => 'RU', 'label' => Craft::t('country-select-field', 'Russian Federation')],
            ['value' => 'RW', 'label' => Craft::t('country-select-field', 'Rwanda')],
            ['value' => 'SA', 'label' => Craft::t('country-select-field', 'Saudi Arabia')],
            ['value' => 'SB', 'label' => Craft::t('country-select-field', 'Solomon Islands')],
            ['value' => 'SC', 'label' => Craft::t('country-select-field', 'Seychelles')],
            ['value' => 'SD', 'label' => Craft::t('country-select-field', 'Sudan')],
            ['value' => 'SE', 'label' => Craft::t('country-select-field', 'Sweden')],
            ['value' => 'SG', 'label' => Craft::t('country-select-field', 'Singapore')],
            ['value' => 'SH', 'label' => Craft::t('country-select-field', 'Saint Helena')],
            ['value' => 'SI', 'label' => Craft::t('country-select-field', 'Slovenia')],
            ['value' => 'SJ', 'label' => Craft::t('country-select-field', 'Svalbard and Jan Mayen')],
            ['value' => 'SK', 'label' => Craft::t('country-select-field', 'Slovakia')],
            ['value' => 'SL', 'label' => Craft::t('country-select-field', 'Sierra Leone')],
            ['value' => 'SM', 'label' => Craft::t('country-select-field', 'San Marino')],
            ['value' => 'SN', 'label' => Craft::t('country-select-field', 'Senegal')],
            ['value' => 'SO', 'label' => Craft::t('country-select-field', 'Somalia')],
            ['value' => 'SR', 'label' => Craft::t('country-select-field', 'Suriname')],
            ['value' => 'SS', 'label' => Craft::t('country-select-field', 'South Sudan')],
            ['value' => 'ST', 'label' => Craft::t('country-select-field', 'Sao Tome and Principe')],
            ['value' => 'SV', 'label' => Craft::t('country-select-field', 'El Salvador')],
            ['value' => 'SX', 'label' => Craft::t('country-select-field', 'Sint Maarten')],
            ['value' => 'SY', 'label' => Craft::t('country-select-field', 'Syrian Arab Republic')],
            ['value' => 'SZ', 'label' => Craft::t('country-select-field', 'Swaziland')],
            ['value' => 'TC', 'label' => Craft::t('country-select-field', 'Turks and Caicos Islands')],
            ['value' => 'TD', 'label' => Craft::t('country-select-field', 'Chad')],
            ['value' => 'TF', 'label' => Craft::t('country-select-field', 'French Southern Territories')],
            ['value' => 'TG', 'label' => Craft::t('country-select-field', 'Togo')],
            ['value' => 'TH', 'label' => Craft::t('country-select-field', 'Thailand')],
            ['value' => 'TJ', 'label' => Craft::t('country-select-field', 'Tajikistan')],
            ['value' => 'TK', 'label' => Craft::t('country-select-field', 'Tokelau')],
            ['value' => 'TL', 'label' => Craft::t('country-select-field', 'Timor-Leste')],
            ['value' => 'TM', 'label' => Craft::t('country-select-field', 'Turkmenistan')],
            ['value' => 'TN', 'label' => Craft::t('country-select-field', 'Tunisia')],
            ['value' => 'TO', 'label' => Craft::t('country-select-field', 'Tonga')],
            ['value' => 'TR', 'label' => Craft::t('country-select-field', 'Turkey')],
            ['value' => 'TT', 'label' => Craft::t('country-select-field', 'Trinidad and Tobago')],
            ['value' => 'TV', 'label' => Craft::t('country-select-field', 'Tuvalu')],
            ['value' => 'TW', 'label' => Craft::t('country-select-field', 'Taiwan')],
            ['value' => 'TZ', 'label' => Craft::t('country-select-field', 'Tanzania, United Republic of')],
            ['value' => 'UA', 'label' => Craft::t('country-select-field', 'Ukraine')],
            ['value' => 'UG', 'label' => Craft::t('country-select-field', 'Uganda')],
            ['value' => 'UM', 'label' => Craft::t('country-select-field', 'United States Minor Outlying Islands')],
            ['value' => 'US', 'label' => Craft::t('country-select-field', 'United States')],
            ['value' => 'UY', 'label' => Craft::t('country-select-field', 'Uruguay')],
            ['value' => 'UZ', 'label' => Craft::t('country-select-field', 'Uzbekistan')],
            ['value' => 'VA', 'label' => Craft::t('country-select-field', 'Holy See (Vatican City State)')],
            ['value' => 'VC', 'label' => Craft::t('country-select-field', 'Saint Vincent and the Grenadines')],
            ['value' => 'VE', 'label' => Craft::t('country-select-field', 'Venezuela')],
            ['value' => 'VG', 'label' => Craft::t('country-select-field', 'Virgin Islands, British')],
            ['value' => 'VI', 'label' => Craft::t('country-select-field', 'Virgin Islands, U.S.')],
            ['value' => 'VN', 'label' => Craft::t('country-select-field', 'Vietnam')],
            ['value' => 'VU', 'label' => Craft::t('country-select-field', 'Vanuatu')],
            ['value' => 'WF', 'label' => Craft::t('country-select-field', 'Wallis and Futuna')],
            ['value' => 'WS', 'label' => Craft::t('country-select-field', 'Samoa')],
            ['value' => 'YE', 'label' => Craft::t('country-select-field', 'Yemen')],
            ['value' => 'YT', 'label' => Craft::t('country-select-field', 'Mayotte')],
            ['value' => 'ZA', 'label' => Craft::t('country-select-field', 'South Africa')],
            ['value' => 'ZM', 'label' => Craft::t('country-select-field', 'Zambia')],
            ['value' => 'ZW', 'label' => Craft::t('country-select-field', 'Zimbabwe')],
        ];

         // Sort countries by label
        usort($countries, function ($a, $b) {
            return strcasecmp($a['label'], $b['label']);
        });

        return $countries;
    }
}
