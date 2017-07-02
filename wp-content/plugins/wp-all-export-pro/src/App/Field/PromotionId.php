<?php

namespace Wpae\App\Field;


class PromotionId extends Field
{
    const SECTION = 'advancedAttributes';

    public function getValue($snippetData)
    {
        $advancedAttributes = $this->feed->getSectionFeedData(self::SECTION);

        $promotionId = $advancedAttributes['promotionId'];
        $promotionId = $this->replaceSnippetsInValue($promotionId, $snippetData);

        return $this->replaceMappings($advancedAttributes['promotionIdMappings'], $promotionId);
    }

    public function getFieldName()
    {
        return 'promotion_id';
    }
}