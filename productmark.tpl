{*
* @package     PSProductReviews
* @author      Axel Etcheverry <axel@etcheverry.biz>
* @copyright   Copyright (c) 2011 Axel Etcheverry (http://www.axel-etcheverry.com)
* Displays     <a href="http://creativecommons.org/licenses/MIT/deed.fr">MIT</a>
* @license     http://creativecommons.org/licenses/MIT/deed.fr    MIT
*}

{if $avgMark}
<div id="review_block">
	<span>{l s='Reviews :' mod='psproductreviews'}</span>
	<div class="smallStars smallStars-{$avgMark}"></div>
</div>
{/if}