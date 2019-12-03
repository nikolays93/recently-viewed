# wp-admin-page

```
/**
 * Class Name: WPAdminPage
 * Class URI: https://github.com/nikolays93/wp-admin-page/
 * Description: Create a new custom admin page.
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
```

### How to use:

```php

use NikolayS93\WPAdminPage\Page;

$Page = new Page(
    'example-page',
    __( 'New example page', DOMAIN ),
    array(
        'parent'      => 'index.php',
        'menu'        => __( 'New page', DOMAIN ),
        'permissions' => 'manage_options',
        'columns'     => 2,
    )
);

$Page->set_content( function () {
    include __DIR__ . '/admin/template/content.php';
} );
```

### Sections:

```php

use NikolayS93\WPAdminPage\Section;

$Page->add_section( new Section(
        'section',
        __( 'Section', DOMAIN ),
        __DIR__ . '/admin/template/section.php'
    )
);
```

### Metaboxes:

```php

use NikolayS93\WPAdminPage\Metabox;

$Page->add_metabox( new Metabox(
    'metabox',
    __( 'Metabox', DOMAIN ),
    __DIR__ . '/admin/template/metabox.php',
    'side',
    'high'
) );
```

### Assets:

```php

$Page->set_assets( function () {
    wp_enqueue_style( 'domain-admin-page', 'http://new-example-page.css' );
} );
```

### @Hooks:

    $pageslug . _after_title (default empty hook)
    $pageslug . _before_form_inputs (default empty hook)
    $pageslug . _inside_page_content
    $pageslug . _inside_side_container
    $pageslug . _inside_advanced_container
    $pageslug . _after_form_inputs (default empty hook)
    $pageslug . _after_page_wrap (default empty hook)
