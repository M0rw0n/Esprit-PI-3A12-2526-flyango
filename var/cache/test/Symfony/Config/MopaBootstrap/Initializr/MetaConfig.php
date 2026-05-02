<?php

namespace Symfony\Config\MopaBootstrap\Initializr;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class MetaConfig 
{
    private $title;
    private $description;
    private $keywords;
    private $authorName;
    private $authorUrl;
    private $feedAtom;
    private $feedRss;
    private $sitemap;
    private $nofollow;
    private $noindex;
    private $_usedProperties = [];

    /**
     * @default 'MopaBootstrapBundle'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function title($value): static
    {
        $this->_usedProperties['title'] = true;
        $this->title = $value;

        return $this;
    }

    /**
     * @default 'MopaBootstrapBundle'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function description($value): static
    {
        $this->_usedProperties['description'] = true;
        $this->description = $value;

        return $this;
    }

    /**
     * @default 'MopaBootstrapBundle, Twitter Bootstrap, HTML5 Boilerplate'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function keywords($value): static
    {
        $this->_usedProperties['keywords'] = true;
        $this->keywords = $value;

        return $this;
    }

    /**
     * @default 'My name'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function authorName($value): static
    {
        $this->_usedProperties['authorName'] = true;
        $this->authorName = $value;

        return $this;
    }

    /**
     * @default '#'
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function authorUrl($value): static
    {
        $this->_usedProperties['authorUrl'] = true;
        $this->authorUrl = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function feedAtom($value): static
    {
        $this->_usedProperties['feedAtom'] = true;
        $this->feedAtom = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function feedRss($value): static
    {
        $this->_usedProperties['feedRss'] = true;
        $this->feedRss = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|mixed $value
     * @return $this
     */
    public function sitemap($value): static
    {
        $this->_usedProperties['sitemap'] = true;
        $this->sitemap = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function nofollow($value): static
    {
        $this->_usedProperties['nofollow'] = true;
        $this->nofollow = $value;

        return $this;
    }

    /**
     * @default false
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function noindex($value): static
    {
        $this->_usedProperties['noindex'] = true;
        $this->noindex = $value;

        return $this;
    }

    public function __construct(array $value = [])
    {
        if (array_key_exists('title', $value)) {
            $this->_usedProperties['title'] = true;
            $this->title = $value['title'];
            unset($value['title']);
        }

        if (array_key_exists('description', $value)) {
            $this->_usedProperties['description'] = true;
            $this->description = $value['description'];
            unset($value['description']);
        }

        if (array_key_exists('keywords', $value)) {
            $this->_usedProperties['keywords'] = true;
            $this->keywords = $value['keywords'];
            unset($value['keywords']);
        }

        if (array_key_exists('author_name', $value)) {
            $this->_usedProperties['authorName'] = true;
            $this->authorName = $value['author_name'];
            unset($value['author_name']);
        }

        if (array_key_exists('author_url', $value)) {
            $this->_usedProperties['authorUrl'] = true;
            $this->authorUrl = $value['author_url'];
            unset($value['author_url']);
        }

        if (array_key_exists('feed_atom', $value)) {
            $this->_usedProperties['feedAtom'] = true;
            $this->feedAtom = $value['feed_atom'];
            unset($value['feed_atom']);
        }

        if (array_key_exists('feed_rss', $value)) {
            $this->_usedProperties['feedRss'] = true;
            $this->feedRss = $value['feed_rss'];
            unset($value['feed_rss']);
        }

        if (array_key_exists('sitemap', $value)) {
            $this->_usedProperties['sitemap'] = true;
            $this->sitemap = $value['sitemap'];
            unset($value['sitemap']);
        }

        if (array_key_exists('nofollow', $value)) {
            $this->_usedProperties['nofollow'] = true;
            $this->nofollow = $value['nofollow'];
            unset($value['nofollow']);
        }

        if (array_key_exists('noindex', $value)) {
            $this->_usedProperties['noindex'] = true;
            $this->noindex = $value['noindex'];
            unset($value['noindex']);
        }

        if ([] !== $value) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($value)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['title'])) {
            $output['title'] = $this->title;
        }
        if (isset($this->_usedProperties['description'])) {
            $output['description'] = $this->description;
        }
        if (isset($this->_usedProperties['keywords'])) {
            $output['keywords'] = $this->keywords;
        }
        if (isset($this->_usedProperties['authorName'])) {
            $output['author_name'] = $this->authorName;
        }
        if (isset($this->_usedProperties['authorUrl'])) {
            $output['author_url'] = $this->authorUrl;
        }
        if (isset($this->_usedProperties['feedAtom'])) {
            $output['feed_atom'] = $this->feedAtom;
        }
        if (isset($this->_usedProperties['feedRss'])) {
            $output['feed_rss'] = $this->feedRss;
        }
        if (isset($this->_usedProperties['sitemap'])) {
            $output['sitemap'] = $this->sitemap;
        }
        if (isset($this->_usedProperties['nofollow'])) {
            $output['nofollow'] = $this->nofollow;
        }
        if (isset($this->_usedProperties['noindex'])) {
            $output['noindex'] = $this->noindex;
        }

        return $output;
    }

}
