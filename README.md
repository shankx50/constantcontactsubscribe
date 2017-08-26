# Introduction

Constant-Contact-Subscribe for [CraftCMS](http://www.craftcms.com) is a straightforward plugin for subscribing to a Constant Contact newsletter list.

## Features:
- Supports adding one email address to any one email list
- Supports creating and updating contact information
- Supported constant contact fields: first name, last name, email and list id
- Includes dropdown field type for selecting a list at an entry/category level

# Required Settings
## API Key
Get API Key with via Constant Contact Mashery account.

## Access Token
Get the Access Token via Constant Contact Mashery using your, or your client's regular Constant Contact account.

## Default List ID
Developers can use the built-in functionality to get lists in one of the SDKs. They can also use Constant Contact's IO docs or a REST client to make a Get /lists API call.

As a hack, you can create a signup form using Constant Contact's form generator and then inspect the embeddable HTML snippet which will reveal the list id.

The default list ID is used only as a fallback, if a request is made without a list ID.

## Installation
1. Vendor files require PHP version 5.5 or higher
2. Clone this repository into your plugin directory.
3. Go to Settings > System > Plugins and install _Contant Contact Subscribe_.
4. Click on the _cog icon_ to get to the plugin's settings page and enter all the required information.

## Response codes
After submitting using AJAX, the plugin returns an object that contains the following response codes:

**201**: Email address was added to the list as a new subscriber<br>
**400**: Error


# Example

This plugin was updated to be used in combination with a [Guest Entry](https://github.com/craftcms/guest-entries) form, so the fields are formatted appropriately for that.

## HTML
The HTML below assumes that css rules hide the elements of `data-type:"message"` until one of them is made visible using jQuery based on the returned response code by adding the class `is-visible` to a given element.

```
<form method="post" accept-charset="UTF-8">
  {{ getCsrfInput() }}

  {# if a list is set, add a hidden input here. this is not a front end field. #}
  {% if entry.constantContactList %}
    <input type="hidden" name="addList" value="{{entry.constantContactList.id}}">
  {% endif %}

  {# first name #}
  <div class="form-group">
    <label for="firstName" class="form-label">First Name</label>
    <input class="form-control" type="text" value="" id="firstName" name="fields[firstName]">
  </div>

  {# last name #}
  <div class="form-group">
    <label for="lastName" class="form-label">Last Name</label>
    <input class="form-control" type="text" value="" id="lastName" name="fields[lastName]">
  </div>

  {# email - required #}
  <div class="form-group required">
    <label for="form-label">Email</label>
    <input class="form-control" type="email" value="" id="email" name="fields[email]">
  </div>

  <div class="form-group">
    <input type="submit" value="Sign me up!">
  </div>
</form>

<div class="form-response response-success">
  <p>Thanks! You've been added to the {{entry.constantContactList.name}} list.</p>
</div>

<div class="form-response response-error">
  <p>Looks like there is a problem. Check the format of your email address.</p>
</div>

```
## AJAX submitting
```
  var $form = $("form");

  $('.form-group.required').each(function(){
    var $input = $(this).find('input');

    if ($input.length) {
      if ($input.val().length == 0) {
        $input.addClass('error');
        requiredCheck = false;
        return false;
      }
    }
  });

  $form.submit(function(event){
    $.ajax({
      type: 'POST',
      url: '/actions/constantContactSubscribe/list/Subscribe',
      data: $('form').serialize(),
      complete: function(data){
	if (data.responseJSON.responseCode == 201){
	  $('.cd-response-success').addClass('is-visible');
	} else {
	  $('.cd-response-error').addClass('is-visible');
	}
      }
    });
    event.preventDefault();
  });
```
