<?php
namespace Craft;

class ConstantContactSubscribePlugin extends BasePlugin
{
  function getName()
  {
    return Craft::t('Constant Contact Subscribe');
  }

  function getVersion()
  {
    return '1.0';
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
      'constantContactApiKey'  => array(AttributeType::String, 'default' => ''),
      'constantContactAccessToken' => array(AttributeType::String, 'default' => ''),
      'constantContactList'  => array(AttributeType::String, 'default' => '')
    );
  }

  public function getSettingsHtml()
  {
    return craft()->templates->render('constantcontactsubscribe/settings', array(
      'settings' => $this->getSettings()
    ));
  }
}
