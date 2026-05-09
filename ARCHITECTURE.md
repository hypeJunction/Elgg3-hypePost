# hypePost — Architecture (Elgg 4.x)

## Summary

hypePost is a utility plugin providing a generic post/content model for Elgg.
It is a **library plugin** — it ships no entity subtypes of its own, but provides
a reusable `fields` hook pipeline, a `Model` service for form handling, a `Post`
service for cover images / metatags / modules, and shared view partials that
other plugins (hypeWall, hypeDiscussions, etc.) extend.

---

## Directory Structure

```
hypepost/
├── classes/hypeJunction/
│   ├── Post/
│   │   ├── Bootstrap.php          # DefaultPluginBootstrap; registers parsley.js view in boot()
│   │   ├── Model.php              # Form save/validate service (DI: posts.model)
│   │   ├── Post.php               # Cover/metatag/module service (DI: posts.post)
│   │   ├── CoverWrapper.php       # Composite cover object (implements JsonSerializable)
│   │   ├── AddProfileModulesField.php  # Hook: fields/object,group,user
│   │   ├── DefineCoverSizes.php   # Hook: entity:cover:sizes/all
│   │   ├── DeleteCoverAction.php  # Action: cover/delete
│   │   ├── EntityMenu.php         # Hook: register/menu:entity
│   │   ├── PopulateExportData.php # Hook: adapter:entity/all
│   │   ├── River.php              # Helper: adds river items
│   │   ├── SaveEditHistory.php    # Event: update/object → annotates edit_history
│   │   ├── SavePostAction.php     # Action: post/save (generic form handler)
│   │   ├── SetObjectFields.php    # Hook: fields/object (adds title/desc/access/cover/tags)
│   │   └── SocialMenu.php         # Hook: register/menu:social
│   ├── Fields/                    # Generic field type library
│   │   ├── FieldInterface.php
│   │   ├── Field.php              # Base field class (CRUD, visibility, validation)
│   │   ├── Collection.php         # ArrayAccess/Iterator collection of FieldInterface
│   │   ├── AccessField.php, BooleanField.php, CoverField.php, ...
│   │   └── MetadataStorage.php
│   ├── Validators/                # Standalone validators (Email, Length, Number, Url)
│   └── ValidationException.php
├── views/default/
│   ├── forms/post/save.php        # Generic post form
│   ├── input/                     # cover, cancel, profile_modules, range inputs
│   ├── page/layouts/post/         # Post page layout
│   ├── post/                      # cover, layout, module, view, card elements
│   ├── post/template/             # default and static_page templates
│   └── resources/post/            # add, edit, view resource views
├── docker/                        # Per-plugin Elgg 4.x test stack
└── elgg-plugin.php
```

---

## Services (elgg-services.php)

| ID | Class | Notes |
|----|-------|-------|
| `posts.post` | `hypeJunction\Post\Post` | Cover, metatags, modules, edit history |
| `posts.model` | `hypeJunction\Post\Model` | Generic form save/validate via fields pipeline |

Both expose `static instance(): self` backed by `elgg()->get()`.

---

## Hooks (plugin hooks)

| Hook | Type | Handler | Purpose |
|------|------|---------|---------|
| `fields` | `object` | `AddProfileModulesField` | Adds sidebar profile modules field |
| `fields` | `group` | `AddProfileModulesField` | Same for groups |
| `fields` | `user` | `AddProfileModulesField` | Same for users |
| `fields` | `object` | `SetObjectFields` | Adds title, description, access, cover, icon, tags, meta |
| `entity:cover:sizes` | `all` | `DefineCoverSizes` | Defines master (1280×720) + original cover sizes |
| `register` | `menu:social` | `SocialMenu` | Adds likes/comment-toggle items |
| `register` | `menu:entity` | `EntityMenu` | Adds cover delete item when editable |
| `adapter:entity` | `all` | `PopulateExportData` | Exports fields via export context |

---

## Events

| Event | Type | Handler | Purpose |
|-------|------|---------|---------|
| `update` | `object` | `SaveEditHistory` | Annotates entity state on every update |

---

## Actions

| Route | Handler | Notes |
|-------|---------|-------|
| `post/save` | `SavePostAction` | Generic form save via Model service |
| `cover/delete` | `DeleteCoverAction` | Delete cover icon from entity |

---

## Routes

| Name | Path | Resource | Public |
|------|------|----------|--------|
| `view:post` | `/post/view/{guid}` | `resources/post/view` | yes |

---

## Dependencies

| Plugin | Required | Notes |
|--------|----------|-------|
| hypeajax | no | JS only (`ajax/Form` AMD module in save.js) |
| hypetime | no | JS only |
| hypescraper | no | Optional fallback for cover URL from web resources |

---

## Migration Notes (3.x → 4.x)

- `ServiceFacade` trait removed from `Post` and `Model`; replaced with manual `static instance()` calling `elgg()->get('service.id')`.
- `\DI\object()` → `\DI\create()` in `elgg-services.php`.
- All hook/event registrations moved from `Bootstrap::init()` to declarative `hooks`/`events` arrays in `elgg-plugin.php`.
- `Bootstrap` reduced to `DefaultPluginBootstrap`; only registers parsley.js view in `boot()`.
- `manifest.xml` and `autoloader.php` deleted; `composer.json` is the sole metadata source.
- PSR-0 autoload upgraded to PSR-4 (`hypeJunction\` → `classes/hypeJunction/`).
- Unused `use hypeJunction\Scraper\WebResource` import removed from `CoverWrapper.php` (fixes bug xz3a).
- `elgg-plugin.php` parsley.js view registration moved to `Bootstrap::boot()` using `elgg_get_config('path')`.

## Seeding

No seeder required. This plugin owns no entity types, subtypes, or persistent relationship schemas — it is a pure UI/utility/admin plugin with no persisted entity surface of its own.
