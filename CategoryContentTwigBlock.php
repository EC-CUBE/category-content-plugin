<?php
namespace Plugin\CategoryContent;

use Eccube\Common\EccubeTwigBlock;

class CategoryContentTwigBlock implements EccubeTwigBlock
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public static function getTwigBlock()
    {
        return [
            '@CategoryContent/default/block_category_content.twig'
        ];
    }

}
