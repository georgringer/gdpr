<?php
declare(strict_types=1);

namespace GeorgRinger\Gdpr\Domain\Model\Dto;

class Search
{

    /** @var string */
    protected $searchWord = '';

    /** @var bool */
    protected $sensitiveOnly = false;

    /**
     * @return string
     */
    public function getSearchWord(): string
    {
        return $this->searchWord;
    }

    /**
     * @param string $searchWord
     */
    public function setSearchWord(string $searchWord)
    {
        $this->searchWord = $searchWord;
    }

    /**
     * @return bool
     */
    public function isSensitiveOnly(): bool
    {
        return (bool)$this->sensitiveOnly;
    }

    /**
     * @param bool $sensitiveOnly
     */
    public function setSensitiveOnly(bool $sensitiveOnly)
    {
        $this->sensitiveOnly = $sensitiveOnly;
    }


}
