<?php

require_once __DIR__ . '/FormElement.php';

class FormCheckbox extends FormElement
{
    /**
     * Default checked/unchecked options
     * @var array
     */
    protected static $_defaultCheckedOptions = array(
        'checkedValue' => '1',
        'uncheckedValue' => '0'
    );

    /**
     * Generates a 'checkbox' element.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are extracted in place of added parameters.
     * @param mixed $value The element value.
     * @param array $attribs Attributes for the element tag.
     * @return string The element XHTML.
     */
    public function formCheckbox($name, $value = null, $attribs = null, array $checkedOptions = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, id, value, attribs, options, listsep, disable

        $checked = false;
        if (isset($attribs['checked']) && $attribs['checked']) {
            $checked = true;
            unset($attribs['checked']);
        } elseif (isset($attribs['checked'])) {
            $checked = false;
            unset($attribs['checked']);
        }

        $checkedOptions = self::determineCheckboxInfo($value, $checked, $checkedOptions);

        // is the element disabled?
        $disabled = '';
        if ($disable) {
            $disabled = ' disabled="disabled"';
        }

        // build the element
        $xhtml = '';
        if ((!$disable && !strstr($name, '[]'))
            && (empty($attribs['disableHidden']) || !$attribs['disableHidden'])
        ) {
            $xhtml = $this->_hidden($name, $checkedOptions['uncheckedValue']);
        }

        if (array_key_exists('disableHidden', $attribs)) {
            unset($attribs['disableHidden']);
        }

        $xhtml .= '<input type="checkbox"'
            . ' name="' . $this->escape($name) . '"'
            . ' id="' . $this->escape($id) . '"'
            . ' value="' . $this->escape($checkedOptions['checkedValue']) . '"'
            . $checkedOptions['checkedString']
            . $disabled
            . $this->_htmlAttribs($attribs)
            . $this->getClosingBracket();

        return $xhtml;
    }

    /**
     * Determine checkbox information
     *
     * @param  string $value
     * @param  bool $checked
     * @param  array|null $checkedOptions
     * @return array
     */
    public static function determineCheckboxInfo($value, $checked, array $checkedOptions = null)
    {
        // Checked/unchecked values
        $checkedValue = null;
        $uncheckedValue = null;
        if (is_array($checkedOptions)) {
            if (array_key_exists('checkedValue', $checkedOptions)) {
                $checkedValue = (string)$checkedOptions['checkedValue'];
                unset($checkedOptions['checkedValue']);
            }
            if (array_key_exists('uncheckedValue', $checkedOptions)) {
                $uncheckedValue = (string)$checkedOptions['uncheckedValue'];
                unset($checkedOptions['uncheckedValue']);
            }
            if (null === $checkedValue) {
                $checkedValue = (string)array_shift($checkedOptions);
            }
            if (null === $uncheckedValue) {
                $uncheckedValue = (string)array_shift($checkedOptions);
            }
        } elseif ($value !== null) {
            $uncheckedValue = self::$_defaultCheckedOptions['uncheckedValue'];
        } else {
            $checkedValue = self::$_defaultCheckedOptions['checkedValue'];
            $uncheckedValue = self::$_defaultCheckedOptions['uncheckedValue'];
        }

        // is the element checked?
        $checkedString = '';
        if ($checked || ((string)$value === $checkedValue)) {
            $checkedString = ' checked="checked"';
            $checked = true;
        } else {
            $checked = false;
        }

        // Checked value should be value if no checked options provided
        if ($checkedValue == null) {
            $checkedValue = $value;
        }

        return array(
            'checked' => $checked,
            'checkedString' => $checkedString,
            'checkedValue' => $checkedValue,
            'uncheckedValue' => $uncheckedValue,
        );
    }
}
