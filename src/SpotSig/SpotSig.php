<?php

/**
 * @package SpotSig
 * @author Tim Williams
 */

namespace SpotSig;

/**
 * Spotsig generator.
 */
class SpotSig
{

    protected $_stopWords   = [];
    protected $_antecedents = ['a', 'an', 'the', 'is'];
    protected $_chainLength = 2;
    protected $_distance    = 1;

    /**
     * @param array $stopwords if empty defaults loaded from stopwords.txt
     * @param array $antecedents if empty defaults used.
     */
    public function __construct(array $stopwords = [], array $antecedents = [])
    {
        if (!empty($antecedents)) {
            $this->_antecedents = $antecedents;
        }

        if (!empty($stopwords)) {
            $this->_stopWords = $stopwords;
        } else {
            $this->_stopWords = explode(PHP_EOL, file_get_contents(__DIR__ . '/stopwords.txt'));
        }
    }

    /**
     * @return array
     */
    public function getStopWords()
    {
        return $this->_stopWords;
    }

    /**
     * @return array
     */
    public function getAntecedents()
    {
        return $this->_antecedents;
    }

    /**
     * @return int
     */
    public function getDistance()
    {
        return $this->_distance;
    }

    /**
     * @param array $stopWords
     * @return \SpotSig\SpotSig
     */
    public function setStopWords($stopWords)
    {
        $this->_stopWords = $stopWords;
        return $this;
    }

    /**
     * @param array $antecedents
     * @return \SpotSig\SpotSig
     */
    public function setAntecedents($antecedents)
    {
        $this->_antecedents = $antecedents;
        return $this;
    }

    /**
     *
     * @param int $distance
     * @return \SpotSig\SpotSig
     */
    public function setDistance($distance)
    {
        $this->_distance = $distance;
        return $this;
    }

    /**
     * Get the spotsig set for the given text
     * @param string $text
     * @return array
     */
    public function analyse($text)
    {
        //split into words
        $words = preg_split('/[^\w\']+/', strtolower($text));

        $sigs = [];

        for ($i = 0; $i < count($words); $i++) {
            $word = $words[$i];
            if (in_array($word, $this->_antecedents)) { //start a new chain
                $k     = $this->_distance + $i;
                $chain = [];
                for ($j = 0; $j < $this->_chainLength && $k < count($words); $j++) {
                    $token = $words[$k];
                    while (in_array($token, $this->_stopWords) && $k < count($words)) {
                        $token = $words[$k];
                        $k++;
                    }
                    if (!in_array($token, $this->_stopWords)) {
                        $chain[] = $token;
                    }
                    $k += $this->_distance;
                }
                if (count($chain) > 0) {
                    $sigs[] = $chain;
                }
            }
        }

        return $sigs;
    }

}
