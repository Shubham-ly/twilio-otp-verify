# TWILIO OTP VERIFICATION

Drupal custom module for otp verification using sms `Twilio` gateway.

## Installation

- Clone repo in `web\modules\custom`.
- Add repo path in `composer.json`.

```php
    "repositories": [
    {
        "type": "path",
        "url": "web/modules/custom/otp_verify"
    }
],
```

- run this command to download dependencies.

```bash
composer require drupal/otp_verify
```

## CONFIGURATIONS

Please go to `/admin/config/otp-verify` to explore the configuration
options of the widget.
