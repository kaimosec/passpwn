# THIS PROJECT IS ARCHIVED. FOR A BETTER PROJECT THAT IS SIMILAR TO THIS ONE, SEE [PASSMUTATE](https://github.com/kaimosensei/passmutate).

PassPwn
=======
![](https://img.shields.io/badge/build-passing-brightgreen) ![](https://img.shields.io/badge/license-GPL%203-blue) ![](https://img.shields.io/badge/passpwn-v1.0-blue)

PassPwn is the bollocks. PassPwn is a password list generator that not only generates a ton of combinations, but also sorts the password list by efficacy.

## Features
- Based on statistical research of published passwords
- Calculates probability for each password and sorts by efficacy
  - Useful for stealth brute-forcing
- Supports custom modules for more combinations
- Enter answers about the target in terminal or INI file
- Can generate upwards of 100,000 passwords per answer
- Supports advanced filtering

## How it Works
Passwords typically consist of three elements:
- Stem: Base of the password (e.g. Thompson)
- Transform: Modification to the stem (e.g. replace e's with 3's)
- Suffix: Characters appended to the password (e.g. 123)

The core module has 10,381 suffixes and 11 transforms and can therefore generate ~114,191 passwords per answer (stem) that you provide about the target.

It will ask questions such as names, hobbies, locations etc. and - knowing the approximate probabilities of different types of stems - be able to generate passwords along with their reasonably accurate probabilities.

It may also add new stems based on your answers. For example, if you add golf as a hobby, it may also include "golfer, golfing, ilovegolfing" etc.

Password probabilities are calculated based on:
- Stem subject
- Type of suffix (if present)
- Type of transform (if present)

Which, in combination, gives every password a unique probability.

## Requirements
- PHP 5.5+ (tested only on Linux)
## Usage
Fill in the **generator.ini** and run
```php passgen.php [OPTIONS]```
or
```./passgen.sh [OPTIONS]```

To fill answers in terminal instead of via the INI file:
```php passgen.php --input```

For help
```php passgen.php -h```

頑張れ!!
