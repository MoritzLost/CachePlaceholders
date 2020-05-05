<?php

namespace ProcessWire;

class CacheableTokenReplacementsConfig extends ModuleConfig
{
    public function getDefaults()
    {
        return [
            'PageRenderHookActive' => true,
            'PageRenderHookFrontendOnly' => true,
            'DelimiterStart' => CacheableTokenReplacements::DEFAULT_DELIMITER_START,
            'DelimiterEnd' => CacheableTokenReplacements::DEFAULT_DELIMITER_END,
            'DelimiterParameters' => CacheableTokenReplacements::DEFAULT_DELIMITER_PARAMETERS,
            'DelimiterKeyValue' => CacheableTokenReplacements::DEFAULT_DELIMITER_KEY_VALUE,
            'DelimiterMultivalue' => CacheableTokenReplacements::DEFAULT_DELIMITER_MULTIVALUE,
        ];
    }

    public function getInputfields()
    {
        $inputfields = parent::getInputfields();

        $PageRenderHookActive = $this->modules->get('InputfieldCheckbox');
        $PageRenderHookActive->name = 'PageRenderHookActive';
        $PageRenderHookActive->label = $this->_('Automatic replacements');
        $PageRenderHookActive->label2 = $this->_('Activate automatic replacements');
        $PageRenderHookActive->description = $this->_('If this box is checked, the module will add a hook after `Page::render` and perform the token replacements on the entire source code. This will work even if the response is served from the template render cache.');
        $PageRenderHookActive->notes = $this->_('Deactivate automatic mode if you want to call `replaceTokens` manually in your code.');
        $PageRenderHookActive->columnWidth = 50;
        $PageRenderHookActive->collapsed = Inputfield::collapsedNever;

        $PageRenderHookFrontendOnly = $this->modules->get('InputfieldCheckbox');
        $PageRenderHookFrontendOnly->name = 'PageRenderHookFrontendOnly';
        $PageRenderHookFrontendOnly->label = $this->_('Automatic mode: Frontend only');
        $PageRenderHookFrontendOnly->label2 = $this->_('Perform automatic replacements only in the frontend, not on CMS pages.');
        $PageRenderHookFrontendOnly->showIf = 'PageRenderHookActive=1';
        $PageRenderHookFrontendOnly->columnWidth = 50;
        $PageRenderHookFrontendOnly->collapsed = Inputfield::collapsedNever;

        $DelimiterSettings = $this->modules->get('InputfieldFieldset');
        $DelimiterSettings->label = $this->_('Token format & delimiters');
        // @TODO: example token with current delimiters
        $DelimiterSettings->collapsed = Inputfield::collapsedYes;

        $delimiterFields = [
            'DelimiterStart' => $this->_('Token start delimiter'),
            'DelimiterEnd' => $this->_('Token end delimiter'),
            'DelimiterParameters' => $this->_('Parameter separator'),
            'DelimiterKeyValue' => $this->_('Key-value separator for parameters'),
            'DelimiterMultivalue' => $this->_('Separator for multivalue parameters'),
        ];
        $defaults = $this->getDefaults();
        foreach ($delimiterFields as $name => $label) {
            $DelimiterField = $this->modules->get('InputfieldText');
            $DelimiterField->name = $name;
            $DelimiterField->label = $label;
            $DelimiterField->columnWidth = 20;
            $DelimiterField->collapsed = Inputfield::collapsedNever;
            $DelimiterField->notes = sprintf($this->_('Default: `%s`'), $defaults[$name]);
            $DelimiterSettings->add($DelimiterField);
        }

        $inputfields->add($PageRenderHookActive);
        $inputfields->add($PageRenderHookFrontendOnly);
        $inputfields->add($DelimiterSettings);

        return $inputfields;
    }
}
