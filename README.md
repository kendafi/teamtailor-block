# Teamtailor Block

This is a WordPress plugin that enables a custom Gutenberg block
to add a Teamtailor widget displaying job listings in any page or post.

It generates the same HTML as the Teamtailor widget code described in their
[documentation](https://support.teamtailor.com/en/articles/129677-integrate-a-job-list-widget),
but with the options you set and maintain in the WordPress content editor.

## Requirements

A Teamtailor API key is required to use the block.
You can create one in your Teamtailor account settings.
Please select `EU Region`, and set the permissions `Public`, and type `Read`. See
[Teamtailors instructions how to create an API key](https://support.teamtailor.com/en/articles/5963369-use-our-teamtailor-api).

The block is registered using the
[PHP-only block registration method](https://make.wordpress.org/core/2026/03/03/php-only-block-registration/)
introduced in WordPress 7.0 released on 20 May 2026.
That or newer version of WordPress is required for this plugin to work.

If you have a Content Security Policy (CSP) restricting from where content can be loaded,
you may need to add the domain `https://scripts.teamtailor-cdn.com` to the `script-src` setting.

## Installation

Download [this repository](https://github.com/kendafi/teamtailor-block)
as a [ZIP file](https://github.com/kendafi/teamtailor-block/archive/refs/heads/master.zip),
install it via your WordPress admin plugin page, and enable the plugin.

Or just copy the code from `teamtailor.php`, and paste it into your theme's `functions.php` file.
