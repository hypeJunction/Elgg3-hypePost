hypePost
========

Provides utilities for creating and editing posts.

**Extendable form fields**
Post forms are built using an extended field API layer, which makes it easier to define
all fields in one place, and not have to worry about maintaining form, action and profile
logic separate from each other.
 
 
## Form Fields

To extend post form, use ``fields, <entity_type>:<entity_subtype>`` (or less granular ``fields, <entity_type>`` ) plugin hook.
 
The hook receives an instance of ``\hypeJunction\Fields\Collection``, which allows you to easily manipulate fields:

```php
elgg_register_plugin_hook_handler('fields', 'object:blog', function(\Elgg\Hook $hook) {

	$fields = $hook->getValue();
	/* @var $fields \hypeJunction\Fields\Collection */
	
	$fields->add('published_date', new MetaField([
		'type' => 'date',
		'required' => true,
	]);
	
	$fields->get('description')->required = false;
	
	return $fields;
});
```


## Features

**Icons**

To enable or disable icons, use ``uses:icon, <entity_type>:<entity_subtype>`` hook. The handler should return `true` or `false`

**Cover Images**

To enable or disable cover images, use ``uses:cover, <entity_type>:<entity_subtype>`` hook. The handler should return `true` or `false`

**Comments**

To enable or disable comments, use ``uses:comments, <entity_type>:<entity_subtype>`` hook. The handler should return `true` or `false`