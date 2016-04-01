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
}
