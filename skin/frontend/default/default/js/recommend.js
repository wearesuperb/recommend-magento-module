/*
 * Superb_Recommend
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * @category   Superb
 * @package    Superb_Recommend
 * @author     Superb <hello@wearesuperb.com>
 * @copyright  Copyright (c) 2015 Superb Media Limited
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
var $recommendTabSize;
var recommendSitePreviewMode = false;
if (window.location.search.indexOf('recommend-site-preview=true')!==-1)
{
    Mage.Cookies.set('recommend-site-preview', '1');
    recommendSitePreviewMode = true;
}
else if (window.location.search.indexOf('recommend-site-preview=false')!==-1)
{
    Mage.Cookies.set('recommend-site-preview', '0');
    recommendSitePreviewMode = false;
}
else if (Mage.Cookies.get('recommend-site-preview') ==='1')
{
    recommendSitePreviewMode = true;
}
function recommendTrackerCallback(id,html)
{
    if (!recommendSitePreviewMode)
        return ;
    jQuery('#'+id).html(html);
    if (typeof html == 'string')
    {
        if (jQuery('#'+id).parent().hasClass('superb-recommend-container'))
            jQuery('#'+id).parent().removeClass('no-display');
    }
}