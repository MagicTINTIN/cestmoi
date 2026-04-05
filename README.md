# C'est moi
> Module présent sur les sites de MagicTINTIN permettant de gérer les achievements

## Module setup

```sh
# at the repo root:
git submodule add git@github.com:MagicTINTIN/cestmoi.git cestmoi
git submodule update --init --recursive
```

```php
// import all functions
include_once("cestmoi/main.php");

// then in the head
cestmoi_setup_head();

// just before </body>
cestmoi_setup_foot();
```