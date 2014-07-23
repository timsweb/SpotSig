<?php
/**
 * @package SpotSig
 * @author Tim Williams
 */
namespace SpotSig;
/**
 * Represents an a document in terms of it's spot signatures.
 */
class DocumentIndex
{
    /**
     * @var arary
     */
    protected $_spotSignatures = [];

    /**
     * @var array
     */
    protected $_weightedIndex = [];

    /**
     * Create a new document from spot signatures
     * @param array $spotSignatures
     */
    public function __construct(array $spotSignatures)
    {
        $this->_spotSignatures = $spotSignatures;
        $this->_index();
    }

    /**
     * Generate the weighted index from this objects spot signatures.
     */
    protected function _index()
    {
        $this->_weightedIndex = [];
        foreach ($this->_spotSignatures as $chain)
        {
            $hash = sha1(implode(' ', $chain));
            if (isset($this->_weightedIndex[$hash])) {
                $this->_weightedIndex[$hash]++;
            } else {
                $this->_weightedIndex[$hash] = 1;
            }
        }
    }

    /**
     * Get the weighted index.
     * @return array
     */
    public function getWeightedIndex()
    {
        return $this->getWeightedIndex();
    }

    /**
     * Get the weight of a given key
     * @param string $hash
     * @return int
     */
    public function getWeight($hash)
    {
        return(isset($this->_weightedIndex[$hash]))? $this->_weightedIndex[$hash] : 0;
    }

    /**
     * Get all the hashes in the index.
     * @return array of string
     */
    public function getKeys()
    {
        return array_keys($this->_weightedIndex);
    }

    /**
     * Get cardinality of signatures for this document.
     * @return int
     */
    public function getCardinality()
    {
        return count($this->_spotSignatures);
    }

    /**
     * Jaccard compare this document with another
     * @param \SpotSig\DocumentIndex $b
     * @param float $threshold
     * @return float
     */
    public function jaccardCompare(DocumentIndex $b, $threshold = 0)
    {
        $upperMax = max([$this->getCardinality(), $b->getCardinality()]);
        $upperUnion = $this->getCardinality() + $b->getCardinality();
        $sMin = $sMax = $sC1 = $sC2 = $bound = 0;
        foreach ($this->getKeys() as $key) {
            $c1 = $this->getWeight($key);
            $c2 = $b->getWeight($key);
            $min = min([$c1, $c2]);
            $max = max([$c1, $c2]);
            $sMin += $min;
            $sMax += $max;
            $sC1 += $c1;
            $sC2 += $c2;
            $bound += $max - $min;
            if ($threshold > 0) {
                if (($upperMax - $bound) / $upperMax < $threshold) {
                    return 0;
                } else if ($sMin / $upperUnion >= $threshold) {
                    return 1;
                }
            }
        }
        return $sMin  / ($sMax + ($this->getCardinality() - $sC1) + ($b->getCardinality() - $sC2));

        /* This is the original java version of this function for reference.
         *
         * double min, max, s_min = 0, s_max = 0, bound = 0;
         * double upper_max = Math.max(index1.totalCount, index2.totalCount);
         * double upper_union = index1.totalCount + index2.totalCount;
         * int i, c1, c2, s_c1 = 0, s_c2 = 0;
         *
         * for (i = 0; i < keys.length; i++) {
         *   c1 = index1.getCount(keys[i]);
         *   c2 = index2.getCount(keys[i]);
         *   min = Math.min(c1, c2);
         *   max = Math.max(c1, c2);
         *   s_min += min;
         *   s_max += max;
         *   s_c1 += c1;
         *   s_c2 += c2;

         *   // Early threshold break for pairwise counter comparison
         *   bound += max - min;
         *   if ((upper_max - bound) / upper_max < threshold)
         *     return 0;
         *   else if (s_min / upper_union >= threshold)
         *     return 1;
         * }
         *
         * return s_min
         *     / (s_max + (index1.totalCount - s_c1) + (index2.totalCount - s_c2));
         */

    }
}