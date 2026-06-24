# Import Static SiteWorx Data

Use this after running `database/upgrade_existing_siteworx.sql`.

## Browser Import

1. Login as admin:
   `http://localhost/siteworx/Admin_SiteWorx/login`

2. Open:
   `http://localhost/siteworx/Admin_SiteWorx/seed_import.php?run=1`

The importer reads every JSON file in `database/seeds/` and inserts or updates:

- `hosting_plans`
- `products`
- `service_catalog`
- `servers`

## CLI Import

If PHP CLI is available:

```bash
php Admin_SiteWorx/seed_import.php
```

The import is update-safe. Existing rows with the same `slug`, `sku`, `code`, or `hostname` are updated.
