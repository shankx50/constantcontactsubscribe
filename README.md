##Introduction

Constant-Contact-Subscribe for [CraftCMS](http://www.craftcms.com) is a very simple plugin for subscribing to a Constant Contact newsletter list. So far it only supports submitting a new email address via AJAX to one specific list that you define in the plugin settings.

##Required Settings
##API Key
Get API Key with via Constant Contact Mashery account.

##Access Token
Get the Access Token via Constant Contact Mashery using your, or your client's regular Constant Contact account.

##List ID
Developers can use the built-in functionality to get lists in one of the SDKs. They can also use Constant Contact's IO docs or a REST client to make a Get /lists API call.

As a hack, you can create a signup form using Constant Contact's form generator and then inspect the embeddable HTML snippet which will reveal the list id.

##Installation
1. Vendor files require PHP version 5.5 or higher
2. Clone this repository into your plugin directory.
3. Go to Settings > System > Plugins and install _Contant Contact Subscribe_.
4. Click on the _cog icon_ to get to the plugin's settings page and enter all the required information.

##Response codes
After submitting using AJAX, the plugin returns an object that contains the following response codes:

**422**: Email address is already subscribed to list<br>
**201**: Email address was added to the list as a new subscriber


##Example
###HTML
The HTML below assumes that css rules hide the elements of `data-type:"message"` until one of them is made visible using jQuery based on the returned response code by adding the class `is-visible` to a given element.

```
<form action="" method="post">
 	<input type="hidden" name="action" value="constantContactSubscribe/list/Subscribe">
	<label class="cd-label" for="cd-email">Label TExt</label>
	<input type="email" id="cd-email" class="cd-email" name="addEmail" value="{% if (constantContactSubscribe is defined) and (not constantContactSubscribe.success) %}{{ constantContactSubscribe.values.addEmail }}{% endif %}" placeholder="Enter your email address">

	<input type="submit" class="cd-submit" name="" value="Submit"/>
</form>

<div data-type="message" class="cd-response cd-response-notification">
	<p>Looks like you have already subscribed to our newsletter.</p>
</div>
<div data-type="message" class="cd-response cd-response-success">
	<p>Thanks! Please confirm your email by clicking the link we just emailed you.</p>
</div>
<div data-type="message" class="cd-response cd-response-error">
	<p>Looks like there is a problem. Check the format of your email address.</p>
</div>

```
###AJAX submitting
```
	$('.cd-submit').on('click', function(event){
		$.ajax({
			type: 'POST',
			url: '/',
			data: $('form').serialize(),
			beforeSend: function(event){
				// Handle the beforeSend event
			},
			complete: function(data){
				// Handle the complete event
				if (data.responseJSON.responseCode == 422) {
					$('.cd-response-notification').addClass('is-visible');
				}else if(data.responseJSON.responseCode == 201){
					$('.cd-response-success').addClass('is-visible');
				}else{
					$('.cd-response-error').addClass('is-visible');
				}
			}
		});
		event.preventDefault();
	});
```
