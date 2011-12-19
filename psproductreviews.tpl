{*
* @package     PSProductReviews
* @author      Axel Etcheverry <axel@etcheverry.biz>
* @copyright   Copyright (c) 2011 Axel Etcheverry (http://www.axel-etcheverry.com)
* Displays     <a href="http://creativecommons.org/licenses/MIT/deed.fr">MIT</a>
* @license     http://creativecommons.org/licenses/MIT/deed.fr    MIT
*}
<div id="idTab6">
	{if $error}
	
	<p class="align_center">{$error|escape:'html':'UTF-8'}</p>
	
	{elseif $reviews}
	
	<div class="clear table_block">
		<table style="width: 100%">
			<tbody>
			{foreach from=$reviews item=review}
				<tr>
					<td>
						<table style="width: 100%;margin-bottom: 15px;">
							<tr>
								<td>
									<table style="width: 100%">
										<tr>
											<td>
												{l s='By' mod='productreviews'} <a href="{$review.user_link}">{$review.user_name|escape:'html':'UTF-8'}</a> {l s='on' mod='psproductreviews'} {dateFormat date=$review.review_date|escape:'html':'UTF-8' full=0}
											</td>
											<td style="width:48px">
												<div class="smallStars smallStars-{$review.review_mark}"></div>
											<td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td style="vertical-align: top">
									{$review.review_content}
								</td>
							</tr>
						</table>
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</div>
	
	{else}
	
	<p class="align_center">{l s='No review for the product.' mod='psproductreviews'}</p>
	
	{/if}
</div>