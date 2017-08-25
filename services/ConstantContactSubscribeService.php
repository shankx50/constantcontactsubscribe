<?php
namespace Craft;

// require the autoloader
require_once(CRAFT_PLUGINS_PATH.'constantcontactsubscribe/vendor/autoload.php');
use Ctct\Components\Contacts\Contact;
use Ctct\ConstantContact;
use Ctct\Exceptions\CtctException;

class ConstantContactSubscribeService extends BaseApplicationComponent
{
    private $cacheKey = 'ConstantContactLists';
    private $cacheDuration = 'PT1H';

    public function getConstantContactLists($apiKey=false, $accessToken=false)
    {

        if (!$apiKey || $accessToken)
        {
          $settings = craft()->plugins->getPlugin("constantContactSubscribe")->getSettings();
          $apiKey = $settings->constantContactApiKey;
          $accessToken = $settings->constantContactAccessToken;
        }

        $cc = new ConstantContact($apiKey);

        // Use the cached list, if it exists
        $lists = craft()->cache->get($this->cacheKey);

        // If not cached, get from the API
        if (true || !$lists)
        {
          try {

            $lists = $cc->listService->getLists($accessToken);

            // Cache it
            craft()->cache->set($this->cacheKey, $lists, $this->cacheDuration);

          } catch (CtctException $ex) {
            foreach ($ex->getErrors() as $error) {
                return $error;
            }
            if (!isset($lists)) {
                $lists = null;
            }
          }

        //   try
        //     {
        //         $client = new \Guzzle\Http\Client("https://$subdomain.wufoo.com");
        //         $request = $client->get('/api/v3/forms.json?IncludeTodayCount=true');
        //         $request->setAuth($apiKey, 'pass', CURLAUTH_BASIC);
        //         $response = $request->send()->json();
        //
        //         $lists = array();
        //         foreach ($response['Forms'] as $form)
        //         {
        //             $lists[$form['Hash']] = array_change_key_case($form);
        //         }
        //
        //         // Cache it
        //         craft()->cache->set($this->cacheKey, $lists, $this->cacheDuration);
        //     }
        //     catch (\Exception $e)
        //     {
        //         return $e->getResponse()->getReasonPhrase();
        //     }
        // }

        return $lists;
    }
}
}
