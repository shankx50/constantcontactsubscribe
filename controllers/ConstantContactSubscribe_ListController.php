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
      //Check if email is valid
      if(!$this->_validateEmail($addEmail)){
        $e = new CtctException();
        $e->setErrors(array("email", "Email not valid"));
        throw $e;
      }
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
        $this->_setMessage(201, $addEmail, $returnContact, "Subscribed successfully", true);

        // Respond that the user already exists on the list
      } elseif (!empty($response->results)) {
        $this->_setMessage(422, $addEmail, "The email address passed already exists on this list.",  true);
      } else {
        $e = new CtctException();
        $e->setErrors(array("type", "Contact type not returned"));
        throw $e;
      }
      // catch any exceptions thrown during the process and print the errors to screen
    } catch (CtctException $ex) {
      $this->_setMessage(400, $addEmail, $ex->getErrors(), false);
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

  private function _setMessage($responseCode, $email, $message = '', $success = false)
  {
    if (craft()->request->isAjaxRequest()) {
      return $this->returnJson(array(
        'success' => $success,
        'responseCode' => $responseCode,
        'message' => $message,
          'email' => $email
      ));
    }
  }

  /**
  * Validate an email address.
  * Provide email address (raw input)
  * Returns true if the email address has the email
  * address format and the domain exists.
  *
  * @param string Email to validate
  * @return boolean
  * @author André Elvan
  */
  private function _validateEmail ($email) {
    $isValid = true;
    $atIndex = strrpos($email, "@");
    if (is_bool($atIndex) && !$atIndex)
    {
      $isValid = false;
    }
    else
    {
      $domain = substr($email, $atIndex+1);
      $local = substr($email, 0, $atIndex);
      $localLen = strlen($local);
      $domainLen = strlen($domain);
      if ($localLen < 1 || $localLen > 64)
      {
        // local part length exceeded
        $isValid = false;
      }
      else if ($domainLen < 1 || $domainLen > 255)
      {
        // domain part length exceeded
        $isValid = false;
      }
      else if ($local[0] == '.' || $local[$localLen-1] == '.')
      {
        // local part starts or ends with '.'
        $isValid = false;
      }
      else if (preg_match('/\\.\\./', $local))
      {
        // local part has two consecutive dots
        $isValid = false;
      }
      else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain))
      {
        // character not valid in domain part
        $isValid = false;
      }
      else if (preg_match('/\\.\\./', $domain))
      {
        // domain part has two consecutive dots
        $isValid = false;
      }
      else if
      (!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',
        str_replace("\\\\","",$local)))
        {
          // character not valid in local part unless
          // local part is quoted
          if (!preg_match('/^"(\\\\"|[^"])+"$/',
          str_replace("\\\\","",$local)))
          {
            $isValid = false;
          }
        }
        if ($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A")))
        {
          // domain not found in DNS
          $isValid = false;
        }
      }
      return $isValid;
    }

  }
