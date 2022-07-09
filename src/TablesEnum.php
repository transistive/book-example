<?php

namespace Nagels\BookExample;

use Error;

enum TablesEnum : string
{
    case ARTICLE_TAGS = 'article_tags';
    case ARTICLES = 'articles';
    case COMMENTS = 'comments';
    case POLYMORPHIC_CATEGORIES = 'polymorphic_categories';
    case TAGS = 'tags';
    case USERS = 'users';

    public function asTag(): string
    {
        return match ($this) {
            self::ARTICLES => 'Article',
            self::TAGS => 'Tag',
            self::COMMENTS => 'Comment',
            self::POLYMORPHIC_CATEGORIES => 'Category',
            self::USERS => 'User',
            default => throw new Error(sprintf('Cannot use "%s" as a Tag', $this->value))
        };
    }
}
