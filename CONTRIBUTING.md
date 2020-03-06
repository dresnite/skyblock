# Contributing Guidelines

## Code Syntax

As [PocketMine-MP](https://github.com/pmmp/PocketMine-MP/blob/master/CONTRIBUTING.md#code-syntax), we are using [PSR-2](http://www.php-fig.org/psr/psr-2/) with a few exceptions.

1. Opening braces MUST go on the same line, and MUST have a space before.
2. `else if` MUST be written as elseif
3. Control structure keywords or opening braces MUST NOT have spaces before or after them.
4. Code SHOULD use spaces for indenting.
5. Files MUST use only the <?php tag.
6. Files MUST NOT have an ending ?> tag.
7. Code MUST use namespaces.
8. Strings SHOULD use the double quote " except when the single quote is required.
9. The header of new PHP files MUST be:
```php
/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 */
```

Before you start making your contributions, we suggest that you read:
* [Clean Code guide](https://github.com/jupeter/clean-code-php)
* [Writing good commit messages](https://github.com/erlang/otp/wiki/Writing-good-commit-messages)