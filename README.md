# Laravel Altcha

[![Latest Version on Packagist](https://img.shields.io/packagist/v/grantholle/laravel-altcha.svg?style=flat-square)](https://packagist.org/packages/grantholle/laravel-altcha)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/grantholle/laravel-altcha/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/grantholle/laravel-altcha/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/grantholle/laravel-altcha/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/grantholle/laravel-altcha/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/grantholle/laravel-altcha.svg?style=flat-square)](https://packagist.org/packages/grantholle/laravel-altcha)

This is a Laravel implementation for the server-side of the [Altcha](https://altcha.org/), a proof-of-work captcha that does not require any third-party service.

## Installation

You can install the package via composer:

```bash
composer require grantholle/laravel-altcha
```

Optionally, publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-altcha-config"
```

## Usage

In `.env` (or published config file), set the following variables:

```dotenv
# Required, sort of like a password
ALTCHA_HMAC_KEY=
# Optional, defaults to SHA-256. Can be SHA-384 or SHA-512
# ALTCHA_ALGORITHM="SHA-256"
```

Out of the box, the package registers a `/altcha-challenge` endpoint to use you on your frontend. 

## Frontend

Following the [frontend integration](https://altcha.org/docs/website-integration), use the following snippet to get a challenge:

```html
<altcha-widget challengeurl="/altcha-challenge"></altcha-widget>
```

Implementation will be different given your frontend, but here's an example Vue component to use:

```vue
<template>
  <altcha-widget challengeurl="/altcha-challenge" @statechange="stateChanged"></altcha-widget>
</template>

<script setup>
import 'altcha'

const emit = defineEmits(['update:modelValue'])
const stateChanged = ev => {
  if (ev.detail.state === 'verified') {
    emit('update:modelValue', ev.detail.payload)
  }
}
</script>
```

In an Inertja.js form, you could use this component like so:

```vue
<template>
  <form @submit.prevent="form.post('/login')">
    <label for="email">Email</label>
    <input type="email" name="email" v-model="form.email">
    
    <label for="password">Password</label>
    <input type="password" name="password" v-model="form.password">
    
    <Altcha v-model="form.token" />
    
    <button type="submit">Submit</button>
  </form>
</template>

<script setup>
import { useForm } from '@inertiajs/inertia-vue3'
// This is the component we made above
import Altcha from '@/components/forms/Altcha.vue'

const form = useForm({
  email: null,
  password: null,
  token: null,
})
</script>
```

## Backend validation

To validate the frontend-generated token/payload, there's a `ValidAltchaToken` rule you can use, assuming the token is passed as `token` in the request:

```php
use Grantholle\LaravelAltcha\Rules\ValidAltchaToken;

$request->validate([
    'email' => ['required', 'email'],
    'password' => ['required'],
    'token' => [new ValidAltcha()],
]);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Grant Holle](https://github.com/grantholle)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
