<?php
namespace Craft;
// require the autoloader
require_once(CRAFT_PLUGINS_PATH.'constantcontactsubscribe/vendor/autoload.php');
use Ctct\Components\Contacts\Contact;
use Ctct\ConstantContact;
use Ctct\Exceptions\CtctException;

class ConstantContactSubscribe_ListController extends BaseController
{
  public function actionSubscribe()
  {
    // Get post variables - returns 400 if email not provided
    $addEmail = craft()->request->getRequiredParam('addEmail');

    // Get plugin settings
    $settings = $this->_init_settings();
    $addList = $settings['constantContactList'];
    define("APIKEY", $settings['constantContactApiKey']);
    define("ACCESS_TOKEN", $settings['constantContactAccessToken']);

    $cc = new ConstantContact(APIKEY);

    // check if the form was submitted
    $action = "Getting Contact By Email Address";
    try {
      // check to see if a contact with the email address already exists in the account
      $response = $cc->contactService->getContacts(ACCESS_TOKEN, array("email" => $addEmail));
      // create a new contact if one does not exist
      if (empty($response->results)) {
        $action = "Creating Contact";
        $contact = new Contact();
        $contact->addEmail($addEmail);
        $contact->addList($addList);
        // $contact->first_name = $_POST['first_name'];
        // $contact->last_name = $_POST['last_name'];
        /*
        * The third parameter of addContact defaults to false, but if this were set to true it would tell Constant
        * Contact that this action is being performed by the contact themselves, and gives the ability to
        * opt contacts back in and trigger Welcome/Change-of-interest emails.
        *
        * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
        */
        $returnContact = $cc->contactService->addContact(ACCESS_TOKEN, $contact, true);
        // update the existing contact if address already existed
      } else {
        $action = "Updating Contact";
        $contact = $response->results[0];
        if ($contact instanceof Contact) {
          $contact->addList($addList);
          // $contact->first_name = $_POST['first_name'];
          // $contact->last_name = $_POST['last_name'];
          /*
          * The third parameter of updateContact defaults to false, but if this were set to true it would tell
          * Constant Contact that this action is being performed by the contact themselves, and gives the ability to
          * opt contacts back in and trigger Welcome/Change-of-interest emails.
          *
          * See: http://developer.constantcontact.com/docs/contacts-api/contacts-index.html#opt_in
          */
          $returnContact = $cc->contactService->updateContact(ACCESS_TOKEN, $contact, true);
        } else {
          $e = new CtctException();
          $e->setErrors(array("type", "Contact type not returned"));
          throw $e;
        }
      }
      // catch any exceptions thrown during the process and print the errors to screen
    } catch (CtctException $ex) {
      echo '<span class="label label-important">Error ' . $action . '</span>';
      echo '<div class="container alert-error"><pre class="failure-pre">';
      print_r($ex->getErrors());
      echo '</pre></div>';
      die();
    }
  }

  private function _init_settings()
  {
    $plugin = craft()->plugins->getPlugin('constantcontactsubscribe');
    $plugin_settings = $plugin->getSettings();
    return $plugin_settings;
  }

  /**
  * Set a message for use in the templates
  *
  * @author Martin Blackburn
  */
  private function _setMessage($errorcode, $email, $vars, $message = '', $success = false, $redirect = '')
  {
    if (craft()->request->isAjaxRequest()) {
      return $this->returnJson(array(
        'success' => $success,
        'errorCode' => $errorcode,
        'message' => $message,
        'values' => array(
          'email' => $email,
          'vars' => $vars
        )
      ));
    }

    if ($redirect!='') {
      // if a redirect url was set in template form, redirect to this
      $this->redirectToPostedUrl();
    } else {
      craft()->urlManager->setRouteVariables(array(
        'mailchimpSubscribe' => array(
          'success' => $success,
          'errorCode' => $errorcode,
          'message' => $message,
          'values' => array(
            'email' => $email,
            'vars' => $vars
          )
        )
      ));
    }
  }

}
