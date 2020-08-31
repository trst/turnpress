# TurnPress

A silly little WordPress headless starter "_theme_".

This is hardly a project. I found myself creating a few headless
WordPress sites over and over again. This repository saves me from having to
copy and paste, over and over again AND from needing to look up all of
the dependencies I need.

Composer to the rescue.

If you want to get fancy, use [DDEV][1] to help bring the whole thing to
life and test/develop locally.

## Getting Started

1. Clone this repository. Yes, this one.
  ```bash
  git clone https://github.com/trst/turnpress
  ```
2. Update the dependencies via `composer update` (you might want to add or remove packages/plugins/themes based on the needs of your project)
3. Install dependencies via `composer install`

### Optional but totally recommended:

- Wiping out the `.git` folder, and tracking it by setting your own remote.
- Adjusting the `.gitignore` to include/exclude files from the repository.

[1]: https://www.ddev.com/
