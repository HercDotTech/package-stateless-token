# Stateless Token

![Build Status](https://cicd.herc.link/api/badges/HercDotTech/package-stateless-token/status.svg)
[![License: GNU GPL v3](https://img.shields.io/badge/License-GNU_GPL_v3-blue.svg)](https://opensource.org/licenses/gpl-3-0)
[![Packagist Version](https://img.shields.io/packagist/v/hercdottech/stateless-token)](https://packagist.org/packages/hercdottech/stateless-token)

The `hercdottech/stateless-token` PHP package provides a way to generate and validate stateless tokens. Without
making use of a database, files, or any other kind of state, this package relies on a private key known only to the
server and an array of clues to validate the tokens.

Due to the stateless nature of this package, it cannot be used to protect against replay attacks.

## Requirements

- PHP 8.3 or later
- PHP JSON Extension (installed by default)
- PHP Hash Extension (installed by default)

## Features

- Generate secure tokens using sha256 algorithm HMAC hash.
- The expiration period of each token is configurable, set to 1 hour by default.
- The generated tokens are URL-safe and can be used with GET requests.

## Installation

Run the following command in your terminal:

```
composer require hercdottech/stateless-token
```