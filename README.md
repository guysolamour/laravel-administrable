# Administrable

[![Packagist](https://img.shields.io/packagist/v/guysolamour/laravel-administrable.svg)](https://packagist.org/packages/guysolamour/laravel-administrable)
[![Packagist](https://poser.pugx.org/guysolamour/laravel-administrable/d/total.svg)](https://packagist.org/packages/guysolamour/laravel-administrable)
[![Packagist](https://img.shields.io/packagist/l/guysolamour/laravel-administrable.svg)](https://packagist.org/packages/guysolamour/laravel-administrable)

## French documentation is available [here](docs/doc-fr.md)

## Preamble

This package was created for my needs during my various projects, wanting to go faster and faster and not being a fan of CMS, I decided to make myself a back office (an administration) according to my needs and applying
my own conventions for not reinventing the wheel.
You must use a **versioning** system like **git** in order to be able to **rollback** as needed because the package modifies and adds certain files.
All files are generated in your working folder and can be changed at your convenience. The package just serves for
file generator.

## Package installation

Prerequisites

- PHP >= 7.4
- Laravel >= 7

Installation via composer

```bash
composer require guysolamour/laravel-administrable
```

## Use

### 1. Administration installation

```php
php artisan administrable:install
```

The ***--debug_packages*** option is used to add some development packages
such as (the debugbar and others). The default value is *false*

```php
php artisan administrable:make:crud {Model} --debug_packages="true"
```

or

```php
php artisan administrable:make:crud {Model} --debug_packages="false"
```

Par défaut le ***guard*** utilisé est ***admin*** et peut être changé en passant en argument de la commande le nom du ***guard*** à utiliser.

By default the **guard** used is **admin** and can be changed by passing in argument of the command the name of the **guard** to use.

```php
php artisan administrable:install client
```

### The options

- **locale**

The locale can be changed with the option *--locale*

```php
php artisan administrable:install {guard=admin} --locale="fr"
```

or with the short version

```bash
php artisan administrable:install {guard=admin} -l "fr"
```

**NB**: The local supported is French

- **generate**

By default the crud of articles (**Post**), messaging (**Mailbox**) and testimonials (**Testimonial**) are generated. This behavior can be modified with the *--generate* option.

**NB**: the models must be separated by a comma and the authorized values are: **Post**, **Mailbox** and **Testimonial**.

```php
php artisan administrable:install {guard=admin} --generate="Post,Mailbox,Testimonial"
```

or with the short version

```php
php artisan administrable:install {guard=admin} -g "Post,Mailbox,Testimonial"
```

The default is: *Post,Mailbox,Testimonial*

- **preset**

When generating the framework used for the front end is **view**. This behavior can be modified with the option ***--preset***.
The authorized values are: **view**, **react** and **bootstrap** in connection with the ***laravel/ui*** package.

```php
php artisan administrable:install {guard=admin} --preset="vue"
```

or with the short version

```php
php artisan administrable:install {guard=admin} -p "vue"
```

- **models**

By default, models are stored in the **App/Models** folder. This folder can be modified with the option ***--models***.
With the value of the name of the folder which must necessarily be located in the ***app*** folder at the root of the project.

**NB**: This folder is automatically created if it does not exist.

```php
php artisan administrable:install {guard=admin} --models="Models"
```

or with the short version

```php
php artisan administrable:install {guard=admin} -m "Models"
```

- **create_db**

To create database.

The *--create_db* with this ***db_connection://db_user:db_password@127.0.0.1:db_port/db_name***

To create database *administrable* for mysql.

```php
php artisan administrable:install {guard=admin} --create_db="mysql://root:root@127.0.0.1:3306/administrable"
```

To create database *administrable* for sqlite.

```php
php artisan administrable:install {guard=admin} --create_db="sqlite://administrable"
```

ou avec la version courte

```php
php artisan administrable:install {guard=admin} -d "mysql://root:root@127.0.0.1:3306/administrable"``
```

**NB:** Only *mysql* and *sqlite* are supported.

**seed**
To automatically seed the database, you must first configure the database access in the ***.env*** file.

```php
php artisan administrable:install {guard=admin} --seed
```

or with the short version

```php
php artisan administrable:install {guard=admin} -s
```

- **theme**

To change the administration theme (template). The available themes are: **adminlte**, **tabler**, **theadmin** and **themekit**

**NB:** The default theme is ***adminlte***.

**adminlte**
![adminlte](docs/img/adminlte.png?raw=true)

**cooladmin**
![cooladmin](docs/img/cooladmin.png?raw=true)

**tabler**
![tabler](docs/img/tabler.png?raw=true)

**theadmin**
![theadmin](docs/img/theadmin.png?raw=true)

**themekit**
![themekit](docs/img/themekit.png?raw=true)

```php
php artisan administrable:install {guard=admin} --theme="adminlte"
```

ou avec la version courte

```php
php artisan administrable:install {guard=admin} -t "adminlte"
```

You can now log in to the back office  ***domain/administrable***

Example: *http::localhost:8000/administrable*

The url can be changed in the administrable config file located in config folder.
You need to publish the service provider first (explain in step 2) and create guard (explain in step 5).

```php
/**
 * Administration routes prefix.
 */
'auth_prefix_path' => 'administrable',
```

### 2. Publication of config and assets (css and js)

Necessary for the theme.

```php
php artisan vendor:publish --provider="Guysolamour\Administrable\ServiceProvider"
```

### 3. Empty the cache

```php
php artisan route:clear && php artisan config:clear
```

### 4. Create the symbolic link for the media

```php
php artisan storage:link
```

### 5. Create guard

You need to create a guard entry in database before log in.

Example:

```php
php artisan administrable:create --username=johndoe --email=john@doe.fr --password=12345678
```

or with the short version

```php
php artisan administrable:create -u johndoe -e john@doe.fr -p 12345678
```

**NB:**

- You will be prompt if a option is not passed.
- available options are: *username*, *email* and *password*

### 6. Seed  the database

```php
php artisan db:seed --class ConfigurationsTableSeeder
```

If the **Post** crud was generated

```php
php artisan db:seed --class PostsTableSeeder
```

If the **Testimonial** crud was generated

```php
php artisan db:seed --class TestimonialsTableSeeder
```

If the **Mailbox** crud was generated

```php
php artisan db:seed --class MailboxesTableSeeder

```

The guard

```php
php artisan db:seed --class {guard}sTableSeeder
```

If the guard is admin:

```php
php artisan db:seed --class AdminsTableSeeder
```

## Crud

This command generates the crud (***model***, ***controller***, ***migration***, ***form***, ***views***, ***seed***) for a particular model.

```php
php artisan administrable:make:crud {Model}
```

The ***--migrate*** option is used to run artisan migrate command

```php
php artisan administrable:make:crud {Model} --migrate="true"
```

or

```php
php artisan administrable:make:crud {Model} --migrate="false"
```

**NB:**

- The default value is *true*.

To adapt the generation, a configuration file ***administrable.yaml*** located at the root of the project is used. This file uses the **Yaml** language. If you do not know this syntax you can go [to the official website] (https://www.yaml.org) to learn more.

Example of model declaration: ***Post***

```yaml
Post:
  name: { name: name, type: string, rules: nullable, slug: true }
  image: { name: image, type: image, rules: required }
  breadcrumb: name
  imagemanager:
    - front
    - back
    - images
```

***NB***: The name of the model is compulsory and must correspond to that defined in the configuration file.

### Overview

Only the name is required to declare a field (in some case).

```yaml
Post:
  name: {  }
```

```yaml
Post:
  name: { name: name }
```

However it can be optional if other keys are passed.

```yaml
Post:
  name: { type: string, rules: required }
```

### The type of the field

```yaml
Post:
  name: {  type: string }
```

The default type is: ***string***
The available types are:

| | | | |
|--|--|--|--|
| string | text | mediumText | longText
| date| datetime| boolean | enum |
| decimal| float| double| |
| integer| mediumInteger| bigInteger| polymorphic |
| ipAdress| image| relation| file |

For file type fields you can use the *file* or *image* type (just an alias)

```yaml
Post:
  name: {  type: file }
```

or

```yaml
Post:
  name: {  type: image }
```

### The validation

The validation rules are passed by the key ***rules*** and by default is an empty string.

```yaml
Post:
  name: { rules: required }
```

It uses the default validation rules of [laravel] (https://laravel.com/docs/validation).

Several rules must be separated by the pipe character **|**

```yaml
Post:
  name: { rules: required|string|min:2 }
```

For the rule ***required*** we can use the diminutive ***req*** (just an alias)

```yaml
Post:
  name: { rules: req|string|min:2 }
```

### Nullable

To make a field nullable

```yaml
Post:
  name: {  nullable: true }
```

or use the nullable validation rule

```yaml
Post:
  name: { rules: nullable }
```

- If a field has the nullable validation rule, the field is automatically nullable.
- If a field is nullable then it automatically has the nullable validation rule.
- No need to specify it in twice.

### Length

To define the length of a field

```yaml
Post:
  name: { length: 60 }
```

For the ***string*** (*text*, *mediumText*, *longText*) or ***int*** (*int*, *smallInt*, *BigInt*)  types,  you can pass the size of the field to the type separate by ***: ***.

```yaml
Post:
  name: {  type: string:60  }
```

```yaml
User:
  age: { type: int:3 }
```

### slug

To sluggify a field

```yaml
Post:
  name: {slug: true }
```

or pass the slug option at the model level with the name of the field to be sluggified which must be defined beforehand (***important***).

```yaml
Post:
  name: { name: name, type: string:125, rules: nullable}
  age: { type: int:3 }
  slug: name
```

**NB:**

- One of the two options can  be used.
- Only one field can be sluggable
- Only  (***text***, ***longText***, ***mediumText***) type fields can be sluggify

### edit slug

To have the slug field in form and edit it

```yaml
Post:
  name: {slug: true }
  edit_slug: true
```

Or use it globally. This will affect all models

```yaml
edit_slug: true
```

**NB:**

- The default value is *false*.
- A slug field is required on the model

### clone

To have a button in index vue to clone or duplicate a field

```yaml
Post:
  name: {slug: true }
  clone: true
```

Or use it globally. This will affect all models

```yaml
clone: true
```

**NB:**

- The default value is *true*.

### fillable

To use fillable or guarded property in the model

```yaml
Post:
  name: {slug: true }
  fillable: true
```

Or use it globally. This will affect all models

```yaml
fillable: true
```

**NB:**

- The default value is *true*.

### trans

The model can be translated with the *trans* option

```yaml
Post:
  name: { name: name, type: string:125, rules: nullable}
  age: { type: int:3 }
  trans: article
```

To field can be translated too.

```yaml
Post:
  name: { trans: nom }
```

**NB:** This translation is only used for display at view level.
If the option is not passed, the translation file in  **resources/lang**  in the current locale folder will be used.

### default value

Field's default value

```yaml
Post:
  name: { default: john }
```

### The actions

Sometimes we do not want to generate all actions (http verbs).

NB: The authorized values are: ***index***, ***show***, ***create***, ***edit*** and ***delete***

Actions can be separated by a , (*comma*)

```yaml
Post:
  actions: index,show,create,edit,delete
```

Actions can be separated by the character pipe |

```yaml
Post:
  actions: index|show|create|edit|delete
```

Actions can be declared as a list

```yaml
Post:
  actions:
    - index
    - show
    - create
    - edit
    - delete
```

### tinymce

To use the tinymce rich editor for a field

```yaml
Post:
  name: { tinymce: true }
```

### timestamps

By default for each model laravel manages the *created_at* and *updated_at* fields.
To deactivate it you can proceed as follows:

```yaml
Post:
  timestamps: false
```

It will add on the model the attribute

```php
protected $timestamps = false;
```

### imagemanger

To use the image manager for a specific model.

**NB**:

- A model can only use 03 collections (*front*, *back* and *images*)

To use all collections and default labels

```yaml
Post:
  imagemanager: true
```

You can choose the collection (s) to use

```yaml
Post:
    imagemanager:
      - front
      - back
      - images
```

Labels can be changed this way

With the comma syntax

```yaml
Post:
  imagemanager: { front: front image label, back: back image label, images: images label }
```

or with the other syntax

```yaml
Post:
    imagemanager:
      front: front image label
      back: back image label
      images: images label
```

### Icon

To change the icon used in the administration sidebar.

```yaml
Post:
  name: { name: name,trans: nom, default: john }
  icon: fa-folder
```

**NB**:

- The icon must be taken from the font awesome library.
- The default value is fa-folder

### The polymorphic type

To create a polymorphic field

```yaml
Post:
  name: { type: string, rules: required, type: polymorphic }
```

or

```yaml
Post:
  name: { rules: required, polymorphic: true }
```

The name of the polymorphic relation is the name of the field with the suffix ***able*** by default. This behavior can be modified with:

```yaml
Post:
  image:
    type: image
    name: mediaable
    rules: required
```

Polymorphic field names can be changed with

```yaml
Post:
  image:
    type: image
    model_id: imageable_id
    model_type: imageable_type
    rules: required
```

The *model_id* must have the suffix ***_ id*** and the *model_type* must have the suffix ***_ type***

**NB:** To generate the model and the migration, you will have to use the *entity* tag on model's definition.

```yaml
Post:
  name: { rules: required }
  entity: true
```

### Constraints

```yaml
Post:
  name: { constraints: nullable}
```

Constraints can be separated by  a comma *,*

```yaml
Post:
  name: { constraints: nullable,unique,index}
```

or with the list syntax

```yaml
Post:
  name:
    constraints:
       - nullable
       - unique
       - index
```

**NB**:

- The constraints must be valid according to the database management system.
- For the constraint ***set null***, no need to pass the field to ***nullable***. It’s done automatically.
- On the other hand, do not forget to remove the validation rule ***required*** if it is present because a field cannot be both ***required*** and ***nullable***.
- If the field is ***nullable*** no need to put it in the list of constraints and vice versa.
- For the ***default*** constraint  the value can be passed with **:**

```yaml
Post:
  age: { constraints: default:18 }
```

### seeder

To avoid generating a seeder file

```yaml
Post:
  age: { constraints: default:18}
  seeder: false
```

**breadcrumb**
For breadcrumb, the procedure is as follows:

```yaml
Post:
  age: { breadcrumb: true}
```

or pass the breadcrumb option at the model level with the name of the field to use

```yaml
Post:
  age: { name: age}
  breadcrumb: age
```

**NB**

- One of the two possibilities cannot be used.
- only one field can have this breadcrumb attribute
- The breadcrumb is only used in administration panel.

### Relationships

For relations the type ***relation*** is used

```yaml
Post:
  user_id: { type: relation, related: user }
```

The related is optional, we guest the name by remove the *_id*  on the field's name.
If the field doest not respect this convention, you have to use this option.

```yaml
Post:
  user_id: { type: relation }
```

Dans ce cas, le related sera **user**.

It is possible to use the full qualify class name.

```yaml
Post:
  user_id: { type: relation, related: App\Models\User }
```

You can change the deletion policy. OnDelete tag

```yaml
Post:
  user_id: { type: { relation: one to one, type: simple, related: user, property: name, onDelete: cascade }, rules: required }
```

You can change the key used for the foreign key.

```yaml
Post:
  user_id: { type: { relation: one to one, type: simple, related: user, property: name, references: id }, rules: required }
```

By default it is id which is used
**NB:**

- The authorized values are: ***cascade*** and ***set null***.
- No onDelete on a polymorphic type field.
- if a field must be polymorphic it will be necessary to create the linked model first before making the connection
- for relationship type fields if the related is not passed it is the name of the field which is used


### Type simple

#### One to One (simple)

```yaml
Post:
  user_id: { type: { relation: one to one, type: simple, related: user, property: name }, rules: required }
```

**NB:**

- The related is obligatory we must know to which field the relation is linked
- The related is mandatory and is the linked model. We can just pass the name
or the namespace.
- The property is the field used only for display and must exist on the linked model.

### Foreign keys

- For the model we are going to create

```yaml
Post:
  user_id:
    type:
      relation: many to many
      type: simple
      related: user
      property: name
      intermediate_table: post_user
      local_keys: { foreign_key: kguy_id, local_key: kid }
    rules: required
```

- For the model to be linked

```yaml
Post:
  user_id:
    type:
      relation: many to many
      type: simple
      related: user
      property: name
      intermediate_table: post_user
      related_keys:  { foreign_key: guy_id, other_key: id }
    rules: required
```

#### One to Many (simple)

```yaml
Post:
  user_id: { type: { relation: one to many, type: simple, related: user, property: name }, rules: required }
```

**NB:**

- The related is obligatory (we must know to which field the relation is linked)
- The related is mandatory and is the linked model. We can just pass the name
or the full namespace.
- The property is the field used only for display and must exist on the linked model.

#### Foreign keys

- For the model we are going to create

```yaml
Post:
  user_id:
    type:
      relation: many to many
      type: simple
      related: user
      property: name
      intermediate_table: post_user
      local_keys: { foreign_key: kguy_id, local_key: kid }
    rules: required
```

- For the model that will be linked

```yaml
Post:
  user_id:
    type:
      relation: many to many
      type: simple
      related: user
      property: name
      intermediate_table: post_user
      related_keys:  { foreign_key: guy_id, other_key: id }
    rules: required
```

**Many to Many (simple)**

```yaml
Post:
  user_id: { type: { relation: many to many, type: simple, related: user, property: name }, rules: required }
```

**NB:**

- The pivot table is automatically generated.
- You can change the name of the pivot intermediate table.

```yaml
Post:
  user_id:
    type:
      relation: many to many
      type: simple
      related: user
      property: name
      intermediate_table: post_user
    rules: required
```

#### Foreign keys

- For the model we are going to create

```yaml
Post:
  user_id:
    type:
      relation: many to many
      type: simple
      related: user
      property: name
      intermediate_table: post_user
      local_keys: { foreign_key: terrain_id, join_key: user_id }
    rules: required
```

- For the model that will be linked

```yaml
Post:
  user_id:
    type:
      relation: many to many
      type: simple
      related: user
      property: name
      intermediate_table: post_user
      related_keys: { foreign_key: user_id, join_key: terrain_id }
    rules: required
```

If the keys are passed and the intermediate table (pivot) is not then this table will be created with the convention.

### One to One (polymorphic)

```yaml
Post:
  user_id:
    type:
      relation: one to one
      type: polymorphic
      related: user
      property: name
    rules: required
```

### One to Many (polymorphic)

```yaml
Post:
  user_id:
    type:
      relation: one to many
      type: polymorphic
      related: user
      property: name
    rules: required
```

### Many to Many polymorphic

The ***many to many*** polymorphic relationship is not yet established. I haven't had to use it in my various projects yet.

## Append Crud

To append one or many fields on an existing model.

```php
php artisan administrable:append:crud {model} --fields="field1,field2,field3"
```

with the short version

```php
php artisan administrable:append:crud {model} -f="field1,field2,field3"
```

**NB:**

- The model must exists
- The fields must be defined in the same way as that of the **administrable:make:crud** command in the  *administrable.yaml* file

## Rollback Crud

Rollback a crud

```php
php artisan administrable:rollback:crud {model}
```

The ***--rollback*** option is used to run the rollback artisan command. The default value is *true*

```php
php artisan administrable:make:crud {Model} --rollback="true"
```

or

```php
php artisan administrable:make:crud {Model} --rollback="false"
```

**NB:**

- The model must have been defined in *administrable.yaml* file.

Example:

```php
php artisan administrable:rollback:crud Post
```

By default the rollback artisan command will be run, if you want to change it.

```php
php artisan administrable:rollback:crud Post --rollback
```

or with the short versioning

```php
php artisan administrable:rollback:crud Post -r
```

## Security

If you discover security issues, please send an email instead of using the issue system. The email is available in the file *composer.json*

## Credits

- [Laravel Administrable](https://github.com/guysolamour/laravel-administrable)
- [All contributors](https://github.com/guysolamour/admin/graphs/contributors)

This package is bootstrapped with the help of
[melihovv/laravel-package-generator](https://github.com/melihovv/laravel-package-generator).
