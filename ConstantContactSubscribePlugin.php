<?php
namespace Craft;

class ConstantContactSubscribePlugin extends BasePlugin
{
  function getName()
  {
    return Craft::t('Constant Contact Subscribe');
  }

  public function getDescription()
  {
    return 'Constant-Contact-Subscribe is a straightforward plugin for subscribing to a Constant Contact newsletter list.';
  }

  function getVersion()
  {
    return '1.1';
  }

  public function getDocumentationUrl()
  {
    return 'https://github.com/shankx50/constantcontactsubscribe';
  }

  function getDeveloper()
  {
    return 'Shankar Poncelet';
  }

  function getDeveloperUrl()
  {
    return 'http://ShankxWebDev.com';
  }

  protected function defineSettings()
  {
    return array(
      'constantContactApiKey'  => array(AttributeType::String,'required' => true),
      'constantContactAccessToken' => array(AttributeType::String,'required' => true),
      'constantContactList'  => array(AttributeType::String,'required' => true)
    );
  }

  public function getSettingsHtml()
  {
    return craft()->templates->render('constantcontactsubscribe/settings', array(
      'settings' => $this->getSettings()
    ));
  }

  public function prepSettings($settings)
  {
    // Clear the cached forms list when the API settings are updated
    craft()->cache->delete('ConstantContactLists');
    return $settings;
  }
}
