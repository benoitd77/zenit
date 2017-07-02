<?php

namespace Wpae\App\Field;


class Multipack extends Field
{
    const SECTION = 'advancedAttributes';

    public function getValue($snippetData)
    {
        $advancedAttributes = $this->feed->getSectionFeedData(self::SECTION);
        
        return $this->replaceSnippetsInValue($advancedAttributes['multipack'], $snippetData);
    }

    public function getFieldName()
    {
        return 'multipack';
    }
}