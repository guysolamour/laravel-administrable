﻿# Administrable

[![Packagist](https://img.shields.io/packagist/v/guysolamour/laravel-administrable.svg)](https://packagist.org/packages/guysolamour/laravel-administrable)
[![Packagist](https://poser.pugx.org/guysolamour/laravel-administrable/d/total.svg)](https://packagist.org/packages/guysolamour/laravel-administrable)
[![Packagist](https://img.shields.io/packagist/l/guysolamour/laravel-administrable.svg)](https://packagist.org/packages/guysolamour/laravel-administrable)

## La documentation anglaise est disponible [ici](README.md)

## Préambule

Ce package a été créé pour mes besoins lors de mes différents projets, voulant aller de plus en plus vite et n'étant pas fan des CMS, j'ai décidé de me faire un back office (une administration) en fonction de mes besoins et en appliquant
mes propres conventions pour ne pas réinventer la roue.
Vous devez utiliser un système de ***versionning*** comme ***git*** afin de pouvoir ***rollback*** au besoin parce que le package modifie et ajoute certains fichiers.
Tous les fichiers sont générés dans votre dossier de travail et
peut etre modifié à votre convenance. Le package sert juste de
générateur de fichier.

## Installation du package

Préréquis

- PHP >= 7.4
- Laravel >= 7

Installation via composer

```bash
composer require guysolamour/laravel-administrable
```

## Usage

### 1. Installation de l'administration

```php
php artisan administrable:install
```

L'option ***--debug_packages*** sert à rajouter des packages de développement tels que
(la debugbar et autres). La valeur par défaut est *false*

```php
php artisan administrable:make:crud {Model} --debug_packages
```


Par défaut le ***guard*** utilisé est ***admin*** et peut être changé en passant en argument de la commande le nom du ***guard*** à utiliser.

```php
php artisan administrable:install client
```

### Les options

- **locale**

La locale peut être changée avec l'option *--locale*

```php
php artisan administrable:install {guard=admin} --locale="fr"
```

ou avec la version courte

```bash
php artisan administrable:install {guard=admin} -l "fr"
```

**NB**: La locale supportée est le francais

- **generate**

Par défaut le crud des articles (**Post**), messagerie (**Mailbox**) et les témoignages (**Testimonial**) sont générés. Ce comportement peut être modifié avec l'option *--generate*.

**NB**: les modèles doivent être séparés par une virgule et les valeurs autorisées sont: **Post**, **Mailbox** et **Testimonial**.

```php
php artisan administrable:install {guard=admin} --generate="Post,Mailbox,Testimonial"
```

ou avec la version courte

```php
php artisan administrable:install {guard=admin} -g "Post,Mailbox,Testimonial"
```

La valeur par défaut est: *Post,Mailbox,Testimonial*

- **preset**

Lors de la génération le framework utilisé pour la partie front est **vue**. Ce comportement peut être modifié avec l'option ***--preset***.
Les valeurs autorisées sont: **vue**, **react** et **bootstrap** en rapport avec le package ***laravel/ui***.

```php
php artisan administrable:install {guard=admin} --preset="vue"
```

ou avec la version courte

```php
php artisan administrable:install {guard=admin} -p "vue"
```

- **models**

Par défaut les models sont stockés dans le dossier **App/Models**.
Ce dossier peut être modifié avec l'option ***--models***.
Avec pour valeur le nom du dossier qui doit forcement être situé dans le dossier ***app*** à la racine du projet.

**NB**: Ce dossier est automatiquement créé s'il n'existe pas.

```php
php artisan administrable:install {guard=admin} --models="Models"
```

ou avec la version courte

```php
php artisan administrable:install {guard=admin} -m "Models"
```

- **create_db**

Pour créer automatiquement la base de base de donnée.

Il faudra passer cette chaine ***db_connection://db_user:db_password@127.0.0.1:db_port/db_name*** à l'option *--create_db*

Pour créer une base de donnée *administrable* pour mysql.

```php
php artisan administrable:install {guard=admin} --create_db="mysql://root:root@127.0.0.1:3306/administrable"
```

Pour créer une base de donnée *administrable* pour sqlite.

```php
php artisan administrable:install {guard=admin} --create_db="sqlite://administrable"
```

ou avec la version courte

```php
php artisan administrable:install {guard=admin} -d "mysql://root:root@127.0.0.1:3306/administrable"
```

**NB:** Seuls *mysql* and *sqlite* sont supportés.

- **seed**

Lancer automatiquement le seeding de la base de donnée. Il faudra au préalable configurer les accès de la base de donnée dans le fichier ***.env***

```php
php artisan administrable:install {guard=admin} --seed
```

ou avec la version courte

```php
php artisan administrable:install {guard=admin} -s
```

- **theme**

Pour changer le thème à utiliser pour l'administration. Les thèmes disponible sont: **adminlte**, **tabler**, **theadmin** et **themekit**

**NB:** La thème par défaut est ***adminlte***.

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

Vous pouvez maintenant vous connecter au back office ***domain/administrable***

Exemple: *http::localhost:8000/administrable*

L'url peut être changé dans le fichier de configuration administrable disponible dans le dossier config.
Vous devez au préalable publier le service provider (expliquer à l'étape 2) et créer le guard (expliquer à l'étape 5).

```php
/**
 * Administration routes prefix.
 */
'auth_prefix_path' => 'administrable',
```

### 2. Publication de la config et des assets (css et js)

Nécessaire pour le fonctionnement du thème.

```php
php artisan vendor:publish --provider="Guysolamour\Administrable\ServiceProvider"
```

### 3. Vider le cache

```php
php artisan route:clear && php artisan config:clear
```

### 4. Créer le lien symbolique pour les médias

```php
php artisan storage:link
```

### 5. Créer le guard

Vous devez créer un guard en base de donnée afin de pouvoir vous connecter.

Exemple:

```php
php artisan administrable:create --username=johndoe --email=john@doe.fr --password=12345678
```

or avec la version courte

```php
php artisan administrable:create -u johndoe -e john@doe.fr -p 12345678
```

**NB:**

- Si une option n'est pas passée, elle vous sera demandé interactivement.
- Les options disponible sont: *username*, *email* et *password*

### 6. Seed de la base de donnée

```php
php artisan db:seed --class ConfigurationsTableSeeder
```

Si le crud **Post** a été généré

```php
php artisan db:seed --class PostsTableSeeder
```

Si le crud **Testimonial** a été généré

```php
php artisan db:seed --class TestimonialsTableSeeder
```

Si le crud **Mailbox** a été généré

```php
php artisan db:seed --class MailboxesTableSeeder
```

Le guard

```php
php artisan db:seed --class {guard}sTableSeeder
```

Si le guard est admin:

```php
php artisan db:seed --class AdminsTableSeeder
```

## Crud

Cette commande génère le crud (modèle, controller, migration, formulaire, vues, seed) pour un modèle en particulier.

```php
php artisan administrable:make:crud {Model}
```

L'option ***--migrate*** sert à exécuter la commande artisan migrate

```php
php artisan administrable:make:crud {Model} --migrate="true"
```

ou

```php
php artisan administrable:make:crud {Model} --migrate="false"
```

**NB:**

- La valuer par défaut est *true*.

Pour adapter la génération, un fichier de configuration ***administrable.yaml*** se trouvant à la racine du projet est utilisé. Ce fichier utilise le langage **Yaml**. Si vous ne maitriser cette syntaxe vous pouvez vous rendre [sur le site officiel](https://www.yaml.org) pour en apprendre davantage.

Exemple de déclaration de modèle appelé ***Post***

```yaml
Post:
  name: { name: name, type: string:400, rules: nullable, slug: true, breadcrumb: true }
  image: { name: image, type: image, rules: required }
  # imagemanager: { front: Image Front Yml, back: Image Back Yml, images: Images }
  # imagemanager: true
  imagemanager:
    - front
    - back
    # - images
```

***NB***: Le nom du modèle est obligatoire et doit correspondre à celui défini dans le fichier de configuration.

### Tour d'horizon

Seul le nom est obligatoire pour déclarer un champ.

```yaml
Post:
  name: {  }
```

```yaml
Post:
  name: { name: name }
```

Cependant il peut être facultatif si d'autres clés sont passés.

```yaml
Post:
  name: { type: string, rules: required }
```

### Le type du champ

```yaml
Post:
  name: {  type: string }
```

Le type par défaut est : ***string***
Les types disponible sont:

| | | | |
|--|--|--|--|
| string | text | mediumText | longText
| date| datetime| boolean | enum |
| decimal| float| double| |
| integer| mediumInteger| bigInteger| polymorphic |
| ipAdress| image| relation| file |

Pour les champs de type file on peut utiliser le type *file* ou *image* (juste un alias)

```yaml
Post:
  name: {  type: file }
```

ou

```yaml
Post:
  name: {  type: image }
```

### La validation

Les règles de validation  sont passées par la clé ***rules*** et par défaut est une chaine de caractère vide.

```yaml
Post:
  name: { rules: required }
```

Elle utilise les règles de validation par défaut de [laravel](https://laravel.com/docs/validation).

Plusieurs règles doivent être séparées par le caractère **|** comme avec laravel

```yaml
Post:
  name: { rules: required|string|min:2 }
```

Pour la règle ***required*** on peut utiliser le diminutif ***req*** (juste un alias)

```yaml
Post:
  name: { rules: req|string|min:2 }
```

### nullable

Pour rendre un champ  nullable

```yaml
Post:
  name: {  nullable: true }
```

ou utiliser la règle de validation nullable

```yaml
Post:
  name: { rules: nullable }
```

Si un champ a la règle de validation nullable, le champ est automatiquement nullable.
Si un champ est nullable alors il a automatiquement la règle de validation nullable.
Pas besoin de le spécifier à deux endroits.

### length

Pour définir la longueur d'un champ

```yaml
Post:
  name: { length: 60 }
```

Pour les types ***string***  (*text*, *mediumText*, *longText*) ou ***int*** (*int*, *smallInt*, *BigInt*) on peut passer la taille du champ  au type séparé de ***:*** .

```yaml
Post:
  name: {  type: string:60  }
```

```yaml
User:
  age: { type: int:3 }
```

### slug

Pour sluggifier un champ

```yaml
Post:
  name: {slug: true }
```

ou bien passer l'option slug au niveau du modèle avec le nom du champ à sluggifier qui doit être au préalable défini (important).

```yaml
Post:
  name: { name: name, type: string:125, rules: nullable}
  age: { type: int:3 }
  slug: name
```

**NB:**

- Une des deux options ne peut être utilisé.
- un seul champ peut avoir ce attribut
- Seul les champs de type (***text*** , ***longText***, ***mediumText***) peuvent être sluggifier

### edit slug

Pour avoir le champ slug dans le formulaire afin de le modifer

```yaml
Post:
  name: {slug: true }
  edit_slug: true
```

ou bien le passer globalement. Celui affectera alors tous les modèles

```yaml
edit_slug: true
```

**NB:**

- La valeur par défaut est *false*.
- La présence d'un champ slug sur le modèle est obligatoire

### clone

Pour avoir un boutton pour cloner ou dupliquer un champ sur la vue index

```yaml
Post:
  name: {slug: true }
  clone: true
```

ou bien le passer globalement. Cela affectera alors tous les modèles

```yaml
clone: true
```

**NB:**

- La valeur par défaut est *true*.

### Fillable

Pour utiliser la propriété fillable ou guarded dans le modèle

```yaml
Post:
  name: {slug: true }
  fillable: true
```

ou bien le passer globalement. Cela affectera alors tous les modèles

```yaml
fillable: true
```

**NB:**

- La valeur par défaut est *true*.

### trans

Le modèle peut etre traduit avec l'option *trans*

```yaml
Post:
  name: { name: name, type: string:125, rules: nullable}
  age: { type: int:3 }
  trans: article
```

Pour  traduire un champ la clé *trans* est utilisé.

```yaml
Post:
  name: { trans: nom }
```

**NB:** Cette traduction est utilisé que pour l'affichage au niveau des vues.
Si l'option n'est pas passé, le fichier de traduction du dossier **lang** au
dans le dossier **resources** de la locale en cours sera utilisé.

### valeur par défaut

Pour la valeur par défaut d'un champ

```yaml
Post:
  name: { default: john }
```

### Les actions

Il arrive qu'on ne souhaite pas générer toutes les actions (les verbes http).

NB: Les valeurs autorisées sont: ***index***, ***show***, ***create***, ***edit*** et ***delete***

Les actions peuvent être séparées par une , (*virgule*)

```yaml
Post:
  actions: index,show,create,edit,delete
```

Les actions peuvent être séparées par le caractère | (*pipe*)

```yaml
Post:
  actions: index|show|create|edit|delete
```

Les actions peuvent être déclarées sous forme de liste

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

Pour utiliser l'éditeur de texte enrichi tinymce sur un champ.

```yaml
Post:
  name: { tinymce: true }
```

### timestamps

Par défaut pour chaque modèle laravel gère les champs created_at et updated_at.
Pour le désactiver vous pouvez procéder comme suit:

```yaml
Post:
  timestamps: false
```

Celui ci ajoutera sur le modèle l'attribut

```php
protected $timestamps = false;
```

### imagemanger

Pour autoriser l'utilisation du Gestionnaire d'image pour un modèle.
Un modèle ne peut avoir que 03 collections (front, back et images)
Un label doit etre associe a chaque collection pour l'affichage dans les vues.

Pour utiliser toutes les collections et les lables par défaut

```yaml
Post:
  imagemanager: true
```

On peut choisir la/les collections à utiliser

```yaml
Post:
    imagemanager:
      - front
      - back
      - images
```

Les labels peuvent etre changés de cette manière

Avec la syntaxe à virgule

```yaml
Post:
  imagemanager: { front: front image label, back: back image label, images: images label }
```

ou avec l'autre syntaxe

```yaml
Post:
    imagemanager:
      front: front image label
      back: back image label
      images: images label
```

### icône

Pour changer l’icône utiliser dans la sidebar de l'administration.

```yaml
Post:
  name: { name: name,trans: nom, default: john }
  icon: fa-folder
```

NB: L'icone doit etre tiré de la librairie font awesome. La valeur par défaut est fa-folder

### Le type polymorphic

Pour créer un champ de type polymorphic

```yaml
Post:
  name: { type: string, rules: required, type: polymorphic }
```

ou

```yaml
Post:
  name: { rules: required, polymorphic: true }
```

Le nom de la relation polymorphique est le nom du champ avec le suffixe ***able*** par défaut. Ce comportement peut être modifié avec:

```yaml
Post:
  image:
    type: image
    name: mediaable
    rules: required
```

Les noms des champs polymorphique peuvent être changé avec

```yaml
Post:
  image:
    type: image
    model_id: imageable_id
    model_type: imageable_type
    rules: required
```

Le *modele_id* doit avoir le suffixe ***_id*** et le *model_type* doit avoir le suffixe ***_type***

**NB:** Pour générer que le modèle et la migration il faudra utiliser le tag *entity* sur la définition du modèle.

```yaml
Post:
  name: { rules: required }
  entity: true
```

### Contraintes

```yaml
Post:
  name: { constraints: nullable}
```

Les contraintes peuvent être séparées par une virgule

```yaml
Post:
  name: { constraints: nullable,unique,index}
```

ou en liste

```yaml
Post:
  name:
    constraints:
       - nullable
       - unique
       - index
```

**NB**:

- Les contraintes doivent être valide en fonction du système de gestion de base de donnée.
- Pour  la contrainte ***set null***,  pas besoin de passer le champ à ***nullable***. C'est fait automatiquement. Par contre ne pas oublier de retirer la règle de validation ***required*** si elle est présente  car un champ ne peut être à la fois ***required*** et ***nullable***.
- Si le champ est ***nullable*** pas besoin de le mettre dans la liste des contraintes et vice versa.
- Pour la contrainte ***default*** la valeur peut être passé  avec **:**

```yaml
Post:
  age: { constraints: default:18}
```

### fichier de seed

Pour ne pas générer de fichier seeder pour un modèle, vous pouvez procéder comme suit:

```yaml
Post:
  age: { constraints: default:18}
  seeder: false
```

**Fil d’araine (breadcrumb)**
Pour le breadcrumb, la procédure est la suivante:

```yaml
Post:
  age: { breadcrumb: true}
```

ou bien passer l'option breadcrumb au niveau du modèle avec le nom du champ à utiliser

```yaml
Post:
  age: { name: age}
  breadcrumb: age
```

**NB:**

- Une des deux possibilités ne peut être utilisée.
- un seul champ peut avoir ce attribut breadcrumb

### Les relations

Pour les relations le type ***relation*** est utilisé

```yaml
Post:
  user_id: { type: relation, related: user }
```

Le related n'est pas oblgatoire, il sera defini par le nom du champ
auquel on retire le _id. si votre champ ne respecte pas cette convention vous devriez alors utiliser le related

```yaml
Post:
  user_id: { type: relation }
```

Dans ce cas, le related sera **user**.

C'est possible d'utiliser le namespace complet.

```yaml
Post:
  user_id: { type: relation, related: App\Models\User }
```

On peut changer la politique de suppression. Le tag onDelete

```yaml
Post:
  user_id: { type: { relation: one to one, type: simple, related: user, property: name, onDelete: cascade }, rules: required }
```

On peut changer la clé utilisé pour la clé étrangère.

```yaml
Post:
  user_id: { type: { relation: one to one, type: simple, related: user, property: name, references: id }, rules: required }
```

Par défaut c'est id qui est utilisé

**NB:**

- Les valeurs autorisées sont: ***cascade*** et ***set null***.
- Pas de onDelete sur un champ de type polymorphic.
- si un champ doit etre polymorphic il faudra creer dabord le modèle lié avant de faire la liaison
- pour les champs de type relation si le related n'est pas passé c'est le nom du champ qui est utilisé

### Type simple

#### One to One (simple)

```yaml
Post:
  user_id: { type: { relation: one to one, type: simple, related: user, property: name }, rules: required }
```

**NB:**

- le related est obligatoire on doit savoir à quel champ est lié la relation
- Le related est obligatoire et est le modele lié. On peut passer juste le nom
ou le namespace.
- Le property est le champ utilisé rien que pour l'affichage et doit exister sur le modèle lié.

### Clés étrangères

- Pour le modele qu'on va créer

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

- Pour le modèle qui sera lié

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

- le related est obligatoire on doit savoir à quel champ est lié la relation
- Le related est obligatoire et est le modele lié. On peut passer juste le nom
ou le namespace.
- Le property est le champ utilisé rien que pour l'affichage et doit exister sur le modèle lié.

#### Clés étrangères

- Pour le modele qu'on va créer

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

- Pour le modele qui sera lié

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

- La table pivot est automatiquement générée.
- On peut changer le nom de la table de pivot
intermediate_table: terrain_caca

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

**Clés étrangères**

- Pour le modele qu'on va créer

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

- Pour le modele qui sera lié

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

si les clés sont passées et que la table intermédiaire (pivot) ne l'est pas alors cette table sera créée avec
la convention.

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

La relation polymorphique ***many to many*** n'est pas encore implantée. J'ai pas encore eu à l'utiliser dans mes différents projets.

## Append Crud

Pour ajouter un ou Plusieurs champs sur un modèle existant.

```php
php artisan administrable:append:crud {model} --fields="field1,field2,field3"
```

ou avec la version courte

```php
php artisan administrable:append:crud {model} -f="field1,field2,field3"
```

**NB:**

- Le modèle doit exister
- Les champs doivent être définis de la même manière que celle de la commande **administrable:make:crud** dans le fichier de *administrable.yaml*

## Rollback Crud

Revenir en arrière après un crud. La valeur par défaut est *true*

```php
php artisan administrable:rollback:crud {model}
```

L'option ***--rollback*** sert à exécuter la commande artisan rollback

```php
php artisan administrable:make:crud {Model} --rollback="true"
```

ou

```php
php artisan administrable:make:crud {Model} --rollback="false"
```

**NB:**

- Le modèle doit être défini dans le fichier *administrable.yaml*.

Exemple:

```php
php artisan administrable:rollback:crud Post
```

Par défaut la commande artisan rollback sera exécutée, si vous voulez changer ce comportement.

```php
php artisan administrable:rollback:crud Post --rollback
```

ou avec la syntaxe courte

```php
php artisan administrable:rollback:crud Post -r
```

## Déploiement

Pour déployer l'application, il faudra utiliser cette commande qui vas générer les
différents scripts.

```php
php artisan administrable:deploy
```

Par défaut, les scripts seront stockés dans le dossier ***.deployment*** à la racine du projet.

L'option ***--path*** sert à changer le chemin.

```php
php artisan administrable:deploy --path=".deployment"
```

ou avec la version courte

```php
php artisan administrable:deploy -d=".deployment"
```

Cette commande stockera les scripts de déploiement dans le dossier choisi.
Un fichier Makefile sera mis à la racine de ce projet contenant toutes les commandes disponibles.

**NB:**

- Le chemin doit être relatif au dossier du projet en cours
- Par défaut le chemin est *.deployment*

Vous pouvez aussi passer l'adresse IP du serveur avec cette option

```php
php artisan administrable:deploy --server="000.000.000.000"
```

ou avec la version courte

```php
php artisan administrable:deploy -s="000.000.000.000"
```

Lors du déploiement, les données sensibles (mot de passe, token et autres) seront cryptés pour leurs protections.
L'option ***--password*** permettra d'enregistrer le mot de passe qui permettra de décrypter ces données.

```php
php artisan administrable:deploy --password="mypassword"
```

ou avec la version courte

```php
php artisan administrable:deploy -p="mypassword"
```

Le mot de passe seran enregistré dans le fichier ***.vault-pass*** à la racine du projet. Ce fichier ne doit pas être versionné.

## Backup

Une commande existe pour déployer pour enregistrer tous les fichiers contenus dans le dossier storage et l'envoyer par ftp.

```php
php artisan administrable:storage:dump
```

Cette commande sera lancée automatiquement toutes les semaines.

## Sécurité

Si vous découvrez des problèmes liés à la sécurité, veuillez envoyer un e-mail au lieu d'utiliser le système de issue. Le mail est disponible dans le fichier *composer.json*
