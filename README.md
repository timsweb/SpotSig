#SpotSig
This is a quickly thrown together PHP implementation of the Spot Sig algorithm
based on a couple of bits from the java code found at
https://github.com/luismmontielg/spotsigs. More information on the algorithm
can be found at http://www.slideshare.net/infoblog/spot-sigs-presentation?from=ss_embed.

##Missing features
At the moment it only generates the spot signatures and does a Jaccard
comparison of two documents. Anyone wanting to add to this library may want to
add in the document-set-de-duplication (with partitioning/sorting etc).

Text entered into the analyse function isn't sanitised in any way; that's up to
the calling code.

##Example useage
```PHP
$ss = new \SpotSig\SpotSig();
$doc1 = new \SpotSig\DocumentIndex($ss->analyse("Large sample text one"));
$doc2 = new \SpotSig\DocumentIndex($ss->analyse("Large sample text two"));
echo $doc1->jaccardCompare($doc2) .PHP_EOL; //just get a value
echo $doc1->jaccardCompare($doc2, 0.6) .PHP_EOL; //use a threshold
```
