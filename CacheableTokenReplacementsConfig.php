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

        $module = $this->modules->get('CacheableTokenReplacements');
        $currentTokens = $module->getTokens();
        $tokenListMarkup = [];
        foreach ($currentTokens as $name => $token) {
            $nameIsValid = preg_match(
                sprintf('/^%s$/', CacheableTokenReplacements::TOKEN_NAME_REGEX)
                , $name
            ) === 1;
            $callbackIsValid = (is_array($token) || $token instanceof \ArrayAccess)
                && isset($token[CacheableTokenReplacements::TOKEN_KEY_CALLBACK])
                && is_callable($token[CacheableTokenReplacements::TOKEN_KEY_CALLBACK]);
            $errors = [
                !$nameIsValid ? $this->_('The token name contains invalid characters.') : null,
                !$callbackIsValid ? $this->_('The token definition does not include a valid callback.') : null,
            ];
            $hasErrors = array_filter($errors);
            $checkMarkup = $hasErrors
                ? implode("\n", array_map(function ($line) {
                        return sprintf('<p><i class="fa fa-fw fa-times-circle"></i> %s</p>', $line);
                    }, array_filter($errors)))
                : sprintf('<i class="fa fa-fw fa-check"></i> %s', $this->_('This token is valid.'));
            $tokenListMarkup[] = sprintf(
                '<div style="margin-bottom:.5rem;">
                    <h3 style="margin-bottom:.25rem;">%1$s</h3>
                    <div class="%2$s">%3$s</div>
                </div>',
                $name,
                $hasErrors ? 'uk-alert-danger' : 'uk-alert-success',
                $checkMarkup
            );
        }
        $CurrentTokenDisplay = $this->modules->get('InputfieldMarkup');
        $CurrentTokenDisplay->label = $this->_('Token list');
        $CurrentTokenDisplay->description = $this->_('This list will show you all the tokens that currently exist. Use this to check if your custom tokens are being registered correctly. You will also see warnings if a token has an invalid name or is missing a valid callback.');
        $CurrentTokenDisplay->columnWidth = 50;
        $CurrentTokenDisplay->collapsed = Inputfield::collapsedNever;
        $CurrentTokenDisplay->value = sprintf('%s', implode("\n", $tokenListMarkup));

        $UsageInstructions = $this->modules->get('InputfieldMarkup');
        $UsageInstructions->label = $this->_('Usage instructions');
        $UsageInstructions->value = '@TODO: Write instructions.';
        $UsageInstructions->columnWidth = 50;
        $UsageInstructions->collapsed = Inputfield::collapsedNever;

        $DelimiterSettings = $this->modules->get('InputfieldFieldset');
        $DelimiterSettings->label = $this->_('Token format & delimiters');
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
            $DelimiterField->columnWidth = 33;
            $DelimiterField->collapsed = Inputfield::collapsedNever;
            $DelimiterField->notes = sprintf($this->_('Default: `%s`'), $defaults[$name]);
            $DelimiterField->required = true;
            $DelimiterField->requiredAttr = true;
            $DelimiterField->minlength = 1;
            $DelimiterField->maxlength = 5;
            $DelimiterSettings->add($DelimiterField);
        }

        $inputfields->add($UsageInstructions);
        $inputfields->add($CurrentTokenDisplay);
        $inputfields->add($PageRenderHookActive);
        $inputfields->add($PageRenderHookFrontendOnly);
        $inputfields->add($DelimiterSettings);

        return $inputfields;
    }
}
