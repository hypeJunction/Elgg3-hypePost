hypePost
========

Provides utilities for creating and editing posts.

**Extendable form fields**
Post forms are built using an extended field API layer, which makes it easier to define
all fields in one place, and not have to worry about maintaining form, action and profile
logic separate from each other.
 
 
## Form Fields

To extend post form, use ``'fields', '<entity_type>:<entity_subtype>`` plugin hook.

Post fields support all options supported by core `elgg_view_field()`. 
In addition to that, all field properties can be defined as closure,
which will receive an entity and a field options as arguments.

```php
$field = [
	'#type' => 'file',
	'name' => 'icon',
	'#label' => function(ElggEntity $entity, $params) {
		return elgg_echo("label:icon:$entity->type:$entity->subtype");
	},
	'required' => function(ElggEntity $entity, $params) {
		return !$entity->hasIcon('medium');
	},
];
```

In addition, post fields support the following parameters:

```php
elgg_register_plugin_hook_handler('fields', 'object:post', function(\Elgg\Hook $hook) {

	$fields = $hook->getValue();
	
	$fields[] = [
		'#type' => 'file',
		'name' => 'icon',
	
		// By default, field value will be retrieved from metadata
        // using field name $entity->custom_property	
		// You can however specify a getter function, which will be executed,
		// if the field value is not specified
		'#getter' => function(ElggEntity $entity, $params) {
			return $entity->getIcon('medium');
		},

		// You can define how field value is extracted within an action
		// This value is then passed to validator and setter		
		'#input' => function() {
			$files = elgg_get_uploaded_files($name);
            if (empty($files)) {
            	return null;
            }
            		
            return array_shift($files);
		},
		
		// By default, field value will be as as metadata using field name
		// Custom setters can be used to sanitize user input values,
		// as well as change storage logic
		'#setter' => function(ElggEntity $entity, $value, $params) {
			return $entity->saveIconFromUploadedFile($value);
		},
		
		// You can apply custom field validation logic
		'#validator' => function(ElggEntity $entity, UploadedFile $file, $params) {
			if (!$file->isValid()) {
				throw new ValidationException("Uploaded file is invalid");
			}
			
			if (!in_array($file->getClientMimeType(), $supported_mimes) {
				throw new ValidationException("Invalid mime type");
			}
		
			return true;
		},
		
		// Dynamically determine if the field should be shown
        '#visibility' => function(ElggEntity $entity) {
        	return elgg_trigger_plugin_hook('uses:icon', "$entity->type:$entity->subtype", [], true);
        },
        
        // Display property value in the profile meta block
        '#profile' => false,
	];
	
	return $fields;
});
```

Field layout options:

```php
$field = [
	'#type' => 'date',
	'#section' => 'content', // 'header', 'content', 'footer' or 'sidebar'
	'#width' => 3, // 1 to 6, will add elgg-col-3of6 class
	'#priority' => 300, // default 500
];
```