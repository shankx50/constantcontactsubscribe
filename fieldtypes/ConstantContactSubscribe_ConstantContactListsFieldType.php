<?php
namespace Craft;

class ConstantContactSubscribe_ConstantContactListsFieldType extends BaseFieldType
{
    public function getName()
    {
        return Craft::t('Constant Contact Lists');
    }

    public function defineContentAttribute()
    {
        return AttributeType::Mixed;
    }


    public function getInputHtml($name, $value)
    {
        // Reformat the input name into something that looks more like an ID
        $id = craft()->templates->formatInputId($name);

        // Get the lists
        $lists = craft()->constantContactSubscribe->getConstantContactLists();

        $options = array('0' => 'None');

        foreach ($lists as $list)
        {
            if ($list->status == "ACTIVE") {
              $options[$list->id] = $list->name;
            }
        }

        // $readable = print_r($options, true);
        // ConstantContactSubscribePlugin::log($readable);

        return craft()->templates->render('constantcontactsubscribe/fieldtype/input', array(
            'id'    => $id,
            'name'  => $name,
            'value' => $value['id'],
            'options' => $options,
        ));
    }

    public function prepValue($value)
    {

        $data = array('id' => $value);
        $lists = craft()->constantContactSubscribe->getConstantContactLists();

        foreach ($lists as $list)
        {
            if ($list->id == $value) {

              $data['name']        = $data['title'] = $list->name;
              $data['status']      = $list->status;
              $data['dateCreated'] = $list->created_date;
              $data['dateUpdated'] = $list->modified_date;
              $data['count']       = $list->contact_count;

            }
        }

        $readable = print_r($data, true);
        ConstantContactSubscribePlugin::log($readable);

        return $data;
    }
}