/*
 Copyright (c) 2003-2016, CKSource - Frederico Knabben. All rights reserved.
 For licensing, see LICENSE.md or http://ckeditor.com/license
*/
CKEDITOR.addTemplates("default",{imagesPath:CKEDITOR.getUrl(CKEDITOR.plugins.getPath("templates")+"templates/images/"),
	templates:[{
		title:"Image Right with Caption",
		image:"blockright.gif",
		description:"One image with caption.",
		html:'<figure class="block_right"><img src="/images/backend/addimage.jpg" alt="Double-click to add new image"><figcaption>Type your caption here.</figcaption></figure>'},
		{
		title:"Image Right, no caption",
		image:"blockright.gif",
		description:"One image with no caption.",
		html:'<figure class="block_right"><img src="/images/backend/addimage.jpg" alt="Double-click to add new image"></figure>'},
		{
		title:"Quote",
		image:"quote.gif",
		description:"A single quote.",
		html:'<figure class="quote"><blockquote class="quote_content"><p>Type quote here.</p></blockquote><figcaption class="quote_caption"><span class="quote_caption_name">Type author name.</span></figcaption></figure>'},
	]});