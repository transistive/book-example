<?php

namespace Nagels\BookExample;

$translationTable = [
    'article_tags' => 'ArticleTag',
    'articles' => 'Article',
    'comments' => 'Comment',
    'polymorphic_categories' => 'Category',
    'tags' => 'Tag',
    'users' => 'User',
];

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
            self::ARTICLE_TAGS => 'ArticleTag'
        };
    }
}
