<?php

namespace Wpae\App\Field;


class CustomLabel3 extends Field
{
    const SECTION = 'advancedAttributes';

    public function getValue($snippetData)
    {
        $advancedAttributes = $this->feed->getSectionFeedData(self::SECTION);

        $customLabel = $this->replaceSnippetsInValue($advancedAttributes['customLabel3'], $snippetData);
        return $this->replaceMappings($advancedAttributes['customLabel3Mappings'], $customLabel);
    }

    public function getFieldName()
    {
        return 'custom_label_3';
    }
}